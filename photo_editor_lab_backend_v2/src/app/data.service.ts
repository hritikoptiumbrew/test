/*
 * Optimumbrew Technology

 * Project       : Photo Editor Lab
 * File          : data.service.ts
 * File Created  : Thursday, 15th October 2020 01:12:03 pm
 * Author        : Optimumbrew
 * Auther Email  : info@optimumbrew.com
 * Last Modified : Thursday, 29th October 2020 10:56:27 am
 * -----
 * Copyright 2018 - 2020 Optimumbrew Technology
 */


import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Router } from '@angular/router';
import { HOST, ERROR } from './app.constants';
import { Observable } from 'rxjs/Observable';
import 'rxjs/Rx';
import 'rxjs/add/operator/toPromise';
import * as moment from 'moment';
@Injectable({
  providedIn: 'root'
})
export class DataService {
  token: any;
  response: any;

  constructor(private http: HttpClient, private router: Router) {
  }

  getData(q) {
    return this.http.get(HOST.BASE_URL + q)
      .map((results: Response) => results.json());
  }
  postData(q, object, headers): Promise<any> {
    return this.http.post(HOST.BASE_URL + q, object, headers).toPromise().then((results: any) => {
      if (headers.headers && !localStorage.getItem("at") && !localStorage.getItem("admin_detail")) {
        localStorage.clear();
        this.router.navigate(['/']);
        let tmp_failed_res: any = {
          cause: "",
          code: 201,
          data: {},
          message: ERROR.LOGGED_OUT_DIFF_TAB
        };
        return tmp_failed_res;
      }
      else {
        let tmp_results: any = results;
        if (tmp_results.code == "400") {
          localStorage.removeItem("at");
          localStorage.clear();
          this.router.navigate(['/']);
        }
        if (tmp_results.code == "401") {
          let token = tmp_results.data.new_token;
          localStorage.setItem("at", token);
          headers = {
            headers:
            {
              'Authorization': 'Bearer ' + localStorage.getItem("at")
            }
          }
          return this.postData(q, object, headers);
        }
        else {
          return tmp_results;
        }
      }
    }, (error: any) => {
      let error_resp = {
        cause: "",
        code: 201,
        data: {},
        message: ERROR.SERVER_INTERNET_ERR
      }
      console.log("ERROR Thrown ", error.status, error);
      return error_resp;
    });
  }
  formatDDMMMYYYYHHMMALOCAL(date) {
    if (date) {
      let stillUtc = moment.utc(date).toDate();
      return moment(stillUtc).local().format('DD MMM, YYYY hh:mm A');
    }
    else {
      return "";
    }
  }
}

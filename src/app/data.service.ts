import { Injectable } from '@angular/core';
import { Http, Response, RequestOptions, Headers, RequestMethod, RequestOptionsArgs } from '@angular/http';
import 'rxjs/Rx';
import 'rxjs/add/operator/toPromise';
import { Router, ActivatedRoute } from '@angular/router';
import { HOST } from './app.constants';
import { Observable } from 'rxjs/Observable';
import { MdDialog, MdDialogRef } from '@angular/material';
import { ViewImageComponent } from './view-image/view-image.component';
import * as moment from 'moment';

@Injectable()
export class DataService {

  token: any;
  response: any;

  constructor(private http: Http, private router: Router, public dialog: MdDialog) {
  }

  getData(q) {
    return this.http.get(HOST.BASE_URL + q)
      .map((results: Response) => results.json());
  }

  postData(q, object, headers): Observable<any> {
    return this.http.post(HOST.BASE_URL + q, object, headers)
      .map((results: Response) => {
        if (results.status < 200 || results.status >= 300) {
          /* console.log("in result.status<200||result.status>300..."); */
          throw new Error('This request has failed ' + results.status);
        }
        else {
          return results.json();
        }
      }, (err) => {
        if (err.status == 400) {
          localStorage.removeItem("photoArtsAdminToken");
          this.router.navigate(['/admin']);
        }
      });
  }

  viewImage(imageURL) {
    let dialogRef = this.dialog.open(ViewImageComponent);
    dialogRef.componentInstance.imageSRC = imageURL;
  }

  compareTwoDates(dateTimeA, dateTimeB) {
    var momentA = moment(dateTimeA);
    var momentB = moment(dateTimeB);
    if (momentA > momentB) return 1;
    else if (momentA < momentB) return -1;
    else return 0;
  }

  isAfter(dateTimeA, dateTimeB) {
    var momentA = moment(dateTimeA);
    var momentB = moment(dateTimeB);
    if (momentA > momentB) {
      return true;
    }
    else {
      return false;
    }
  }

  isBefore(dateTimeA, dateTimeB) {
    var momentA = moment(dateTimeA);
    var momentB = moment(dateTimeB);
    if (momentA < momentB) {
      return true;
    }
    else {
      return false;
    }
  }

  isEqual(dateTimeA, dateTimeB) {
    var momentA = moment(dateTimeA);
    var momentB = moment(dateTimeB);
    if (momentA == momentB) {
      return true;
    }
    else {
      return false;
    }
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

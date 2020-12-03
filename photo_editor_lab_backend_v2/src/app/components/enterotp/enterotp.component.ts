/*
 * Optimumbrew Technology

 * Project       : Photo Editor Lab
 * File          : enterotp.component.ts
 * File Created  : Monday, 26th October 2020 06:20:35 pm
 * Author        : Optimumbrew
 * Auther Email  : info@optimumbrew.com
 * Last Modified : Thursday, 29th October 2020 11:24:21 am
 * -----
 * Copyright 2018 - 2020 Optimumbrew Technology
 */


import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { NbDialogRef } from '@nebular/theme';
import { DataService } from 'app/data.service';
import { UtilService } from 'app/util.service';
import { ERROR, ENV_CONFIG } from '../../app.constants';
@Component({
  selector: 'ngx-enterotp',
  templateUrl: './enterotp.component.html',
  styleUrls: ['./enterotp.component.scss']
})
export class EnterotpComponent implements OnInit {

  adminData: any = {};
  admin_detail: any;
  token: any;
  occFrom: any;
  constructor(private route: Router, private dialogRef: NbDialogRef<EnterotpComponent>, private dataService: DataService, private utils: UtilService) {
    this.token = localStorage.getItem('at');
    this.utils.dialogref = this.dialogRef;
  }

  ngOnInit(): void {
    this.admin_detail = this.adminData.admin_detail;
    this.occFrom = this.adminData.occuredFrom;
  }
  closeDialog() {
    this.dialogRef.close({ res: '' });
  }
  setOtpCode(event) {
    if (event.length >= 6) {
      this.verifyOtp(event);
    }
  }
  verifyOtp(event) {
    if (this.admin_detail.shouldNavigate == true || this.occFrom == "change-pwd") {
      this.utils.showLoader();
      this.dataService.postData("verify2faOPT", {
        "verify_code": event,
        "user_id": this.admin_detail.id,
        "google2fa_secret": this.admin_detail.google2fa_secret
      }, {}).then((results: any) => {
        if (results.code == 200) {
          this.utils.hideLoader();
          this.dialogRef.close({ res: "add" });
          localStorage.setItem("at", results.data.token);
          localStorage.setItem("admin_detail", JSON.stringify((results.data.user_detail)));
          if (this.occFrom != "change-pwd") {
            if (this.admin_detail.shouldNavigate == true) {
              this.utils.showSuccess(results.message, 4000);
              this.route.navigate(['/admin/']);
            }
            else {
              this.utils.showSuccess(ERROR.TWO_FA_DIS_SUCCESS, 4000);
            }
          }

        }
        else if (results.code == 201) {
          this.utils.showError(results.message, 4000);
          this.utils.hideLoader();
        }
        else if (results.status || results.status == 0) {
          this.utils.showError(ERROR.SERVER_ERR, 4000);
          this.utils.hideLoader();
        }
        else {
          this.utils.showError(results.message, 4000);
          this.utils.hideLoader();
        }
      }, (error: any) => {
        console.log(error);
        this.utils.hideLoader();
        this.utils.showError(ERROR.SERVER_ERR, 4000);
      }).catch((error: any) => {
        console.log(error);
        this.utils.hideLoader();
        this.utils.showError(ERROR.SERVER_ERR, 4000);
      });
    }
    else {
      this.utils.showLoader();
      this.dataService.postData("disable2faByAdmin", {
        "verify_code": event,
        "google2fa_secret": this.admin_detail.google2fa_secret
      }, {
        headers: {
          'Authorization': 'Bearer' + this.token
        }
      }).then((results: any) => {
        if (results.code == 200) {
          this.utils.hideLoader();
          this.dialogRef.close({ res: "add" });
          localStorage.setItem("at", results.data.token);
          localStorage.setItem("admin_detail", JSON.stringify((results.data.user_detail)));
          if (this.admin_detail.shouldNavigate == true) {
            this.route.navigate(['/admin/categories']);
          }
          this.utils.showSuccess(results.message, 4000);
        }
        else if (results.code == 201) {
          this.utils.showError(results.message, 4000);
          this.utils.hideLoader();
        }
        else if (results.status || results.status == 0) {
          this.utils.showError(ERROR.SERVER_ERR, 4000);
          this.utils.hideLoader();
        }
        else {
          this.utils.showError(results.message, 4000);
          this.utils.hideLoader();
        }
      }, (error: any) => {
        console.log(error);
        this.utils.hideLoader();
        this.utils.showError(ERROR.SERVER_ERR, 4000);
      }).catch((error: any) => {
        console.log(error);
        this.utils.hideLoader();
        this.utils.showError(ERROR.SERVER_ERR, 4000);
      });
    }
  }
}

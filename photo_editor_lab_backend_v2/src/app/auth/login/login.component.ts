/*
 * Optimumbrew Technology

 * Project       : Photo Editor Lab
 * File          : login.component.ts
 * File Created  : Thursday, 15th October 2020 01:12:03 pm
 * Author        : Optimumbrew
 * Auther Email  : info@optimumbrew.com
 * Last Modified : Thursday, 29th October 2020 11:37:48 am
 * -----
 * Copyright 2018 - 2020 Optimumbrew Technology
 */


import { Component, OnInit } from '@angular/core';
import { FormControl } from '@angular/forms';
import { Router } from '@angular/router';
import { DataService } from 'app/data.service';
import { from } from 'rxjs';
import { ERROR, ENV_CONFIG } from '../../app.constants';

import { UtilService } from 'app/util.service';
import { ValidationsService } from 'app/validations.service';
import { NbDialogService } from '@nebular/theme';
import { EnterotpComponent } from 'app/components/enterotp/enterotp.component';
import { ViewimageComponent } from 'app/components/viewimage/viewimage.component';

@Component({
  selector: 'ngx-login',
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.scss']
})
export class LoginComponent implements OnInit {

  d:any = new Date();
  current_year = this.d.getFullYear();
  checked = false;
  admin_detail: any;
  errormsg: any = ERROR;
  passTextType: boolean;
  constructor(private dialog: NbDialogService, private validService: ValidationsService, private dataService: DataService, private router: Router, private utils: UtilService) {
    if (localStorage.getItem('at')) {
      this.router.navigate(['/admin/categories']);
    }
  }


  ngOnInit(): void {

  }
  ngAfterViewInit(): void {
    if (this.utils.enable2FaStatus != undefined && this.utils.enable2FaStatus == 'enable') {
      this.dialog.open(ViewimageComponent, {
        closeOnBackdropClick: false,closeOnEsc: false, context: {
          imgSrc: this.utils.qrcodeUrl,
          typeImg: 'otp'
        }
      }).onClose.subscribe((result) => {
        this.utils.enable2FaStatus = "diabled";
        this.dialog.open(EnterotpComponent, {
          closeOnBackdropClick: false,closeOnEsc: false, context: {
            adminData: this.utils.otpdata
          }
        });
      });
    }
  }
  emailFormControl = new FormControl('');
  passwordFormControl = new FormControl('');



  checkValidation(id, type, catId, blankMsg, typeMsg,validType) {
    var validObj = {
      "id": id,
      "errorId": catId,
      "type": type,
      "blank_msg": blankMsg,
      "type_msg": typeMsg,
      "button_check": {
        "button_id": "btnLogin",
        "successArr": ['loginEmail', 'loginPass']
      }
    }
    this.validService.checkValid(validObj);
    if(validType != "blank" && validType != "blur")
    {
      this.do_login();
    }
  }
  
  passHideShow() {
    this.passTextType = !this.passTextType;
  }
  do_login() {
    // var loginStatus;
    // loginStatus = document.getElementById("btnLogin").hasAttribute("disabled");
    var validObj = [
      {
        "id": 'loginEmail',
        "errorId": 'EmailError',
        "type": 'email',
        "blank_msg": ERROR.EMPTY_EMAIL,
        "type_msg": ERROR.INVALID_EMAIL,
      },
      {
        "id": 'loginPass',
        "errorId": 'passError',
        "type": '',
        "blank_msg": ERROR.EMPTY_PASSWORD,
        "type_msg": ''
      }
    ]
    var addStatus = this.validService.checkAllValid(validObj);
    if (addStatus){
      this.utils.showLoader();
      this.dataService.postData('doLogin',
        {
          "email_id": this.emailFormControl.value,
          "password": this.passwordFormControl.value
        }, {}).then((results: any) => {
          if (results.code == 200) {
            this.admin_detail = results.data.user_detail;
            localStorage.setItem("u_r", results.data.role);
            this.utils.hideLoader();
            if (ENV_CONFIG.ENABLE_2FA === true) {
              if (this.admin_detail.google2fa_enable == 0 || this.admin_detail.google2fa_enable == false) {
                localStorage.setItem('at', results.data.token);
                this.admin_detail.enable_subcategory = false;
                localStorage.setItem("u_r", results.data.role);
                localStorage.setItem("admin_detail", JSON.stringify(this.admin_detail));
                this.router.navigate(['/admin/categories']);
              } else if (this.admin_detail.google2fa_enable == 1 || this.admin_detail.google2fa_enable == true) {
                this.openOTPDialog(this.admin_detail);
              }
            }
            else {
              this.admin_detail.enable_subcategory = false;
              localStorage.setItem("admin_detail", JSON.stringify(this.admin_detail));
              this.router.navigate(['/admin/categories']);
            }
          }
          else if (results.code == 201) {
            // this.utils.showError(results.message, 4000);
            this.utils.hideLoader();
            document.getElementById("passError").innerHTML = results.message;
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
  openOTPDialog(admin_detail) {
    admin_detail.shouldNavigate = true;
    var passData = { admin_detail: admin_detail, occuredFrom: 'login' }
    this.dialog.open(EnterotpComponent, {
      closeOnBackdropClick: false,closeOnEsc: false, context: {
        adminData: passData
      }
    }).onClose.subscribe((result) => {
      if (result) {
      }
      else {
        this.router.navigate(['/admin/categories']);
        localStorage.setItem("admin_detail", JSON.stringify(this.admin_detail));
      }
    });
  }
}

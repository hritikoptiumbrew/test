import { Component, OnInit, ViewChild, ElementRef, ViewEncapsulation } from '@angular/core';
import { MdDialog, MdSnackBar, MdSnackBarConfig } from '@angular/material';
import { Router } from '@angular/router';
import { DataService } from '../data.service';
import { LoadingComponent } from '../loading/loading.component';
import { DeleteUserGeneratedComponent } from '../delete-user-generated/delete-user-generated.component';
import { EnterPasswordComponent } from '../enter-password/enter-password.component';
import { EnterOTPComponent } from '../enter-otp/enter-otp.component';
import { ViewImageComponent } from '../view-image/view-image.component';
import { ENV_CONFIG } from '../app.constants';

@Component({
  templateUrl: './settings.component.html',
  encapsulation: ViewEncapsulation.None
})
export class SettingsComponent implements OnInit {

  token: any;
  successMsg: any;
  errorMsg: any;
  cnf_password: any;
  passwordData: any = {};
  loading: any;
  server_list: any[] = [];
  tmp_server_list: any[] = [];
  validation_list: any = [];
  tmp_validation_list: any = [];
  category_list: any = [];
  vltnData: any = { "category_id": "" };
  total_record: any;
  new_server_url: any = "";
  st: any = {};
  lg_rep: any = {};
  env: any = ENV_CONFIG;
  tfa_status: any = "";
  is_verified: any = false;
  @ViewChild('choice1') choice1: ElementRef;
  @ViewChild('choice2') choice2: ElementRef;
  @ViewChild('flap') flap: ElementRef;

  constructor(private dataService: DataService, private router: Router, public dialog: MdDialog, public snackBar: MdSnackBar) {
    this.token = localStorage.getItem('photoArtsAdminToken');
    this.lg_rep = JSON.parse(localStorage.getItem('admin_detail'));
    if (!this.lg_rep.google2fa_enable || this.lg_rep.google2fa_enable == 0 || this.lg_rep.google2fa_enable == false) {
      this.getStatisticsData();
    }
    else {
      this.openOTPDialog(this.lg_rep, 'change-pwd').then((otpResp) => {
        if (otpResp && otpResp.result && otpResp.result.user_name) {
          this.getStatisticsData();
        }
      });
    }
  }

  async ngOnInit() {
    await this.choice1;
    // console.log(this.choice1);
    this.token = localStorage.getItem('photoArtsAdminToken');
    if (ENV_CONFIG.ENABLE_2FA) {
      this.tfa_status = this.lg_rep.google2fa_enable == 0 ? 'Disabled' : 'Enabled';
      if (this.lg_rep.google2fa_enable) {
        await this.choice1.nativeElement.click();
        this.flap.nativeElement.style.backgroundColor = "green";
      }
      else {
        this.choice2.nativeElement.click()
        this.flap.nativeElement.style.backgroundColor = "red";
      }
    }
  }

  transitionEnd(event) {
    // console.log(event);
  }

  updtAth(e, choice: ElementRef, enabled, flap: ElementRef) {
    e.preventDefault();
    this.google2faChanged(choice, enabled, flap);
  }

  google2faChanged(choice, enabled, flap) {
    if (enabled == true) {
      this.loading = this.dialog.open(LoadingComponent);
      this.token = localStorage.getItem('photoArtsAdminToken');
      this.dataService.postData('enable2faByAdmin',
        {}, {
          headers: {
            'Authorization': 'Bearer ' + this.token
          }
        }).subscribe(results => {
          if (results.code == 200) {
            this.choice1.nativeElement.click();
            this.flap.nativeElement.style.backgroundColor = "green";
            this.lg_rep.google2fa_enable = 1;
            this.tfa_status = this.lg_rep.google2fa_enable == 0 ? 'Disabled' : 'Enabled';
            this.loading.close();
            if (results.data && results.data.google2fa_url) {
              this.router.navigate(['/']);
              localStorage.removeItem("photoArtsAdminToken");
              localStorage.removeItem("admin_detail");
              let imgDialogRef = this.dialog.open(ViewImageComponent);
              imgDialogRef.componentInstance.imageSRC = results.data.google2fa_url;
              this.lg_rep.google2fa_secret = results.data.google2fa_secret;
              this.lg_rep.shouldNavigate = true;
              imgDialogRef.afterClosed().subscribe(result => {
                let dialogRef = this.dialog.open(EnterOTPComponent, {
                  disableClose: true,
                  panelClass: 'enter-otp-container',
                  data: { admin_detail: this.lg_rep, occuredFrom: 'enable2fa' }
                })
              });
            }
            this.showSuccess(results.message, false);
            this.errorMsg = "";
            // this.showSuccess(results.message, false);
          }
          else if (results.code == 400) {
            this.loading.close();
            localStorage.removeItem("photoArtsAdminToken");
            this.router.navigate(['/admin']);
          }
          else if (results.code == 401) {
            this.token = results.data.new_token;
            localStorage.setItem("photoArtsAdminToken", this.token);
            this.getStatisticsData();
          }
          else {
            this.loading.close();
            this.successMsg = "";
            // this.errorMsg = results.message;
            this.errorMsg = "";
            this.showError(results.message, false);
          }
        }, error => {
          this.loading.close();
          this.showError("Unable to connect with server, please reload the page.", false);
          /* console.log(error.status); */
          /* console.log(error); */
        });
    }
    else if (enabled == false) {
      this.openOTPDialog(this.lg_rep, 'disable2fa');
    }
  }

  openEnterPassword() {
    let dialogRef = this.dialog.open(EnterPasswordComponent, {
      disableClose: true,
      panelClass: 'enter-otp-container',
      data: this.lg_rep
    });
    dialogRef.afterClosed().subscribe(result => {
      if (result) {
        this.lg_rep.google2fa_enable = 1;
        localStorage.setItem("admin_detail", JSON.stringify(this.lg_rep));
        if (this.lg_rep.google2fa_enable) {
          this.choice1.nativeElement.click();
          this.flap.nativeElement.style.backgroundColor = "green";
        }
        else {
          this.choice2.nativeElement.click()
          this.flap.nativeElement.style.backgroundColor = "red";
        }
      }
      else {
        this.lg_rep.google2fa_enable = 0;
        localStorage.setItem("admin_detail", JSON.stringify(this.lg_rep));
        if (this.lg_rep.google2fa_enable) {
          this.choice1.nativeElement.click();
          this.flap.nativeElement.style.backgroundColor = "green";
        }
        else {
          this.choice2.nativeElement.click()
          this.flap.nativeElement.style.backgroundColor = "red";
        }
      }
    });
  }

  openOTPDialog(admin_detail: any, occuredFrom): Promise<any> {
    return new Promise(async (resolve) => {
      admin_detail.shouldNavigate = false;
      let dialogRef = this.dialog.open(EnterOTPComponent, {
        disableClose: true,
        panelClass: 'enter-otp-container',
        data: { admin_detail: admin_detail, occuredFrom: occuredFrom }
      });
      dialogRef.afterClosed().subscribe(result => {
        if (result && result.user_name) {
          this.lg_rep = result;
          this.tfa_status = this.lg_rep.google2fa_enable == 0 ? 'Disabled' : 'Enabled';
          localStorage.setItem("admin_detail", JSON.stringify(this.lg_rep));
          if (this.lg_rep.google2fa_enable) {
            this.choice1.nativeElement.click();
            this.flap.nativeElement.style.backgroundColor = "green";
          }
          else {
            this.choice2.nativeElement.click();
            this.flap.nativeElement.style.backgroundColor = "red";
          }
        }
        resolve({ result: result });
      });
    });
  }

  changePassword(passwordData) {
    if (typeof passwordData == "undefined" || passwordData == "" || passwordData == null) {
      this.successMsg = "";
      this.errorMsg = "Please Enter Current Password";
      return false;
    }
    else if (typeof passwordData.current_password == "undefined" || passwordData.current_password == "" || passwordData.current_password == null) {
      this.successMsg = "";
      this.errorMsg = "Please Enter Current Password";
      return false;
    }
    else if (typeof passwordData.new_password == "undefined" || passwordData.new_password == "" || passwordData.new_password == null) {
      this.successMsg = "";
      this.errorMsg = "Please Enter New Password";
      return false;
    }
    else if (typeof this.cnf_password == "undefined" || this.cnf_password == "" || this.cnf_password == null) {
      this.successMsg = "";
      this.errorMsg = "Please Enter Repeat New Password";
      return false;
    }
    else if (this.cnf_password != passwordData.new_password) {
      this.successMsg = "";
      this.errorMsg = "New Password & Repeat New Password Missmatch";
      return false;
    }
    else if (passwordData.new_password.length < 6) {
      this.successMsg = "";
      this.errorMsg = "Password should be atleast 6 character";
      return false;
    }
    else if (this.cnf_password.length < 6) {
      this.successMsg = "";
      this.errorMsg = "Password should be atleast 6 character";
      return false;
    }
    else {
      /* if (!this.lg_rep.google2fa_enable || this.lg_rep.google2fa_enable == 0 || this.lg_rep.google2fa_enable == false) {
        this.updatePwd(passwordData);
      }
      else {
        this.openOTPDialog(this.lg_rep, 'change-pwd').then((otpResp) => {
          if (otpResp && otpResp.result && otpResp.result.user_name) {
            this.updatePwd(passwordData);
          }
        });
      } */
      this.updatePwd(passwordData);
    }
  }

  updatePwd(passwordData) {
    this.loading = this.dialog.open(LoadingComponent);
    this.errorMsg = "";
    this.successMsg = "";
    this.dataService.postData("changePassword", {
      "current_password": passwordData.current_password,
      "new_password": passwordData.new_password
    }, {
        headers: {
          'Authorization': 'Bearer ' + this.token
        }
      }).subscribe(results => {
        if (results.code == 200) {
          if (results.data && results.data.token) {
            this.token = results.data.token;
            localStorage.setItem('photoArtsAdminToken', results.data.token);
          }
          this.passwordData = {};
          this.cnf_password = "";
          this.successMsg = results.message;
          this.loading.close();
        }
        else if (results.code == 400) {
          this.loading.close();
          localStorage.removeItem("photoArtsAdminToken");
          this.router.navigate(['/admin']);
        }
        else if (results.code == 401) {
          this.token = results.data.new_token;
          this.loading.close();
          localStorage.setItem("photoArtsAdminToken", this.token);
          this.updatePwd(passwordData);
        }
        else {
          this.errorMsg = results.message;
          this.loading.close();
        }
      });
  }

  getStatisticsData() {
    this.loading = this.dialog.open(LoadingComponent);
    this.token = localStorage.getItem('photoArtsAdminToken');
    this.dataService.postData('getAllServerUrls',
      {}, {
        headers: {
          'Authorization': 'Bearer ' + this.token
        }
      }).subscribe(results => {
        if (results.code == 200) {
          this.server_list = results.data.result;
          this.tmp_server_list = JSON.parse(JSON.stringify(results.data.result));
          this.server_list.forEach(element => {
            element.is_editing = false;
          });
          this.total_record = results.data.total_record;
          this.loading.close();
          this.getValidationData();
          this.errorMsg = "";
          // this.showSuccess(results.message, false);
        }
        else if (results.code == 400) {
          this.loading.close();
          localStorage.removeItem("photoArtsAdminToken");
          this.router.navigate(['/admin']);
        }
        else if (results.code == 401) {
          this.token = results.data.new_token;
          localStorage.setItem("photoArtsAdminToken", this.token);
          this.getStatisticsData();
        }
        else {
          this.loading.close();
          this.successMsg = "";
          // this.errorMsg = results.message;
          this.errorMsg = "";
          this.showError(results.message, false);
        }
      }, error => {
        this.loading.close();
        this.showError("Unable to connect with server, please reload the page.", false);
        /* console.log(error.status); */
        /* console.log(error); */
      });
  }

  getValidationData() {
    this.category_list = [];
    this.loading = this.dialog.open(LoadingComponent);
    this.token = localStorage.getItem('photoArtsAdminToken');
    this.dataService.postData('getAllValidationsForAdmin',
      {}, {
        headers: {
          'Authorization': 'Bearer ' + this.token
        }
      }).subscribe(results => {
        if (results.code == 200) {
          this.validation_list = results.data.result;
          this.category_list = results.data.category_list;
          this.category_list.unshift({
            category_id: 0,
            name: "Default"
          })
          this.tmp_validation_list = JSON.parse(JSON.stringify(results.data.result));
          this.validation_list.forEach((element: any) => {
            element.is_editing = false;
          });
          this.is_verified = true;
          this.loading.close();
          this.errorMsg = "";
          // this.showSuccess(results.message, false);
        }
        else if (results.code == 400) {
          this.loading.close();
          localStorage.removeItem("photoArtsAdminToken");
          this.router.navigate(['/admin']);
        }
        else if (results.code == 401) {
          this.token = results.data.new_token;
          localStorage.setItem("photoArtsAdminToken", this.token);
          this.getStatisticsData();
        }
        else {
          this.loading.close();
          this.successMsg = "";
          // this.errorMsg = results.message;
          this.errorMsg = "";
          this.showError(results.message, false);
        }
      }, (error: any) => {
        this.loading.close();
        this.showError("Unable to connect with server, please reload the page.", false);
        /* console.log(error.status); */
        /* console.log(error); */
      });
  }

  saveUpdatedValidation(validationDetails) {
    if (!validationDetails) {
      this.showError("Please enter validation details", false);
      return false;
    }
    else if (!validationDetails.validation_name || validationDetails.validation_name.trim() == "") {
      this.showError("Please enter validation name", false);
      return false;
    }
    else if (!validationDetails.max_value_of_validation || validationDetails.max_value_of_validation.trim() == "") {
      this.showError("Please enter validation value", false);
      return false;
    }
    else if (!validationDetails.description || validationDetails.description.trim() == "") {
      this.showError("Please enter description for validation", false);
      return false;
    }
    else {
      let request_data: any = {
        "setting_id": validationDetails.setting_id,
        "category_id": validationDetails.category_id,
        "validation_name": validationDetails.validation_name,
        "max_value_of_validation": validationDetails.max_value_of_validation,
        "is_featured": validationDetails.is_featured ? 1 : 0,
        "is_catalog": validationDetails.is_catalog ? 1 : 0,
        "description": validationDetails.description
      };
      this.token = localStorage.getItem('photoArtsAdminToken');
      this.dataService.postData('editValidation',
        request_data, {
          headers: {
            'Authorization': 'Bearer ' + this.token
          }
        }).subscribe(results => {
          if (results.code == 200) {
            this.showSuccess(results.message, false);
            this.new_server_url = "";
            this.getValidationData();
          }
          else if (results.code == 400) {
            this.loading.close();
            localStorage.removeItem("photoArtsAdminToken");
            this.router.navigate(['/admin']);
          }
          else if (results.code == 401) {
            this.token = results.data.new_token;
            localStorage.setItem("photoArtsAdminToken", this.token);
            this.saveUpdatedValidation(validationDetails);
          }
          else {
            this.loading.close();
            this.successMsg = "";
            this.errorMsg = "";
            this.showError(results.message, false);
          }
        }, error => {
          /* console.log(error.status); */
          /* console.log(error); */
          this.loading.close();
          this.successMsg = "";
          this.errorMsg = "";
          this.showError("Unable to connect with server, please try again.", false);
        });
    }
  }

  deleteValidation(validationDetails) {
    let tmp_request_data = {
      "setting_id": validationDetails.setting_id
    };
    let dialogRef = this.dialog.open(DeleteUserGeneratedComponent, { disableClose: true });
    dialogRef.componentInstance.delete_request_data = tmp_request_data;
    dialogRef.componentInstance.API_NAME = "deleteValidation";
    dialogRef.afterClosed().subscribe(result => {
      if (!result) {
        this.getStatisticsData();
      }
    });
  }

  addValidation(vltnData) {
    if (!vltnData) {
      this.showError("Please enter validation details", false);
      return false;
    }
    else if (!vltnData.category_id || vltnData.category_id.trim() == "") {
      this.showError("Please enter category id", false);
      return false;
    }
    else if (!vltnData.validation_name || vltnData.validation_name.trim() == "") {
      this.showError("Please enter validation name", false);
      return false;
    }
    else if (!vltnData.max_value_of_validation || vltnData.max_value_of_validation.trim() == "") {
      this.showError("Please enter validation value", false);
      return false;
    }
    else if (!vltnData.description || vltnData.description.trim() == "") {
      this.showError("Please enter description for validation", false);
      return false;
    }
    else {
      let request_data: any = {
        "category_id": vltnData.category_id,
        "validation_name": vltnData.validation_name,
        "max_value_of_validation": vltnData.max_value_of_validation,
        "is_featured": vltnData.is_featured ? 1 : 0,
        "is_catalog": vltnData.is_catalog ? 1 : 0,
        "description": vltnData.description
      };
      this.token = localStorage.getItem('photoArtsAdminToken');
      this.dataService.postData('addValidation',
        request_data, {
          headers: {
            'Authorization': 'Bearer ' + this.token
          }
        }).subscribe(results => {
          if (results.code == 200) {
            this.showSuccess(results.message, false);
            this.new_server_url = "";
            this.vltnData = { "category_id": "" };
            this.getValidationData();
          }
          else if (results.code == 400) {
            this.loading.close();
            localStorage.removeItem("photoArtsAdminToken");
            this.router.navigate(['/admin']);
          }
          else if (results.code == 401) {
            this.token = results.data.new_token;
            localStorage.setItem("photoArtsAdminToken", this.token);
            this.saveUpdatedValidation(vltnData);
          }
          else {
            this.loading.close();
            this.successMsg = "";
            this.errorMsg = "";
            this.showError(results.message, false);
          }
        }, error => {
          /* console.log(error.status); */
          /* console.log(error); */
          this.loading.close();
          this.successMsg = "";
          this.errorMsg = "";
          this.showError("Unable to connect with server, please try again.", false);
        });
    }
  }

  updateServerURL(row_details: any, data_list: any, original_data_list: any, data_key: any) {
    data_list.forEach((element: any, i: number) => {
      this.resetRow(element, i, original_data_list, data_key);
    });
    row_details.is_editing = true;
  }

  resetRow(row_details: any, i: number, data_list: any, data_key: any) {
    row_details.is_editing = false;
    data_key.forEach((element: any) => {
      row_details[element] = data_list[i][element];
    });
  }

  saveNewServerURL(new_server_url) {
    if (!new_server_url || new_server_url.trim() == "") {
      this.showError("Please enter server URL", false);
      return false;
    }
    else {
      let request_data: any = {
        "server_url": new_server_url
      };
      this.token = localStorage.getItem('photoArtsAdminToken');
      this.dataService.postData('addServerUrl',
        request_data, {
          headers: {
            'Authorization': 'Bearer ' + this.token
          }
        }).subscribe(results => {
          if (results.code == 200) {
            this.showSuccess(results.message, false);
            this.new_server_url = "";
            this.getStatisticsData();
          }
          else if (results.code == 400) {
            this.loading.close();
            localStorage.removeItem("photoArtsAdminToken");
            this.router.navigate(['/admin']);
          }
          else if (results.code == 401) {
            this.token = results.data.new_token;
            localStorage.setItem("photoArtsAdminToken", this.token);
            this.saveNewServerURL(new_server_url);
          }
          else {
            this.loading.close();
            this.successMsg = "";
            this.errorMsg = "";
            this.showError(results.message, false);
          }
        }, error => {
          /* console.log(error.status); */
          /* console.log(error); */
          this.loading.close();
        });
    }
  }

  saveUpdatedServerURL(server_details) {
    if (!server_details.server_url || server_details.server_url.trim() == "") {
      this.showError("Please enter server URL", false);
      return false;
    }
    else {
      let request_data: any = {
        "server_url_id": server_details.server_url_id,
        "server_url": server_details.server_url
      };
      this.token = localStorage.getItem('photoArtsAdminToken');
      this.dataService.postData('updateServerUrl',
        request_data, {
          headers: {
            'Authorization': 'Bearer ' + this.token
          }
        }).subscribe(results => {
          if (results.code == 200) {
            this.showSuccess(results.message, false);
            this.getStatisticsData();
          }
          else if (results.code == 400) {
            this.loading.close();
            localStorage.removeItem("photoArtsAdminToken");
            this.router.navigate(['/admin']);
          }
          else if (results.code == 401) {
            this.token = results.data.new_token;
            localStorage.setItem("photoArtsAdminToken", this.token);
            this.saveUpdatedServerURL(server_details);
          }
          else {
            this.loading.close();
            this.successMsg = "";
            this.errorMsg = "";
            this.showError(results.message, false);
          }
        }, error => {
          /* console.log(error.status); */
          /* console.log(error); */
          this.loading.close();
        });
    }
  }

  deleteServerURL(server_details) {
    let tmp_request_data = {
      "server_url_id": server_details.server_url_id
    };
    let dialogRef = this.dialog.open(DeleteUserGeneratedComponent, { disableClose: true });
    dialogRef.componentInstance.delete_request_data = tmp_request_data;
    dialogRef.componentInstance.API_NAME = "deleteServerUrl";
    dialogRef.afterClosed().subscribe(result => {
      if (!result) {
        this.getStatisticsData();
      }
    });
  }

  showError(message, action) {
    let config = new MdSnackBarConfig();
    config.extraClasses = ['snack-error'];
    /* config.horizontalPosition = "right";
    config.verticalPosition = "top"; */
    config.duration = 5000;
    this.snackBar.open(message, action ? 'Okay!' : undefined, config);
  }

  showSuccess(message, action) {
    let config = new MdSnackBarConfig();
    config.extraClasses = ['snack-success'];
    /* config.horizontalPosition = "right";
    config.verticalPosition = "top"; */
    config.duration = 5000;
    this.snackBar.open(message, action ? 'Okay!' : undefined, config);
  }


}

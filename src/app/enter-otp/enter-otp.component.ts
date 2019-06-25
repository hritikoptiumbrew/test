import { Component, OnInit, Inject } from '@angular/core';
import { MdDialog, MdDialogRef, MD_DIALOG_DATA, MdSnackBar, MdSnackBarConfig } from '@angular/material';
import { Router } from '@angular/router';
import { DataService } from '../data.service';
import { ERROR } from '../app.constants';
import { LoadingComponent } from '../loading/loading.component';


@Component({
  selector: 'app-enter-otp',
  templateUrl: './enter-otp.component.html',
  styleUrls: ['./enter-otp.component.css']
})
export class EnterOTPComponent implements OnInit {

  codeData: any = {};
  token: any;
  loading: any;
  admin_detail: any = {};
  occFrom: any;

  constructor(public dialogRef: MdDialogRef<EnterOTPComponent>, @Inject(MD_DIALOG_DATA) public data: any, public dialog: MdDialog, private dataService: DataService, private router: Router, public snackBar: MdSnackBar) {
    this.token = localStorage.getItem('photoArtsAdminToken');
    this.admin_detail = this.data.admin_detail;
    this.occFrom = this.data.occuredFrom;
  }

  ngOnInit() {
  }

  verify2faOPT(codeData) {
    /* console.log(codeData); */
    if (typeof codeData.verify_code == "undefined" || this.dataService.trim(codeData.verify_code) == "" || codeData.verify_code == null) {
      this.showError(ERROR.VERIFICATION_CODE_EMPTY, false);
      return false;
    }
    else {
      if (this.admin_detail.shouldNavigate == true || this.occFrom == "change-pwd") {
        this.loading = this.dialog.open(LoadingComponent);
        this.token = localStorage.getItem('photoArtsAdminToken');
        this.dataService.postData("verify2faOPT", {
          "verify_code": codeData.verify_code,
          "user_id": this.admin_detail.id,
          "google2fa_secret": this.admin_detail.google2fa_secret
        }, {}).subscribe(results => {
          /* console.log(results); */
          if (results.code == 200) {
            localStorage.setItem("photoArtsAdminToken", results.data.token);
            localStorage.setItem("admin_detail", JSON.stringify(this.admin_detail));
            localStorage.setItem("admin_detail", JSON.stringify(results.data.user_detail));
            this.loading ? this.loading.close() : '';
            this.dialogRef.close(results.data.user_detail);
            if (this.occFrom != "change-pwd") {
              if (this.admin_detail.shouldNavigate == true) {
                this.showSuccess(results.message, false);
                this.router.navigate(['/admin/categories']);
              }
              else {
                this.showSuccess(ERROR.TWO_FA_DIS_SUCCESS, false);
              }
            }
          }
          else if (results.code == 201) {
            this.showError(results.message, false);
            this.loading ? this.loading.close() : '';
          }
          else if (results.code == 400) {
            this.loading ? this.loading.close() : '';
            localStorage.removeItem("photoArtsAdminToken");
            this.router.navigate(['/admin']);
          }
          else if (results.code == 401) {
            this.token = results.data.new_token;
            localStorage.setItem("photoArtsAdminToken", this.token);
            this.verify2faOPT(codeData);
          }
          else {
            this.loading ? this.loading.close() : '';
            this.showError(results.message, false);
          }
        }, error => {
          this.loading ? this.loading.close() : '';
          this.showError("Unable to connect with server, please reload the page.", false);
          /* console.log(error.status); */
          /* console.log(error); */
        });
      }
      else {
        this.loading ? this.loading.close() : '';
        this.dataService.postData("disable2faByAdmin", {
          "verify_code": codeData.verify_code,
          "google2fa_secret": this.admin_detail.google2fa_secret
        }, {
            headers: {
              'Authorization': 'Bearer' + localStorage.getItem('photoArtsAdminToken')
            }
          }).subscribe(results => {
            /* console.log(results); */
            if (results.code == 200) {
              localStorage.setItem("photoArtsAdminToken", results.data.token);
              this.admin_detail = results.data.user_detail;
              localStorage.setItem("admin_detail", JSON.stringify(this.admin_detail));
              localStorage.setItem("admin_detail", JSON.stringify(results.data.user_detail));
              this.token = localStorage.getItem('photoArtsAdminToken');
              this.showSuccess(results.message, false);
              this.loading ? this.loading.close() : '';
              this.dialogRef.close(this.admin_detail);
              if (this.admin_detail.shouldNavigate == true) {
                this.router.navigate(['/admin/categories']);
              }
            }
            else if (results.code == 201) {
              this.showError(results.message, false);
              this.loading ? this.loading.close() : '';
            }
            else if (results.code == 400) {
              this.loading ? this.loading.close() : '';
              localStorage.removeItem("photoArtsAdminToken");
              this.router.navigate(['/admin']);
            }
            else if (results.code == 401) {
              this.token = results.data.new_token;
              localStorage.setItem("photoArtsAdminToken", this.token);
              this.verify2faOPT(codeData);
            }
            else {
              this.loading ? this.loading.close() : '';
              this.showError(results.message, false);
            }
          }, (error: any) => {
            this.loading ? this.loading.close() : '';
            this.showError("Unable to connect with server, please reload the page.", false);
            /* console.log(error.status); */
            /* console.log(error); */
          });
      }
    }
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

import { Component } from '@angular/core';
import { MdDialog, MdSnackBar, MdSnackBarConfig } from '@angular/material';
import { Router } from '@angular/router';
import { DataService } from '../data.service';
import { LoadingComponent } from '../loading/loading.component';
import { EnterOTPComponent } from '../enter-otp/enter-otp.component';
import { ERROR, ENV_CONFIG } from '../app.constants';

@Component({
  templateUrl: './login.component.html'
})
export class LoginComponent {

  user_detail: any = {};
  successMsg: string = "";
  errorMsg: string = "";
  loading: any;
  admin_detail: any = JSON.parse(localStorage.getItem("admin_detail"));

  constructor(private dataService: DataService, private router: Router, public dialog: MdDialog, public snackBar: MdSnackBar) {
   
    if (this.router.url == "/admin" || this.router.url == "/") {
      if (localStorage.getItem('photoArtsAdminToken')) {
        this.router.navigate(['/admin/categories']);
      }
    }
  }

  do_login(user_detail) {
    if (typeof user_detail == "undefined" || user_detail == "" || user_detail == null) {
      // this.errorMsg = "Please Enter Username";
      this.showError("Please Enter Username", false);
      return false;
    }
    else if (typeof user_detail.user_id == "undefined" || user_detail.user_id == "" || user_detail.user_id == null) {
      // this.errorMsg = "Please Enter Username";
      this.showError("Please Enter Username", false);
      return false;
    }
    else if (typeof user_detail.password == "undefined" || user_detail.password == "" || user_detail.password == null) {
      // this.errorMsg = "Please Enter Password";
      this.showError("Please Enter Password", false);
      return false;
    }
    else {
      this.loading = this.dialog.open(LoadingComponent);
      this.dataService.postData('doLogin',
        {
          "email_id": user_detail.user_id,
          "password": user_detail.password
        }, {}).subscribe(results => {
          if (results.code == 200) {
            this.errorMsg = "";
            localStorage.setItem('photoArtsAdminToken', results.data.token);
            this.admin_detail = results.data.user_detail;
            this.loading.close();
            // this.successMsg = results.message;
            if (ENV_CONFIG.ENABLE_2FA === true) {
              if (this.admin_detail.google2fa_enable == 0 || this.admin_detail.google2fa_enable == false) {
                localStorage.setItem('at', results.data.token);
                this.admin_detail.enable_subcategory = false;
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
          else {
            this.successMsg = "";
            // this.errorMsg = results.message;
            this.showError(results.message, false);
            this.loading.close();
          }
        });
    }
  }


  openOTPDialog(admin_detail) {
    admin_detail.shouldNavigate = true;
    let dialogRef = this.dialog.open(EnterOTPComponent, {
      disableClose: true,
      panelClass: 'enter-otp-container',
      data: { admin_detail: admin_detail, occuredFrom: 'login' }
    });
    dialogRef.afterClosed().subscribe(result => {
      if (result) {
      }
      else {
        localStorage.setItem("admin_detail", JSON.stringify(this.admin_detail));
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

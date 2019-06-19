
import { Component, OnInit, Inject } from '@angular/core';
import { MdDialog, MdDialogRef, MD_DIALOG_DATA, MdSnackBar, MdSnackBarConfig } from '@angular/material';
import { Router } from '@angular/router';
import { DataService } from '../data.service';
import { ERROR } from '../app.constants';
import { LoadingComponent } from '../loading/loading.component';


@Component({
  selector: 'app-enter-password',
  templateUrl: './enter-password.component.html',
  styleUrls: ['./enter-password.component.css']
})
export class EnterPasswordComponent implements OnInit {

  passwordData: any = {};
  token: any;
  cnf_password: any;
  admin_detail: any = {};
  loading: any;

  constructor(public dialogRef: MdDialogRef<EnterPasswordComponent>, @Inject(MD_DIALOG_DATA) public data: any, private dataService: DataService, public dialog: MdDialog, private router: Router, public snackBar: MdSnackBar) {
    this.token = localStorage.getItem('photoArtsAdminToken');
    this.admin_detail = this.data;
  }

  ngOnInit() {
  }

  disable2faByAdmin(passwordData) {
    /* console.log(passwordData); */
    if (typeof passwordData.current_password == "undefined" || this.dataService.trim(passwordData.current_password) == "" || passwordData.current_password == null) {
      this.showError(ERROR.EMPTY_PASSWORD, false);
      return false;
    }
    else {
      this.loading = this.dialog.open(LoadingComponent);
      this.token = localStorage.getItem('photoArtsAdminToken');
      this.dataService.postData("disable2faByAdmin", passwordData, {
        headers: {
          'Authorization': 'Bearer' + this.token
        }
      }).subscribe(results => {
        if (results.code == 200) {
          this.loading.close();
          this.admin_detail.google2fa_enable = 0;
          localStorage.setItem("admin_detail", JSON.stringify(this.admin_detail));
          this.showSuccess(results.message, false);
          this.dialogRef.close();
        }
        else if (results.code == 201) {
          this.showError(results.message, false);
          this.loading.close();
        }
        else if (results.code == 400) {
          this.loading.close();
          this.dialogRef.close(true);
          localStorage.removeItem("at");
          this.router.navigate(['/login']);
        }
        else {
          this.loading.close();
          this.showError(results.message, false);
        }
      }, (error: any) => {
        console.log(error);
        this.loading.close();
        this.showError(ERROR.SERVER_ERR, false);
      });
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

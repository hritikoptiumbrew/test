import { Component } from '@angular/core';
import { MdDialog } from '@angular/material';
import { Router } from '@angular/router';
import { DataService } from '../data.service';
import { LoadingComponent } from '../loading/loading.component';

@Component({
  templateUrl: './login.component.html'
})
export class LoginComponent {

  user_detail: any = {};
  successMsg: string = "";
  errorMsg: string = "";
  loading: any;

  constructor(private dataService: DataService, private router: Router, public dialog: MdDialog) {
    if (this.router.url == "/admin" || this.router.url == "/") {
      if (localStorage.getItem('photoArtsAdminToken')) {
        this.router.navigate(['/admin/categories']);
      }
    }
  }

  do_login(user_detail) {
    if (typeof user_detail == "undefined" || user_detail == "" || user_detail == null) {
      this.errorMsg = "Please Enter Username";
      return false;
    }
    else if (typeof user_detail.user_id == "undefined" || user_detail.user_id == "" || user_detail.user_id == null) {
      this.errorMsg = "Please Enter Username";
      return false;
    }
    else if (typeof user_detail.password == "undefined" || user_detail.password == "" || user_detail.password == null) {
      this.errorMsg = "Please Enter Password";
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
            this.loading.close();
            this.successMsg = results.message;
            this.router.navigate(['/admin/categories']);
          }
          else {
            this.successMsg = "";
            this.errorMsg = results.message;
            this.loading.close();
          }
        });
    }
  }

}

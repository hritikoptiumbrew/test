import { Component, OnInit } from '@angular/core';
import { MdDialog } from '@angular/material';
import { Router } from '@angular/router';
import { DataService } from '../data.service';
import { LoadingComponent } from '../loading/loading.component';

@Component({
  templateUrl: './settings.component.html'
})
export class SettingsComponent implements OnInit {

  token: any;
  successMsg: any;
  errorMsg: any;
  cnf_password: any;
  passwordData: any = {};
  loading: any;


  constructor(private dataService: DataService, private router: Router, public dialog: MdDialog) {
  }

  ngOnInit() {
    this.token = localStorage.getItem('photoArtsAdminToken');
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
      this.loading = this.dialog.open(LoadingComponent);
      this.errorMsg = "";
      this.successMsg = "";
      this.dataService.postData("changePassword", {
        "current_password": passwordData.current_password,
        "new_password": passwordData.new_password
      }, {
          headers: {
            'Authorization': 'Bearer' + this.token
          }
        }).subscribe(results => {
          if (results.code == 200) {
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
            this.changePassword(passwordData);
          }
          else {
            this.errorMsg = results.message;
            this.loading.close();
          }
        })
    }
  }

}

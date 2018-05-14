import { Component, OnInit } from '@angular/core';
import { MdDialog, MdDialogRef } from '@angular/material';
import { Router, ActivatedRoute } from '@angular/router';
import { Http, RequestOptions, Headers, Response, RequestMethod, RequestOptionsArgs } from '@angular/http';
import { Observable } from 'rxjs/Rx';
import { DataService } from '../data.service';
import { HOST } from '../app.constants';
import { LoadingComponent } from '../loading/loading.component';

@Component({
  templateUrl: './notification.component.html'
})
export class NotificationComponent implements OnInit {

  token: any;
  successMsg: any;
  errorMsg: any;
  private sub: any; //route subscriber
  private sub_category_id: any;
  notification: any = {};
  loading: any;
  current_path: any = "";
  constructor(public route: ActivatedRoute, private dataService: DataService, private router: Router, public dialog: MdDialog) {
  }

  ngOnInit() {
    this.token = localStorage.getItem('photoArtsAdminToken');
    this.current_path = this.getLocalStorageData();
    this.sub = this.route.params
      .subscribe(params => {
        this.sub_category_id = params['sub_category_id'];
      });
  }

  sendNotification(notification) {
    notification.sub_category_id = this.sub_category_id;
    if (typeof notification == "undefined" || notification == "" || notification == null) {
      this.successMsg = "";
      this.errorMsg = "Please Enter Title";
      return false;
    }
    else if (typeof notification.title == "undefined" || notification.title == "" || notification.title == null) {
      this.successMsg = "";
      this.errorMsg = "Please Enter Title";
      return false;
    }
    else if (typeof notification.message == "undefined" || notification.message == "" || notification.message == null) {
      this.successMsg = "";
      this.errorMsg = "Please Enter Message";
      return false;
    }
    else {
      this.loading = this.dialog.open(LoadingComponent);
      this.errorMsg = "";
      this.dataService.postData("sendPushNotification", {
        "data": {
          "GCM_DATA": notification
        }
      }, {
          headers: {
            'Authorization': 'Bearer' + this.token
          }
        }).subscribe(results => {
          if (results.code == 200) {
            this.notification = {};
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
            this.sendNotification(notification);
          }
          else {
            this.errorMsg = results.message;
            this.loading.close();
          }
        })
    }
  }

  getLocalStorageData() {
    let tmp_selected_catagory = JSON.parse(localStorage.getItem("selected_catagory"));
    let tmp_selected_sub_catagory = JSON.parse(localStorage.getItem("selected_sub_catagory"));
    let tmp_current_path = tmp_selected_catagory.name + " / " + tmp_selected_sub_catagory.name;
    return tmp_current_path;
  }

}

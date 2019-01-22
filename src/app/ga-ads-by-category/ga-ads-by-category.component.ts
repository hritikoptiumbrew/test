import { Component, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { MdDialog } from '@angular/material';
import { DataService } from '../data.service';
import { LoadingComponent } from '../loading/loading.component';
import { DeleteUserGeneratedComponent } from '../delete-user-generated/delete-user-generated.component';

@Component({
  selector: 'app-ga-ads-by-category',
  templateUrl: './ga-ads-by-category.component.html',
  styleUrls: ['./ga-ads-by-category.component.css']
})
export class GaAdsByCategoryComponent implements OnInit {

  token: any;
  ad_category_data: any = {};
  selected_sub_category: any = JSON.parse(localStorage.getItem("selected_sub_catagory"));
  selected_admob_catagory: any = JSON.parse(localStorage.getItem("selected_admob_catagory"));
  selectedTabIndex: any = 0;
  platform: any;
  errorMsg: any;
  successMsg: any;
  total_record: any;
  loading: any;

  constructor(public route: ActivatedRoute, private dataService: DataService, private router: Router, public dialog: MdDialog) {
    /* this.loading = this.dialog.open(LoadingComponent); */
    this.token = localStorage.getItem('photoArtsAdminToken');
  }

  ngOnInit() {
    /* console.log(this.ad_category_data.android); */
    if (this.selectedTabIndex == 1) {
      this.platform = 1;
      if (this.ad_category_data.ios.length == 0) {
        this.ad_category_data.ios.push({
          "advertise_category_id": this.ad_category_data.advertise_category_id,
          "sub_category_id": this.selected_sub_category.sub_category_id,
          "server_id": ""
        });
      }
    }
    else {
      this.platform = 2;
      if (this.ad_category_data.android.length == 0) {
        this.ad_category_data.android.push({
          "advertise_category_id": this.ad_category_data.advertise_category_id,
          "sub_category_id": this.selected_sub_category.sub_category_id,
          "server_id": ""
        });
      }
    }
  }

  addAdvertisement(ad_id) {
    this.errorMsg = "";
    this.successMsg = "";
    if (typeof ad_id.server_id == "undefined" || ad_id.server_id.trim() == "" || ad_id.server_id == null) {
      this.errorMsg = "Please Enter Ad ID";
      return false;
    }
    else {
      this.loading = this.dialog.open(LoadingComponent);
      this.dataService.postData('addAdvertiseServerId',
        {
          "advertise_category_id": this.ad_category_data.advertise_category_id,
          "sub_category_id": this.selected_sub_category.sub_category_id,
          "server_id": ad_id.server_id,
          "sub_category_advertise_server_id": ad_id.sub_category_advertise_server_id,
          "device_platform": this.platform
        }, {
          headers: {
            'Authorization': 'Bearer ' + this.token
          }
        }).subscribe(results => {
          if (results.code == 200) {
            this.errorMsg = "";
            this.successMsg = results.message;
            this.loading.close();
          }
          else if (results.code == 400) {
            this.loading.close();
            localStorage.removeItem("photoArtsAdminToken");
            this.router.navigate(['/admin']);
          }
          else if (results.code == 401) {
            this.loading.close();
            this.token = results.data.new_token;
            localStorage.setItem("photoArtsAdminToken", this.token);
            this.addAdvertisement(ad_id);
          }
          else {
            this.loading.close();
            console.log(results.message);
            this.successMsg = "";
            this.errorMsg = results.message;
          }
        });
    }
  }

  selectedIndexChangeFunc(event) {
    /* console.log("Tab Changed", event); */
    this.selectedTabIndex = event;
    if (this.selectedTabIndex == 1) {
      this.platform = 1;
    }
    else {
      this.platform = 2;
    }
  }

  addAdvertisementField(platform) {
    /* console.log(platform); */
    if (this.selectedTabIndex == 1) {
      this.ad_category_data.ios.push({
        "advertise_category_id": this.ad_category_data.advertise_category_id,
        "sub_category_id": this.selected_sub_category.sub_category_id,
        "server_id": ""
      });
    }
    else {
      this.ad_category_data.android.push({
        "advertise_category_id": this.ad_category_data.advertise_category_id,
        "sub_category_id": this.selected_sub_category.sub_category_id,
        "server_id": ""
      });
    }
  }


  removeAdvertisementField(ad_id, i) {
    if (this.selectedTabIndex == 1) {
      if (!ad_id.sub_category_advertise_server_id) {
        this.ad_category_data.ios.splice(i, 1);
        if (this.ad_category_data.ios.length == 0) {
          this.ad_category_data.ios.push({
            "advertise_category_id": this.ad_category_data.advertise_category_id,
            "sub_category_id": this.selected_sub_category.sub_category_id,
            "server_id": ""
          });
        }
      }
      else {
        let tmp_request_data = {
          "sub_category_advertise_server_id": ad_id.sub_category_advertise_server_id
        };
        let dialogRef = this.dialog.open(DeleteUserGeneratedComponent, { disableClose: true });
        dialogRef.componentInstance.delete_request_data = tmp_request_data;
        dialogRef.componentInstance.API_NAME = "deleteAdvertiseServerId";
        dialogRef.afterClosed().subscribe(result => {
          if (!result) {
            this.ad_category_data.ios.splice(i, 1);
            if (this.ad_category_data.ios.length == 0) {
              this.ad_category_data.ios.push({
                "advertise_category_id": this.ad_category_data.advertise_category_id,
                "sub_category_id": this.selected_sub_category.sub_category_id,
                "server_id": ""
              });
            }
          }
        });
      }
    }
    else {
      if (!ad_id.sub_category_advertise_server_id) {
        this.ad_category_data.android.splice(i, 1);
        if (this.ad_category_data.android.length == 0) {
          this.ad_category_data.android.push({
            "advertise_category_id": this.ad_category_data.advertise_category_id,
            "sub_category_id": this.selected_sub_category.sub_category_id,
            "server_id": ""
          });
        }
      }
      else {
        let tmp_request_data = {
          "sub_category_advertise_server_id": ad_id.sub_category_advertise_server_id
        };
        let dialogRef = this.dialog.open(DeleteUserGeneratedComponent, { disableClose: true });
        dialogRef.componentInstance.delete_request_data = tmp_request_data;
        dialogRef.componentInstance.API_NAME = "deleteAdvertiseServerId";
        dialogRef.afterClosed().subscribe(result => {
          if (!result) {
            this.ad_category_data.android.splice(i, 1);
            if (this.ad_category_data.android.length == 0) {
              this.ad_category_data.android.push({
                "advertise_category_id": this.ad_category_data.advertise_category_id,
                "sub_category_id": this.selected_sub_category.sub_category_id,
                "server_id": ""
              });
            }
          }
        });
      }
    }
  }

}

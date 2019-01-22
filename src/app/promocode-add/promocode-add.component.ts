import { Component, OnInit } from '@angular/core';
import { MdDialog, MdDialogRef } from '@angular/material';
import { Router } from '@angular/router';
import { DataService } from '../data.service';
import { LoadingComponent } from '../loading/loading.component';

@Component({
  selector: 'app-promocode-add',
  templateUrl: './promocode-add.component.html',
  styleUrls: ['./promocode-add.component.css']
})
export class PromocodeAddComponent implements OnInit {

  token: any;
  loading: any;
  promocode_details: any = {};
  errorMsg: any;
  constructor(public dialogRef: MdDialogRef<PromocodeAddComponent>, private dataService: DataService, private router: Router, public dialog: MdDialog) {
    this.token = localStorage.getItem('photoArtsAdminToken');
    this.promocode_details.device_platform = 1;
  }

  ngOnInit() {
  }

  addPromoCode(promocode_details) {
    if (typeof promocode_details == 'undefined' || promocode_details == "" || promocode_details == null) {
      this.errorMsg = "Please enter promo code";
      return false;
    }
    else if (typeof promocode_details.promo_code == 'undefined' || promocode_details.promo_code == "" || promocode_details.promo_code == null) {
      this.errorMsg = "Please enter promo code";
      return false;
    }
    else if (typeof promocode_details.package_name == 'undefined' || promocode_details.package_name == "" || promocode_details.package_name == null) {
      this.errorMsg = "Please enter package name";
      return false;
    }
    else if (typeof promocode_details.device_udid == 'undefined' || promocode_details.device_udid == "" || promocode_details.device_udid == null) {
      this.errorMsg = "Please enter device UDID";
      return false;
    }
    else {
      this.errorMsg = "";
      this.loading = this.dialog.open(LoadingComponent);
      this.dataService.postData('addPromoCode', promocode_details, {
        headers: {
          'Authorization': 'Bearer ' + this.token
        }
      }).subscribe(results => {
        if (results.code == 200) {
          this.loading.close();
          this.dialogRef.close();
        }
        else if (results.code == 400) {
          localStorage.removeItem("photoArtsAdminToken");
          this.loading.close();
          this.router.navigate(['/admin']);
        }
        else if (results.code == 401) {
          this.token = results.data.new_token;
          this.loading.close();
          localStorage.setItem("photoArtsAdminToken", this.token);
          this.addPromoCode(promocode_details);
        }
        else {
          this.errorMsg = results.message;
          this.loading.close();
        }
      });
    }
  }


}

import { Component, OnInit } from '@angular/core';
import { MdDialog, MdDialogRef } from '@angular/material';
import { Router, ActivatedRoute } from '@angular/router';
import { DataService } from '../data.service';
import { LoadingComponent } from '../loading/loading.component';

@Component({
  selector: 'app-advertisements-link',
  templateUrl: './advertisements-link.component.html'
})
export class AdvertisementsLinkComponent implements OnInit {

  token: any;
  sub_category_id: any;
  advertisement_list: any = [];
  advertisement_data: any = {};
  selected_sub_category: any = {};
  total_record: any;
  selected_platform: any;
  successMsg: any;
  errorMsg: any;
  loading: any;

  constructor(public dialogRef: MdDialogRef<AdvertisementsLinkComponent>, public route: ActivatedRoute, private dataService: DataService, private router: Router, public dialog: MdDialog) {
    this.loading = this.dialog.open(LoadingComponent);
  }

  ngOnInit() {
    this.token = localStorage.getItem('photoArtsAdminToken');
    this.getAllSubCategoryWithLinkDetails(this.advertisement_data, this.selected_sub_category);
  }

  getAllSubCategoryWithLinkDetails(advertisement_data, selected_sub_category) {
    /* console.log(advertisement_data, selected_sub_category); */
    this.errorMsg = "";
    this.successMsg = "";
    this.dataService.postData('getAllAdvertisementToLinkAdvertisement',
      {
        "sub_category_id": selected_sub_category.sub_category_id
      }, {
        headers: {
          'Authorization': 'Bearer ' + this.token
        }
      }).subscribe(results => {
        if (results.code == 200) {
          this.successMsg = "";
          let tmp_advertisement_list = results.data.result
          this.advertisement_list = [];
          tmp_advertisement_list.forEach(element => {
            this.selected_platform = this.selected_platform.toLowerCase();
            element.platform = element.platform.toLowerCase();
            if (this.selected_platform == element.platform) {
              this.advertisement_list.push(element);
            }
          });
          this.total_record = tmp_advertisement_list;
          this.loading.close();
        }
        else if (results.code == 400) {
          this.loading.close();
          localStorage.removeItem("photoArtsAdminToken");
          this.router.navigate(['/admin']);
        }
        else if (results.code == 401) {
          this.errorMsg = "";
          this.successMsg = "";
          this.token = results.data.new_token;
          this.loading.close();
          localStorage.setItem("photoArtsAdminToken", this.token);
          this.getAllSubCategoryWithLinkDetails(advertisement_data, selected_sub_category);
        }
        else {
          this.loading.close();
          this.errorMsg = results.message;
          this.successMsg = "";
        }
      });
  }

  linkCatalogWithSubCategory(selected_sub_category, API_NAME, advertisement_details) {
    /* console.log(selected_sub_category, API_NAME, advertisement_details); */
    this.errorMsg = "";
    this.successMsg = "";
    this.loading = this.dialog.open(LoadingComponent);
    this.dataService.postData(API_NAME,
      {
        "advertise_link_id": advertisement_details.advertise_link_id,
        "sub_category_id": selected_sub_category.sub_category_id
      }, {
        headers: {
          'Authorization': 'Bearer ' + this.token
        }
      }).subscribe(results => {
        if (results.code == 200) {
          this.successMsg = results.message;
          this.getAllSubCategoryWithLinkDetails(this.advertisement_data, this.selected_sub_category);
          this.errorMsg = "";
          this.loading.close();
        }
        else if (results.code == 400) {
          this.loading.close();
          localStorage.removeItem("photoArtsAdminToken");
          this.router.navigate(['/admin']);
        }
        else if (results.code == 401) {
          this.token = results.data.new_token;
          this.errorMsg = "";
          this.successMsg = "";
          this.loading.close();
          localStorage.setItem("photoArtsAdminToken", this.token);
          this.linkCatalogWithSubCategory(selected_sub_category, API_NAME, advertisement_details);
        }
        else {
          this.loading.close();
          this.errorMsg = results.message;
          this.successMsg = "";
        }
      });
  }

  imagePreview(advertisement_details) {
    this.dataService.viewImage(advertisement_details.original_img);
  }

  closeDialog() {
    this.dialogRef.close();
  }

}

import { Component, OnInit, Renderer, ViewChild, ElementRef, Input, Output, EventEmitter, ViewEncapsulation } from '@angular/core';
import { MdDialog, MdDialogRef } from '@angular/material';
import { Location } from '@angular/common';
import { Http, RequestOptions, Headers, Response, RequestMethod, RequestOptionsArgs } from '@angular/http';
import { Observable } from 'rxjs/Rx';
import { Router, ActivatedRoute } from '@angular/router';
import { DataService } from '../data.service';
import { HOST } from '../app.constants';
import { LoadingComponent } from '../loading/loading.component';
import { AdvertisementsDeleteComponent } from '../advertisements-delete/advertisements-delete.component';
import { AdvertisementsUpdateComponent } from '../advertisements-update/advertisements-update.component';
import { AdvertisementsAddComponent } from '../advertisements-add/advertisements-add.component';
import { AdvertisementsLinkComponent } from '../advertisements-link/advertisements-link.component';

@Component({
  templateUrl: './advertisements.component.html'
})
export class AdvertisementsComponent implements OnInit {

  token: any;
  successMsg: any;
  errorMsg: any;
  advertisement_list: any;
  public sub: any; //route subscriber
  public sub_category_id: any;
  total_record: any;
  sortByTagName: any;
  order_type: Boolean;
  order_type_val: any;
  itemsPerPage: any;;
  currentPage: number = 1;
  searchArray: any[];
  searchErr: string;
  searchTag: any;
  searchQuery: any;
  loading: any;
  current_path: any = "";

  constructor(public route: ActivatedRoute, private dataService: DataService, private router: Router, public dialog: MdDialog, public location: Location) {
    this.loading = this.dialog.open(LoadingComponent);
    this.itemsPerPage = 200;
    this.searchArray = [
      { 'searchTagValue': 'platform', 'searchTagName': 'Platform' },
    ];
    this.searchTag = this.searchArray[0].searchTagValue;
  }

  ngOnInit() {
    this.token = localStorage.getItem('photoArtsAdminToken');
    this.current_path = this.getLocalStorageData();
    this.sub = this.route.params
      .subscribe(params => {
        this.sub_category_id = params['sub_category_id'];
        this.getAllAdvertisements(this.sub_category_id, this.currentPage);
      });
  }

  pageChanged(event) {
    this.loading = this.dialog.open(LoadingComponent);
    this.currentPage = event;
    this.getAllAdvertisements(this.sub_category_id, this.currentPage);
  }

  getAllAdvertisements(sub_category_id, currentPage) {
    this.token = localStorage.getItem('photoArtsAdminToken');
    this.dataService.postData('getAllLink',
      {
        "sub_category_id": sub_category_id,
        "page": currentPage,
        "item_count": this.itemsPerPage
      }, {
        headers: {
          'Authorization': 'Bearer ' + this.token
        }
      }).subscribe(results => {
        if (results.code == 200) {
          this.advertisement_list = results.data.link_list;
          this.total_record = results.data.total_record;
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
          this.token = results.data.new_token;
          localStorage.setItem("photoArtsAdminToken", this.token);
          this.getAllAdvertisements(this.sub_category_id, this.currentPage);
        }
        else {
          this.loading.close();
          this.successMsg = "";
          this.errorMsg = results.message;
        }
      }, error => {
        /* console.log(error.status); */
        /* console.log(error); */
      });
  }

  linkAdvertisement(advertisement: any) {
    let dialogRef = this.dialog.open(AdvertisementsLinkComponent);
    let selected_sub_catagory = JSON.parse(localStorage.getItem("selected_sub_catagory"));
    dialogRef.componentInstance.advertisement_data = advertisement;
    dialogRef.componentInstance.selected_sub_catagory = selected_sub_catagory;
    dialogRef.componentInstance.advertisement_data.sub_category_id = this.sub_category_id;
    dialogRef.afterClosed().subscribe(result => {
      this.getAllAdvertisements(this.sub_category_id, this.currentPage);
    });
  }

  getAllAdvertisementToLinkAdvertisement(sub_category_id, platform) {
    /* console.log(sub_category_id); */
    /* console.log(platform); */
    let dialogRef = this.dialog.open(AdvertisementsLinkComponent);
    let selected_sub_catagory = JSON.parse(localStorage.getItem("selected_sub_catagory"));
    dialogRef.componentInstance.selected_sub_catagory = selected_sub_catagory;
    dialogRef.componentInstance.advertisement_data.sub_category_id = this.sub_category_id;
    dialogRef.componentInstance.selected_platform = platform;
    dialogRef.afterClosed().subscribe(result => {
      this.getAllAdvertisements(this.sub_category_id, this.currentPage);
    });
  }

  imagePreview(image_url) {
    this.dataService.viewImage(image_url);
  }

  addAdvertisement(platform) {
    let advertisement_platform = JSON.parse(JSON.stringify(platform));
    let dialogRef = this.dialog.open(AdvertisementsAddComponent);
    dialogRef.componentInstance.advertisement_data.platform = advertisement_platform;
    dialogRef.componentInstance.advertisement_data.sub_category_id = this.sub_category_id;
    dialogRef.afterClosed().subscribe(result => {
      this.getAllAdvertisements(this.sub_category_id, this.currentPage);
    });
  }

  updateAdvertisement(advertisement) {
    let advertisement_data = JSON.parse(JSON.stringify(advertisement));
    let dialogRef = this.dialog.open(AdvertisementsUpdateComponent);
    dialogRef.componentInstance.advertisement_data = advertisement_data;
    dialogRef.afterClosed().subscribe(result => {
      this.getAllAdvertisements(this.sub_category_id, this.currentPage);
    });
  }

  deleteAdvertiement(advertisement) {
    let dialogRef = this.dialog.open(AdvertisementsDeleteComponent);
    dialogRef.componentInstance.advertise_link_id = advertisement.advertise_link_id;
    dialogRef.afterClosed().subscribe(result => {
      this.getAllAdvertisements(this.sub_category_id, this.currentPage);
    });
  }

  getLocalStorageData() {
    let tmp_selected_catagory = JSON.parse(localStorage.getItem("selected_catagory"));
    let tmp_selected_sub_catagory = JSON.parse(localStorage.getItem("selected_sub_catagory"));
    let tmp_current_path = tmp_selected_catagory.name + " / " + tmp_selected_sub_catagory.name;
    return tmp_current_path;
  }

  goBackFunction() {
    this.location.back();
  }

}

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
  selector: 'app-adv-management',
  templateUrl: './adv-management.component.html'
})
export class AdvManagementComponent implements OnInit {

  token: any;
  successMsg: any;
  errorMsg: any;
  advertisement_list: any;
  private sub: any; //route subscriber
  private sub_category_id: any;
  total_record: any;
  sortByTagName: any;
  order_type: Boolean;
  order_type_val: any;
  itemsPerPage: any;;
  itemsPerPageArray: any[];
  currentPage: number = 1;
  searchArray: any[];
  searchErr: string;
  searchTag: any;
  searchQuery: any;
  loading: any;
  current_path: any = "";

  constructor(public route: ActivatedRoute, private dataService: DataService, private router: Router, public dialog: MdDialog, public location: Location) {
    this.itemsPerPage = 1000;
    this.searchArray = [
      { 'searchTagValue': 'platform', 'searchTagName': 'Platform' },
    ];
    this.searchTag = this.searchArray[0].searchTagValue;
    this.itemsPerPageArray = [
      { 'itemPerPageValue': '15', 'itemPerPageName': '15' },
      { 'itemPerPageValue': '30', 'itemPerPageName': '30' },
      { 'itemPerPageValue': '45', 'itemPerPageName': '45' },
      { 'itemPerPageValue': '60', 'itemPerPageName': '60' },
      { 'itemPerPageValue': '75', 'itemPerPageName': '75' },
      { 'itemPerPageValue': '90', 'itemPerPageName': '90' },
      { 'itemPerPageValue': '100', 'itemPerPageName': '100' }
    ];
    /* this.itemsPerPage = this.itemsPerPageArray[0].itemPerPageValue; */
    this.getAllAdvertisements(this.currentPage, this.itemsPerPage);
  }

  ngOnInit() {
    this.token = localStorage.getItem('photoArtsAdminToken');
    this.current_path = this.getLocalStorageData();
  }

  pageChanged(event) {
    this.currentPage = event;
    this.getAllAdvertisements(this.currentPage, this.itemsPerPage);
  }

  itemPerPageChanged(itemsPerPage) {
    this.itemsPerPage = itemsPerPage;
    this.getAllAdvertisements(this.currentPage, this.itemsPerPage);
  }

  getAllAdvertisements(currentPage, itemsPerPage) {
    this.loading = this.dialog.open(LoadingComponent);
    this.token = localStorage.getItem('photoArtsAdminToken');
    this.dataService.postData('getAllAdvertisements',
      {
        "page": currentPage,
        "item_count": this.itemsPerPage
      }, {
        headers: {
          'Authorization': 'Bearer ' + this.token
        }
      }).subscribe(results => {
        if (results.code == 200) {
          this.advertisement_list = results.data.result;
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
          this.getAllAdvertisements(this.currentPage, this.itemsPerPage);
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
    let dialogRef = this.dialog.open(AdvertisementsLinkComponent, { disableClose: true });
    let selected_sub_catagory = JSON.parse(localStorage.getItem("selected_sub_catagory"));
    dialogRef.componentInstance.advertisement_data = advertisement;
    dialogRef.componentInstance.selected_sub_catagory = selected_sub_catagory;
    dialogRef.componentInstance.advertisement_data.sub_category_id = this.sub_category_id;
    dialogRef.afterClosed().subscribe(result => {
      if (!result) {
        this.getAllAdvertisements(this.currentPage, this.itemsPerPage);
      }
    });
  }

  addAdvertisement(platform) {
    let advertisement_platform = JSON.parse(JSON.stringify(platform));
    let dialogRef = this.dialog.open(AdvertisementsAddComponent, { disableClose: true });
    dialogRef.componentInstance.advertisement_data.platform = advertisement_platform;
    dialogRef.componentInstance.advertisement_data.sub_category_id = this.sub_category_id;
    dialogRef.afterClosed().subscribe(result => {
      if (!result) {
        this.getAllAdvertisements(this.currentPage, this.itemsPerPage);
      }
    });
  }

  updateAdvertisement(advertisement) {
    let advertisement_data = JSON.parse(JSON.stringify(advertisement));
    let dialogRef = this.dialog.open(AdvertisementsUpdateComponent, { disableClose: true });
    dialogRef.componentInstance.advertisement_data = advertisement_data;
    dialogRef.afterClosed().subscribe(result => {
      if (!result) {
        this.getAllAdvertisements(this.currentPage, this.itemsPerPage);
      }
    });
  }

  deleteAdvertiement(advertisement) {
    let dialogRef = this.dialog.open(AdvertisementsDeleteComponent, { disableClose: true });
    dialogRef.componentInstance.advertise_link_id = advertisement.advertise_link_id;
    dialogRef.afterClosed().subscribe(result => {
      if (!result) {
        this.getAllAdvertisements(this.currentPage, this.itemsPerPage);
      }
    });
  }

  imagePreview(image_url) {
    this.dataService.viewImage(image_url);
  }

  getLocalStorageData() {
    /* let tmp_selected_catagory = JSON.parse(localStorage.getItem("selected_catagory"));
    let tmp_selected_sub_catagory = JSON.parse(localStorage.getItem("selected_sub_catagory"));
    let tmp_current_path = tmp_selected_catagory.name + " / " + tmp_selected_sub_catagory.name; */
    return "tmp_current_path";
  }

  goBackFunction() {
    this.location.back();
  }

}

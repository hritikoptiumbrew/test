import { Component, OnInit, Renderer, ViewChild, ElementRef, Input, Output, EventEmitter, ViewEncapsulation } from '@angular/core';
import { MdDialog, MdDialogRef } from '@angular/material';
import { Http, RequestOptions, Headers, Response, RequestMethod, RequestOptionsArgs } from '@angular/http';
import { Observable } from 'rxjs/Rx';
import { Router, ActivatedRoute } from '@angular/router';
import { DataService } from '../data.service';
import { HOST } from '../app.constants';
import { LoadingComponent } from '../loading/loading.component';

@Component({
  templateUrl: './image-details.component.html'
})
export class ImageDetailsComponent implements OnInit {

  token: any;
  successMsg: any;
  errorMsg: any;
  image_detail_list: any;
  total_record: any;
  sortByTagName: any;
  order_type: Boolean;
  order_type_val: any;
  itemsPerPageArray: any[];
  itemsPerPage: any;
  currentPage: number = 1;
  searchArray: any[];
  searchErr: string;
  searchTag: any;
  searchQuery: any;
  loading: any;

  constructor(private dataService: DataService, private router: Router, public dialog: MdDialog) {
    this.loading = this.dialog.open(LoadingComponent);
    this.itemsPerPageArray = [
      { 'itemPerPageValue': '15', 'itemPerPageName': '15' },
      { 'itemPerPageValue': '30', 'itemPerPageName': '30' },
      { 'itemPerPageValue': '45', 'itemPerPageName': '45' },
      { 'itemPerPageValue': '60', 'itemPerPageName': '60' },
      { 'itemPerPageValue': '75', 'itemPerPageName': '75' },
      { 'itemPerPageValue': '90', 'itemPerPageName': '90' },
      { 'itemPerPageValue': '100', 'itemPerPageName': '100' }
    ];
    this.itemsPerPage = this.itemsPerPageArray[0].itemPerPageValue;
  }

  ngOnInit() {
    this.token = localStorage.getItem('photoArtsAdminToken');
    this.getAllImagesWithDetails(this.currentPage, this.itemsPerPage, this.sortByTagName, this.order_type_val);
  }

  itemPerPageChanged(itemsPerPage) {
    this.loading = this.dialog.open(LoadingComponent);
    this.itemsPerPage = itemsPerPage;
    this.getAllImagesWithDetails(this.currentPage, this.itemsPerPage, this.sortByTagName, this.order_type_val);
  }

  sortBy(sortByTagName, order_type_val) {
    this.loading = this.dialog.open(LoadingComponent);
    this.sortByTagName = sortByTagName;
    if (order_type_val == "ASC") {
      this.order_type = false;
      this.order_type_val = "DESC";
    }
    else {
      this.order_type = true;
      this.order_type_val = "ASC";
    }
    this.getAllImagesWithDetails(this.currentPage, this.itemsPerPage, this.sortByTagName, this.order_type_val);
  }

  pageChanged(event) {
    this.loading = this.dialog.open(LoadingComponent);
    this.currentPage = event;
    this.getAllImagesWithDetails(this.currentPage, this.itemsPerPage, this.sortByTagName, this.order_type_val);
  }

  getAllImagesWithDetails(currentPage, itemsPerPage, sortByTagName, order_type_val) {
    this.dataService.postData('getImageDetails',
      {
        "item_count": itemsPerPage,
        "page": currentPage,
        "order_by": sortByTagName,
        "order_type": order_type_val
      }, {
        headers: {
          'Authorization': 'Bearer ' + this.token
        }
      }).subscribe(results => {
        if (results.code == 200) {
          this.image_detail_list = results.data.image_details;
          this.total_record = results.data.total_record;
          this.loading.close();
          this.errorMsg = "";
          this.successMsg = results.message;
        }
        else if (results.code == 400) {
          this.loading.close();
          localStorage.removeItem("photoArtsAdminToken");
          this.router.navigate(['/admin']);
        }
        else if (results.code == 401) {
          this.token = results.data.new_token;
          localStorage.setItem("photoArtsAdminToken", this.token);
          this.getAllImagesWithDetails(this.currentPage, this.itemsPerPage, this.sortByTagName, this.order_type_val);
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

}

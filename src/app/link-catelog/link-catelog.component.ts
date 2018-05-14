import { Component, OnInit, Renderer, ViewChild, ElementRef, Input, Output, EventEmitter } from '@angular/core';
import { MdDialog, MdDialogRef } from '@angular/material';
import { Http, RequestOptions, Headers, Response, RequestMethod, RequestOptionsArgs } from '@angular/http';
import { Observable } from 'rxjs/Rx';
import { Router, ActivatedRoute } from '@angular/router';
import { DataService } from '../data.service';
import { HOST } from '../app.constants';
import { LoadingComponent } from '../loading/loading.component';

@Component({
  selector: 'app-link-catelog',
  templateUrl: './link-catelog.component.html'
})
export class LinkCatelogComponent implements OnInit {

  token: any;
  sub_category_id: any;
  catalog_list: any;
  catalog_data: any = {};
  total_record: any;
  fileList: any;
  file: any;
  formData = new FormData();
  successMsg: any;
  errorMsg: any;
  loading: any;

  constructor(public dialogRef: MdDialogRef<LinkCatelogComponent>, public route: ActivatedRoute, private dataService: DataService, private router: Router, public dialog: MdDialog) {
    this.loading = this.dialog.open(LoadingComponent);
  }

  ngOnInit() {
    this.token = localStorage.getItem('photoArtsAdminToken');
    this.getAllSubCategoryWithLinkDetails(this.catalog_data);
  }

  getAllSubCategoryWithLinkDetails(catalog_data) {
    this.errorMsg = "";
    this.successMsg = "";
    this.dataService.postData('getAllSubCategoryForLinkCatalog',
      {
        "catalog_id": catalog_data.catalog_id,
        "category_id": catalog_data.category_id
      }, {
        headers: {
          'Authorization': 'Bearer ' + this.token
        }
      }).subscribe(results => {
        if (results.code == 200) {
          this.successMsg = "";
          this.catalog_list = results.data.category_list;
          this.total_record = this.catalog_list.length;
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
          this.getAllSubCategoryWithLinkDetails(catalog_data);
        }
        else {
          this.loading.close();
          this.errorMsg = results.message;
          this.successMsg = "";
        }
      });
  }

  linkCatalogWithSubCategory(catalog, API_NAME) {
    this.errorMsg = "";
    this.successMsg = "";
    this.loading = this.dialog.open(LoadingComponent);
    this.dataService.postData(API_NAME,
      {
        "catalog_id": this.catalog_data.catalog_id,
        "sub_category_id": catalog.sub_category_id
      }, {
        headers: {
          'Authorization': 'Bearer ' + this.token
        }
      }).subscribe(results => {
        if (results.code == 200) {
          this.successMsg = results.message;
          this.getAllSubCategoryWithLinkDetails(this.catalog_data);
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
          this.linkCatalogWithSubCategory(catalog, API_NAME);
        }
        else {
          this.loading.close();
          this.errorMsg = results.message;
          this.successMsg = "";
        }
      });
  }

  closeDialog() {
    this.dialogRef.close();
  }
}

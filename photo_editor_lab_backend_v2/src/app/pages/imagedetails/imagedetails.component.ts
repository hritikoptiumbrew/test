/*
 * Optimumbrew Technology

 * Project       : Photo Editor Lab
 * File          : imagedetails.component.ts
 * File Created  : Saturday, 24th October 2020 03:56:00 pm
 * Author        : Optimumbrew
 * Auther Email  : info@optimumbrew.com
 * Last Modified : Saturday, 24th October 2020 04:06:40 pm
 * -----
 * Copyright 2018 - 2020 Optimumbrew Technology
 */


import { Component, OnInit } from '@angular/core';
import { DomSanitizer } from '@angular/platform-browser';
import { DataService } from 'app/data.service';
import { UtilService } from 'app/util.service';
import { LocalDataSource } from 'ng2-smart-table';
import { PaginatePipe } from 'ngx-pagination';
import { ERROR, ENV_CONFIG } from '../../app.constants';
@Component({
  selector: 'ngx-imagedetails',
  templateUrl: './imagedetails.component.html',
  styleUrls: ['./imagedetails.component.scss']
})
export class ImagedetailsComponent implements OnInit {
  dataSource: LocalDataSource
  totalRecord: any = 0;
  token: any = 0;
  imageDetails: any;
  j: any = 1;
  currentPage:any = 1;
  pageSize: any = [15,30,45,60,75,90,100];
  selectedImageSize= '15';
  sortByTagName: any;
  order_type: Boolean;
  order_type_val: any;
  previousLabel = "<";
  nextLabel=">";
  private units = [
    'bytes',
    'KB',
    'MB',
    'GB',
    'TB',
    'PB'
  ];
  constructor(private dataService: DataService, private _sanitizer: DomSanitizer, private utils: UtilService) {
    this.token = localStorage.getItem("at");
  }

  ngOnInit(): void {
    // this.gettotalImageCount();
    this.getAllImageDetails();
  }
  setPageSize(value) {
    this.selectedImageSize = value;
    this.getAllImageDetails();
  }
  handlePageChange(event): void {
    this.currentPage = event;
    this.getAllImageDetails();
  }
  sortBy(sortByTagName, order_type_val) {
    this.sortByTagName = sortByTagName;
    if (order_type_val == "ASC") {
      this.order_type = false;
      this.order_type_val = "DESC";
    }
    else {
      this.order_type = true;
      this.order_type_val = "ASC";
    }
    this.getAllImageDetails();
  }
  getAllImageDetails() {
    this.utils.showPageLoader();
    this.dataService.postData('getImageDetails',
      {
        "item_count": this.selectedImageSize,
        "page": this.currentPage,
        "order_by": this.sortByTagName,
        "order_type": this.order_type_val
      }, {
      headers: {
        'Authorization': 'Bearer ' + this.token
      }
    }).then((results: any) => {
      if (results.code == 200) {
       
        var j = 1;
        results.data.image_details.forEach(appname => {
          appname.resolution = appname.width + " * " + appname.height;
          appname.id = j;
          j++; 
        });
        this.imageDetails = results.data.image_details;
        this.totalRecord = results.data.total_record;
        this.dataSource = new LocalDataSource(this.imageDetails);
        this.utils.hidePageLoader();
      }
      else if (results.code == 201) {
        this.utils.showError(results.message, 4000);
        this.utils.hidePageLoader();
      }
      else if (results.status || results.status == 0) {
        this.utils.showError(ERROR.SERVER_ERR, 4000);
        this.utils.hidePageLoader();
      }
      else {
        this.utils.showError(results.message, 4000);
        this.utils.hidePageLoader();
      }
    }, (error: any) => {
      console.log(error);
      this.utils.hidePageLoader();
      this.utils.showError(ERROR.SERVER_ERR, 4000);
    }).catch((error: any) => {
      console.log(error);
      this.utils.hidePageLoader();
      this.utils.showError(ERROR.SERVER_ERR, 4000);
    });
  }
  imageLoad(event){
    if(event.target.previousElementSibling != null)
    {
      event.target.previousElementSibling.remove();
    }
  }
}

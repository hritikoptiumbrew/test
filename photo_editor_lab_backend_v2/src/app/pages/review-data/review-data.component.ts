import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { DataService } from 'app/data.service';
import { DomSanitizer } from '@angular/platform-browser';
import { UtilService } from 'app/util.service';
import { ERROR, ENV_CONFIG } from '../../app.constants';




@Component({
  selector: 'ngx-review-data',
  templateUrl: './review-data.component.html',
  styleUrls: ['./review-data.component.scss']
})
export class ReviewDataComponent implements OnInit {
  token: any;
  reviewArray: any;
  totalRecord : any;
  sortByTagName: any;
  order_type: boolean;
  order_type_val: string;
  currentPage:any = 1;
  pageSize: any = [25,50,75,100];
  selectedImageSize:any = '25';
  previousLabel = "<";
  nextLabel=">";

  constructor(private dataService: DataService, private utils: UtilService, private _sanitizer: DomSanitizer) { 
    this.token = localStorage.getItem("at");
  }

  ngOnInit(): void {
    this.getapidata();
  }

  setPageSize(value: any) {
    this.selectedImageSize = value;
    this.getapidata();
  }
  
  handlePageChange(event: any): void {
    this.currentPage = event;
    this.getapidata();
  }

  sortBy(sortByTagName: any, order_type_val: string) {
    this.sortByTagName = sortByTagName;
    if (order_type_val == "ASC") {
      this.order_type = false;
      this.order_type_val = "DESC";
    }
    else {
      this.order_type = true;
      this.order_type_val = "ASC";
    }
    this.getapidata();
  }

  getapidata(){
    this.utils.showPageLoader();
    this.dataService.postData('getAiChats',{},{
      headers: {
        'Authorization': 'Bearer ' + this.token
      }
    }).then((results: any) => {
      if (results.code == 200) {
        for(let i=0; i<=results.data.length-1; i++){
          results.data[i].app_json = JSON.parse(results.data[i].app_json);
          results.data[i].device_json =JSON.parse(results.data[i].device_json);
          if(results.data[i].app_json == null){
            results.data[i].app_json = {"app_version" : "","platform" : ""}
          }
          if(results.data[i].device_json == null){
            results.data[i].device_json = {"country" : "","language" : ""}
          }
        } 
        this.reviewArray = results.data;  
        console.log(results.data)
        this.totalRecord = results.data.length;
        this.utils.hidePageLoader();
      }else if (results.code == 201) {
        this.utils.showError(results.message, 4000);
        this.utils.hidePageLoader();
      } else if (results.status || results.status == 0) {
        this.utils.showError(ERROR.SERVER_ERR, 4000);
        this.utils.hidePageLoader();
      }
      else {
        this.utils.showError(results.message, 4000);
        this.utils.hidePageLoader();
      }
    },(error: any) => {
      console.log(error);
      this.utils.hidePageLoader();
      this.utils.showError(ERROR.SERVER_ERR, 4000);
    }).catch((error: any) => {
      console.log(error);
      this.utils.hidePageLoader();
      this.utils.showError(ERROR.SERVER_ERR, 4000);
    });
  }
}

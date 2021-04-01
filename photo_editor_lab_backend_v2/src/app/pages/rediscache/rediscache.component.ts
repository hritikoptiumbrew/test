/*
 * Optimumbrew Technology

 * Project       : Photo Editor Lab
 * File          : rediscache.component.ts
 * File Created  : Monday, 26th October 2020 10:58:28 am
 * Author        : Optimumbrew
 * Auther Email  : info@optimumbrew.com
 * Last Modified : Monday, 26th October 2020 11:02:15 am
 * -----
 * Copyright 2018 - 2020 Optimumbrew Technology
 */


import { Component, OnInit, ViewChild } from '@angular/core';
import { DomSanitizer } from '@angular/platform-browser';
import { lastChildNotComment } from '@nebular/theme/components/helpers';
import { DataService } from 'app/data.service';
import { UtilService } from 'app/util.service';
import { LocalDataSource, Ng2SmartTableComponent } from 'ng2-smart-table';
import { ERROR, ENV_CONFIG } from '../../app.constants';

@Component({
  selector: 'ngx-rediscache',
  templateUrl: './rediscache.component.html',
  styleUrls: ['./rediscache.component.scss']
})
export class RediscacheComponent implements OnInit {

  totalRecord: any = 0;
  keyDetails: any = [];
  keys_list: any = [];
  token: any;
  j = 1;
  dataSource: LocalDataSource
  @ViewChild('table') table: Ng2SmartTableComponent;
  settings = {
    selectMode: 'multi',
    columns: {
      id: {
        title: "#",
        filter: false,
        width: '75px',
        type: 'text',
        sort: false
      },
      key: {
        title: "Cache Key",
        type: 'string',
      }
    },
    actions: {
      add: false,
      position: 'right',
      delete: false,
      edit: false,
    },
    pager: {
      display: false
    }
  };
  constructor(private dataService: DataService, private utils: UtilService, private _sanitizer: DomSanitizer) {
    this.token = localStorage.getItem("at");
  }

  ngOnInit(): void {
    this.getAllKeys();
  }
  getRows(event) {
    this.j = 1;
  }
  keyupEvent(event){
    this.j = 1;
  }
  getAllKeys() {
    this.keyDetails = [];
    this.j = 1;
    this.utils.showPageLoader();
    this.dataService.postData('getRedisKeys',
      {}, {
      headers: {
        'Authorization': 'Bearer ' + this.token
      }
    }).then((results: any) => {
      if (results.code == 200) {
        this.totalRecord = results.data.keys_list.length;
        var i =1;
        for (let j = 0; j < this.totalRecord; j++) {
          this.keyDetails.push({ "key": results.data.keys_list[j] , "id":i});
          i++;
        }
        this.keyDetails = JSON.parse(JSON.stringify(this.keyDetails));
        this.dataSource = new LocalDataSource(this.keyDetails);
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
  selectKeys(event) {
    var totalRecord = document.querySelectorAll('tbody .ng2-smart-row').length;
    if (event.selected.length < totalRecord) {
      this.table.isAllSelected = false;
    }
    else if (event.selected.length == totalRecord) {
      this.table.isAllSelected = true;
    }
    this.keys_list = [];
    event.selected.forEach(element => {
      this.keys_list.push(element);
    });
  }
  removeKeys() {
    if (this.keys_list.length == 0) {
      this.utils.showError(ERROR.SEL_CACHE_KEYS, 4000);
    }
    else {
      this.utils.getConfirm().then((result) => {
        this.utils.showLoader();
        this.dataService.postData('deleteRedisKeys',
          {
            "keys_list": this.keys_list
          }, {
          headers: {
            'Authorization': 'Bearer ' + this.token
          }
        }).then((results: any) => {
          if (results.code == 200) {
            this.table.isAllSelected = false;
            this.utils.hideLoader();
            this.getAllKeys();
            this.utils.showSuccess(results.message, 4000);
          }
          else if (results.code == 201) {
            this.utils.showError(results.message, 4000);
            this.utils.hideLoader();
          }
          else if (results.status || results.status == 0) {
            this.utils.showError(ERROR.SERVER_ERR, 4000);
            this.utils.hideLoader();
          }
          else {
            this.utils.showError(results.message, 4000);
            this.utils.hideLoader();
          }
        }, (error: any) => {
          console.log(error);
          this.utils.hideLoader();
          this.utils.showError(ERROR.SERVER_ERR, 4000);
        }).catch((error: any) => {
          console.log(error);
          this.utils.hideLoader();
          this.utils.showError(ERROR.SERVER_ERR, 4000);
        });
      });
    }
  }
}

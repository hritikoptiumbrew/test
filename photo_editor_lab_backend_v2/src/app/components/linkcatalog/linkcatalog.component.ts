/*
 * Optimumbrew Technology

 * Project       : Photo Editor Lab
 * File          : linkcatalog.component.ts
 * File Created  : Monday, 19th October 2020 09:32:59 am
 * Author        : Optimumbrew
 * Auther Email  : info@optimumbrew.com
 * Last Modified : Thursday, 29th October 2020 11:25:58 am
 * -----
 * Copyright 2018 - 2020 Optimumbrew Technology
 */


import { Component, OnInit } from '@angular/core';
import { NbDialogRef } from '@nebular/theme';
import { DataService } from 'app/data.service';
import { UtilService } from 'app/util.service';
import { ERROR, ENV_CONFIG } from '../../app.constants';
@Component({
  selector: 'ngx-linkcatalog',
  templateUrl: './linkcatalog.component.html',
  styleUrls: ['./linkcatalog.component.scss']
})
export class LinkcatalogComponent implements OnInit {

  catalogData: any;
  token: any;
  catalogList: any;
  linkStatus: any;
  constructor(private dialogRef: NbDialogRef<LinkcatalogComponent>, private dataService: DataService, private utils: UtilService) {
    this.token = localStorage.getItem('at');
    this.utils.dialogref = this.dialogRef;
  }

  ngOnInit(): void {
    this.getAllSubCategoryWithLinkDetails(this.catalogData)
  }

  closeDialog() {
    this.dialogRef.close({ res: this.linkStatus });
  }

  linkCatalogStatus(catalog, apiName, indexItem) {
    const selected_sub_category = JSON.parse(localStorage.getItem('selected_sub_category'));
    if (catalog.is_multi_page_support != selected_sub_category.is_multi_page_support) {
      this.utils.showError("results.message", 4000);
      return;
    }
    this.utils.showLoader();
    this.dataService.postData(apiName,
      {
        "catalog_id": this.catalogData.catalog_id,
        "sub_category_id": JSON.parse(JSON.stringify(catalog)).sub_category_id
      }, {
      headers: {
        'Authorization': 'Bearer ' + this.token
      }
    }).then((results: any) => {
      if (results.code == 200) {
        this.utils.showSuccess(results.message, 4000);
        this.linkStatus = "add";
        if (apiName == "linkCatalog") {
          this.catalogList[indexItem].linked = 1;
        }
        else {
          this.catalogList[indexItem].linked = 0;
        }
        this.utils.hideLoader();
        // console.log(this.catalogList[0]);
        // console.log(indexItem);
        // this.getAllSubCategoryWithLinkDetails(this.catalogData);
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
  }

  getAllSubCategoryWithLinkDetails(catalogData) {
    this.utils.showLoader();
    this.dataService.postData('getAllSubCategoryForLinkCatalog',
      {
        "catalog_id": catalogData.catalog_id,
        "category_id": catalogData.category_id
      }, {
      headers: {
        'Authorization': 'Bearer ' + this.token
      }
    }).then((results: any) => {
      if (results.code == 200) {
        this.utils.hideLoader();
        this.catalogList = results.data.category_list;
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
  }

}

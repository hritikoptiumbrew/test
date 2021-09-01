/*
 * Optimumbrew Technology

 * Project       : Photo Editor Lab
 * File          : movetocatalog.component.ts
 * File Created  : Monday, 19th October 2020 05:10:28 pm
 * Author        : Optimumbrew
 * Auther Email  : info@optimumbrew.com
 * Last Modified : Thursday, 29th October 2020 11:27:02 am
 * -----
 * Copyright 2018 - 2020 Optimumbrew Technology
 */


import { Component, OnInit } from '@angular/core';
import { DataService } from 'app/data.service';
import { UtilService } from 'app/util.service';
import { ERROR, ENV_CONFIG } from '../../app.constants';
import * as $ from 'jquery';
import { NbDialogRef } from '@nebular/theme';
@Component({
  selector: 'ngx-movetocatalog',
  templateUrl: './movetocatalog.component.html',
  styleUrls: ['./movetocatalog.component.scss']
})
export class MovetocatalogComponent implements OnInit {

  catalogData: any;
  selectedCategory: any;
  selectedCatalog: any;
  subCategoryList: any;
  totalRecords: any = 0;
  token: any;
  errormsg: any;
  imgIds:any = [];
  title:string = 'Template';
  constructor(private dialog: NbDialogRef<MovetocatalogComponent>, private dataService: DataService, private utils: UtilService) {
    this.token = localStorage.getItem('at');
    this.utils.dialogref = this.dialog;
  }

  ngOnInit(): void {
    this.selectedCategory = JSON.parse(localStorage.getItem('selected_category'));
    this.selectedCatalog = JSON.parse(localStorage.getItem('selected_catalog'));
    this.getSubList(this.catalogData);
  }

  closeDialog() {
    this.dialog.close({ res: "" });
  }

  checkboxSelect(catData, event) {
    this.errormsg = "";
    this.selectedCatalog = catData;
    $('input[name="' + event.target.name + '"]').not(event.target).prop('checked', false);
    this.subCategoryList.forEach((sbCtDtl: any) => {
      sbCtDtl.has_tplt = false;
      sbCtDtl.catalog_list.forEach((ctlgDtl: any) => {
        if (this.selectedCatalog.catalog_id == ctlgDtl.catalog_id) {
          ctlgDtl.is_linked = event.target.checked == true ? 1 : 0;
          this.selectedCatalog = ctlgDtl;
          sbCtDtl.has_tplt = true;
        }
        else {

          ctlgDtl.is_linked = 0;
        }
      });
    });
  }

  getSubList(catalogData) {
    this.utils.showLoader();
    this.dataService.postData('getAllSubCategoryToMoveTemplate',
      {
        "img_id": catalogData.img_id,
        "category_id": this.selectedCategory.category_id,
        "is_featured": this.selectedCatalog.is_featured
      }, {
      headers: {
        'Authorization': 'Bearer ' + this.token
      }
    }).then((results: any) => {
      if (results.code == 200) {
        this.subCategoryList = results.data.sub_category_list;
        if (this.subCategoryList) {
          this.totalRecords = this.subCategoryList.length;
        }
        this.subCategoryList.forEach((sbCtDlt: any) => {
          sbCtDlt.has_tplt = false;
          sbCtDlt.catalog_list.forEach((ctLgDtl: any) => {
            if (ctLgDtl.is_linked == 1 || ctLgDtl.is_linked == true) {
              this.selectedCatalog = ctLgDtl;
              sbCtDlt.has_tplt = true;
            }
          });
        });
        this.utils.hideLoader();
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

  moveTemplate() {
    if (this.selectedCatalog.is_linked == 0 || this.selectedCatalog.is_linked == false) {
      this.errormsg = ERROR.SEL_TMPLT_MV;
      return false;
    }
    else {
      this.utils.showLoader();
      this.errormsg = "";
      this.dataService.postData('moveTemplate',
        {
          "catalog_id": this.selectedCatalog.catalog_id,
          "template_list": this.imgIds.length == 0?[this.catalogData.img_id]:this.imgIds
        }, {
        headers: {
          'Authorization': 'Bearer ' + this.token
        }
      }).then((results: any) => {
        if (results.code == 200) {
          this.utils.hideLoader();
          this.utils.showSuccess(results.message, 4000);
          this.dialog.close({ res: "add" });
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
}

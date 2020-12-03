/*
 * Optimumbrew Technology

 * Project       : Photo Editor Lab
 * File          : addsearchtags.component.ts
 * File Created  : Thursday, 15th October 2020 01:12:03 pm
 * Author        : Optimumbrew
 * Auther Email  : info@optimumbrew.com
 * Last Modified : Thursday, 29th October 2020 11:18:19 am
 * -----
 * Copyright 2018 - 2020 Optimumbrew Technology
 */


import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { NbDialogRef, NbDialogService, NbWindowRef } from '@nebular/theme';
import { DataService } from 'app/data.service';
import { UtilService } from 'app/util.service';
import { ValidationsService } from 'app/validations.service';
import { ERROR, ENV_CONFIG } from '../../app.constants';
@Component({
  selector: 'ngx-addsearchtags',
  templateUrl: './addsearchtags.component.html',
  styleUrls: ['./addsearchtags.component.scss']
})
export class AddsearchtagsComponent implements OnInit {


  constructor(private dialogRef: NbDialogRef<AddsearchtagsComponent>, private validService: ValidationsService, private dataService: DataService, private utils: UtilService, private route: Router) {
    this.token = localStorage.getItem('at');
    this.utils.dialogref = this.dialogRef;
  }
  subCatData: any;
  token: any;
  searchTagsData: any;
  searchTagList: any = [];
  tmpCategoryList: any = [];
  tagName: any;
  errormsg = ERROR;
  titleHeader: any;
  totalRecord: any;
  ngOnInit(): void {
    this.titleHeader = "Search tags of " + this.subCatData.name;
    this.getAllCategorySearchTags();
   
  }


  checkValidation(id, type, catId, blankMsg, typeMsg,validType) {
    var validObj = {
      "id": id,
      "errorId": catId,
      "type": type,
      "blank_msg": blankMsg,
      "type_msg": typeMsg,
      "button_check": {
        "button_id": "addTag",
        "successArr": ['tagInput']
      }
    }
    this.validService.checkValid(validObj);
    if(validType != "blank")
    {
      this.addSearchTag();
    }
  }

  addSearchTag() {
    var validObj = [
      {
        "id": 'tagInput',
        "errorId": 'tagError',
        "type": '',
        "blank_msg": ERROR.TAG_EMPTY_NAME,
        "type_msg": '',
      }
    ]
    var addStatus = this.validService.checkAllValid(validObj);
    if (addStatus){
      this.utils.showLoader();
    this.dataService.postData('addSearchCategoryTag', {
      "tag_name": this.tagName,
      "sub_category_id": this.subCatData.sub_category_id
    },
      {
        headers: {
          'Authorization': 'Bearer ' + this.token
        }
      }).then((results: any) => {
        if (results.code == 200) {
          this.utils.hideLoader();
          this.getAllCategorySearchTags();
          this.tagName = "";
          this.utils.dialogStatus = "add";
        }
        else if (results.code == 201) {
          document.getElementById("tagError").innerHTML = results.message;
          // this.utils.showError(results.message, 4000);
          this.tagName = "";
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
  closeDialog() {
    this.dialogRef.close();
  }
  resetRow(category, i) {
    this.searchTagList[i].tag_name = this.tmpCategoryList[i].tag_name;
    category.is_update = false;
    category.tag_name = this.tmpCategoryList[i].tag_name;
  }
  showUpdate(searchTag) {
    this.searchTagList.forEach((element, i) => {
      this.resetRow(element, i);
    });
    searchTag.is_update = true;
  }
  updateSearchTag(item) {
    if (typeof item.tag_name == "undefined" || item.tag_name.trim() == "" || item.tag_name == null) {
      // this.utils.showError(ERROR.TAG_EMPTY_NAME, 4000);
      document.getElementById("editInputTag").innerHTML = ERROR.TAG_EMPTY_NAME; 
      return false;
    }
    else {
      this.dataService.postData('updateSearchCategoryTag', {
        "tag_name": item.tag_name,
        "sub_category_tag_id": item.sub_category_tag_id,
        "sub_category_id": this.subCatData.sub_category_id
      },
        {
          headers: {
            'Authorization': 'Bearer ' + this.token
          }
        }).then((results: any) => {
          if (results.code == 200) {
            this.utils.hideLoader();
            this.utils.showSuccess(results.message, 4000);
            this.getAllCategorySearchTags();
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
  moveToFirst(item,indexItem) {
    this.utils.showLoader();
    this.dataService.postData('setCategoryTagRankOnTheTopByAdmin', {
      "sub_category_tag_id": item.sub_category_tag_id
    }, {
      headers: {
        'Authorization': 'Bearer ' + this.token
      }
    }).then((results: any) => {
      if (results.code == 200) {
        this.utils.hideLoader();
        this.utils.showSuccess(results.message, 4000);
        var element = this.searchTagList[indexItem];
        this.searchTagList.splice(indexItem, 1);
        this.searchTagList.splice(0, 0, element);
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
  deleteSearchTag(item) {
    this.utils.getConfirm().then((result) => {
      this.utils.showLoader();
      this.dataService.postData('deleteSearchCategoryTag', {
        "sub_category_tag_id": item.sub_category_tag_id
      }, {
        headers: {
          'Authorization': 'Bearer ' + this.token
        }
      }).then((results: any) => {
        if (results.code == 200) {
          this.utils.hideLoader();
          this.utils.showSuccess(results.message, 4000);
          this.getAllCategorySearchTags();
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
  getAllCategorySearchTags() {
    this.dataService.postData('getCategoryTagBySubCategoryId',
      {
        "sub_category_id": this.subCatData.sub_category_id,
      }, {
      headers: {
        'Authorization': 'Bearer ' + this.token
      }
    }).then((results: any) => {
      if (results.code == 200) {
        // this.searchTagsData = results.data.result;
        this.searchTagList = results.data.result;
        this.tmpCategoryList = JSON.parse(JSON.stringify(results.data.result));
        this.searchTagList.forEach(element => {
          element.is_update = false;
        });
        this.totalRecord = results.data.total_record;
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
}

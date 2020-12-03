/*
 * Optimumbrew Technology

 * Project       : Photo Editor Lab
 * File          : addvalidations.component.ts
 * File Created  : Tuesday, 27th October 2020 05:39:32 pm
 * Author        : Optimumbrew
 * Auther Email  : info@optimumbrew.com
 * Last Modified : Thursday, 29th October 2020 11:20:47 am
 * -----
 * Copyright 2018 - 2020 Optimumbrew Technology
 */


import { Component, OnInit } from '@angular/core';
import { NbDialogRef } from '@nebular/theme';
import { DataService } from 'app/data.service';
import { UtilService } from 'app/util.service';
import { ValidationsService } from 'app/validations.service';
import { ERROR, ENV_CONFIG } from '../../app.constants';
@Component({
  selector: 'ngx-addvalidations',
  templateUrl: './addvalidations.component.html',
  styleUrls: ['./addvalidations.component.scss']
})
export class AddvalidationsComponent implements OnInit {

  validationsData: any;
  categoryData: any;
  selectedCategory: any = 'none';
  errormsg = ERROR;
  validName: any;
  validValue: any;
  validDescrip: any;
  validSelectCatalog: any = 0;
  validSelectCover: any = 0;
  token: any;
  constructor(private dataService: DataService, private utils: UtilService, private validService: ValidationsService, private dialogRef: NbDialogRef<AddvalidationsComponent>) {
    this.token = localStorage.getItem("at");
    this.utils.dialogref = this.dialogRef;
  }

  ngOnInit(): void {
    if (this.validationsData) {
      this.selectedCategory = this.validationsData.category_id.toString();
      this.validName = this.validationsData.validation_name;
      this.validValue = this.validationsData.max_value_of_validation;
      this.validSelectCatalog = this.validationsData.is_featured;
      this.validSelectCover = this.validationsData.is_catalog;
      this.validDescrip = this.validationsData.description;
    }
  }

  closeDialog() {
    this.dialogRef.close({ res: "" });
  }
  checkCategValid() {
    if (this.selectedCategory == undefined || this.selectedCategory == "none") {
      document.getElementById("selectCategoryError").innerHTML = ERROR.VALIDATION_SEL_CAT;
    }
    else {
      document.getElementById("selectCategoryError").innerHTML = "";
      return true;
    }
  }
  checkValidation(id, type, catId, blankMsg, typeMsg, validType) {
    document.getElementById("valiaddError").innerHTML = "";
      var validObj = {
        "id": id,
        "errorId": catId,
        "type": type,
        "blank_msg": blankMsg,
        "type_msg": typeMsg,
        "button_check": {
          "button_id": "validAdd",
          "successArr": ['validNameInput', 'ValueInput', 'descripText']
        }
      }
      this.validService.checkValid(validObj);
      if (validType != "blank") {
        this.addValidation();
      }
  }
  addValidation() {
    var validObj = [
      {
        "id": 'validNameInput',
        "errorId": 'validInputError',
        "type": '',
        "blank_msg": ERROR.VALIDATION_NAME_EMPTY,
        "type_msg": '',
      },
      {
        "id": 'ValueInput',
        "errorId": 'valueInputError',
        "type": '',
        "blank_msg": ERROR.VALIDATION_VALUE_EMPTY,
        "type_msg": '',
      },
      {
        "id": 'descripText',
        "errorId": 'descipTextError',
        "type": '',
        "blank_msg": ERROR.VALIDATION_DESCRIP_EMPTY,
        "type_msg": '',
      }
    ]
    var addStatus = this.validService.checkAllValid(validObj);
    var categStatus = this.checkCategValid();
    if (addStatus && categStatus) {
      this.utils.showLoader();
      var requestData;
      var apiUrl;
      if (this.validationsData) {
        requestData = {
          "setting_id": this.validationsData.setting_id,
          "category_id": this.selectedCategory,
          "validation_name": this.validName,
          "max_value_of_validation": this.validValue,
          "is_featured": this.validSelectCatalog ? 1 : 0,
          "is_catalog": this.validSelectCover ? 1 : 0,
          "description": this.validDescrip
        };
        apiUrl = "editValidation";
      }
      else {
        requestData = {
          "category_id": this.selectedCategory,
          "validation_name": this.validName,
          "max_value_of_validation": this.validValue,
          "is_featured": this.validSelectCatalog ? 1 : 0,
          "is_catalog": this.validSelectCover ? 1 : 0,
          "description": this.validDescrip
        };
        apiUrl = "addValidation";
      }
      this.dataService.postData(apiUrl,
        requestData, {
        headers: {
          'Authorization': 'Bearer ' + this.token
        }
      }).then((results: any) => {
        if (results.code == 200) {
          this.utils.hideLoader();
          this.dialogRef.close({ res: "add" });
          this.utils.showSuccess(results.message, 4000);
        }
        else if (results.code == 201) {
          document.getElementById("valiaddError").innerHTML = results.message;
          // this.utils.showError(results.message, 4000);
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

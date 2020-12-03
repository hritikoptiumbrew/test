/*
 * Optimumbrew Technology

 * Project       : Photo Editor Lab
 * File          : editcategory.component.ts
 * File Created  : Saturday, 17th October 2020 11:08:43 am
 * Author        : Optimumbrew
 * Auther Email  : info@optimumbrew.com
 * Last Modified : Thursday, 29th October 2020 11:22:06 am
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
  selector: 'ngx-editcategory',
  templateUrl: './editcategory.component.html',
  styleUrls: ['./editcategory.component.scss']
})
export class EditcategoryComponent implements OnInit {

  categoryData: any;
  catName: any;
  errormsg = ERROR;
  token: any;
  constructor(private validService: ValidationsService, private utils: UtilService, private dataService: DataService, private dialogRef: NbDialogRef<EditcategoryComponent>) {
    this.token = localStorage.getItem('at');
    this.utils.dialogref = this.dialogRef;
  }

  ngOnInit(): void {
    if(this.categoryData)
    {
      this.catName = this.categoryData.name;
    }
  }

  closedialog() {
    this.dialogRef.close({ res: "" });
  }
  checkValidation(id, type, catId, blankMsg, typeMsg,validType) {
    var validObj = {
      "id": id,
      "errorId": catId,
      "type": type,
      "blank_msg": blankMsg,
      "type_msg": typeMsg,
      "button_check": {
        "button_id": "Catedit",
        "successArr": ['editCatInput']
      }
    }
    this.validService.checkValid(validObj);
    if(validType != "blank")
    {
      this.editCategory();
    }
  }
  editCategory() {
    var validObj = [
      {
        "id": 'editCatInput',
        "errorId": 'inputCatError',
        "type": '',
        "blank_msg": ERROR.CAT_NAME_EMPTY,
        "type_msg": '',
      }
    ]
    var addStatus = this.validService.checkAllValid(validObj);
    if (addStatus){
      this.utils.showLoader();
      var apiUrl;
      var requestData;
      if(this.categoryData)
      {
        requestData = {
          "category_id": this.categoryData.category_id,
          "name": this.catName
        }
        apiUrl = "updateCategory";
      }
      else
      {
        requestData = {
          "name": this.catName
        }
        apiUrl = "addCategory";
      }
      this.dataService.postData(apiUrl,requestData, {
      headers: {
        'Authorization': 'Bearer ' + this.token
      }
    }).then((results: any) => {
      if (results.code == 200) {
        this.utils.hideLoader();
        this.dialogRef.close({ res: 'add' });
        this.utils.showSuccess(results.message, 4000);
      }
      else if (results.code == 201) {
        document.getElementById("inputCatError").innerHTML = results.message;
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

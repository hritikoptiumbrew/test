/*
 * Optimumbrew Technology

 * Project       : Photo Editor Lab
 * File          : addsubcategory.component.ts
 * File Created  : Thursday, 15th October 2020 01:12:03 pm
 * Author        : Optimumbrew
 * Auther Email  : info@optimumbrew.com
 * Last Modified : Thursday, 29th October 2020 11:19:27 am
 * -----
 * Copyright 2018 - 2020 Optimumbrew Technology
 */


import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { NbDialogRef, NbWindowRef } from '@nebular/theme';
import { DataService } from 'app/data.service';
import { UtilService } from 'app/util.service';
import { ValidationsService } from 'app/validations.service';
import { ERROR, ENV_CONFIG } from '../../app.constants';
@Component({
  selector: 'ngx-addsubcategory',
  templateUrl: './addsubcategory.component.html',
  styleUrls: ['./addsubcategory.component.scss']
})
export class AddsubcategoryComponent implements OnInit {

  constructor(private validService: ValidationsService, private dialogref: NbDialogRef<AddsubcategoryComponent>, private dataService: DataService, private utils: UtilService, private route: Router) {
    this.utils.dialogref = this.dialogref;
   }

  selectType: any = '0';
  subImage: any;
  subName: any;
  formData = new FormData();
  fileList: any;
  file: any;
  categoryId: any;
  token: any;
  errormsg = ERROR;
  subCatData: any;
  ngOnInit(): void {
    if (this.subCatData) {
      this.subImage = this.subCatData.thumbnail_img;
      this.subName = this.subCatData.name;
      this.selectType = this.subCatData.is_featured.toString();
      // this.utils.showLoader();
      this.categoryId = this.subCatData.sub_category_id;
    }

  }
  closeLoading() {
    this.utils.hideLoader();
  }
  closedialog() {
    this.dialogref.close({ res: "" });
  }
  fileChange(event) {
    if (event.target.files && event.target.files[0]) {
      var reader = new FileReader();
      reader.onload = (event: any) => {
        this.subImage = event.target.result;
      }
      reader.readAsDataURL(event.target.files[0]);

    }
    this.fileList = event.target.files;
    if (this.fileList.length > 0) {
      this.file = this.fileList[0];
      var filesize = Math.round(this.file.size/1024);
      if(filesize > 100)
      {
        document.getElementById("imageError").innerHTML = "Maximum 100Kb file allow to upload";
      }
      else
      {
        document.getElementById("imageError").innerHTML = "";
      }
      this.formData.append('file', this.file, this.file.name);
    }

  }
  checkImageValid() {
    document.getElementById("subCatAddError").innerHTML = "";
    if (this.subImage == undefined || this.subImage == "none" || this.subImage == "") {
      document.getElementById("imageError").innerHTML = ERROR.IMG_REQ;
    }
    else {
      if(this.file)
      {
        var filesize = Math.round(this.file.size/1024);
        if(filesize > 100)
        {
          document.getElementById("imageError").innerHTML = "Maximum 100Kb file allow to upload";
        }
        else
        {
          document.getElementById("imageError").innerHTML = "";
          return true;
        }
      }
      else
      {
        document.getElementById("imageError").innerHTML = "";
        return true;
      }
    }
  }
  checkValidation(id, type, catId, blankMsg, typeMsg,validType) {
    document.getElementById("subCatAddError").innerHTML = "";
    var validObj = {
      "id": id,
      "errorId": catId,
      "type": type,
      "blank_msg": blankMsg,
      "type_msg": typeMsg,
      "button_check": {
        "button_id": "subCatAdd",
        "successArr": ['addSubInput','subImage']
      }
    }
    this.validService.checkValid(validObj);
    if(validType != "blank")
    {
      this.addSubCategory();
    }
  }
  
  addSubCategory() {
    var validObj = [
      {
        "id": 'addSubInput',
        "errorId": 'inputError',
        "type": '',
        "blank_msg": ERROR.SUB_CAT_NAME_EMPTY,
        "type_msg": '',
      }
    ]
    var addStatus = this.validService.checkAllValid(validObj);
    var imageStatus = this.checkImageValid();
    if (addStatus && imageStatus){
      var catApliUrl;
      if (this.subCatData) {
        catApliUrl = 'updateSubCategory';
      }
      else {
        catApliUrl = 'addSubCategory';
      }
      this.token = localStorage.getItem('at');
      this.utils.showLoader();
      var category_data;
      if (this.subCatData) {
        category_data = {
          'sub_category_id': this.categoryId,
          'name': this.subName,
          'is_featured': this.selectType
        };
      }
      else {
        category_data = {
          'category_id': this.categoryId,
          'name': this.subName,
          'is_featured': this.selectType
        };
      }
      this.formData.append('request_data', JSON.stringify(category_data));
      this.dataService.postData(catApliUrl, this.formData,
        {
          headers: {
            'Authorization': 'Bearer ' + this.token
          }
        }).then((results: any) => {
          if (results.code == 200) {
            this.utils.hideLoader();
            this.dialogref.close({ res: "add" });
            this.utils.showSuccess(results.message, 4000);
          }
          else if (results.code == 201) {
            document.getElementById("subCatAddError").innerHTML = results.message;
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
  imageLoad(event){
    if(event.target.previousElementSibling != null)
    {
      event.target.previousElementSibling.remove();
    }
  }
}

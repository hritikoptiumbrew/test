/*
 * Optimumbrew Technology

 * Project       : Photo Editor Lab
 * File          : editfont.component.ts
 * File Created  : Thursday, 22nd October 2020 04:11:05 pm
 * Author        : Optimumbrew
 * Auther Email  : info@optimumbrew.com
 * Last Modified : Thursday, 22nd October 2020 04:12:01 pm
 * -----
 * Copyright 2018 - 2020 Optimumbrew Technology
 */


import { Component, OnInit } from '@angular/core';
import { NbDialogRef } from '@nebular/theme';
import { DataService } from 'app/data.service';
import { UtilService } from 'app/util.service';
import { ValidationsService } from 'app/validations.service';
import { ERROR, ENV_CONFIG } from '../../app.constants';
import * as $ from 'jquery';
@Component({
  selector: 'ngx-editfont',
  templateUrl: './editfont.component.html',
  styleUrls: ['./editfont.component.scss']
})
export class EditfontComponent implements OnInit {

  fontData: any;
  iosFontName: any;
  androidFontName: any;
  errormsg = ERROR;
  token: any;
  formData = new FormData();
  fileList: any;
  file: any;
  catalogId: any;
  selectedCategory: any = JSON.parse(localStorage.getItem("selected_category"));
  selectedCatalog: any = JSON.parse(localStorage.getItem("selected_catalog"));
  fontDetails: any = {};
  constructor(private validService: ValidationsService, private utils: UtilService, private dataService: DataService, private dialogRef: NbDialogRef<EditfontComponent>) {
    this.token = localStorage.getItem('at');
    this.utils.dialogref = this.dialogRef;
  }

  ngOnInit(): void {
    this.iosFontName = this.fontData.ios_font_name;
    this.androidFontName = this.fontData.android_font_name;
  }

  closedialog() {
    this.dialogRef.close({ res: "" });
  }
  fileChange(event) {
    if (event.target.files && event.target.files[0]) {
      var reader = new FileReader();
      reader.onload = (event: any) => {
        this.fontDetails.font_file = event.target.result;
        document.getElementById("fontFileError").innerHTML = "";
        document.getElementById("fontAddError").innerHTML = "";
      }
      reader.readAsDataURL(event.target.files[0]);
    }
    this.fileList = event.target.files;
    if (this.fileList.length > 0) {
      this.formData.delete("file");
      this.file = this.fileList[0];
      this.formData.append('file', this.file, this.file.name);
    }
  }
  checkValidation(id, type, catId, blankMsg, typeMsg, validType) {
    document.getElementById("fontAddError").innerHTML = "";
    var validObj = {
      "id": id,
      "errorId": catId,
      "type": type,
      "blank_msg": blankMsg,
      "type_msg": typeMsg,
      "button_check": {
        "button_id": "fontedit",
        "successArr": ['editIosInput', 'editandroidInput']
      }
    }
    this.validService.checkValid(validObj);
    if (validType != "blank") {
      if (this.fontData) {
        this.editCategory();
      }
      else {
        this.addNewFont(this.fontDetails, '0');
      }
    }
  }
  editCategory() {
    var validObj = [
      {
        "id": 'editIosInput',
        "errorId": 'inputIosError',
        "type": '',
        "blank_msg": ERROR.FONT_NAME_EMPTY,
        "type_msg": '',
      },
      {
        "id": 'editandroidInput',
        "errorId": 'inputAndroidError',
        "type": '',
        "blank_msg": ERROR.FONT_PATH_EMPTY,
        "type_msg": '',
      }
    ]
    var addStatus = this.validService.checkAllValid(validObj);
    if (addStatus) {
      this.dataService.postData('editFont',
        {
          "font_id": this.fontData.font_id,
          "ios_font_name": this.iosFontName,
          "android_font_name": this.androidFontName
        }, {
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
          // this.utils.showError(results.message, 4000);
          document.getElementById("fontAddError").innerHTML = results.message;
          this.utils.hideLoader();
        }
        else if (results.status || results.status == 0) {
          this.utils.showError(ERROR.SERVER_ERR, 4000);
          this.utils.hideLoader();
        }
        else {
          // this.utils.showError(results.message, 4000);
          document.getElementById("fontAddError").innerHTML = results.message;
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
  checkfontfile() {
    if (!this.fontDetails.font_file) {
      document.getElementById("fontFileError").innerHTML = ERROR.FONT_FILE_EMPTY;
      return false;
    }
    else {
      document.getElementById("fontFileError").innerHTML = "";
      return true;
    }
  }
  addNewFont(font_details: any, is_replace) {
    var validObj = [
      {
        "id": 'addFontInput',
        "errorId": 'inputFontError',
        "type": '',
        "blank_msg": ERROR.FONT_NAME_EMPTY,
        "type_msg": '',
      }
    ]
    var addStatus = this.validService.checkAllValid(validObj);
    var fontStatus = this.checkfontfile();
    if (addStatus && fontStatus) {
      this.utils.showLoader();
      let request_data: any = {
        "category_id": this.selectedCategory.category_id,
        "is_featured": this.selectedCatalog.is_featured,
        "catalog_id": this.catalogId,
        "ios_font_name": this.fontDetails.font_name,
        "android_font_name": font_details.android_font_name,
        "is_replace": is_replace
      };
      this.formData.append("request_data", JSON.stringify(request_data));
      this.dataService.postData('addFont',
        this.formData, {
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
          // this.utils.showError(results.message, 4000);
          document.getElementById("fontAddError").innerHTML = results.message;
          this.utils.hideLoader();
        }
        else if (results.status || results.status == 0) {
          this.utils.showError(ERROR.SERVER_ERR, 4000);
          this.utils.hideLoader();
        }
        else {
          document.getElementById("fontAddError").innerHTML = results.message;
          // this.utils.showError(results.message, 4000);
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

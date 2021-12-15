/*
 * Optimumbrew Technology

 * Project       : Photo Editor Lab
 * File          : addcatalog.component.ts
 * File Created  : Saturday, 17th October 2020 04:14:40 pm
 * Author        : Optimumbrew
 * Auther Email  : info@optimumbrew.com
 * Last Modified : Thursday, 29th October 2020 11:30:47 am
 * -----
 * Copyright 2018 - 2020 Optimumbrew Technology
 */

import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { NbDialogRef } from '@nebular/theme';
import { DataService } from 'app/data.service';
import { UtilService } from 'app/util.service';
import { ValidationsService } from 'app/validations.service';
import { ERROR, ENV_CONFIG } from '../../app.constants';

@Component({
  selector: 'ngx-addcatalog',
  templateUrl: './addcatalog.component.html',
  styleUrls: ['./addcatalog.component.scss']
})
export class AddcatalogComponent implements OnInit {

  catalogData: any;
  calogImage: any;
  lanscapeImage: any;
  portraitImage: any;
  iconImage: any;
  CalogName: any;
  formData = new FormData();
  fileList: any;
  file: any;
  token: any;
  errormsg = ERROR;
  selectCalogType: any = '0';
  selectCalogPrice: any = '1';
  selectCalogPriceIOS: any = '1';
  selectedCategory: any;
  subCategoryId: any;
  catalogId: any;
  selectedCatalogType: any = '1';
  tagName: any;
  listOfTag: any = [];
  catalogList: any = [];
  indexOfCatalog: any;
  catalogTypes: any = [
    {
      name: "Normal",
      value: "1"
    },
    {
      name: "Fix date",
      value: "2"
    },
    {
      name: "Non-fix date",
      value: "3"
    },
    {
      name: "Non date",
      value: "4"
    },
  ];
  popularity: any = "1";
  eventDate: any;
  fileList1: any;
  file1: any;
  fileList2: any;
  file2: any;
  fileList3: any;
  file3: any;
  constructor(private validService: ValidationsService, private dialogref: NbDialogRef<AddcatalogComponent>, private dataService: DataService, private utils: UtilService, private route: Router) {
    this.token = localStorage.getItem('at');
    this.selectedCategory = JSON.parse(localStorage.getItem('selected_category')).category_id;
    this.subCategoryId = JSON.parse(localStorage.getItem('selected_sub_category')).sub_category_id;
    this.utils.dialogref = this.dialogref;
  }

  ngOnInit(): void {

    if (this.catalogData) {
      if (this.catalogData.webp_original_img) {
        this.calogImage = this.catalogData.webp_original_img;
      }
      else {
        this.calogImage = this.catalogData.thumbnail_img;
      }
      this.CalogName = this.catalogData.name;
      this.selectCalogType = this.catalogData.is_featured.toString();
      this.selectCalogPrice = this.catalogData.is_free.toString();
      this.selectCalogPriceIOS = this.catalogData.is_ios_free == 0 || this.catalogData.is_ios_free == 1 ? this.catalogData.is_ios_free.toString() : this.catalogData.is_free.toString();
      this.catalogId = this.catalogData.catalog_id;
      if (this.catalogData.catalog_type) {
        this.selectedCatalogType = "" + this.catalogData.catalog_type;
      }
      if (this.catalogData.popularity_rate && this.catalogData.popularity_rate != null) {
        this.popularity = "" + this.catalogData.popularity_rate;
      }
      if (this.catalogData.event_date && this.catalogData.event_date != null) {
        this.eventDate = this.catalogData.event_date;
      }
      if (this.catalogData.icon && this.catalogData.icon != null) {
        this.iconImage = this.catalogData.icon;
      }
      if (this.catalogData.compressed_landscape_img && this.catalogData.compressed_landscape_img != null) {
        this.lanscapeImage = this.catalogData.compressed_landscape_img;
      }
      if (this.catalogData.compressed_portrait_img && this.catalogData.compressed_portrait_img != null) {
        this.portraitImage = this.catalogData.compressed_portrait_img;
      }
    }

    // if(this.catalogList[this.indexOfCatalog].search_category !== null){
    //   var newList = this.catalogList[this.indexOfCatalog].search_category.split(',');
    //   if (newList.length >= 0) {
    //     for (let i = 0; i < newList.length; i++) {
    //       this.listOfTag.push(newList[i]);
    //     }
    //   }
    // }
    if (this.indexOfCatalog.search_category !== null) {
      let newList = this.indexOfCatalog.search_category.split(',')
      for (let i = 0; i < newList.length; i++) {
        this.listOfTag.push(newList[i]);
      }
    }
  }

  fileChange(event) {
    if (event.target.files && event.target.files[0]) {
      var reader = new FileReader();
      reader.onload = (event: any) => {
        this.calogImage = event.target.result;
        this.checkImageValid();
        // this.checkValidation('calogImage', 'image', 'imageCalogError', '', '');
      }
      reader.readAsDataURL(event.target.files[0]);
    }
    this.fileList = event.target.files;
    if (this.fileList.length > 0) {
      this.file = this.fileList[0];
      var filesize = Math.round(this.file.size / 1024);
      if (filesize > 100) {
        document.getElementById("imageCalogError").innerHTML = "Maximum 100Kb file allow";
      }
      else {
        document.getElementById("imageCalogError").innerHTML = "";
      }
      this.formData.append('file', this.file, this.file.name);
    }
  }
  fileChangeicon(event) {
    if (event.target.files && event.target.files[0]) {
      var reader = new FileReader();
      reader.onload = (event: any) => {
        this.iconImage = event.target.result;
        this.checkIconValid();
        // this.checkValidation('calogImage', 'image', 'imageCalogError', '', '');
      }
      reader.readAsDataURL(event.target.files[0]);
    }
    this.fileList1 = event.target.files;
    if (this.fileList1.length > 0) {
      this.file1 = this.fileList1[0];
      var filesize = Math.round(this.file1.size / 1024);
      if (filesize > 50) {
        document.getElementById("iconCalogError").innerHTML = "Maximum 50Kb file allow";
      }
      else {
        document.getElementById("iconCalogError").innerHTML = "";
      }
      this.formData.append('icon', this.file1, this.file1.name);
    }
  }
  fileChangeLandscape(event) {
    if (event.target.files && event.target.files[0]) {
      var reader = new FileReader();
      reader.onload = (event: any) => {
        this.lanscapeImage = event.target.result;
        this.checkLandscapeValid();
        // this.checkValidation('calogImage', 'image', 'imageCalogError', '', '');
      }
      reader.readAsDataURL(event.target.files[0]);
    }
    this.fileList2 = event.target.files;
    if (this.fileList2.length > 0) {
      this.file2 = this.fileList2[0];
      var filesize = Math.round(this.file2.size / 1024);
      if (filesize > 100) {
        document.getElementById("lanscapeCalogError").innerHTML = "Maximum 100Kb file allow";
      }
      else {
        document.getElementById("lanscapeCalogError").innerHTML = "";
      }
      this.formData.append('landscape', this.file2, this.file2.name);
    }
  }
  fileChangePortrait(event) {
    if (event.target.files && event.target.files[0]) {
      var reader = new FileReader();
      reader.onload = (event: any) => {
        this.portraitImage = event.target.result;
        this.checkPortraitValid();
        // this.checkValidation('calogImage', 'image', 'imageCalogError', '', '');
      }
      reader.readAsDataURL(event.target.files[0]);
    }
    this.fileList3 = event.target.files;
    if (this.fileList3.length > 0) {
      this.file3 = this.fileList3[0];
      var filesize = Math.round(this.file3.size / 1024);
      if (filesize > 100) {
        document.getElementById("portraitCalogError").innerHTML = "Maximum 100Kb file allow";
      }
      else {
        document.getElementById("portraitCalogError").innerHTML = "";
      }
      this.formData.append('portrait', this.file3, this.file3.name);
    }
  }
  closedialog() {
    this.dialogref.close({ res: "" });
  }
  checkImageValid() {
    document.getElementById("CalogAddError").innerHTML = "";
    if (this.calogImage == undefined || this.calogImage == "none" || this.calogImage == "") {
      document.getElementById("imageCalogError").innerHTML = ERROR.IMG_REQ;
    }
    else {
      if (this.file) {
        var filesize = Math.round(this.file.size / 1024);
        if (filesize > 100) {
          document.getElementById("imageCalogError").innerHTML = "Maximum 100Kb file allow";
        }
        else {
          document.getElementById("imageCalogError").innerHTML = "";
          return true;
        }
      }
      else {
        document.getElementById("imageCalogError").innerHTML = "";
        return true;
      }
    }
  }
  checkPortraitValid() {
    document.getElementById("CalogAddError").innerHTML = "";
    if (this.file3) {
      var filesize = Math.round(this.file3.size / 1024);
      if (filesize > 100) {
        document.getElementById("portraitCalogError").innerHTML = "Maximum 100Kb file allow";
      }
      else {
        document.getElementById("portraitCalogError").innerHTML = "";
        return true;
      }
    }
    else {
      document.getElementById("portraitCalogError").innerHTML = "";
      return true;
    }
  }
  checkLandscapeValid() {
    document.getElementById("CalogAddError").innerHTML = "";
    if (this.file2) {
      var filesize = Math.round(this.file2.size / 1024);
      if (filesize > 100) {
        document.getElementById("lanscapeCalogError").innerHTML = "Maximum 100Kb file allow";
      }
      else {
        document.getElementById("lanscapeCalogError").innerHTML = "";
        return true;
      }
    }
    else {
      document.getElementById("lanscapeCalogError").innerHTML = "";
      return true;
    }
  }
  checkIconValid() {
    document.getElementById("CalogAddError").innerHTML = "";
    if (this.file1) {
      var filesize = Math.round(this.file1.size / 1024);
      if (filesize > 50) {
        document.getElementById("iconCalogError").innerHTML = "Maximum 50Kb file allow";
      }
      else {
        document.getElementById("iconCalogError").innerHTML = "";
        return true;
      }
    }
    else {
      document.getElementById("iconCalogError").innerHTML = "";
      return true;
    }
  }
  checkValidation(id, type, catId, blankMsg, typeMsg, validType) {
    var validObj = {
      "id": id,
      "errorId": catId,
      "type": type,
      "blank_msg": blankMsg,
      "type_msg": typeMsg,
      "button_check": {
        "button_id": "subCalogAdd",
        "successArr": ['addCalogInput', 'calogImage']
      }
    }
    this.validService.checkValid(validObj);
    if (validType != "blank") {
      this.addCatalog();
    }
  }
  checkOtherStatus() {
    if (this.selectedCatalogType == 2 || this.selectedCatalogType == 3) {
      if (this.eventDate == undefined || this.eventDate == "") {
        document.getElementById("inputdateError").innerHTML = "Please enter event date";
        return false;
      }
      else {
        document.getElementById("inputdateError").innerHTML = "";
        return true;
      }
    }
    else {
      // document.getElementById("inputdateError").innerHTML = "";
      return true;
    }
  }
  addCatalog() {
    console.log(this.listOfTag)
    var validObj = [
      {
        "id": 'addCalogInput',
        "errorId": 'inputCalogError',
        "type": '',
        "blank_msg": ERROR.CAT_LOG_EMPTY,
        "type_msg": '',
      }
    ]
    var addStatus = this.validService.checkAllValid(validObj);
    var imageStatus = this.checkImageValid();
    var iconStatus = this.checkIconValid();
    var portraitStatus = this.checkPortraitValid();
    var lanscapeStatus = this.checkLandscapeValid();
    var otherStatus = this.checkOtherStatus();
    if (addStatus && imageStatus && iconStatus && otherStatus && portraitStatus && lanscapeStatus) {
      this.utils.showLoader();
      var catApliUrl;
      if (this.catalogData) {
        catApliUrl = 'updateCatalog';
      }
      else {
        catApliUrl = 'addCatalog';
      }
      var request_data
      if (this.catalogData) {
        request_data = {
          "category_id": this.selectedCategory,
          "sub_category_id": this.subCategoryId,
          "is_free": this.selectCalogPrice,
          "is_ios_free": this.selectCalogPriceIOS,
          "catalog_type": +this.selectedCatalogType,
          "event_date": this.eventDate,
          "popularity_rate": this.popularity,
          "is_featured": this.selectCalogType,
          "name": this.CalogName,
          "catalog_id": this.catalogId,
          "search_category": this.listOfTag.join(',')
        };
      }
      else {
        request_data = {
          "category_id": this.selectedCategory,
          "sub_category_id": this.subCategoryId,
          "is_free": this.selectCalogPrice,
          "is_ios_free": this.selectCalogPriceIOS,
          "catalog_type": +this.selectedCatalogType,
          "event_date": this.eventDate,
          "popularity_rate": this.popularity,
          "is_featured": this.selectCalogType,
          "name": this.CalogName
        };
      }

      this.formData.append('request_data', JSON.stringify(request_data));
      this.dataService.postData(catApliUrl, this.formData,
        {
          headers: {
            'Authorization': 'Bearer ' + this.token
          }
        }).then((results: any) => {
          if (results.code == 200) {
            this.utils.hideLoader();
            this.dialogref.close({ res: "add" });
            if (this.catalogData) {
              this.utils.showSuccess("Catalog updated successfully", 4000);
            }
            else {
              this.utils.showSuccess("Catalog added successfully", 4000);
            }
          }
          else if (results.code == 201) {
            // this.utils.showError(results.message, 4000);
            document.getElementById("CalogAddError").innerHTML = results.message;
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
  imageLoad(event) {
    if (event.target.previousElementSibling != null) {
      event.target.previousElementSibling.remove();
    }
  }
  addTag() {
    let newName = this.tagName.split(',');
    if (this.tagName == '') {
      return
    }
    else {
      for (let l = 0; l < newName.length; l++) {
        if (this.listOfTag.indexOf(newName[l])) {
          this.listOfTag.push(newName[l].trim().toLowerCase());
        }
      }
      this.tagName = '';
    }

    const unique = (value, index, self) => {
      return self.indexOf(value) === index
    }
    const uniqueTags = this.listOfTag.filter(unique)

    this.listOfTag = uniqueTags;
  }
  removeTag(tag) {
    let index = this.listOfTag.indexOf(tag);
    this.listOfTag.splice(index, 1);
  }
}

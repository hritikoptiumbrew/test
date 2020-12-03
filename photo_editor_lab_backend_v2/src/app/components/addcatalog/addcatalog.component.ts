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
  CalogName: any;
  formData = new FormData();
  fileList: any;
  file: any;
  token: any;
  errormsg = ERROR;
  selectCalogType: any = '0';
  selectCalogPrice: any = '1';
  selectedCategory: any;
  subCategoryId: any;
  catalogId: any;
  constructor(private validService: ValidationsService, private dialogref: NbDialogRef<AddcatalogComponent>, private dataService: DataService, private utils: UtilService, private route: Router) {
    this.token = localStorage.getItem('at');
    this.selectedCategory = JSON.parse(localStorage.getItem('selected_category')).category_id;
    this.subCategoryId = JSON.parse(localStorage.getItem('selected_sub_category')).sub_category_id;
    this.utils.dialogref = this.dialogref;
  }

  ngOnInit(): void {
    if (this.catalogData) {
      if(this.catalogData.webp_original_img)
      {
        this.calogImage = this.catalogData.webp_original_img;
      }
      else
      {
        this.calogImage = this.catalogData.thumbnail_img;
      }
      this.CalogName = this.catalogData.name;
      this.selectCalogType = this.catalogData.is_featured.toString();
      this.selectCalogPrice = this.catalogData.is_free.toString();
      this.catalogId = this.catalogData.catalog_id;
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
      var filesize = Math.round(this.file.size/1024);
      if(filesize > 100)
      {
        document.getElementById("imageCalogError").innerHTML = "Maximum 100Kb file allow to upload";
      }
      else
      {
        document.getElementById("imageCalogError").innerHTML = "";
      }
      this.formData.append('file', this.file, this.file.name);
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
      if(this.file)
      {
        var filesize = Math.round(this.file.size/1024);
        if(filesize > 100)
        {
          document.getElementById("imageCalogError").innerHTML = "Maximum 100Kb file allow to upload";
        }
        else
        {
          document.getElementById("imageCalogError").innerHTML = "";
          return true;
        }
      }
      else
      {
        document.getElementById("imageCalogError").innerHTML = "";
        return true;
      }
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
        "successArr": ['addCalogInput','calogImage']
      }
    }
    this.validService.checkValid(validObj);
    if (validType != "blank") {
      this.addCatalog();
    }
  }
  addCatalog() {
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
    if (addStatus && imageStatus) {
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
          "is_featured": this.selectCalogType,
          "name": this.CalogName,
          "catalog_id": this.catalogId
        };
      }
      else {
        request_data = {
          "category_id": this.selectedCategory,
          "sub_category_id": this.subCategoryId,
          "is_free": this.selectCalogPrice,
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
  imageLoad(event){
    if(event.target.previousElementSibling != null)
    {
      event.target.previousElementSibling.remove();
    }
  }
}

/*
 * Optimumbrew Technology

 * Project       : Photo Editor Lab
 * File          : popularsampleadd.component.ts
 * File Created  : Thursday, 22nd October 2020 05:42:15 pm
 * Author        : Optimumbrew
 * Auther Email  : info@optimumbrew.com
 * Last Modified : Thursday, 22nd October 2020 05:47:33 pm
 * -----
 * Copyright 2018 - 2020 Optimumbrew Technology
 */


import { Component, OnInit } from '@angular/core';
import { NbDialogRef, NbDialogService } from '@nebular/theme';
import { DataService } from 'app/data.service';
import { UtilService } from 'app/util.service';
import { ValidationsService } from 'app/validations.service';
import { ERROR, ENV_CONFIG } from '../../app.constants';

@Component({
  selector: 'ngx-popularsampleadd',
  templateUrl: './popularsampleadd.component.html',
  styleUrls: ['./popularsampleadd.component.scss']
})
export class PopularsampleaddComponent implements OnInit {

  sampleData: any;
  catalogId: any;
  sampleImage: any;
  selectedCategory: any = JSON.parse(localStorage.getItem("selected_category"));
  selectedCatalog: any = JSON.parse(localStorage.getItem("selected_catalog"));
  displayImage: any
  formData = new FormData();
  fileList1: any;
  file1: any;
  fileList2: any;
  file2: any;
  selectType: any = '1';
  token: any;

  constructor(private validService: ValidationsService, private utils: UtilService, private dialogRef: NbDialogRef<PopularsampleaddComponent>, private dataService: DataService) {
    this.token = localStorage.getItem("at");
    this.utils.dialogref = this.dialogRef;
  }

  ngOnInit(): void {
    if (this.sampleData) {
      this.sampleImage = this.sampleData.original_thumbnail_img;
      this.displayImage = this.sampleData.display_thumbnail_img;
      this.selectType = this.sampleData.image_type.toString();
    }
  }
  fileChange1(event) {
    if (event.target.files && event.target.files[0]) {
      var reader = new FileReader();
      reader.onload = (event: any) => {
        this.sampleImage = event.target.result;
      }
      reader.readAsDataURL(event.target.files[0]);
    }
    this.fileList1 = event.target.files;
    if (this.fileList1.length > 0) {
      this.file1 = this.fileList1[0];
      var filesize = Math.round(this.file1.size/1024);
      if(filesize > 100)
      {
        document.getElementById("imageErrorSample").innerHTML = "Maximum 100Kb file allow to upload";
      }
      else
      {
        document.getElementById("imageErrorSample").innerHTML = "";
      }
      this.formData.append('original_img', this.file1, this.file1.name);
    }
  }
  fileChange2(event) {
    if (event.target.files && event.target.files[0]) {
      var reader = new FileReader();
      reader.onload = (event: any) => {
        this.displayImage = event.target.result;
      }
      reader.readAsDataURL(event.target.files[0]);

    }
    this.fileList2 = event.target.files;
    if (this.fileList2.length > 0) {
      this.file2 = this.fileList2[0];
      var filesize = Math.round(this.file2.size/1024);
      if(filesize > 100)
      {
        document.getElementById("imageErrorDisplay").innerHTML = "Maximum 100Kb file allow to upload";
      }
      else
      {
        document.getElementById("imageErrorDisplay").innerHTML = "";
      }
      this.formData.append('display_img', this.file2, this.file2.name);
    }

  }
  checkValidation(id, type, catId, blankMsg, typeMsg) {
    var validObj = {
      "id": id,
      "errorId": catId,
      "type": type,
      "blank_msg": blankMsg,
      "type_msg": typeMsg,
      "button_check": {
        "button_id": "sampleAdd",
        "successArr": ['sampleImage', 'displayImage']
      }
    }
    this.validService.checkValid(validObj);
  }
  closedialog() {
    this.dialogRef.close({ res: "" });
  }
  checkImageValidSample() {
    document.getElementById("sampleAddError").innerHTML = "";
    if (this.sampleImage == undefined || this.sampleImage == "none" || this.sampleImage == "") {
      document.getElementById("imageErrorSample").innerHTML = ERROR.IMG_REQ;
    }
    else {
      if(this.file1)
      {
        var filesize = Math.round(this.file1.size/1024);
        if(filesize > 100)
        {
          document.getElementById("imageErrorSample").innerHTML = "Maximum 100Kb file allow to upload";
        }
        else
        {
          document.getElementById("imageErrorSample").innerHTML = "";
          return true;
        }
      }
      else
      {
        document.getElementById("imageErrorSample").innerHTML = "";
        return true;
      }
    }
  }
  checkImageValidDisplay() {
    document.getElementById("sampleAddError").innerHTML = "";
    if (this.displayImage == undefined || this.displayImage == "none" || this.displayImage == "") {
      document.getElementById("imageErrorDisplay").innerHTML = ERROR.IMG_REQ;
    }
    else {
      if(this.file2)
      {
        var filesize = Math.round(this.file2.size/1024);
        if(filesize > 100)
        {
          document.getElementById("imageErrorDisplay").innerHTML = "Maximum 100Kb file allow to upload";
        }
        else
        {
          document.getElementById("imageErrorDisplay").innerHTML = "";
          return true;
        }
      }
      else
      {
        document.getElementById("imageErrorDisplay").innerHTML = "";
        return true;
      }
    }
  }
  addsampleImage() {
    var sampleStatus = this.checkImageValidSample();
    var displayStatus = this.checkImageValidDisplay();
    if(sampleStatus && displayStatus)
    {
      this.utils.showLoader();
      var requestData;
      var apiUrl;
      if (this.sampleData) {
        requestData = {
          "category_id": this.selectedCategory.category_id,
          "is_featured": this.selectedCatalog.is_featured,
          "image_type": this.selectType,
          "img_id": this.sampleData.img_id
        };
        apiUrl = "updateFeaturedBackgroundCatalogImage";
      }
      else {
        requestData = {
          "category_id": this.selectedCategory.category_id,
          "is_featured": this.selectedCatalog.is_featured,
          "image_type": this.selectType,
          "catalog_id": this.catalogId
        };
        apiUrl = "addFeaturedBackgroundCatalogImage"
      }
      this.formData.append('request_data', JSON.stringify(requestData));
      this.dataService.postData(apiUrl, this.formData,
        {
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
            document.getElementById("sampleAddError").innerHTML = results.message;
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

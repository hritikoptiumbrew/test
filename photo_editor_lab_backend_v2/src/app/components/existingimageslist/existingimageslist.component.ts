/*
 * Optimumbrew Technology

 * Project       : Photo Editor Lab
 * File          : existingimageslist.component.ts
 * File Created  : Wednesday, 21st October 2020 10:15:14 am
 * Author        : Optimumbrew
 * Auther Email  : info@optimumbrew.com
 * Last Modified : Thursday, 29th October 2020 11:25:20 am
 * -----
 * Copyright 2018 - 2020 Optimumbrew Technology
 */


import { Component, OnInit } from '@angular/core';
import { NbDialogRef } from '@nebular/theme';
import { DataService } from 'app/data.service';
import { UtilService } from 'app/util.service';
import { ERROR, ENV_CONFIG } from '../../app.constants';
@Component({
  selector: 'ngx-existingimageslist',
  templateUrl: './existingimageslist.component.html',
  styleUrls: ['./existingimageslist.component.scss']
})
export class ExistingimageslistComponent implements OnInit {

  imageFiles: any;
  isAllChecked: any;
  formData = new FormData;
  token: any;
  requestData = {
    "is_replace": 1,
    "category_id": JSON.parse(localStorage.getItem("selected_category")).category_id
  };
  constructor(private dialogRef: NbDialogRef<ExistingimageslistComponent>, private dataService: DataService, private utils: UtilService) {
    this.token = localStorage.getItem('at');
    this.utils.dialogref = this.dialogRef;
  }

  ngOnInit(): void {
    this.imageFiles.forEach(element => {
      element.is_checked = false;
    });
  }
  closeDialog() {
    this.dialogRef.close({ res: "" });
  }
  selectAll(isAllChecked) {
    document.getElementById("imageExtError").innerHTML = "";
    if (isAllChecked == true) {
      for (let k = 0; k < this.imageFiles.length; k++) {
        this.imageFiles[k].is_checked = true;
      }
    }
    else {
      for (let k = 0; k < this.imageFiles.length; k++) {
        this.imageFiles[k].is_checked = false;
      }
    }
  }
  valueChanged() {
    document.getElementById("imageExtError").innerHTML = "";
    for (let k = 0; k < this.imageFiles.length; k++) {
      if (this.imageFiles[k].is_checked == false) {
        this.isAllChecked = false;
      }
    }
  }
  replaceImages(existingFiles) {
    let j = 0;
    this.formData = new FormData();
    for (let k = 0; k < existingFiles.length; k++) {
      if (existingFiles[k].is_checked == true) {
        j++;
        this.formData.append('file[]', existingFiles[k].new_image);
      }
    }
    if (j <= 0) {
      document.getElementById("imageExtError").innerHTML = ERROR.IMG_REP_EMPTY;
      return false;
    }
    else {
      this.formData.append("request_data", JSON.stringify(this.requestData));
      this.utils.showLoader();
      this.dataService.postData('addCatalogImagesForJson', this.formData,
        {
          headers: {
            'Authorization': 'Bearer ' + this.token
          }
        }).then((results: any) => {
          if (results.code == 200) {
            this.utils.hideLoader();
            this.utils.showSuccess(results.message, 4000);
            this.dialogRef.close({ res: "add" });
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
  imageLoad(event){
    if(event.target.previousElementSibling != null)
    {
      event.target.previousElementSibling.remove();
    }
  }
}

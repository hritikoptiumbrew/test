/*
 * Optimumbrew Technology

 * Project       : Photo Editor Lab
 * File          : addsubcategoryimagesbyid.component.ts
 * File Created  : Thursday, 22nd October 2020 11:50:20 am
 * Author        : Optimumbrew
 * Auther Email  : info@optimumbrew.com
 * Last Modified : Thursday, 22nd October 2020 11:52:36 am
 * -----
 * Copyright 2018 - 2020 Optimumbrew Technology
 */


import { Component, OnInit } from '@angular/core';
import { NbDialogRef } from '@nebular/theme';
import { DataService } from 'app/data.service';
import { UtilService } from 'app/util.service';
import { ERROR, ENV_CONFIG } from '../../app.constants';
@Component({
  selector: 'ngx-addsubcategoryimagesbyid',
  templateUrl: './addsubcategoryimagesbyid.component.html',
  styleUrls: ['./addsubcategoryimagesbyid.component.scss']
})
export class AddsubcategoryimagesbyidComponent implements OnInit {

  selectedCategory: any = JSON.parse(localStorage.getItem("selected_category"));
  selectedCatalog: any = JSON.parse(localStorage.getItem("selected_catalog"));
  catalogId: any;
  fileList: any;
  file: any;
  extFile: any;
  totalFiles: any = 0;
  formData = new FormData();
  // existingFiles: any = [];
  errorList: any = [];
  files: any = [];
  token: any;
  constructor(private dialogRef: NbDialogRef<AddsubcategoryimagesbyidComponent>, private dataService: DataService, private utils: UtilService) {
    this.token = localStorage.getItem("at");
    this.utils.dialogref = this.dialogRef;
  }

  ngOnInit(): void {
  }
  closeDialog() {
    this.dialogRef.close({ res: "" });
  }
  fileChange(event) {
    this.files = [];
    this.errorList = [];
    // this.existingFiles = [];
    this.fileList = event.target.files;
    if (this.fileList && this.fileList.length > 0) {
    
      for (let i = 0; i < this.fileList.length; i++) {
        var reader = new FileReader();
        reader.onload = (event: any) => {
          this.fileList[i].compressed_img = event.target.result;
        }
        reader.readAsDataURL(this.fileList[i]);
        this.files.push(this.fileList[i]);
        this.totalFiles = this.files.length;
        if(this.totalFiles > 20)
        {
          document.getElementById("imageFileError").innerHTML = "Max 20 files allow to upload";
        } 
        else
        {
          document.getElementById("imageFileError").innerHTML = "";
        }
      }
    }
    else {
      this.fileList = [];
      // this.existingFiles = [];
    }
  }
  deleteImage(i) {
    document.getElementById("imageFileError").innerHTML = "";
    this.files.splice(i, 1);
    this.totalFiles = this.files.length;
  }
  getFileFormData() {
    this.formData = new FormData();
    for (let i = 0; i < this.files.length; i++) {
      this.formData.append('file[]', this.files[i]);
    }
  }
  addImages() {
    this.getFileFormData();
    this.errorList = [];
    if (typeof this.files == 'undefined' || this.files == "" || this.files == null || this.files.length <= 0) {
      document.getElementById("imageFileError").innerHTML = ERROR.IMG_UP_EMPTY;
      return false;
    }
    else if(this.totalFiles > 20)
    {
        document.getElementById("imageFileError").innerHTML = "Max 20 files allow to upload";
    }
    else {
      this.utils.showLoader();
      let requestData = {
        'catalog_id': this.catalogId,
        'category_id': this.selectedCategory.category_id,
        'is_featured': this.selectedCatalog.is_featured
      };
      this.formData.append("request_data", JSON.stringify(requestData));
      this.dataService.postData('addCatalogImages', this.formData,
        {
          headers: {
            'Authorization': 'Bearer ' + this.token
          }
        }).then((results: any) => {
          if (results.code == 200) {
            this.dialogRef.close({ res: "add" });
            this.utils.showSuccess(results.message, 4000);
            this.utils.hideLoader();
          }
          else if (results.code == 201) {
            document.getElementById("imageFileError").innerHTML = results.message;
            // this.utils.showError(results.message, 4000);
            this.utils.hideLoader();
          }
          else if (results.status || results.status == 0) {
            this.utils.showError(ERROR.SERVER_ERR, 4000);
            this.utils.hideLoader();
          }
          else if (results.code == 432) {
            document.getElementById("imageFileError").innerHTML = results.message;
            // this.utils.showError(results.message, 4000);
            this.errorList = results.data.error_list;
            this.utils.hideLoader();
          }
          else {
            document.getElementById("imageFileError").innerHTML = results.message;
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

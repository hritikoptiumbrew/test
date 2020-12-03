/*
 * Optimumbrew Technology

 * Project       : Photo Editor Lab
 * File          : addjsonimages.component.ts
 * File Created  : Tuesday, 20th October 2020 03:11:44 pm
 * Author        : Optimumbrew
 * Auther Email  : info@optimumbrew.com
 * Last Modified : Tuesday, 20th October 2020 03:16:57 pm
 * -----
 * Copyright 2018 - 2020 Optimumbrew Technology
 */

import { Component, OnInit } from '@angular/core';
import { NbDialogRef, NbDialogService } from '@nebular/theme';
import { DataService } from 'app/data.service';
import { UtilService } from 'app/util.service';
import { ERROR, ENV_CONFIG } from '../../app.constants';
import { ExistingimageslistComponent } from '../existingimageslist/existingimageslist.component';

@Component({
  selector: 'ngx-addjsonimages',
  templateUrl: './addjsonimages.component.html',
  styleUrls: ['./addjsonimages.component.scss']
})
export class AddjsonimagesComponent implements OnInit {

  fileList: any;
  file: any;
  extFile: any;
  totalFiles: any = 0;
  formData = new FormData();
  existingFiles: any = [];
  errorList: any = [];
  files: any = [];
  token: any;
  requestData = {
    "is_replace": 0,
    "category_id": JSON.parse(localStorage.getItem("selected_category")).category_id
  };
  constructor(private dialog: NbDialogService, private dialogRef: NbDialogRef<AddjsonimagesComponent>, private dataService: DataService, private utils: UtilService) {
    this.token = localStorage.getItem('at');
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
    this.existingFiles = [];
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
      this.existingFiles = [];
     
    }
  }
  deleteImage(i) {
   
    document.getElementById("imageFileError").innerHTML = "";
    this.files.splice(i, 1);
    this.totalFiles = this.files.length;
    if(this.totalFiles == 0)
    {
      this.existingFiles = [];
    }
  }
  getFileFormData() {
    this.formData = new FormData();
    for (let i = 0; i < this.files.length; i++) {
      this.formData.append('file[]', this.files[i]);
    }
  }
  viewExistingImages(extFiles) {
    this.open(false, extFiles);
  }
  protected open(closeOnBackdropClick: boolean, data) {
    this.dialog.open(ExistingimageslistComponent, {
      closeOnBackdropClick,closeOnEsc: false, context: {
        imageFiles: data
      }
    }).onClose.subscribe((result) => {
      if (result.res == "add") {
        for(let i=0;i<this.files.length;i++)
        {
          data.forEach(element => {
              if(element.name == this.files[i].name)
              {
                this.files.splice(i,1);
              }
          });
        }
        if(this.files.length > 0)
        {
          this.addImages();
        }
        else
        {
          this.dialogRef.close();
        }
      }
    });
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
      this.formData.append("request_data", JSON.stringify(this.requestData));
      this.dataService.postData('addCatalogImagesForJson', this.formData,
        {
          headers: {
            'Authorization': 'Bearer ' + this.token
          }
        }).then((results: any) => {
          if (results.code == 200) {
            this.dialogRef.close();
            this.utils.showSuccess(results.message, 4000);
            this.utils.hideLoader();
          }
          else if (results.code == 201) {
            // this.utils.showError(results.message, 4000);
            document.getElementById("imageFileError").innerHTML = results.message;
            this.utils.hideLoader();
          }
          else if (results.status || results.status == 0) {
            // this.utils.showError(ERROR.SERVER_ERR, 4000);
            document.getElementById("imageFileError").innerHTML = results.message;

            this.utils.hideLoader();
          }
          else if (results.code == 420) {
            this.utils.hideLoader();
            // this.utils.showError(results.message, 4000);
            document.getElementById("imageFileError").innerHTML = results.message;
            this.existingFiles = results.data.existing_files;

            for (var i = 0, file; file = this.files[i]; i++) {
              this.existingFiles.forEach(element => {
                if (file.name == element.name) {
                  element.new_image = file;
                }
              });
            }
          }
          else if (results.code == 432) {
            // this.utils.showError(results.message, 4000);
            document.getElementById("imageFileError").innerHTML = results.message;
            this.errorList = results.data.error_list;
            this.utils.hideLoader();
          }
          else {
            // this.utils.showError(results.message, 4000);
            document.getElementById("imageFileError").innerHTML = results.message;
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

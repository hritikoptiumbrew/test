/*
 * Optimumbrew Technology

 * Project       : Photo Editor Lab
 * File          : addjsondata.component.ts
 * File Created  : Wednesday, 21st October 2020 02:07:08 pm
 * Author        : Optimumbrew
 * Auther Email  : info@optimumbrew.com
 * Last Modified : Thursday, 29th October 2020 11:16:58 am
 * -----
 * Copyright 2018 - 2020 Optimumbrew Technology
 */


import { Component, OnInit } from '@angular/core';
import { FormControl } from '@angular/forms';
import { NbDialogRef } from '@nebular/theme';
import { Observable, of } from 'rxjs';
import { startWith, map } from 'rxjs/operators';
import { DataService } from 'app/data.service';
import { UtilService } from 'app/util.service';
import { ValidationsService } from 'app/validations.service';
import { ERROR, ENV_CONFIG } from '../../app.constants';
@Component({
  selector: 'ngx-addjsondata',
  templateUrl: './addjsondata.component.html',
  styleUrls: ['./addjsondata.component.scss']
})
export class AddjsondataComponent implements OnInit {

  upJSonData: any;
  options: string[];
  filteredOptions$: Observable<string[]>;
  searchTagList: any;
  allSearchTag: string[] = [];
  searchINputControl = new FormControl();
  selectedSearchTags: string[] = [];
  selectedCategory = JSON.parse(localStorage.getItem("selected_category"));
  selectedCataLog = JSON.parse(localStorage.getItem("selected_catalog"));
  selectedSubCategory = JSON.parse(localStorage.getItem("selected_sub_category"));
  jsonImage: any;
  jsonGIF: any;
  jsonImageAfter: any;
  selectedType: any = '0';
  selectedPrice: any = '1';
  selectedIOSPrice: any = '1';
  selectedStyle: any = '1';
  catalogId: any;
  formData = new FormData();
  fileList: any;
  file: any;
  files: any;
  jsonData: any;
  token: any;
  incorrect_fonts = [];
  mismatch_fonts = [];
  constructor(private validService: ValidationsService, private dialogRef: NbDialogRef<AddjsondataComponent>, private utils: UtilService, private dataService: DataService) {
    this.token = localStorage.getItem("at");
    this.utils.dialogref = this.dialogRef;
  }

  ngOnInit(): void {
    this.searchTagList = JSON.parse(localStorage.getItem("search_tag_list"));
    // this.selectedSearchTags = JSON.parse(localStorage.getItem("selected_catalog")).name.toLowerCase().replace(/[^\w\s]/gi, '').trim().split(" ");
    this.selectedSearchTags = this.selectedCataLog.name.toLowerCase().replace(/[^a-zA-Z ]/g, "  ").replace(/\s\s+/g, ' ').trim().split(" ");
    if (this.searchTagList) {
      this.searchTagList.forEach(element => {
        this.allSearchTag.push(element.tag_name);
      });
    }
    this.options = this.allSearchTag;
    this.filteredOptions$ = of(this.options);
    this.filteredOptions$ = this.searchINputControl.valueChanges
      .pipe(
        startWith(''),
        map(filterString => this.filter(filterString)),
      );
    if (this.upJSonData) {
      this.jsonImage = this.upJSonData.thumbnail_img;
      this.jsonGIF = this.upJSonData.gif_file;
      this.jsonImageAfter = this.upJSonData.thumbnail_after_img;

      this.selectedType = this.upJSonData.is_featured;
      this.selectedStyle = this.upJSonData.is_portrait.toString();
      this.selectedPrice = this.upJSonData.is_free.toString();
      this.selectedIOSPrice = this.upJSonData.is_ios_free == 1 || this.upJSonData.is_ios_free == 0 ? this.upJSonData.is_ios_free.toString() : this.upJSonData.is_free.toString();
      if (typeof this.upJSonData.search_category == "undefined" || this.upJSonData.search_category.trim() == "" || this.upJSonData.search_category == null) {
        this.selectedSearchTags = [];
      }
      else {
        this.selectedSearchTags = this.upJSonData.search_category.split(",");
      }
      this.upJSonData.json_data = JSON.stringify(this.upJSonData.json_data, null, 2);
      this.jsonData = this.upJSonData.json_data;

    }
  }
  private filter(value: string): string[] {
    const filterValue = value.toLowerCase();
    return this.options.filter(optionValue => optionValue.toLowerCase().includes(filterValue));
  }
  validateString(str) {
    var regex = /^[a-z0-9&,# ]+$/i.test(str);
    return regex;
  }

  get disableInputGIF(): boolean {
    if (this.jsonImageAfter && this.jsonImage)
      return true;
    else
      return false;
  }

  get disableInputFile(): boolean {
    if (this.jsonGIF && this.jsonImageAfter)
      return true;
    else
      return false;
  }

  get disableInputAfter(): boolean {
    if (this.jsonGIF && this.jsonImage)
      return true;
    else
      return false;
  }

  add(event) {
    if (typeof event == "object") {
      if (event.target.value.trim() != "") {
        // if (!this.validateString(event.target.value)) {
        //   document.getElementById("tagInputError").innerHTML = "Special characters not allowed, only alphanumeric, ' & , #' is allowed in tag name.";
        //   return;
        // }
        // else {
        var newStr = event.target.value;
        var newArr = newStr.split(",");
        for (let i = 0; i < newArr.length; i++) {
          if ((newArr[i] || '').trim()) {
            this.selectedSearchTags.push(newArr[i].trim().toLowerCase());
          }
        }
        document.getElementById("tagInputError").innerHTML = "";
        this.searchINputControl.setValue("");
        // }
      }
      else {
        this.searchINputControl.setValue("");
      }
    }
    else {
      if (event != "") {
        // if (!this.validateString(event)) {
        //   document.getElementById("tagInputError").innerHTML = "Special characters not allowed, only alphanumeric, '&' is allowed in tag name.";
        //   return;
        // }
        // else {
        document.getElementById("tagInputError").innerHTML = "";
        this.selectedSearchTags.push(event.toLowerCase());
        this.searchINputControl.setValue("");
        // }
      }
    }
    const unique = (value, index, self) => {
      return self.indexOf(value) === index
    }

    const uniqueTags = this.selectedSearchTags.filter(unique)

    this.selectedSearchTags = uniqueTags;
  }
  remove(fruit) {
    var i = this.selectedSearchTags.indexOf(fruit);
    this.selectedSearchTags.splice(i, 1);
  }
  fileChange(event) {
    // this.formData = new FormData();
    if (event.target.files && event.target.files[0]) {
      var reader = new FileReader();
      reader.onload = (event: any) => {
        this.jsonImage = event.target.result;
        this.checkImageValid()
      }
      reader.readAsDataURL(event.target.files[0]);

    }
    this.fileList = event.target.files;
    if (this.fileList.length > 0) {
      this.file = this.fileList[0];
      this.formData.append('file', this.file, this.file.name);
    }
  }

  fileChangeGIF(event) {
    // this.formData = new FormData();
    if (event.target.files && event.target.files[0]) {
      var reader = new FileReader();
      reader.onload = (event: any) => {
        this.jsonGIF = event.target.result;
        // this.checkImageValid()
      }
      reader.readAsDataURL(event.target.files[0]);

    }
    this.fileList = event.target.files;
    if (this.fileList.length > 0) {
      this.file = this.fileList[0];
      this.formData.append('gif_file', this.file, this.file.name);
    }
  }

  fileChangeAfter(event) {
    // this.formData = new FormData();
    if (event.target.files && event.target.files[0]) {
      var reader = new FileReader();
      reader.onload = (event: any) => {
        this.jsonImageAfter = event.target.result;
        // this.checkImageValid()
      }
      reader.readAsDataURL(event.target.files[0]);

    }
    this.fileList = event.target.files;
    if (this.fileList.length > 0) {
      this.file = this.fileList[0];
      this.formData.append('after_file', this.file, this.file.name);
    }
  }

  fileChangemultiple(event) {
    // this.formData = new FormData();
    this.files = [];
    this.fileList = event.target.files;
    if (this.fileList && this.fileList.length > 0) {
      for (let i = 0; i < this.fileList.length; i++) {
        var reader = new FileReader();
        reader.onload = (event: any) => {
          if (i == 0) {
            this.jsonImage = event.target.result;
          }
        }
        reader.readAsDataURL(this.fileList[i]);
        this.files.push(this.fileList[i]);
        this.formData.append('file[]', this.fileList[i]);
        if (this.files.length > 20) {
          document.getElementById("imageError").innerHTML = "Max 20 files allow to upload";
        }
        else {
          document.getElementById("imageError").innerHTML = "";
        }
      }
    }
    else {
      this.fileList = [];
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
        "button_id": "saveJson",
        "successArr": ['jsonImage', 'radioCalogPriInput', 'radioCalogtypeInput', 'radioCalogstyleInput']
      }
    }
    this.validService.checkValid(validObj);
  }
  checkImageValid() {
    if (this.selectedSubCategory.is_multi_page_support == 1 && !this.upJSonData) {
      document.getElementById("jsonAddError").innerHTML = "";
      if (this.files.length == 0) {
        document.getElementById("imageError").innerHTML = ERROR.IMG_REQ;
      }
      else {
        document.getElementById("imageError").innerHTML = "";
        return true;
      }
    } else {
      document.getElementById("jsonAddError").innerHTML = "";
      if (this.jsonImage == undefined || this.jsonImage == "none" || this.jsonImage == "") {
        document.getElementById("imageError").innerHTML = ERROR.IMG_REQ;
      }
      else {
        document.getElementById("imageError").innerHTML = "";
        return true;
      }
    }
  }
  checkJsonValid() {
    document.getElementById("jsonAddError").innerHTML = "";

    if (typeof this.jsonData == 'undefined' || this.jsonData.trim() == "" || this.jsonData == null) {
      document.getElementById("jsonFileError").innerHTML = ERROR.JSON_DATA_EMPTY;
    }
    else {
      if (!this.isJson(this.jsonData)) {
        document.getElementById("jsonFileError").innerHTML = "JSON data you enter is not valid";
      }
      else {
        document.getElementById("jsonFileError").innerHTML = "";
        return true;
      }
    }
  }
  removeError() {
    document.getElementById("jsonFileError").innerHTML = "";
  }
  isJson(str) {
    try {
      JSON.parse(str);
    } catch (e) {
      return false;
    }
    return true;
  }
  closeDialog() {
    this.dialogRef.close({ res: "" });
  }
  openFile(event) {
    var input = event.target;
    var reader = new FileReader();
    reader.onload = (event: any) => {
      var text = reader.result;
      this.jsonData = event.target.result;
      this.checkJsonValid();
    };
    reader.readAsText(input.files[0]);
  }
  saveJson() {
    var imageStatus = this.checkImageValid();
    var jsonStatus = this.checkJsonValid();
    if (imageStatus && jsonStatus) {
      var apiUrl;
      var requestData;
      this.utils.showLoader();
      var json_data = JSON.parse(this.jsonData);
      let tmp_selected_tags = this.selectedSearchTags.join();
      if (this.upJSonData) {
        requestData = {
          "category_id": this.selectedCategory.category_id,
          "is_featured_catalog": this.selectedCataLog.is_featured,
          "img_id": this.upJSonData.img_id,
          "content_type": this.upJSonData.content_type,
          "is_free": this.selectedPrice,
          "is_ios_free": this.selectedIOSPrice,
          "is_featured": this.selectedType,
          "is_portrait": this.selectedStyle,
          "catalog_id": this.catalogId,
          "json_data": json_data,
          "search_category": tmp_selected_tags,
          "sub_category_id": this.selectedSubCategory.sub_category_id,
          "old_file": this.upJSonData.thumbnail_img ? this.upJSonData.thumbnail_img.split('/').pop() : null,
          "old_gif_file": this.upJSonData.gif_file ? this.upJSonData.gif_file.split('/').pop() : null,
          "old_after_file": this.upJSonData.thumbnail_after_img ? this.upJSonData.thumbnail_after_img.split('/').pop() : null,
        };

        apiUrl = "editJsonData";
      }
      else {
        requestData = {
          "category_id": this.selectedCategory.category_id,
          "is_featured_catalog": this.selectedCataLog.is_featured,
          "is_free": this.selectedPrice,
          "is_ios_free": this.selectedIOSPrice,
          "is_featured": this.selectedType,
          "is_portrait": this.selectedStyle,
          "catalog_id": this.catalogId,
          "json_data": json_data,
          "search_category": tmp_selected_tags,
          "sub_category_id": this.selectedSubCategory.sub_category_id
        };
        apiUrl = this.selectedSubCategory.is_multi_page_support == 0 ? "addJson" : "addMultiPageJson";
      }
      this.formData.append('request_data', JSON.stringify(requestData));
      this.dataService.postData(apiUrl, this.formData,
        {
          headers: {
            'Authorization': 'Bearer ' + this.token
          }
        }).then((results: any) => {
          if (results.code == 200) {
            this.incorrect_fonts = [];
            this.mismatch_fonts = [];
            this.utils.hideLoader();
            this.dialogRef.close({ res: "add" });
            this.utils.showSuccess(results.message, 4000);
          }
          else if (results.code == 201) {
            document.getElementById("jsonAddError").innerHTML = results.message;
            // this.utils.showError(results.message, 4000);
            this.utils.hideLoader();
          }
          else if (results.code == 433) {
            document.getElementById("jsonAddError").innerHTML = results.message;
            this.incorrect_fonts = results.data.incorrect_fonts;
            this.mismatch_fonts = results.data.mismatch_fonts;
            this.utils.hideLoader();
          }
          else if (results.status || results.status == 0) {
            this.utils.showError(ERROR.SERVER_ERR, 4000);
            this.utils.hideLoader();
          }
          else {
            document.getElementById("jsonAddError").innerHTML = results.message;
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
}

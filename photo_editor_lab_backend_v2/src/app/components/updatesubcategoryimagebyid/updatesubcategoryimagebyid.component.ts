/*
 * Optimumbrew Technology

 * Project       : Photo Editor Lab
 * File          : updatesubcategoryimagebyid.component.ts
 * File Created  : Thursday, 22nd October 2020 10:39:47 am
 * Author        : Optimumbrew
 * Auther Email  : info@optimumbrew.com
 * Last Modified : Thursday, 29th October 2020 11:29:21 am
 * -----
 * Copyright 2018 - 2020 Optimumbrew Technology
 */


import { Component, OnInit } from '@angular/core';
import { FormControl } from '@angular/forms';
import { NbDialogRef } from '@nebular/theme';
import { Observable, of } from 'rxjs';
import { map, startWith } from 'rxjs/operators';
import { DataService } from 'app/data.service';
import { UtilService } from 'app/util.service';
import { ValidationsService } from 'app/validations.service';
import { ERROR, ENV_CONFIG } from '../../app.constants';
@Component({
  selector: 'ngx-updatesubcategoryimagebyid',
  templateUrl: './updatesubcategoryimagebyid.component.html',
  styleUrls: ['./updatesubcategoryimagebyid.component.scss']
})
export class UpdatesubcategoryimagebyidComponent implements OnInit {

  selectedCategory: any = JSON.parse(localStorage.getItem("selected_category"));
  selectedCatalog: any = JSON.parse(localStorage.getItem("selected_catalog"));
  categoryData: any;
  formData = new FormData();
  fileList: any;
  file: any;
  searchTagList: any;
  options: string[];
  token: any;
  filteredOptions$: Observable<string[]>;
  allSearchTag: string[] = [];
  searchINputControl = new FormControl();
  selectedSearchTags: string[] = [];
  constructor(private dialogRef: NbDialogRef<UpdatesubcategoryimagebyidComponent>, private utils: UtilService, private dataService: DataService) {
    this.token = localStorage.getItem('at');
    this.utils.dialogref = this.dialogRef;
  }

  ngOnInit(): void {
    this.searchTagList = JSON.parse(localStorage.getItem("search_tag_list"));
    // this.selectedSearchTags = JSON.parse(localStorage.getItem("selected_catalog")).name.toLowerCase().replace(/[^\w\s]/gi, '').trim().split(" ");
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
    if (typeof this.categoryData.search_category == "undefined" || this.categoryData.search_category.trim() == "" || this.categoryData.search_category == null) {
      this.selectedSearchTags = [];
    }
    else {
      this.selectedSearchTags = this.categoryData.search_category.split(",");
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
  add(event) {
    if (typeof event == "object") {
      if (event.target.value.trim() != "") {
        if (!this.validateString(event.target.value)) {
          document.getElementById("tagInputError").innerHTML = "Special characters not allowed, only alphanumeric, '& , #' is allowed in tag name.";
          return;
        }
        else {
          var newStr = event.target.value;
          var newArr = newStr.split(",");
          for (let i = 0; i < newArr.length; i++) {
            if ((newArr[i] || '').trim()) {
              this.selectedSearchTags.push(newArr[i].trim().toLowerCase());
            }
          }
          document.getElementById("tagInputError").innerHTML = "";
          this.searchINputControl.setValue("");
        }
      }
      else {
        this.searchINputControl.setValue("");
      }
    }
    else {
      if (event != "") {
        if (!this.validateString(event)) {
          document.getElementById("tagInputError").innerHTML = "Special characters not allowed, only alphanumeric, '&' is allowed in tag name.";
          return;
        }
        else {
          document.getElementById("tagInputError").innerHTML = "";
          this.selectedSearchTags.push(event);
          this.searchINputControl.setValue("");
        }
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
    if (event.target.files && event.target.files[0]) {
      var reader = new FileReader();
      reader.onload = (event: any) => {
        this.categoryData.compressed_img = event.target.result;
      }
      reader.readAsDataURL(event.target.files[0]);

    }
    this.fileList = event.target.files;
    if (this.fileList.length > 0) {
      this.file = this.fileList[0];
      this.formData.append('file', this.file, this.file.name);
    }
  }
  closeDialog() {
    this.dialogRef.close({ res: "" });
  }
  updateCategory() {
    this.utils.showLoader();
    this.formData.delete("request_data");
    let tmpSelectedTags = this.selectedSearchTags.join();
    let image_data = {
      'img_id': this.categoryData.img_id,
      'category_id': this.selectedCategory.category_id,
      'is_featured': this.selectedCatalog.is_featured,
      'search_category': tmpSelectedTags
    };
    this.formData.append('request_data', JSON.stringify(image_data));
    this.dataService.postData('updateCatalogImage', this.formData,
      {
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
  imageLoad(event) {
    if(event.target.previousElementSibling != null)
    {
      event.target.previousElementSibling.remove();
    }
  }
}

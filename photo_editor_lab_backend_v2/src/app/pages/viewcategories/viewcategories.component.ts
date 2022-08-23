/*
 * Optimumbrew Technology

 * Project       : Photo Editor Lab
 * File          : viewcategories.component.ts
 * File Created  : Thursday, 15th October 2020 01:12:03 pm
 * Author        : Optimumbrew
 * Auther Email  : info@optimumbrew.com
 * Last Modified : Thursday, 29th October 2020 11:12:05 am
 * -----
 * Copyright 2018 - 2020 Optimumbrew Technology
 */


import { Component, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { NbDialogService, NbWindowRef, NbWindowService } from '@nebular/theme';
import { AddsearchtagsComponent } from 'app/components/addsearchtags/addsearchtags.component';
import { AddsubcategoryComponent } from 'app/components/addsubcategory/addsubcategory.component';
import { LoadingComponent } from 'app/components/loading/loading.component';
import { DataService } from 'app/data.service';
import { UtilService } from 'app/util.service';
import { ValidationsService } from 'app/validations.service';
import { ViewcorruptedfontsComponent } from 'app/components/viewcorruptedfonts/viewcorruptedfonts.component';
import * as $ from 'jquery'
import { ERROR, ENV_CONFIG } from '../../app.constants';
@Component({
  selector: 'ngx-viewcategories',
  templateUrl: './viewcategories.component.html',
  styleUrls: ['./viewcategories.component.scss']
})
export class ViewcategoriesComponent implements OnInit {

  previousLabel = "<";
  nextLabel=">";
  broadItem: any;
  pageSize: any = [25,50,75,100];
  selectedPageSize: any = '50';
  currentPage: any = 1;
  categoryId: any;
  dialogStatus: any;
  token: any;
  totalRecords: any;
  categoryData: any;
  searchQuery: any;
  errormsg = ERROR;
  paginstatus = "true";
  isselect:any=false;

  constructor(private validService: ValidationsService, private actRoute: ActivatedRoute, private dialog: NbDialogService, private dataService: DataService, private utils: UtilService, private route: Router) {
    this.broadItem = JSON.parse(localStorage.getItem('selected_category')).name;
    this.categoryId = this.actRoute.snapshot.params.categoryId;
  }

  ngOnInit(): void {
    this.getAllCategories(this.categoryId, this.currentPage, this.selectedPageSize);
  }
  checkValidation(id, type, catId, blankMsg, typeMsg,validType) {
    var validObj = {
      "id": id,
      "errorId": catId,
      "type": type,
      "blank_msg": blankMsg,
      "type_msg": typeMsg,
      "button_check": {
        "button_id": "subCatButton",
        "successArr": ['subCatInput']
      }
    }
    this.validService.checkValid(validObj);
    if(validType != "blank")
    {
      this.searchCategory();
    }
  }
  gotoCategories() {
    this.route.navigate(['/admin/categories']);
  }
  setPageSize(value) {
    this.selectedPageSize = value;
    if(this.selectedPageSize > this.totalRecords)
    {
      this.currentPage = 1;
    }
    this.getAllCategories(this.categoryId, this.currentPage, this.selectedPageSize);
  }
  refreshPage() {
    this.paginstatus = "true";
    this.searchQuery = "";
    this.selectedPageSize = '50';
    this.currentPage = 1;
    document.getElementById("catError").innerHTML = "";
    this.getAllCategories(this.categoryId, this.currentPage, this.selectedPageSize);
  }
  searchCategory() {
   
    var validObj = [
      {
        "id": 'subCatInput',
        "errorId": 'catError',
        "type": '',
        "blank_msg": ERROR.SUB_CAT_NAME_SEARCH_EMPTY,
        "type_msg": '',
      }
    ]
    var addStatus = this.validService.checkAllValid(validObj);
    
    if (addStatus){
      this.utils.showPageLoader();
    this.dataService.postData('searchSubCategoryByName', {
      "name": this.searchQuery,
      "category_id": this.categoryId
    }, {
      headers: {
        'Authorization': 'Bearer ' + this.token
      }
    }).then((results: any) => {
      if (results.code == 200) {
        this.paginstatus = "false";
        this.totalRecords = results.data.category_list.length;
        this.selectedPageSize = this.totalRecords.toString();
        this.categoryData = results.data.category_list;
        var featuredImg = 0;
        this.utils.hidePageLoader();    
      }
      else if (results.code == 201) {
        this.utils.showError(results.message, 4000);
        this.utils.hidePageLoader();
      }
      else if (results.status || results.status == 0) {
        this.utils.showError(ERROR.SERVER_ERR, 4000);
        this.utils.hidePageLoader();
      }
      else {
        this.utils.showError(results.message, 4000);
        this.utils.hidePageLoader();
      }
    }, (error: any) => {
      console.log(error);
      this.utils.hidePageLoader();
      this.utils.showError(ERROR.SERVER_ERR, 4000);
    }).catch((error: any) => {
      console.log(error);
      this.utils.hidePageLoader();
      this.utils.showError(ERROR.SERVER_ERR, 4000);
    });
    }
  }
  deleteCategory(catId) {
    this.utils.getConfirm().then((result) => {
      this.utils.showLoader();
      this.dataService.postData('deleteSubCategory',
        {
          "sub_category_id": catId
        }, {
        headers: {
          'Authorization': 'Bearer ' + this.token
        }
      }).then((results: any) => {
        if (results.code == 200) {
          this.utils.hideLoader();
          this.utils.showSuccess("Sub category deleted successfully", 4000);
          this.getAllCategories(this.categoryId, this.currentPage, this.selectedPageSize);
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
    });
  }
  addSubCategory() {
    this.open(false, '', "Add sub category");
  }
  addSearchtag(data) {
    this.openTags(false, data);
  }
  protected openTags(closeOnBackdropClick: boolean, data) {
    this.dialog.open(AddsearchtagsComponent, {
      closeOnBackdropClick,closeOnEsc: false, context: {
        subCatData: data
      }
    });
  }
  updateCategory(data) {
    this.open(false, data, "Update sub category");
  }
  protected open(closeOnBackdropClick: boolean, data, titleText) {
    this.dialog.open(AddsubcategoryComponent, {
      closeOnBackdropClick,closeOnEsc: false,autoFocus: false, context: {
        categoryId: this.categoryId,
        subCatData: data,
      }
    }).onClose.subscribe((result) => {
      if (result && result.res == "add") {
        this.getAllCategories(this.categoryId, this.currentPage, this.selectedPageSize);
      }
    });
  }
  viewCatalog(data) {
    localStorage.setItem("selected_sub_category", JSON.stringify(data));
    data.name = data.name.replace(/ /g, '');
    this.route.navigate(['/admin/categories/', this.categoryId, data.name, data.sub_category_id]);
  }
  handlePageChange(event): void {
    this.currentPage = event;
    this.getAllCategories(this.categoryId, this.currentPage, this.selectedPageSize);
  }
  getAllCategories(categoryId, currentPage, selectedPage) {

    this.utils.showPageLoader();
    this.token = localStorage.getItem('at');
    this.dataService.postData('getSubCategoryByCategoryId',
      {
        "category_id": categoryId,
        "page": currentPage,
        "item_count": selectedPage
      }, {
      headers: {
        'Authorization': 'Bearer ' + this.token
      }
    }).then((results: any) => {
      if (results.code == 200) {
        this.totalRecords = results.data.total_record;
        this.categoryData = results.data.category_list;
        // var featuredImg = 0;
        // this.categoryData.forEach(element => {
        //   if(element.is_featured == 1){
        //     featuredImg++;
        //   }
        // });
        // if(this.totalRecords == 0)
        // {
        //   this.utils.hidePageLoader();
        // }
        // else
        // {
        //   this.totalImages = results.data.category_list.length + featuredImg;;
        // }
        this.utils.hidePageLoader();
      }
      else if (results.code == 201) {
        this.utils.showError(results.message, 4000);
        this.utils.hidePageLoader();
      }
      else if (results.status || results.status == 0) {
        this.utils.showError(ERROR.SERVER_ERR, 4000);
        this.utils.hidePageLoader();
      }
      else {
        this.utils.showError(results.message, 4000);
        this.utils.hidePageLoader();
      }
    }, (error: any) => {
      console.log(error);
      this.utils.hidePageLoader();
      this.utils.showError(ERROR.SERVER_ERR, 4000);
    }).catch((error: any) => {
      console.log(error);
      this.utils.hidePageLoader();
      this.utils.showError(ERROR.SERVER_ERR, 4000);
    });
  }
  imageLoad(event){
    if(event.target.previousElementSibling != null)
    {
      event.target.previousElementSibling.classList.remove('placeholder-img');
    }
  }
  viewCorruptedFonts(){
    this.dialog.open(ViewcorruptedfontsComponent,{ closeOnBackdropClick: true });
  }
  selectAllCat()
  {
    console.log("hello");
    this.isselect = true;
    console.log(this.categoryId);
  }
  selectAll(){
    console.log("selectAll");
    
  }
  removeAllCat(){
    console.log("remove All");
  }
  cancelCat(){
    this.isselect = false;
    console.log("cancel");
  }
}

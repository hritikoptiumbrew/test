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
import { NbDialogService, NbIconLibraries, NbWindowRef, NbWindowService } from '@nebular/theme';
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

  constructor(private iconLibraries: NbIconLibraries,private validService: ValidationsService, private actRoute: ActivatedRoute, private dialog: NbDialogService, private dataService: DataService, private utils: UtilService, private route: Router) {
    this.broadItem = JSON.parse(localStorage.getItem('selected_category')).name;
    this.categoryId = this.actRoute.snapshot.params.categoryId;

    this.iconLibraries.registerSvgPack('custom', {
      'postCalendar': `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" class="eva eva-folder" fill="currentColor">
      <path d="M19.0711 0.642121C18.8691 0.438024 18.6286 0.276156 18.3636 0.165924C18.0986 0.0556922 17.8143 -0.000705545 17.5274 6.66213e-06H2.18509C1.60601 6.26755e-06 1.05061 0.230154 0.6409 0.639891C0.231188 1.04963 0.000675595 1.60544 0 2.18524V17.8122C0.000866936 18.3922 0.23136 18.9482 0.640956 19.3583C1.05055 19.7684 1.60584 19.9991 2.18509 20H17.5237C18.1029 19.9994 18.6581 19.7687 19.0674 19.3584C19.4767 18.9482 19.7066 18.3921 19.7066 17.8122V2.1856C19.7081 1.89901 19.6526 1.61499 19.5435 1.35002C19.4345 1.08506 19.2739 0.844434 19.0711 0.642121Z" />
      <path d="M13.501 8.66672V6.3405C13.502 6.20756 13.4677 6.07674 13.4016 5.96157C13.3354 5.8464 13.2398 5.75107 13.1247 5.68542C13.0105 5.61855 12.8808 5.58307 12.7487 5.58253C12.6165 5.58199 12.4865 5.61641 12.3718 5.68235L8.45891 7.92062H6.16463C5.96431 7.92164 5.77252 8.00213 5.63106 8.14455C5.48959 8.28697 5.40993 8.47977 5.40942 8.68092V11.2709C5.41023 11.472 5.49013 11.6647 5.63173 11.8068C5.77333 11.949 5.96514 12.0293 6.16539 12.0301H6.32752V14.1217C6.32752 14.3106 6.38271 14.4605 6.49309 14.5713C6.67778 14.7537 6.93436 14.7507 7.17755 14.7507H7.43375C7.65438 14.7507 7.87157 14.7341 8.03714 14.5713C8.14765 14.4607 8.20271 14.3106 8.20271 14.1251V12.032H8.45547L12.3714 14.2703C12.4848 14.3364 12.6135 14.3713 12.7446 14.3713C12.878 14.3703 13.0089 14.3344 13.1243 14.2672C13.239 14.2011 13.3343 14.1056 13.4003 13.9906C13.4664 13.8755 13.501 13.745 13.5006 13.6121V11.3608C13.804 11.2906 14.0747 11.1191 14.2686 10.8745C14.4624 10.6298 14.568 10.3264 14.568 10.0138C14.568 9.7011 14.4624 9.39768 14.2686 9.15303C14.0747 8.90838 13.804 8.73695 13.5006 8.66672H13.501ZM7.57676 14.1109C7.51825 14.1175 7.42381 14.1175 7.35957 14.1175H7.17793C7.10987 14.1175 7.01886 14.1175 6.96036 14.114V12.0289H7.5802V14.1109H7.57676ZM8.2291 11.3969H6.16424C6.13068 11.3969 6.09848 11.3835 6.07475 11.3597C6.05101 11.3358 6.03768 11.3035 6.03768 11.2698V8.68092C6.03768 8.64722 6.05101 8.61489 6.07475 8.59105C6.09848 8.56721 6.13068 8.55382 6.16424 8.55382H8.2291V11.3969ZM12.872 13.6091C12.8724 13.6309 12.8668 13.6525 12.8559 13.6715C12.845 13.6904 12.8291 13.706 12.81 13.7166C12.7916 13.7283 12.7703 13.7346 12.7485 13.7346C12.7267 13.7346 12.7053 13.7283 12.6869 13.7166L8.8585 11.5278V8.41751L12.6869 6.22876C12.706 6.21722 12.7279 6.21112 12.7502 6.21112C12.7725 6.21112 12.7944 6.21722 12.8135 6.22876C12.8324 6.23947 12.8481 6.2551 12.859 6.27403C12.8698 6.29295 12.8753 6.31446 12.875 6.33628V13.6091H12.872ZM13.5014 10.6965V9.32449C13.6313 9.38548 13.7412 9.48241 13.8182 9.60391C13.8952 9.72542 13.9361 9.86646 13.9361 10.0105C13.9361 10.1545 13.8952 10.2956 13.8182 10.4171C13.7412 10.5386 13.6313 10.6355 13.5014 10.6965Z" fill="#E1E8F3" stroke="#E1E8F3" stroke-width="0.3"/>
      <path d="M3.66312 4.49701C3.95712 4.4973 4.2446 4.41025 4.48917 4.24689C4.73374 4.08353 4.92441 3.8512 5.03705 3.5793C5.1497 3.3074 5.17924 3.00816 5.12196 2.71944C5.06467 2.43072 4.92313 2.1655 4.71524 1.95736C4.50735 1.74921 4.24246 1.60749 3.95409 1.55014C3.66573 1.49278 3.36685 1.52236 3.09529 1.63514C2.82372 1.74792 2.59168 1.93883 2.42852 2.18371C2.26536 2.42858 2.17842 2.71641 2.17871 3.01077C2.17967 3.40465 2.33637 3.78212 2.61455 4.06064C2.89272 4.33915 3.26972 4.49604 3.66312 4.49701ZM3.66312 2.34058C3.79522 2.34029 3.92444 2.37923 4.03443 2.45247C4.14443 2.52572 4.23027 2.62997 4.28108 2.75205C4.3319 2.87414 4.34542 3.00857 4.31993 3.13835C4.29444 3.26812 4.23109 3.38742 4.13789 3.48115C4.04468 3.57487 3.92581 3.63882 3.79631 3.66491C3.6668 3.69099 3.53248 3.67805 3.41032 3.6277C3.28817 3.57735 3.18367 3.49186 3.11004 3.38205C3.03641 3.27224 2.99695 3.14303 2.99666 3.01077C2.99676 2.83356 3.06691 2.66359 3.19179 2.53801C3.31667 2.41243 3.48613 2.34145 3.66312 2.34058Z" fill="#E1E8F3"/>
      <path d="M5.96513 2.89005H16.4434C16.5518 2.89005 16.6559 2.84691 16.7326 2.77012C16.8093 2.69332 16.8524 2.58917 16.8524 2.48057C16.8524 2.37197 16.8093 2.26782 16.7326 2.19102C16.6559 2.11423 16.5518 2.07109 16.4434 2.07109H5.96513C5.85666 2.07109 5.75264 2.11423 5.67594 2.19102C5.59924 2.26782 5.55615 2.37197 5.55615 2.48057C5.55615 2.58917 5.59924 2.69332 5.67594 2.77012C5.75264 2.84691 5.85666 2.89005 5.96513 2.89005Z" fill="#E1E8F3"/>
      <path d="M5.96513 4.15482H11.3882C11.4966 4.15482 11.6007 4.11168 11.6774 4.03489C11.7541 3.9581 11.7971 3.85394 11.7971 3.74534C11.7971 3.63674 11.7541 3.53259 11.6774 3.45579C11.6007 3.379 11.4966 3.33586 11.3882 3.33586H5.96513C5.85666 3.33586 5.75264 3.379 5.67594 3.45579C5.59924 3.53259 5.55615 3.63674 5.55615 3.74534C5.55615 3.85394 5.59924 3.9581 5.67594 4.03489C5.75264 4.11168 5.85666 4.15482 5.96513 4.15482Z" fill="#E1E8F3"/>
      <path d="M11.3542 16.4858H2.97074C2.86228 16.4858 2.75825 16.529 2.68155 16.6058C2.60486 16.6826 2.56177 16.7867 2.56177 16.8953C2.56177 17.0039 2.60486 17.1081 2.68155 17.1849C2.75825 17.2617 2.86228 17.3048 2.97074 17.3048H11.3542C11.4627 17.3048 11.5667 17.2617 11.6434 17.1849C11.7201 17.1081 11.7632 17.0039 11.7632 16.8953C11.7632 16.7867 11.7201 16.6826 11.6434 16.6058C11.5667 16.529 11.4627 16.4858 11.3542 16.4858Z" fill="#E1E8F3"/>
      <path d="M8.39111 17.7522H2.97099C2.86252 17.7522 2.7585 17.7954 2.6818 17.8722C2.6051 17.9489 2.56201 18.0531 2.56201 18.1617C2.56201 18.2703 2.6051 18.3745 2.6818 18.4512C2.7585 18.528 2.86252 18.5712 2.97099 18.5712H8.39403C8.44773 18.571 8.50088 18.5602 8.55042 18.5395C8.59997 18.5187 8.64495 18.4884 8.68279 18.4502C8.72063 18.4121 8.7506 18.3668 8.77097 18.3171C8.79135 18.2673 8.80174 18.214 8.80155 18.1602C8.80135 18.1065 8.79059 18.0533 8.76986 18.0036C8.74913 17.954 8.71884 17.909 8.68073 17.8711C8.64262 17.8332 8.59742 17.8032 8.54773 17.7828C8.49804 17.7624 8.44482 17.752 8.39111 17.7522Z" fill="#E1E8F3"/>
      </svg>`
    });
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
  postCalendar(data){
    localStorage.setItem("selected_sub_category", JSON.stringify(data));
    this.route.navigate(['/admin/categories/', this.categoryId,'post-calendar']);
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
    console.log(this.categoryData);
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

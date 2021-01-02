/*
 * Optimumbrew Technology

 * Project       : Photo Editor Lab
 * File          : catalogsget.component.ts
 * File Created  : Friday, 16th October 2020 11:08:55 am
 * Author        : Optimumbrew
 * Auther Email  : info@optimumbrew.com
 * Last Modified : Thursday, 29th October 2020 11:01:52 am
 * -----
 * Copyright 2018 - 2020 Optimumbrew Technology
 */


import { Component, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { NbDialogService } from '@nebular/theme';
import { AddcatalogComponent } from 'app/components/addcatalog/addcatalog.component';
import { LinkcatalogComponent } from 'app/components/linkcatalog/linkcatalog.component';
import { ViewimageComponent } from 'app/components/viewimage/viewimage.component';
import { DataService } from 'app/data.service';
import { UtilService } from 'app/util.service';
import { ValidationsService } from 'app/validations.service';
import { ERROR, ENV_CONFIG } from '../../app.constants';
@Component({
  selector: 'ngx-catalogsget',
  templateUrl: './catalogsget.component.html',
  styleUrls: ['./catalogsget.component.scss'],
})
export class CatalogsgetComponent implements OnInit {

  broadHome: any;
  broadItem: any;
  categoryId: any;
  subCategoryId: any;
  subCategoryName: any;
  featuredCatalogList: any = [];
  normalCatalogList: any = [];
  catalogList: any;
  token: any;
  totalRecords: any;
  searchQuery: any;
  errormsg = ERROR;
  totalImages:any;
  constructor(private actRoute: ActivatedRoute, private validService: ValidationsService, private dialog: NbDialogService, private dataService: DataService, private utils: UtilService, private route: Router) {
    this.token = localStorage.getItem('at');
    this.broadHome = JSON.parse(localStorage.getItem('selected_category')).name;
    this.broadItem = JSON.parse(localStorage.getItem('selected_sub_category')).name;
    this.categoryId = this.actRoute.snapshot.params.categoryId;
    this.subCategoryId = this.actRoute.snapshot.params.subCategoryId;
    this.subCategoryName = this.actRoute.snapshot.params.subCategoryName;
  }

  ngOnInit(): void {
    this.getAllCatalogs();
  }
  searchCategory() {
    var validObj = [
      {
        "id": 'calogSearchInput',
        "errorId": 'catCalogError',
        "type": '',
        "blank_msg": ERROR.CAT_LOG_SEARCH_EMPTY,
        "type_msg": '',
      }
    ]
    var addStatus = this.validService.checkAllValid(validObj);

    if (addStatus) {
      this.utils.showPageLoader();
      this.dataService.postData('searchCatalogByName', {
        "sub_category_id": this.subCategoryId,
        "name": this.searchQuery
      }, {
        headers: {
          'Authorization': 'Bearer ' + this.token
        }
      }).then((results: any) => {
        if (results.code == 200) {
          this.featuredCatalogList = [];
          this.normalCatalogList = [];
          this.catalogList = results.data.category_list;
          this.totalRecords = this.catalogList.length;
          this.catalogList.forEach(element => {
            if (element.is_featured == 1) {
              this.featuredCatalogList.push(element);
            }
            else {
              this.normalCatalogList.push(element);
            }
          });
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
  viewImage(imgUrl){
    this.dialog.open(ViewimageComponent, { context: {
        imgSrc: imgUrl,
        typeImg: 'cat'
      }
    })
  }
  checkValidation(id, type, catId, blankMsg, typeMsg, validType) {
    var validObj = {
      "id": id,
      "errorId": catId,
      "type": type,
      "blank_msg": blankMsg,
      "type_msg": typeMsg,
      "button_check": {
        "button_id": "subCalogButton",
        "successArr": ['calogSearchInput']
      }
    }
    this.validService.checkValid(validObj);
    if (validType != "blank") {
      this.searchCategory();
    }
  }
  setRank(type,indexItem){
    if(type == "feature")
    {
      var element = this.featuredCatalogList[indexItem];
      this.featuredCatalogList.splice(indexItem, 1);
      this.featuredCatalogList.splice(0, 0, element);
    }
    else
    {
      var element = this.normalCatalogList[indexItem];
      this.normalCatalogList.splice(indexItem, 1);
      this.normalCatalogList.splice(0, 0, element);
    }
  }
  refreshPage() {
    this.searchQuery = "";
    this.getAllCatalogs();
  }
  gotoCategories() {
    this.route.navigate(['/admin/categories']);
  }
  gotoSubCategory() {
    this.route.navigate(['/admin/categories/', this.categoryId]);
  }
  viewCatalog(catalog) {
    localStorage.setItem("selected_catalog", JSON.stringify(catalog));
    catalog.name = catalog.name.replace(/ /g, '');
    if (catalog.is_featured == 1 && this.categoryId == 3) {
      this.route.navigate(['/admin/popular/', this.categoryId, this.subCategoryName, this.subCategoryId, catalog.name, catalog.catalog_id]);
    }
    else if (this.categoryId == 4) {
      this.route.navigate(['/admin/fonts/', this.categoryId, this.subCategoryName, this.subCategoryId, catalog.name, catalog.catalog_id]);
    }
    else if (this.categoryId == 7) {
      this.route.navigate(['/admin/blog-list/', this.categoryId, this.subCategoryName, this.subCategoryId, catalog.name, catalog.catalog_id]);
    }
    else {
      this.route.navigate(['/admin/categories/', this.categoryId, this.subCategoryName, this.subCategoryId, catalog.name, catalog.catalog_id]);
    }
  }
  addCatalog() {
    this.open(false, "");
  }
  editCatalog(data) {
    this.open(false, data);
  }
  protected open(closeOnBackdropClick: boolean, data) {
    this.dialog.open(AddcatalogComponent, {
      closeOnBackdropClick,closeOnEsc: false,autoFocus: false, context: {
        catalogData: data
      }
    }).onClose.subscribe((result) => {
      if (result.res == "add") {
        this.getAllCatalogs();
      }
    });
  }
  linkCatalog(data) {
    var cataLogData = data;
    cataLogData.category_id = this.categoryId;
    this.openLinkTag(false, cataLogData);
  }
  protected openLinkTag(closeOnBackdropClick: boolean, data) {
    this.dialog.open(LinkcatalogComponent, {
      closeOnBackdropClick,closeOnEsc: false, context: {
        catalogData: data
      }
    }).onClose.subscribe((result) => {
      
      if (result.res == "add") {
        this.getAllCatalogs();
      }
    });
  }
  moveToFirst(category,type,indexItem) {
    this.utils.showLoader();
    this.dataService.postData('setCatalogRankOnTheTopByAdmin', {
      "catalog_id": category.catalog_id
    }, {
      headers: {
        'Authorization': 'Bearer ' + this.token
      }
    }).then((results: any) => {

      if (results.code == 200) {
        // this.getAllCatalogs();
        this.setRank(type,indexItem);
        this.utils.showSuccess(results.message, 4000);
        this.utils.hideLoader();
       
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
  deleteCatalog(calogId) {
    this.utils.getConfirm().then((result) => {
      this.utils.showLoader();
      this.dataService.postData('deleteCatalog',
        {
          "catalog_id": calogId
        }, {
        headers: {
          'Authorization': 'Bearer ' + this.token
        }
      }).then((results: any) => {
        if (results.code == 200) {
          this.utils.hideLoader();
          this.utils.showSuccess(results.message, 4000);
          this.getAllCatalogs();
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
  getAllCatalogs() {
    this.utils.showPageLoader();
    this.dataService.postData('getCatalogBySubCategoryId',
      {
        "sub_category_id": this.subCategoryId
      }, {
      headers: {
        'Authorization': 'Bearer ' + this.token
      }
    }).then((results: any) => {
      if (results.code == 200) {
        this.featuredCatalogList = [];
        this.normalCatalogList = [];
        this.catalogList = results.data.category_list;
        this.totalRecords = results.data.total_record;
        // this.sub_category_name = results.data.category_name;
        this.catalogList.forEach(element => {
          if (element.is_featured == 1) {
            this.featuredCatalogList.push(element);
          }
          else {
            this.normalCatalogList.push(element);
          }
        });
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
}

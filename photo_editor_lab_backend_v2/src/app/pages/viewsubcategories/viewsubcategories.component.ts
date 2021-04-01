/*
 * Optimumbrew Technology

 * Project       : Photo Editor Lab
 * File          : viewsubcategories.component.ts
 * File Created  : Monday, 19th October 2020 11:58:13 am
 * Author        : Optimumbrew
 * Auther Email  : info@optimumbrew.com
 * Last Modified : Thursday, 29th October 2020 11:13:11 am
 * -----
 * Copyright 2018 - 2020 Optimumbrew Technology
 */


import { Component, OnInit, ViewChild } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { NbDialogService, NbMenuService } from '@nebular/theme';
import { AddjsondataComponent } from 'app/components/addjsondata/addjsondata.component';
import { AddjsonimagesComponent } from 'app/components/addjsonimages/addjsonimages.component';
import { AddsubcategoryimagesbyidComponent } from 'app/components/addsubcategoryimagesbyid/addsubcategoryimagesbyid.component';
// import { AddblogsComponent } from 'app/components/addblogs/addblogs.component';
import { MovetocatalogComponent } from 'app/components/movetocatalog/movetocatalog.component';
import { UpdatesubcategoryimagebyidComponent } from 'app/components/updatesubcategoryimagebyid/updatesubcategoryimagebyid.component';
import { ViewimageComponent } from 'app/components/viewimage/viewimage.component';
import { DataService } from 'app/data.service';
import { UtilService } from 'app/util.service';
import { ERROR, ENV_CONFIG } from '../../app.constants';
@Component({
  selector: 'ngx-viewsubcategories',
  templateUrl: './viewsubcategories.component.html',
  styleUrls: ['./viewsubcategories.component.scss']
})
export class ViewsubcategoriesComponent implements OnInit {
  token: any;
  broadHome: any;
  broadSubHome: any;
  broadItem: any;
  categoryId: any;
  subCategoryId: any;
  searchTagList: any = [];
  catalogId: any;
  SubCategoryName: any;
  totalRecords: any;
  viewCatdata: any;
  totalImages: any;
  loadedImages: any;
  //This variable is store multiple ids in array which is use for set multirank and move multuiple templates 
  templatesArr: any = [];
  //this variable is use for check multi selction is on or not
  multiselectFlag: any = false;
  copyURLS = [
    { title: 'Copy Thumbnail Image URL', img_url: 'hello', img_type: 'Thumbnail Image' },
    { title: 'Copy Compressed Image URL', img_url: 'hello', img_type: 'Compressed Image' },
    { title: 'Copy Original Image URL', img_url: 'hello', img_type: 'Original Image' }
  ];
  constructor(private menuService: NbMenuService, private dialog: NbDialogService, private route: Router, private actRoute: ActivatedRoute, private dataService: DataService, private utils: UtilService) {
    this.token = localStorage.getItem('at');
    this.broadHome = JSON.parse(localStorage.getItem('selected_category')).name;
    this.broadSubHome = JSON.parse(localStorage.getItem('selected_sub_category')).name;
    this.broadItem = JSON.parse(localStorage.getItem('selected_catalog')).name;
    this.categoryId = this.actRoute.snapshot.params.categoryId;
    this.subCategoryId = this.actRoute.snapshot.params.subCategoryId;
    this.catalogId = this.actRoute.snapshot.params.catalogId;
    this.loadedImages = 0;
    window.setInterval(function () {
      localStorage.removeItem("search_tag_list");
    }, 720000);
    // this.setSearchTags();
  }

  ngOnInit(): void {
    this.menuService.onItemClick().subscribe((event) => {
      this.copyUrl(event.item);
    })
    // setTimeout(function () {
    //   window.dispatchEvent(new Event('resize'))
    // }, 1)
    this.getAllCategories();
  }
  // setSearchTags(){
  //   if(localStorage.getItem("timestamp_tags"))
  //   {
  //     var date = new Date();
  //     // date.setHours( date.getHours() + 2 );
  //     var timestamp = date.getTime();
  //     var curTime = timestamp;
  //     var oldTime = parseInt(localStorage.getItem("timestamp_tags"));
  //     if(curTime > oldTime)
  //     {
  //       localStorage.removeItem("search_tag_list");
  //       localStorage.removeItem("timestamp_tags")
  //     }
  //   }
  //   if(!localStorage.getItem("search_tag_list"))
  //   {
  //     this.getAllSearchTags();
  //   }
  // }
  // getAllSearchTags() {
  //   this.dataService.postData('getAllTags',
  //     {}, {
  //     headers: {
  //       'Authorization': 'Bearer ' + this.token
  //     }
  //   }).then(results => {
  //     if (results.code == 200) {
  //       this.searchTagList = results.data.result;
  //       var date = new Date();
  //       date.setHours( date.getHours() + 2 );
  //       var timestamp = date.getTime();
  //       localStorage.setItem("search_tag_list", JSON.stringify(this.searchTagList));
  //       localStorage.setItem("timestamp_tags",timestamp.toString());
  //       this.utils.hideLoader();
  //     }
  //     else if (results.code == 201) {
  //       this.utils.showError(results.message, 4000);
  //       this.utils.hideLoader();
  //     }
  //     else if (results.status || results.status == 0) {
  //       this.utils.showError(ERROR.SERVER_ERR, 4000);
  //       this.utils.hideLoader();
  //     }
  //     else {
  //       this.utils.showError(results.message, 4000);
  //       this.utils.hideLoader();
  //     }
  //   }, (error: any) => {
  //     console.log(error);
  //     this.utils.hideLoader();
  //     this.utils.showError(ERROR.SERVER_ERR, 4000);
  //   }).catch((error: any) => {
  //     console.log(error);
  //     this.utils.hideLoader();
  //     this.utils.showError(ERROR.SERVER_ERR, 4000);
  //   });
  // }
  gotoCategories() {
    this.route.navigate(['/admin/categories']);
  }
  gotoSubCategories() {
    this.route.navigate(['/admin/categories/', this.categoryId]);
  }
  gotoCatalog() {
    this.SubCategoryName = this.broadSubHome.replace(/ /g, '');
    this.route.navigate(['/admin/categories/', this.categoryId, this.SubCategoryName, this.subCategoryId]);
  }
  moveToFirst(data, indexItem) {
    this.utils.showLoader();
    this.dataService.postData('setContentRankOnTheTopByAdmin', {
      "img_id": data.img_id
    }, {
      headers: {
        'Authorization': 'Bearer ' + this.token
      }
    }).then((results: any) => {

      if (results.code == 200) {
        this.utils.showSuccess(results.message, 4000);
        this.utils.hideLoader();
        this.getAllCategories();
        // var element = this.viewCatdata[indexItem];
        // this.viewCatdata.splice(indexItem, 1);
        // this.viewCatdata.splice(0, 0, element);
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
  deleteTemplate(data) {
    this.utils.getConfirm().then((result) => {
      this.utils.showLoader();
      this.dataService.postData('deleteCatalogImage',
        {
          "img_id": data.img_id
        }, {
        headers: {
          'Authorization': 'Bearer ' + this.token
        }
      }).then((results: any) => {

        if (results.code == 200) {
          this.utils.hideLoader();
          this.getAllCategories();
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
    });
  }
  getAllCategories() {
    this.utils.showPageLoader();
    this.dataService.postData('getDataByCatalogIdForAdmin',
      {
        "catalog_id": this.catalogId
      }, {
      headers: {
        'Authorization': 'Bearer ' + this.token
      }
    }).then(results => {
      if (results.code == 200) {
        this.viewCatdata = results.data.image_list;
        this.totalRecords = this.viewCatdata.length;
        // this.sub_category_name = results.data.category_name;
        this.utils.hidePageLoader();
        window.dispatchEvent(new Event('resize'))
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
  moveToCatalog(data) {
    this.openMove(false, data, []);
  }
  //this gfunction is use for move multiple templates into another catalog
  moveMutipleTemplate(){
    if (this.templatesArr.length == 0) {
      this.utils.showError("Please select templates for move", 6000);
    }
    else
    {
      this.openMove(false, this.viewCatdata[0], this.templatesArr);
    }
  }

  protected openMove(closeOnBackdropClick: boolean, data, ids_arr) {
    this.dialog.open(MovetocatalogComponent, {
      closeOnBackdropClick, closeOnEsc: false, autoFocus: false, context: {
        catalogData: data,
        imgIds: ids_arr
      }
    }).onClose.subscribe((result) => {
      if (result && result.res == "add") {
        this.multiselectFlag = false;
        this.getAllCategories();
      }
    });
  }
  addJsonImages() {
    this.openJsonImages(false);
  }
  protected openJsonImages(closeOnBackdropClick) {
    this.dialog.open(AddjsonimagesComponent, { closeOnBackdropClick, closeOnEsc: false });
  }
  addJsonData() {
    // this.setSearchTags();
    this.openJsonData(false);
  }
  protected openJsonData(closeOnBackdropClick) {
    this.dialog.open(AddjsondataComponent, {
      closeOnBackdropClick, closeOnEsc: false, autoFocus: false, context: {
        catalogId: this.catalogId
      }
    }).onClose.subscribe((result) => {
      if (result && result.res == "add") {
        this.getAllCategories();
      }
    });
  }
  editSubCategory(item) {
    // this.setSearchTags();
    if (item.is_json_data == 1 || item.is_json_data == "1") {
      this.openUpdateJson(false, item);
    }
    else {
      this.openUpdateJsonImage(false, item);
    }
  }
  protected openUpdateJson(closeOnBackdropClick, data) {
    this.dialog.open(AddjsondataComponent, {
      closeOnBackdropClick, closeOnEsc: false, autoFocus: false, context: {
        upJSonData: JSON.parse(JSON.stringify(data)),
        catalogId: this.categoryId
      }
    }).onClose.subscribe((result) => {
      if (result && result.res == "add") {
        this.getAllCategories();
      }
    });
  }
  protected openUpdateJsonImage(closeOnBackdropClick, data) {
    this.dialog.open(UpdatesubcategoryimagebyidComponent, {
      closeOnBackdropClick, closeOnEsc: false, context: {
        categoryData: JSON.parse(JSON.stringify(data))
      }
    }).onClose.subscribe((result) => {
      if (result && result.res == "add") {
        this.getAllCategories();
      }
    });
  }
  addNormalImages() {
    // this.setSearchTags();
    this.openAddSubCategoryImage(false);
  }
  protected openAddSubCategoryImage(closeOnBackdropClick) {
    this.dialog.open(AddsubcategoryimagesbyidComponent, {
      closeOnBackdropClick, closeOnEsc: false, context: {
        catalogId: this.catalogId
      }
    }).onClose.subscribe((result) => {
      if (result && result.res == "add") {
        this.getAllCategories();
      }
    });
  }
  viewImage(imgUrl, type) {
    console.log(type);
    this.dialog.open(ViewimageComponent, {
      context: {
        imgSrc: imgUrl,
        typeImg: 'cat'
      }
    })
  }
  imageLoad(event) {
    if (event.target.previousElementSibling != null) {
      event.target.previousElementSibling.classList.remove('placeholder-img');
    }
  }
  setUrls(thumbnail_img, compressed_img, original_img) {
    this.copyURLS[0].img_url = thumbnail_img;
    this.copyURLS[1].img_url = compressed_img;
    this.copyURLS[2].img_url = original_img;
  }
  copyUrl(url_item) {
    if (url_item.img_type == "Thumbnail Image" || url_item.img_type == "Compressed Image" || url_item.img_type == "Original Image") {
      let selBox = document.createElement('textarea');
      selBox.style.position = 'fixed';
      selBox.style.left = '0';
      selBox.style.top = '0';
      selBox.style.opacity = '0';
      selBox.value = url_item.img_url;
      document.body.appendChild(selBox);
      selBox.focus();
      selBox.select();
      document.execCommand('copy');
      document.body.removeChild(selBox);
      this.utils.showSuccess("Copied the URL for " + url_item.img_type, 2000);
    }
  }
  addTemplateRank(event, temp_index) {
    if (this.templatesArr.length > 19) {
      if (!this.templatesArr.includes(temp_index)) {
        this.utils.showError("Maximum 20 templates are allow for select", 6000);
      }
      else {
        this.templatesArr.splice(this.templatesArr.indexOf(temp_index), 1);
      }
    }
    else {
      if (!this.templatesArr.includes(temp_index)) {
        this.templatesArr.push(temp_index);
      }
      else {
        this.templatesArr.splice(this.templatesArr.indexOf(temp_index), 1);
      }
    }
  }
  uploadTemplateRankArr() {
    if (this.templatesArr.length == 0) {
      this.utils.showError("Please select templates for set rank", 6000);
    }
    else {
      this.utils.showLoader();
      this.dataService.postData('setMultipleContentRankByAdmin', {
        "img_ids": this.templatesArr
      }, {
        headers: {
          'Authorization': 'Bearer ' + this.token
        }
      }).then((results: any) => {

        if (results.code == 200) {
          this.utils.showSuccess(results.message, 4000);
          this.utils.hideLoader();
          this.templatesArr = []
          this.multiselectFlag = false;
          this.getAllCategories();
          // var element = this.viewCatdata[indexItem];
          // this.viewCatdata.splice(indexItem, 1);
          // this.viewCatdata.splice(0, 0, element);
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
}

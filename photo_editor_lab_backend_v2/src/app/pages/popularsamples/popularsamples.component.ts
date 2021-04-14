/*
 * Optimumbrew Technology

 * Project       : Photo Editor Lab
 * File          : popularsamples.component.ts
 * File Created  : Thursday, 22nd October 2020 04:54:48 pm
 * Author        : Optimumbrew
 * Auther Email  : info@optimumbrew.com
 * Last Modified : Thursday, 22nd October 2020 06:42:08 pm
 * -----
 * Copyright 2018 - 2020 Optimumbrew Technology
 */


import { Component, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { NbDialogService } from '@nebular/theme';
import { PopularsampleaddComponent } from 'app/components/popularsampleadd/popularsampleadd.component';
import { ViewimageComponent } from 'app/components/viewimage/viewimage.component';
import { DataService } from 'app/data.service';
import { UtilService } from 'app/util.service';
import { ERROR, ENV_CONFIG } from '../../app.constants';
@Component({
  selector: 'ngx-popularsamples',
  templateUrl: './popularsamples.component.html',
  styleUrls: ['./popularsamples.component.scss']
})
export class PopularsamplesComponent implements OnInit {

  broadHome: any;
  broadSubHome: any;
  broadItem: any;
  totalRecords: any;
  categoryId: any;
  subCategoryId: any;
  catalogId: any;
  sampleData: any;
  SubCategoryName: any;
  pageSize: any = [25,50,75,100];
  selectedPageSize: any = '50';
  token: any;
  constructor(private dialog: NbDialogService, private route: Router, private actRoute: ActivatedRoute, private dataService: DataService, private utils: UtilService) {
    this.token = localStorage.getItem("at");
  }

  ngOnInit(): void {
    this.broadHome = JSON.parse(localStorage.getItem('selected_category')).name;
    this.broadSubHome = JSON.parse(localStorage.getItem('selected_sub_category')).name;
    this.broadItem = JSON.parse(localStorage.getItem('selected_catalog')).name;
    this.categoryId = this.actRoute.snapshot.params.categoryId;
    this.subCategoryId = this.actRoute.snapshot.params.subCategoryId;
    this.catalogId = this.actRoute.snapshot.params.catalogId;
    this.getAllBackgroundCatogory(this.catalogId);
  }

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
  // setPageSize(value) {
  //   this.selectedPageSize = value;
  //   // this.getAllCategories(this.categoryId, this.currentPage, this.selectedPageSize);
  //   // this.utils.showLoader();
  // }
  uploadImages(data) {
    this.open(false, data);
  }
  protected open(closeOnBackdropClick: boolean, data) {
    this.dialog.open(PopularsampleaddComponent, {
      closeOnBackdropClick,closeOnEsc: false,autoFocus: false, context: {
        sampleData: data,
        catalogId: this.catalogId
      }
    }).onClose.subscribe((result) => {
      if (result.res == "add") {
        this.getAllBackgroundCatogory(this.catalogId);
      }
    });
  }
  deleteSampleImage(data) {
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
          this.utils.showSuccess(results.message, 4000);
          this.getAllBackgroundCatogory(this.catalogId);
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
  getAllBackgroundCatogory(catalogId) {
    this.utils.showPageLoader();
    this.dataService.postData('getSampleImagesForAdmin',
      {
        "catalog_id": catalogId
      }, {
      headers: {
        'Authorization': 'Bearer ' + this.token
      }
    }).then((results: any) => {
      if (results.code == 200) {
        this.sampleData = results.data.image_list;
        this.totalRecords = this.sampleData.length;
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
  viewImage(imgUrl){
    this.dialog.open(ViewimageComponent, { context: {
        imgSrc: imgUrl,
        typeImg: 'cat'
      }
    })
  }
  imageLoad(event){
    if(event.target.previousElementSibling != null)
    {
      event.target.previousElementSibling.classList.remove('placeholder-img');
    }
  }
}

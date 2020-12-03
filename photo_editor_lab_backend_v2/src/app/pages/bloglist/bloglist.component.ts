/*
 * Optimumbrew Technology

 * Project       : Photo Editor Lab
 * File          : bloglist.component.ts
 * File Created  : Thursday, 22nd October 2020 06:40:00 pm
 * Author        : Optimumbrew
 * Auther Email  : info@optimumbrew.com
 * Last Modified : Thursday, 22nd October 2020 06:42:34 pm
 * -----
 * Copyright 2018 - 2020 Optimumbrew Technology
 */


import { Component, OnInit } from '@angular/core';
import { DataService } from 'app/data.service';
import { UtilService } from 'app/util.service';
import { ERROR, ENV_CONFIG } from '../../app.constants';
import { ActivatedRoute, Router } from '@angular/router';
import { NbDialogService } from '@nebular/theme';
import { AddblogsComponent } from 'app/components/addblogs/addblogs.component';
import { ViewimageComponent } from 'app/components/viewimage/viewimage.component';
@Component({
  selector: 'ngx-bloglist',
  templateUrl: './bloglist.component.html',
  styleUrls: ['./bloglist.component.scss']
})
export class BloglistComponent implements OnInit {
  previousLabel = "<";
  nextLabel=">";
  broadHome: any;
  broadSubHome: any;
  currentPage: any = 1;
  broadItem: any;
  totalRecords: any;
  categoryId: any;
  subCategoryId: any;
  catalogId: any;
  BlogData: any;
  SubCategoryName: any;
  pageSize: any = [15, 30, 45, 60, 75, 90, 100];
  selectedPageSize: any = '15';
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
    this.getAllBlogList();
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
  setPageSize(value) {
    this.selectedPageSize = value;
    if(this.selectedPageSize > this.totalRecords)
    {
      this.currentPage = 1;
    }
    this.getAllBlogList();
  }
  handlePageChange(event): void {
    this.currentPage = event;
    this.getAllBlogList();
  }
  uploadBlogs(data) {
    this.open(false, data);
  }
  protected open(closeOnBackdropClick: boolean, data) {
    this.dialog.open(AddblogsComponent, {
      closeOnBackdropClick,closeOnEsc: false, context: {
        upBlogData: data,
        catalogId: this.catalogId
      }
    }).onClose.subscribe((result) => {
      if (result.res && result.res == "add") {
        this.getAllBlogList();
      }
    });
  }
  moveToFirst(data,indexItem) {
    this.utils.showLoader();
    this.dataService.postData('setBlogRankOnTheTopByAdmin',
      {
        "blog_id": data.blog_id,
      }, {
      headers: {
        'Authorization': 'Bearer ' + this.token
      }
    }).then((results: any) => {
      if (results.code == 200) {
        this.utils.hideLoader();
        this.utils.showSuccess(results.message, 4000);
        // this.getAllBlogList();
        var element = this.BlogData[indexItem];
        this.BlogData.splice(indexItem, 1);
        this.BlogData.splice(0, 0, element);
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
  deleteBlogContent(data) {
    this.utils.getConfirm().then((result) => {
      this.utils.showLoader();
      this.dataService.postData('deleteBlogContent',
        {
          "blog_id": data.blog_id,
          "fg_image": data.fg_image
        }, {
        headers: {
          'Authorization': 'Bearer ' + this.token
        }
      }).then((results: any) => {
        if (results.code == 200) {
          this.utils.hideLoader();
          this.utils.showSuccess(results.message, 4000);
          this.getAllBlogList();
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
  getAllBlogList() {
    this.utils.showPageLoader();
    this.dataService.postData('getBlogContent',
      {
        "catalog_id": this.catalogId,
        "page": this.currentPage,
        "item_count": this.selectedPageSize
      }, {
      headers: {
        'Authorization': 'Bearer ' + this.token
      }
    }).then((results: any) => {
      if (results.code == 200) {
        this.BlogData = results.data.result;
        this.totalRecords = results.data.total_record;
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

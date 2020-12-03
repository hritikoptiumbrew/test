/*
 * Optimumbrew Technology

 * Project       : Photo Editor Lab
 * File          : categories.component.ts
 * File Created  : Thursday, 15th October 2020 01:12:03 pm
 * Author        : Optimumbrew
 * Auther Email  : info@optimumbrew.com
 * Last Modified : Thursday, 29th October 2020 11:03:30 am
 * -----
 * Copyright 2018 - 2020 Optimumbrew Technology
 */


import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { NbDialogService } from '@nebular/theme';
import { EditcategoryComponent } from 'app/components/editcategory/editcategory.component';
import { DataService } from 'app/data.service';
import { UtilService } from 'app/util.service';
import { ValidationsService } from 'app/validations.service';
import { LocalDataSource } from 'ng2-smart-table';


import { ERROR, ENV_CONFIG } from '../../app.constants';
@Component({
  selector: 'ngx-categories',
  templateUrl: './categories.component.html',
  styleUrls: ['./categories.component.scss']
})
export class CategoriesComponent implements OnInit  {

  datasource:LocalDataSource;
  token: any;
  totalRecords: any;
  categoryData: any;
  categoryName: any = "";
  errormsg = ERROR;
  selectedPageSize= 15;
  previousLabel = "<";
  nextLabel=">";
  currentPage:any=1;
  constructor(private validService: ValidationsService, private dialog: NbDialogService, private dataService: DataService, private utils: UtilService, private router: Router) {
    this.token = localStorage.getItem('at');
    this.getAllBackgroundCatogory(this.currentPage);
  }
  ngOnInit(): void {
   
  }
  handlePageChange(event): void {
    this.currentPage = event;
    this.getAllBackgroundCatogory(this.currentPage);
  }
  settings = {
    mode: 'external',
    edit: {
      editButtonContent: '<i class="fa fa-edit" title="edit"></i>',
      saveButtonContent: '<i class="nb-checkmark"></i>',
      cancelButtonContent: '<i class="nb-close"></i>',
      confirmSave: true,
    },
    delete: {
      deleteButtonContent: '<i class="fa fa-folder-open" title="View"></i>',
      confirmDelete: true,
    },
    columns: {
      id: {
        title: '#',
        type: 'number',
        width: '75px',
        editable: false,
        filter: false,
        hideHeader: true,
      },
      name: {
        title: 'Category Name',
        type: 'html',
        filter: false,
        sort: false,
      }
    },
    actions: {
      add: false,
      position: 'right',
      delete: true,
      edit: true,
    },
    pager:{
      perPage: this.selectedPageSize
    },
    hideSubHeader: true
  };

  getAllBackgroundCatogory(currentPage) {
    this.utils.showPageLoader();
    this.dataService.postData('getAllCategory',
      {
        "page": currentPage
      }, {
      headers: {
        'Authorization': 'Bearer ' + this.token
      }
    }).then((results: any) => {

      if (results.code == 200) {
        this.totalRecords = results.data.total_record;
        var i = 0;
        results.data.category_list.forEach(appname => {
          appname.id = i+1+(15 * (currentPage-1)); 
          i++;
        });
        this.categoryData = results.data.category_list;
        
        this.datasource = new LocalDataSource(this.categoryData);
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

  getRow(event) {
    localStorage.setItem("selected_category", JSON.stringify(event.data));
    this.router.navigate(['/admin/categories/', event.data.category_id]);
  }
 
  editCategory(event) {
    this.open(false, event.data);
  }
  addCategory(){
    this.open(false, '');
  }
  protected open(closeOnBackdropClick: boolean, data) {
    this.dialog.open(EditcategoryComponent, {
      closeOnBackdropClick,closeOnEsc: false, context: {
        categoryData: data
      }
    }).onClose.subscribe((result) => {
      if (result.res == "add") {
        this.getAllBackgroundCatogory(this.currentPage);
      }
    });
  }
}

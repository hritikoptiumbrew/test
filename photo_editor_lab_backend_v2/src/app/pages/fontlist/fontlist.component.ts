/*
 * Optimumbrew Technology

 * Project       : Photo Editor Lab
 * File          : fontlist.component.ts
 * File Created  : Thursday, 22nd October 2020 12:16:53 pm
 * Author        : Optimumbrew
 * Auther Email  : info@optimumbrew.com
 * Last Modified : Thursday, 22nd October 2020 12:27:55 pm
 * -----
 * Copyright 2018 - 2020 Optimumbrew Technology
 */


import { Component, OnInit, ViewChild } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { DataService } from 'app/data.service';
import { UtilService } from 'app/util.service';
import { ERROR, ENV_CONFIG } from '../../app.constants';
import * as $ from 'jquery';
import { EditfontComponent } from 'app/components/editfont/editfont.component';
import { NbDialogRef, NbDialogService } from '@nebular/theme';
import { LocalDataSource, Ng2SmartTableComponent } from 'ng2-smart-table';
@Component({
  selector: 'ngx-fontlist',
  templateUrl: './fontlist.component.html',
  styleUrls: ['./fontlist.component.scss']
})
export class FontlistComponent implements OnInit {
  dataSource: LocalDataSource
  selectedCategory: any = JSON.parse(localStorage.getItem("selected_category"));
  selectedCatalog: any = JSON.parse(localStorage.getItem("selected_catalog"));
  pageSize: any = [15, 30, 45, 60, 75, 90, 100];
  selectedPageSize= '15';
  broadHome: any;
  broadSubHome: any;
  broadItem: any;
  categoryId: any;
  subCategoryId: any;
  catalogId: any;
  checkedAll: any;
  SubCategoryName: any;
  totalRecords: any = 24;
  FontData: any;
  fontIds: any = [];
  token: any;
  j: any = 1;
  fontDetails: any = {};
  public input: string = '<input type="checkbox"></input>';
  public inputCheck: string = '<input type="checkbox" checked></input>';
  formData = new FormData();
  fileList: any;
  file: any;
  @ViewChild('table') table: Ng2SmartTableComponent;
  settings = {
    mode: 'external',
    selectMode: 'multi',
    add: {
      addButtonContent: '<i class="nb-plus"></i>',
      createButtonContent: '<i class="nb-checkmark"></i>',
      cancelButtonContent: '<i class="nb-close"></i>',
    },
    edit: {
      editButtonContent: '<i class="fa fa-edit" title="Edit"></i>',
      saveButtonContent: '<i class="nb-checkmark"></i>',
      cancelButtonContent: '<i class="nb-close"></i>',
      confirmSave: true,
    },
    delete: {
      deleteButtonContent: '<i class="fa fa-trash-alt" title="Delete"></i>',
      confirmDelete: true,
    },
    columns: {
      id: {
        title: '#',
        type: 'text',
        width: '75px',
        editable: false,
        filter: false,
        hideHeader: true,
        sort: false,
      },
      font_name: {
        title: 'Font Name',
        type: 'html',
        hideHeader: true,
        filter: false,
      },
      ios_font_name: {
        title: 'Font Name(ios)',
        type: 'html',
        hideHeader: true,
        filter: false,
      },
      android_font_name: {
        title: 'Font Path(Android)',
        type: 'html',
        hideHeader: true,
        filter: false,
      },
      is_active: {
        title: 'Status',
        type: 'html',
        filter: false,
        valuePrepareFunction: (value) => { return value == 0 ? "In-Active" : "Active" },
      }
    },
    actions: {
      add: false,
      position: 'right',
      delete: true,
      edit: true,
    },
    pager: {
      display: false,
      perPage: parseInt(this.selectedPageSize)
    },
    rowClassFunction: (row)=>{
      return "table-td-th"
    }
  };
  setPageSize(value) {
    this.selectedPageSize = value;
    this.settings.pager.perPage = parseInt(this.selectedPageSize);
    this.dataSource.setPaging(this.dataSource.getPaging().page,parseInt(this.selectedPageSize),false);
  }
  constructor(private dialog: NbDialogService, private route: Router, private actRoute: ActivatedRoute, private dataService: DataService, private utils: UtilService) {
    this.token = localStorage.getItem('at');
  }
  ngOnInit(): void {
    this.broadHome = JSON.parse(localStorage.getItem('selected_category')).name;
    this.broadSubHome = JSON.parse(localStorage.getItem('selected_sub_category')).name;
    this.broadItem = JSON.parse(localStorage.getItem('selected_catalog')).name;
    this.categoryId = this.actRoute.snapshot.params.categoryId;
    this.subCategoryId = this.actRoute.snapshot.params.subCategoryId;
    this.catalogId = this.actRoute.snapshot.params.catalogId;
    this.getAllFontsByCatalogId(this.catalogId);
  }
  getRows(event){
    this.j = 1;
  }
  fileChange(event) {
    if (event.target.files && event.target.files[0]) {
      var reader = new FileReader();
      reader.onload = (event: any) => {
        this.fontDetails.font_file = event.target.result;
        document.getElementById("fontError").innerHTML = "";
      }
      reader.readAsDataURL(event.target.files[0]);
    }
    this.fileList = event.target.files;
    if (this.fileList.length > 0) {
      this.formData.delete("file");
      this.file = this.fileList[0];
      this.formData.append('file', this.file, this.file.name);
    }
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
  selectFonts(event) {
   if(event.selected.length < this.totalRecords)
   {
    this.table.isAllSelected = false;
   }
   else if(event.selected.length == this.totalRecords)
   {
    this.table.isAllSelected = true;
   }
    this.fontIds = [];
    event.selected.forEach(element => {
      this.fontIds.push(element.font_id);
    });
  }
  moveToCorrupt() {
    if (this.fontIds.length == 0) {
      this.utils.showError(ERROR.SEL_FONT, 4000);
    }
    else {
      this.utils.getConfirm().then((result) => {
        this.utils.showLoader();
        this.dataService.postData('removeInvalidFont',
          {
            "catalog_id": parseInt(this.catalogId),
            "font_ids": this.fontIds.join(",")
          }, {
          headers: {
            'Authorization': 'Bearer ' + this.token
          }
        }).then((results: any) => {
          if (results.code == 200) {
            this.table.isAllSelected = false;
            this.utils.hideLoader();
            this.getAllFontsByCatalogId(this.catalogId);
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
  }
  getAllFontsByCatalogId(catalogId) {
    this.utils.showPageLoader();
    this.dataService.postData('getAllFontsByCatalogIdForAdmin',
      {
        "catalog_id": catalogId
      }, {
      headers: {
        'Authorization': 'Bearer ' + this.token
      }
    }).then((results: any) => {

      if (results.code == 200) {
        var j = 1;
        results.data.result.forEach(appname => {
          appname.id = j;
          j++; 
        });
        this.totalRecords = results.data.total_count;
        this.FontData = results.data.result;
        this.dataSource = new LocalDataSource(this.FontData);
        this.dataSource.setPaging(this.dataSource.getPaging().page,parseInt(this.selectedPageSize),false);
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
  addFont(){
    this.open(false,'');
  }
  editFont(event) {
    this.open(false, event.data);
  }
  protected open(closeOnBackdropClick: boolean, data) {
    this.dialog.open(EditfontComponent, {
      closeOnBackdropClick,closeOnEsc: false, autoFocus: false, context: {
        fontData: data,
        catalogId: this.catalogId
      }
    }).onClose.subscribe((result) => {
      if (result.res == "add") {
        this.j = 1;
        this.getAllFontsByCatalogId(this.catalogId);
      }
    });
  }
  deleteFont(event) {
    this.utils.getConfirm().then((result) => {
      this.utils.showLoader();
      this.dataService.postData('deleteFont',
        {
          "font_id": event.data.font_id
        }, {
        headers: {
          'Authorization': 'Bearer ' + this.token
        }
      }).then((results: any) => {
        if (results.code == 200) {
          this.utils.hideLoader();
          this.j = 1;
          this.utils.showSuccess(results.message, 4000);
          this.getAllFontsByCatalogId(this.catalogId);
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
  addNewFont(font_details: any, is_replace) {
    if (!font_details.font_file) {
      document.getElementById("fontError").innerHTML = ERROR.FONT_FILE_EMPTY;
      return false;
    }
    else {
      this.utils.showLoader();
      let request_data: any = {
        "category_id": this.selectedCategory.category_id,
        "is_featured": this.selectedCatalog.is_featured,
        "catalog_id": this.catalogId,
        "ios_font_name": this.fontDetails.font_name,
        "android_font_name": font_details.android_font_name,
        "is_replace": is_replace
      };
      this.formData.append("request_data", JSON.stringify(request_data));
      this.dataService.postData('addFont',
        this.formData, {
        headers: {
          'Authorization': 'Bearer ' + this.token
        }
      }).then((results: any) => {
        if (results.code == 200) {
          this.utils.hideLoader();
          $("#fontFile").val("");
          this.fontDetails.font_name = "";
          this.utils.showSuccess(results.message, 4000);
          this.getAllFontsByCatalogId(this.catalogId);
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

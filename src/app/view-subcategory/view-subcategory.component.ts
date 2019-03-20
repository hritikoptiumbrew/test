import { Component, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { MdDialog, MdSnackBar, MdSnackBarConfig } from '@angular/material';
import { DataService } from '../data.service';
import { LoadingComponent } from '../loading/loading.component';
import { UpdateSubcategoryImageByIdComponent } from '../update-subcategory-image-by-id/update-subcategory-image-by-id.component';
import { AddSubcategoryImagesByIdComponent } from '../add-subcategory-images-by-id/add-subcategory-images-by-id.component';
import { DeleteSubcategoryImageByIdComponent } from '../delete-subcategory-image-by-id/delete-subcategory-image-by-id.component';
import { AddJsonImagesComponent } from '../add-json-images/add-json-images.component';
import { AddJsonDataComponent } from '../add-json-data/add-json-data.component';
import { UpdateJsonDataComponent } from '../update-json-data/update-json-data.component';

@Component({
  selector: 'app-view-subcategory',
  templateUrl: './view-subcategory.component.html'
})
export class ViewSubcategoryComponent implements OnInit {

  token: any;
  private sub: any; //route subscriber
  private categoryId: any;
  subCategoryName: any;
  private catalogId: any;
  sub_category_list: any[];
  sub_category_name: any;
  search_tag_list: any = [];
  catalogName: any;
  errorMsg: any;
  successMsg: any;
  total_record: any;
  itemsPerPage: number = 15;
  currentPage: number = 1;
  loading: any;
  current_path: any = "";

  constructor(public route: ActivatedRoute, private dataService: DataService, private router: Router, public dialog: MdDialog, public snackBar: MdSnackBar) {
    this.loading = this.dialog.open(LoadingComponent);
  }

  ngOnInit() {
    this.token = localStorage.getItem('photoArtsAdminToken');
    this.current_path = this.getLocalStorageData();
    this.sub = this.route.params
      .subscribe(params => {
        this.subCategoryName = params['subCategoryName'];
        this.catalogId = params['catalogId'];
        this.catalogName = params['catalogName'];
        this.categoryId = params['categoryId'];
        this.getAllCategories();
      });
  }

  getAllSearchTags() {
    this.token = localStorage.getItem('photoArtsAdminToken');
    this.dataService.postData('getAllTags',
      {}, {
        headers: {
          'Authorization': 'Bearer ' + this.token
        }
      }).subscribe(results => {
        if (results.code == 200) {
          this.search_tag_list = results.data.result;
          localStorage.setItem("search_tag_list", JSON.stringify(this.search_tag_list));
          this.loading.close();
          this.errorMsg = "";
        }
        else if (results.code == 400) {
          this.loading.close();
          localStorage.removeItem("photoArtsAdminToken");
          this.router.navigate(['/admin']);
        }
        else if (results.code == 401) {
          this.token = results.data.new_token;
          localStorage.setItem("photoArtsAdminToken", this.token);
          this.getAllSearchTags();
        }
        else {
          this.loading.close();
          this.successMsg = "";
          this.errorMsg = results.message;
        }
      }, error => {
        /* console.log(error.status); */
        /* console.log(error); */
      });
  }

  pageChanged(event) {
    this.loading = this.dialog.open(LoadingComponent);
    this.currentPage = event;
    this.getAllCategories();
  }

  getAllCategories() {
    this.token = localStorage.getItem('photoArtsAdminToken');
    this.dataService.postData('getDataByCatalogIdForAdmin',
      {
        "catalog_id": this.catalogId
      }, {
        headers: {
          'Authorization': 'Bearer ' + this.token
        }
      }).subscribe(results => {
        if (results.code == 200) {
          this.sub_category_list = results.data.image_list;
          this.total_record = this.sub_category_list.length;
          this.sub_category_name = results.data.category_name;
          this.errorMsg = "";
          this.successMsg = results.message;
          this.loading.close();
          this.getAllSearchTags();
        }
        else if (results.code == 400) {
          this.loading.close();
          localStorage.removeItem("photoArtsAdminToken");
          this.router.navigate(['/admin']);
        }
        else if (results.code == 401) {
          this.token = results.data.new_token;
          localStorage.setItem("photoArtsAdminToken", this.token);
          this.getAllCategories();
        }
        else {
          this.loading.close();
          this.successMsg = "";
          this.errorMsg = results.message;
        }
      }, error => {
        /* console.log(error.status); */
        /* console.log(error); */
      });
  }

  viewSubCategory(category) {
    this.router.navigate(['/admin/categories/', this.categoryId, category.category_id]);
  }

  openAddJSONImages() {
    let dialogRef = this.dialog.open(AddJsonImagesComponent);
  }

  openAddJSONData() {
    let dialogRef = this.dialog.open(AddJsonDataComponent, {
      panelClass: 'add-json-dialog'
    });
    dialogRef.componentInstance.catalog_data.catalog_id = this.catalogId;
    dialogRef.afterClosed().subscribe(result => {
      if (!result) {
        this.getAllCategories();
      }
    });
  }

  addSubCategory() {
    let dialogRef = this.dialog.open(AddSubcategoryImagesByIdComponent);
    dialogRef.componentInstance.catalog_id = this.catalogId;
    dialogRef.afterClosed().subscribe(result => {
      if (!result) {
        this.getAllCategories();
      }
    });
  }

  moveToFirst(category) {
    this.loading = this.dialog.open(LoadingComponent);
    this.dataService.postData('setContentRankOnTheTopByAdmin', {
      "img_id": category.img_id
    }, {
        headers: {
          'Authorization': 'Bearer ' + this.token
        }
      }).subscribe(results => {
        if (results.code == 200) {
          this.showSuccess(results.message, false);
          this.getAllCategories();
          this.errorMsg = "";
          // this.loading.close();
        }
        else if (results.code == 400) {
          this.loading.close();
          localStorage.removeItem("photoArtsAdminToken");
          this.router.navigate(['/admin']);
        }
        else if (results.code == 401) {
          this.token = results.data.new_token;
          this.loading.close();
          localStorage.setItem("photoArtsAdminToken", this.token);
          this.moveToFirst(category);
        }
        else {
          this.loading.close();
          this.showError(results.message, false);
        }
      });
  }

  updateSubCategory(category) {
    if (category.is_json_data == 1 || category.is_json_data == "1") {
      let catalog_data = JSON.parse(JSON.stringify(category));
      let dialogRef = this.dialog.open(UpdateJsonDataComponent, {
        panelClass: 'add-json-dialog'
      });
      dialogRef.componentInstance.catalog_data = catalog_data;
      dialogRef.componentInstance.catalog_id = this.catalogId;
      dialogRef.afterClosed().subscribe(result => {
        if (!result) {
          this.getAllCategories();
        }
      });
    }
    else {
      let sub_category_data = JSON.parse(JSON.stringify(category));
      let dialogRef = this.dialog.open(UpdateSubcategoryImageByIdComponent);
      dialogRef.componentInstance.sub_category_data = sub_category_data;
      dialogRef.afterClosed().subscribe(result => {
        if (!result) {
          this.getAllCategories();
        }
      });
    }
  }

  deleteSubCategory(category) {
    let dialogRef = this.dialog.open(DeleteSubcategoryImageByIdComponent);
    dialogRef.componentInstance.sub_category_img_id = category.img_id;
    dialogRef.afterClosed().subscribe(result => {
      if (!result) {
        this.getAllCategories();
      }
    });
  }

  showError(message, action) {
    let config = new MdSnackBarConfig();
    config.extraClasses = ['snack-error'];
    /* config.horizontalPosition = "right";
    config.verticalPosition = "top"; */
    config.duration = 5000;
    this.snackBar.open(message, action ? 'Okay!' : undefined, config);
  }

  showSuccess(message, action) {
    let config = new MdSnackBarConfig();
    config.extraClasses = ['snack-success'];
    /* config.horizontalPosition = "right";
    config.verticalPosition = "top"; */
    config.duration = 5000;
    this.snackBar.open(message, action ? 'Okay!' : undefined, config);
  }

  getLocalStorageData() {
    let tmp_selected_catagory = JSON.parse(localStorage.getItem("selected_catagory"));
    let tmp_selected_sub_catagory = JSON.parse(localStorage.getItem("selected_sub_catagory"));
    let tmp_selected_catalog = JSON.parse(localStorage.getItem("selected_catalog"));
    let tmp_current_path = tmp_selected_catagory.name + " / " + tmp_selected_sub_catagory.name + " / " + tmp_selected_catalog.name;
    return tmp_current_path;
  }
}

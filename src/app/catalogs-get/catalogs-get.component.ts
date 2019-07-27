import { Component, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { MdDialog, MdSnackBar, MdSnackBarConfig } from '@angular/material';
import { DataService } from '../data.service';
import { LoadingComponent } from '../loading/loading.component';
import { CatalogsAddComponent } from '../catalogs-add/catalogs-add.component';
import { CatalogsUpdateComponent } from '../catalogs-update/catalogs-update.component';
import { CatalogsDeleteComponent } from '../catalogs-delete/catalogs-delete.component';
import { ConfirmActionComponent } from '../confirm-action/confirm-action.component';
import { LinkCatelogComponent } from '../link-catelog/link-catelog.component';

@Component({
  selector: 'app-catalogs-get',
  templateUrl: './catalogs-get.component.html'
})
export class CatalogsGetComponent implements OnInit {

  token: any;
  private sub: any; //route subscriber
  private categoryId: any;
  subCategoryName: any;
  private subCategoryId: any;
  private catalogName: any;
  private catalogId: any;
  catalog_list: any[] = [];
  featured_catalog_list: any[] = [];
  normal_catalog_list: any[] = [];
  sub_category_name: any;
  errorMsg: any;
  searchErr: any;
  searchQuery: any;
  searchTag: any;
  successMsg: any;
  total_record: any;
  itemsPerPage: number = 15;
  currentPage: number = 1;
  loading: any;
  current_path: any = "";

  constructor(public route: ActivatedRoute, private dataService: DataService, public snackBar: MdSnackBar, private router: Router, public dialog: MdDialog) {
    this.loading = this.dialog.open(LoadingComponent);
  }

  ngOnInit() {
    this.token = localStorage.getItem('photoArtsAdminToken');
    this.current_path = this.getLocalStorageData();
    this.sub = this.route.params
      .subscribe(params => {
        this.subCategoryName = params['subCategoryName'];
        this.subCategoryId = params['subCategoryId'];
        this.categoryId = params['categoryId'];
        this.getAllCatalogs(this.subCategoryId);
      });
  }

  linkCatalog(category) {
    let catalog_data = category;
    catalog_data.category_id = this.categoryId;
    let dialogRef = this.dialog.open(LinkCatelogComponent);
    dialogRef.componentInstance.catalog_data = JSON.parse(JSON.stringify(catalog_data));
    dialogRef.afterClosed().subscribe(result => {
      this.getAllCatalogs(this.subCategoryId);
    });
  }

  pageChanged(event) {
    this.loading = this.dialog.open(LoadingComponent);
    this.currentPage = event;
    this.getAllCatalogs(this.subCategoryId);
  }

  getAllCatalogs(categoryId) {
    this.featured_catalog_list = [];
    this.normal_catalog_list = [];
    this.token = localStorage.getItem('photoArtsAdminToken');
    this.dataService.postData('getCatalogBySubCategoryId',
      {
        "sub_category_id": this.subCategoryId
      }, {
        headers: {
          'Authorization': 'Bearer ' + this.token
        }
      }).subscribe(results => {
        if (results.code == 200) {
          this.catalog_list = results.data.category_list;
          this.total_record = results.data.total_record;
          this.sub_category_name = results.data.category_name;
          this.catalog_list.forEach(element => {
            if (element.is_featured == 1) {
              this.featured_catalog_list.push(element);
            }
            else {
              this.normal_catalog_list.push(element);
            }
          });
          this.errorMsg = "";
          this.successMsg = results.message;
          this.loading.close();
        }
        else if (results.code == 400) {
          this.loading.close();
          localStorage.removeItem("photoArtsAdminToken");
          this.router.navigate(['/admin']);
        }
        else if (results.code == 401) {
          this.token = results.data.new_token;
          localStorage.setItem("photoArtsAdminToken", this.token);
          this.getAllCatalogs(this.subCategoryId);
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

  moveToFirst(category) {
    this.loading = this.dialog.open(LoadingComponent);
    this.dataService.postData('setCatalogRankOnTheTopByAdmin', {
      "catalog_id": category.catalog_id
    }, {
        headers: {
          'Authorization': 'Bearer ' + this.token
        }
      }).subscribe(results => {
        if (results.code == 200) {
          this.showSuccess(results.message, false);
          this.getAllCatalogs(this.subCategoryId);
          this.errorMsg = "";
          this.searchErr = "";
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
          this.searchErr = results.message;
        }
      });
  }

  addAppContentViaMigration(category) {
    /* console.log(category); */
    let dialogRef = this.dialog.open(ConfirmActionComponent, { disableClose: true });
    dialogRef.afterClosed().subscribe(result => {
      if (!result) {
        this.loading = this.dialog.open(LoadingComponent);
        this.token = localStorage.getItem('photoArtsAdminToken');
        this.dataService.postData('addAppContentViaMigration',
          {
            "catalog_id": category.catalog_id
          }, {
            headers: {
              'Authorization': 'Bearer ' + this.token
            }
          }).subscribe(results => {
            if (results.code == 200) {
              this.errorMsg = "";
              this.showSuccess(results.message, false);
              this.getAllCatalogs(this.subCategoryId);
            }
            else if (results.code == 400) {
              this.loading.close();
              localStorage.removeItem("photoArtsAdminToken");
              this.router.navigate(['/admin']);
            }
            else if (results.code == 401) {
              this.token = results.data.new_token;
              localStorage.setItem("photoArtsAdminToken", this.token);
              this.getAllCatalogs(this.subCategoryId);
            }
            else {
              this.loading.close();
              this.successMsg = "";
              this.showError(results.message, false);
              this.errorMsg = results.message;
            }
          }, error => {
            /* console.log(error.status); */
            /* console.log(error); */
          });
      }
    });
  }

  viewCatalog(catalog) {
    localStorage.setItem("selected_catalog", JSON.stringify(catalog));
    catalog.name = catalog.name.replace(/ /g, '');
    if (catalog.is_featured == 1 && this.categoryId == 3) {
      this.router.navigate(['/admin/popular/', this.categoryId, this.subCategoryName, this.subCategoryId, catalog.name, catalog.catalog_id]);
    }
    else if (this.categoryId == 4) {
      this.router.navigate(['/admin/fonts/', this.categoryId, this.subCategoryName, this.subCategoryId, catalog.name, catalog.catalog_id]);
    }
    else {
      this.router.navigate(['/admin/categories/', this.categoryId, this.subCategoryName, this.subCategoryId, catalog.name, catalog.catalog_id]);
    }
  }

  addCatalog() {
    let dialogRef = this.dialog.open(CatalogsAddComponent);
    dialogRef.componentInstance.sub_category_id = this.subCategoryId;
    dialogRef.afterClosed().subscribe(result => {
      if (!result) {
        this.getAllCatalogs(this.categoryId);
      }
    });
  }

  updateCatalog(category) {
    let catalog_data = JSON.parse(JSON.stringify(category));
    let dialogRef = this.dialog.open(CatalogsUpdateComponent);
    dialogRef.componentInstance.catalog_data = catalog_data;
    dialogRef.afterClosed().subscribe(result => {
      if (!result) {
        this.getAllCatalogs(this.categoryId);
      }
    });
  }

  deleteCatalog(category) {
    let dialogRef = this.dialog.open(CatalogsDeleteComponent);
    dialogRef.componentInstance.catalog_id = category.catalog_id;
    dialogRef.afterClosed().subscribe(result => {
      if (!result) {
        this.getAllCatalogs(this.categoryId);
      }
    });
  }

  searchData(searchQuery) {
    if (typeof searchQuery == "undefined" || searchQuery == "" || searchQuery == null) {
      this.searchErr = "Please Enter Search Query";
      return false;
    }
    else {
      this.loading = this.dialog.open(LoadingComponent);
      this.dataService.postData('searchCatalogByName', {
        "sub_category_id": this.subCategoryId,
        "name": searchQuery
      }, {
          headers: {
            'Authorization': 'Bearer ' + this.token
          }
        }).subscribe(results => {
          if (results.code == 200) {
            this.featured_catalog_list = [];
            this.normal_catalog_list = [];
            this.catalog_list = results.data.category_list;
            this.total_record = this.catalog_list.length;
            this.sub_category_name = results.data.category_name;
            this.catalog_list.forEach(element => {
              if (element.is_featured == 1) {
                this.featured_catalog_list.push(element);
              }
              else {
                this.normal_catalog_list.push(element);
              }
            });
            this.errorMsg = "";
            this.searchErr = "";
            this.loading.close();
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
            this.searchData(searchQuery);
          }
          else {
            this.loading.close();
            this.searchErr = results.message;
          }
        });
    }
  }

  do_reset() {
    this.loading = this.dialog.open(LoadingComponent);
    this.searchQuery = "";
    this.searchErr = "";
    this.searchTag = "";
    this.currentPage = 1;
    this.getAllCatalogs(this.categoryId);
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
    let tmp_current_path = tmp_selected_catagory.name + " / " + tmp_selected_sub_catagory.name;
    return tmp_current_path;
  }

}

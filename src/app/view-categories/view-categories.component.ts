import { Component, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { MdDialog, MdSnackBar, MdSnackBarConfig } from '@angular/material';
import { DataService } from '../data.service';
import { LoadingComponent } from '../loading/loading.component';
import { AddSubCategoryByCategoryIdComponent } from '../add-sub-category-by-category-id/add-sub-category-by-category-id.component';
import { UpdateSubCategoryByCategoryIdComponent } from '../update-sub-category-by-category-id/update-sub-category-by-category-id.component';
import { DeleteSubCategoryByCategoryIdComponent } from '../delete-sub-category-by-category-id/delete-sub-category-by-category-id.component';
import { ViewSubCatTagsComponent } from '../view-sub-cat-tags/view-sub-cat-tags.component';

@Component({
  templateUrl: './view-categories.component.html'
})
export class ViewCategoriesComponent implements OnInit {

  token: any;
  private sub: any; //route subscriber
  categoryId: any;
  category_list: any[];
  category_name: any;
  errorMsg: any;
  successMsg: any;
  total_record: any;
  itemsPerPageArray: any[];
  itemsPerPage: number = 15;
  currentPage: number = 1;
  showPagination: boolean = true;
  searchArray: any[];
  searchErr: string;
  searchTag: any;
  searchQuery: any;
  loading: any;
  fonts_list: any;
  len:any;

  constructor(public route: ActivatedRoute, private dataService: DataService, private router: Router, public dialog: MdDialog, public snackBar: MdSnackBar) {
    this.loading = this.dialog.open(LoadingComponent);
    this.itemsPerPageArray = [
      { 'itemPerPageValue': '25', 'itemPerPageName': '25' },
      { 'itemPerPageValue': '50', 'itemPerPageName': '50' },
      { 'itemPerPageValue': '75', 'itemPerPageName': '75' },
      { 'itemPerPageValue': '100', 'itemPerPageName': '100' },
    ];
    this.itemsPerPage = this.itemsPerPageArray[3].itemPerPageValue;
  }

  ngOnInit() {
    this.token = localStorage.getItem('photoArtsAdminToken');
    this.sub = this.route.params
      .subscribe(params => {
        this.categoryId = params['categoryId'];
        this.getAllCategories(this.categoryId, this.currentPage, this.itemsPerPage);
      });
  }

  pageChanged(event) {
    this.loading = this.dialog.open(LoadingComponent);
    this.currentPage = event;
    this.getAllCategories(this.categoryId, this.currentPage, this.itemsPerPage);
  }

  itemPerPageChanged(itemsPerPage) {
    this.loading = this.dialog.open(LoadingComponent);
    this.itemsPerPage = itemsPerPage;
    this.getAllCategories(this.categoryId, this.currentPage, this.itemsPerPage);
  }

  getAllCategories(categoryId, currentPage, itemsPerPage) {
    this.token = localStorage.getItem('photoArtsAdminToken');
    this.dataService.postData('getSubCategoryByCategoryId',
      {
        "category_id": categoryId,
        "page": currentPage,
        "item_count": itemsPerPage
      }, {
      headers: {
        'Authorization': 'Bearer ' + this.token
      }
    }).subscribe(results => {
      if (results.code == 200) {
        this.showPagination = true;
        this.category_list = results.data.category_list;
        this.total_record = results.data.total_record;
        this.category_name = results.data.category_name;
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
        this.getAllCategories(this.categoryId, this.currentPage, this.itemsPerPage);
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

  ivkSbCatTagDialog(category: any) {
    localStorage.setItem("selected_sub_category", JSON.stringify(category));
    let dialogRef = this.dialog.open(ViewSubCatTagsComponent, {
      disableClose: true,
      panelClass: 'modal-ttl-sroll',
      data: {
        category_data: category
      }
    });
    dialogRef.afterClosed().subscribe(result => {
      if (!result) {
        this.getAllCategories(this.categoryId, this.currentPage, this.itemsPerPage);
      }
    });
  }

  viewSubCategory(category) {
    localStorage.setItem("selected_sub_category", JSON.stringify(category));
    category.name = category.name.replace(/ /g, '');
    this.router.navigate(['/admin/categories/', this.categoryId, category.name, category.sub_category_id]);
  }

  viewAllUsers(category) {
    localStorage.setItem("selected_sub_category", JSON.stringify(category));
    this.router.navigate(['/admin/users/', category.sub_category_id]);
  }

  viewPremiumUsers(category) {
    localStorage.setItem("selected_sub_category", JSON.stringify(category));
    this.router.navigate(['/admin/purchases/', category.sub_category_id]);
  }

  viewRestoredDevices(category) {
    localStorage.setItem("selected_sub_category", JSON.stringify(category));
    this.router.navigate(['/admin/restores/', category.sub_category_id]);
  }

  sendANotification(category) {
    localStorage.setItem("selected_sub_category", JSON.stringify(category));
    this.router.navigate(['/admin/notification/', category.sub_category_id]);
  }

  viewAdvertisements(category) {
    localStorage.setItem("selected_sub_category", JSON.stringify(category));
    this.router.navigate(['/admin/advertisements/', category.sub_category_id]);
  }

  viewDesignByUsers(category) {
    localStorage.setItem("selected_sub_category", JSON.stringify(category));
    this.router.navigate(['/admin/categories/', this.categoryId, 'user-designs']);
  }

  viewAdmobAds(category) {
    localStorage.setItem("selected_sub_category", JSON.stringify(category));
    this.router.navigate(['/admin/admob-ads/']);
  }

  addSubCategory() {
    let dialogRef = this.dialog.open(AddSubCategoryByCategoryIdComponent);
    dialogRef.componentInstance.category_id = this.categoryId;
    dialogRef.afterClosed().subscribe(result => {
      this.getAllCategories(this.categoryId, this.currentPage, this.itemsPerPage);
    });
  }

  updateSubCategory(category) {
    let sub_category_data = JSON.parse(JSON.stringify(category));
    let dialogRef = this.dialog.open(UpdateSubCategoryByCategoryIdComponent);
    dialogRef.componentInstance.sub_category_data = sub_category_data;
    dialogRef.afterClosed().subscribe(result => {
      this.getAllCategories(this.categoryId, this.currentPage, this.itemsPerPage);
    });
  }

  deleteSubCategory(category) {
    let dialogRef = this.dialog.open(DeleteSubCategoryByCategoryIdComponent);
    dialogRef.componentInstance.sub_category_id = category.sub_category_id;
    dialogRef.afterClosed().subscribe(result => {
      this.getAllCategories(this.categoryId, this.currentPage, this.itemsPerPage);
    });
  }

  searchData(searchQuery) {
    if (typeof searchQuery == "undefined" || searchQuery == "" || searchQuery == null) {
      this.searchErr = "";
      this.showError("Please Enter Search Query", false);
      return false;
    }
    else {
      this.showPagination = false;
      this.loading = this.dialog.open(LoadingComponent);
      this.dataService.postData('searchSubCategoryByName', {
        "name": searchQuery,
        "category_id": this.categoryId
      }, {
        headers: {
          'Authorization': 'Bearer ' + this.token
        }
      }).subscribe(results => {
        if (results.code == 200) {
          this.category_list = results.data.category_list;
          this.total_record = this.category_list.length;
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
          this.searchErr = "";
          this.showError(results.message, false);
        }
      });
    }
  }

  getCategories() {
    this.loading = this.dialog.open(LoadingComponent);
    this.token = localStorage.getItem('photoArtsAdminToken');
    this.dataService.postData('getCorruptedFontList',
      {
        "last_sync_time": 0
      }, {
      headers: {
        'Authorization': 'Bearer ' + this.token
      }
    }).subscribe(results => {
      if (results.code == 200) {
        this.fonts_list = results.data.result;
        this.len = this.fonts_list.length;
        this.loading.close();
      }
      else if (results.code == 400) {
        this.loading.close();
        localStorage.removeItem("videoFlyerAdminToken");
        this.router.navigate(['/admin']);
      }
      else if (results.code == 401) {
        this.token = results.data.new_token;
        localStorage.setItem("videoFlyerAdminToken", this.token);
        this.getAllCategories(this.categoryId, this.currentPage, this.itemsPerPage);
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


  do_reset() {
    this.loading = this.dialog.open(LoadingComponent);
    this.searchQuery = "";
    this.searchErr = "";
    this.searchTag = "";
    this.currentPage = 1;
    this.showPagination = true;
    this.itemsPerPage = this.itemsPerPageArray[3].itemPerPageValue;
    this.getAllCategories(this.categoryId, this.currentPage, this.itemsPerPage);
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

}
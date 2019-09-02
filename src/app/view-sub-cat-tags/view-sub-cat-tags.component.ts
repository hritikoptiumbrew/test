import { Component, OnInit, Renderer, ViewChild, ElementRef, ViewEncapsulation, Inject, OnDestroy } from '@angular/core';
import { MdDialog, MdDialogRef, MD_DIALOG_DATA, MdSnackBar, MdSnackBarConfig } from '@angular/material';
import { Router } from '@angular/router';
import { DataService } from '../data.service';
import { ERROR } from '../app.constants';
import { LoadingComponent } from '../loading/loading.component';
import { DeleteUserGeneratedComponent } from "../delete-user-generated/delete-user-generated.component";

@Component({
  selector: 'app-view-sub-cat-tags',
  templateUrl: './view-sub-cat-tags.component.html',
  styleUrls: ['./view-sub-cat-tags.component.css']
})
export class ViewSubCatTagsComponent implements OnInit {

  category_data: any = {};
  token: any;
  searchTag_list: any = [];
  subCategoryDetails: any = JSON.parse(localStorage.getItem("selected_sub_category"));
  tmp_category_list: any = [];
  new_tag_name: any = "";
  sortByTagName: any;
  order_type: Boolean;
  order_type_val: any;
  errorMsg: any;
  successMsg: any;
  total_record: any;
  loading: any;

  constructor(public dialogRef: MdDialogRef<ViewSubCatTagsComponent>, @Inject(MD_DIALOG_DATA) public data: any, private dataService: DataService, private router: Router, public dialog: MdDialog, public snackBar: MdSnackBar) {
    this.category_data = this.data.category_data;
    this.getAllCategorySearchTags();
  }

  ngOnInit() {
  }

  sortBy(sortByTagName, order_type_val) {
    this.sortByTagName = sortByTagName;
    if (order_type_val == "ASC") {
      this.order_type = false;
      this.order_type_val = "DESC";
    }
    else {
      this.order_type = true;
      this.order_type_val = "ASC";
    }
    this.getAllCategorySearchTags();
  }

  resetRow(category, i) {
    this.searchTag_list[i].tag_name = this.tmp_category_list[i].tag_name;
    category.is_update = false;
    category.tag_name = this.tmp_category_list[i].tag_name;
  }

  validateString(str) {
    var regex = /^[a-z0-9& ]+$/i.test(str);
    return regex;
  }

  showUpdate(search_tag) {
    this.searchTag_list.forEach((element, i) => {
      this.resetRow(element, i);
    });
    search_tag.is_update = true;
  }

  getAllCategorySearchTags() {
    this.loading = this.dialog.open(LoadingComponent);
    this.token = localStorage.getItem('photoArtsAdminToken');
    this.dataService.postData('getCategoryTagBySubCategoryId',
      {
        "sub_category_id": this.category_data.sub_category_id,
        "order_by": this.sortByTagName,
        "order_type": this.order_type_val
      }, {
        headers: {
          'Authorization': 'Bearer ' + this.token
        }
      }).subscribe(results => {
        if (results.code == 200) {
          this.searchTag_list = results.data.result;
          this.tmp_category_list = JSON.parse(JSON.stringify(results.data.result));
          this.searchTag_list.forEach(element => {
            element.is_update = false;
          });
          this.total_record = results.data.total_record;
          this.errorMsg = "";
          // this.successMsg = results.message;
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
          this.getAllCategorySearchTags();
        }
        else {
          this.loading.close();
          this.successMsg = "";
          this.errorMsg = results.message;
        }
      }, error => {
        this.loading.close();
        this.successMsg = "";
        this.errorMsg = ERROR.SERVER_ERR;
        /* console.log(error.status); */
        /* console.log(error); */
      });
  }

  addSearchTag(new_tag_name) {
    this.token = localStorage.getItem('photoArtsAdminToken');
    if (typeof new_tag_name == "undefined" || new_tag_name.trim() == "" || new_tag_name == null) {
      this.showError("Please enter tag name", false);
      return false;
    }
    /* else if (!this.validateString(new_tag_name)) {
      this.showError("Special characters not allowed, only alphanumeric, '&' is allowed in tag name.", false);
      return false;
    } */
    else {
      this.loading = this.dialog.open(LoadingComponent);
      this.dataService.postData('addSearchCategoryTag', {
        "tag_name": new_tag_name,
        "sub_category_id": this.subCategoryDetails.sub_category_id
      },
        {
          headers: {
            'Authorization': 'Bearer ' + this.token
          }
        }).subscribe((results: any) => {
          if (results.code == 200) {
            this.showSuccess(results.message, false);
            this.new_tag_name = "";
            this.loading.close();
            this.getAllCategorySearchTags();
          }
          else if (results.code == 400) {
            this.loading.close();
            localStorage.removeItem("photoArtsAdminToken");
            this.router.navigate(['/admin']);
          }
          else if (results.code == 401) {
            this.token = results.data.new_token;
            localStorage.setItem("photoArtsAdminToken", this.token);
            this.updateSearchTag(new_tag_name);
          }
          else {
            this.loading.close();
            this.successMsg = "";
            this.errorMsg = results.message;
          }
        }, (error: any) => {
          this.loading.close();
          this.successMsg = "";
          this.errorMsg = ERROR.SERVER_ERR;
          /* console.log(error.status); */
          /* console.log(error); */
        });
    }
  }

  updateSearchTag(search_tag) {
    this.token = localStorage.getItem('photoArtsAdminToken');
    if (typeof search_tag.tag_name == "undefined" || search_tag.tag_name.trim() == "" || search_tag.tag_name == null) {
      this.showError("Please enter tag name", false);
      return false;
    }
    /* else if (!this.validateString(search_tag.tag_name)) {
      this.showError("Special characters not allowed, only alphanumeric, '&' is allowed in tag name.", false);
      return false;
    } */
    else {
      this.loading = this.dialog.open(LoadingComponent);
      this.dataService.postData('updateSearchCategoryTag', {
        "tag_name": search_tag.tag_name,
        "sub_category_tag_id": search_tag.sub_category_tag_id,
        "sub_category_id": this.subCategoryDetails.sub_category_id
      },
        {
          headers: {
            'Authorization': 'Bearer ' + this.token
          }
        }).subscribe((results: any) => {
          if (results.code == 200) {
            this.showSuccess(results.message, false);
            this.loading.close();
            this.getAllCategorySearchTags();
          }
          else if (results.code == 400) {
            this.loading.close();
            localStorage.removeItem("photoArtsAdminToken");
            this.router.navigate(['/admin']);
          }
          else if (results.code == 401) {
            this.token = results.data.new_token;
            localStorage.setItem("photoArtsAdminToken", this.token);
            this.updateSearchTag(search_tag);
          }
          else {
            this.loading.close();
            this.successMsg = "";
            this.errorMsg = results.message;
          }
        }, (error: any) => {
          this.loading.close();
          this.successMsg = "";
          this.errorMsg = ERROR.SERVER_ERR;
          /* console.log(error.status); */
          /* console.log(error); */
        });
    }
  }

  moveToFirst(search_tag) {
    this.loading = this.dialog.open(LoadingComponent);
    this.dataService.postData('setCategoryTagRankOnTheTopByAdmin', {
      "sub_category_tag_id": search_tag.sub_category_tag_id
    }, {
        headers: {
          'Authorization': 'Bearer ' + this.token
        }
      }).subscribe(results => {
        if (results.code == 200) {
          this.showSuccess(results.message, false);
          this.errorMsg = "";
          this.loading.close();
          this.getAllCategorySearchTags();
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
          this.moveToFirst(search_tag);
        }
        else {
          this.loading.close();
          this.showError(results.message, false);
        }
      }, (error: any) => {
        this.loading.close();
        this.successMsg = "";
        this.errorMsg = ERROR.SERVER_ERR;
      });
  }

  deleteTag(search_tag, API_NAME) {
    let tmp_request_data = {
      "sub_category_tag_id": search_tag.sub_category_tag_id
    };
    let dialogRef = this.dialog.open(DeleteUserGeneratedComponent);
    dialogRef.componentInstance.delete_request_data = tmp_request_data;
    dialogRef.componentInstance.API_NAME = API_NAME;
    dialogRef.afterClosed().subscribe(result => {
      if (!result) {
        this.getAllCategorySearchTags();
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

}

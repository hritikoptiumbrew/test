import { Component, OnInit } from '@angular/core';
import { MdDialog, MdSnackBar, MdSnackBarConfig } from '@angular/material';
import { Router } from '@angular/router';
import { DataService } from '../data.service';
import { LoadingComponent } from '../loading/loading.component';
import { DeleteUserGeneratedComponent } from '../delete-user-generated/delete-user-generated.component';

@Component({
  selector: 'app-search-tags',
  templateUrl: './search-tags.component.html',
  styleUrls: ['./search-tags.component.css']
})
export class SearchTagsComponent implements OnInit {

  token: any;
  successMsg: any;
  errorMsg: any;
  search_tag_list: any;
  total_record: any;
  itemsPerPage: number = 15;
  currentPage: number = 1;
  searchArray: any[];
  searchErr: string;
  searchTag: any;
  searchQuery: any;
  loading: any;
  itemsPerPageArray: any[];
  sortByTagName: any;
  order_type: Boolean;
  order_type_val: any;
  current_path: any = "";
  tag_name: any = "";

  constructor(private dataService: DataService, private router: Router, public dialog: MdDialog, public snackBar: MdSnackBar) {
    this.token = localStorage.getItem('photoArtsAdminToken');
    this.searchArray = [
      { 'searchTagValue': '', 'searchTagName': 'Search Type' },
      { 'searchTagValue': 'promo_code', 'searchTagName': 'Promo code' },
      { 'searchTagValue': 'package_name', 'searchTagName': 'Package name' },
      { 'searchTagValue': 'device_udid', 'searchTagName': 'Device UDID' },
      { 'searchTagValue': 'create_time', 'searchTagName': 'Create time' }
    ];
    this.searchTag = this.searchArray[0].searchTagValue;

    this.itemsPerPageArray = [
      { 'itemPerPageValue': '15', 'itemPerPageName': '15' },
      { 'itemPerPageValue': '30', 'itemPerPageName': '30' },
      { 'itemPerPageValue': '45', 'itemPerPageName': '45' },
      { 'itemPerPageValue': '60', 'itemPerPageName': '60' },
      { 'itemPerPageValue': '75', 'itemPerPageName': '75' },
      { 'itemPerPageValue': '90', 'itemPerPageName': '90' },
      { 'itemPerPageValue': '100', 'itemPerPageName': '100' }
    ];
    this.itemsPerPage = this.itemsPerPageArray[0].itemPerPageValue;
    this.getAllSearchTags();
  }

  ngOnInit() {
    this.token = localStorage.getItem('photoArtsAdminToken');
  }

  itemPerPageChanged(itemsPerPage) {
    this.itemsPerPage = itemsPerPage;
    this.getAllSearchTags();
  }

  pageChanged(event) {
    this.currentPage = event;
    this.getAllSearchTags();
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
    this.getAllSearchTags();
  }

  getAllSearchTags() {
    this.loading = this.dialog.open(LoadingComponent);
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
          this.search_tag_list.forEach(element => {
            element.is_update = false;
          });
          this.total_record = results.data.total_record;
          this.loading.close();
          this.errorMsg = "";
          this.successMsg = results.message;
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

  addTag(tag_name) {
    /* console.log(tag_name); */
    if (typeof tag_name == "undefined" || tag_name.trim() == "" || tag_name == null) {
      this.showError("Please enter tag name", false);
      return false;
    }
    else if (!this.validateString(tag_name)) {
      this.showError("Special characters not allowed, only alphanumeric, '&' is allowed in tag name.", false);
      return false;
    }
    else {
      this.dataService.postData('addTag', {
        "tag_name": tag_name
      },
        {
          headers: {
            'Authorization': 'Bearer ' + this.token
          }
        }).subscribe(results => {
          if (results.code == 200) {
            this.showSuccess(results.message, false);
            this.tag_name = "";
            this.do_reset();
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
            this.addTag(tag_name);
          }
          else {
            this.loading.close();
            this.showError(results.message, false);
          }
        });
    }
  }

  validateString(str) {
    var regex = /^[a-z0-9& ]+$/i.test(str);
    return regex;
  }

  showUpdate(search_tag) {
    search_tag.is_update = true;
  }

  updateSeatchTag(search_tag) {
    /* console.log(search_tag); */
    if (typeof search_tag.tag_name == "undefined" || search_tag.tag_name.trim() == "" || search_tag.tag_name == null) {
      this.showError("Please enter tag name", false);
      return false;
    }
    else if (!this.validateString(search_tag.tag_name)) {
      this.showError("Special characters not allowed, only alphanumeric, '&' is allowed in tag name.", false);
      return false;
    }
    else {
      this.dataService.postData('updateTag', {
        "tag_name": search_tag.tag_name,
        "tag_id": search_tag.tag_id
      },
        {
          headers: {
            'Authorization': 'Bearer ' + this.token
          }
        }).subscribe(results => {
          if (results.code == 200) {
            this.showSuccess(results.message, false);
            this.do_reset();
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
            this.updateSeatchTag(search_tag);
          }
          else {
            this.loading.close();
            this.showError(results.message, false);
          }
        });
    }
  }

  deleteTag(search_tag) {
    let tmp_request_data = {
      "tag_id": search_tag.tag_id
    };
    let dialogRef = this.dialog.open(DeleteUserGeneratedComponent);
    dialogRef.componentInstance.delete_request_data = tmp_request_data;
    dialogRef.componentInstance.API_NAME = "deleteTag";
    dialogRef.afterClosed().subscribe(result => {
      this.do_reset();
    });
  }

  do_reset() {
    this.searchQuery = "";
    this.searchErr = "";
    this.searchTag = "";
    this.currentPage = 1;
    this.sortByTagName = null;
    this.order_type_val = null;
    this.itemsPerPage = this.itemsPerPageArray[0].itemPerPageValue;
    this.getAllSearchTags();
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

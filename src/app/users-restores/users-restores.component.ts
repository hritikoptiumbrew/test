import { Component, OnInit } from '@angular/core';
import { MdDialog, MdDialogRef } from '@angular/material';
import { Router, ActivatedRoute } from '@angular/router';
import { Http, RequestOptions, Headers, Response, RequestMethod, RequestOptionsArgs } from '@angular/http';
import { Observable } from 'rxjs/Rx';
import { DataService } from '../data.service';
import { HOST } from '../app.constants';
import { LoadingComponent } from '../loading/loading.component';

@Component({
  templateUrl: './users-restores.component.html'
})
export class UsersRestoresComponent implements OnInit {

  token: any;
  successMsg: any;
  errorMsg: any;
  list_device: any;
  private sub: any; //route subscriber
  private sub_category_id: any;
  total_record: any;
  itemsPerPageArray: any[];
  itemsPerPage: any;
  sortByTagName: any;
  order_type: Boolean;
  order_type_val: any;
  currentPage: number = 1;
  searchArray: any[];
  searchTag: any;
  searchQuery: any;
  loading: any;
  searchErr: any;
  current_path: any = "";

  constructor(public route: ActivatedRoute, private dataService: DataService, private router: Router, public dialog: MdDialog) {
    this.loading = this.dialog.open(LoadingComponent);
    this.searchArray = [
      { 'searchTagValue': '', 'searchTagName': 'Search Type' },
      { 'searchTagValue': 'order_number', 'searchTagName': 'Order Number' },
      { 'searchTagValue': 'restore', 'searchTagName': 'Restore Count' },
      { 'searchTagValue': 'create_time', 'searchTagName': 'Created Time' },
      { 'searchTagValue': 'update_time', 'searchTagName': 'Updated Time' }
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
  }

  ngOnInit() {
    this.token = localStorage.getItem('photoArtsAdminToken');
    this.current_path = this.getLocalStorageData();
    this.sub = this.route.params
      .subscribe(params => {
        this.sub_category_id = params['sub_category_id'];
        this.getAllRestoredUsers(this.sub_category_id, this.currentPage, this.itemsPerPage, this.sortByTagName, this.order_type_val);
      });
  }

  pageChanged(event) {
    this.loading = this.dialog.open(LoadingComponent);
    this.currentPage = event;
    this.getAllRestoredUsers(this.sub_category_id, this.currentPage, this.itemsPerPage, this.sortByTagName, this.order_type_val);
  }

  itemPerPageChanged(itemsPerPage) {
    this.loading = this.dialog.open(LoadingComponent);
    this.itemsPerPage = itemsPerPage;
    this.getAllRestoredUsers(this.sub_category_id, this.currentPage, this.itemsPerPage, this.sortByTagName, this.order_type_val);
  }

  sortBy(sortByTagName, order_type_val) {
    this.loading = this.dialog.open(LoadingComponent);
    this.sortByTagName = sortByTagName;
    if (order_type_val == "ASC") {
      this.order_type = false;
      this.order_type_val = "DESC";
    }
    else {
      this.order_type = true;
      this.order_type_val = "ASC";
    }
    this.getAllRestoredUsers(this.sub_category_id, this.currentPage, this.itemsPerPage, this.sortByTagName, this.order_type_val);
  }

  searchTagChange(event) {
    this.searchQuery = "";
  }

  getAllRestoredUsers(sub_category_id, currentPage, itemsPerPage, sortByTagName, order_type_val) {
    this.dataService.postData('getAllRestoreDevice',
      {
        "sub_category_id": sub_category_id,
        "item_count": itemsPerPage,
        "page": currentPage,
        "order_by": sortByTagName,
        "order_type": order_type_val
      }, {
        headers: {
          'Authorization': 'Bearer ' + this.token
        }
      }).subscribe(results => {
        if (results.code == 200) {
          this.list_device = results.data.list_device;
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
          this.getAllRestoredUsers(this.sub_category_id, this.currentPage, this.itemsPerPage, this.sortByTagName, this.order_type_val);
        }
        else {
          this.successMsg = "";
          this.errorMsg = results.message;
          this.loading.close();
        }
      }, error => {
        /* console.log(error.status); */
        /* console.log(error); */
      });
  }

  searchData(searchQuery, searchTag) {
    if (typeof searchTag == "undefined" || searchTag == "" || searchTag == null) {
      this.searchErr = "Please Select Search Type";
      return false;
    }
    if (typeof searchQuery == "undefined" || searchQuery == "" || searchQuery == null) {
      this.searchErr = "Please Enter Search Query";
      return false;
    }
    else {
      this.loading = this.dialog.open(LoadingComponent);
      this.dataService.postData('searchRestoreDevice', {
        "search_type": searchTag,
        "search_query": searchQuery
      }, {
          headers: {
            'Authorization': 'Bearer ' + this.token
          }
        }).subscribe(results => {
          if (results.code == 200) {
            this.list_device = results.data.list_device;
            this.total_record = this.list_device.length;
            this.itemsPerPage = this.list_device.length;
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
            this.loading.close();
            localStorage.setItem("photoArtsAdminToken", this.token);
            this.searchData(searchQuery, searchTag);
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
    this.sortByTagName = null;
    this.order_type_val = null;
    this.itemsPerPage = this.itemsPerPageArray[0].itemPerPageValue;
    this.getAllRestoredUsers(this.sub_category_id, this.currentPage, this.itemsPerPage, this.sortByTagName, this.order_type_val);
  }

  getLocalStorageData() {
    let tmp_selected_catagory = JSON.parse(localStorage.getItem("selected_catagory"));
    let tmp_selected_sub_catagory = JSON.parse(localStorage.getItem("selected_sub_catagory"));
    let tmp_current_path = tmp_selected_catagory.name + " / " + tmp_selected_sub_catagory.name;
    return tmp_current_path;
  }

}

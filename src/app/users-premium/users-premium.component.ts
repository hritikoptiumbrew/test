import { Component, OnInit } from '@angular/core';
import { MdDialog } from '@angular/material';
import { Router, ActivatedRoute } from '@angular/router';
import { DataService } from '../data.service';
import { LoadingComponent } from '../loading/loading.component';

@Component({
  templateUrl: './users-premium.component.html'
})
export class UsersPremiumComponent implements OnInit {

  token: any;
  private sub: any; //route subscriber
  private sub_category_id: any;
  successMsg: any;
  errorMsg: any;
  user_list: any;
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
  searchErr: string;
  current_path: any = "";

  constructor(public route: ActivatedRoute, private dataService: DataService, private router: Router, public dialog: MdDialog) {
    this.loading = this.dialog.open(LoadingComponent);
    this.searchArray = [
      { 'searchTagValue': '', 'searchTagName': 'Search Type' },
      { 'searchTagValue': 'order_number', 'searchTagName': 'Order Number' },
      { 'searchTagValue': 'currency_code', 'searchTagName': 'Currency Code' },
      { 'searchTagValue': 'device_platform', 'searchTagName': 'Platform' },
      { 'searchTagValue': 'tot_order_amount', 'searchTagName': 'Order Amount' },
      { 'searchTagValue': 'create_time', 'searchTagName': 'Created Time' }
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
        this.getAllPremiumUsers(this.sub_category_id, this.currentPage, this.itemsPerPage, this.sortByTagName, this.order_type_val);
      });
  }

  pageChanged(event) {
    this.loading = this.dialog.open(LoadingComponent);
    this.currentPage = event;
    this.getAllPremiumUsers(this.sub_category_id, this.currentPage, this.itemsPerPage, this.sortByTagName, this.order_type_val);
  }

  itemPerPageChanged(itemsPerPage) {
    this.loading = this.dialog.open(LoadingComponent);
    this.itemsPerPage = itemsPerPage;
    this.getAllPremiumUsers(this.sub_category_id, this.currentPage, this.itemsPerPage, this.sortByTagName, this.order_type_val);
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
    this.getAllPremiumUsers(this.sub_category_id, this.currentPage, this.itemsPerPage, this.sortByTagName, this.order_type_val);
  }

  getAllPremiumUsers(sub_category_id, currentPage, itemsPerPage, sortByTagName, order_type_val) {
    this.dataService.postData('getPurchaseUser',
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
          this.user_list = results.data.list_user;
          this.total_record = results.data.total_record;
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
          this.getAllPremiumUsers(this.sub_category_id, this.currentPage, this.itemsPerPage, this.sortByTagName, this.order_type_val);
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
      this.dataService.postData('searchPurchaseUser', {
        "search_type": searchTag,
        "search_query": searchQuery
      }, {
          headers: {
            'Authorization': 'Bearer ' + this.token
          }
        }).subscribe(results => {
          if (results.code == 200) {
            this.user_list = results.data.list_user;
            this.total_record = this.user_list.length;
            this.itemsPerPage = this.user_list.length;
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
    this.getAllPremiumUsers(this.sub_category_id, this.currentPage, this.itemsPerPage, this.sortByTagName, this.order_type_val);
  }

  getLocalStorageData() {
    let tmp_selected_category = JSON.parse(localStorage.getItem("selected_category"));
    let tmp_selected_sub_category = JSON.parse(localStorage.getItem("selected_sub_category"));
    let tmp_current_path = tmp_selected_category.name + " / " + tmp_selected_sub_category.name;
    return tmp_current_path;
  }

}

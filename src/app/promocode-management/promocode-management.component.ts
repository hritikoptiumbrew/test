import { Component, OnInit } from '@angular/core';
import { MdDialog, MdDialogRef } from '@angular/material';
import { Router, ActivatedRoute } from '@angular/router';
import { DataService } from '../data.service';
import { HOST } from '../app.constants';
import { LoadingComponent } from '../loading/loading.component';
import { PromocodeAddComponent } from '../promocode-add/promocode-add.component';
import * as moment from 'moment';

@Component({
  selector: 'app-promocode-management',
  templateUrl: './promocode-management.component.html',
  styleUrls: ['./promocode-management.component.css']
})
export class PromocodeManagementComponent implements OnInit {

  token: any;
  successMsg: any;
  errorMsg: any;
  promocode_list: any;
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

  constructor(private dataService: DataService, private router: Router, public dialog: MdDialog) {
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
    this.getAllPromoCode(this.currentPage, this.itemsPerPage, this.sortByTagName, this.order_type_val);
  }

  ngOnInit() {
    this.token = localStorage.getItem('photoArtsAdminToken');
  }

  itemPerPageChanged(itemsPerPage) {
    this.itemsPerPage = itemsPerPage;
    this.getAllPromoCode(this.currentPage, this.itemsPerPage, this.sortByTagName, this.order_type_val);
  }

  pageChanged(event) {
    this.currentPage = event;
    this.getAllPromoCode(this.currentPage, this.itemsPerPage, this.sortByTagName, this.order_type_val);
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
    this.getAllPromoCode(this.currentPage, this.itemsPerPage, this.sortByTagName, this.order_type_val);
  }

  getAllPromoCode(currentPage, itemsPerPage, sortByTagName, order_type_val) {
    this.loading = this.dialog.open(LoadingComponent);
    this.token = localStorage.getItem('photoArtsAdminToken');
    this.dataService.postData('getAllPromoCode',
      {
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
          this.promocode_list = results.data.result;
          this.promocode_list.forEach(element => {
            let serverDate = moment.utc(element.create_time).toDate();
            element.create_time = moment(serverDate).format('YYYY-MM-DD hh:mm:ss A');
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
          this.getAllPromoCode(currentPage, itemsPerPage, sortByTagName, order_type_val);
        }
        else {
          this.loading.close();
          this.successMsg = "";
          this.errorMsg = results.message;
        }
      }, error => {
        console.log(error.status);
        console.log(error);
      });
  }

  addCategory() {
    let dialogRef = this.dialog.open(PromocodeAddComponent);
    dialogRef.afterClosed().subscribe(result => {
      if (!result) {
        this.do_reset();
      }
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
      this.dataService.postData('searchPromoCode', {
        "search_type": searchTag,
        "search_query": searchQuery
      }, {
          headers: {
            'Authorization': 'Bearer ' + this.token
          }
        }).subscribe(results => {
          if (results.code == 200) {
            this.promocode_list = results.data.result;
            this.total_record = this.promocode_list.length;
            this.itemsPerPage = this.promocode_list.length;
            this.promocode_list.forEach(element => {
              let serverDate = moment.utc(element.create_time).toDate();
              element.create_time = moment(serverDate).format('YYYY-MM-DD hh:mm:ss A');
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
    this.searchQuery = "";
    this.searchErr = "";
    this.searchTag = "";
    this.currentPage = 1;
    this.sortByTagName = null;
    this.order_type_val = null;
    this.itemsPerPage = this.itemsPerPageArray[0].itemPerPageValue;
    this.getAllPromoCode(this.currentPage, this.itemsPerPage, this.sortByTagName, this.order_type_val);
  }

}

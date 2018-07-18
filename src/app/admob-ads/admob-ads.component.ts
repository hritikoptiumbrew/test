import { Component, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { MdDialog, MdDialogRef } from '@angular/material';
import { DataService } from '../data.service';
import { LoadingComponent } from '../loading/loading.component';
import { GaAdsByCategoryComponent } from '../ga-ads-by-category/ga-ads-by-category.component';
import * as moment from 'moment';


@Component({
  selector: 'app-admob-ads',
  templateUrl: './admob-ads.component.html',
  styleUrls: ['./admob-ads.component.css']
})
export class AdmobAdsComponent implements OnInit {

  token: any;
  ga_admob_list: any[];
  sub_category_name: any;
  itemsPerPageArray: any[];
  itemsPerPage: number = 15;
  currentPage: number = 1;
  showPagination: boolean = true;
  selected_sub_category: any = JSON.parse(localStorage.getItem("selected_sub_catagory"));
  catalogName: any;
  errorMsg: any;
  successMsg: any;
  total_record: any;
  loading: any;
  current_path: any = "";


  constructor(public route: ActivatedRoute, private dataService: DataService, private router: Router, public dialog: MdDialog) {
    this.itemsPerPageArray = [
      { 'itemPerPageValue': '15', 'itemPerPageName': '15' },
      { 'itemPerPageValue': '25', 'itemPerPageName': '25' },
      { 'itemPerPageValue': '50', 'itemPerPageName': '50' },
      { 'itemPerPageValue': '75', 'itemPerPageName': '75' },
      { 'itemPerPageValue': '100', 'itemPerPageName': '100' },
    ];
    this.itemsPerPage = this.itemsPerPageArray[0].itemPerPageValue;
    this.loading = this.dialog.open(LoadingComponent);
    this.token = localStorage.getItem('photoArtsAdminToken');
    this.current_path = this.getLocalStorageData();
    this.getAllCategories(this.selected_sub_category, this.currentPage, this.itemsPerPage);
  }

  ngOnInit() {
  }

  pageChanged(event) {
    this.loading = this.dialog.open(LoadingComponent);
    this.currentPage = event;
    this.getAllCategories(this.selected_sub_category, this.currentPage, this.itemsPerPage);
  }

  itemPerPageChanged(itemsPerPage) {
    this.loading = this.dialog.open(LoadingComponent);
    this.itemsPerPage = itemsPerPage;
    this.getAllCategories(this.selected_sub_category, this.currentPage, this.itemsPerPage);
  }

  getAllCategories(selected_sub_category, currentPage, itemsPerPage) {
    this.token = localStorage.getItem('photoArtsAdminToken');
    this.dataService.postData('getAdvertiseServerIdForAdmin',
      {
        "sub_category_id": selected_sub_category.sub_category_id
      }, {
        headers: {
          'Authorization': 'Bearer ' + this.token
        }
      }).subscribe(results => {
        if (results.code == 200) {
          this.ga_admob_list = results.data.result;
          this.total_record = this.ga_admob_list.length;
          this.ga_admob_list.forEach(element => {
            let serverDate = moment.utc(element.create_time).toDate();
            element.create_time = moment(serverDate).format('YYYY-MM-DD hh:mm:ss A');
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
          this.getAllCategories(this.selected_sub_category, this.currentPage, this.itemsPerPage);
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

  getLocalStorageData() {
    let tmp_selected_catagory = JSON.parse(localStorage.getItem("selected_catagory"));
    let tmp_selected_sub_catagory = JSON.parse(localStorage.getItem("selected_sub_catagory"));
    let tmp_current_path = tmp_selected_catagory.name + " / " + tmp_selected_sub_catagory.name + " / admob-ads";
    return tmp_current_path;
  }

  viewCategory(category) {
    console.log(category);
    localStorage.setItem("selected_admob_catagory", JSON.stringify(category));
    /* this.router.navigate(['/admin/admob-ads/', tmp_url_data]); */
    let dialogRef = this.dialog.open(GaAdsByCategoryComponent);
    dialogRef.componentInstance.ad_category_data = JSON.parse(JSON.stringify(category));
    dialogRef.afterClosed().subscribe(result => {
      this.loading = this.dialog.open(LoadingComponent, { disableClose: true });
      this.getAllCategories(this.selected_sub_category, this.currentPage, this.itemsPerPage);
    });
  }

}

import { Component, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { MdDialog } from '@angular/material';
import { DataService } from '../data.service';
import { LoadingComponent } from '../loading/loading.component';
import { DeleteUserGeneratedComponent } from '../delete-user-generated/delete-user-generated.component';

@Component({
  selector: 'app-user-generated-designs',
  templateUrl: './user-generated-designs.component.html',
  styleUrls: ['./user-generated-designs.component.css']
})
export class UserGeneratedDesignsComponent implements OnInit {

  token: any;
  private sub: any; //route subscriber
  private categoryId: any;
  subCategoryName: any;
  private catalogId: any;
  sub_category_list: any[];
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
    this.sub = this.route.params
      .subscribe(params => {
        this.subCategoryName = params['subCategoryName'];
        this.catalogId = params['catalogId'];
        this.catalogName = params['catalogName'];
        this.categoryId = params['categoryId'];
        this.getAllCategories(this.selected_sub_category, this.currentPage, this.itemsPerPage);
      });
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
    this.dataService.postData('getUserFeedsBySubCategoryId',
      {
        "sub_category_id": selected_sub_category.sub_category_id,
        "page": currentPage,
        "item_count": itemsPerPage
      }, {
        headers: {
          'Authorization': 'Bearer ' + this.token
        }
      }).subscribe(results => {
        if (results.code == 200) {
          this.sub_category_list = results.data.result;
          this.total_record = results.data.total_record;
          this.sub_category_name = results.data.category_name;
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
        /* console.log(error.status); */
        /* console.log(error); */
      });
  }

  getLocalStorageData() {
    let tmp_selected_catagory = JSON.parse(localStorage.getItem("selected_catagory"));
    let tmp_selected_sub_catagory = JSON.parse(localStorage.getItem("selected_sub_catagory"));
    let tmp_selected_catalog = JSON.parse(localStorage.getItem("selected_catalog"));
    let tmp_current_path = tmp_selected_catagory.name + " / " + tmp_selected_sub_catagory.name + " / User Generated Designs";
    return tmp_current_path;
  }

  deleteUserFeeds(category, API_NAME) {
    /* console.log(category.user_feeds_id); */
    let tmp_request_data = {
      "user_feeds_id": category.user_feeds_id
    };
    let dialogRef = this.dialog.open(DeleteUserGeneratedComponent);
    dialogRef.componentInstance.delete_request_data = tmp_request_data;
    dialogRef.componentInstance.API_NAME = API_NAME;
    dialogRef.afterClosed().subscribe(result => {
      this.getAllCategories(this.selected_sub_category, this.currentPage, this.itemsPerPage);
    });
  }

  deleteAllUserFeeds(API_NAME) {
    let tmp_request_data = {
      "sub_category_id": this.selected_sub_category.sub_category_id
    };
    let dialogRef = this.dialog.open(DeleteUserGeneratedComponent);
    dialogRef.componentInstance.delete_request_data = tmp_request_data;
    dialogRef.componentInstance.API_NAME = API_NAME;
    dialogRef.afterClosed().subscribe(result => {
      this.getAllCategories(this.selected_sub_category, this.currentPage, this.itemsPerPage);
    });
  }

}

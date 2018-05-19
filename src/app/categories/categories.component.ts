import { Component, OnInit } from '@angular/core';
import { MdDialog, MdDialogRef } from '@angular/material';
import { Router, ActivatedRoute } from '@angular/router';
import { DataService } from '../data.service';
import { HOST } from '../app.constants';
import { LoadingComponent } from '../loading/loading.component';
import { AddCategoryComponent } from '../add-category/add-category.component';
import { UpdateCategoryComponent } from '../update-category/update-category.component';
import { DeleteCategoryComponent } from '../delete-category/delete-category.component';

@Component({
  templateUrl: './categories.component.html'
})
export class CategoriesComponent implements OnInit {

  token: any;
  successMsg: any;
  errorMsg: any;
  category_list: any;
  total_record: any;
  itemsPerPage: number = 15;
  currentPage: number = 1;
  searchArray: any[];
  searchErr: string;
  searchTag: any;
  searchQuery: any;
  loading: any;

  constructor(private dataService: DataService, private router: Router, public dialog: MdDialog) {
    this.loading = this.dialog.open(LoadingComponent);
  }

  ngOnInit() {
    this.token = localStorage.getItem('photoArtsAdminToken');
    this.getAllBackgroundCatogory(this.currentPage);
  }
  getAllBackgroundCatogory(currentPage) {
    this.token = localStorage.getItem('photoArtsAdminToken');
    this.dataService.postData('getAllCategory',
      {
        "page": currentPage
      }, {
        headers: {
          'Authorization': 'Bearer ' + this.token
        }
      }).subscribe(results => {
        if (results.code == 200) {
          this.category_list = results.data.category_list;
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
          this.getAllBackgroundCatogory(currentPage);
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

  viewCategory(category) {
    localStorage.setItem("selected_catagory", JSON.stringify(category));
    this.router.navigate(['/admin/categories/', category.category_id]);
  }

  addCategory() {
    let dialogRef = this.dialog.open(AddCategoryComponent);
    dialogRef.afterClosed().subscribe(result => {
      this.getAllBackgroundCatogory(this.currentPage);
    });
  }

  updateCategory(category) {
    let category_data = JSON.parse(JSON.stringify(category));
    let dialogRef = this.dialog.open(UpdateCategoryComponent);
    dialogRef.componentInstance.category_data = category_data;
    dialogRef.afterClosed().subscribe(result => {
      this.getAllBackgroundCatogory(this.currentPage);
    });
  }

  deleteCategory(category) {
    let dialogRef = this.dialog.open(DeleteCategoryComponent);
    dialogRef.componentInstance.category_id = category.category_id;
    dialogRef.afterClosed().subscribe(result => {
      this.getAllBackgroundCatogory(this.currentPage);
    });
  }

  pageChanged(event) {
    this.loading = this.dialog.open(LoadingComponent);
    this.currentPage = event;
    this.getAllBackgroundCatogory(this.currentPage);
  }

  itemPerPageChanged(itemsPerPage) {
    /* console.log(itemsPerPage); */
  }

  searchData(searchQuery) {
    if (typeof searchQuery == "undefined" || searchQuery == "" || searchQuery == null) {
      this.searchErr = "Please Enter Search Query";
      return false;
    }
    else {
      this.loading = this.dialog.open(LoadingComponent);
      this.dataService.postData('searchCategoryByName', {
        "name": searchQuery
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
    this.getAllBackgroundCatogory(this.currentPage);
  }

}

import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { DataService } from 'app/data.service';
import { LoadingComponent } from 'app/loading/loading.component';
import { MdDialog, MdSnackBar, MdSnackBarConfig } from '@angular/material';
import { DeleteUserGeneratedComponent } from '../delete-user-generated/delete-user-generated.component';
import { AddOrUpdateBlogComponent } from 'app/add-or-update-blog/add-or-update-blog.component';
import { ERROR } from 'app/app.constants';

@Component({
  selector: 'app-blog-list',
  templateUrl: './blog-list.component.html',
  styleUrls: ['./blog-list.component.css']
})
export class BlogListComponent implements OnInit {
  token: any;
  blog_list: any;
  total_record: any;
  errorMsg: any;
  successMsg: any;
  itemsPerPage: any = 25;
  currentPage: number = 1;
  loading: any;
  itemsPerPageArray: any[];
  showPagination: boolean = true;

  constructor(private router: Router, private dataService: DataService, public dialog: MdDialog, public snackBar: MdSnackBar) {
    this.loading = this.dialog.open(LoadingComponent);
    this.getAllBlogs();
    this.itemsPerPageArray = [
      { 'itemPerPageValue': '25', 'itemPerPageName': '25' },
      { 'itemPerPageValue': '50', 'itemPerPageName': '50' },
      { 'itemPerPageValue': '75', 'itemPerPageName': '75' },
      { 'itemPerPageValue': '100', 'itemPerPageName': '100' },
    ];
    this.itemsPerPage = this.itemsPerPageArray[3].itemPerPageValue;
    this.itemsPerPage = this.itemsPerPageArray[0].itemPerPageValue;
  }

  ngOnInit() {

  }

  pageChanged(event) {
    this.currentPage = event;
    this.loading = this.dialog.open(LoadingComponent);
    this.getAllBlogs();
  }

  itemPerPageChanged(itemsPerPage) {
    this.itemsPerPage = itemsPerPage;
    this.loading = this.dialog.open(LoadingComponent);
    this.getAllBlogs();
  }

  getAllBlogs() {
    this.token = localStorage.getItem('photoArtsAdminToken');
    this.dataService.postData('getBlogContent',
      {
        "page": this.currentPage,
        "item_count": this.itemsPerPage
      }, {
      headers: {
        'Authorization': 'Bearer ' + this.token
      }
    }).subscribe(results => {
      if (results.code == 200) {
        this.loading.close();
        this.showPagination = true;
        this.blog_list = results.data.result;
        this.total_record = results.data.total_record;
      }
      else if (results.code == 400) {
        this.loading.close();
        localStorage.removeItem("photoArtsAdminToken");
        this.router.navigate(['/admin']);
      }
      else if (results.code == 401) {
        this.token = results.data.new_token;
        localStorage.setItem("photoArtsAdminToken", this.token);
        this.getAllBlogs();
      }
      else {
        this.loading.close();
        this.successMsg = "";
        this.errorMsg = results.message;

      }
    }, (error: any) => {
      this.loading.close();
      this.successMsg = "";
      this.showError(ERROR.SERVER_ERR, false);
    });
  }

  moveToFirst(category) {
    this.loading = this.dialog.open(LoadingComponent);
    this.dataService.postData('setBlogRankOnTheTopByAdmin', {
      "blog_id": category.blog_id
    }, {
      headers: {
        'Authorization': 'Bearer ' + this.token
      }
    }).subscribe(results => {
      if (results.code == 200) {
        this.getAllBlogs();
        this.errorMsg = "";
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
      }
    });
  }

  ivkDelBlg(blog_details) {
    let tmp_request_data = {
      "blog_id": blog_details.blog_id,
      "fg_image": blog_details.fg_image
    };
    let dialogRef = this.dialog.open(DeleteUserGeneratedComponent);
    dialogRef.componentInstance.delete_request_data = tmp_request_data;
    dialogRef.componentInstance.API_NAME = 'deleteBlogContent';
    dialogRef.afterClosed().subscribe(result => {
      if (!result) {
        this.getAllBlogs();
      }
    });
  }

  ivkAddBlg(platform) {
    let dialogRef = this.dialog.open(AddOrUpdateBlogComponent, {
      disableClose: true,
      panelClass: 'add-blg-dialog',
    });
    dialogRef.componentInstance.platform = platform;
    dialogRef.afterClosed().subscribe(result => {
      if (!result) {
        this.getAllBlogs();
      }
    });
  }

  ivkUpdateBlg(blog_details) {
    let dialogRef = this.dialog.open(AddOrUpdateBlogComponent, {
      disableClose: true,
      panelClass: 'add-blg-dialog',
      data: { blog_details: blog_details }
    });
    dialogRef.afterClosed().subscribe(result => {
      if (!result) {
        this.getAllBlogs();
      }
    });
  }
  /*  ivkUpdateBlg(blog_details) {
     let dialogRef = this.dialog.open(BlogListComponent, {
       disableClose: true,
       panelClass: 'add-blg-dialog',
       data: { blog_details: blog_details }
     });
     dialogRef.afterClosed().subscribe(result => {
       if (!result) {
         this.getAllBlogs();
       }
     });
   } */

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

import { Component, OnInit, Renderer, ViewChild, ElementRef, Input, Output, EventEmitter } from '@angular/core';
import { MdDialog, MdDialogRef } from '@angular/material';
import { Observable } from 'rxjs/Rx';
import { Router, ActivatedRoute } from '@angular/router';
import { DataService } from '../data.service';
import { HOST } from '../app.constants';
import { LoadingComponent } from '../loading/loading.component';


@Component({
  selector: 'app-update-category',
  templateUrl: './update-category.component.html'
})
export class UpdateCategoryComponent implements OnInit {

  token: any;
  category_data: any = {};
  update_category_data: any = {};
  loading: any;
  errorMsg: any;
  constructor(public dialogRef: MdDialogRef<UpdateCategoryComponent>, private dataService: DataService, private router: Router, public dialog: MdDialog) {
    this.token = localStorage.getItem('photoArtsAdminToken');
  }

  ngOnInit() {
    this.update_category_data = this.category_data;
  }

  updateCategory(update_category_data) {
    if (typeof update_category_data == 'undefined' || update_category_data == "" || update_category_data == null) {
      this.errorMsg = "Please enter category name";
    }
    else if (typeof update_category_data.name == 'undefined' || update_category_data.name == "" || update_category_data.name == null) {
      this.errorMsg = "Please enter category name";
    }
    else {
      this.errorMsg = "";
      this.loading = this.dialog.open(LoadingComponent);
      this.dataService.postData('updateCategory',
        {
          "category_id": update_category_data.category_id,
          "name": update_category_data.name
        }, {
          headers: {
            'Authorization': 'Bearer ' + this.token
          }
        }).subscribe(results => {
          if (results.code == 200) {
            this.loading.close();
            this.dialogRef.close();
          }
          else if (results.code == 400) {
            localStorage.removeItem("photoArtsAdminToken");
            this.loading.close();
            this.router.navigate(['/admin']);
          }
          else if (results.code == 401) {
            this.token = results.data.new_token;
            this.loading.close();
            localStorage.setItem("photoArtsAdminToken", this.token);
            this.updateCategory(update_category_data);
          }
          else {
            this.errorMsg = results.message;
            this.loading.close();
          }
        });
    }
  }

}

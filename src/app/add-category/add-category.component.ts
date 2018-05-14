import { Component, OnInit, Renderer, ViewChild, ElementRef, Input, Output, EventEmitter } from '@angular/core';
import { MdDialog, MdDialogRef } from '@angular/material';
import { Observable } from 'rxjs/Rx';
import { Router, ActivatedRoute } from '@angular/router';
import { DataService } from '../data.service';
import { HOST } from '../app.constants';
import { LoadingComponent } from '../loading/loading.component';

@Component({
  selector: 'app-add-category',
  templateUrl: './add-category.component.html',
})
export class AddCategoryComponent implements OnInit {

  token: any;
  loading: any;
  category_name:any;
  errorMsg: any;
  constructor(public dialogRef: MdDialogRef<AddCategoryComponent>, private dataService: DataService, private router: Router, public dialog: MdDialog) {
    this.token = localStorage.getItem('photoArtsAdminToken');
  }

  ngOnInit() {
  }

  addCategory(category_name) {
    if (typeof category_name == 'undefined' || category_name == "" || category_name == null) {
      this.errorMsg = "Please enter category name";
      return false;
    }
    else {
      this.errorMsg = "";
      this.loading = this.dialog.open(LoadingComponent);
      this.dataService.postData('addCategory',
        {
          "name": category_name
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
            this.addCategory(category_name);
          }
          else {
            this.errorMsg = results.message;
            this.loading.close();
          }
        });
    }
  }

}

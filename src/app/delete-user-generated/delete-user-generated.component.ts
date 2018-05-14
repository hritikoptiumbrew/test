import { Component, OnInit, Renderer, ViewChild, ElementRef, Input, Output, EventEmitter } from '@angular/core';
import { MdDialog, MdDialogRef } from '@angular/material';
import { Observable } from 'rxjs/Rx';
import { Router, ActivatedRoute } from '@angular/router';
import { DataService } from '../data.service';
import { HOST } from '../app.constants';
import { LoadingComponent } from '../loading/loading.component';

@Component({
  selector: 'app-delete-user-generated',
  templateUrl: './delete-user-generated.component.html',
  styleUrls: ['./delete-user-generated.component.css']
})
export class DeleteUserGeneratedComponent implements OnInit {

  token: any;
  user_feeds_id: any;
  delete_request_data: any = {};
  API_NAME: any;
  loading: any;
  constructor(public dialogRef: MdDialogRef<DeleteUserGeneratedComponent>, private dataService: DataService, private router: Router, public dialog: MdDialog) {
    this.token = localStorage.getItem('photoArtsAdminToken');
  }

  ngOnInit() {
  }

  deleteCategoryImage(delete_request_data, API_NAME) {
    this.loading = this.dialog.open(LoadingComponent);
    this.dataService.postData(API_NAME,
      delete_request_data, {
        headers: {
          'Authorization': 'Bearer ' + this.token
        }
      }).subscribe(results => {
        if (results.code == 200) {
          this.loading.close();
          this.dialogRef.close();
        }
        else if (results.code == 400) {
          this.loading.close();
          localStorage.removeItem("photoArtsAdminToken");
          this.router.navigate(['/admin']);
        }
        else if (results.code == 401) {
          this.loading.close();
          this.token = results.data.new_token;
          localStorage.setItem("photoArtsAdminToken", this.token);
          this.deleteCategoryImage(delete_request_data, API_NAME);
        }
        else {
          this.loading.close();
          this.dialogRef.close();
          console.log(results.message);
        }
      });
  }

}

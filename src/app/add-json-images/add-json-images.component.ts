import { Component, OnInit } from '@angular/core';
import { MdDialog, MdDialogRef } from '@angular/material';
import { Observable } from 'rxjs/Rx';
import { Router, ActivatedRoute } from '@angular/router';
import { DataService } from '../data.service';
import { HOST } from '../app.constants';
import { LoadingComponent } from '../loading/loading.component';

@Component({
  selector: 'app-add-json-images',
  templateUrl: './add-json-images.component.html'
})
export class AddJsonImagesComponent implements OnInit {

  token: any;
  category_img: any;
  fileList: any;
  file: any;
  formData = new FormData();
  successMsg: any;
  errorMsg: any;
  loading: any;

  constructor(public dialogRef: MdDialogRef<AddJsonImagesComponent>, private dataService: DataService, private router: Router, public dialog: MdDialog) {

  }

  ngOnInit() {
    this.token = localStorage.getItem('photoArtsAdminToken');
  }

  fileChange(event) {
    this.formData = new FormData();
    this.fileList = event.target.files;
    for (let i = 0; i < this.fileList.length; i++) {
      this.formData.append('file[]', this.fileList[i]);
    }
  }

  addImages() {
    if (typeof this.fileList == 'undefined' || this.fileList == "" || this.fileList == null) {
      this.errorMsg = "Please select one or multiple images";
      return false;
    }
    else {
      this.errorMsg = "";
      this.loading = this.dialog.open(LoadingComponent);
      this.dataService.postData('addCatalogImagesForJson', this.formData,
        {
          headers: {
            'Authorization': 'Bearer ' + this.token
          }
        }).subscribe(results => {
          if (results.code == 200) {
            this.successMsg = results.message;
            this.loading.close();
            this.dialogRef.close();
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
            this.addImages();
          }
          else {
            this.loading.close();
            this.errorMsg = results.message;
          }
        });
    }
  }

}

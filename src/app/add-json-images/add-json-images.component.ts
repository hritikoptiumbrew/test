import { Component, OnInit } from '@angular/core';
import { MdDialog, MdDialogRef } from '@angular/material';
import { Router } from '@angular/router';
import { DataService } from '../data.service';
import { LoadingComponent } from '../loading/loading.component';
import { ExistingImagesListComponent } from '../existing-images-list/existing-images-list.component';

@Component({
  selector: 'app-add-json-images',
  templateUrl: './add-json-images.component.html'
})
export class AddJsonImagesComponent implements OnInit {

  token: any;
  category_img: any;
  selected_category: any = JSON.parse(localStorage.getItem("selected_category"));
  fileList: any;
  file: any;
  formData = new FormData();
  existing_files: any = [];
  error_list: any = [];
  successMsg: any;
  errorMsg: any;
  loading: any;
  request_data = {
    "is_replace": 0,
    "category_id": this.selected_category.category_id
  };

  constructor(public dialogRef: MdDialogRef<AddJsonImagesComponent>, private dataService: DataService, private router: Router, public dialog: MdDialog) {

  }

  ngOnInit() {
    this.token = localStorage.getItem('photoArtsAdminToken');
  }

  fileChange(event) {
    this.formData = new FormData();
    this.error_list = [];
    this.fileList = event.target.files;
    if (this.fileList && this.fileList.length > 0) {
      for (let i = 0; i < this.fileList.length; i++) {
        var reader = new FileReader();
        reader.onload = (event: any) => {
          this.fileList[i].compressed_img = event.target.result;
        }
        reader.readAsDataURL(this.fileList[i]);
        this.formData.append('file[]', this.fileList[i]);
      }
    }
    else {
      this.fileList = [];
      this.errorMsg = "";
      this.existing_files = [];
    }
  }

  addImages() {
    this.existing_files = [];
    this.error_list = [];
    if (typeof this.fileList == 'undefined' || this.fileList == "" || this.fileList == null || this.fileList.length <= 0) {
      this.errorMsg = "Please select one or multiple images";
      return false;
    }
    else {
      this.errorMsg = "";
      this.loading = this.dialog.open(LoadingComponent);
      this.formData.append("request_data", JSON.stringify(this.request_data));
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
            this.dialog.closeAll();
            this.router.navigate(['/admin']);
          }
          else if (results.code == 401) {
            this.token = results.data.new_token;
            this.loading.close();
            localStorage.setItem("photoArtsAdminToken", this.token);
            this.addImages();
          }
          else if (results.code == 420) {
            this.loading.close();
            this.errorMsg = results.message;
            this.existing_files = results.data.existing_files;
            for (var i = 0, file; file = this.fileList[i]; i++) {
              this.existing_files.forEach(element => {
                if (file.name == element.name) {
                  element.new_image = file;
                }
              });
            }
            /* console.log(this.existing_files); */
          }
          else if (results.code == 432) {
            this.error_list = results.data.error_list;
            this.loading.close();
          }
          else {
            this.loading.close();
            this.errorMsg = results.message;
          }
        });
    }
  }

  viewExistingImages(existing_files) {
    /* console.log(existing_files); */
    let dialogRef = this.dialog.open(ExistingImagesListComponent, { disableClose: true });
    dialogRef.componentInstance.existing_files = existing_files;
    dialogRef.afterClosed().subscribe(result => {
      /* console.log(result); */
      if (!result) {
        this.dialogRef.close();
      }
    });
  }

}

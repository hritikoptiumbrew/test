import { Component, OnInit } from '@angular/core';
import { MdDialog, MdDialogRef } from '@angular/material';
import { Router } from '@angular/router';
import { DataService } from '../data.service';
import { LoadingComponent } from '../loading/loading.component';

@Component({
  selector: 'app-add-subcategory-images-by-id',
  templateUrl: './add-subcategory-images-by-id.component.html'
})
export class AddSubcategoryImagesByIdComponent implements OnInit {

  token: any;
  category_img: any;
  selected_category: any = JSON.parse(localStorage.getItem("selected_category"));
  selected_catalog: any = JSON.parse(localStorage.getItem("selected_catalog"));
  catalog_id: any;
  fileList: any;
  file: any;
  formData = new FormData();
  error_list: any = [];
  successMsg: any;
  errorMsg: any;
  loading: any;

  constructor(public dialogRef: MdDialogRef<AddSubcategoryImagesByIdComponent>, private dataService: DataService, private router: Router, public dialog: MdDialog) {

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
    }
  }


  addImages() {
    this.error_list = [];
    if (typeof this.fileList == 'undefined' || this.fileList == "" || this.fileList == null) {
      this.errorMsg = "Please select one or multiple images";
      return false;
    }
    else {
      this.errorMsg = "";
      this.loading = this.dialog.open(LoadingComponent);
      let request_data = {
        'catalog_id': this.catalog_id,
        'category_id': this.selected_category.category_id,
        'is_featured': this.selected_catalog.is_featured
      };
      this.formData.append('request_data', JSON.stringify(request_data));
      this.dataService.postData('addCatalogImages', this.formData,
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

}

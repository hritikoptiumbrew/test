import { Component, OnInit, Renderer, ViewChild, ElementRef, Input, Output, EventEmitter } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { MdDialog, MdDialogRef } from '@angular/material';
import { Http, RequestOptions, Headers, Response, RequestMethod, RequestOptionsArgs } from '@angular/http';
import { Observable } from 'rxjs/Rx';
import { DataService } from '../data.service';
import { HOST } from '../app.constants';
import { LoadingComponent } from '../loading/loading.component';

@Component({
  selector: 'app-update-subcategory-image-by-id',
  templateUrl: './update-subcategory-image-by-id.component.html'
})
export class UpdateSubcategoryImageByIdComponent {

  token: any;
  sub_category_data: any;
  background_img: any;
  fileList: any;
  file: any;
  formData = new FormData();
  successMsg: any;
  errorMsg: any;
  loading: any;

  @ViewChild('fileInput') fileInputElement: ElementRef;

  constructor(public dialogRef: MdDialogRef<UpdateSubcategoryImageByIdComponent>, private dataService: DataService, private router: Router, private renderer: Renderer, public dialog: MdDialog) {
    this.token = localStorage.getItem('photoArtsAdminToken');
  }

  onImageClicked(event) {
    this.renderer.invokeElementMethod(this.fileInputElement.nativeElement, 'click');
  }

  fileChange(event) {

    if (event.target.files && event.target.files[0]) {
      var reader = new FileReader();
      reader.onload = (event: any) => {
        this.sub_category_data.compressed_img = event.target.result;
      }
      reader.readAsDataURL(event.target.files[0]);
    }

    this.fileList = event.target.files;
    if (this.fileList.length > 0) {
      this.file = this.fileList[0];
      this.formData.append('file', this.file, this.file.name);
    }
  }

  updateImageByCategoryId(sub_category_data) {
    if (typeof this.file == 'undefined' || this.file == "" || this.file == null) {
      this.errorMsg = "Please select new image";
      return false;
    }
    else {
      this.loading = this.dialog.open(LoadingComponent);
      let image_data = {
        'img_id': sub_category_data.img_id
      };
      this.formData.append('request_data', JSON.stringify(image_data));
      this.dataService.postData('updateCatalogImage', this.formData,
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
            this.updateImageByCategoryId(sub_category_data);
          }
          else {
            this.loading.close();
            this.errorMsg = results.message;
          }
        });
    }
  }
}

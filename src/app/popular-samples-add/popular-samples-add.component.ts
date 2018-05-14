import { Component, OnInit, Renderer, ViewChild, ElementRef, Input, Output, EventEmitter } from '@angular/core';
import { MdDialog, MdDialogRef } from '@angular/material';
import { Observable } from 'rxjs/Rx';
import { Router, ActivatedRoute } from '@angular/router';
import { DataService } from '../data.service';
import { HOST } from '../app.constants';
import { LoadingComponent } from '../loading/loading.component';

@Component({
  selector: 'app-popular-samples-add',
  templateUrl: './popular-samples-add.component.html'
})
export class PopularSamplesAddComponent implements OnInit {

  token: any;
  advertisement_data: any = {};
  sample_data: any = {};
  //catalog_id:any;
  fileList1: any;
  fileList2: any;
  file1: any;
  file2: any;
  formData = new FormData();
  successMsg: any;
  errorMsg: any;
  loading: any;

  constructor(public dialogRef: MdDialogRef<PopularSamplesAddComponent>, private dataService: DataService, private router: Router, private renderer: Renderer, public dialog: MdDialog) {
  }

  @ViewChild('fileInput1') fileInputElement1: ElementRef;
  @ViewChild('fileInput2') fileInputElement2: ElementRef;

  ngOnInit() {
    this.token = localStorage.getItem('photoArtsAdminToken');
  }

  onImageClicked1(event) {
    this.renderer.invokeElementMethod(this.fileInputElement1.nativeElement, 'click');
  }

  onImageClicked2(event) {
    this.renderer.invokeElementMethod(this.fileInputElement2.nativeElement, 'click');
  }

  fileChange1(event) {
    if (event.target.files && event.target.files[0]) {
      var reader = new FileReader();
      reader.onload = (event: any) => {
        this.sample_data.original_img = event.target.result;
      }
      reader.readAsDataURL(event.target.files[0]);
    }

    this.fileList1 = event.target.files;
    if (this.fileList1.length > 0) {
      this.file1 = this.fileList1[0];
      this.formData.append('original_img', this.file1, this.file1.name);
    }
  }

  fileChange2(event) {
    if (event.target.files && event.target.files[0]) {
      var reader = new FileReader();
      reader.onload = (event: any) => {
        this.sample_data.display_img = event.target.result;
      }
      reader.readAsDataURL(event.target.files[0]);
    }

    this.fileList2 = event.target.files;
    if (this.fileList2.length > 0) {
      this.file2 = this.fileList2[0];
      this.formData.append('display_img', this.file2, this.file2.name);
    }
  }

  addImages(sample_data) {
    this.token = localStorage.getItem('photoArtsAdminToken');
    if (typeof this.fileList1 == 'undefined' || this.fileList1 == "" || this.fileList1 == null) {
      this.errorMsg = "Select background image";
      return false;
    }
    if (typeof this.fileList2 == 'undefined' || this.fileList2 == "" || this.fileList2 == null) {
      this.errorMsg = "Select display image";
      return false;
    }
    else if (typeof sample_data.image_type=='undefined') {
      this.errorMsg = "Select image type";
      return false;
    }
    else {
      this.loading = this.dialog.open(LoadingComponent);
      let request_data = {
        "image_type": sample_data.image_type,
        "catalog_id": sample_data.catalog_id
      };
      this.formData.append('request_data', JSON.stringify(request_data));
      this.dataService.postData('addFeaturedBackgroundCatalogImage', this.formData,
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
            this.addImages(sample_data);
          }
          else {
            this.loading.close();
            this.errorMsg = results.message;
          }
        });
    }
  }

}

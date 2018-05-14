import { Component, OnInit, Renderer, ViewChild, ElementRef, Input, Output, EventEmitter } from '@angular/core';
import { MdDialog, MdDialogRef } from '@angular/material';
import { Http, RequestOptions, Headers, Response, RequestMethod, RequestOptionsArgs } from '@angular/http';
import { Observable } from 'rxjs/Rx';
import { Router, ActivatedRoute } from '@angular/router';
import { DataService } from '../data.service';
import { HOST } from '../app.constants';
import { LoadingComponent } from '../loading/loading.component';

@Component({
  selector: 'app-catalogs-add',
  templateUrl: './catalogs-add.component.html'
})
export class CatalogsAddComponent implements OnInit {

  token: any;
  sub_category_id: any;
  catalog_data: any = {};
  fileList: any;
  file: any;
  formData = new FormData();
  successMsg: any;
  errorMsg: any;
  loading: any;

  constructor(public dialogRef: MdDialogRef<CatalogsAddComponent>, private dataService: DataService, private router: Router, private renderer: Renderer, public dialog: MdDialog) {

  }

  @ViewChild('fileInput') fileInputElement: ElementRef;

  ngOnInit() {
    this.token = localStorage.getItem('photoArtsAdminToken');
  }

  onImageClicked(event) {
    this.renderer.invokeElementMethod(this.fileInputElement.nativeElement, 'click');
  }

  fileChange(event) {

    if (event.target.files && event.target.files[0]) {
      var reader = new FileReader();
      reader.onload = (event: any) => {
        this.catalog_data.compressed_img = event.target.result;
      }
      reader.readAsDataURL(event.target.files[0]);
    }

    this.fileList = event.target.files;
    if (this.fileList.length > 0) {
      this.file = this.fileList[0];
      this.formData.append('file', this.file, this.file.name);
    }
  }

  addCatalog(catalog_data) {
    if (typeof this.file == 'undefined' || this.file == "" || this.file == null) {
      this.errorMsg = "Image required";
      return false;
    }
    else if (typeof catalog_data.name == 'undefined' || catalog_data.name == "" || catalog_data.name == null) {
      this.errorMsg = "Name required";
      return false;
    }
    else if (typeof catalog_data.is_free == 'undefined') {
      this.errorMsg = "Select catalog pricing";
      return false;
    }
    else if (typeof catalog_data.is_featured == 'undefined') {
      this.errorMsg = "Select catalog type";
      return false;
    }
    else {
      this.errorMsg = "";
      this.loading = this.dialog.open(LoadingComponent);
      let request_data = {
        "sub_category_id": this.sub_category_id,
        "is_free": catalog_data.is_free,
        "is_featured": catalog_data.is_featured,
        "name": catalog_data.name
      };
      this.formData.append('request_data', JSON.stringify(request_data));
      this.dataService.postData('addCatalog', this.formData,
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
            this.addCatalog(catalog_data);
          }
          else {
            this.loading.close();
            this.errorMsg = results.message;
          }
        });
    }
  }

}

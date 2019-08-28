import { Component, OnInit, Renderer, ViewChild, ElementRef } from '@angular/core';
import { MdDialog, MdDialogRef } from '@angular/material';
import { Router } from '@angular/router';
import { DataService } from '../data.service';
import { LoadingComponent } from '../loading/loading.component';

@Component({
  selector: 'app-catalogs-update',
  templateUrl: './catalogs-update.component.html'
})
export class CatalogsUpdateComponent implements OnInit {

  token: any;
  sub_category_id: any;
  catalog_data: any = {};
  selected_category: any = JSON.parse(localStorage.getItem("selected_category"));
  selected_sub_category = JSON.parse(localStorage.getItem("selected_sub_category"));
  fileList: any;
  file: any;
  formData = new FormData();
  successMsg: any;
  errorMsg: any;
  loading: any;

  constructor(public dialogRef: MdDialogRef<CatalogsUpdateComponent>, private dataService: DataService, private router: Router, private renderer: Renderer, public dialog: MdDialog) { }

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

  update_catelog(catalog_data) {
    if (typeof catalog_data.name == "undefined" || catalog_data.name == "" || catalog_data.name == null) {
      this.errorMsg = "Name required";
      return false;
    }
    else {
      this.loading = this.dialog.open(LoadingComponent);
      let category_data = {
        'category_id': this.selected_category.category_id,
        'sub_category_id': this.selected_sub_category.sub_category_id,
        'catalog_id': catalog_data.catalog_id,
        'name': catalog_data.name,
        "is_featured": catalog_data.is_featured,
        'is_free': catalog_data.is_free
      };
      this.formData.append('request_data', JSON.stringify(category_data));
      this.dataService.postData('updateCatalog', this.formData,
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
            this.update_catelog(catalog_data);
          }
          else {
            this.loading.close();
            this.errorMsg = results.message;
          }
        });
    }
  }



}

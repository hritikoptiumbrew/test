import { Component, OnInit, Renderer, ViewChild, ElementRef } from '@angular/core';
import { MdDialog, MdDialogRef } from '@angular/material';
import { Router } from '@angular/router';
import { DataService } from '../data.service';
import { LoadingComponent } from '../loading/loading.component';

@Component({
  selector: 'app-update-sub-category-by-category-id',
  templateUrl: './update-sub-category-by-category-id.component.html'
})
export class UpdateSubCategoryByCategoryIdComponent implements OnInit {

  token: any;
  sub_category_data: any = {};
  fileList: any;
  file: any;
  formData = new FormData();
  successMsg: any;
  errorMsg: any;
  loading: any;

  @ViewChild('fileInput') fileInputElement: ElementRef;

  constructor(public dialogRef: MdDialogRef<UpdateSubCategoryByCategoryIdComponent>, private dataService: DataService, private router: Router, private renderer: Renderer, public dialog: MdDialog) {
  }

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

  updateCategory(sub_category_data) {
    // console.log(sub_category_data.is_featured);
    if (typeof sub_category_data.name == "undefined" || sub_category_data.name == "" || sub_category_data.name == null) {
      this.errorMsg = "Name required";
      return false;
    }
    else if (typeof sub_category_data.is_featured == "undefined") {
      this.errorMsg = "Please select type";
      return false;
    }
    else {
      this.loading = this.dialog.open(LoadingComponent);
      let category_data = {
        'sub_category_id': sub_category_data.sub_category_id,
        'name': sub_category_data.name,
        'is_featured': sub_category_data.is_featured
      };
      this.formData.append('request_data', JSON.stringify(category_data));
      this.dataService.postData('updateSubCategory', this.formData,
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
            this.updateCategory(sub_category_data);
          }
          else {
            this.loading.close();
            this.errorMsg = results.message;
          }
        });
    }
  }

}

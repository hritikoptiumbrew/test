import { Component, OnInit } from '@angular/core';
import { MdDialog, MdDialogRef, MdSnackBar, MdSnackBarConfig } from '@angular/material';
import { Router, ActivatedRoute } from '@angular/router';
import { DomSanitizer } from '@angular/platform-browser';
import { DataService } from '../data.service';
import { LoadingComponent } from '../loading/loading.component';

@Component({
  selector: 'app-existing-images-list',
  templateUrl: './existing-images-list.component.html',
  styleUrls: ['./existing-images-list.component.css']
})
export class ExistingImagesListComponent implements OnInit {

  token: any;
  existing_files: any;
  is_all_checked: any;
  loading: any;
  formData = new FormData();
  request_data = {
    "is_replace": 1
  };

  constructor(public dataservice: DataService, public dialogRef: MdDialogRef<ExistingImagesListComponent>, private router: Router, public dataService: DataService, public sanitizer: DomSanitizer, public snackBar: MdSnackBar, public dialog: MdDialog) { }

  ngOnInit() {
    this.token = localStorage.getItem('photoArtsAdminToken');
    this.existing_files.forEach(element => {
      let urlCreator = window.URL;
      element.url = element.url + "?" + new Date().getTime();
      element.new_image_url = this.sanitizer.bypassSecurityTrustUrl(urlCreator.createObjectURL(element.new_image));
      element.is_checked = false;
    });
  }

  selectAll(is_all_checked) {
    if (is_all_checked == true) {
      for (let k = 0; k < this.existing_files.length; k++) {
        this.existing_files[k].is_checked = true;
      }
    }
    else {
      for (let k = 0; k < this.existing_files.length; k++) {
        this.existing_files[k].is_checked = false;
      }
    }
  }

  valueChanged() {
    for (let k = 0; k < this.existing_files.length; k++) {
      if (this.existing_files[k].is_checked == false) {
        this.is_all_checked = false;
      }
    }
  }

  replaceImages(existing_files) {
    let j = 0;
    this.formData = new FormData();
    for (let k = 0; k < existing_files.length; k++) {
      if (existing_files[k].is_checked == true) {
        j++;
        this.formData.append('file[]', existing_files[k].new_image);
      }
    }
    if (j <= 0) {
      this.showError("Please select one or multiple images to replace", false);
      return false;
    }
    else {
      this.formData.append("request_data", JSON.stringify(this.request_data));
      this.loading = this.dialog.open(LoadingComponent);
      this.dataService.postData('addCatalogImagesForJson', this.formData,
        {
          headers: {
            'Authorization': 'Bearer ' + this.token
          }
        }).subscribe(results => {
          if (results.code == 200) {
            this.showSuccess(results.message, false);
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
            this.replaceImages(existing_files);
          }
          else {
            this.loading.close();
            this.showError(results.message, false);
          }
        });
    }
  }

  showError(message, action) {
    let config = new MdSnackBarConfig();
    config.extraClasses = ['snack-error'];
    /* config.horizontalPosition = "right";
    config.verticalPosition = "top"; */
    config.duration = 5000;
    this.snackBar.open(message, action ? 'Okay!' : undefined, config);
  }

  showSuccess(message, action) {
    let config = new MdSnackBarConfig();
    config.extraClasses = ['snack-success'];
    /* config.horizontalPosition = "right";
    config.verticalPosition = "top"; */
    config.duration = 5000;
    this.snackBar.open(message, action ? 'Okay!' : undefined, config);
  }

}

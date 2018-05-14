import { Component, OnInit, Renderer, ViewChild, ElementRef, Input, Output, EventEmitter } from '@angular/core';
import { MdDialog, MdDialogRef } from '@angular/material';
import { Observable } from 'rxjs/Rx';
import { Router, ActivatedRoute } from '@angular/router';
import { DataService } from '../data.service';
import { HOST } from '../app.constants';
import { LoadingComponent } from '../loading/loading.component';

@Component({
  selector: 'app-advertisements-update',
  templateUrl: './advertisements-update.component.html'
})
export class AdvertisementsUpdateComponent implements OnInit {

  token: any;
  advertisement_data: any = {};
  platformsArray: any[];
  platform: any;
  fileList: any;
  file: any;
  file_logo: any;
  fileList_logo: any;
  formData = new FormData();
  successMsg: any;
  errorMsg: any;
  loading: any;

  constructor(public dialogRef: MdDialogRef<AdvertisementsUpdateComponent>, private dataService: DataService, private router: Router, private renderer: Renderer, public dialog: MdDialog) {
  }

  @ViewChild('fileInput') fileInputElement: ElementRef;
  @ViewChild('fileInput2') fileInputElement2: ElementRef;

  ngOnInit() {
    this.token = localStorage.getItem('photoArtsAdminToken');
  }


  onImageClicked(event) {
    this.renderer.invokeElementMethod(this.fileInputElement.nativeElement, 'click');
  }

  onImage2Clicked(event) {
    this.renderer.invokeElementMethod(this.fileInputElement2.nativeElement, 'click');
  }


  fileChange(event) {
    if (event.target.files && event.target.files[0]) {
      var reader = new FileReader();
      reader.onload = (event: any) => {
        this.advertisement_data.compressed_img = event.target.result;
      }
      reader.readAsDataURL(event.target.files[0]);
    }

    this.fileList = event.target.files;
    if (this.fileList.length > 0) {
      this.file = this.fileList[0];
      this.formData.append('file', this.file, this.file.name);
    }
  }

  file2Change(event) {
    if (event.target.files && event.target.files[0]) {
      var reader = new FileReader();
      reader.onload = (event: any) => {
        this.advertisement_data.app_logo_compressed_img = event.target.result;
      }
      reader.readAsDataURL(event.target.files[0]);
    }

    this.fileList_logo = event.target.files;
    if (this.fileList_logo.length > 0) {
      this.file_logo = this.fileList_logo[0];
      this.formData.append('logo_file', this.file_logo, this.file_logo.name);
    }
  }

  addAdvertisement(advertisement_data) {
    if (typeof advertisement_data.compressed_img == "undefined" || advertisement_data.compressed_img == "" || advertisement_data.compressed_img == null) {
      this.errorMsg = "Please select image";
      return false;
    }
    else if (typeof advertisement_data.app_logo_compressed_img == "undefined" || advertisement_data.app_logo_compressed_img == "" || advertisement_data.app_logo_compressed_img == null) {
      this.errorMsg = "Please select application logo";
      return false;
    }
    else if (typeof advertisement_data.name == "undefined" || advertisement_data.name == "" || advertisement_data.name == null) {
      this.errorMsg = "Please enter application name";
      return false;
    }
    else if (typeof advertisement_data.url == "undefined" || advertisement_data.url == "" || advertisement_data.url == null) {
      this.errorMsg = "Please enter application url";
      return false;
    }
    else if (typeof advertisement_data.app_description == "undefined" || advertisement_data.app_description == "" || advertisement_data.app_description == null) {
      this.errorMsg = "Please enter application description";
      return false;
    }
    else {
      this.loading = this.dialog.open(LoadingComponent);
      let category_data = {
        'advertise_link_id': advertisement_data.advertise_link_id,
        'name': advertisement_data.name,
        'platform': advertisement_data.platform,
        'url': advertisement_data.url,
        'app_description': advertisement_data.app_description
      };
      this.formData.append('request_data', JSON.stringify(category_data));
      this.dataService.postData('updateLink', this.formData,
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
            this.addAdvertisement(advertisement_data);
          }
          else {
            this.loading.close();
            this.errorMsg = results.message;
          }
        });
    }
  }

}

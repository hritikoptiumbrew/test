import { Component, OnInit, ViewChild, ElementRef } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { MdDialog, MdSnackBar, MdSnackBarConfig } from '@angular/material';
import { DataService } from '../data.service';
import { LoadingComponent } from '../loading/loading.component';
import { DeleteUserGeneratedComponent } from '../delete-user-generated/delete-user-generated.component';

@Component({
  selector: 'app-font-list',
  templateUrl: './font-list.component.html',
  styleUrls: ['./font-list.component.css']
})
export class FontListComponent implements OnInit {
  show_add: any = 1;
  token: any;
  private sub: any; //route subscriber
  private categoryId: any;
  selected_category: any = JSON.parse(localStorage.getItem("selected_category"));
  selected_catalog: any = JSON.parse(localStorage.getItem("selected_catalog"));
  subCategoryName: any;
  private catalogId: any;
  fonts_list: any[];
  tmp_fonts_list: any[];
  sub_category_name: any;
  catalogName: any;
  errorMsg: any;
  successMsg: any;
  total_record: any;
  loading: any;
  sortByTagName: any;
  order_type: Boolean;
  order_type_val: any;
  current_path: any = "";
  formData = new FormData();
  fileList: any;
  file: any;
  font_details: any = {};
  checked: any;

  @ViewChild('fileInput') fileInputElement: ElementRef;

  constructor(public route: ActivatedRoute, private dataService: DataService, private router: Router, public dialog: MdDialog, public snackBar: MdSnackBar) {
    this.loading = this.dialog.open(LoadingComponent);
  }

  ngOnInit() {
    this.token = localStorage.getItem('photoArtsAdminToken');
    this.current_path = this.getLocalStorageData();
    this.sub = this.route.params
      .subscribe(params => {
        this.subCategoryName = params['subCategoryName'];
        this.catalogId = params['catalogId'];
        this.catalogName = params['catalogName'];
        this.categoryId = params['categoryId'];
        this.getAllFontsByCatalogId(this.catalogId);
      });
  }

  fileChange(event) {
    if (event.target.files && event.target.files[0]) {
      var reader = new FileReader();
      reader.onload = (event: any) => {
        this.font_details.font_file = event.target.result;
      }
      reader.readAsDataURL(event.target.files[0]);
    }
    this.fileList = event.target.files;
    if (this.fileList.length > 0) {
      this.formData.delete("file");
      this.file = this.fileList[0];
      this.formData.append('file', this.file, this.file.name);
    }
  }

  getAllFontsByCatalogId(catalogId) {
    this.token = localStorage.getItem('photoArtsAdminToken');
    this.dataService.postData('getAllFontsByCatalogIdForAdmin',
      {
        "catalog_id": catalogId,
        "order_by": this.sortByTagName,
        "order_type": this.order_type_val
      }, {
        headers: {
          'Authorization': 'Bearer ' + this.token
        }
      }).subscribe(results => {
        if (results.code == 200) {
          this.fonts_list = results.data.result;
          this.tmp_fonts_list = JSON.parse(JSON.stringify(results.data.result));
          this.fonts_list.forEach(element => {
            element.is_editing = false;
            element.is_linked = '';
          });
          this.total_record = this.fonts_list.length;
          this.errorMsg = "";
          this.successMsg = results.message;
          this.loading.close();
        }
        else if (results.code == 400) {
          this.loading.close();
          localStorage.removeItem("photoArtsAdminToken");
          this.router.navigate(['/admin']);
        }
        else if (results.code == 401) {
          this.token = results.data.new_token;
          localStorage.setItem("photoArtsAdminToken", this.token);
          this.getAllFontsByCatalogId(this.catalogId);
        }
        else {
          this.loading.close();
          this.successMsg = "";
          this.errorMsg = results.message;
        }
      }, error => {
        /* console.log(error.status); */
        /* console.log(error); */
      });
  }

  updateCategory(font_details: any) {
    this.fonts_list.forEach((element: any, i: number) => {
      this.resetRow(element, i);
    });
    let category_data = JSON.parse(JSON.stringify(font_details));
    font_details.is_editing = true;
  }

  resetRow(font_details: any, i: number) {
    font_details.is_editing = false;
    font_details.ios_font_name = this.tmp_fonts_list[i].ios_font_name;
    font_details.android_font_name = this.tmp_fonts_list[i].android_font_name;
  }

  sortBy(sortByTagName, order_type_val) {
    this.sortByTagName = sortByTagName;
    if (order_type_val == "ASC") {
      this.order_type = false;
      this.order_type_val = "DESC";
    }
    else {
      this.order_type = true;
      this.order_type_val = "ASC";
    }
    this.loading = this.dialog.open(LoadingComponent);
    this.getAllFontsByCatalogId(this.catalogId);
  }

  uploadFontFile(font_details: any, is_replace) {
    if (!font_details.font_file) {
      this.showError("Please choose font file to upload", false);
      return false;
    }
    /* else if (!font_details.ios_font_name) {
      this.showError("Please enter font name", false);
      return false;
    }
    else if (!font_details.android_font_name) {
      this.showError("Please enter font path", false);
      return false;
    } */
    else {
      this.loading = this.dialog.open(LoadingComponent);
      let request_data: any = {
        "category_id": this.selected_category.category_id,
        "is_featured": this.selected_catalog.is_featured,
        "catalog_id": this.catalogId,
        "ios_font_name": font_details.ios_font_name,
        "android_font_name": font_details.android_font_name,
        "is_replace": is_replace
      };
      this.formData.append("request_data", JSON.stringify(request_data));
      this.token = localStorage.getItem('photoArtsAdminToken');
      this.dataService.postData('addFont',
        this.formData, {
          headers: {
            'Authorization': 'Bearer ' + this.token
          }
        }).subscribe(results => {
          if (results.code == 200) {
            this.fileInputElement.nativeElement.value = "";
            this.formData = new FormData();
            this.font_details = {};
            this.show_add = 1;
            this.showSuccess(results.message, false);
            this.getAllFontsByCatalogId(this.catalogId);
          }
          else if (results.code == 400) {
            this.loading.close();
            localStorage.removeItem("photoArtsAdminToken");
            this.router.navigate(['/admin']);
          }
          else if (results.code == 401) {
            this.token = results.data.new_token;
            localStorage.setItem("photoArtsAdminToken", this.token);
            this.getAllFontsByCatalogId(this.catalogId);
          }
          else if (results.code == 420) {
            this.loading.close();
            this.show_add = 1;
            this.formData.delete("request_data");
            this.showError(results.message, false);
          }
          else {
            this.loading.close();
            this.successMsg = "";
            this.errorMsg = results.message;
            this.formData.delete("request_data");
            this.showError(results.message, false);
          }
        }, error => {
          /* console.log(error.status); */
          /* console.log(error); */
        });
    }
  }

  saveUpdatedFont(font_details: any) {
    if (!font_details.ios_font_name) {
      this.showError("Please enter font name", false);
      return false;
    }
    else if (!font_details.android_font_name) {
      this.showError("Please enter font path", false);
      return false;
    }
    else {
      this.loading = this.dialog.open(LoadingComponent);
      let request_data: any = {
        "font_id": font_details.font_id,
        "ios_font_name": font_details.ios_font_name,
        "android_font_name": font_details.android_font_name
      };
      this.token = localStorage.getItem('photoArtsAdminToken');
      this.dataService.postData('editFont',
        request_data, {
          headers: {
            'Authorization': 'Bearer ' + this.token
          }
        }).subscribe(results => {
          if (results.code == 200) {
            this.showSuccess(results.message, false);
            this.getAllFontsByCatalogId(this.catalogId);
          }
          else if (results.code == 400) {
            this.loading.close();
            localStorage.removeItem("photoArtsAdminToken");
            this.router.navigate(['/admin']);
          }
          else if (results.code == 401) {
            this.token = results.data.new_token;
            localStorage.setItem("photoArtsAdminToken", this.token);
            this.getAllFontsByCatalogId(this.catalogId);
          }
          else {
            this.loading.close();
            this.successMsg = "";
            this.errorMsg = results.message;
            this.showError(results.message, false);
          }
        }, error => {
          /* console.log(error.status); */
          /* console.log(error); */
        });
    }
  }

  deleteCategory(API_NAME, font_details) {
    let tmp_request_data = {
      "font_id": font_details.font_id
    };
    let dialogRef = this.dialog.open(DeleteUserGeneratedComponent);
    dialogRef.componentInstance.delete_request_data = tmp_request_data;
    dialogRef.componentInstance.API_NAME = API_NAME;
    dialogRef.afterClosed().subscribe(result => {
      if (!result) {
        this.getAllFontsByCatalogId(this.catalogId);
      }
    });
  }

  selectAllFont() {
    for (let font_details of this.fonts_list) {
      if (!this.checked) {
        font_details.is_linked = true ;
      } else {
        font_details.is_linked = false ;
      }
    }
  }

  selectFont() {
    var c;
    for (let font_detail of this.fonts_list) {
      if (font_detail.is_linked == 0) {
        c = 0;
        break;
      }else if (font_detail.is_linked == 1) {
        c = 1;
      } 
    }
    if(c==0){
      this.checked = false;
    } else {
      this.checked = true ;
    }

  }

  removeInvalidFonts(API_NAME) {
    var fontId = [];
    var font_ids = '';

    for (let font_details of this.fonts_list) {
      if (font_details.is_linked) {
        fontId.push(font_details.font_id);
      }
      font_ids = fontId.join(',');
    }
    if (font_ids == '' || font_ids == undefined || font_ids == null) {
      this.showError("Please select fonts which you want to remove ", false);
      return false;
    } else {
      var requestdata = {
        "catalog_id": parseInt(this.catalogId),
        "font_ids": font_ids,
      }
    }
    let dialogRef = this.dialog.open(DeleteUserGeneratedComponent);
    dialogRef.componentInstance.delete_request_data = requestdata;
    dialogRef.componentInstance.API_NAME = API_NAME;
    dialogRef.afterClosed().subscribe(result => {
      if (!result) {
        this.getAllFontsByCatalogId(this.catalogId);
        for (let font_details of this.fonts_list) {
          font_details.is_linked = 0;
        }
      }
    });
  }
  
  getLocalStorageData() {
    let tmp_selected_category = JSON.parse(localStorage.getItem("selected_category"));
    let tmp_selected_sub_category = JSON.parse(localStorage.getItem("selected_sub_category"));
    let tmp_selected_catalog = JSON.parse(localStorage.getItem("selected_catalog"));
    let tmp_current_path = tmp_selected_category.name + " / " + tmp_selected_sub_category.name + " / " + tmp_selected_catalog.name;
    return tmp_current_path;
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

import { Component, OnInit, Renderer, ViewChild, ElementRef, ViewEncapsulation } from '@angular/core';
import { MdDialog, MdDialogRef, MdSnackBar, MdSnackBarConfig } from '@angular/material';
import { FormControl } from '@angular/forms';
import { Observable } from 'rxjs/Rx';
import 'rxjs/add/operator/startWith';
import 'rxjs/add/operator/map';
import { Router } from '@angular/router';
import { DataService } from '../data.service';
import { LoadingComponent } from '../loading/loading.component';

@Component({
  selector: 'app-add-json-data',
  templateUrl: './add-json-data.component.html',
  encapsulation: ViewEncapsulation.None
})
export class AddJsonDataComponent implements OnInit {

  token: any;
  sub_category_id: any;
  catalog_data: any = {};
  selected_category: any = JSON.parse(localStorage.getItem("selected_category"));
  selected_catalog: any = JSON.parse(localStorage.getItem("selected_catalog"));
  search_tag_list: any = [];
  fileList: any;
  file: any;
  formData = new FormData();
  successMsg: any;
  errorMsg: any;
  loading: any;

  visible = true;
  selectable = true;
  removable = true;
  addOnBlur = false;
  separatorKeysCodes: number[] = [13, 188];
  fruitCtrl = new FormControl();
  filtered_search_tags: Observable<string[]>;
  selected_search_tags: string[] = [];
  all_search_tags: string[] = [];
  incorrect_fonts: any[] = [];
  mismatch_fonts: any[] = [];

  constructor(public dialogRef: MdDialogRef<AddJsonDataComponent>, private dataService: DataService, private router: Router, private renderer: Renderer, public dialog: MdDialog, public snackBar: MdSnackBar) {
    this.search_tag_list = JSON.parse(localStorage.getItem("search_tag_list"));
    if (this.search_tag_list) {
      this.search_tag_list.forEach(element => {
        this.all_search_tags.push(element.tag_name);
      });
    }
    this.filtered_search_tags = this.fruitCtrl.valueChanges.
      startWith(null).
      map((fruit: string | null) => fruit ? this._filter(fruit) : this.all_search_tags.slice()); 
    this.selected_search_tags = this.selected_catalog.name.toLowerCase().replace(/[^a-zA-Z ]/g, "  ").replace(/\s\s+/g, ' ').trim().split(" ");
  }

  @ViewChild('fileInput') fileInputElement: ElementRef;
  @ViewChild('fruitInput') fruitInput: ElementRef;

  ngOnInit() {  
    this.token = localStorage.getItem('photoArtsAdminToken');
    /* console.log(this.catalog_data); */
  }
  add(event): void {
    const input = event.input;
    const value = event.value;
    // if (!this.validateString(value)) {
    //   this.showError("Special characters not allowed, only alphanumeric, '&' is allowed in tag name.", false);
    //   return;
    // }
    // else {
      let tmp_array = value.split(",");
      for (let i = 0; i < tmp_array.length; i++) {
        if ((tmp_array[i] || '').trim()) {
          this.selected_search_tags.push(tmp_array[i].trim());
        }
      }

      // Reset the input value
      if (input) {
        input.value = '';
      }

      this.fruitCtrl.setValue(null);
    /* } */
    // }
  }

  validateString(str) {
    var regex = /^[a-z0-9&, ]+$/i.test(str);
    return regex;
  }

  remove(fruit: string): void {
    const index = this.selected_search_tags.indexOf(fruit);

    if (index >= 0) {
      this.selected_search_tags.splice(index, 1);
    }
  }

  selected(fruit): void {
    /* console.log(fruit); */
    this.selected_search_tags.push(fruit);
    this.fruitInput.nativeElement.value = '';
    this.fruitCtrl.setValue(null);
  }

  private _filter(value: string): string[] {
    const filterValue = value.toLowerCase();

    return this.all_search_tags.filter(fruit => fruit.toLowerCase().indexOf(filterValue) === 0);
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
    this.token = localStorage.getItem('photoArtsAdminToken');
    let catalog_data_tmp = JSON.parse(JSON.stringify(catalog_data));
    this.incorrect_fonts = [];
    this.mismatch_fonts = [];
    /* let catalog_data_tmp = catalog_data; */
    if (typeof this.file == 'undefined' || this.file == "" || this.file == null) {
      this.errorMsg = "Image required";
      return false;
    }
    /* else if (typeof catalog_data.name == 'undefined' || catalog_data.name == "" || catalog_data.name == null) {
      this.errorMsg = "Name required";
      return false;
    } */
    else if (typeof catalog_data_tmp.is_free == 'undefined') {
      this.errorMsg = "Select catalog pricing";
      return false;
    }
    else if (typeof catalog_data_tmp.is_featured == 'undefined') {
      this.errorMsg = "Select catalog type";
      return false;
    }
    /* else if (this.selected_search_tags.length <= 0) {
      this.errorMsg = "Please Select/Enter atleast one search tag";
      return false;
    } */
    else if (typeof catalog_data_tmp.json_data == 'undefined' || this.trim(catalog_data_tmp.json_data) == "" || catalog_data_tmp.json_data == null) {
      this.errorMsg = "Please enter JSON data";
      return false;
    }
    else {
      this.errorMsg = "";
      this.loading = this.dialog.open(LoadingComponent);
      /* console.log(catalog_data_tmp.json_data); */
      catalog_data_tmp.json_data = JSON.parse(catalog_data_tmp.json_data);
      /* console.log(catalog_data_tmp.json_data); */
      this.selected_search_tags = this.arrayUnique(this.selected_search_tags);
      let tmp_selected_tags = this.selected_search_tags.join();
      /* console.log(tmp_selected_tags); */
      let request_data = {
        "category_id": this.selected_category.category_id,
        "is_featured_catalog": this.selected_catalog.is_featured,
        "is_free": catalog_data_tmp.is_free,
        "is_featured": catalog_data_tmp.is_featured,
        "is_portrait": catalog_data_tmp.is_portrait,
        "catalog_id": catalog_data_tmp.catalog_id,
        "json_data": catalog_data_tmp.json_data,
        "search_category": tmp_selected_tags
      };
      this.formData.append('request_data', JSON.stringify(request_data));
      this.dataService.postData('addJson', this.formData,
        {
          headers: {
            'Authorization': 'Bearer ' + this.token
          }
        }).subscribe(results => {
          if (results.code == 200) {
            this.successMsg = results.message;
            this.incorrect_fonts = [];
            this.mismatch_fonts = [];
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
            this.addCatalog(catalog_data_tmp);
          }
          else if (results.code == 433) {
            this.showError(results.message, false);
            this.incorrect_fonts = results.data.incorrect_fonts;
            this.mismatch_fonts = results.data.mismatch_fonts;
            this.loading.close();
          }
          else {
            this.loading.close();
            this.formData.delete("request_data");
            this.errorMsg = results.message;
          }
        });
    }
  }

  trim(str) {
    return str.replace(/^\s+|\s+$/g, "");
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

  openFile(event) {
    var input = event.target;
    var reader = new FileReader();
    reader.onload = (event: any) => {
      var text = reader.result;
      this.catalog_data.json_data = event.target.result;
    };
    reader.readAsText(input.files[0]);
  }

  arrayUnique(array) {
    var a = array.concat();
    for (var i = 0; i < a.length; ++i) {
      for (var j = i + 1; j < a.length; ++j) {
        if (a[i].toLowerCase() === a[j].toLowerCase())
          a.splice(j--, 1);
      }
    }

    return a;
  }
}

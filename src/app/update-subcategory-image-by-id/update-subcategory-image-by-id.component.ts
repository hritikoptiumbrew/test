import { Component, OnInit, Renderer, ViewChild, ElementRef } from '@angular/core';
import { Router } from '@angular/router';
import { MdDialog, MdDialogRef } from '@angular/material';
import { FormControl } from '@angular/forms';
import { Observable } from 'rxjs/Rx';
import 'rxjs/add/operator/startWith';
import 'rxjs/add/operator/map';
import { DataService } from '../data.service';
import { LoadingComponent } from '../loading/loading.component';

@Component({
  selector: 'app-update-subcategory-image-by-id',
  templateUrl: './update-subcategory-image-by-id.component.html'
})
export class UpdateSubcategoryImageByIdComponent implements OnInit {

  token: any;
  sub_category_data: any;
  selected_category: any = JSON.parse(localStorage.getItem("selected_category"));
  selected_catalog: any = JSON.parse(localStorage.getItem("selected_catalog"));
  background_img: any;
  fileList: any;
  file: any;
  formData = new FormData();
  successMsg: any;
  errorMsg: any;
  loading: any;
  search_tag_list: any = [];

  visible = true;
  selectable = true;
  removable = true;
  addOnBlur = false;
  separatorKeysCodes: number[] = [13, 188];
  fruitCtrl = new FormControl();
  filtered_search_tags: Observable<string[]>;
  selected_search_tags: string[] = [];
  all_search_tags: string[] = [];

  @ViewChild('fileInput') fileInputElement: ElementRef;
  @ViewChild('fruitInput') fruitInput: ElementRef;

  constructor(public dialogRef: MdDialogRef<UpdateSubcategoryImageByIdComponent>, private dataService: DataService, private router: Router, private renderer: Renderer, public dialog: MdDialog) {
    this.token = localStorage.getItem('photoArtsAdminToken');
    this.search_tag_list = JSON.parse(localStorage.getItem("search_tag_list"));
    if (this.search_tag_list) {
      this.search_tag_list.forEach(element => {
        this.all_search_tags.push(element.tag_name);
      });
    }
    this.filtered_search_tags = this.fruitCtrl.valueChanges.
      startWith(null).
      map((fruit: string | null) => fruit ? this._filter(fruit) : this.all_search_tags.slice());
  }

  ngOnInit() {
    if (typeof this.sub_category_data.search_category == "undefined" || this.sub_category_data.search_category.trim() == "" || this.sub_category_data.search_category == null) {
      this.selected_search_tags = [];
    }
    else {
      this.selected_search_tags = this.sub_category_data.search_category.split(",");
    }
  }

  add(event): void {
    const input = event.input;
    const value = event.value;

    if (!this.validateString(value)) {
      this.errorMsg = "Special characters not allowed, only alphanumeric, '&' is allowed in tag name.";
      return;
    }
    else {
      this.errorMsg = "";
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
    }
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
    this.errorMsg = "";
    this.formData.delete('file');
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
    /* if (typeof this.file == 'undefined' || this.file == "" || this.file == null) {
      this.errorMsg = "Please select new image";
      return false;
    } */
    this.formData.delete("request_data");
    let tmp_selected_tags = this.selected_search_tags.join();
    /* if (typeof tmp_selected_tags == 'undefined' || tmp_selected_tags.trim() == "" || tmp_selected_tags == null) {
      this.errorMsg = "Please Select/Enter atleast one search tag";
      return false;
    }
    else { */
    this.loading = this.dialog.open(LoadingComponent);
    let image_data = {
      'img_id': sub_category_data.img_id,
      'category_id': this.selected_category.category_id,
      'is_featured': this.selected_catalog.is_featured,
      'search_category': tmp_selected_tags
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
    /* } */
  }
}

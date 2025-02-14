import { Component } from '@angular/core';
import { MdDialogRef } from '@angular/material';
import { Router } from '@angular/router';
import { DataService } from '../data.service';

@Component({
  selector: 'app-delete-subcategory-image-by-id',
  templateUrl: './delete-subcategory-image-by-id.component.html'
})
export class DeleteSubcategoryImageByIdComponent {

  token: any;
  sub_category_img_id: any;
  constructor(public dialogRef: MdDialogRef<DeleteSubcategoryImageByIdComponent>, private dataService: DataService, private router: Router) {
    this.token = localStorage.getItem('photoArtsAdminToken');
  }
  deleteCategoryImage(sub_category_img_id) {
    this.dataService.postData('deleteCatalogImage',
      {
        "img_id": sub_category_img_id
      }, {
        headers: {
          'Authorization': 'Bearer ' + this.token
        }
      }).subscribe(results => {
        if (results.code == 200) {
          this.dialogRef.close();
        }
        else if (results.code == 400) {
          localStorage.removeItem("photoArtsAdminToken");
          this.router.navigate(['/admin']);
        }
        else if (results.code == 401) {
          this.token = results.data.new_token;
          localStorage.setItem("photoArtsAdminToken", this.token);
          this.deleteCategoryImage(sub_category_img_id);
        }
        else {
          this.dialogRef.close();
          /* console.log(results.message); */
        }
      });
  }

}

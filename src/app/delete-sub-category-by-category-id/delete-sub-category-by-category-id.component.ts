import { Component } from '@angular/core';
import { MdDialogRef } from '@angular/material';
import { Router } from '@angular/router';
import { DataService } from '../data.service';

@Component({
  selector: 'app-delete-sub-category-by-category-id',
  templateUrl: './delete-sub-category-by-category-id.component.html'
})
export class DeleteSubCategoryByCategoryIdComponent {

  token: any;
  sub_category_id: any = {};

  constructor(public dialogRef: MdDialogRef<DeleteSubCategoryByCategoryIdComponent>, private dataService: DataService, private router: Router) {
    this.token = localStorage.getItem('photoArtsAdminToken');
  }
  deleteSubCategory(sub_category_id) {
    this.dataService.postData('deleteSubCategory',
      {
        "sub_category_id": sub_category_id
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
          this.deleteSubCategory(sub_category_id);
        }
        else {
          /* console.log(results.message); */
        }
      });
  }

}

import { Component } from '@angular/core';
import { MdDialogRef } from '@angular/material';
import { Router } from '@angular/router';
import { DataService } from '../data.service';

@Component({
  selector: 'app-delete-category',
  templateUrl: './delete-category.component.html'
})
export class DeleteCategoryComponent {

  token: any;
  category_id: any = {};
  constructor(public dialogRef: MdDialogRef<DeleteCategoryComponent>, private dataService: DataService, private router: Router) {
    this.token = localStorage.getItem('photoArtsAdminToken');
  }
  deleteCategory(category_id) {
    this.dataService.postData('deleteCategory',
      {
        "category_id": category_id
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
          this.deleteCategory(category_id);
        }
        else {
          /* console.log(results.message); */
        }
      });
  }

}

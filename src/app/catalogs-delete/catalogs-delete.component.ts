import { Component } from '@angular/core';
import { MdDialogRef } from '@angular/material';
import { Router } from '@angular/router';
import { DataService } from '../data.service';

@Component({
  selector: 'app-catalogs-delete',
  templateUrl: './catalogs-delete.component.html'
})
export class CatalogsDeleteComponent {

  token: any;
  catalog_id: any = {};

  constructor(public dialogRef: MdDialogRef<CatalogsDeleteComponent>, private dataService: DataService, private router: Router) {
    this.token = localStorage.getItem('photoArtsAdminToken');
  }
  deleteCatalog(catalog_id) {
    this.dataService.postData('deleteCatalog',
      {
        "catalog_id": catalog_id
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
          this.deleteCatalog(catalog_id);
        }
        else {
          /* console.log(results.message); */
        }
      });
  }

}

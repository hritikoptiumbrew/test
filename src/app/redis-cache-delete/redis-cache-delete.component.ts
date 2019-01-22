import { Component } from '@angular/core';
import { MdDialogRef } from '@angular/material';
import { Router } from '@angular/router';
import { DataService } from '../data.service';

@Component({
  selector: 'app-redis-cache-delete',
  templateUrl: './redis-cache-delete.component.html'
})
export class RedisCacheDeleteComponent {

  token: any;
  delete_cache_data: any = [];
  constructor(public dialogRef: MdDialogRef<RedisCacheDeleteComponent>, private dataService: DataService, private router: Router) {
    this.token = localStorage.getItem('photoArtsAdminToken');
  }

  clearCacheData(delete_cache_data) {
    this.dataService.postData('deleteRedisKeys',
      {
        "keys_list": delete_cache_data
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
          this.clearCacheData(delete_cache_data);
        }
        else {
          this.dialogRef.close();
          /* console.log(results.message); */
        }
      });
  }

}

import { Component, OnInit } from '@angular/core';
import { MdDialog, MdDialogRef } from '@angular/material';
import { Router, ActivatedRoute } from '@angular/router';
import { DataService } from '../data.service';
import { HOST } from '../app.constants';
import { LoadingComponent } from '../loading/loading.component';
import { RedisCacheDeleteComponent } from '../redis-cache-delete/redis-cache-delete.component';

@Component({
  templateUrl: './redis-cache.component.html'
})
export class RedisCacheComponent implements OnInit {

  token: any;
  successMsg: any;
  errorMsg: any;
  keys_list: any = [];
  delete_cache: any = [];
  total_record: any;
  is_all_checked: any;
  loading: any;

  constructor(private dataService: DataService, private router: Router, public dialog: MdDialog) {
    this.loading = this.dialog.open(LoadingComponent);
  }

  selectAll(is_all_checked) {
    if (is_all_checked == true) {
      for (let k = 0; k < this.keys_list.length; k++) {
        this.keys_list[k].is_checked = true;
        this.delete_cache.push({ "key": this.keys_list[k].key });
      }
    }
    else {
      for (let k = 0; k < this.keys_list.length; k++) {
        this.keys_list[k].is_checked = false;
        this.delete_cache = [];
      }
    }
  }

  valueChanged(key_detail) {
    let selected_item = [];
    for (let k = 0; k < this.keys_list.length; k++) {
      if (this.keys_list[k].is_checked == true) {
        selected_item.push({ "key": this.keys_list[k].key });
      }
      this.delete_cache = selected_item;
    }
  }

  ngOnInit() {
    this.token = localStorage.getItem('photoArtsAdminToken');
    this.getAllBackgroundCatogory();
  }

  getAllBackgroundCatogory() {
    this.token = localStorage.getItem('photoArtsAdminToken');
    this.keys_list = [];
    this.dataService.postData('getRedisKeys',
      {}, {
        headers: {
          'Authorization': 'Bearer ' + this.token
        }
      }).subscribe(results => {
        if (results.code == 200) {
          this.total_record = results.data.keys_list.length;
          for (let j = 0; j < this.total_record; j++) {
            this.keys_list.push({ "key": results.data.keys_list[j], "is_checked": false });
          }
          this.loading.close();
          this.errorMsg = "";
          this.successMsg = results.message;
        }
        else if (results.code == 400) {
          this.loading.close();
          localStorage.removeItem("photoArtsAdminToken");
          this.router.navigate(['/admin']);
        }
        else if (results.code == 401) {
          this.token = results.data.new_token;
          localStorage.setItem("photoArtsAdminToken", this.token);
          this.getAllBackgroundCatogory();
        }
        else {
          this.loading.close();
          this.successMsg = "";
          this.errorMsg = results.message;
        }
      }, error => {
        console.log(error.status);
        console.log(error);
      });
  }

  deleteFromCache(key_detail) {
    let dialogRef = this.dialog.open(RedisCacheDeleteComponent);
    dialogRef.componentInstance.delete_cache_data = key_detail;
    dialogRef.afterClosed().subscribe(result => {
      if (result == true) {
      }
      else {
        this.loading = this.dialog.open(LoadingComponent);
        this.is_all_checked = false;
        this.delete_cache = [];
        this.getAllBackgroundCatogory();
      }
    });

  }

}

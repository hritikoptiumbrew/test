import { Component, OnInit, Renderer, ViewChild, ElementRef, Input, Output, EventEmitter } from '@angular/core';
import { MdDialog, MdDialogRef } from '@angular/material';
import { Observable } from 'rxjs/Rx';
import { Router, ActivatedRoute } from '@angular/router';
import { DataService } from '../data.service';
import { HOST } from '../app.constants';
import { LoadingComponent } from '../loading/loading.component';

@Component({
  selector: 'app-advertisements-delete',
  templateUrl: './advertisements-delete.component.html'
})
export class AdvertisementsDeleteComponent {

  token: any;
  advertise_link_id: any = {};
  constructor(public dialogRef: MdDialogRef<AdvertisementsDeleteComponent>, private dataService: DataService, private router: Router) {
    this.token = localStorage.getItem('photoArtsAdminToken');
  }
  deleteAd(advertise_link_id) {
    this.dataService.postData('deleteLink',
      {
        "advertise_link_id": advertise_link_id
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
          this.deleteAd(advertise_link_id);
        }
        else {
          /* console.log(results.message); */
        }
      });
  }


}

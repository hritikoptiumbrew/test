import { Component, OnInit, Inject } from '@angular/core';
import { MdDialog, MdDialogRef, MD_DIALOG_DATA } from '@angular/material';
import { Router, ActivatedRoute } from '@angular/router';
import { DataService } from '../data.service';
import { LoadingComponent } from '../loading/loading.component';

@Component({
  selector: 'app-mv-to-ctlg',
  templateUrl: './mv-to-ctlg.component.html',
  styleUrls: ['./mv-to-ctlg.component.css']
})
export class MvToCtlgComponent implements OnInit {

  token: any;
  sub_category_list: any;
  catalog_data: any = {};
  total_record: any;
  successMsg: any;
  errorMsg: any;
  loading: any;

  constructor(public dialogRef: MdDialogRef<MvToCtlgComponent>, @Inject(MD_DIALOG_DATA) public data: any, public route: ActivatedRoute, private dataService: DataService, private router: Router, public dialog: MdDialog) {
    this.token = localStorage.getItem("photoArtsAdminToken");
    this.catalog_data = data.catalog_data;
    this.gtSbCatLst(this.catalog_data)

  }

  ngOnInit() {
    this.token = localStorage.getItem("photoArtsAdminToken");
  }

  gtSbCatLst(catalog_data) {
    this.loading = this.dialog.open(LoadingComponent);
    this.errorMsg = "";
    this.successMsg = "";
    this.dataService.postData('getAllSubCategoryToMoveTemplate',
      {
        "img_id": catalog_data.img_id
      }, {
        headers: {
          'Authorization': 'Bearer ' + this.token
        }
      }).subscribe(results => {
        if (results.code == 200) {
          this.successMsg = "";
          this.sub_category_list = results.data.sub_category_list;
          this.total_record = this.sub_category_list.length;
          this.loading.close();
        }
        else if (results.code == 400) {
          this.loading.close();
          localStorage.removeItem("photoArtsAdminToken");
          this.router.navigate(['/admin']);
        }
        else if (results.code == 401) {
          this.errorMsg = "";
          this.successMsg = "";
          this.token = results.data.new_token;
          this.loading.close();
          localStorage.setItem("photoArtsAdminToken", this.token);
          this.gtSbCatLst(catalog_data);
        }
        else {
          this.loading.close();
          this.errorMsg = results.message;
          this.successMsg = "";
        }
      });
  }

}

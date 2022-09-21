import { Component, Inject, inject, Input, OnInit } from '@angular/core';
import { NbDialogRef, NB_DOCUMENT } from '@nebular/theme';
import { UtilService } from 'app/util.service';
import { DataService } from 'app/data.service';
import { ERROR } from 'app/app.constants';

@Component({
  selector: 'ngx-view-catalog-type',
  templateUrl: './view-catalog-type.component.html',
  styleUrls: ['./view-catalog-type.component.scss']
})
export class ViewCatalogTypeComponent implements OnInit {
  catlogId:any;
  img_ids:any=[];
  is_free;
  is_ios_free;
  is_featured;
  is_portrait;
  is_active;
  // @Input() title: any;
  token: string;
  dialog: any;
  constructor(private dialogRef: NbDialogRef<ViewCatalogTypeComponent>,
    private utils:UtilService,
    private dataService: DataService,
    ) { 
      this.token = localStorage.getItem('at');
      this.utils.dialogref = this.dialogRef;
    }

  ngOnInit(): void {
    // console.log('here is your title: ', this.title); 
  }
  closeDialog(){
    // console.log("hello");
    this.dialogRef.close({ res: "" });
  }
  saveCatlogData()
  {
    // console.log("hello");
    this.dataService.postData('updateMultipleTemplateByAdmin', {
      "catalog_id":this.catlogId,
      "img_ids": this.img_ids.join(','),
      "is_free":this.is_free,
      "is_ios_free":this.is_ios_free,
      "is_featured":this.is_featured,
      "is_portrait":this.is_portrait,
      "is_active":this.is_active
    }, {
      headers: {
        'Authorization': 'Bearer ' + this.token
      }
    }).then((results: any) => {
      // console.log(results);

      if (results.code == 200) {
        this.utils.showSuccess(results.message, 4000);
        this.utils.hideLoader();
        this.dialogRef.close({ res: "add" });
      }
      else if (results.code == 201) {
        this.utils.showError(results.message, 4000);
        this.utils.hideLoader();
      }
      else if (results.status || results.status == 0) {
        this.utils.showError(ERROR.SERVER_ERR, 4000);
        this.utils.hideLoader();
      }
      else {
        this.utils.showError(results.message, 4000);
        this.utils.hideLoader();
      }
    },(error: any) => {
      console.log(error);
      this.utils.hideLoader();
      this.utils.showError(ERROR.SERVER_ERR, 4000);
    }).catch((error: any) => {
      console.log(error);
      this.utils.hideLoader();
      this.utils.showError(ERROR.SERVER_ERR, 4000);
    });
  }

}

/*
 * Optimumbrew Technology

 * Project       : Photo Editor Lab
 * File          : viewcorruptedfonts.component.ts
 * File Created  : Wednesday, 11th November 2020 06:09:44 pm
 * Author        : Optimumbrew
 * Auther Email  : info@optimumbrew.com
 * Last Modified : Wednesday, 11th November 2020 06:27:12 pm
 * -----
 * Copyright 2018 - 2020 Optimumbrew Technology
 */


import { Component, OnInit } from '@angular/core';
import { NbDialogRef } from '@nebular/theme';
import { DataService } from 'app/data.service';
import { UtilService } from 'app/util.service';
import { ERROR, ENV_CONFIG } from '../../app.constants';
@Component({
  selector: 'ngx-viewcorruptedfonts',
  templateUrl: './viewcorruptedfonts.component.html',
  styleUrls: ['./viewcorruptedfonts.component.scss']
})
export class ViewcorruptedfontsComponent implements OnInit {

  token:any;
  fontList:any;
  totalRecord:any;
  constructor(private dataService: DataService,private utils: UtilService,private nbDialogRef: NbDialogRef<ViewcorruptedfontsComponent>) { 
    this.utils.dialogref = this.nbDialogRef;
  }

  ngOnInit(): void {
    this.token = localStorage.getItem("at");
    this.getFontList();
  }

  closeDialog(){
    this.nbDialogRef.close();
  }
  getFontList(){
    this.utils.showLoader();
     this.dataService.postData('getCorruptedFontList',
       {
         "last_sync_time": 0
       }, {
       headers: {
         'Authorization': 'Bearer ' + this.token
       }
     }).then((results: any) => {
       if (results.code == 200) {
         this.fontList = results.data.result;
         this.totalRecord = this.fontList.length;
         this.utils.hideLoader();
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
     }, (error: any) => {
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

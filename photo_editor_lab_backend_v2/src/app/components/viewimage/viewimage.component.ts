/*
 * Optimumbrew Technology

 * Project       : Photo Editor Lab
 * File          : viewimage.component.ts
 * File Created  : Thursday, 15th October 2020 01:12:03 pm
 * Author        : Optimumbrew
 * Auther Email  : info@optimumbrew.com
 * Last Modified : Thursday, 29th October 2020 11:30:02 am
 * -----
 * Copyright 2018 - 2020 Optimumbrew Technology
 */


import { Component, OnInit } from '@angular/core';
import { NbDialogRef } from '@nebular/theme';
import { UtilService } from 'app/util.service';


@Component({
  selector: 'ngx-viewimage',
  templateUrl: './viewimage.component.html',
  styleUrls: ['./viewimage.component.scss']
})
export class ViewimageComponent implements OnInit {

  imgSrc: any;
  typeImg:any;
  category_id:any;  
  constructor(private utils: UtilService,private dialogRef: NbDialogRef<ViewimageComponent>) { 
    this.utils.dialogref = this.dialogRef;
    
  }

  ngOnInit(): void {
    this.category_id = JSON.parse(localStorage.getItem('selected_category')).category_id;
    
  }

  closeDialog() {
    this.dialogRef.close();
  }


}

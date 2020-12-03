/*
 * Optimumbrew Technology

 * Project       : Photo Editor Lab
 * File          : dashboard.component.ts
 * File Created  : Thursday, 15th October 2020 01:12:03 pm
 * Author        : Optimumbrew
 * Auther Email  : info@optimumbrew.com
 * Last Modified : Thursday, 29th October 2020 11:04:14 am
 * -----
 * Copyright 2018 - 2020 Optimumbrew Technology
 */


import { Component } from '@angular/core';
import { UtilService } from 'app/util.service';

@Component({
  selector: 'ngx-dashboard',
  templateUrl: './dashboard.component.html',
})
export class DashboardComponent {

  constructor(private util: UtilService) {

  }

  opendialog() {
    this.util.showLoader();
  }

}

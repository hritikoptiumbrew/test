/*
 * Optimumbrew Technology

 * Project       : Photo Editor Lab
 * File          : app.component.ts
 * File Created  : Thursday, 15th October 2020 01:12:03 pm
 * Author        : Optimumbrew
 * Auther Email  : info@optimumbrew.com
 * Last Modified : Thursday, 29th October 2020 10:59:11 am
 * -----
 * Copyright 2018 - 2020 Optimumbrew Technology
 */

import { Component, OnInit } from '@angular/core';
import { NbDialogService } from '@nebular/theme';
import { AnalyticsService } from './@core/utils/analytics.service';

@Component({
  selector: 'ngx-app',
  template: `<router-outlet></router-outlet>`,
})
export class AppComponent implements OnInit {

  constructor(private analytics: AnalyticsService, private dialog: NbDialogService) {
  }

  ngOnInit() {
    this.analytics.trackPageViews();
  }

}

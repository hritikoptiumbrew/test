/*
 * Optimumbrew Technology

 * Project       : Photo Editor Lab
 * File          : one-column.layout.ts
 * File Created  : Thursday, 15th October 2020 01:12:03 pm
 * Author        : Optimumbrew
 * Auther Email  : info@optimumbrew.com
 * Last Modified : Thursday, 29th October 2020 11:40:11 am
 * -----
 * Copyright 2018 - 2020 Optimumbrew Technology
 */


import { Component } from '@angular/core';

@Component({
  selector: 'ngx-one-column-layout',
  styleUrls: ['./one-column.layout.scss'],
  template: `
    <nb-layout windowMode>
      <nb-layout-header fixed>
        <ngx-header></ngx-header>
      </nb-layout-header>

      <nb-sidebar class="menu-sidebar" tag="menu-sidebar" responsive>
        <ng-content select="nb-menu"></ng-content>
      </nb-sidebar>

      <nb-layout-column style="position: relative">
        <ng-content select="router-outlet"></ng-content>
        <div class="new-loader" id="pageLoadingNew" loading-visible="false">
          <div class="pageloader-content">
            <div class="page-loader">
            <div class="loading">
            <div class="loading-bar"></div>
            <div class="loading-bar"></div>
            <div class="loading-bar"></div>
            <div class="loading-bar"></div>
          </div>
          <div style="font-size: medium; margin-top: 10px;">
            Please Wait...
          </div>
            </div> 
          </div>
        </div>
      </nb-layout-column>

      <!--nb-layout-footer fixed>
        <ngx-footer></ngx-footer>
      </nb-layout-footer-->
    
    </nb-layout>
  `,
})
export class OneColumnLayoutComponent { 
  
}

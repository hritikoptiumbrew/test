/*
 * Optimumbrew Technology

 * Project       : Photo Editor Lab
 * File          : viewsubcategories.module.ts
 * File Created  : Monday, 19th October 2020 11:58:05 am
 * Author        : Optimumbrew
 * Auther Email  : info@optimumbrew.com
 * Last Modified : Thursday, 29th October 2020 11:13:31 am
 * -----
 * Copyright 2018 - 2020 Optimumbrew Technology
 */


import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ViewsubcategoriesComponent } from './viewsubcategories.component';
import { NbCardModule, NbContextMenuModule, NbIconModule, NbTooltipModule } from '@nebular/theme';
import { LazyLoadImageModule } from 'ng-lazyload-image';
import { VirtualScrollerModule } from 'ngx-virtual-scroller';

@NgModule({
  declarations: [ViewsubcategoriesComponent],
  imports: [
    CommonModule,
    NbCardModule,
    NbTooltipModule,
    NbIconModule,
    LazyLoadImageModule,
    NbContextMenuModule,
    VirtualScrollerModule
  ]
})
export class ViewsubcategoriesModule { }

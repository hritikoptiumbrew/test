/*
 * Optimumbrew Technology

 * Project       : Photo Editor Lab
 * File          : viewcategories.module.ts
 * File Created  : Thursday, 15th October 2020 01:12:03 pm
 * Author        : Optimumbrew
 * Auther Email  : info@optimumbrew.com
 * Last Modified : Thursday, 29th October 2020 11:12:29 am
 * -----
 * Copyright 2018 - 2020 Optimumbrew Technology
 */


import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ViewcategoriesComponent } from './viewcategories.component';
import { NbAccordionModule, NbCardModule, NbIconModule, NbInputModule, NbSelectModule, NbTooltipModule } from '@nebular/theme';
import { FormsModule } from '@angular/forms'
import { NgxPaginationModule } from 'ngx-pagination';
import { LazyLoadImageModule } from 'ng-lazyload-image';

// import { NgxPaginateModule } from 'ngx-paginate';

@NgModule({
  declarations: [ViewcategoriesComponent],
  imports: [
    CommonModule,
    NbSelectModule,
    NbCardModule,
    NbInputModule,
    NbIconModule,
    FormsModule,
    NgxPaginationModule,
    NbTooltipModule,
    LazyLoadImageModule,
    NbAccordionModule
  ]
})
export class ViewcategoriesModule { }

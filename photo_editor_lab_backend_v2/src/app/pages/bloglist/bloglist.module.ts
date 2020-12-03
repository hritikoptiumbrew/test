/*
 * Optimumbrew Technology

 * Project       : Photo Editor Lab
 * File          : bloglist.module.ts
 * File Created  : Thursday, 22nd October 2020 06:39:16 pm
 * Author        : Optimumbrew
 * Auther Email  : info@optimumbrew.com
 * Last Modified : Thursday, 22nd October 2020 06:42:39 pm
 * -----
 * Copyright 2018 - 2020 Optimumbrew Technology
 */


import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { BloglistComponent } from './bloglist.component';
import { NbCardModule, NbIconModule, NbSelectModule, NbTooltipModule } from '@nebular/theme';
import { FormsModule } from '@angular/forms';
import { NgxPaginationModule } from 'ngx-pagination';
import { LazyLoadImageModule } from 'ng-lazyload-image';


@NgModule({
  declarations: [BloglistComponent],
  imports: [
    CommonModule,
    NbCardModule,
    NbTooltipModule,
    NbIconModule,
    NbIconModule,
    FormsModule,
    NbSelectModule,
    NgxPaginationModule,
    LazyLoadImageModule
  ]
})
export class BloglistModule { }

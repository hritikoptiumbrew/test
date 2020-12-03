/*
 * Optimumbrew Technology

 * Project       : Photo Editor Lab
 * File          : imagedetails.module.ts
 * File Created  : Saturday, 24th October 2020 03:55:50 pm
 * Author        : Optimumbrew
 * Auther Email  : info@optimumbrew.com
 * Last Modified : Saturday, 24th October 2020 04:06:44 pm
 * -----
 * Copyright 2018 - 2020 Optimumbrew Technology
 */


import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ImagedetailsComponent } from './imagedetails.component';
import { NbCardModule, NbInputModule, NbSelectModule } from '@nebular/theme';
import { Ng2SmartTableModule } from 'ng2-smart-table';
import { FormsModule } from '@angular/forms';
import { NgxPaginationModule } from 'ngx-pagination';
import { SizePipe } from 'app/size.pipe';
import { LazyLoadImageModule } from 'ng-lazyload-image';

@NgModule({
  declarations: [ImagedetailsComponent,SizePipe],
  imports: [
    CommonModule,
    NbCardModule,
    NbInputModule,
    Ng2SmartTableModule,
    FormsModule,
    NbSelectModule,
    NgxPaginationModule,
    LazyLoadImageModule
  ]
})
export class ImagedetailsModule { }

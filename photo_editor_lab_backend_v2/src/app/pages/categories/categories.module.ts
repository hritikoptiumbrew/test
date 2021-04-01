/*
 * Optimumbrew Technology

 * Project       : Photo Editor Lab
 * File          : categories.module.ts
 * File Created  : Thursday, 15th October 2020 01:12:03 pm
 * Author        : Optimumbrew
 * Auther Email  : info@optimumbrew.com
 * Last Modified : Thursday, 29th October 2020 11:03:59 am
 * -----
 * Copyright 2018 - 2020 Optimumbrew Technology
 */


import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { CategoriesComponent } from './categories.component';
import { NbCardModule, NbInputModule, NbIconModule, NbTooltipModule, NbSelectModule } from '@nebular/theme';

import { Ng2SmartTableModule } from 'ng2-smart-table';
import { FormsModule } from '@angular/forms'
import { NgxPaginationModule } from 'ngx-pagination';


@NgModule({
  declarations: [CategoriesComponent],
  imports: [
    CommonModule,
    NbCardModule,
    NbInputModule,
    NbIconModule,
    Ng2SmartTableModule,
    FormsModule,
    NbTooltipModule,
    NbSelectModule,
    NgxPaginationModule
  ]
})
export class CategoriesModule { }

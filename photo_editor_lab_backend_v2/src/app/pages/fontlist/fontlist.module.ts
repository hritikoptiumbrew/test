/*
 * Optimumbrew Technology

 * Project       : Photo Editor Lab
 * File          : fontlist.module.ts
 * File Created  : Thursday, 22nd October 2020 12:16:40 pm
 * Author        : Optimumbrew
 * Auther Email  : info@optimumbrew.com
 * Last Modified : Thursday, 22nd October 2020 12:28:01 pm
 * -----
 * Copyright 2018 - 2020 Optimumbrew Technology
 */


import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FontlistComponent } from './fontlist.component';
import { NbCardModule, NbIconModule, NbInputModule, NbSelectModule, NbTooltipModule } from '@nebular/theme';
import { Ng2SmartTableModule } from 'ng2-smart-table';
import { FormsModule } from '@angular/forms';


@NgModule({
  declarations: [FontlistComponent],
  imports: [
    CommonModule,
    NbCardModule,
    NbTooltipModule,
    NbIconModule,
    NbInputModule,
    Ng2SmartTableModule,
    FormsModule,
    NbSelectModule
  ]
})
export class FontlistModule { }

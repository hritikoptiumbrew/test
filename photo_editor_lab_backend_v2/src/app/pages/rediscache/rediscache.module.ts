/*
 * Optimumbrew Technology

 * Project       : Photo Editor Lab
 * File          : rediscache.module.ts
 * File Created  : Monday, 26th October 2020 10:58:13 am
 * Author        : Optimumbrew
 * Auther Email  : info@optimumbrew.com
 * Last Modified : Monday, 26th October 2020 11:02:19 am
 * -----
 * Copyright 2018 - 2020 Optimumbrew Technology
 */


import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RediscacheComponent } from './rediscache.component';
import { NbAccordionModule, NbCardModule, NbIconModule } from '@nebular/theme';
import { Ng2SmartTableModule } from 'ng2-smart-table';
import { FormsModule } from '@angular/forms';


@NgModule({
  declarations: [RediscacheComponent],
  imports: [
    CommonModule,
    NbCardModule,
    Ng2SmartTableModule,
    FormsModule,
    NbAccordionModule,
    NbIconModule
  ]
})
export class RediscacheModule { }

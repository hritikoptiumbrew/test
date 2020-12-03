/*
 * Optimumbrew Technology

 * Project       : Photo Editor Lab
 * File          : statistics.module.ts
 * File Created  : Friday, 23rd October 2020 01:09:06 pm
 * Author        : Optimumbrew
 * Auther Email  : info@optimumbrew.com
 * Last Modified : Friday, 23rd October 2020 01:14:18 pm
 * -----
 * Copyright 2018 - 2020 Optimumbrew Technology
 */


import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { StatisticsComponent } from './statistics.component';
import { NbAccordionModule, NbCardModule } from '@nebular/theme';
import { Ng2SmartTableModule } from 'ng2-smart-table';
import { FormsModule } from '@angular/forms';



@NgModule({
  declarations: [StatisticsComponent],
  imports: [
    CommonModule,
    NbCardModule,
    Ng2SmartTableModule,
    FormsModule,
    NbAccordionModule
  ]
})
export class StatisticsModule { }

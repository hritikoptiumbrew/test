/*
 * Optimumbrew Technology

 * Project       : Photo Editor Lab
 * File          : popularsamples.module.ts
 * File Created  : Thursday, 22nd October 2020 04:53:58 pm
 * Author        : Optimumbrew
 * Auther Email  : info@optimumbrew.com
 * Last Modified : Thursday, 22nd October 2020 06:42:12 pm
 * -----
 * Copyright 2018 - 2020 Optimumbrew Technology
 */


import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { PopularsamplesComponent } from './popularsamples.component';
import { NbCardModule, NbIconModule, NbSelectModule, NbTooltipModule } from '@nebular/theme';
import { FormsModule } from '@angular/forms';
import { LazyLoadImageModule } from 'ng-lazyload-image';

@NgModule({
  declarations: [PopularsamplesComponent],
  imports: [
    CommonModule,
    NbCardModule,
    NbTooltipModule,
    NbIconModule,
    NbIconModule,
    FormsModule,
    NbSelectModule,
    LazyLoadImageModule
  ]
})
export class PopularsamplesModule { }

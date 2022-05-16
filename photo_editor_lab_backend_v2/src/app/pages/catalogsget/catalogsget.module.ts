/*
 * Optimumbrew Technology

 * Project       : Photo Editor Lab
 * File          : catalogsget.module.ts
 * File Created  : Friday, 16th October 2020 11:08:26 am
 * Author        : Optimumbrew
 * Auther Email  : info@optimumbrew.com
 * Last Modified : Thursday, 29th October 2020 11:02:26 am
 * -----
 * Copyright 2018 - 2020 Optimumbrew Technology
 */


import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { CatalogsgetComponent } from './catalogsget.component';
import { FormsModule } from '@angular/forms';
import { NbAccordionModule, NbBadgeModule, NbCardModule, NbIconModule, NbInputModule, NbRadioModule, NbSelectModule, NbTooltipModule } from '@nebular/theme';
import { LazyLoadImageModule } from 'ng-lazyload-image';

@NgModule({
  declarations: [CatalogsgetComponent],
  imports: [
    CommonModule,
    NbSelectModule,
    NbCardModule,
    NbIconModule,
    NbInputModule,
    NbRadioModule,
    FormsModule,
    NbTooltipModule,
    NbAccordionModule,
    NbBadgeModule,
    LazyLoadImageModule
  ]
})
export class CatalogsgetModule { }

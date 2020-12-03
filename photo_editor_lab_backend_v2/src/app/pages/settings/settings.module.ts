/*
 * Optimumbrew Technology

 * Project       : Photo Editor Lab
 * File          : settings.module.ts
 * File Created  : Monday, 26th October 2020 02:26:38 pm
 * Author        : Optimumbrew
 * Auther Email  : info@optimumbrew.com
 * Last Modified : Thursday, 29th October 2020 11:09:34 am
 * -----
 * Copyright 2018 - 2020 Optimumbrew Technology
 */


import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { SettingsComponent } from './settings.component';
import { NbCardModule, NbFormFieldModule, NbIconModule, NbInputModule, NbPopoverModule, NbSelectModule, NbTabsetModule, NbTooltipModule } from '@nebular/theme';
import { FormsModule } from '@angular/forms';
import { Ng2SmartTableModule } from 'ng2-smart-table';



@NgModule({
  declarations: [SettingsComponent],
  imports: [
    CommonModule,
    NbTabsetModule,
    FormsModule,
    NbCardModule,
    NbFormFieldModule,
    NbInputModule,
    NbIconModule,
    Ng2SmartTableModule,
    NbSelectModule,
    NbTooltipModule,
    NbPopoverModule
  ]
})
export class SettingsModule { }

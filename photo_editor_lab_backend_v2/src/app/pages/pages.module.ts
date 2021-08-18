/*
 * Optimumbrew Technology

 * Project       : Photo Editor Lab
 * File          : pages.module.ts
 * File Created  : Thursday, 15th October 2020 01:12:03 pm
 * Author        : Optimumbrew
 * Auther Email  : info@optimumbrew.com
 * Last Modified : Thursday, 29th October 2020 11:36:25 am
 * -----
 * Copyright 2018 - 2020 Optimumbrew Technology
 */


import { NgModule } from '@angular/core';
import { NbCardModule, NbMenuModule, NbWindowModule } from '@nebular/theme';

import { ThemeModule } from '../@theme/theme.module';
import { PagesComponent } from './pages.component';
import { DashboardModule } from './dashboard/dashboard.module';
import { PagesRoutingModule } from './pages-routing.module';
import { NbDialogModule } from '@nebular/theme';

import { CategoriesModule } from './categories/categories.module';
import { ViewcategoriesModule } from './viewcategories/viewcategories.module';
import { NgxPaginateModule } from 'ngx-paginate';
import { CatalogsgetModule } from './catalogsget/catalogsget.module';
import { ViewsubcategoriesModule } from './viewsubcategories/viewsubcategories.module';
import { FontlistModule } from './fontlist/fontlist.module';
import { PopularsamplesModule } from './popularsamples/popularsamples.module';
import { BloglistModule } from './bloglist/bloglist.module';

import { StatisticsModule } from './statistics/statistics.module';
import { ImagedetailsModule } from './imagedetails/imagedetails.module';
import { RediscacheModule } from './rediscache/rediscache.module';
import { SettingsModule } from './settings/settings.module';
import { SearchComponent } from './search/search.component';

@NgModule({
  imports: [
    PagesRoutingModule,
    ThemeModule,
    NbMenuModule,
    DashboardModule,
    CategoriesModule,
    NbDialogModule.forChild(),
    NbWindowModule.forChild(),
    NbCardModule,
    ViewcategoriesModule,
    NgxPaginateModule,
    CatalogsgetModule,
    ViewsubcategoriesModule,
    FontlistModule,
    PopularsamplesModule,
    BloglistModule,
    StatisticsModule,
    ImagedetailsModule,
    RediscacheModule,
    SettingsModule, 
  ],
  declarations: [
    PagesComponent,
    SearchComponent,
  ],
})
export class PagesModule {
}

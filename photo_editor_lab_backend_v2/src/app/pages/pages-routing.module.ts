/*
 * Optimumbrew Technology

 * Project       : Photo Editor Lab
 * File          : pages-routing.module.ts
 * File Created  : Thursday, 15th October 2020 01:12:03 pm
 * Author        : Optimumbrew
 * Auther Email  : info@optimumbrew.com
 * Last Modified : Thursday, 29th October 2020 11:36:00 am
 * -----
 * Copyright 2018 - 2020 Optimumbrew Technology
 */


import { RouterModule, Routes } from '@angular/router';
import { NgModule } from '@angular/core';

import { PagesComponent } from './pages.component';
import { DashboardComponent } from './dashboard/dashboard.component';
import { CategoriesComponent } from './categories/categories.component';
import { ViewcategoriesComponent } from './viewcategories/viewcategories.component';
import { CatalogsgetComponent } from './catalogsget/catalogsget.component';
import { ViewsubcategoriesComponent } from './viewsubcategories/viewsubcategories.component';
import { FontlistModule } from './fontlist/fontlist.module';
import { FontlistComponent } from './fontlist/fontlist.component';
import { PopularsamplesComponent } from './popularsamples/popularsamples.component';
import { BloglistComponent } from './bloglist/bloglist.component';
import { StatisticsComponent } from './statistics/statistics.component';
import { ImagedetailsComponent } from './imagedetails/imagedetails.component';
import { RediscacheComponent } from './rediscache/rediscache.component';
import { SettingsComponent } from './settings/settings.component';
import { SearchComponent } from './search/search.component';
import { ReviewDataComponent } from './review-data/review-data.component';
import { PostcalenderComponent } from './postcalender/postcalender.component';
import { AiPosterComponent } from './ai-poster/ai-poster.component';

const routes: Routes = [{
  path: '',
  component: PagesComponent,
  children: [
    // {
    //   path: 'dashboard',
    //   component: DashboardComponent,
    // },
    {
      path: '',
      component: CategoriesComponent
    },
    {
      path: 'categories',
      component: CategoriesComponent
    },
    {
      path: 'categories/:categoryId',
      component: ViewcategoriesComponent
    },
    {
      path: 'categories/:categoryId/:post-calendar',
      component: PostcalenderComponent
    },
    {
      path: 'categories/:categoryId/:subCategoryName/:subCategoryId',
      component: CatalogsgetComponent
    },
    {
      path: 'categories/:categoryId/:subCategoryName/:subCategoryId/:catalogName/:catalogId',
      component: ViewsubcategoriesComponent
    },
    {
      path: 'fonts/:categoryId/:subCategoryName/:subCategoryId/:catalogName/:catalogId',
      component: FontlistComponent
    },
    {
      path: 'popular/:categoryId/:subCategoryName/:subCategoryId/:catalogName/:catalogId',
      component: PopularsamplesComponent
    },
    {
      path: 'blog-list/:categoryId/:subCategoryName/:subCategoryId/:catalogName/:catalogId',
      component: BloglistComponent
    },
    {
      path: 'review',
      component: ReviewDataComponent
    },
    {
      path: 'poster',
      component: AiPosterComponent
    },
    {
      path: 'statistics',
      component: StatisticsComponent
    },
    {
      path: 'image-details',
      component: ImagedetailsComponent
    },
    {
      path: 'redis-cache',
      component: RediscacheComponent
    },
    {
      path: 'settings',
      component: SettingsComponent
    },
    {
      path:'search',
      component:SearchComponent
    }
  ]
}];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule],
})
export class PagesRoutingModule {
}

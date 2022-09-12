/*
 * Optimumbrew Technology

 * Project       : Photo Editor Lab
 * File          : app.module.ts
 * File Created  : Thursday, 15th October 2020 01:12:03 pm
 * Author        : Optimumbrew
 * Auther Email  : info@optimumbrew.com
 * Last Modified : Thursday, 29th October 2020 10:58:33 am
 * -----
 * Copyright 2018 - 2020 Optimumbrew Technology
 */



import { BrowserModule } from '@angular/platform-browser';
import { BrowserAnimationsModule } from '@angular/platform-browser/animations';
import { NgModule } from '@angular/core';
import { HttpClientModule } from '@angular/common/http';
import { CoreModule } from './@core/core.module';
import { ThemeModule } from './@theme/theme.module';
import { AppComponent } from './app.component';
import { AppRoutingModule } from './app-routing.module';
import { MatCheckboxModule } from '@angular/material/checkbox';
import { FormsModule, ReactiveFormsModule, } from '@angular/forms';
import { ViewimageComponent } from './components/viewimage/viewimage.component';
import { AddsearchtagsComponent } from './components/addsearchtags/addsearchtags.component';
import { AddsubcategoryComponent } from './components/addsubcategory/addsubcategory.component';
import { MatChipsModule } from '@angular/material/chips';
import {
  NbDatepickerModule,
  NbDialogModule,
  NbMenuModule,
  NbSidebarModule,
  NbWindowModule,
  NbCardModule,
  NbRadioModule,
  NbInputModule,
  NbAccordionModule,
  NbCheckboxModule,
  NbAutocompleteModule,
  NbSelectModule,
  NbIconModule,
  NbTooltipModule,
  NbButtonModule,
  NbToggleModule,
} from '@nebular/theme';

import { LoadingComponent } from './components/loading/loading.component';
import { Ng2SmartTableModule } from 'ng2-smart-table';
import { EditcategoryComponent } from './components/editcategory/editcategory.component';
import { AddcatalogComponent } from './components/addcatalog/addcatalog.component';
import { LinkcatalogComponent } from './components/linkcatalog/linkcatalog.component';
import { AddblogsComponent } from './components/addblogs/addblogs.component';
import { MovetocatalogComponent } from './components/movetocatalog/movetocatalog.component';
import { AddjsonimagesComponent } from './components/addjsonimages/addjsonimages.component';
import { ExistingimageslistComponent } from './components/existingimageslist/existingimageslist.component';
import { AddjsondataComponent } from './components/addjsondata/addjsondata.component';
import { UpdatesubcategoryimagebyidComponent } from './components/updatesubcategoryimagebyid/updatesubcategoryimagebyid.component';
import { AddsubcategoryimagesbyidComponent } from './components/addsubcategoryimagesbyid/addsubcategoryimagesbyid.component';
import { EditfontComponent } from './components/editfont/editfont.component';
import { PopularsampleaddComponent } from './components/popularsampleadd/popularsampleadd.component';
import { StatisticsDetailsComponent } from './components/statistics-details/statistics-details.component';
import { Daterangepicker } from 'ng2-daterangepicker';
import { EnterotpComponent } from './components/enterotp/enterotp.component';
import { NgOtpInputModule } from 'ng-otp-input';
import { AddvalidationsComponent } from './components/addvalidations/addvalidations.component';
import { AuthModule } from './auth/auth.module';
import { AddserverurlComponent } from './components/addserverurl/addserverurl.component';
import { ViewcorruptedfontsComponent } from './components/viewcorruptedfonts/viewcorruptedfonts.component';
import { NgxPaginationModule } from 'ngx-pagination';
import { AddMultipleTagsComponent } from './components/add-multiple-tags/add-multiple-tags.component';
import { MenubarComponent } from './components/menubar/menubar.component';
import { ViewCatalogTypeComponent } from './view-catalog-type/view-catalog-type.component';
@NgModule({
  declarations: [AppComponent, LoadingComponent, ViewimageComponent, AddsearchtagsComponent, AddsubcategoryComponent, EditcategoryComponent, AddcatalogComponent, LinkcatalogComponent, AddblogsComponent, MovetocatalogComponent, AddjsonimagesComponent, ExistingimageslistComponent, AddjsondataComponent, UpdatesubcategoryimagebyidComponent, AddsubcategoryimagesbyidComponent, EditfontComponent, PopularsampleaddComponent, StatisticsDetailsComponent, EnterotpComponent, AddvalidationsComponent, AddserverurlComponent, ViewcorruptedfontsComponent, AddMultipleTagsComponent, MenubarComponent, ViewCatalogTypeComponent],
  imports: [
    BrowserModule,
    BrowserAnimationsModule,
    HttpClientModule,
    AppRoutingModule,
    // MatFormFieldModule,
    // MatInputModule,
    // MatIconModule,
    // MatButtonModule,
    MatCheckboxModule,
    MatChipsModule,
    NbAutocompleteModule,
    FormsModule,
    ReactiveFormsModule,
    ThemeModule.forRoot(),

    NbSidebarModule.forRoot(),
    NbMenuModule.forRoot(),
    NbDatepickerModule.forRoot(),
    NbDialogModule.forRoot(),
    NbWindowModule.forRoot(),
    CoreModule.forRoot(),
    NbCardModule,
    NbRadioModule,
    NbInputModule,
    Ng2SmartTableModule,
    NbAccordionModule,
    NbCheckboxModule,
    NbSelectModule,
    Daterangepicker,
    NgOtpInputModule,
    NbIconModule,
    AuthModule,
    NbTooltipModule,
    NgxPaginationModule,
    NbButtonModule,
    NbToggleModule
  ],
  bootstrap: [AppComponent],
})
export class AppModule {
}

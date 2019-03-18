import { BrowserModule } from '@angular/platform-browser';
import { NgModule, CUSTOM_ELEMENTS_SCHEMA } from '@angular/core';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { HttpModule } from '@angular/http';
import { RouterModule } from '@angular/router';
import { BrowserAnimationsModule } from '@angular/platform-browser/animations';
import { MdButtonModule, MdInputModule, MdDialogModule, MdRadioModule, MdSidenavModule, MdSelectModule, MdTabsModule, MdCheckboxModule, MdSnackBarModule, MdAutocompleteModule, MdChipsModule, MdExpansionModule } from '@angular/material';
import { AngularFontAwesomeModule } from 'angular-font-awesome/angular-font-awesome';
import { NgbModule } from '@ng-bootstrap/ng-bootstrap';
import { Daterangepicker } from 'ng2-daterangepicker';
import { AppRoutes } from './app.routes';
import { DataService } from './data.service';
import { AuthenticationService } from './authentication.service';

import { AppComponent } from './app.component';
import { SizePipe } from './size.pipe';
import { LoginComponent } from './login/login.component';
import { LoadingComponent } from './loading/loading.component';
import { HeaderComponent } from './header/header.component';
import { NavigationComponent } from './navigation/navigation.component';
import { CategoriesComponent } from './categories/categories.component';
import { ViewCategoriesComponent } from './view-categories/view-categories.component';
import { UpdateCategoryComponent } from './update-category/update-category.component';
import { DeleteCategoryComponent } from './delete-category/delete-category.component';
import { AddCategoryComponent } from './add-category/add-category.component';
import { ImageDetailsComponent } from './image-details/image-details.component';
import { UsersAllComponent } from './users-all/users-all.component';
import { UsersPremiumComponent } from './users-premium/users-premium.component';
import { UsersRestoresComponent } from './users-restores/users-restores.component';
import { NotificationComponent } from './notification/notification.component';
import { SettingsComponent } from './settings/settings.component';
import { AddSubCategoryByCategoryIdComponent } from './add-sub-category-by-category-id/add-sub-category-by-category-id.component';
import { UpdateSubCategoryByCategoryIdComponent } from './update-sub-category-by-category-id/update-sub-category-by-category-id.component';
import { DeleteSubCategoryByCategoryIdComponent } from './delete-sub-category-by-category-id/delete-sub-category-by-category-id.component';
import { ViewSubcategoryComponent } from './view-subcategory/view-subcategory.component';
import { UpdateSubcategoryImageByIdComponent } from './update-subcategory-image-by-id/update-subcategory-image-by-id.component';
import { AddSubcategoryImagesByIdComponent } from './add-subcategory-images-by-id/add-subcategory-images-by-id.component';
import { DeleteSubcategoryImageByIdComponent } from './delete-subcategory-image-by-id/delete-subcategory-image-by-id.component';
import { CatalogsGetComponent } from './catalogs-get/catalogs-get.component';
import { CatalogsAddComponent } from './catalogs-add/catalogs-add.component';
import { CatalogsUpdateComponent } from './catalogs-update/catalogs-update.component';
import { CatalogsDeleteComponent } from './catalogs-delete/catalogs-delete.component';
import { AdvertisementsComponent } from './advertisements/advertisements.component';
import { AdvertisementsDeleteComponent } from './advertisements-delete/advertisements-delete.component';
import { AdvertisementsUpdateComponent } from './advertisements-update/advertisements-update.component';
import { AdvertisementsAddComponent } from './advertisements-add/advertisements-add.component';
import { PlatformFilterPipe } from './platform-filter.pipe';
import { RedisCacheComponent } from './redis-cache/redis-cache.component';
import { RedisCacheDeleteComponent } from './redis-cache-delete/redis-cache-delete.component';
import { LinkCatelogComponent } from './link-catelog/link-catelog.component';
import { PopularSamplesComponent } from './popular-samples/popular-samples.component';
import { PopularSamplesAddComponent } from './popular-samples-add/popular-samples-add.component';
import { PopularSamplesUpdateComponent } from './popular-samples-update/popular-samples-update.component';
import { AddJsonImagesComponent } from './add-json-images/add-json-images.component';
import { AddJsonDataComponent } from './add-json-data/add-json-data.component';
import { UpdateJsonDataComponent } from './update-json-data/update-json-data.component';
import { AdvertisementsLinkComponent } from './advertisements-link/advertisements-link.component';
import { AdvManagementComponent } from './adv-management/adv-management.component';
import { ViewImageComponent } from './view-image/view-image.component';
import { UserGeneratedDesignsComponent } from './user-generated-designs/user-generated-designs.component';
import { DeleteUserGeneratedComponent } from './delete-user-generated/delete-user-generated.component';
import { PromocodeManagementComponent } from './promocode-management/promocode-management.component';
import { PromocodeAddComponent } from './promocode-add/promocode-add.component';
import { ConfirmActionComponent } from './confirm-action/confirm-action.component';
import { AdmobAdsComponent } from './admob-ads/admob-ads.component';
import { GaAdsByCategoryComponent } from './ga-ads-by-category/ga-ads-by-category.component';
import { ExistingImagesListComponent } from './existing-images-list/existing-images-list.component';
import { SearchTagsComponent } from './search-tags/search-tags.component';
import { FontListComponent } from './font-list/font-list.component';
import { StatisticsComponent } from './statistics/statistics.component';
import { StatisticsDetailsComponent } from './statistics-details/statistics-details.component';

@NgModule({
  declarations: [
    AppComponent,
    SizePipe,
    LoginComponent,
    LoadingComponent,
    HeaderComponent,
    NavigationComponent,
    CategoriesComponent,
    ViewCategoriesComponent,
    UpdateCategoryComponent,
    DeleteCategoryComponent,
    AddCategoryComponent,
    ImageDetailsComponent,
    UsersAllComponent,
    UsersPremiumComponent,
    UsersRestoresComponent,
    NotificationComponent,
    SettingsComponent,
    AddSubCategoryByCategoryIdComponent,
    UpdateSubCategoryByCategoryIdComponent,
    DeleteSubCategoryByCategoryIdComponent,
    ViewSubcategoryComponent,
    UpdateSubcategoryImageByIdComponent,
    AddSubcategoryImagesByIdComponent,
    DeleteSubcategoryImageByIdComponent,
    CatalogsGetComponent,
    CatalogsAddComponent,
    CatalogsUpdateComponent,
    CatalogsDeleteComponent,
    AdvertisementsComponent,
    AdvertisementsDeleteComponent,
    AdvertisementsUpdateComponent,
    AdvertisementsAddComponent,
    PlatformFilterPipe,
    RedisCacheComponent,
    RedisCacheDeleteComponent,
    LinkCatelogComponent,
    PopularSamplesComponent,
    PopularSamplesAddComponent,
    PopularSamplesUpdateComponent,
    AddJsonImagesComponent,
    AddJsonDataComponent,
    UpdateJsonDataComponent,
    AdvertisementsLinkComponent,
    AdvManagementComponent,
    ViewImageComponent,
    UserGeneratedDesignsComponent,
    DeleteUserGeneratedComponent,
    PromocodeManagementComponent,
    PromocodeAddComponent,
    ConfirmActionComponent,
    AdmobAdsComponent,
    GaAdsByCategoryComponent,
    ExistingImagesListComponent,
    SearchTagsComponent,
    FontListComponent,
    StatisticsComponent,
    StatisticsDetailsComponent,
  ],
  imports: [
    BrowserModule,
    FormsModule,
    ReactiveFormsModule,
    HttpModule,
    BrowserAnimationsModule,
    AngularFontAwesomeModule,
    MdButtonModule,
    MdInputModule,
    MdDialogModule,
    MdRadioModule,
    MdSidenavModule,
    MdSelectModule,
    MdTabsModule,
    MdSnackBarModule,
    MdCheckboxModule,
    MdAutocompleteModule,
    MdChipsModule,
    MdExpansionModule,
    Daterangepicker,
    NgbModule.forRoot(),
    RouterModule.forRoot(AppRoutes, { useHash: true })
  ],
  entryComponents: [
    LoadingComponent,
    AddCategoryComponent,
    UpdateCategoryComponent,
    DeleteCategoryComponent,
    AddSubCategoryByCategoryIdComponent,
    UpdateSubCategoryByCategoryIdComponent,
    DeleteSubCategoryByCategoryIdComponent,
    UpdateSubcategoryImageByIdComponent,
    AddSubcategoryImagesByIdComponent,
    DeleteSubcategoryImageByIdComponent,
    CatalogsAddComponent,
    CatalogsUpdateComponent,
    CatalogsDeleteComponent,
    AdvertisementsDeleteComponent,
    AdvertisementsUpdateComponent,
    AdvertisementsAddComponent,
    RedisCacheDeleteComponent,
    LinkCatelogComponent,
    PopularSamplesAddComponent,
    PopularSamplesUpdateComponent,
    AddJsonImagesComponent,
    AddJsonDataComponent,
    UpdateJsonDataComponent,
    AdvertisementsLinkComponent,
    ViewImageComponent,
    DeleteUserGeneratedComponent,
    PromocodeAddComponent,
    ConfirmActionComponent,
    GaAdsByCategoryComponent,
    ExistingImagesListComponent,
    StatisticsDetailsComponent,
  ],
  providers: [DataService, AuthenticationService],
  bootstrap: [AppComponent],
  schemas: [CUSTOM_ELEMENTS_SCHEMA]
})
export class AppModule { }

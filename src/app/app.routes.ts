import { AuthenticationService } from './authentication.service';
import { LoginComponent } from './login/login.component';
import { CategoriesComponent } from './categories/categories.component';
import { ViewCategoriesComponent } from './view-categories/view-categories.component';
import { ViewSubcategoryComponent } from './view-subcategory/view-subcategory.component';
import { CatalogsGetComponent } from './catalogs-get/catalogs-get.component';
import { ImageDetailsComponent } from './image-details/image-details.component';
import { UsersAllComponent } from './users-all/users-all.component';
import { UsersPremiumComponent } from './users-premium/users-premium.component';
import { UsersRestoresComponent } from './users-restores/users-restores.component';
import { NotificationComponent } from './notification/notification.component';
import { AdvertisementsComponent } from './advertisements/advertisements.component';
import { RedisCacheComponent } from './redis-cache/redis-cache.component';
import { PopularSamplesComponent } from './popular-samples/popular-samples.component';
import { SettingsComponent } from './settings/settings.component';
import { AdvManagementComponent } from './adv-management/adv-management.component';
import { UserGeneratedDesignsComponent } from './user-generated-designs/user-generated-designs.component';

export const AppRoutes = [
    { path: '', component: LoginComponent, pathMatch: 'full' },
    { path: 'admin', component: LoginComponent },
    { path: 'admin/categories', component: CategoriesComponent, canActivate: [AuthenticationService] },
    { path: 'admin/categories/:categoryId', component: ViewCategoriesComponent, canActivate: [AuthenticationService] },
    { path: 'admin/categories/:categoryId/:subCategoryName/:subCategoryId', component: CatalogsGetComponent, canActivate: [AuthenticationService] },
    { path: 'admin/categories/:categoryId/:subCategoryName/:subCategoryId/:catalogName/:catalogId', component: ViewSubcategoryComponent, canActivate: [AuthenticationService] },
    { path: 'admin/popular/:categoryId/:subCategoryName/:subCategoryId/:catalogName/:catalogId', component: PopularSamplesComponent, canActivate: [AuthenticationService] },
    { path: 'admin/image-details', component: ImageDetailsComponent, canActivate: [AuthenticationService] },
    { path: 'admin/users/:sub_category_id', component: UsersAllComponent, canActivate: [AuthenticationService] },
    { path: 'admin/purchases/:sub_category_id', component: UsersPremiumComponent, canActivate: [AuthenticationService] },
    { path: 'admin/restores/:sub_category_id', component: UsersRestoresComponent, canActivate: [AuthenticationService] },
    { path: 'admin/notification/:sub_category_id', component: NotificationComponent, canActivate: [AuthenticationService] },
    { path: 'admin/advertisements/:sub_category_id', component: AdvertisementsComponent, canActivate: [AuthenticationService] },
    { path: 'admin/advertisements', component: AdvManagementComponent, canActivate: [AuthenticationService] },
    { path: 'admin/categories/:categoryId/user-designs', component: UserGeneratedDesignsComponent, canActivate: [AuthenticationService] },
    { path: 'admin/redis-cache', component: RedisCacheComponent, canActivate: [AuthenticationService] },
    { path: 'admin/settings', component: SettingsComponent, canActivate: [AuthenticationService] },
]
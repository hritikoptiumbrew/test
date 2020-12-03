/*
 * Optimumbrew Technology

 * Project       : Photo Editor Lab
 * File          : app-routing.module.ts
 * File Created  : Thursday, 15th October 2020 01:12:03 pm
 * Author        : Optimumbrew
 * Auther Email  : info@optimumbrew.com
 * Last Modified : Thursday, 29th October 2020 10:59:44 am
 * -----
 * Copyright 2018 - 2020 Optimumbrew Technology
 */


import { ExtraOptions, RouterModule, Routes } from '@angular/router';
import { NgModule } from '@angular/core';
import { LoginComponent } from './auth/login/login.component';


export const routes: Routes = [
  { path: '', component: LoginComponent ,pathMatch: 'full'},
  { path: 'auth', component:LoginComponent},
  { path:'admin', loadChildren: () => import('./pages/pages.module').then(m => m.PagesModule)},
  { path:'**', redirectTo: '' }
];

const config: ExtraOptions = {
  useHash: true,
};

@NgModule({
  imports: [RouterModule.forRoot(routes, config)],
  exports: [RouterModule],
})
export class AppRoutingModule {
}

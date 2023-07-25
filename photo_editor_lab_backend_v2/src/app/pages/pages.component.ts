/*
 * Optimumbrew Technology

 * Project       : Photo Editor Lab
 * File          : pages.component.ts
 * File Created  : Thursday, 15th October 2020 01:12:03 pm
 * Author        : Optimumbrew
 * Auther Email  : info@optimumbrew.com
 * Last Modified : Thursday, 29th October 2020 11:36:18 am
 * -----
 * Copyright 2018 - 2020 Optimumbrew Technology
 */


import { Component} from '@angular/core';
import { Router } from '@angular/router';
import { UtilService } from 'app/util.service';


import { MENU_ITEMS } from './pages-menu';

@Component({
  selector: 'ngx-pages',
  styleUrls: ['pages.component.scss'],
  template: `
    <ngx-one-column-layout>
      <nb-menu [items]="menu"></nb-menu>
      <router-outlet></router-outlet>
    </ngx-one-column-layout>
  `
})
export class PagesComponent {

  constructor(private route: Router,private utils: UtilService) {
    if (!localStorage.getItem('at')) {
      this.route.navigate(['']);
    }
    var that = this;
    window.onhashchange = function() {
        that.utils.hidePageDialog();
     }
  }

  menu = MENU_ITEMS;


}

/*
 * Optimumbrew Technology

 * Project       : Photo Editor Lab
 * File          : footer.component.ts
 * File Created  : Thursday, 15th October 2020 01:12:03 pm
 * Author        : Optimumbrew
 * Auther Email  : info@optimumbrew.com
 * Last Modified : Thursday, 29th October 2020 11:41:48 am
 * -----
 * Copyright 2018 - 2020 Optimumbrew Technology
 */


import { Component } from '@angular/core';

@Component({
  selector: 'ngx-footer',
  styleUrls: ['./footer.component.scss'],
  template: `
    <span class="created-by">
    Copyright &copy; 2019-2020 <a href="">Optimumbrew Technology</a> all rights reserved
    </span>
  `,
})
export class FooterComponent {
}

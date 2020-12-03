/*
 * Optimumbrew Technology

 * Project       : Photo Editor Lab
 * File          : capitalize.pipe.ts
 * File Created  : Thursday, 15th October 2020 01:12:03 pm
 * Author        : Optimumbrew
 * Auther Email  : info@optimumbrew.com
 * Last Modified : Thursday, 29th October 2020 11:39:41 am
 * -----
 * Copyright 2018 - 2020 Optimumbrew Technology
 */


import { Pipe, PipeTransform } from '@angular/core';

@Pipe({ name: 'ngxCapitalize' })
export class CapitalizePipe implements PipeTransform {

  transform(input: string): string {
    return input && input.length
      ? (input.charAt(0).toUpperCase() + input.slice(1).toLowerCase())
      : input;
  }
}

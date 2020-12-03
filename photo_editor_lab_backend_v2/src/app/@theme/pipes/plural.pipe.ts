/*
 * Optimumbrew Technology

 * Project       : Photo Editor Lab
 * File          : plural.pipe.ts
 * File Created  : Thursday, 15th October 2020 01:12:03 pm
 * Author        : Optimumbrew
 * Auther Email  : info@optimumbrew.com
 * Last Modified : Thursday, 29th October 2020 11:39:50 am
 * -----
 * Copyright 2018 - 2020 Optimumbrew Technology
 */


import { Pipe, PipeTransform } from '@angular/core';

@Pipe({ name: 'ngxPlural' })
export class PluralPipe implements PipeTransform {

  transform(input: number, label: string, pluralLabel: string = ''): string {
    input = input || 0;
    return input === 1
      ? `${input} ${label}`
      : pluralLabel
        ? `${input} ${pluralLabel}`
        : `${input} ${label}s`;
  }
}

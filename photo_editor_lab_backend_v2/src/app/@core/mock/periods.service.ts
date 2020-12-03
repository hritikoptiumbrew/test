/*
 * Optimumbrew Technology

 * Project       : Photo Editor Lab
 * File          : periods.service.ts
 * File Created  : Thursday, 15th October 2020 01:12:03 pm
 * Author        : Optimumbrew
 * Auther Email  : info@optimumbrew.com
 * Last Modified : Thursday, 29th October 2020 11:43:00 am
 * -----
 * Copyright 2018 - 2020 Optimumbrew Technology
 */


import { Injectable } from '@angular/core';

@Injectable()
export class PeriodsService {
  getYears() {
    return [
      '2010', '2011', '2012',
      '2013', '2014', '2015',
      '2016', '2017', '2018',
    ];
  }

  getMonths() {
    return [
      'Jan', 'Feb', 'Mar',
      'Apr', 'May', 'Jun',
      'Jul', 'Aug', 'Sep',
      'Oct', 'Nov', 'Dec',
    ];
  }

  getWeeks() {
    return [
      'Mon',
      'Tue',
      'Wed',
      'Thu',
      'Fri',
      'Sat',
      'Sun',
    ];
  }
}

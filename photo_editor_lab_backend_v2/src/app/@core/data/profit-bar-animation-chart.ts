/*
 * Optimumbrew Technology

 * Project       : Photo Editor Lab
 * File          : profit-bar-animation-chart.ts
 * File Created  : Thursday, 15th October 2020 01:12:03 pm
 * Author        : Optimumbrew
 * Auther Email  : info@optimumbrew.com
 * Last Modified : Thursday, 29th October 2020 11:44:48 am
 * -----
 * Copyright 2018 - 2020 Optimumbrew Technology
 */


import { Observable } from 'rxjs';

export abstract class ProfitBarAnimationChartData {
  abstract getChartData(): Observable<{ firstLine: number[]; secondLine: number[]; }>;
}

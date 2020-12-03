/*
 * Optimumbrew Technology

 * Project       : Photo Editor Lab
 * File          : electricity.ts
 * File Created  : Thursday, 15th October 2020 01:12:03 pm
 * Author        : Optimumbrew
 * Auther Email  : info@optimumbrew.com
 * Last Modified : Thursday, 29th October 2020 11:44:38 am
 * -----
 * Copyright 2018 - 2020 Optimumbrew Technology
 */


import { Observable } from 'rxjs';

export interface Month {
  month: string;
  delta: string;
  down: boolean;
  kWatts: string;
  cost: string;
}

export interface Electricity {
  title: string;
  active?: boolean;
  months: Month[];
}

export interface ElectricityChart {
  label: string;
  value: number;
}

export abstract class ElectricityData {
  abstract getListData(): Observable<Electricity[]>;
  abstract getChartData(): Observable<ElectricityChart[]>;
}

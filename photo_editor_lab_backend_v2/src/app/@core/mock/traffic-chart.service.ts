/*
 * Optimumbrew Technology

 * Project       : Photo Editor Lab
 * File          : traffic-chart.service.ts
 * File Created  : Thursday, 15th October 2020 01:12:03 pm
 * Author        : Optimumbrew
 * Auther Email  : info@optimumbrew.com
 * Last Modified : Thursday, 29th October 2020 11:44:04 am
 * -----
 * Copyright 2018 - 2020 Optimumbrew Technology
 */


import { Injectable } from '@angular/core';
import { of as observableOf,  Observable } from 'rxjs';
import { TrafficChartData } from '../data/traffic-chart';

@Injectable()
export class TrafficChartService extends TrafficChartData {

  private data: number[] = [
    300, 520, 435, 530,
    730, 620, 660, 860,
  ];

  getTrafficChartData(): Observable<number[]> {
    return observableOf(this.data);
  }
}

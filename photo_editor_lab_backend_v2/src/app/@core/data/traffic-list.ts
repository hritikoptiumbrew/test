/*
 * Optimumbrew Technology

 * Project       : Photo Editor Lab
 * File          : traffic-list.ts
 * File Created  : Thursday, 15th October 2020 01:12:03 pm
 * Author        : Optimumbrew
 * Auther Email  : info@optimumbrew.com
 * Last Modified : Thursday, 29th October 2020 11:45:23 am
 * -----
 * Copyright 2018 - 2020 Optimumbrew Technology
 */


import { Observable } from 'rxjs';

export interface TrafficList {
  date: string;
  value: number;
  delta: {
    up: boolean;
    value: number;
  };
  comparison: {
    prevDate: string;
    prevValue: number;
    nextDate: string;
    nextValue: number;
  };
}

export abstract class TrafficListData {
  abstract getTrafficListData(period: string): Observable<TrafficList>;
}

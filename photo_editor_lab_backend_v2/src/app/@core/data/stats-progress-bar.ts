/*
 * Optimumbrew Technology

 * Project       : Photo Editor Lab
 * File          : stats-progress-bar.ts
 * File Created  : Thursday, 15th October 2020 01:12:03 pm
 * Author        : Optimumbrew
 * Auther Email  : info@optimumbrew.com
 * Last Modified : Thursday, 29th October 2020 11:45:08 am
 * -----
 * Copyright 2018 - 2020 Optimumbrew Technology
 */


import { Observable } from 'rxjs';

export interface ProgressInfo {
  title: string;
  value: number;
  activeProgress: number;
  description: string;
}

export abstract class StatsProgressBarData {
  abstract getProgressInfoData(): Observable<ProgressInfo[]>;
}

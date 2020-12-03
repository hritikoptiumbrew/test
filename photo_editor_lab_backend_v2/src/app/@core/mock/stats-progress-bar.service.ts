/*
 * Optimumbrew Technology

 * Project       : Photo Editor Lab
 * File          : stats-progress-bar.service.ts
 * File Created  : Thursday, 15th October 2020 01:12:03 pm
 * Author        : Optimumbrew
 * Auther Email  : info@optimumbrew.com
 * Last Modified : Thursday, 29th October 2020 11:43:54 am
 * -----
 * Copyright 2018 - 2020 Optimumbrew Technology
 */


import { Injectable } from '@angular/core';
import { of as observableOf, Observable } from 'rxjs';
import { ProgressInfo, StatsProgressBarData } from '../data/stats-progress-bar';

@Injectable()
export class StatsProgressBarService extends StatsProgressBarData {
  private progressInfoData: ProgressInfo[] = [
    {
      title: 'Today’s Profit',
      value: 572900,
      activeProgress: 70,
      description: 'Better than last week (70%)',
    },
    {
      title: 'New Orders',
      value: 6378,
      activeProgress: 30,
      description: 'Better than last week (30%)',
    },
    {
      title: 'New Comments',
      value: 200,
      activeProgress: 55,
      description: 'Better than last week (55%)',
    },
  ];

  getProgressInfoData(): Observable<ProgressInfo[]> {
    return observableOf(this.progressInfoData);
  }
}

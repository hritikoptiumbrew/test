/*
 * Optimumbrew Technology

 * Project       : Photo Editor Lab
 * File          : profit-bar-animation-chart.service.ts
 * File Created  : Thursday, 15th October 2020 01:12:03 pm
 * Author        : Optimumbrew
 * Auther Email  : info@optimumbrew.com
 * Last Modified : Thursday, 29th October 2020 11:43:09 am
 * -----
 * Copyright 2018 - 2020 Optimumbrew Technology
 */


import { Injectable } from '@angular/core';
import { of as observableOf,  Observable } from 'rxjs';
import { ProfitBarAnimationChartData } from '../data/profit-bar-animation-chart';

@Injectable()
export class ProfitBarAnimationChartService extends ProfitBarAnimationChartData {

  private data: any;

  constructor() {
    super();
    this.data = {
      firstLine: this.getDataForFirstLine(),
      secondLine: this.getDataForSecondLine(),
    };
  }

  getDataForFirstLine(): number[] {
    return this.createEmptyArray(100)
      .map((_, index) => {
        const oneFifth = index / 5;

        return (Math.sin(oneFifth) * (oneFifth - 10) + index / 6) * 5;
      });
  }

  getDataForSecondLine(): number[] {
    return this.createEmptyArray(100)
      .map((_, index) => {
        const oneFifth = index / 5;

        return (Math.cos(oneFifth) * (oneFifth - 10) + index / 6) * 5;
      });
  }

  createEmptyArray(nPoints: number) {
    return Array.from(Array(nPoints));
  }

  getChartData(): Observable<{ firstLine: number[]; secondLine: number[]; }> {
    return observableOf(this.data);
  }
}

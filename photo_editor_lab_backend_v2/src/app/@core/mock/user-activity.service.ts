/*
 * Optimumbrew Technology

 * Project       : Photo Editor Lab
 * File          : user-activity.service.ts
 * File Created  : Thursday, 15th October 2020 01:12:03 pm
 * Author        : Optimumbrew
 * Auther Email  : info@optimumbrew.com
 * Last Modified : Thursday, 29th October 2020 11:44:11 am
 * -----
 * Copyright 2018 - 2020 Optimumbrew Technology
 */


import { Injectable } from '@angular/core';
import { of as observableOf,  Observable } from 'rxjs';
import { PeriodsService } from './periods.service';
import { UserActive, UserActivityData } from '../data/user-activity';

@Injectable()
export class UserActivityService extends UserActivityData {

  private getRandom = (roundTo: number) => Math.round(Math.random() * roundTo);
  private generateUserActivityRandomData(date) {
    return {
      date,
      pagesVisitCount: this.getRandom(1000),
      deltaUp: this.getRandom(1) % 2 === 0,
      newVisits: this.getRandom(100),
    };
  }

  data = {};

  constructor(private periods: PeriodsService) {
    super();
    this.data = {
      week: this.getDataWeek(),
      month: this.getDataMonth(),
      year: this.getDataYear(),
    };
  }

  private getDataWeek(): UserActive[] {
    return this.periods.getWeeks().map((week) => {
      return this.generateUserActivityRandomData(week);
    });
  }

  private getDataMonth(): UserActive[] {
    const currentDate = new Date();
    const days = currentDate.getDate();
    const month = this.periods.getMonths()[currentDate.getMonth()];

    return Array.from(Array(days)).map((_, index) => {
      const date = `${index + 1} ${month}`;

      return this.generateUserActivityRandomData(date);
    });
  }

  private getDataYear(): UserActive[] {
    return this.periods.getYears().map((year) => {
      return this.generateUserActivityRandomData(year);
    });
  }

  getUserActivityData(period: string): Observable<UserActive[]> {
    return observableOf(this.data[period]);
  }
}

/*
 * Optimumbrew Technology

 * Project       : Photo Editor Lab
 * File          : user-activity.ts
 * File Created  : Thursday, 15th October 2020 01:12:03 pm
 * Author        : Optimumbrew
 * Auther Email  : info@optimumbrew.com
 * Last Modified : Thursday, 29th October 2020 11:45:27 am
 * -----
 * Copyright 2018 - 2020 Optimumbrew Technology
 */


import { Observable } from 'rxjs';

export interface UserActive {
  date: string;
  pagesVisitCount: number;
  deltaUp: boolean;
  newVisits: number;
}

export abstract class UserActivityData {
  abstract getUserActivityData(period: string): Observable<UserActive[]>;
}

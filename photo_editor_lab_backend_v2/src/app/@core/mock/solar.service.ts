/*
 * Optimumbrew Technology

 * Project       : Photo Editor Lab
 * File          : solar.service.ts
 * File Created  : Thursday, 15th October 2020 01:12:03 pm
 * Author        : Optimumbrew
 * Auther Email  : info@optimumbrew.com
 * Last Modified : Thursday, 29th October 2020 11:43:47 am
 * -----
 * Copyright 2018 - 2020 Optimumbrew Technology
 */


import { Injectable } from '@angular/core';
import { of as observableOf,  Observable } from 'rxjs';
import { SolarData } from '../data/solar';

@Injectable()
export class SolarService extends SolarData {
  private value = 42;

  getSolarData(): Observable<number> {
    return observableOf(this.value);
  }
}

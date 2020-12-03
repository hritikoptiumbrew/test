/*
 * Optimumbrew Technology

 * Project       : Photo Editor Lab
 * File          : temperature-humidity.ts
 * File Created  : Thursday, 15th October 2020 01:12:03 pm
 * Author        : Optimumbrew
 * Auther Email  : info@optimumbrew.com
 * Last Modified : Thursday, 29th October 2020 11:45:12 am
 * -----
 * Copyright 2018 - 2020 Optimumbrew Technology
 */


import { Observable } from 'rxjs';

export interface Temperature {
  value: number;
  min: number;
  max: number;
}

export abstract class TemperatureHumidityData {
  abstract getTemperatureData(): Observable<Temperature>;
  abstract getHumidityData(): Observable<Temperature>;
}

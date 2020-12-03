/*
 * Optimumbrew Technology

 * Project       : Photo Editor Lab
 * File          : temperature-humidity.service.ts
 * File Created  : Thursday, 15th October 2020 01:12:03 pm
 * Author        : Optimumbrew
 * Auther Email  : info@optimumbrew.com
 * Last Modified : Thursday, 29th October 2020 11:43:57 am
 * -----
 * Copyright 2018 - 2020 Optimumbrew Technology
 */


import { Injectable } from '@angular/core';
import { of as observableOf,  Observable } from 'rxjs';
import { TemperatureHumidityData, Temperature } from '../data/temperature-humidity';

@Injectable()
export class TemperatureHumidityService extends TemperatureHumidityData {

  private temperatureDate: Temperature = {
    value: 24,
    min: 12,
    max: 30,
  };

  private humidityDate: Temperature = {
    value: 87,
    min: 0,
    max: 100,
  };

  getTemperatureData(): Observable<Temperature> {
    return observableOf(this.temperatureDate);
  }

  getHumidityData(): Observable<Temperature> {
    return observableOf(this.humidityDate);
  }
}

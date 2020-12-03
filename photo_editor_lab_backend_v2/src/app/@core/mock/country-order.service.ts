/*
 * Optimumbrew Technology

 * Project       : Photo Editor Lab
 * File          : country-order.service.ts
 * File Created  : Thursday, 15th October 2020 01:12:03 pm
 * Author        : Optimumbrew
 * Auther Email  : info@optimumbrew.com
 * Last Modified : Thursday, 29th October 2020 11:42:41 am
 * -----
 * Copyright 2018 - 2020 Optimumbrew Technology
 */


import { Injectable } from '@angular/core';
import { of as observableOf, Observable } from 'rxjs';
import { CountryOrderData } from '../data/country-order';

@Injectable()
export class CountryOrderService extends CountryOrderData {

  private countriesCategories = [
    'Sofas',
    'Furniture',
    'Lighting',
    'Tables',
    'Textiles',
  ];
  private countriesCategoriesLength = this.countriesCategories.length;
  private generateRandomData(nPoints: number): number[] {
    return Array.from(Array(nPoints)).map(() => {
      return Math.round(Math.random() * 20);
    });
  }

  getCountriesCategories(): Observable<string[]> {
    return observableOf(this.countriesCategories);
  }

  getCountriesCategoriesData(country: string): Observable<number[]> {
    return observableOf(this.generateRandomData(this.countriesCategoriesLength));
  }
}

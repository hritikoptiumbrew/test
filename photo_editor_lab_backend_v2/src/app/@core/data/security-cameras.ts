/*
 * Optimumbrew Technology

 * Project       : Photo Editor Lab
 * File          : security-cameras.ts
 * File Created  : Thursday, 15th October 2020 01:12:03 pm
 * Author        : Optimumbrew
 * Auther Email  : info@optimumbrew.com
 * Last Modified : Thursday, 29th October 2020 11:44:54 am
 * -----
 * Copyright 2018 - 2020 Optimumbrew Technology
 */


import { Observable } from 'rxjs';

export interface Camera {
  title: string;
  source: string;
}

export abstract class SecurityCamerasData {
  abstract getCamerasData(): Observable<Camera[]>;
}

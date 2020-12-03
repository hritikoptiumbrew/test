/*
 * Optimumbrew Technology

 * Project       : Photo Editor Lab
 * File          : round.pipe.ts
 * File Created  : Thursday, 15th October 2020 01:12:03 pm
 * Author        : Optimumbrew
 * Auther Email  : info@optimumbrew.com
 * Last Modified : Thursday, 29th October 2020 11:39:53 am
 * -----
 * Copyright 2018 - 2020 Optimumbrew Technology
 */


import { Pipe, PipeTransform } from '@angular/core';

@Pipe({ name: 'ngxRound' })
export class RoundPipe implements PipeTransform {

  transform(input: number): number {
    return Math.round(input);
  }
}

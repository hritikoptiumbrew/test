/*
 * Optimumbrew Technology

 * Project       : Photo Editor Lab
 * File          : timing.pipe.ts
 * File Created  : Thursday, 15th October 2020 01:12:03 pm
 * Author        : Optimumbrew
 * Auther Email  : info@optimumbrew.com
 * Last Modified : Thursday, 29th October 2020 11:39:57 am
 * -----
 * Copyright 2018 - 2020 Optimumbrew Technology
 */


import { Pipe, PipeTransform } from '@angular/core';

@Pipe({ name: 'timing' })
export class TimingPipe implements PipeTransform {
  transform(time: number): string {
    if (time) {
      const minutes = Math.floor(time / 60);
      const seconds = Math.floor(time % 60);
      return `${this.initZero(minutes)}${minutes}:${this.initZero(seconds)}${seconds}`;
    }

    return '00:00';
  }

  private initZero(time: number): string {
    return time < 10 ? '0' : '';
  }
}

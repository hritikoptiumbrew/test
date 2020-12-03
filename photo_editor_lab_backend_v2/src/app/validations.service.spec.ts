/*
 * Optimumbrew Technology

 * Project       : Photo Editor Lab
 * File          : validations.service.spec.ts
 * File Created  : Thursday, 15th October 2020 01:12:03 pm
 * Author        : Optimumbrew
 * Auther Email  : info@optimumbrew.com
 * Last Modified : Thursday, 29th October 2020 10:56:16 am
 * -----
 * Copyright 2018 - 2020 Optimumbrew Technology
 */


import { TestBed } from '@angular/core/testing';

import { ValidationsService } from './validations.service';

describe('ValidationsService', () => {
  let service: ValidationsService;

  beforeEach(() => {
    TestBed.configureTestingModule({});
    service = TestBed.inject(ValidationsService);
  });

  it('should be created', () => {
    expect(service).toBeTruthy();
  });
});

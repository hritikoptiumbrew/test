/*
 * Optimumbrew Technology

 * Project       : Photo Editor Lab
 * File          : statistics.component.spec.ts
 * File Created  : Friday, 23rd October 2020 01:09:15 pm
 * Author        : Optimumbrew
 * Auther Email  : info@optimumbrew.com
 * Last Modified : Friday, 23rd October 2020 01:14:07 pm
 * -----
 * Copyright 2018 - 2020 Optimumbrew Technology
 */


import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { StatisticsComponent } from './statistics.component';

describe('StatisticsComponent', () => {
  let component: StatisticsComponent;
  let fixture: ComponentFixture<StatisticsComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [StatisticsComponent]
    })
      .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(StatisticsComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});

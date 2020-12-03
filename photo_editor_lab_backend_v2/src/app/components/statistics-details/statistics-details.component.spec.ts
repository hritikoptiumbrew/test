/*
 * Optimumbrew Technology

 * Project       : Photo Editor Lab
 * File          : statistics-details.component.spec.ts
 * File Created  : Saturday, 24th October 2020 11:19:12 am
 * Author        : Optimumbrew
 * Auther Email  : info@optimumbrew.com
 * Last Modified : Thursday, 29th October 2020 11:28:22 am
 * -----
 * Copyright 2018 - 2020 Optimumbrew Technology
 */


import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { StatisticsDetailsComponent } from './statistics-details.component';

describe('StatisticsDetailsComponent', () => {
  let component: StatisticsDetailsComponent;
  let fixture: ComponentFixture<StatisticsDetailsComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [StatisticsDetailsComponent]
    })
      .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(StatisticsDetailsComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});

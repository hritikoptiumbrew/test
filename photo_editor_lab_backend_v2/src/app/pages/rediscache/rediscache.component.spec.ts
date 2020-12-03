/*
 * Optimumbrew Technology

 * Project       : Photo Editor Lab
 * File          : rediscache.component.spec.ts
 * File Created  : Monday, 26th October 2020 10:58:28 am
 * Author        : Optimumbrew
 * Auther Email  : info@optimumbrew.com
 * Last Modified : Monday, 26th October 2020 11:02:10 am
 * -----
 * Copyright 2018 - 2020 Optimumbrew Technology
 */


import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { RediscacheComponent } from './rediscache.component';

describe('RediscacheComponent', () => {
  let component: RediscacheComponent;
  let fixture: ComponentFixture<RediscacheComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [RediscacheComponent]
    })
      .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(RediscacheComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});

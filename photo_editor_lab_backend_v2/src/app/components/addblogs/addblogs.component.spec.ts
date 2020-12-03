/*
 * Optimumbrew Technology

 * Project       : Photo Editor Lab
 * File          : addblogs.component.spec.ts
 * File Created  : Monday, 19th October 2020 02:03:07 pm
 * Author        : Optimumbrew
 * Auther Email  : info@optimumbrew.com
 * Last Modified : Thursday, 29th October 2020 11:15:29 am
 * -----
 * Copyright 2018 - 2020 Optimumbrew Technology
 */


import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { AddblogsComponent } from './addblogs.component';

describe('AddblogsComponent', () => {
  let component: AddblogsComponent;
  let fixture: ComponentFixture<AddblogsComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [AddblogsComponent]
    })
      .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(AddblogsComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});

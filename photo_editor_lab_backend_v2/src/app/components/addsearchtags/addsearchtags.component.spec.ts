/*
 * Optimumbrew Technology

 * Project       : Photo Editor Lab
 * File          : addsearchtags.component.spec.ts
 * File Created  : Thursday, 15th October 2020 01:12:03 pm
 * Author        : Optimumbrew
 * Auther Email  : info@optimumbrew.com
 * Last Modified : Thursday, 29th October 2020 11:18:11 am
 * -----
 * Copyright 2018 - 2020 Optimumbrew Technology
 */


import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { AddsearchtagsComponent } from './addsearchtags.component';

describe('AddsearchtagsComponent', () => {
  let component: AddsearchtagsComponent;
  let fixture: ComponentFixture<AddsearchtagsComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [AddsearchtagsComponent]
    })
      .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(AddsearchtagsComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});

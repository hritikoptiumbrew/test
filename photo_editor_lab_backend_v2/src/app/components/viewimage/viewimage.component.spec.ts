/*
 * Optimumbrew Technology

 * Project       : Photo Editor Lab
 * File          : viewimage.component.spec.ts
 * File Created  : Thursday, 15th October 2020 01:12:03 pm
 * Author        : Optimumbrew
 * Auther Email  : info@optimumbrew.com
 * Last Modified : Thursday, 29th October 2020 11:29:54 am
 * -----
 * Copyright 2018 - 2020 Optimumbrew Technology
 */


import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ViewimageComponent } from './viewimage.component';

describe('ViewimageComponent', () => {
  let component: ViewimageComponent;
  let fixture: ComponentFixture<ViewimageComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ViewimageComponent]
    })
      .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ViewimageComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});

/*
 * Optimumbrew Technology

 * Project       : Photo Editor Lab
 * File          : imagedetails.component.spec.ts
 * File Created  : Saturday, 24th October 2020 03:56:00 pm
 * Author        : Optimumbrew
 * Auther Email  : info@optimumbrew.com
 * Last Modified : Saturday, 24th October 2020 04:06:36 pm
 * -----
 * Copyright 2018 - 2020 Optimumbrew Technology
 */


import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ImagedetailsComponent } from './imagedetails.component';

describe('ImagedetailsComponent', () => {
  let component: ImagedetailsComponent;
  let fixture: ComponentFixture<ImagedetailsComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ImagedetailsComponent]
    })
      .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ImagedetailsComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});

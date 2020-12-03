/*
 * Optimumbrew Technology

 * Project       : Photo Editor Lab
 * File          : viewsubcategories.component.spec.ts
 * File Created  : Monday, 19th October 2020 11:58:13 am
 * Author        : Optimumbrew
 * Auther Email  : info@optimumbrew.com
 * Last Modified : Thursday, 29th October 2020 11:13:04 am
 * -----
 * Copyright 2018 - 2020 Optimumbrew Technology
 */


import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ViewsubcategoriesComponent } from './viewsubcategories.component';

describe('ViewsubcategoriesComponent', () => {
  let component: ViewsubcategoriesComponent;
  let fixture: ComponentFixture<ViewsubcategoriesComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ViewsubcategoriesComponent]
    })
      .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ViewsubcategoriesComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});

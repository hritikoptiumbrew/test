/*
 * Optimumbrew Technology

 * Project       : Photo Editor Lab
 * File          : editcategory.component.spec.ts
 * File Created  : Saturday, 17th October 2020 11:08:43 am
 * Author        : Optimumbrew
 * Auther Email  : info@optimumbrew.com
 * Last Modified : Thursday, 29th October 2020 11:21:52 am
 * -----
 * Copyright 2018 - 2020 Optimumbrew Technology
 */

import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { EditcategoryComponent } from './editcategory.component';

describe('EditcategoryComponent', () => {
  let component: EditcategoryComponent;
  let fixture: ComponentFixture<EditcategoryComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [EditcategoryComponent]
    })
      .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(EditcategoryComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});

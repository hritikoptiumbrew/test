/*
 * Optimumbrew Technology

 * Project       : Photo Editor Lab
 * File          : addsubcategoryimagesbyid.component.spec.ts
 * File Created  : Thursday, 22nd October 2020 11:50:20 am
 * Author        : Optimumbrew
 * Auther Email  : info@optimumbrew.com
 * Last Modified : Thursday, 22nd October 2020 11:52:30 am
 * -----
 * Copyright 2018 - 2020 Optimumbrew Technology
 */


import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { AddsubcategoryimagesbyidComponent } from './addsubcategoryimagesbyid.component';

describe('AddsubcategoryimagesbyidComponent', () => {
  let component: AddsubcategoryimagesbyidComponent;
  let fixture: ComponentFixture<AddsubcategoryimagesbyidComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [AddsubcategoryimagesbyidComponent]
    })
      .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(AddsubcategoryimagesbyidComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});

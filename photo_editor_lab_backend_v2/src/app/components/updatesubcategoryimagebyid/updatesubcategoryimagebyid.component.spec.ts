/*
 * Optimumbrew Technology

 * Project       : Photo Editor Lab
 * File          : updatesubcategoryimagebyid.component.spec.ts
 * File Created  : Thursday, 22nd October 2020 10:39:47 am
 * Author        : Optimumbrew
 * Auther Email  : info@optimumbrew.com
 * Last Modified : Thursday, 29th October 2020 11:29:16 am
 * -----
 * Copyright 2018 - 2020 Optimumbrew Technology
 */


import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { UpdatesubcategoryimagebyidComponent } from './updatesubcategoryimagebyid.component';

describe('UpdatesubcategoryimagebyidComponent', () => {
  let component: UpdatesubcategoryimagebyidComponent;
  let fixture: ComponentFixture<UpdatesubcategoryimagebyidComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [UpdatesubcategoryimagebyidComponent]
    })
      .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(UpdatesubcategoryimagebyidComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});

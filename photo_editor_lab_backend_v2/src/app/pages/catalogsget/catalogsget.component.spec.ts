/*
 * Optimumbrew Technology

 * Project       : Photo Editor Lab
 * File          : catalogsget.component.spec.ts
 * File Created  : Friday, 16th October 2020 11:08:55 am
 * Author        : Optimumbrew
 * Auther Email  : info@optimumbrew.com
 * Last Modified : Thursday, 29th October 2020 11:01:41 am
 * -----
 * Copyright 2018 - 2020 Optimumbrew Technology
 */


import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { CatalogsgetComponent } from './catalogsget.component';

describe('CatalogsgetComponent', () => {
  let component: CatalogsgetComponent;
  let fixture: ComponentFixture<CatalogsgetComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [CatalogsgetComponent]
    })
      .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(CatalogsgetComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});

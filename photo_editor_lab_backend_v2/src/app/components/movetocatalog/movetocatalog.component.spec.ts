/*
 * Optimumbrew Technology

 * Project       : Photo Editor Lab
 * File          : movetocatalog.component.spec.ts
 * File Created  : Monday, 19th October 2020 05:10:28 pm
 * Author        : Optimumbrew
 * Auther Email  : info@optimumbrew.com
 * Last Modified : Thursday, 29th October 2020 11:26:56 am
 * -----
 * Copyright 2018 - 2020 Optimumbrew Technology
 */


import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { MovetocatalogComponent } from './movetocatalog.component';

describe('MovetocatalogComponent', () => {
  let component: MovetocatalogComponent;
  let fixture: ComponentFixture<MovetocatalogComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [MovetocatalogComponent]
    })
      .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(MovetocatalogComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});

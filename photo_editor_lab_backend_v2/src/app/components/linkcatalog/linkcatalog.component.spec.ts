/*
 * Optimumbrew Technology

 * Project       : Photo Editor Lab
 * File          : linkcatalog.component.spec.ts
 * File Created  : Monday, 19th October 2020 09:32:59 am
 * Author        : Optimumbrew
 * Auther Email  : info@optimumbrew.com
 * Last Modified : Thursday, 29th October 2020 11:25:42 am
 * -----
 * Copyright 2018 - 2020 Optimumbrew Technology
 */


import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { LinkcatalogComponent } from './linkcatalog.component';

describe('LinkcatalogComponent', () => {
  let component: LinkcatalogComponent;
  let fixture: ComponentFixture<LinkcatalogComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [LinkcatalogComponent]
    })
      .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(LinkcatalogComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});

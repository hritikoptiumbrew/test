/*
 * Optimumbrew Technology

 * Project       : Photo Editor Lab
 * File          : addcatalog.component.spec.ts
 * File Created  : Saturday, 17th October 2020 04:14:40 pm
 * Author        : Optimumbrew
 * Auther Email  : info@optimumbrew.com
 * Last Modified : Thursday, 29th October 2020 11:30:38 am
 * -----
 * Copyright 2018 - 2020 Optimumbrew Technology
 */



import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { AddcatalogComponent } from './addcatalog.component';

describe('AddcatalogComponent', () => {
  let component: AddcatalogComponent;
  let fixture: ComponentFixture<AddcatalogComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [AddcatalogComponent]
    })
      .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(AddcatalogComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});

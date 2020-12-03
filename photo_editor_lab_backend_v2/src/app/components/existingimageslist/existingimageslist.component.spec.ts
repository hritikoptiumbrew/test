/*
 * Optimumbrew Technology

 * Project       : Photo Editor Lab
 * File          : existingimageslist.component.spec.ts
 * File Created  : Wednesday, 21st October 2020 10:15:14 am
 * Author        : Optimumbrew
 * Auther Email  : info@optimumbrew.com
 * Last Modified : Thursday, 29th October 2020 11:25:13 am
 * -----
 * Copyright 2018 - 2020 Optimumbrew Technology
 */


import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ExistingimageslistComponent } from './existingimageslist.component';

describe('ExistingimageslistComponent', () => {
  let component: ExistingimageslistComponent;
  let fixture: ComponentFixture<ExistingimageslistComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ExistingimageslistComponent]
    })
      .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ExistingimageslistComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});

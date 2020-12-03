/*
 * Optimumbrew Technology

 * Project       : Photo Editor Lab
 * File          : addvalidations.component.spec.ts
 * File Created  : Tuesday, 27th October 2020 05:39:32 pm
 * Author        : Optimumbrew
 * Auther Email  : info@optimumbrew.com
 * Last Modified : Thursday, 29th October 2020 11:20:38 am
 * -----
 * Copyright 2018 - 2020 Optimumbrew Technology
 */


import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { AddvalidationsComponent } from './addvalidations.component';

describe('AddvalidationsComponent', () => {
  let component: AddvalidationsComponent;
  let fixture: ComponentFixture<AddvalidationsComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [AddvalidationsComponent]
    })
      .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(AddvalidationsComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});

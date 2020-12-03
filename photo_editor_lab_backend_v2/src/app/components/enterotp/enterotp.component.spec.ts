/*
 * Optimumbrew Technology

 * Project       : Photo Editor Lab
 * File          : enterotp.component.spec.ts
 * File Created  : Monday, 26th October 2020 06:20:35 pm
 * Author        : Optimumbrew
 * Auther Email  : info@optimumbrew.com
 * Last Modified : Thursday, 29th October 2020 11:24:14 am
 * -----
 * Copyright 2018 - 2020 Optimumbrew Technology
 */


import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { EnterotpComponent } from './enterotp.component';

describe('EnterotpComponent', () => {
  let component: EnterotpComponent;
  let fixture: ComponentFixture<EnterotpComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ EnterotpComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(EnterotpComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});

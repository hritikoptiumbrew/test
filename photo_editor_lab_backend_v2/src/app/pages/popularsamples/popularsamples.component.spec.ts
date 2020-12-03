/*
 * Optimumbrew Technology

 * Project       : Photo Editor Lab
 * File          : popularsamples.component.spec.ts
 * File Created  : Thursday, 22nd October 2020 04:54:48 pm
 * Author        : Optimumbrew
 * Auther Email  : info@optimumbrew.com
 * Last Modified : Thursday, 22nd October 2020 06:42:02 pm
 * -----
 * Copyright 2018 - 2020 Optimumbrew Technology
 */


import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { PopularsamplesComponent } from './popularsamples.component';

describe('PopularsamplesComponent', () => {
  let component: PopularsamplesComponent;
  let fixture: ComponentFixture<PopularsamplesComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [PopularsamplesComponent]
    })
      .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(PopularsamplesComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});

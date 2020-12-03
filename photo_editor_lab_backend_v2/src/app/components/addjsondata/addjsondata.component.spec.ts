/*
 * Optimumbrew Technology

 * Project       : Photo Editor Lab
 * File          : addjsondata.component.spec.ts
 * File Created  : Wednesday, 21st October 2020 02:07:08 pm
 * Author        : Optimumbrew
 * Auther Email  : info@optimumbrew.com
 * Last Modified : Thursday, 29th October 2020 11:16:52 am
 * -----
 * Copyright 2018 - 2020 Optimumbrew Technology
 */


import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { AddjsondataComponent } from './addjsondata.component';

describe('AddjsondataComponent', () => {
  let component: AddjsondataComponent;
  let fixture: ComponentFixture<AddjsondataComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [AddjsondataComponent]
    })
      .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(AddjsondataComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});

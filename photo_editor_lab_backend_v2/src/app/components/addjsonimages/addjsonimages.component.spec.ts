/*
 * Optimumbrew Technology

 * Project       : Photo Editor Lab
 * File          : addjsonimages.component.spec.ts
 * File Created  : Tuesday, 20th October 2020 03:11:44 pm
 * Author        : Optimumbrew
 * Auther Email  : info@optimumbrew.com
 * Last Modified : Tuesday, 20th October 2020 03:16:44 pm
 * -----
 * Copyright 2018 - 2020 Optimumbrew Technology
 */

import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { AddjsonimagesComponent } from './addjsonimages.component';

describe('AddjsonimagesComponent', () => {
  let component: AddjsonimagesComponent;
  let fixture: ComponentFixture<AddjsonimagesComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [AddjsonimagesComponent]
    })
      .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(AddjsonimagesComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});

/*
 * Optimumbrew Technology

 * Project       : Photo Editor Lab
 * File          : popularsampleadd.component.spec.ts
 * File Created  : Thursday, 22nd October 2020 05:42:15 pm
 * Author        : Optimumbrew
 * Auther Email  : info@optimumbrew.com
 * Last Modified : Thursday, 22nd October 2020 05:47:28 pm
 * -----
 * Copyright 2018 - 2020 Optimumbrew Technology
 */


import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { PopularsampleaddComponent } from './popularsampleadd.component';

describe('PopularsampleaddComponent', () => {
  let component: PopularsampleaddComponent;
  let fixture: ComponentFixture<PopularsampleaddComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [PopularsampleaddComponent]
    })
      .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(PopularsampleaddComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});

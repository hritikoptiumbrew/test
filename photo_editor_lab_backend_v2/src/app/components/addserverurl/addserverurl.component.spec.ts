/*
 * Optimumbrew Technology

 * Project       : Photo Editor Lab
 * File          : addserverurl.component.spec.ts
 * File Created  : Saturday, 31st October 2020 11:49:27 am
 * Author        : Optimumbrew
 * Auther Email  : info@optimumbrew.com
 * Last Modified : Saturday, 31st October 2020 11:49:55 am
 * -----
 * Copyright 2018 - 2020 Optimumbrew Technology
 */


import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { AddserverurlComponent } from './addserverurl.component';

describe('AddserverurlComponent', () => {
  let component: AddserverurlComponent;
  let fixture: ComponentFixture<AddserverurlComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ AddserverurlComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(AddserverurlComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});

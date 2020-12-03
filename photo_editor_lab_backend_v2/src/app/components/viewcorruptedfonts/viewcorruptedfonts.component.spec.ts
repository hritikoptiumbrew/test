/*
 * Optimumbrew Technology

 * Project       : Photo Editor Lab
 * File          : viewcorruptedfonts.component.spec.ts
 * File Created  : Wednesday, 11th November 2020 06:09:44 pm
 * Author        : Optimumbrew
 * Auther Email  : info@optimumbrew.com
 * Last Modified : Wednesday, 11th November 2020 06:26:57 pm
 * -----
 * Copyright 2018 - 2020 Optimumbrew Technology
 */


import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ViewcorruptedfontsComponent } from './viewcorruptedfonts.component';

describe('ViewcorruptedfontsComponent', () => {
  let component: ViewcorruptedfontsComponent;
  let fixture: ComponentFixture<ViewcorruptedfontsComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ViewcorruptedfontsComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ViewcorruptedfontsComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});

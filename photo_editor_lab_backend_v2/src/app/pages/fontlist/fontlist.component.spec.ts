/*
 * Optimumbrew Technology

 * Project       : Photo Editor Lab
 * File          : fontlist.component.spec.ts
 * File Created  : Thursday, 22nd October 2020 12:16:53 pm
 * Author        : Optimumbrew
 * Auther Email  : info@optimumbrew.com
 * Last Modified : Thursday, 22nd October 2020 12:27:50 pm
 * -----
 * Copyright 2018 - 2020 Optimumbrew Technology
 */


import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { FontlistComponent } from './fontlist.component';

describe('FontlistComponent', () => {
  let component: FontlistComponent;
  let fixture: ComponentFixture<FontlistComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [FontlistComponent]
    })
      .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(FontlistComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});

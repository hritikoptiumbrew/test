/*
 * Optimumbrew Technology

 * Project       : Photo Editor Lab
 * File          : editfont.component.spec.ts
 * File Created  : Thursday, 22nd October 2020 04:11:05 pm
 * Author        : Optimumbrew
 * Auther Email  : info@optimumbrew.com
 * Last Modified : Thursday, 22nd October 2020 04:11:54 pm
 * -----
 * Copyright 2018 - 2020 Optimumbrew Technology
 */


import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { EditfontComponent } from './editfont.component';

describe('EditfontComponent', () => {
  let component: EditfontComponent;
  let fixture: ComponentFixture<EditfontComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [EditfontComponent]
    })
      .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(EditfontComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});

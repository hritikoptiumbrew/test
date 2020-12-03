/*
 * Optimumbrew Technology

 * Project       : Photo Editor Lab
 * File          : bloglist.component.spec.ts
 * File Created  : Thursday, 22nd October 2020 06:40:00 pm
 * Author        : Optimumbrew
 * Auther Email  : info@optimumbrew.com
 * Last Modified : Thursday, 22nd October 2020 06:42:29 pm
 * -----
 * Copyright 2018 - 2020 Optimumbrew Technology
 */


import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { BloglistComponent } from './bloglist.component';

describe('BloglistComponent', () => {
  let component: BloglistComponent;
  let fixture: ComponentFixture<BloglistComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [BloglistComponent]
    })
      .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(BloglistComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});

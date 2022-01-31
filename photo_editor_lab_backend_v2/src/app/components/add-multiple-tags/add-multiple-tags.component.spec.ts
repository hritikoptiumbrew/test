import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { AddMultipleTagsComponent } from './add-multiple-tags.component';

describe('AddMultipleTagsComponent', () => {
  let component: AddMultipleTagsComponent;
  let fixture: ComponentFixture<AddMultipleTagsComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ AddMultipleTagsComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(AddMultipleTagsComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});

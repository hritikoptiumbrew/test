import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { AddthemepostComponent } from './addthemepost.component';

describe('AddthemepostComponent', () => {
  let component: AddthemepostComponent;
  let fixture: ComponentFixture<AddthemepostComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ AddthemepostComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(AddthemepostComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});

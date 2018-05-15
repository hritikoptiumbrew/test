import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { PromocodeManagementComponent } from './promocode-management.component';

describe('PromocodeManagementComponent', () => {
  let component: PromocodeManagementComponent;
  let fixture: ComponentFixture<PromocodeManagementComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ PromocodeManagementComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(PromocodeManagementComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should be created', () => {
    expect(component).toBeTruthy();
  });
});

import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { EnterOTPComponent } from './enter-otp.component';

describe('EnterOTPComponent', () => {
  let component: EnterOTPComponent;
  let fixture: ComponentFixture<EnterOTPComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ EnterOTPComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(EnterOTPComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should be created', () => {
    expect(component).toBeTruthy();
  });
});

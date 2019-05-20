import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { MvToCtlgComponent } from './mv-to-ctlg.component';

describe('MvToCtlgComponent', () => {
  let component: MvToCtlgComponent;
  let fixture: ComponentFixture<MvToCtlgComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ MvToCtlgComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(MvToCtlgComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should be created', () => {
    expect(component).toBeTruthy();
  });
});

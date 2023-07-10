import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { RepeatThemesComponent } from './repeat-themes.component';

describe('RepeatThemesComponent', () => {
  let component: RepeatThemesComponent;
  let fixture: ComponentFixture<RepeatThemesComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ RepeatThemesComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(RepeatThemesComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});

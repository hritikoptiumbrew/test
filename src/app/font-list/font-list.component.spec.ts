import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { FontListComponent } from './font-list.component';

describe('FontListComponent', () => {
  let component: FontListComponent;
  let fixture: ComponentFixture<FontListComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ FontListComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(FontListComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should be created', () => {
    expect(component).toBeTruthy();
  });
});

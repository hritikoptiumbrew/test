import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { UserGeneratedDesignsComponent } from './user-generated-designs.component';

describe('UserGeneratedDesignsComponent', () => {
  let component: UserGeneratedDesignsComponent;
  let fixture: ComponentFixture<UserGeneratedDesignsComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ UserGeneratedDesignsComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(UserGeneratedDesignsComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should be created', () => {
    expect(component).toBeTruthy();
  });
});

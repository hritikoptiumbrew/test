import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { PostcalenderComponent } from './postcalender.component';

describe('PostcalenderComponent', () => {
  let component: PostcalenderComponent;
  let fixture: ComponentFixture<PostcalenderComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ PostcalenderComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(PostcalenderComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});

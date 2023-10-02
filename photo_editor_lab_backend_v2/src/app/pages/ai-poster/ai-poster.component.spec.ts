import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { AiPosterComponent } from './ai-poster.component';

describe('AiPosterComponent', () => {
  let component: AiPosterComponent;
  let fixture: ComponentFixture<AiPosterComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ AiPosterComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(AiPosterComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});

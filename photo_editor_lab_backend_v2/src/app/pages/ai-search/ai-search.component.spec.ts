import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { AiSearchComponent } from './ai-search.component';

describe('AiSearchComponent', () => {
  let component: AiSearchComponent;
  let fixture: ComponentFixture<AiSearchComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ AiSearchComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(AiSearchComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});

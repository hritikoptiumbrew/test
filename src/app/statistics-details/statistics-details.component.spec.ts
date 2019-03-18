import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { StatisticsDetailsComponent } from './statistics-details.component';

describe('StatisticsDetailsComponent', () => {
  let component: StatisticsDetailsComponent;
  let fixture: ComponentFixture<StatisticsDetailsComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ StatisticsDetailsComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(StatisticsDetailsComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should be created', () => {
    expect(component).toBeTruthy();
  });
});

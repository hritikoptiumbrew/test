import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { StatisticsDetailsByCatalogComponent } from './statistics-details-by-catalog.component';

describe('StatisticsDetailsByCatalogComponent', () => {
  let component: StatisticsDetailsByCatalogComponent;
  let fixture: ComponentFixture<StatisticsDetailsByCatalogComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ StatisticsDetailsByCatalogComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(StatisticsDetailsByCatalogComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should be created', () => {
    expect(component).toBeTruthy();
  });
});

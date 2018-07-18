import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { GaAdsByCategoryComponent } from './ga-ads-by-category.component';

describe('GaAdsByCategoryComponent', () => {
  let component: GaAdsByCategoryComponent;
  let fixture: ComponentFixture<GaAdsByCategoryComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ GaAdsByCategoryComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(GaAdsByCategoryComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should be created', () => {
    expect(component).toBeTruthy();
  });
});

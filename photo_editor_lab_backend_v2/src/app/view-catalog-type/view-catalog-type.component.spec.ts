import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ViewCatalogTypeComponent } from './view-catalog-type.component';

describe('ViewCatalogTypeComponent', () => {
  let component: ViewCatalogTypeComponent;
  let fixture: ComponentFixture<ViewCatalogTypeComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ViewCatalogTypeComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ViewCatalogTypeComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});

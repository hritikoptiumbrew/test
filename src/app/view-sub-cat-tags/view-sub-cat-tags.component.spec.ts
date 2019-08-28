import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ViewSubCatTagsComponent } from './view-sub-cat-tags.component';

describe('ViewSubCatTagsComponent', () => {
  let component: ViewSubCatTagsComponent;
  let fixture: ComponentFixture<ViewSubCatTagsComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ViewSubCatTagsComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ViewSubCatTagsComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should be created', () => {
    expect(component).toBeTruthy();
  });
});

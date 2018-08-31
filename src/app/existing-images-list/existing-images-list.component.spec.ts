import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ExistingImagesListComponent } from './existing-images-list.component';

describe('ExistingImagesListComponent', () => {
  let component: ExistingImagesListComponent;
  let fixture: ComponentFixture<ExistingImagesListComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ExistingImagesListComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ExistingImagesListComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should be created', () => {
    expect(component).toBeTruthy();
  });
});

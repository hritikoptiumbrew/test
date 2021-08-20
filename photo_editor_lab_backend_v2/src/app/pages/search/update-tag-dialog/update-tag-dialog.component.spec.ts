import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { UpdateTagDialogComponent } from './update-tag-dialog.component';

describe('UpdateTagDialogComponent', () => {
  let component: UpdateTagDialogComponent;
  let fixture: ComponentFixture<UpdateTagDialogComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ UpdateTagDialogComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(UpdateTagDialogComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});

import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { DeleteUserGeneratedComponent } from './delete-user-generated.component';

describe('DeleteUserGeneratedComponent', () => {
  let component: DeleteUserGeneratedComponent;
  let fixture: ComponentFixture<DeleteUserGeneratedComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ DeleteUserGeneratedComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(DeleteUserGeneratedComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should be created', () => {
    expect(component).toBeTruthy();
  });
});

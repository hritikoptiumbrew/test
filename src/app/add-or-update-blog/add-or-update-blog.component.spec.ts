import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { AddOrUpdateBlogComponent } from './add-or-update-blog.component';

describe('AddOrUpdateBlogComponent', () => {
  let component: AddOrUpdateBlogComponent;
  let fixture: ComponentFixture<AddOrUpdateBlogComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ AddOrUpdateBlogComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(AddOrUpdateBlogComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should be created', () => {
    expect(component).toBeTruthy();
  });
});

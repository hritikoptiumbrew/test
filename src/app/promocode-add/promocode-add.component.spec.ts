import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { PromocodeAddComponent } from './promocode-add.component';

describe('PromocodeAddComponent', () => {
  let component: PromocodeAddComponent;
  let fixture: ComponentFixture<PromocodeAddComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ PromocodeAddComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(PromocodeAddComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should be created', () => {
    expect(component).toBeTruthy();
  });
});

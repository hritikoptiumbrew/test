import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { AdmobAdsComponent } from './admob-ads.component';

describe('AdmobAdsComponent', () => {
  let component: AdmobAdsComponent;
  let fixture: ComponentFixture<AdmobAdsComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ AdmobAdsComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(AdmobAdsComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should be created', () => {
    expect(component).toBeTruthy();
  });
});

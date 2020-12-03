/*
 * Optimumbrew Technology

 * Project       : Photo Editor Lab
 * File          : seo.service.ts
 * File Created  : Thursday, 15th October 2020 01:12:03 pm
 * Author        : Optimumbrew
 * Auther Email  : info@optimumbrew.com
 * Last Modified : Thursday, 29th October 2020 11:42:29 am
 * -----
 * Copyright 2018 - 2020 Optimumbrew Technology
 */


import { Injectable, Inject, PLATFORM_ID, OnDestroy } from '@angular/core';
import { isPlatformBrowser } from '@angular/common';
import { NavigationEnd, Router } from '@angular/router';
import { NB_DOCUMENT } from '@nebular/theme';
import { filter, takeUntil } from 'rxjs/operators';
import { Subject } from 'rxjs';

@Injectable()
export class SeoService implements OnDestroy {

  private readonly destroy$ = new Subject<void>();
  private readonly dom: Document;
  private readonly isBrowser: boolean;
  private linkCanonical: HTMLLinkElement;

  constructor(
    private router: Router,
    @Inject(NB_DOCUMENT) document,
    @Inject(PLATFORM_ID) platformId,
  ) {
    this.isBrowser = isPlatformBrowser(platformId);
    this.dom = document;

    if (this.isBrowser) {
      this.createCanonicalTag();
    }
  }

  ngOnDestroy() {
    this.destroy$.next();
    this.destroy$.complete();
  }

  createCanonicalTag() {
    this.linkCanonical = this.dom.createElement('link');
    this.linkCanonical.setAttribute('rel', 'canonical');
    this.dom.head.appendChild(this.linkCanonical);
    this.linkCanonical.setAttribute('href', this.getCanonicalUrl());
  }

  trackCanonicalChanges() {
    if (!this.isBrowser) {
      return;
    }

    this.router.events.pipe(
      filter((event) => event instanceof NavigationEnd),
      takeUntil(this.destroy$),
    )
      .subscribe(() => {
        this.linkCanonical.setAttribute('href', this.getCanonicalUrl());
      });
  }

  private getCanonicalUrl(): string {
    return this.dom.location.origin + this.dom.location.pathname;
  }
}

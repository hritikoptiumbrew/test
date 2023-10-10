/*
 * Optimumbrew Technology

 * Project       : Photo Editor Lab
 * File          : pages-menu.ts
 * File Created  : Thursday, 15th October 2020 01:12:03 pm
 * Author        : Optimumbrew
 * Auther Email  : info@optimumbrew.com
 * Last Modified : Thursday, 29th October 2020 11:35:43 am
 * -----
 * Copyright 2018 - 2020 Optimumbrew Technology
 */


import { NbMenuItem } from '@nebular/theme';

export const MENU_ITEMS: NbMenuItem[] = [
  // {
  //   title: 'Dashboard',
  //   icon: 'home-outline',
  //   link: '/admin/dashboard',
  //   home: true,
  // },
  {
    title: 'Categories',
    icon: 'image-outline',
    link: '/admin/categories',
  },
  {
    title: 'AI Text Details',
    icon: 'file-text-outline',
    link: '/admin/review'
  },
  {
    title: 'AI Poster',
    icon: 'book-open-outline',
    link: '/admin/poster'
  },
  {
    title: 'Statistics',
    icon: 'bar-chart-outline',
    link: '/admin/statistics'
  },
  {
    title: 'Image Details',
    icon: 'info-outline',
    link: '/admin/image-details'
  },
  {
    title: 'Search',
    icon: 'search',
    link: '/admin/search'
  },
  {
    title: 'Redis Cache',
    icon: 'hard-drive-outline',
    link: '/admin/redis-cache'
  },
  {
    title: 'Settings',
    icon: 'settings-2-outline',
    link: '/admin/settings'
  },
];

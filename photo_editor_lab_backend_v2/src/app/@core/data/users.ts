/*
 * Optimumbrew Technology

 * Project       : Photo Editor Lab
 * File          : users.ts
 * File Created  : Thursday, 15th October 2020 01:12:03 pm
 * Author        : Optimumbrew
 * Auther Email  : info@optimumbrew.com
 * Last Modified : Thursday, 29th October 2020 11:45:30 am
 * -----
 * Copyright 2018 - 2020 Optimumbrew Technology
 */


import { Observable } from 'rxjs';

export interface User {
  name: string;
  picture: string;
}

export interface Contacts {
  user: User;
  type: string;
}

export interface RecentUsers extends Contacts {
  time: number;
}

export abstract class UserData {
  abstract getUsers(): Observable<User[]>;
  abstract getContacts(): Observable<Contacts[]>;
  abstract getRecentUsers(): Observable<RecentUsers[]>;
}

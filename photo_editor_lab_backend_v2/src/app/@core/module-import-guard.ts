/*
 * Optimumbrew Technology

 * Project       : Photo Editor Lab
 * File          : module-import-guard.ts
 * File Created  : Thursday, 15th October 2020 01:12:03 pm
 * Author        : Optimumbrew
 * Auther Email  : info@optimumbrew.com
 * Last Modified : Thursday, 29th October 2020 11:42:00 am
 * -----
 * Copyright 2018 - 2020 Optimumbrew Technology
 */


export function throwIfAlreadyLoaded(parentModule: any, moduleName: string) {
  if (parentModule) {
    throw new Error(`${moduleName} has already been loaded. Import Core modules in the AppModule only.`);
  }
}

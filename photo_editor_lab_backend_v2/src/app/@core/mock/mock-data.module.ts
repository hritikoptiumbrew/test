/*
 * Optimumbrew Technology

 * Project       : Photo Editor Lab
 * File          : mock-data.module.ts
 * File Created  : Thursday, 15th October 2020 01:12:03 pm
 * Author        : Optimumbrew
 * Auther Email  : info@optimumbrew.com
 * Last Modified : Thursday, 29th October 2020 11:42:50 am
 * -----
 * Copyright 2018 - 2020 Optimumbrew Technology
 */


import { NgModule, ModuleWithProviders } from '@angular/core';
import { CommonModule } from '@angular/common';

import { UserService } from './users.service';
import { ElectricityService } from './electricity.service';
import { SmartTableService } from './smart-table.service';
import { UserActivityService } from './user-activity.service';
import { OrdersChartService } from './orders-chart.service';
import { ProfitChartService } from './profit-chart.service';
import { TrafficListService } from './traffic-list.service';
import { PeriodsService } from './periods.service';
import { EarningService } from './earning.service';
import { OrdersProfitChartService } from './orders-profit-chart.service';
import { TrafficBarService } from './traffic-bar.service';
import { ProfitBarAnimationChartService } from './profit-bar-animation-chart.service';
import { TemperatureHumidityService } from './temperature-humidity.service';
import { SolarService } from './solar.service';
import { TrafficChartService } from './traffic-chart.service';
import { StatsBarService } from './stats-bar.service';
import { CountryOrderService } from './country-order.service';
import { StatsProgressBarService } from './stats-progress-bar.service';
import { VisitorsAnalyticsService } from './visitors-analytics.service';
import { SecurityCamerasService } from './security-cameras.service';

const SERVICES = [
  UserService,
  ElectricityService,
  SmartTableService,
  UserActivityService,
  OrdersChartService,
  ProfitChartService,
  TrafficListService,
  PeriodsService,
  EarningService,
  OrdersProfitChartService,
  TrafficBarService,
  ProfitBarAnimationChartService,
  TemperatureHumidityService,
  SolarService,
  TrafficChartService,
  StatsBarService,
  CountryOrderService,
  StatsProgressBarService,
  VisitorsAnalyticsService,
  SecurityCamerasService,
];

@NgModule({
  imports: [
    CommonModule,
  ],
  providers: [
    ...SERVICES,
  ],
})
export class MockDataModule {
  static forRoot(): ModuleWithProviders<MockDataModule> {
    return {
      ngModule: MockDataModule,
      providers: [
        ...SERVICES,
      ],
    };
  }
}

/*
 * Optimumbrew Technology

 * Project       : Photo Editor Lab
 * File          : orders-profit-chart.ts
 * File Created  : Thursday, 15th October 2020 01:12:03 pm
 * Author        : Optimumbrew
 * Auther Email  : info@optimumbrew.com
 * Last Modified : Thursday, 29th October 2020 11:44:45 am
 * -----
 * Copyright 2018 - 2020 Optimumbrew Technology
 */


import { Observable } from 'rxjs';
import { OrdersChart } from './orders-chart';
import { ProfitChart  } from './profit-chart';

export interface OrderProfitChartSummary {
  title: string;
  value: number;
}

export abstract class OrdersProfitChartData {
  abstract getOrderProfitChartSummary(): Observable<OrderProfitChartSummary[]>;
  abstract getOrdersChartData(period: string): Observable<OrdersChart>;
  abstract getProfitChartData(period: string): Observable<ProfitChart>;
}

/*
 * Optimumbrew Technology

 * Project       : Photo Editor Lab
 * File          : orders-chart.ts
 * File Created  : Thursday, 15th October 2020 01:12:03 pm
 * Author        : Optimumbrew
 * Auther Email  : info@optimumbrew.com
 * Last Modified : Thursday, 29th October 2020 11:44:42 am
 * -----
 * Copyright 2018 - 2020 Optimumbrew Technology
 */


export interface OrdersChart {
  chartLabel: string[];
  linesData: number[][];
}

export abstract class OrdersChartData {
  abstract getOrdersChartData(period: string): OrdersChart;
}

/*
 * Optimumbrew Technology

 * Project       : Photo Editor Lab
 * File          : statistics-details.component.ts
 * File Created  : Saturday, 24th October 2020 11:19:12 am
 * Author        : Optimumbrew
 * Auther Email  : info@optimumbrew.com
 * Last Modified : Thursday, 29th October 2020 11:28:32 am
 * -----
 * Copyright 2018 - 2020 Optimumbrew Technology
 */


import { Component, OnInit, ViewChild } from '@angular/core';
import { NbDialogRef } from '@nebular/theme';
import { DataService } from 'app/data.service';
import { UtilService } from 'app/util.service';
import * as moment from 'moment';
import { DaterangepickerComponent } from 'ng2-daterangepicker';
import { LocalDataSource } from 'ng2-smart-table';
import { ERROR, ENV_CONFIG } from '../../app.constants';
@Component({
  selector: 'ngx-statistics-details',
  templateUrl: './statistics-details.component.html',
  styleUrls: ['./statistics-details.component.scss']
})
export class StatisticsDetailsComponent implements OnInit {
  @ViewChild(DaterangepickerComponent) private picker: DaterangepickerComponent;
  public pickerOptions = {
    startDate: moment().subtract(6, 'days'),
    endDate: moment(),
    showDropdowns: false,
    locale: { format: 'YYYY-MM-DD' },
    opens: 'left',
    minDate: '2015-01-01',
    ranges: {
      'This Week': [moment().subtract(6, 'days'), moment()],
      'This Month': [moment().startOf('month'), moment().endOf('month')],
      'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
      'This Year': [moment().startOf('year'), moment().endOf('year')],
      'Last Year': [moment().subtract(1, 'year').startOf('year'), moment().subtract(1, 'year').endOf('year')]
    }
  }
  dataSource: LocalDataSource
  settings: any;
  selectType: any;
  contentDetails: any = [];
  startDate: any;
  requestData: any;
  endDate: any;
  selectedRange: any;
  serverData: any;
  contentData: any;
  apiUrl: any;
  totalRecords: any = 0;
  pageSize: any = [15, 30, 45, 60, 75, 90, 100];
  selectedPageSize: any = '15';
  currentPage: any = 1;
  token: any;
  previousLabel = "<";
  nextLabel=">";
  order_type: Boolean;
  order_type_val: any;
  sortByTagName:any;
  constructor(private dialogRef: NbDialogRef<StatisticsDetailsComponent>, private dataService: DataService, private utils: UtilService) {
    this.token = localStorage.getItem("at");
    this.utils.dialogref = this.dialogRef;
  }
  ngOnInit(): void {
      this.settings = {
        mode: 'external',
        columns: {
          id: {
            title: "#",
            filter: false,
            width: '75px',
            type: 'text',
            sort: false
          },
          catalog_name: {
            title: "Catalog Name",
            filter: true,
          },
          content_count: {
            title: "Uploaded Templates",
            filter: true,
          },
          last_uploaded_date: {
            title: "Last Uploaded on",
            filter: true,
          },
        },
        actions: {
          add: false,
          edit: false,
          delete: false,

        },
        pager:{
          display: true,
          perPage: parseInt(this.selectedPageSize)
        },
        hideSubHeader: true
      };
    this.startDate = moment().subtract(6, 'days').format('YYYY-MM-DD');
    this.endDate = moment().format('YYYY-MM-DD');
    this.getDataByRange();
  }
  handlePageChange(event): void {
    this.currentPage = event;
    this.getDataByRange(); 
    // this.getAllBackgroundCatogory(this.currentPage);
  }
  setPageSize(value) {
    this.selectedPageSize = value;
    this.settings.pager.perPage = parseInt(this.selectedPageSize);
    this.dataSource.setPaging(this.dataSource.getPaging().page,parseInt(this.selectedPageSize),true);
    if(this.selectType == 'date')
    {
      // if(this.selectedPageSize > this.totalRecords)
      // {
      //   this.currentPage = 1;
      // }
      this.getDataByRange(); 
    }
  }
  pageChanged(event){
    this.currentPage = event;
    this.getDataByRange();
  }
  refreshPage(){
    this.selectedPageSize = '15';
    this.getDataByRange();
  }
  updateRange(value: any) {
    this.startDate = value.start.format('YYYY-MM-DD');
    this.endDate = value.end.format('YYYY-MM-DD');
    this.selectedRange = this.startDate + " to " + this.endDate;
  }
  sortBy(sortByTagName, order_type_val) {
    this.sortByTagName = sortByTagName;
    if (order_type_val == "ASC") {
      this.order_type = false;
      this.order_type_val = "DESC";
    }
    else {
      this.order_type = true;
      this.order_type_val = "ASC";
    }
    this.getDataByRange();
  }
  getDataByRange() {
    
    if (this.selectType == "date") {
      this.requestData = {
        "api_url": this.serverData.api_url,
        "category_id": this.contentData.category_id,
        "sub_category_id": this.contentData.sub_category_id,
        "from_date": this.startDate,
        "to_date": this.endDate,
        "page": this.currentPage,
        "item_count": this.selectedPageSize,
        "order_by": this.sortByTagName,
        "order_type": this.order_type_val,
      }
      this.apiUrl = "getSummaryDetailFromDiffServer"
    }
    else {
      this.requestData = {
        "api_url": this.serverData.api_url,
        "sub_category_id": this.contentData.sub_category_id,
        "from_date": this.startDate,
        "to_date": this.endDate,
      }
      this.apiUrl = "getSummaryOfCatalogsFromDiffServer";
    }
    this.getStatisticsData(this.requestData, this.apiUrl);
  }
  closeDialog() {
    this.dialogRef.close();
  }
  getStatisticsData(requestData, apiUrl) {
    this.utils.showLoader();
    this.dataService.postData(apiUrl, requestData, {
      headers: {
        'Authorization': 'Bearer ' + this.token
      }
    }).then((results: any) => {
      if (results.code == 200) {
        var j = 1;
        results.data.data.result.forEach(appname => {
          appname.id = j;
          j++; 
        });
        this.contentDetails = results.data.data.result;
        this.totalRecords = results.data.data.total_record;
        this.dataSource = new LocalDataSource(this.contentDetails);
        this.utils.hideLoader();
      }
      else if (results.code == 201) {
        this.utils.showError(results.message, 4000);
        this.utils.hideLoader();
      }
      else if (results.status || results.status == 0) {
        this.utils.showError(ERROR.SERVER_ERR, 4000);
        this.utils.hideLoader();
      }
      else {
        this.utils.showError(results.message, 4000);
        this.utils.hideLoader();
      }
    }, (error: any) => {
      console.log(error);
      this.utils.hideLoader();
      this.utils.showError(ERROR.SERVER_ERR, 4000);
    }).catch((error: any) => {
      console.log(error);
      this.utils.hideLoader();
      this.utils.showError(ERROR.SERVER_ERR, 4000);
    });
  }
}

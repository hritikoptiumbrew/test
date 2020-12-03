/*
 * Optimumbrew Technology

 * Project       : Photo Editor Lab
 * File          : statistics.component.ts
 * File Created  : Friday, 23rd October 2020 01:09:15 pm
 * Author        : Optimumbrew
 * Auther Email  : info@optimumbrew.com
 * Last Modified : Friday, 23rd October 2020 01:14:14 pm
 * -----
 * Copyright 2018 - 2020 Optimumbrew Technology
 */


import { Component, OnInit } from '@angular/core';
import { DomSanitizer } from '@angular/platform-browser';
import { NbDialogService } from '@nebular/theme';
import { StatisticsDetailsComponent } from 'app/components/statistics-details/statistics-details.component';
import { DataService } from 'app/data.service';
import { UtilService } from 'app/util.service';
import { ERROR, ENV_CONFIG } from '../../app.constants';
@Component({
  selector: 'ngx-statistics',
  templateUrl: './statistics.component.html',
  styleUrls: ['./statistics.component.scss']
})
export class StatisticsComponent implements OnInit {

  token: any;
  serverData: any;
  totalRecord: any;
  serverDetails: any;
  settings = {
    mode: 'external',
    edit: {
      editButtonContent: '<i class="fa fa-calendar-alt" title="View By Date"></i>',
      saveButtonContent: '<i class="nb-checkmark"></i>',
      cancelButtonContent: '<i class="nb-close"></i>',
      confirmSave: true,
    },
    delete: {
      deleteButtonContent: '<i class="fa fa-file-alt" title="View By Catalog"></i>',
      confirmDelete: true,
    },
    columns: {
      id: {
        title: "#",
        sort: 'false',
        filter: false,
        width: '75px',
        type: 'text',
      },
      compressed_img: {
        title: "App Icon",
        filter: false,
        sort: false,
        type: 'html',
        valuePrepareFunction: (value) => { return this._sanitizer.bypassSecurityTrustHtml('<img src="' + value + '" style="height:50px;">') },
      },
      name: {
        title: "Name"
      },
      no_of_catalogs: {
        title: "Total Catalogs"
      },
      content_count: {
        title: "Total Templates"
      },
      free_content: {
        title: "Free Templates"
      },
      paid_content: {
        title: "Paid Templates"
      },
      last_uploaded_count: {
        title: "Last Uploaded Count"
      },
      last_uploaded_date: {
        title: "Last Uploaded On"
      }
    },
    actions: {
      add: false,
      position: 'right',
      delete: true,
      edit: true,
      pager: true,
    },
  };
  constructor(private dialog: NbDialogService, private _sanitizer: DomSanitizer, private dataService: DataService, private utils: UtilService) {
    this.token = localStorage.getItem("at");
  }
  ngOnInit(): void {
    this.getStatisticsData();

  }
  
  viewServerDetails(event, item) {
    if (!event) {
      this.utils.showPageLoader();
      this.dataService.postData('getSummaryOfIndividualServerByAdmin',
        {
          "api_url": item.api_url
        }, {
        headers: {
          'Authorization': 'Bearer ' + this.token
        }
      }).then((results: any) => {
        if (results.code == 200) {
          var i = 1;
          results.data.result.forEach(appname => {
            appname.last_uploaded_date = this.dataService.formatDDMMMYYYYHHMMALOCAL(appname.last_uploaded_date);
            appname.id = i;
            i++;
          });
          item.result = results.data.result;
          this.serverDetails = results.data.result;
          this.utils.hidePageLoader();
        }
        else if (results.code == 201) {
          this.utils.showError(results.message, 4000);
          this.utils.hidePageLoader();
        }
        else if (results.status || results.status == 0) {
          this.utils.showError(ERROR.SERVER_ERR, 4000);
          this.utils.hidePageLoader();
        }
        else {
          this.utils.showError(results.message, 4000);
          this.utils.hidePageLoader();
        }
      }, (error: any) => {
        console.log(error);
        this.utils.hidePageLoader();
        this.utils.showError(ERROR.SERVER_ERR, 4000);
      }).catch((error: any) => {
        console.log(error);
        this.utils.hidePageLoader();
        this.utils.showError(ERROR.SERVER_ERR, 4000);
      });
    }
  }
  getStatisticsData() {
    this.utils.showPageLoader();
    this.dataService.postData('getAllServerUrls',
      {}, {
      headers: {
        'Authorization': 'Bearer ' + this.token
      }
    }).then((results: any) => {
      if (results.code == 200) {
        this.serverData = results.data.result;
        this.totalRecord = results.data.total_record;
        this.utils.hidePageLoader();
      }
      else if (results.code == 201) {
        this.utils.showError(results.message, 4000);
        this.utils.hidePageLoader();
      }
      else if (results.status || results.status == 0) {
        this.utils.showError(ERROR.SERVER_ERR, 4000);
        this.utils.hidePageLoader();
      }
      else {
        this.utils.showError(results.message, 4000);
        this.utils.hidePageLoader();
      }
    }, (error: any) => {
      console.log(error);
      this.utils.hidePageLoader();
      this.utils.showError(ERROR.SERVER_ERR, 4000);
    }).catch((error: any) => {
      console.log(error);
      this.utils.hidePageLoader();
      this.utils.showError(ERROR.SERVER_ERR, 4000);
    });
  }

  openDateRangePicker(event, item, getType) {
    this.open(false, false, event.data, item, getType)
  }
  protected open(closeOnBackdropClick: boolean, autoFocus: boolean, data, item, getType) {
    this.dialog.open(StatisticsDetailsComponent, {
      closeOnBackdropClick,closeOnEsc: false, autoFocus, context: {
        contentData: data,
        serverData: item,
        selectType: getType
      }
    });
  }
}

import { Component, OnInit, ViewChild } from '@angular/core';
import { MdDialog, MdSnackBar, MdSnackBarConfig } from '@angular/material';
import { Router } from '@angular/router';
import { DataService } from '../data.service';
import { LoadingComponent } from '../loading/loading.component';
import { StatisticsDetailsComponent } from '../statistics-details/statistics-details.component';
import { StatisticsDetailsByCatalogComponent } from '../statistics-details-by-catalog/statistics-details-by-catalog.component';

@Component({
  selector: 'app-statistics',
  templateUrl: './statistics.component.html',
  styleUrls: ['./statistics.component.css']
})
export class StatisticsComponent implements OnInit {

  token: any;
  successMsg: any;
  errorMsg: any;
  server_list: any;
  total_record: any;
  loading: any;
  sortByTagName: any;
  order_type: Boolean;
  order_type_val: any;
  current_path: any = "";

  constructor(private dataService: DataService, private router: Router, public dialog: MdDialog, public snackBar: MdSnackBar) {
    this.token = localStorage.getItem('photoArtsAdminToken');
    this.getStatisticsData();
  }

  ngOnInit() {
  }

  getStatisticsData() {
    this.loading = this.dialog.open(LoadingComponent);
    this.token = localStorage.getItem('photoArtsAdminToken');
    this.dataService.postData('getSummaryOfAllServersByAdmin',
      {}, {
        headers: {
          'Authorization': 'Bearer ' + this.token
        }
      }).subscribe(results => {
        if (results.code == 200) {
          this.server_list = results.data.summary_of_all_servers;
          this.total_record = results.data.total_record;
          this.server_list.forEach(element => {
            element.result.forEach(appname => {
              appname.last_uploaded_date = this.dataService.formatDDMMMYYYYHHMMALOCAL(appname.last_uploaded_date);
            });
          });
          this.loading.close();
          this.errorMsg = "";
          // this.showSuccess(results.message, false);
        }
        else if (results.code == 400) {
          this.loading.close();
          localStorage.removeItem("photoArtsAdminToken");
          this.router.navigate(['/admin']);
        }
        else if (results.code == 401) {
          this.token = results.data.new_token;
          localStorage.setItem("photoArtsAdminToken", this.token);
          this.getStatisticsData();
        }
        else {
          this.loading.close();
          this.successMsg = "";
          this.errorMsg = results.message;
          this.showError(results.message, false);
        }
      }, error => {
        this.loading.close();
        this.showError("Unable to connect with server, please reload the page.", false);
        /* console.log(error.status); */
        /* console.log(error); */
      });
  }

  openDateRangePicker(content_details, server_details) {
    content_details.api_url = server_details.api_url;
    let dialogRef = this.dialog.open(StatisticsDetailsComponent, { data: content_details });
  }

  getDetailsByCatalogs(content_details, server_details) {
    content_details.api_url = server_details.api_url;
    let dialogRef = this.dialog.open(StatisticsDetailsByCatalogComponent, { data: content_details });
  }

  showError(message, action) {
    let config = new MdSnackBarConfig();
    config.extraClasses = ['snack-error'];
    /* config.horizontalPosition = "right";
    config.verticalPosition = "top"; */
    config.duration = 5000;
    this.snackBar.open(message, action ? 'Okay!' : undefined, config);
  }

  showSuccess(message, action) {
    let config = new MdSnackBarConfig();
    config.extraClasses = ['snack-success'];
    /* config.horizontalPosition = "right";
    config.verticalPosition = "top"; */
    config.duration = 5000;
    this.snackBar.open(message, action ? 'Okay!' : undefined, config);
  }

}

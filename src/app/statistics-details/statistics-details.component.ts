import { Component, OnInit, ViewChild, Inject } from '@angular/core';
import { MdDialog, MdDialogRef, MdSnackBar, MdSnackBarConfig, MD_DIALOG_DATA } from '@angular/material';
import { Router } from '@angular/router';
import { DataService } from '../data.service';
import { LoadingComponent } from '../loading/loading.component';
import * as moment from 'moment';
import { Daterangepicker, DaterangepickerConfig, DaterangePickerComponent } from 'ng2-daterangepicker';

@Component({
  selector: 'app-statistics-details',
  templateUrl: './statistics-details.component.html',
  styleUrls: ['./statistics-details.component.css']
})
export class StatisticsDetailsComponent implements OnInit {

  @ViewChild(DaterangePickerComponent) private picker: DaterangePickerComponent;

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
  };
  start_date: any;
  end_date: any;
  selected_range: any;
  content_details: any = {};

  token: any;
  successMsg: any;
  errorMsg: any;
  content_list: any;
  total_record: any;
  loading: any;
  sortByTagName: any;
  order_type: Boolean;
  order_type_val: any;
  current_path: any = "";
  itemsPerPage: number = 15;
  currentPage: number = 1;
  searchArray: any[];
  searchErr: string;
  searchTag: any;
  searchQuery: any;
  itemsPerPageArray: any[];


  constructor(public dialogRef: MdDialogRef<StatisticsDetailsComponent>, @Inject(MD_DIALOG_DATA) public data: any, private dataService: DataService, private router: Router, public dialog: MdDialog, public snackBar: MdSnackBar) {
    this.token = localStorage.getItem('photoArtsAdminToken');
    this.content_details = data;
    this.start_date = moment().subtract(6, 'days').format('YYYY-MM-DD');
    this.end_date = moment().format('YYYY-MM-DD');
    this.selected_range = this.start_date + " to " + this.end_date;
    this.itemsPerPageArray = [
      { 'itemPerPageValue': '15', 'itemPerPageName': '15' },
      { 'itemPerPageValue': '30', 'itemPerPageName': '30' },
      { 'itemPerPageValue': '45', 'itemPerPageName': '45' },
      { 'itemPerPageValue': '60', 'itemPerPageName': '60' },
      { 'itemPerPageValue': '75', 'itemPerPageName': '75' },
      { 'itemPerPageValue': '90', 'itemPerPageName': '90' },
      { 'itemPerPageValue': '100', 'itemPerPageName': '100' }
    ];
    this.itemsPerPage = this.itemsPerPageArray[0].itemPerPageValue;
    this.getStatisticsData(this.currentPage, this.itemsPerPage, this.sortByTagName, this.order_type_val);
  }

  ngOnInit() {
  }

  selectedDate(value: any, dateInput: any) {
    dateInput.start = value.start;
    dateInput.end = value.end;
  }

  updateRange(value: any) {
    this.start_date = value.start.format('YYYY-MM-DD');
    this.end_date = value.end.format('YYYY-MM-DD');
    this.selected_range = this.start_date + " to " + this.end_date;
  }

  getDataByRange() {
    this.getStatisticsData(this.currentPage, this.itemsPerPage, this.sortByTagName, this.order_type_val);
  }

  itemPerPageChanged(itemsPerPage) {
    this.currentPage = 1;
    this.itemsPerPage = itemsPerPage;
    this.getStatisticsData(this.currentPage, this.itemsPerPage, this.sortByTagName, this.order_type_val);
  }

  pageChanged(event) {
    this.currentPage = event;
    this.getStatisticsData(this.currentPage, this.itemsPerPage, this.sortByTagName, this.order_type_val);
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
    this.getStatisticsData(this.currentPage, this.itemsPerPage, this.sortByTagName, this.order_type_val);
  }

  getStatisticsData(currentPage, itemsPerPage, sortByTagName, order_type_val) {
    this.loading = this.dialog.open(LoadingComponent);
    this.token = localStorage.getItem('photoArtsAdminToken');
    this.dataService.postData('getSummaryDetailFromDiffServer',
      {
        "api_url": this.content_details.api_url,
        "category_id": this.content_details.category_id,
        "sub_category_id": this.content_details.sub_category_id,
        "from_date": this.start_date,
        "to_date": this.end_date,
        "page": currentPage,
        "item_count": itemsPerPage,
        "order_by": sortByTagName,
        "order_type": order_type_val
      }, {
        headers: {
          'Authorization': 'Bearer ' + this.token
        }
      }).subscribe(results => {
        if (results.code == 200) {
          if (results.data) {
            this.content_list = results.data.data.result;
            this.total_record = results.data.data.total_record;
          }
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
          this.getStatisticsData(currentPage, itemsPerPage, sortByTagName, order_type_val);
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

  resetAll() {
    this.currentPage = 1;
    this.order_type_val = undefined;
    this.sortByTagName = undefined;
    this.itemsPerPage = this.itemsPerPageArray[0].itemPerPageValue;
    this.getStatisticsData(this.currentPage, this.itemsPerPage, this.sortByTagName, this.order_type_val);
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

import { Component, ElementRef, OnInit, QueryList, ViewChild } from '@angular/core';
import { Observable, of } from 'rxjs';
import { map } from 'rxjs/operators';
import { DaterangepickerConfig } from 'ng2-daterangepicker'
import * as moment from 'moment/moment';
import { NbDialogService } from '@nebular/theme';
import { DataService } from 'app/data.service';
import { UpdateTagDialogComponent } from './update-tag-dialog/update-tag-dialog.component';
import { Router } from '@angular/router';
import { UtilService } from 'app/util.service';
import { ERROR } from 'app/app.constants';



@Component({
  selector: 'ngx-search',
  templateUrl: './search.component.html',
  styleUrls: ['./search.component.scss']
})
export class SearchComponent implements OnInit {

  @ViewChild('CatInput') catInput;
  @ViewChild('master') check;
  @ViewChild('checBoxes') ckeckboxes: QueryList<ElementRef>;

  selectedItem = '';

  selectedNumberOfItems = "25";

  numberOfItems: any;

  optionForCatagory: any;

  filterOptions$: Observable<string[]>;

  start = moment().subtract(7, 'days').format('YYYY-MM-DD');

  end = moment().format('YYYY-MM-DD');

  dataForTable: any = [];

  catagoryList: [];

  inputValSub: string = "All Templates";

  isNextPage: boolean;

  pageNum: number = 1;

  collection: any[];

  DataForDialog: any = [];

  allSubData: any = [];

  total_data: any;

  serchTage: string = "";

  serchQuery: string = "";

  selectedStauts = "1";

  selectedTag: string = "";

  subCatId: number = 66;

  token: string = localStorage.getItem('at');

  sortByTagName: any;

  order_type: any;

  order_type_val: any;

  checked: boolean = false;

  daterange: any = {};

  pre = "<";
  next = ">";

  masterCheck: boolean = false;

  public optionsD: any = {
    locale: { format: 'YYYY-MM-DD' },
    alwaysShowCalendars: false,
    ranges: {
      'Today': [moment(), moment()],
      'Yesterday': [moment().subtract(1, 'days'), moment()],
      'Week': [moment().subtract(7, 'days'), moment()],
      'Month': [moment().subtract(1, 'month'), moment()],
      'Year': [moment().subtract(1, 'year'), moment()],
    }
  }

  constructor(public dataOption: DaterangepickerConfig, public api: DataService, private dialogService: NbDialogService,
    private router: Router, private util: UtilService) {

    this.getSubCategory();
    this.onserch();
  }

  ngOnInit() {
    this.optionForCatagory = [];
    this.filterOptions$ = of(this.optionForCatagory);
  }

  // function for date selection
  public selectedDate(value: any, datepicker?: any) {
    datepicker.start = value.start;
    datepicker.end = value.end;

    this.daterange.start = value.start;
    this.daterange.end = value.end;
    this.daterange.label = value.label;

    this.start = this.daterange.start.format('YYYY-MM-DD');
    this.end = this.daterange.end.format('YYYY-MM-DD');
  }

  //Get list of subcategories
  getSubCategory() {
    const dataForCat = {
      "category_id": 2
    }
    this.api.postData("getAllSubCategory", dataForCat, { headers: { 'Authorization': 'Bearer ' + this.token } })
      .then(response => {
        if (response.code == 200) {
          this.allSubData = response.data.category_list;
          for (var i = 0; i < response.data.category_list.length; i++) {
            this.optionForCatagory.push(response.data.category_list[i].sub_category_name);
          }
        } else if (response.code == 201) {
          this.util.showError(response.message, 3000);
        } else {
          this.util.showError(ERROR.SERVER_ERR, 3000);
        }
      }, (error: any) => {
        console.log(error);
        this.util.showError(ERROR.SERVER_ERR, 3000);
      })
      .catch(error => {
        console.log(error);
        this.util.showError(ERROR.SERVER_ERR, 3000);
      });

  }

  //Select and deselect tags
  checkBox(event, index, row) {
    const tag = row.tag;
    if (event.target.checked == true) {
      this.DataForDialog.push(tag);
    }
    else {
      // Deselect tag if already selected
      this.DataForDialog.splice((this.DataForDialog.indexOf(tag)), 1)
    }
    this.disableAdd();
  }

  disableAdd() {
    // if no tags selected then add button remain disable
    if (this.DataForDialog.length > 0) {
      this.checked = true;
    } else if (this.DataForDialog.length <= 0) {
      this.checked = false;
    }
  }

  //function for auto-complate
  private filtered(value: string): string[] {
    const filteredValue = value.toLowerCase();
    return this.optionForCatagory.filter(optionValues => optionValues.toLowerCase().includes(filteredValue));
  }

  getFiltered(values: string): Observable<string[]> {
    return of(values).pipe(
      map(filteredString => this.filtered(filteredString)),
    );
  }

  onGetValue(event) {
    this.filterOptions$ = this.getFiltered(this.catInput.nativeElement.value);
  }

  SelectionChange($event) {
    this.filterOptions$ = this.getFiltered($event);
  }

  //search tags by search type
  onSerchButton() {

    // clear sorting
    this.sortByTagName = "";
    this.order_type_val = "";
    this.pageNum = 1;
    this.onserch();
  }

  //get list of table data
  onserch() {
    this.DataForDialog = [];
    this.disableAdd();
    this.masterCheck = false;

    let index = this.allSubData.findIndex(data => data.sub_category_name == this.inputValSub);
    if (index == -1) {
      this.subCatId = 66;
    } else {
      this.subCatId = this.allSubData[index].sub_category_id;
    }
    if (this.selectedItem == '0') {
      // this.selectedStauts = 1 -> success
      // this.selectedStauts = 0 -> fail
      this.serchTage = "is_success"
      if (this.selectedStauts == '1') {
        this.serchQuery = '1';
      } else {
        this.serchQuery = '0';
      }
    } else if (this.selectedItem == '1') {
      this.serchTage = 'tag';
      this.serchQuery = this.selectedTag;
    } else {
      //if no value is selected from filter
      this.selectedStauts = '1';
      this.serchTage = '';
      this.serchQuery = '';
      this.selectedTag = "";
    }
    //if input value is empty return
    if (this.inputValSub.trim() == "") {
      this.dataForTable = [];
      return
    }
    this.numberOfItems = this.selectedNumberOfItems;

    let data = {
      "page": this.pageNum,
      "item_count": this.numberOfItems,
      "start_date": this.start,
      "end_date": this.end,
      "sub_category_id": this.subCatId,
      "search_type": this.serchTage,
      "search_query": this.serchQuery,
      "order_by": this.sortByTagName,
      "order_type": this.order_type_val,
    }
    this.util.showPageLoader()
    this.api.postData("getAllSearchingDetailsForAdmin", data, { headers: { 'Authorization': 'Bearer ' + this.token } })
      .then((response: any) => {
        if (response.code == 200) {
          this.dataForTable = response.data.result;
          this.isNextPage = response.data.result.is_next_page;
          this.total_data = response.data.total_record;
          this.util.hidePageLoader();
        }
        else if (response.code == 201) {
          this.util.hidePageLoader();
          this.util.showError(response.message, 3000);
        }
        else {
          this.util.hidePageLoader();
          this.util.showError(ERROR.SERVER_ERR, 3000);
        }
      }, (error: any) => {
        console.log(error);
        this.util.hidePageLoader();
        this.util.showError(ERROR.SERVER_ERR, 4000);
      }).catch(error => {
        console.log(error);
        this.util.hidePageLoader();
        this.util.showError(ERROR.SERVER_ERR, 3000);
      })
  }

  //function for opening the dialog
  openDia() {
    let dialogRef = this.dialogService.open(UpdateTagDialogComponent, {
      closeOnBackdropClick: false,
      context: {
        //data that send to dialog
        dataFromPage: this.DataForDialog,
        startDate: this.start,
        endDate: this.end,
        subCatId: this.subCatId,
        checked: this.masterCheck
      }
      //function on closing dialog
    }).onClose.subscribe(checks => {
      if (checks.data == false) {
        this.onserch();
        this.DataForDialog = [];
        this.disableAdd();
      }
    })
  }

  //selecting number of items per page
  typeChange() {
    this.pageNum = 1;
    this.onserch();
  }

  //function for shorting tabale data
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
    this.onserch();
  }

  //function for pagination
  handlePageChange(event) {
    this.pageNum = event;
    this.onserch();
  }

  //function for refreshing the content count
  onRefresh(tagName, id) {
    this.util.showPageLoader()
    let data = {
      "page": 1,
      "item_count": this.numberOfItems,
      "search_category": tagName,
      "sub_category_id": this.subCatId,
      'search_tag_id': id
    }
    this.api.postData('refreshSearchCountByAdmin', data, { headers: { 'Authorization': 'Bearer ' + this.token } })
      .then((response: any) => {
        if (response.code == 200) {
          let c_c = response.data.total_record
          let i = this.dataForTable.findIndex(table => table.id == id)
          this.dataForTable[i].content_count = c_c
          this.util.hidePageLoader();
          this.util.showSuccess(response.message, 3000);
        }
        else if (response.code == 427) {
          this.util.hidePageLoader();
          this.util.showError(response.message, 3000);
        }
        else if (response.code == 201) {
          this.util.hidePageLoader();
          this.util.showError(response.message, 3000);
        }
        else {
          this.util.hidePageLoader();
          this.util.showError(ERROR.SERVER_ERR, 3000);
        }
      }, (error: any) => {
        console.log(error);
        this.util.hidePageLoader();
        this.util.showError(ERROR.SERVER_ERR, 4000);
      })
      .catch(error => {
        console.log(error);
        this.util.hidePageLoader();
        this.util.showError(ERROR.SERVER_ERR, 3000)
      });
  }

  //function for selecting all check box
  selectAllCheckBox(event) {
    this.masterCheck = !this.masterCheck;
    if (this.masterCheck == true) {
      for (let i = 0; i < this.dataForTable.length; i++) {
        this.DataForDialog.push(this.dataForTable[i].tag);
      }
    } else {
      //function for unselecting all tags 
      this.DataForDialog = [];
    }
    this.disableAdd();
  }
}

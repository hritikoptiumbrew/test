import { Component, OnInit, ViewChild } from '@angular/core';
import { Observable, of } from 'rxjs';
import { map } from 'rxjs/operators';
import { DaterangepickerConfig } from 'ng2-daterangepicker'
import * as moment from 'moment/moment';
import { NbDialogService } from '@nebular/theme';
import { DataService } from 'app/data.service';
import { UpdateTagDialogComponent } from './update-tag-dialog/update-tag-dialog.component';
import { Router } from '@angular/router';
import { UtilService } from 'app/util.service';
// import { DialogComponent } from './dialog/dialog.component';


@Component({
  selector: 'ngx-search',
  templateUrl: './search.component.html',
  styleUrls: ['./search.component.scss']
})
export class SearchComponent implements OnInit {

  @ViewChild('CatInput') catInput;

  selectedItem = '';

  selectedNumberOfItems: number = 10;
  numberOfItems: number;

  // auto input-------------
  optionForCatagory: any;

  filterOptions$: Observable<string[]>;

  start = moment().subtract(7, 'days').format('YYYY-MM-DD');

  end = moment().format('YYYY-MM-DD');

  reqData: object;

  dataForTable: any = [];

  catagoryList: [];

  inputValSub: string = "Flyer Maker Stickers";

  isNextPage: boolean;

  pageNum: number = 1;

  collection: any[];

  DataForDialog = [];

  allSubData: any = [];

  total_data;

  serchTage: string = "";

  serchQuery: string = "";

  selectedStauts = "";

  selectedTag: string = "";

  subCatId: number = 60;

  token: string = localStorage.getItem('at');

  sortByTagName: any;

  order_type: any;

  order_type_val: any;

  checked = false;

  public daterange: any = {};

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

  public selectedDate(value: any, datepicker?: any) {
    datepicker.start = value.start;
    datepicker.end = value.end;

    this.daterange.start = value.start;
    this.daterange.end = value.end;
    this.daterange.label = value.label;

    this.start = this.daterange.start.format('YYYY-MM-DD');
    this.end = this.daterange.end.format('YYYY-MM-DD');
  }

  pre = "<";
  next = ">";

  constructor(public dataOption: DaterangepickerConfig, public api: DataService, private dialogService: NbDialogService,
    private router: Router , private util:UtilService) {
    const dataForCat = {
      "category_id": 2
    }

    this.api.postData("getAllSubCategory", dataForCat, { headers: { 'Authorization': 'Bearer ' + this.token } })
      .then(response => {
        this.allSubData = response.data.category_list;
        for (var i = 0; i < response.data.category_list.length; i++) {
          this.optionForCatagory.push(response.data.category_list[i].sub_category_name)
        }
      })

    this.onserch()
  }

  ngOnInit() {
    this.optionForCatagory = [];
    this.filterOptions$ = of(this.optionForCatagory);

  }

  checkBox(event, index, row) {

    const tag = row.tag;
    if (this.DataForDialog.indexOf(tag) < 0) {
      this.DataForDialog.push(tag);
    } else {
      this.DataForDialog.splice((this.DataForDialog.indexOf(tag)), 1)
    }
    // console.log(this.DataForDialog);
    this.disableAdd();
  }

  disableAdd() {
    if (this.DataForDialog.length > 0) {
      this.checked = true;
    } else if (this.DataForDialog.length <= 0) {
      this.checked = false;
    }
  }


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

  change() {

    // let index = this.allSubData.findIndex(data => data.sub_category_name == this.inputValSub);
    // // console.log(this.allSubData[index].sub_category_id)
    // if (index == -1) {
    //   this.subCatId = 60;
    // } else {
    //   this.subCatId = this.allSubData[index].sub_category_id;
    // }
    // console.log(this.subCatId);
  }

  SelectionChange($event) {
    this.filterOptions$ = this.getFiltered($event);
  }

  onserch() {
    this.DataForDialog = [];
    this.disableAdd()

    let index = this.allSubData.findIndex(data => data.sub_category_name == this.inputValSub);
    if (index == -1) {
      this.subCatId = 60;
    } else {
      this.subCatId = this.allSubData[index].sub_category_id;
    }
    if (this.selectedItem == '0') {
      this.serchTage = "is_success"
      if (this.selectedStauts == '1') {
        this.serchQuery = '1'
      } else {
        this.serchQuery = '0'
      }
    } else if (this.selectedItem == '1') {
      this.serchTage = 'tag'
      this.serchQuery = this.selectedTag;
    } else {
      this.serchTage = '';
      this.serchQuery = '';
      this.selectedTag = "";
    }
    this.numberOfItems = this.selectedNumberOfItems

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
    // this.util.showLoader();

    this.api.postData("getAllSearchingDetailsForAdmin", data, { headers: { 'Authorization': 'Bearer ' + this.token } })
      .then(response => {
        // this.util.hideLoader()
        this.dataForTable = response.data.result;
        this.isNextPage = response.data.result.is_next_page;
        this.total_data = response.data.total_record;
        // this.sourse.load(response.data.result)
        // console.log(this.dataForTable);

      }).catch(error => {
        console.log(error)

        // this.util.hideLoader()
      })
  }

  // inputValForDia:boolean = true
  openDia() {
    this.dialogService.open(UpdateTagDialogComponent, {
      context: {
        dataFromPage: this.DataForDialog,
        startDate: this.start,
        endDate: this.end,
        subCatId: this.subCatId
      }
    })

    // if(this.inputValForDia)
    // {
    //   this.DataForDialog.push(this.inputValSub);
    //   this.inputValForDia = false;
    // }
    // console.log(this.DataForDialog)
  }
  typeChange() {
    this.onserch()
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
    this.onserch();
  }

  handlePageChange(event) {
    this.pageNum = event;
    this.onserch();
    // console.log(this.pageNum)
  }

  onRefresh(tagName , id) {
    // this.selectedItem = '';
    // this.selectedNumberOfItems = 10;
    // this.selectedStauts = '';
    // this.inputValSub ="Flyer Maker Stickers";
    // this.start = moment().format('YYYY-MM-DD');
    // this.end = moment().format('YYYY-MM-DD');
    // this.dataForTable = [];
    // this.pageNum = 1;
    // this.total_data = "";
    let data = {
      "page": this.pageNum,   
      "item_count": this.numberOfItems, 
      "search_category": tagName,   
      "sub_category_id": this.subCatId,
      'search_tag_id':id
    }
    this.api.postData('searchCardsBySubCategoryIdForAdmin',  data , { headers: { 'Authorization': 'Bearer ' + this.token } })
    .then(response =>{
      let c_c = response.data.total_record
      let i = this.dataForTable.findIndex(table => table.id == id)
      // console.log(this.dataForTable[i].content_count)
      this.dataForTable[i].content_count = c_c

    })
  }
}

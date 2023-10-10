import { Component, ElementRef, OnInit, TemplateRef, ViewChild, ChangeDetectorRef  } from '@angular/core';
import { NbDialogService } from '@nebular/theme';
import { ERROR } from 'app/app.constants';
import { AddindustryComponent } from 'app/components/addindustry/addindustry.component';
import { AddthemeComponent } from 'app/components/addtheme/addtheme.component';
import { AddthemepostComponent } from 'app/components/addthemepost/addthemepost.component';
import { RepeatThemesComponent } from 'app/components/repeat-themes/repeat-themes.component';
import { DataService } from 'app/data.service';
import { UtilService } from 'app/util.service';
import { NgbModal, NgbModule,NgbPopover } from '@ng-bootstrap/ng-bootstrap';
import {
  ChangeDetectionStrategy,
} from '@angular/core';
import {
  startOfDay,
  endOfDay,
  subDays,
  addDays,
  endOfMonth,
  isSameDay,
  isSameMonth,
  addHours,
} from 'date-fns';
import { Subject, Observable, of } from 'rxjs';
import { map, startWith } from 'rxjs/operators';
import {
  CalendarDateFormatter,
  CalendarEvent,
  CalendarEventAction,
  CalendarEventTimesChangedEvent,
  CalendarMonthViewBeforeRenderEvent,
  CalendarMonthViewDay,
  CalendarView
  
} from 'angular-calendar';
import { EventColor } from 'calendar-utils';
import { CustomDateFormatter } from './custom-date-formatter.providers';
import { DatePipe } from '@angular/common';
import { FormControl } from '@angular/forms';

const colors: Record<string, EventColor> = {
  red: {
    primary: '#ad2121',
    secondary: '#FAE3E3',
  },
  blue: {
    primary: '#1e90ff',
    secondary: '#D1E8FF',
  },
  yellow: {
    primary: '#e3bc08',
    secondary: '#FDF1BA',
  },
};

@Component({
  selector: 'ngx-postcalender',
  changeDetection: ChangeDetectionStrategy.OnPush,
  templateUrl: './postcalender.component.html',
  styleUrls: ['./postcalender.component.scss'],
  providers: [
    {
      provide: CalendarDateFormatter,
      useClass: CustomDateFormatter,
    },
  ],
})
export class PostcalenderComponent implements OnInit {

  @ViewChild('modalContent', { static: true }) modalContent: TemplateRef<any>;

  view: CalendarView = CalendarView.Month;
  CalendarView = CalendarView;
  viewDate: Date = new Date();
  modalData: {
    action: string;
    event: CalendarEvent;
  };
  refresh = new Subject<void>();
  events: CalendarEvent[] = [];
  activeDayIsOpen: boolean = false;
  pageSizeOfIndustry: any = [20,40,60,80,100];
  pageSizeOfTheme: any = [20,40,60,80,100];
  selectedPageSizeOfIndustry: any = '100';
  selectedPageSizeOfTheme: any = '100';
  pageNumOfIndustry: number = 1;
  pageNumOfTheme: number = 1;
  previousLabel = "<";
  nextLabel = ">";
  selected_sub_category_id:any;
  industry_list = [];
  industry_list_selection = [];
  industry_list_selection_active = [];
  theme_list_selection = [];
  total_industry:any;
  theme_list:any = [];
  total_theme:any;
  selectedIndustry:any = "";
  selectedIndustryName:any = "";
  scheduledPostList:any = [];

  options: any[];
  filteredOptions$: Observable<string[]>;
  @ViewChild('autoInput') input;
  @ViewChild('autoInput') autoInput: ElementRef;
  
  constructor(
    private dialog: NbDialogService,  
    private utils: UtilService,
    private dataService: DataService,
    private datePipe: DatePipe,
    public modal: NgbModal,
    private cdr:ChangeDetectorRef
    ) {}

  ngOnInit(): void {
    let selected_sub_category = JSON.parse(localStorage.getItem("selected_sub_category"));
    this.selected_sub_category_id = selected_sub_category.sub_category_id;
  }

  dayClicked({ date, events }: { date: Date; events: CalendarEvent[] }): void {
    if (isSameMonth(date, this.viewDate)) {
      if (
        (isSameDay(this.viewDate, date) && this.activeDayIsOpen === true) ||
        events.length === 0
      ) {
        this.activeDayIsOpen = false;
      } else {
        this.activeDayIsOpen = true;
      }
      this.viewDate = date;
    }
  }

  eventTimesChanged({
    event,
    newStart,
    newEnd,
  }: CalendarEventTimesChangedEvent): void {
    this.events = this.events.map((iEvent) => {
      if (iEvent === event) {
        return {
          ...event,
          start: newStart,
          end: newEnd,
        };
      }
      return iEvent;
    });
    this.handleEvent('Dropped or resized', event);
  }

  handleEvent(action: string, event: CalendarEvent): void {
    this.modalData = { event, action };
  }

  addEvent(): void {
    this.events = [
      ...this.events,
      {
        title: 'New event',
        start: startOfDay(new Date()),
        end: endOfDay(new Date()),
        color: colors.red,
        draggable: true,
        resizable: {
          beforeStart: true,
          afterEnd: true,
        },
      },
    ];
  }

  deleteEvent(eventToDelete: CalendarEvent) {
    this.events = this.events.filter((event) => event !== eventToDelete);
  }

  setView(view: CalendarView) {
    this.view = view;
  }

  closeOpenMonthViewDay() {
    this.activeDayIsOpen = false;
    this.getScheduledPostDetails();
  }

  getDate(date){
    return date.toString().substring(8, 10);
  }

  getDateForPost(date){
    return  this.datePipe.transform(date, 'yyyy-MM-dd')
  }

  addBtnVisibility(date){
    let currentPost = this.scheduledPostList.filter(post => post.schedule_date === this.datePipe.transform(date, 'yyyy-MM-dd') );

    if(currentPost.length === 1) {
      return true
    }
    return false;
  }

  applyDateSelectionPolicy({ body }: { body: CalendarMonthViewDay[] }): void {
    body.forEach(day => {
      if (day.date.getTime() < new Date().getTime()) {
        day.cssClass = 'cell-disabled';
      }
    });
  } 

  GetCurrentDate() {
      this.getScheduledPostDetails();
  }
  
  getThemeBySubCategoryId(){
    this.utils.showPageLoader();
    this.dataService.postData('getThemeBySubCategoryId',
      {
        "sub_category_id": this.selected_sub_category_id,
        "page": this.pageNumOfTheme,
        "item_count": this.selectedPageSizeOfTheme
      }, {
      headers: {
        'Authorization': 'Bearer ' + localStorage.getItem('at')
      }
    }).then((results: any) => {

      if (results.code == 200) {
        this.theme_list = results.data.theme_list;
        this.theme_list.forEach(element => {
          let yourDate = new Date(element.created_at + ' UTC');
          element.created_at = yourDate.toString();
        });
        this.total_theme = results.data.total_record
        this.cdr.detectChanges();
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
      this.utils.hidePageLoader();
      this.utils.showError(ERROR.SERVER_ERR, 4000);
    }).catch((error: any) => {
      this.utils.hidePageLoader();
      this.utils.showError(ERROR.SERVER_ERR, 4000);
    });
  }

  getIndustryBySubCategoryId(){
    this.utils.showPageLoader();
    this.dataService.postData('getIndustryBySubCategoryIdForAdmin',
      {
        "sub_category_id": this.selected_sub_category_id,
        "page": this.pageNumOfIndustry,
        "item_count": this.selectedPageSizeOfIndustry
      }, {
      headers: {
        'Authorization': 'Bearer ' + localStorage.getItem('at')
      }
    }).then((results: any) => {

      if (results.code == 200) {
        
        this.industry_list = results.data.industry_list;
        this.industry_list.forEach(element => {
          let yourDate = new Date(element.created_at + ' UTC');
          element.created_at = yourDate.toString();
        });
        this.total_industry = results.data.total_record;
        this.cdr.detectChanges();
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
      this.utils.hidePageLoader();
      this.utils.showError(ERROR.SERVER_ERR, 4000);
    }).catch((error: any) => {
      this.utils.hidePageLoader();
      this.utils.showError(ERROR.SERVER_ERR, 4000);
    });
  }

  getIndustryForSelection(){
    this.industry_list_selection_active = [];
    this.utils.showPageLoader();
    this.dataService.postData('getIndustryBySubCategoryIdForAdmin',
      {
        "sub_category_id": this.selected_sub_category_id,
        "page": 1,
        "item_count":1000
      }, {
      headers: {
        'Authorization': 'Bearer ' + localStorage.getItem('at')
      }
    }).then((results: any) => {

      if (results.code == 200) {
        this.industry_list_selection = results.data.industry_list;
        if(this.industry_list_selection.length != 0){
          this.industry_list_selection.forEach((element,index) => {
            if(element.is_active == 1){
              this.industry_list_selection_active.push(element);
            }
          });
        }

        let tempIndustryNameArr = [];
        this.industry_list_selection_active.forEach(element => {
          tempIndustryNameArr.push(element.industry_name);
        });
        this.options = this.industry_list_selection_active;
        this.filteredOptions$ = of(tempIndustryNameArr);

        if(this.industry_list_selection_active.length == 0){
          this.selectedIndustry = "";
          $("#select_industry").children().text("Select Your Industry");
          $("#select_industry").children().addClass("placeholder");
        }
        else{
          this.industry_list_selection_active.forEach((element,index) => {
            if(index == 0){
              $("#select_industry").children().text(element.industry_name);
              $("#select_industry").children().removeClass("placeholder");
              this.selectedIndustry = element.id.toString();
              this.selectedIndustryName = element.industry_name;
              this.autoInput.nativeElement.value = element.industry_name;
            } 
          }); 

          this.getScheduledPostDetails();
        }

        this.cdr.detectChanges();
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
      this.utils.hidePageLoader();
      this.utils.showError(ERROR.SERVER_ERR, 4000);
    }).catch((error: any) => {
      this.utils.hidePageLoader();
      this.utils.showError(ERROR.SERVER_ERR, 4000);
    });
  }

  private filter(value: string): string[] {
    const filterValue = value.toLowerCase();
    let tempFilterValue = [];
    this.options.forEach(element => {
      tempFilterValue.push(element.industry_name);
    });
    return tempFilterValue.filter(optionValue => optionValue.toLowerCase().includes(filterValue));
  }

  getFilteredOptions(value: string): Observable<string[]> {
    return of(value).pipe(
      map(filterString => this.filter(filterString)),
    );
  }

  onChange() {
    this.filteredOptions$ = this.getFilteredOptions(this.input.nativeElement.value);
  }

  onSelectionChange($event) {
    this.filteredOptions$ = this.getFilteredOptions($event);
  }

  getThemeForSelection(){
    this.utils.showPageLoader();
    this.dataService.postData('getThemeBySubCategoryId',
      {
        "sub_category_id": this.selected_sub_category_id,
        "page": 1,
        "item_count":100
      }, {
      headers: {
        'Authorization': 'Bearer ' + localStorage.getItem('at')
      }
    }).then((results: any) => {

      if (results.code == 200) {
        this.theme_list_selection = results.data.theme_list;
        
        this.cdr.detectChanges();
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
      this.utils.hidePageLoader();
      this.utils.showError(ERROR.SERVER_ERR, 4000);
    }).catch((error: any) => {
      this.utils.hidePageLoader();
      this.utils.showError(ERROR.SERVER_ERR, 4000);
    });
  }

  getScheduledPostDetails(){
    let dt = new Date(this.viewDate)
    this.utils.showPageLoader();
    this.dataService.postData('getScheduledPostDetails',
      {
        "sub_category_id": this.selected_sub_category_id,
        "month": dt.getMonth() + 1,
        "year": dt.getFullYear(),
        "industry_id": Number(this.selectedIndustry)
      }, {
      headers: {
        'Authorization': 'Bearer ' + localStorage.getItem('at')
      }
    }).then((results: any) => {

      if (results.code == 200) {
        this.scheduledPostList = results.data.scheduled_post_list;
        this.cdr.detectChanges();
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
      this.utils.hidePageLoader();
      this.utils.showError(ERROR.SERVER_ERR, 4000);
    }).catch((error: any) => {
      this.utils.hidePageLoader();
      this.utils.showError(ERROR.SERVER_ERR, 4000);
    });
  }

  deleteScheduledPost(scheduledPost){
    this.utils.getConfirm().then((result) => {
      if(result){
        this.utils.showLoader();
        let request_data = {
          "post_schedule_id": scheduledPost.id
        }
        this.dataService.postData('deleteScheduledPost', request_data,
          {
            headers:
              { 'Authorization': 'Bearer ' + localStorage.getItem('at') }
          })
          .then(response => {
            if (response.code == 200) {
              this.utils.hideLoader();
              this.getScheduledPostDetails();
              this.utils.showSuccess(response.message, 3000);
            } else if (response.code == 201) {
              this.utils.hideLoader();
              this.utils.showError(response.message, 3000);
            }
            else {
              this.utils.hideLoader();
              this.utils.showError(ERROR.SERVER_ERR, 3000);
            }
          })
          .catch(e => {
            this.utils.hideLoader();
            this.utils.showError(ERROR.SERVER_ERR, 3000);
          })
      }
      
    });
  }

  tabChanged(event){
    if(event.tabTitle == "Schedule Management"){
      this.getThemeForSelection();
      this.getIndustryForSelection();
    }
    else if(event.tabTitle == "Theme Management"){
      this.selectedPageSizeOfTheme = '100';
      this.pageNumOfTheme = 1;
      this.getThemeBySubCategoryId();
    }
    else if(event.tabTitle == "industry Management"){
      this.selectedPageSizeOfIndustry = '100';
      this.pageNumOfIndustry = 1;
      this.getIndustryBySubCategoryId();
    }
  }

  setPageSizeOfIndustry(value) {
    this.selectedPageSizeOfIndustry = value;
    const element = document.getElementById("industry-table-body");
    element.scrollTo(0, 0);
    this.pageNumOfIndustry = 1;
    this.getIndustryBySubCategoryId();
  }

  setPageSizeOfTheme(value) {
    this.selectedPageSizeOfTheme = value;
    const element = document.getElementById("theme-table-body");
    element.scrollTo(0, 0);
    this.pageNumOfTheme = 1;
    this.getThemeBySubCategoryId();
  }

  addIndustry(){
    this.openAddIndustry(false, '');
  }

  protected openAddIndustry(closeOnBackdropClick: boolean,data) {
    let dialogRef =  this.dialog.open(AddindustryComponent,  {
      closeOnBackdropClick,closeOnEsc: false,
      context: {
        btnText: "add industry",
        dialogTitle: "add industry",
        sub_category_id: this.selected_sub_category_id
      },
    }).onClose.subscribe((result) => {
      if (result.res == "add") {
        this.getIndustryBySubCategoryId();
      }
    });
   
  }

  // selectedChangeIndustry(value){
  //   this.industry_list_selection_active.forEach(element => {
  //     if(element.id == value){
  //       $("#select_industry").children().text(element.industry_name);
  //       $("#select_industry").children().removeClass("placeholder");
  //     }
  //   });
    
  //   this.selectedIndustry = value;
  //   this.getScheduledPostDetails();
  // }

  changeSelectedIndustry(value){
    this.industry_list_selection_active.forEach(element => {
      if(element.industry_name == value){
        this.selectedIndustry = element.id;
        this.selectedIndustryName = element.industry_name;
      }
    });
    this.getScheduledPostDetails();
  }

  addTheme(){
    this.openAddTheme(false, '');
  }

  protected openAddTheme(closeOnBackdropClick: boolean,data) {
    let dialogRef =  this.dialog.open(AddthemeComponent,  {
      closeOnBackdropClick,closeOnEsc: false,
      context: {
        dialogTitle: 'Add Theme',
        btnText: 'Add Theme',
        sub_category_id: this.selected_sub_category_id
      }
    }).onClose.subscribe((result) => {
      if (result.res == "add") {
        this.getThemeBySubCategoryId();
      }
    });
   
  }

  updateTheme(theme){
    this.openEditTheme(false, theme);
  }

  protected openEditTheme(closeOnBackdropClick: boolean,theme) {
    let dialogRef =  this.dialog.open(AddthemeComponent,  {
      closeOnBackdropClick,closeOnEsc: false,
      context: {
        btnText: "Update Theme",
        dialogTitle: "Edit theme",
        theme: theme
      }
    }).onClose.subscribe((result) => {
      if (result.res == "update") {
        this.getThemeBySubCategoryId();
      }
    });
  }

  setRankIndistry(industry){
    this.utils.showLoader();
    let request_data = {
      "industry_id": industry.id
    }
    this.dataService.postData('setIndustryRankOnTheTopByAdmin', request_data,
      {
        headers:
          { 'Authorization': 'Bearer ' + localStorage.getItem('at') }
      })
      .then(response => {
        if (response.code == 200) {
          this.utils.hideLoader();
          this.getIndustryBySubCategoryId();
          this.utils.showSuccess(response.message, 3000);
        } else if (response.code == 201) {
          this.utils.hideLoader();
          this.getIndustryBySubCategoryId();
          this.utils.showError(response.message, 3000);
        }
        else {
          this.utils.hideLoader();
          this.utils.showError(ERROR.SERVER_ERR, 3000);
        }
      })
      .catch(e => {
        this.utils.hideLoader();
        this.utils.showError(ERROR.SERVER_ERR, 3000);
      })
  }

  updateIndustry(industry){
    this.openEditIndustry(false, industry);
  }

  protected openEditIndustry(closeOnBackdropClick: boolean,industry) {
    let dialogRef =  this.dialog.open(AddindustryComponent,  {
      closeOnBackdropClick,closeOnEsc: false,
      context: {
        btnText: "Update industry",
        dialogTitle: "Edit industry",
        industry: industry,
        sub_category_id: this.selected_sub_category_id
      }
    }).onClose.subscribe((result) => {
      if (result.res == "update") {
        this.getIndustryBySubCategoryId();
      }
    });
   
  }

  deleteIndustry(industry){
        let request_data = {
          "sub_category_id": industry.sub_category_id,
          "industry_id": industry.id,
          "is_active": industry.is_active == 1 ? 1:0
        }
        this.dataService.postData('deleteIndustry', request_data,
          {
            headers:
              { 'Authorization': 'Bearer ' + localStorage.getItem('at') }
          })
          .then(response => {
            if (response.code == 200) {
              this.utils.hideLoader();
              this.getIndustryBySubCategoryId();
              this.utils.showSuccess(response.message, 3000);
            } else if (response.code == 201) {
              this.utils.hideLoader();
              this.getIndustryBySubCategoryId();
              this.utils.showError(response.message, 3000);
            }
            else {
              this.utils.hideLoader();
              this.utils.showError(ERROR.SERVER_ERR, 3000);
            }
          })
          .catch(e => {
            this.utils.hideLoader();
            this.utils.showError(ERROR.SERVER_ERR, 3000);
          })
      
  }

  deleteTheme(theme){
    this.utils.getConfirm().then((result) => {
      if(result){
      this.utils.showLoader();
      let request_data = {
        "sub_category_id": theme.sub_category_id,
        "theme_id": theme.id
      }
      this.dataService.postData('deleteTheme', request_data,
        {
          headers:
            { 'Authorization': 'Bearer ' + localStorage.getItem('at') }
        })
        .then(response => {
          if (response.code == 200) {
            this.utils.hideLoader();
            this.getThemeBySubCategoryId();
            this.utils.showSuccess(response.message, 3000);
          } else if (response.code == 201) {
            this.utils.hideLoader();
            this.utils.showError(response.message, 3000);
          }
          else {
            this.utils.hideLoader();
            this.utils.showError(ERROR.SERVER_ERR, 3000);
          }
        })
        .catch(e => {
          this.utils.hideLoader();
          this.utils.showError(ERROR.SERVER_ERR, 3000);
        })
      }
    });
  }

  repeatThemes(){
    this.openRepeatThemes(false, '');
  }

  protected openRepeatThemes(closeOnBackdropClick: boolean,data) {
    let dt = new Date(this.viewDate)
    let dialogRef =  this.dialog.open(RepeatThemesComponent,  {
      closeOnBackdropClick,closeOnEsc: false,
      context: {
        from_month : dt.getMonth() + 1,
        from_year : dt.getFullYear(),
        selected_sub_category_id: this.selected_sub_category_id,
        selected_industry_id: this.selectedIndustry
      }
    }).onClose.subscribe((result) => {
      if (result.res == "repeatPost") {
        this.getScheduledPostDetails();
      }
    });
   
  }

  addThemePost(clickedDay){
    let date = this.datePipe.transform(clickedDay, 'yyyy-MM-dd');
    if(typeof this.selectedIndustry == 'undefined' || this.selectedIndustry == ""){
      this.utils.showError("Please add industry to add new post.", 3000);
      return false;
    }
    else if(this.theme_list_selection.length == 0){
      this.utils.showError("Please add theme to add new post.", 3000);
      return false;
    }
    else{
      this.openAddThemePost(false, date);
    }
  }

  protected openAddThemePost(closeOnBackdropClick: boolean,date) {
    let dialogRef =  this.dialog.open(AddthemepostComponent,  {
      closeOnBackdropClick,closeOnEsc: false,
      context: {
        selected_sub_category_id: this.selected_sub_category_id,
        selected_date_for_post: date,
        selected_industry_id: this.selectedIndustry,
        header_text: "Add Theme"

      }
    }).onClose.subscribe((result) => {
      if (result.res == "postAdded") {
        this.getScheduledPostDetails();
      }
    });
   
  }

  updateThemePost(clickedDay, selected_theme_name, selected_arr_temp_list, selected_tags, post_schedule_id){
    let date = this.datePipe.transform(clickedDay, 'yyyy-MM-dd');
    if(typeof this.selectedIndustry == 'undefined'){
      this.utils.showError("Please select industry to add new post.", 3000);
      return false;
    }
    else{
      this.openUpdateThemePost(false, date, selected_theme_name, selected_arr_temp_list, selected_tags, post_schedule_id);
    }
  }

  protected openUpdateThemePost(closeOnBackdropClick: boolean,date, selected_theme_name, selected_arr_temp_list, selected_tags, post_schedule_id) {
    let dialogRef =  this.dialog.open(AddthemepostComponent,  {
      closeOnBackdropClick,closeOnEsc: false,
      context: {
        selected_sub_category_id: this.selected_sub_category_id,
        selected_date_for_post: date,
        selected_industry_id: this.selectedIndustry,
        header_text: "Update Theme",
        selected_theme_name: selected_theme_name,
        selected_arr_temp_list: selected_arr_temp_list,
        selected_tags: selected_tags,
        post_schedule_id: post_schedule_id
      }
    }).onClose.subscribe((result) => {
      if (result.res == "postUpdated") {
        this.getScheduledPostDetails();
      }
    });
  }

  handlePageChangeOfIndustry(event) {
    this.pageNumOfIndustry = event;
    const element = document.getElementById("industry-table-body");
    element.scrollTo(0, 0);
    this.getIndustryBySubCategoryId();
  }

  handlePageChangeOfTheme(event) {
    this.pageNumOfTheme = event;
    const element = document.getElementById("theme-table-body");
    element.scrollTo(0, 0);
    this.getThemeBySubCategoryId();
  }

}
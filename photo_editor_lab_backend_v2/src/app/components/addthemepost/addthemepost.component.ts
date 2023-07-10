import { E } from '@angular/cdk/keycodes';
import { Component, OnInit } from '@angular/core';
import { NbDialogRef } from '@nebular/theme';
import { ERROR } from 'app/app.constants';
import { DataService } from 'app/data.service';
import { UtilService } from 'app/util.service';
import { Console } from 'console';
import { fromEvent } from 'rxjs';
import { DatePipe } from '@angular/common'
import { ChangeDetectorRef } from '@angular/core';

@Component({
  selector: 'ngx-addthemepost',
  templateUrl: './addthemepost.component.html',
  styleUrls: ['./addthemepost.component.scss'],
})
export class AddthemepostComponent implements OnInit {

  selectedThemeName:any;
  selectedThemeDesc:any;
  selectedThemeId:any;
  selectedTheme:any;
  tagList:any = [];
  tagInput:any;
  arrange_post:any = [];
  selected_sub_category_id:any;
  selected_date_for_post:any;
  selected_industry_id:any;
  theme_list:any = [];
  templates:any = [];
  header_text:any;
  selected_theme_id:any;
  selected_tags:any;
  post_schedule_id:any;

  searchQuery:any;
  is_next_pageOfTemplates: boolean = false;
  currentPageOfTemplates: number = 1;
  date_array:any = [];
  public selectedWeekDates: Date [];
  selected_arr_temp_list:any;
  selected_theme_name:any;
  preview_date_arr = [];

  constructor(private dialogref: NbDialogRef<AddthemepostComponent>,private dataService: DataService,
    private utils: UtilService,public datePipe: DatePipe,private cdr: ChangeDetectorRef) { 
      this.utils.dialogref = this.dialogref;
    }

  ngOnInit(): void {
    const date = new Date(this.selected_date_for_post);
    for(let i = 0; i < 3; i++){
      this.preview_date_arr.push(this.datePipe.transform(date.setDate(date.getDate() - 1), 'yyyy-MM-dd'))
    }
    this.preview_date_arr.reverse().push(this.datePipe.transform(this.selected_date_for_post, 'yyyy-MM-dd'))
    const date1 = new Date(this.selected_date_for_post);
    for(let i = 0; i < 3; i++){
      this.preview_date_arr.push(this.datePipe.transform(date1.setDate(date1.getDate() + 1), 'yyyy-MM-dd'))
    }
    
    this.getThemeBySubCategoryId();
    if(this.selected_tags){
      this.selected_tags.split(',').forEach(element => {
        this.tagList.push(element)
      });
    }

    if(this.selected_arr_temp_list){
      this.selected_arr_temp_list.forEach(element => {
        element.selected = true;
        element.compressed_img = element.sample_image;
        element.template_id = element.json_id;
        this.arrange_post.push(element)
      });
    }
  }

  getThemeBySubCategoryId(){
    this.utils.showPageLoader();
    this.dataService.postData('getThemeBySubCategoryId',
      {
        "sub_category_id": this.selected_sub_category_id,
        "page": 1,
        "item_count": 100
      }, {
      headers: {
        'Authorization': 'Bearer ' + localStorage.getItem('at')
      }
    }).then((results: any) => {

      if (results.code == 200) {
        this.theme_list = results.data.theme_list;
        if(this.selected_theme_name){
          this.theme_list.forEach(element => {
            if(element.theme_name == this.selected_theme_name){
              this.selected_theme_id =  element.id.toString();
              this.selectedThemeName = element.theme_name;
              this.selectedThemeDesc = element.short_description;
            }
          });
        }
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

  getTemplateBySubCategoryId(){
    if (typeof this.searchQuery == "undefined" || this.searchQuery == "" || this.searchQuery == null || this.searchQuery.trim() == "") {
      this.utils.showError("Please enter search query.", 3000);
      return false;
    }
    else{
      this.utils.showLoader();
      this.currentPageOfTemplates = 1;
      let request_data = {
        "sub_category_id":this.selected_sub_category_id,
        "search_category": this.searchQuery.trim(), 
        "page":this.currentPageOfTemplates,
        "item_count":15
      }
      this.dataService.postData('getAllTemplateBySearchTag', request_data,
        {
          headers:
            { 'Authorization': 'Bearer ' + localStorage.getItem('at') }
        })
        .then(response => {
          if (response.code == 200) {
            this.utils.hideLoader();
            this.is_next_pageOfTemplates = response.data.is_next_page;
            this.templates = response.data.result;
            this.templates.forEach(element => {
              if(this.arrange_post.length != 0){
                this.arrange_post.forEach(element2 => {
                  element.selected =  element2.template_id == element.template_id ? true : false;
                });
              }
              else{
                element.selected = false;
              }
              
            }); 

            if(this.arrange_post.length != 0){
              this.arrange_post.forEach(element => {
                this.templates.forEach(element2 => {
                  if(element.template_id == element2.template_id){
                    element2.selected = true;
                  }
                });
              });
            }

          let temp_div = document.getElementById("temp-arrange-container");
          temp_div.scrollTop = 0;
          this.cdr.detectChanges();

          } else if (response.code == 201) {
            this.utils.hideLoader();
            this.templates = [];
          }
          else {
            this.utils.hideLoader();
            this.utils.showError(ERROR.SERVER_ERR, 3000);
          }
        })
        .catch(e => {
          // console.log(e)
          this.utils.hideLoader();
          // this.utils.showError(ERROR.SERVER_ERR, 3000);
        })
    }
  }

  getMoreTemplateFromTag(){
    if (this.is_next_pageOfTemplates == true){
      this.utils.showLoader();
      this.currentPageOfTemplates++;
      let moreTemp;
      let request_data = {
        "sub_category_id":this.selected_sub_category_id,
        "search_category": this.searchQuery.trim(), 
        "page":this.currentPageOfTemplates,
        "item_count":15
      }
      this.dataService.postData('getAllTemplateBySearchTag', request_data,
        {
          headers:
            { 'Authorization': 'Bearer ' + localStorage.getItem('at') }
        })
        .then(response => {
          if (response.code == 200) {
            this.utils.hideLoader();
            moreTemp = response.data.result;
            this.is_next_pageOfTemplates = response.data.is_next_page;
            
            moreTemp.forEach(element => {
              element.selected = false
              this.templates.push(element);
            }); 

            if(this.arrange_post.length > 0){
              this.arrange_post.forEach(element => {
                this.templates.forEach(element2 => {
                  if(element.template_id == element2.template_id){
                    element2.selected = true;
                  }
                });
              });
            }
          } else if (response.code == 201) {
            this.utils.hideLoader();
            if(response.message == "PhotoEditorLab is unable to search templates."){
              moreTemp = [];
            }
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
  }

  scrollHandler(event){
    if (event.target.offsetHeight + event.target.scrollTop >= event.target.scrollHeight) {
      this.getMoreTemplateFromTag();
    }
  }

  onDrop(dropResult) {
    this.arrange_post = this.utils.applyDrag(this.arrange_post, dropResult);
  }

  closedialog() {
    this.dialogref.close({ res: "" });
  }

  changeTheme(selected){
    this.theme_list.forEach(element => {
      if(element.id == selected){
        this.selectedThemeName = element.theme_name
        this.selectedThemeDesc = element.short_description
        this.selectedThemeId = element.id
      }
    });
  }

  selectTemplate(id){
    this.templates.forEach(element => {
      if(element.template_id == id){
        if(element.selected == false){
          if(this.arrange_post.length < 10){
            this.arrange_post.push(element);
            element.selected = true;
          }
          else{
            this.utils.showError("You can select maximum 10 templates", 3000);
          }
        }
        else{
          element.selected = false;
          this.arrange_post.splice(this.arrange_post.findIndex(x => x.template_id === id), 1)
        }
      }
    });
  }

  deleteArrangePost(id){
    this.templates.forEach(element => {
      if(element.template_id == id){
        element.selected = false;
      }
    });
    this.arrange_post.splice(this.arrange_post.findIndex(x => x.template_id === id), 1)
  }

  addTag() {
    if (this.tagInput.length <= 0 && this.tagInput == '') {
      return;
    }
    else if (this.tagInput.trim().length <= 0) {
      return;
    }
    else {
      this.tagList.push(this.tagInput);
      this.tagInput = '';
    }
  }

  removeTag(tagName) {
    let index = this.tagList.indexOf(tagName);
    this.tagList.splice(index, 1)
  }

  addSchedulePost(){
    let template_ids = [];
    this.arrange_post.forEach(element => {
      template_ids.push(element.template_id);
    });

    if (typeof this.selectedThemeId == "undefined" || this.selectedThemeId == "" || this.selectedThemeId == null) {
      this.utils.showError("Please select theme.", 3000);
      return false;
    }
    else if (template_ids.length < 3) {
      this.utils.showError("Please select minimum 3 templates.", 3000);
      return false;
    }
    else if (this.tagList.length == 0) {
      this.utils.showError("Please enter search tags for relevant templates.", 3000);
      return false;
    }
    else{
      this.utils.showLoader();
      let request_data = {
        "sub_category_id":this.selected_sub_category_id,
        "post_industry_id": this.selected_industry_id, 
        "post_theme_id":this.selectedThemeId,
        "template_ids": template_ids,
        "schedule_date":this.selected_date_for_post,
        "tags": this.tagList.join(',')
      }
      this.dataService.postData('addSchedulePost', request_data,
        {
          headers:
            { 'Authorization': 'Bearer ' + localStorage.getItem('at') }
        })
        .then(response => {
          if (response.code == 200) {
            this.utils.hideLoader();
            this.dialogref.close({ res: "postAdded" });
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
  }

  updateScheduledPost(){
    let template_ids = [];
    this.arrange_post.forEach(element => {
      template_ids.push(element.template_id);
    });

    if (template_ids.length < 3) {
      this.utils.showError("Please select minimum 3 templates.", 3000);
      return false;
    }
    else if (this.tagList.length == 0) {
      this.utils.showError("Please enter search tags for relevant templates.", 3000);
      return false;
    }
    else{
      this.utils.showLoader();
      let request_data = {
        "post_schedule_id": this.post_schedule_id, 
        "sub_category_id":this.selected_sub_category_id,
        "post_industry_id": this.selected_industry_id, 
        "post_theme_id":this.selected_theme_id,
        "template_ids": template_ids,
        "schedule_date":this.selected_date_for_post,
        "tags": this.tagList.join(',')
      }
      this.dataService.postData('updateScheduledPost', request_data,
        {
          headers:
            { 'Authorization': 'Bearer ' + localStorage.getItem('at') }
        })
        .then(response => {
          if (response.code == 200) {
            this.utils.hideLoader();
            this.dialogref.close({ res: "postUpdated" });
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
  }

}

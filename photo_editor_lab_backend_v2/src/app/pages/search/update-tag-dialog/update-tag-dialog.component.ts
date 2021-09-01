import { Component, OnInit } from '@angular/core';
import { NbDialogRef } from '@nebular/theme';
import { ERROR } from 'app/app.constants';
import { DataService } from 'app/data.service';
import { UtilService } from 'app/util.service';

@Component({
  selector: 'ngx-update-tag-dialog',
  templateUrl: './update-tag-dialog.component.html',
  styleUrls: ['./update-tag-dialog.component.scss']
})
export class UpdateTagDialogComponent implements OnInit {

  tagList = [];

  inputVal: string = "";

  dataFromPage = [];

  filterDataFromPage = [];

  startDate: string;
  endDate: string;

  subCatId: number;

  imgData: any[] = [];

  listOfId: any = [];

  searchByTagData: any;

  pagNum: number = 1;

  token: string = localStorage.getItem('at');

  hightlight: boolean = false;

  showInputError: boolean = false;

  checked: boolean;

  pageLoader: boolean = false;

  constructor(protected dialogRef: NbDialogRef<any>, public api: DataService, public util: UtilService) {

  }

  ngOnInit(): void {
    //removing copied tag from arrey
    this.filterDataFromPage = [...new Set(this.dataFromPage)];
  }

  //for closing dialog
  close() {
    this.checked = true;
    this.dialogRef.close({ data: this.checked });
  }

  //function for adding tagname to tagList(array)
  onsubmit() {
    if (this.validateString(this.inputVal) && this.inputVal.trim() !== "") {
      let newInputValue = this.inputVal.split(',');
      for (let i = 0; i < newInputValue.length; i++) {
        this.tagList.push(newInputValue[i]);
      }
      this.inputVal = "";
      this.showInputError = false;
    }
    else {
      this.showInputError = true;
    }
  }

  //remove tag from list of tag
  removeTags(name) {
    const index = this.tagList.indexOf(name)
    this.tagList.splice(index, 1);
  }

  //validation for input value
  validateString(str) {
    var regex = /^[a-z0-9&,# ]+$/i.test(str);
    return regex;
  }

  //search templates by tag name
  searchByTags() {
    this.pagNum = 1;
    this.imgData = [];
    if (this.tagList.length > 0) {
      this.util.showLoader();
      this.searchByTag();
    }
    else {
      this.util.showError('Please enter tag name', 2000);
    }
  }

  //callin search API for list of Img
  searchByTag() {
    if (this.pagNum == 1) {
      this.imgData = [];
    }
    const data = {
      "page": this.pagNum,
      "item_count": 18,
      "sub_category_id": this.subCatId,
      "search_category": this.tagList.join(",")
    };
    this.api.postData("searchCardsBySubCategoryIdForAdmin", data, { headers: { 'Authorization': 'Bearer ' + this.token } })
      .then(resoponse => {
        if (resoponse.code == 200 || resoponse.code == 427) {
          for (let i = 0; i < resoponse.data.result.length; i++) {
            this.imgData.push(resoponse.data.result[i]);
          }
          this.searchByTagData = resoponse.data;
          this.checked = false;
          this.pageLoader = false;
          this.util.hideLoader()
        }
        else if (resoponse.code == 201) {
          this.util.hideLoader();
          this.pageLoader = false;
          this.util.showError(resoponse.message, 3000);
        }
        else {
          this.util.hideLoader();
          this.pageLoader = false;
          this.util.showError(ERROR.SERVER_ERR, 3000);
        }
      }, (error: any) => {
        console.log(error);
        this.util.hideLoader();
        this.util.showError(ERROR.SERVER_ERR, 4000);
      })
      .catch(error => {
        console.log(error);
        this.util.hideLoader();
        this.util.showError(ERROR.SERVER_ERR, 3000);
      })
  }

  //adding  jason_id of image to array
  addImgId(id, event) {
    const i = this.listOfId.indexOf(id);
    if (i == -1) {
      this.listOfId.push(id);
      this.hightlight = true;
    } else {
      this.listOfId.splice(i, 1);
      this.hightlight = false;
    }
    //hightlighting the selected template
    event.target.parentElement.classList.toggle('bgAdd');
  }

  //function for adding images to the tag name
  updateTag() {
    if (this.listOfId.length > 0) {
      const data = {
        "img_ids": this.listOfId.join(','),
        "search_category": this.filterDataFromPage.join(','),
        "sub_category_id": this.subCatId,
      }
      this.util.showLoader()
      this.api.postData("updateTemplateSearchingTagsByAdmin", data, { headers: { 'Authorization': 'Bearer ' + this.token } })
        .then(response => {
          if (response.code == 200) {
            this.dialogRef.close({ data: this.checked });
            this.util.hideLoader();
            this.util.showSuccess(response.message, 3000);
          } else if (response.code == 201) {
            this.util.hideLoader();
            this.util.showError(response.message, 3000);
          }
          else {
            this.util.hideLoader();
            this.util.showError(ERROR.SERVER_ERR, 3000);
          }
        })
        .catch(e => {
          this.util.hideLoader();
          console.log(e);
          this.util.showError(ERROR.SERVER_ERR, 3000);
        })
    }
    else {
      this.util.showError("Please select templates", 2000);
    }
  }

  //function for loading next page
  loadNext() {
    if (this.imgData.length > 2) {
      if (this.searchByTagData.is_next_page == true) {
        this.pagNum += 1;
        this.pageLoader = true;
        this.searchByTag();
      }
    }
  }
}


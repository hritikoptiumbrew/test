import { Component, OnInit } from '@angular/core';
import { NbDialogRef } from '@nebular/theme';
import { DataService } from 'app/data.service';
import { UtilService } from 'app/util.service';

@Component({
  selector: 'ngx-update-tag-dialog',
  templateUrl: './update-tag-dialog.component.html',
  styleUrls: ['./update-tag-dialog.component.scss']
})
export class UpdateTagDialogComponent implements OnInit {

  tagList = [];

  inputVal = "";

  dataFromPage = [];

  startDate: string;
  endDate: string;
  subCatId: number;

  responseData: any[] = [];

  listOfId = [];

  responseDataFull;

  pagNum: number = 1;

  token: string = localStorage.getItem('at');

  constructor(protected dialogRef: NbDialogRef<any>, public api: DataService, public util: UtilService) { }

  ngOnInit(): void {
  }
  close() {
    this.dialogRef.close();
  }
  onsubmit() {
    this.tagList.push(this.inputVal);
    this.inputVal = "";
  }
  removeTags(name) {
    const index = this.tagList.indexOf(name)
    this.tagList.splice(index, 1);
  }

  searchByTags() {
    this.responseData = []
    this.searchByTag()
  }
  searchByTag() {
    if (this.pagNum == 1) {
      this.responseData = [];
    }
    const data = {
      "page": this.pagNum,
      "item_count": 12,
      "sub_category_id": this.subCatId,
      "search_category": this.tagList.join(" , ")
    };
    this.api.postData("searchCardsBySubCategoryIdForAdmin", data, { headers: { 'Authorization': 'Bearer ' + this.token } })
      .then(resoponse => {
        if (resoponse.code == 201) {
          this.util.showError('Please enter tag name', 2000)
        } else {
          for (let i = 0; i < resoponse.data.result.length; i++) {
            this.responseData.push(resoponse.data.result[i])
          }
          // this.responseData = resoponse.data.result;
          this.responseDataFull = resoponse.data;
        }
        // console.log(this.responseData)
      })
      .catch(error => {
        console.log(error)
      })
    // this.pagNum = (this.pagNum + 1);
  }
  addImgId(id) {
    const i = this.listOfId.indexOf(id);
    if (i == -1) {
      this.listOfId.push(id)
    } else {
      this.listOfId.splice(i, 1)
    }
    // console.log(this.listOfId)
  }

  updateTag() {
    const data = {
      "img_ids": this.listOfId.join(','),
      "search_category": this.dataFromPage.join(','),
      "sub_category_id": this.subCatId,
    }
    this.api.postData("updateTemplateSearchingTagsByAdmin", data, { headers: { 'Authorization': 'Bearer ' + this.token } })
      .then(response => {
        // console.log(response);
        if (response.code == 200) {
          this.dialogRef.close();
          // alert("tags Upadated")
          this.util.showSuccess(response.message, 3000);
        }
        if (response.code == 201) {
          this.util.showError("Please select images", 2000)
        }
      })
      .catch(e => {
        console.log(e)
      })
  }

  loadNext() {
    if (this.responseData.length > 2) {
      if (this.responseDataFull.is_next_page == true) {
        this.pagNum += 1;
        this.searchByTag();
      }
    }
  }
}


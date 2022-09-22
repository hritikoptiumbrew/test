import { Component, OnInit } from '@angular/core';
import { FormControl } from '@angular/forms';
import { NbDialogRef, NbDialogService } from '@nebular/theme';
import { ERROR } from 'app/app.constants';
import { ViewimageComponent } from 'app/components/viewimage/viewimage.component';
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
  cat_id:any;

  filterDataFromPage = [];

  startDate: string;
  endDate: string;

  subCatId: string;
  is_featured:any;

  imgData: any[] = [];

  listOfId: any = [];

  searchByTagData: any;

  pagNum: number = 1;

  token: string = localStorage.getItem('at');

  hightlight: boolean = false;

  showInputError: boolean = false;

  checked: boolean;

  pageLoader: boolean = false;
  selectedSearchTags: string[] = [];
  searchINputControl = new FormControl();

  constructor(protected dialogRef: NbDialogRef<any>, public api: DataService, public util: UtilService, private dialog: NbDialogService) {
  }

  ngOnInit(): void {
    this.dataFromPage = this.dataFromPage.join().split(" ").toString().split(',');
    //removing copied tag from arrey
    this.filterDataFromPage = [...new Set(this.dataFromPage)];
    var temp_array = [];
    this.filterDataFromPage.forEach(element => {
      element = element.replace(/[`~!@#$%^&*()_|+\-=?;:'",.<>\{\}\[\]\\\/]/gi, '');
      temp_array.push(element);
    });
    this.filterDataFromPage = temp_array;
    this.selectedSearchTags = this.filterDataFromPage;
  }

  //for closing dialog
  close() {
    this.checked = true;
    this.dialogRef.close({ data: this.checked });
  }

  //function for adding tagname to tagList(array)
  onsubmit() {
    if (this.validateString(this.inputVal) && this.inputVal.trim() !== "") {
      // let newInputValue = this.inputVal.split(',');
      // for (let i = 0; i < newInputValue.length; i++) {
      //   this.tagList.push(newInputValue[i]);
      // }
      // this.inputVal = "";
      this.showInputError = false;
      this.searchByTags();
    }
    else {
      this.showInputError = true;
    }
  }
  remove(fruit) {
    var i = this.selectedSearchTags.indexOf(fruit);
    this.selectedSearchTags.splice(i, 1);
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
    if (this.inputVal.split(',').length > 0) {
      this.util.showLoader();
      this.searchByTag();
    }
    else {
      this.util.showError('Please enter tag name', 2000);
    }
  }
//add tag
add(event) {
  if (typeof event == "object") {
    if (event.target.value.trim() != "") {
      // if (!this.validateString(event.target.value)) {
      //   document.getElementById("tagInputError").innerHTML = "Special characters not allowed, only alphanumeric, ' & , #' is allowed in tag name.";
      //   return;
      // }
      // else {
      var newStr = event.target.value;
      var newArr = newStr.split(",");
      for (let i = 0; i < newArr.length; i++) {
        if ((newArr[i] || '').trim()) {
          this.selectedSearchTags.push(newArr[i].trim().toLowerCase());
        }
      }
      document.getElementById("tagInputError").innerHTML = "";
      this.searchINputControl.setValue("");
      // }
    }
    else {
      document.getElementById("tagInputError").innerHTML = "please enter valid tag";
      this.searchINputControl.setValue("");
    }
  }
  else {
    if (event != "") {
      // if (!this.validateString(event)) {
      //   document.getElementById("tagInputError").innerHTML = "Special characters not allowed, only alphanumeric, '&' is allowed in tag name.";
      //   return;
      // }
      // else {
      document.getElementById("tagInputError").innerHTML = "";
      this.selectedSearchTags.push(event.toLowerCase());
      this.searchINputControl.setValue("");
      // }
    }
  }
  const unique = (value, index, self) => {
    return self.indexOf(value) === index
  }

  const uniqueTags = this.selectedSearchTags.filter(unique)

  this.selectedSearchTags = uniqueTags;
}
//
  //callin search API for list of Img
  searchByTag() {
    if (this.pagNum == 1) {
      this.imgData = [];
    }
    const data = {
      "page": this.pagNum,
      "item_count": 18,
      "category_id":this.cat_id,
      "is_featured":this.is_featured,
      "sub_category_id": this.subCatId,
      // "search_category": this.tagList.join(",")
      "search_category": this.inputVal
    };
    this.api.postData("searchCardsBySubCategoryIdForAdmin", data, { headers: { 'Authorization': 'Bearer ' + this.token } })
      .then(resoponse => {
        if (resoponse.code == 200) {
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
        } else if (resoponse.code == 427) {
          this.util.hideLoader();
          this.pageLoader = false;
          this.util.showError("Sorry, we couldn't find any templates for " + this.tagList.join(","), 6000);
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

  viewImage(imgUrl) {
    // console.log(type);
    this.dialog.open(ViewimageComponent, {
      context: {
        imgSrc: imgUrl,
        typeImg: 'cat',
      }
    })
  }
  selectAll() {
    this.listOfId = [];
      for (let i = 0; i < this.imgData.length; i++) {
          this.listOfId.push(this.imgData[i].json_id);
      }
  }
}


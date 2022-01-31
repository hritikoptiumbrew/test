import { Component, OnInit } from '@angular/core';
import { FormControl } from '@angular/forms';
import { NbDialogRef } from '@nebular/theme';
import { ERROR } from 'app/app.constants';
import { DataService } from 'app/data.service';
import { UtilService } from 'app/util.service';
import { MovetocatalogComponent } from '../movetocatalog/movetocatalog.component';

@Component({
  selector: 'ngx-add-multiple-tags',
  templateUrl: './add-multiple-tags.component.html',
  styleUrls: ['./add-multiple-tags.component.scss']
})
export class AddMultipleTagsComponent implements OnInit {

  templatesIds: any = [];
  allSearchTag: string[] = [];
  searchINputControl = new FormControl();
  selectedSearchTags: string[] = [];

  constructor(
    private dialog: NbDialogRef<MovetocatalogComponent>,
    private dataService: DataService,
    private utils: UtilService
  ) { }

  ngOnInit(): void {
  }

  add(event) {
    if (typeof event == "object") {
      if (event.target.value.trim() != "") {
        // if (!this.validateString(event.target.value)) {
        //   document.getElementById("tagInputError").innerHTML = "Special characters not allowed, only alphanumeric, '& , #' is allowed in tag name.";
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
        this.selectedSearchTags.push(event);
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

  remove(fruit) {
    var i = this.selectedSearchTags.indexOf(fruit);
    this.selectedSearchTags.splice(i, 1);
  }

  addTags() {
    this.utils.showLoader();
    const data = {
      "img_ids": this.templatesIds.join(','),
      "search_category": this.selectedSearchTags.join(','),
      // "sub_category_id": this.subCatId,
    }
    this.dataService.postData(
      "updateTemplateSearchingTagsByAdmin",
      data,
      {
        headers:
          { 'Authorization': 'Bearer ' + localStorage.getItem('at') }
      })
      .then(response => {
        if (response.code == 200) {
          this.dialog.close({ code: 200 });
          this.utils.hideLoader();
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
        console.log(e);
        this.utils.showError(ERROR.SERVER_ERR, 3000);
      })
  }

  closeDialog() {
    this.dialog.close();
  }

}

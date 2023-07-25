import { Component, OnInit } from '@angular/core';
import { AddindustryComponent } from '../addindustry/addindustry.component';
import { NbDialogRef } from '@nebular/theme';
import { DataService } from 'app/data.service';
import { UtilService } from 'app/util.service';
import { Router } from '@angular/router';
import { ERROR } from 'app/app.constants';

@Component({
  selector: 'ngx-addtheme',
  templateUrl: './addtheme.component.html',
  styleUrls: ['./addtheme.component.scss']
})
export class AddthemeComponent implements OnInit {

  constructor(private dialogref: NbDialogRef<AddindustryComponent>, private dataService: DataService, private utils: UtilService, private route: Router) {
    this.utils.dialogref = this.dialogref;
   }

  btnText;
  dialogTitle;
  themeName;
  themeDescription;
  sub_category_id;
  theme:any;

  ngOnInit(): void {
    if (this.theme) {
      this.themeName = this.theme.theme_name;
      this.themeDescription = this.theme.short_description;
      this.sub_category_id = this.theme.sub_category_id;
    }
  }

  closeLoading() {
    this.utils.hideLoader();
  }
  
  closedialog() {
    this.dialogref.close({ res: "" });
  }

  addTheme(){
    if (typeof this.themeName == "undefined" || this.themeName == "" || this.themeName == null || this.themeName.trim() == "") {
      this.utils.showError("Please enter theme name.", 3000);
      return false;
    }
    else if (typeof this.themeDescription == "undefined" || this.themeDescription == "" || this.themeDescription == null || this.themeDescription.trim() == "") {
      this.utils.showError("Please enter theme description.", 3000);
      return false;
    }
    else{
      this.utils.showLoader();
      let request_data = {
        "sub_category_id": this.sub_category_id,
        "theme_name": this.themeName,
        "short_description": this.themeDescription
      }
      this.dataService.postData('addTheme', request_data,
        {
          headers:
            { 'Authorization': 'Bearer ' + localStorage.getItem('at') }
        })
        .then(response => {
          if (response.code == 200) {
            this.dialogref.close({ res: "add" });
            this.utils.hideLoader();
            this.utils.showSuccess(response.message, 3000);
          } else if (response.code == 201) {
            this.utils.hideLoader();
            if(response.message == "PhotoEditorLab is unable to add theme."){
              this.utils.showError("theme name already exists.", 3000);
            }
            else{
              this.utils.showError(response.message, 3000);
            }
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
  }

  updateTheme(){
    if (typeof this.themeName == "undefined" || this.themeName == "" || this.themeName == null || this.themeName.trim() == "") {
      this.utils.showError("Please enter theme name.", 3000);
      return false;
    }
    else if (typeof this.themeDescription == "undefined" || this.themeDescription == "" || this.themeDescription == null || this.themeDescription.trim() == "") {
      this.utils.showError("Please enter theme description.", 3000);
      return false;
    }
    else{
      this.utils.showLoader();
      let request_data = {
        "sub_category_id": this.sub_category_id,
        "theme_id" : this.theme.id,
        "theme_name": this.themeName,
        "short_description": this.themeDescription
      }
      this.dataService.postData('updateTheme', request_data,
        {
          headers:
            { 'Authorization': 'Bearer ' + localStorage.getItem('at') }
        })
        .then(response => {
          if (response.code == 200) {
            this.dialogref.close({ res: "update" });
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
          this.utils.showError(ERROR.SERVER_ERR, 3000);
        })
    }
  }


}

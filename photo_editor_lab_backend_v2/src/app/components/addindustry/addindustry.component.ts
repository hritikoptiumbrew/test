import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { NbDialogRef } from '@nebular/theme';
import { ERROR } from 'app/app.constants';
import { DataService } from 'app/data.service';
import { UtilService } from 'app/util.service';
import { ValidationsService } from 'app/validations.service';

@Component({
  selector: 'ngx-addindustry',
  templateUrl: './addindustry.component.html',
  styleUrls: ['./addindustry.component.scss']
})
export class AddindustryComponent implements OnInit {

  constructor(private dialog: NbDialogRef<AddindustryComponent>,private util: UtilService,private validService: ValidationsService,private dialogref: NbDialogRef<AddindustryComponent>, private dataService: DataService, private utils: UtilService, private route: Router) {
    this.utils.dialogref = this.dialog;
   }
  btnText;
  dialogTitle;
  industryIcon: any;
  industryName: any;
  formData = new FormData();
  fileList: any;
  file: any;
  sub_category_id: any;
  token: any;
  errormsg = ERROR;
  industry:any;
  isErrorMsg = false;

  ngOnInit(): void {
    if (this.industry) {
      this.industryIcon = this.industry.icon_webp;
      this.industryName = this.industry.industry_name;
      this.sub_category_id = this.industry.sub_category_id;
    }
  }

  closeLoading() {
    this.utils.hideLoader();
  }
  
  fileChange(event) {
    if (event.target.files && event.target.files[0]) {
      var reader = new FileReader();
      reader.onload = (event: any) => {
          this.industryIcon = event.target.result;
      }
      reader.readAsDataURL(event.target.files[0]);

    }
    this.fileList = event.target.files;
    if (this.fileList.length > 0) {
      this.formData.delete("icon")  
      this.file = this.fileList[0];
      var filesize = Math.round(this.file.size / 1024);
      if (filesize > 100) {
        document.getElementById("imageError").innerHTML = "Maximum 100Kb file allow to upload";
        this.isErrorMsg = true
      }
      else {
        document.getElementById("imageError").innerHTML = "";
        this.isErrorMsg = false
      }
      this.formData.append('icon', this.file, this.file.name);
    }

  }
  
  imageLoad(event) {
    if (event.target.previousElementSibling != null) {
      event.target.previousElementSibling.remove();
    }
  }

  closedialog() {
    this.dialogref.close({ res: "" });
  }

  addIndustry(){
    if (typeof this.file == "undefined" || this.file == "" || this.file == null) {
      this.utils.showError("Please select industry logo.", 3000);
      return false;
    }
    else if (typeof this.industryName == "undefined" || this.industryName == "" || this.industryName == null || this.industryName.trim() == "") {
      this.utils.showError("Please enter industry name.", 3000);
      return false;
    }
    else{
      if(this.isErrorMsg == false){
        this.formData.delete("request_data")
        this.formData.delete("icon")  
        this.utils.showLoader();
        let request_data = {
          "sub_category_id": this.sub_category_id,
          "industry_name": this.industryName
        }
        this.formData.append('icon', this.file, this.file.name);
        this.formData.append('request_data', JSON.stringify(request_data));
    
        this.dataService.postData('addIndustry', this.formData,
          {
            headers:
              { 'Authorization': 'Bearer ' + localStorage.getItem('at') }
          })
          .then(response => {
            if (response.code == 200) {
              this.dialog.close({ res: "add" });
              this.utils.hideLoader();
              this.utils.showSuccess(response.message, 3000);
            } else if (response.code == 201) {
              this.utils.hideLoader();
              if(response.message == "PhotoEditorLab is unable to add industry."){
                this.utils.showError("Industry name already exists.", 3000);
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
  }

  updateIndustry(){
    if (typeof this.industryName == "undefined" || this.industryName == "" || this.industryName == null || this.industryName.trim() == "") {
      this.utils.showError("Please enter industry name.", 3000);
      return false;
    }
    else{
      if(this.isErrorMsg == false){
        this.utils.showLoader();
      this.formData.delete("request_data")
      let request_data = {
        "sub_category_id": this.sub_category_id,
        "industry_id" : this.industry.id,
        "industry_name": this.industryName
      }
      this.formData.append('request_data', JSON.stringify(request_data));
  
      this.dataService.postData('updateIndustry', this.formData,
        {
          headers:
            { 'Authorization': 'Bearer ' + localStorage.getItem('at') }
        })
        .then(response => {
          if (response.code == 200) {
            this.dialog.close({ res: "update" });
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
    }
  }
}
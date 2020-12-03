/*
 * Optimumbrew Technology

 * Project       : Photo Editor Lab
 * File          : addserverurl.component.ts
 * File Created  : Saturday, 31st October 2020 11:49:27 am
 * Author        : Optimumbrew
 * Auther Email  : info@optimumbrew.com
 * Last Modified : Saturday, 31st October 2020 11:49:59 am
 * -----
 * Copyright 2018 - 2020 Optimumbrew Technology
 */


import { Component, OnInit } from '@angular/core';
import { NbDialogRef } from '@nebular/theme';
import { DataService } from 'app/data.service';
import { UtilService } from 'app/util.service';
import { ValidationsService } from 'app/validations.service';
import { ERROR, ENV_CONFIG } from '../../app.constants';
@Component({
  selector: 'ngx-addserverurl',
  templateUrl: './addserverurl.component.html',
  styleUrls: ['./addserverurl.component.scss']
})
export class AddserverurlComponent implements OnInit {

  serverData: any;
  urlName: any;
  errormsg = ERROR;
  token: any;
  constructor(private validService: ValidationsService, private utils: UtilService, private dataService: DataService, private dialogRef: NbDialogRef<AddserverurlComponent>) {
    this.token = localStorage.getItem('at');
    this.utils.dialogref = this.dialogRef;
  }

  ngOnInit(): void {
    if(this.serverData)
    {
      this.urlName = this.serverData.server_url;
    }
  }

  closedialog() {
    this.dialogRef.close({ res: "" });
  }
  checkValidation(id, type, catId, blankMsg, typeMsg,validType) {
    var validObj = {
      "id": id,
      "errorId": catId,
      "type": type,
      "blank_msg": blankMsg,
      "type_msg": typeMsg,
      "button_check": {
        "button_id": "Catedit",
        "successArr": ['editUrlInput']
      }
      
    }
    this.validService.checkValid(validObj);
    if(validType != "blank")
    {
      this.addUrl();
    }
  }
  addUrl() {
    var validObj = [
      {
        "id": 'editUrlInput',
        "errorId": 'inputUrlError',
        "type": '',
        "blank_msg": ERROR.SERVER_URL_EMPTY,
        "type_msg": '',
      }
    ]
    var addStatus = this.validService.checkAllValid(validObj);
    if (addStatus){
      var apiUrl;
      var requestData;
      if(this.serverData)
      {
        requestData = {
          "server_url_id": this.serverData.server_url_id,
          "server_url": this.urlName
        }
        apiUrl = "updateServerUrl";
      }
      else
      {
        requestData = {
          "server_url": this.urlName
        }
        apiUrl = "addServerUrl";
      }
      this.dataService.postData(apiUrl,requestData, {
      headers: {
        'Authorization': 'Bearer ' + this.token
      }
    }).then((results: any) => {
      if (results.code == 200) {
        this.utils.hideLoader();
        this.dialogRef.close({ res: 'add' });
        this.utils.showSuccess(results.message, 4000);
      }
      else if (results.code == 201) {
        document.getElementById("inputUrlError").innerHTML = results.message;
        // this.utils.showError(results.message, 4000);
        this.utils.hideLoader();
      }
      else if (results.status || results.status == 0) {
        this.utils.showError(ERROR.SERVER_ERR, 4000);
        this.utils.hideLoader();
      }
      else {
        this.utils.showError(results.message, 4000);
        this.utils.hideLoader();
      }
    }, (error: any) => {
      console.log(error);
      this.utils.hideLoader();
      this.utils.showError(ERROR.SERVER_ERR, 4000);
    }).catch((error: any) => {
      console.log(error);
      this.utils.hideLoader();
      this.utils.showError(ERROR.SERVER_ERR, 4000);
    });
    }
  }

}

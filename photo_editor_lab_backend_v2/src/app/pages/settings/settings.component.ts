/*
 * Optimumbrew Technology

 * Project       : Photo Editor Lab
 * File          : settings.component.ts
 * File Created  : Monday, 26th October 2020 02:26:52 pm
 * Author        : Optimumbrew
 * Auther Email  : info@optimumbrew.com
 * Last Modified : Thursday, 29th October 2020 11:08:46 am
 * -----
 * Copyright 2018 - 2020 Optimumbrew Technology
 */



import { Component, OnInit } from '@angular/core';

import { DomSanitizer } from '@angular/platform-browser';
import { Router } from '@angular/router';
import { NbDialogService } from '@nebular/theme';
import { AddserverurlComponent } from 'app/components/addserverurl/addserverurl.component';
import { AddvalidationsComponent } from 'app/components/addvalidations/addvalidations.component';
import { EnterotpComponent } from 'app/components/enterotp/enterotp.component';
import { ViewimageComponent } from 'app/components/viewimage/viewimage.component';
import { DataService } from 'app/data.service';
import { UtilService } from 'app/util.service';
import { ValidationsService } from 'app/validations.service';
import { LocalDataSource } from 'ng2-smart-table';
import { ERROR, ENV_CONFIG } from '../../app.constants';
@Component({
  selector: 'ngx-settings',
  templateUrl: './settings.component.html',
  styleUrls: ['./settings.component.scss']
})
export class SettingsComponent implements OnInit {
  validDataSource: LocalDataSource
  serverDataSource: LocalDataSource
  curPassType: boolean;
  newPassType: boolean;
  cnewPassType: boolean;
  j: any = 1;
  cnt: any = 1;
  curPass: any;
  newPass: any;
  cnewPass: any;
  errormsg: any = ERROR;
  token: any;
  showTabs: any;
  lgRep: any = {};
  enableChecked = "no";
  statisticsList: any;
  newServerUrl: any;
  validationsList: any;
  categoryList: any;
  validPageSize: any = [15, 30, 45, 60, 75, 90, 100];
  serverPageSize: any = [15, 30, 45, 60, 75, 90, 100];
  validSelectedPageSize = '15';
  serverSelectedPageSize = '15';
  settings = {
    mode: 'external',
    edit: {
      editButtonContent: '<i class="nb-edit" title="Edit"></i>',
      saveButtonContent: '<i class="nb-checkmark"></i>',
      cancelButtonContent: '<i class="nb-close"></i>',
      confirmSave: true,
    },
    delete: {
      deleteButtonContent: '<i class="nb-trash" title="Delete"></i>',
      confirmDelete: true,
    },
    columns: {
      id: {
        title: '#',
        type: 'number',
        width: '75px',
        editable: false,
        filter: false,
        hideHeader: true,
        sort: false,
        valuePrepareFunction: (row) => {
          return this.j++;
        }
      },
      server_url: {
        title: 'Server URL',
        type: 'html',
        filter: false,
        hideHeader: true
      },
      api_url: {
        title: 'API URL',
        type: 'html',
        filter: false,
        hideHeader: true,
        editable: false,
      }
    },
    actions: {
      add: false,
      position: 'right',
      delete: true,
      edit: true,
      pager: true,
      filter: false
    },
    pager: {
      perPage: parseInt(this.serverSelectedPageSize)
    },
    hideSubHeader: true
  };
  setserverPageSize(value) {
    this.j = 1;
    this.serverSelectedPageSize = value;
    this.settings.pager.perPage = parseInt(this.serverSelectedPageSize);
    this.serverDataSource.setPaging(this.serverDataSource.getPaging().page, parseInt(this.serverSelectedPageSize), true);
  }
  getRows(event) {
    this.cnt = 1;
  }
  getserverRows(event) {
    this.j = 1;
  }
  constructor(private _sanitizer: DomSanitizer, private dialog: NbDialogService, private validService: ValidationsService, private dataService: DataService, private utils: UtilService, private route: Router) {
    this.token = localStorage.getItem("at");

  }

  ngOnInit(): void {
    // console.log(document.querySelectorAll(".validation-config-card ng2-smart-table"));
  }
  ngAfterViewInit(): void {
    setTimeout(() => {
      this.lgRep = JSON.parse(localStorage.getItem('admin_detail'));
      if (!this.lgRep.google2fa_enable || this.lgRep.google2fa_enable == 0 || this.lgRep.google2fa_enable == false) {
        this.showTabs = true;
        this.getServerStatistics();
        this.getValidationList();
      }
      else {
        this.showTabs = false;
        this.enableChecked = "yes";
        this.openOTPDialog(this.lgRep, 'change-pwd');
      }
    });
    // console.log(document.querySelectorAll(":host /deep/.validation-config-card ng2-smart-table"));
    
  }
  passwordToggle(type) {
    if (type == 1) {
      this.curPassType = !this.curPassType;
    }
    else if (type == 2) {
      this.newPassType = !this.newPassType;
    }
    else {
      this.cnewPassType = !this.cnewPassType;
    }
  }
  twoFaOPeration(inputVal) {
    if (inputVal == "yes") {
      // this.utils.showLoader();
      this.dataService.postData('enable2faByAdmin',
        {}, {
        headers: {
          'Authorization': 'Bearer ' + this.token
        }
      }).then((results: any) => {

        if (results.code == 200) {
          this.utils.hideLoader();
          if (results.data && results.data.google2fa_url) {
            // this.open(false,results.data.google2fa_url);
            this.lgRep.google2fa_enable = 1;
            this.lgRep.google2fa_secret = results.data.google2fa_secret;
            this.lgRep.shouldNavigate = true;
            let otpdata = { admin_detail: this.lgRep, occuredFrom: 'enable2fa' };
            this.utils.enable2FaStatus = "enable";
            this.utils.otpdata = otpdata;
            this.utils.qrcodeUrl = results.data.google2fa_url;
            this.route.navigate(['/']);
            localStorage.removeItem("at");
            localStorage.removeItem("admin_detail");
            this.utils.showSuccess(results.message, 4000);
          }
        }
        else if (results.code == 201) {
          this.utils.showError(results.message, 4000);
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
    else {
      this.openOTPDialog(this.lgRep, 'disable2fa');
    }
  }
  
  openOTPDialog(admin_detail, occuredFrom) {
    admin_detail.shouldNavigate = false;
    var passData = { admin_detail: admin_detail, occuredFrom: occuredFrom }
    this.dialog.open(EnterotpComponent, {
      closeOnBackdropClick: false,closeOnEsc: false, context: {
        adminData: passData
      }
    }).onClose.subscribe((result) => {
      if (result.res == "add") {
        this.showTabs = true;
        this.getServerStatistics();
      }
      else {
        this.enableChecked = "yes";
      }
    });;
  }
  checkValidation(id, type, catId, blankMsg, typeMsg,validType) {
    document.getElementById("changePassError").innerHTML = "";
    var validObj = {
      "id": id,
      "errorId": catId,
      "type": type,
      "blank_msg": blankMsg,
      "type_msg": typeMsg,
      "button_check": {
        "button_id": "btnChaPass",
        "successArr": ['curInput', 'newInput', 'cnewInput']
      }
    }
    this.validService.checkValid(validObj);
    if(validType != "blank")
    {
      this.changePassword();
    }
  }
  changePassword() {
    var validObj = [
          {
            "id": 'curInput',
            "errorId": 'curPassError',
            "type": '',
            "blank_msg": ERROR.CURR_PASSWORD,
            "type_msg": '',
          },
          {
            "id": 'newInput',
            "errorId": 'newPassError',
            "type": 'password',
            "blank_msg": ERROR.NEW_PASSWORD,
            "type_msg": ERROR.INVALIDCHANGE_PASS,
          },
          {
            "id": 'cnewInput',
            "errorId": 'cnewPassError',
            "type": '',
            "blank_msg": ERROR.RE_PASSWORD,
            "type_msg": '',
          }
        ]
        var addStatus = this.validService.checkAllValid(validObj);
    
        if (addStatus) {
          if (this.newPass != this.cnewPass) {
            document.getElementById("changePassError").innerHTML = ERROR.MISSMATCH_PASSWORD;
          }
          else {
            this.utils.showLoader();
            this.dataService.postData("changePassword", {
              "current_password": this.curPass,
              "new_password": this.newPass
            }, {
              headers: {
                'Authorization': 'Bearer ' + this.token
              }
            }).then((results: any) => {
      
              if (results.code == 200) {
                if (results.data && results.data.token) {
                  this.token = results.data.token;
                  localStorage.setItem('at', results.data.token);
                }
                this.curPass = "";
                this.newPass = "";
                this.cnewPass = ""
                this.utils.hideLoader();
                this.utils.showSuccess(results.message, 4000);
              }
              else if (results.code == 201) {
                document.getElementById("changePassError").innerHTML = results.message;
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
  getServerStatistics() {
    this.utils.showPageLoader();
    this.dataService.postData('getAllServerUrls',
      {}, {
      headers: {
        'Authorization': 'Bearer ' + this.token
      }
    }).then((results: any) => {
      if (results.code == 200) {
        this.statisticsList = results.data.result;
        this.serverDataSource = new LocalDataSource(this.statisticsList);
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
      console.log(error);
      this.utils.hidePageLoader();
      this.utils.showError(ERROR.SERVER_ERR, 4000);
    }).catch((error: any) => {
      console.log(error);
      this.utils.hidePageLoader();
      this.utils.showError(ERROR.SERVER_ERR, 4000);
    });
  }

  editServerUrl(event) {
    this.openServer(false, event.data);
  }
  addServerUrl(){
    this.openServer(false, '');
  }
  protected openServer(closeOnBackdropClick: boolean, data) {
    this.dialog.open(AddserverurlComponent, {
      closeOnBackdropClick,closeOnEsc: false, context: {
        serverData: data
      }
    }).onClose.subscribe((result) => {
      if (result.res == "add") {
        this.getServerStatistics();
      }
    });
  }
  deleteServerURl(event) {
    this.utils.getConfirm().then((result) => {
      this.utils.showLoader();
      this.dataService.postData('deleteServerUrl',
        {
          "server_url_id": event.data.server_url_id
        }, {
        headers: {
          'Authorization': 'Bearer ' + this.token
        }
      }).then((results: any) => {
        if (results.code == 200) {
          this.j = 1;
          this.utils.hideLoader();
          this.utils.showSuccess(results.message, 4000);
          this.getServerStatistics();
        }
        else if (results.code == 201) {
          this.utils.showError(results.message, 4000);
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
    });
  }
  getValidationList() {
    this.utils.showPageLoader();
    this.dataService.postData('getAllValidationsForAdmin',
      {}, {
      headers: {
        'Authorization': 'Bearer ' + this.token
      }
    }).then((results: any) => {
      if (results.code == 200) {
        this.validationsList = results.data.result;
        this.categoryList = results.data.category_list;
        this.categoryList.unshift({
          category_id: 0,
          name: "Default"
        });
        this.categoryList.unshift({
          category_id: "none",
          name: "Select Category"
        })
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
      console.log(error);
      this.utils.hidePageLoader();
      this.utils.showError(ERROR.SERVER_ERR, 4000);
    }).catch((error: any) => {
      console.log(error);
      this.utils.hidePageLoader();
      this.utils.showError(ERROR.SERVER_ERR, 4000);
    });
  }
  addValidations() {
    this.open(false, '', this.categoryList);
   
  }
  editValidation(validData) {
    this.open(false, validData, this.categoryList);
  }
  protected open(closeOnBackdropClick: boolean, validationdata, categorydata) {
    this.dialog.open(AddvalidationsComponent, {
      closeOnBackdropClick,closeOnEsc: false, context: {
        validationsData: validationdata,
        categoryData: categorydata
      }
    }).onClose.subscribe((result) => {
      if (result.res == "add") {
        this.cnt = 1;
        this.getValidationList();
      }
    });
  }

  deleteValidation(validData) {
    this.utils.getConfirm().then((result) => {
      this.utils.showLoader();
      this.dataService.postData('deleteValidation',
        {
          "setting_id": validData.setting_id
        }, {
        headers: {
          'Authorization': 'Bearer ' + this.token
        }
      }).then((results: any) => {
        if (results.code == 200) {
          this.cnt = 1;
          this.utils.hideLoader();
          this.utils.showSuccess(results.message, 4000);
          this.getValidationList();
        }
        else if (results.code == 201) {
          this.utils.showError(results.message, 4000);
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
    });
  }
}

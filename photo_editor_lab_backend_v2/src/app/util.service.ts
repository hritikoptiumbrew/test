/*
 * Optimumbrew Technology

 * Project       : Photo Editor Lab
 * File          : util.service.ts
 * File Created  : Thursday, 15th October 2020 01:12:03 pm
 * Author        : Optimumbrew
 * Auther Email  : info@optimumbrew.com
 * Last Modified : Thursday, 29th October 2020 10:56:20 am
 * -----
 * Copyright 2018 - 2020 Optimumbrew Technology
 */


import { Injectable } from '@angular/core';
import { NbDialogService, NbDialogConfig, NbWindowService } from '@nebular/theme';
// import { MatDialog } from '@angular/material/dialog';

import Swal from 'sweetalert2';
import { LoadingComponent } from './components/loading/loading.component';


@Injectable({
  providedIn: 'root'
})
export class UtilService {


  loading: any;
  imgLoading: any;
  delstatus: any;
  dialogStatus: any;
  enable2FaStatus: any;
  otpdata: any;
  qrcodeUrl: any;
  errorImg:any = "./assets/default-image-150x150.jpg";
  dialogref:any;
  constructor(private dialog: NbDialogService, private windowService: NbWindowService) { 

  }
  hidePageDialog(){
    if(this.dialogref)
    {
      this.dialogref.close({ res: "" });
    }
  }
  showPageLoader(){
      document.getElementById("pageLoadingNew").setAttribute("loading-visible","true");
  }
  hidePageLoader(){
      document.getElementById("pageLoadingNew").setAttribute("loading-visible","false");
  }
  showLoader() {
    if (this.loading) {
      this.loading.close();
    }
    this.open(false);
  }
  protected open(closeOnBackdropClick: boolean) {
    this.loading = this.dialog.open(LoadingComponent, { closeOnBackdropClick,closeOnEsc: false });
  }
  hideLoader() {
    if (this.loading) {
      this.loading.close();
    }
  }
  showImgLoader(imgUrl) {
    if (this.imgLoading) {
      this.imgLoading.close();
    }
  }
  hideImgLoader() {
    if (this.imgLoading) {
      this.imgLoading.close();
    }
  }

  showError(msg, time) {
    Swal.fire({
      icon: 'error',
      toast: true,
      position: 'top-right',
      title: msg,
      showConfirmButton: false,
      timer: time
    });
  }
  showSuccess(msg, time) {
    Swal.fire({
      icon: 'success',
      toast: true,
      position: 'top-right',
      title: msg,
      showConfirmButton: false,
      timer: time
    });
  }
  getConfirm() {
    let promise = new Promise((resolve, reject) => {
      Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!',
        allowOutsideClick: false,
        allowEscapeKey: false
      }).then((result) => {
        if (result.isConfirmed) {
          resolve(result);
        }
      });
    });
    return promise;
  }
  getdelstatus() {
    return this.delstatus
  }
}

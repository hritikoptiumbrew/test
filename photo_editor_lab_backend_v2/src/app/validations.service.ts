/*
 * Optimumbrew Technology

 * Project       : Photo Editor Lab
 * File          : validations.service.ts
 * File Created  : Thursday, 15th October 2020 01:12:03 pm
 * Author        : Optimumbrew
 * Auther Email  : info@optimumbrew.com
 * Last Modified : Thursday, 29th October 2020 10:56:11 am
 * -----
 * Copyright 2018 - 2020 Optimumbrew Technology
 */


import { Injectable } from '@angular/core';
import * as $ from 'jquery';
@Injectable({
  providedIn: 'root'
})
export class ValidationsService {

  constructor() { }

  checkValid(validObj) {
    var elem = document.getElementById(validObj.id);
    var errorelem = document.getElementById(validObj.errorId);
    var inputelem = document.getElementById(validObj.id) as HTMLInputElement
    var elemVal = inputelem.value;

    if (validObj.type == "radio" || validObj.type == "image") {
      errorelem.innerHTML = "";
      elem.setAttribute("valid-status", "true");
    }
    else {
      if (elemVal.trim().length == 0) {
        errorelem.innerHTML = validObj.blank_msg;
        elem.setAttribute("valid-status", "false");
      }
      else if (validObj.type == "alpha") {
        var pattern = /^[ A-Za-z0-9_#-&]+$/;
        if (!pattern.test(elemVal)) {

          errorelem.innerHTML = validObj.type_msg;
          elem.setAttribute("valid-status", "false");
        }
        else {
          errorelem.innerHTML = "";

          elem.setAttribute("valid-status", "true");
        }
      }
      else if (validObj.type == "email") {
        var pattern = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
        if (!elemVal.match(pattern)) {
          errorelem.innerHTML = validObj.type_msg;
          elem.setAttribute("valid-status", "false");
        }
        else {
          errorelem.innerHTML = "";
          elem.setAttribute("valid-status", "true");
        }
      }
      else if (validObj.type == "password") {
        console.log("in");
        if (elemVal.length < 6) {
          console.log("6");
          errorelem.innerHTML = validObj.type_msg;
          elem.setAttribute("valid-status", "false");
        }
        else {
          console.log("else");
          errorelem.innerHTML = "";
          elem.setAttribute("valid-status", "true");
        }
      }
      else if (validObj.type == "alphaNum") {
        var pattern = /^[a-zA-Z0-9 ]+$/;
        if (!pattern.test(elemVal)) {

          errorelem.innerHTML = validObj.type_msg;
          elem.setAttribute("valid-status", "false");
        }
        else {
          errorelem.innerHTML = "";

          elem.setAttribute("valid-status", "true");
        }
      }
      else {
        if(elemVal.length > 255){
          errorelem.innerHTML = "Category name is too long (Maximum 255 character allow)";
          elem.setAttribute("valid-status", "false");
        }
        else
        {
          errorelem.innerHTML = "";
          elem.setAttribute("valid-status", "true");
        }
      }
    }
    if (validObj.button_check) {
      // this.checkValidSuccess(validObj.button_check.button_id, validObj.button_check.successArr);
    }
  }
  checkAllValid(validobj) {
    var successArr = [];
    validobj.forEach(element => {
      this.checkValid(element);
      successArr.push(element.id);
    });
    var successStatus = this.checkValidSuccess(successArr);
    return successStatus;
  }
  checkValidSuccess(successArr) {
    var validstatus = [];
    successArr.forEach(element => {
      var elem = document.getElementById(element).getAttribute("valid-status");
      validstatus.push(elem);
    });
    var returnstatus = validstatus.every((value) => {
      return value == "true";
    });
    return returnstatus;
    // if (returnstatus) {
    //   // document.getElementById(id).removeAttribute("disabled");
    // }
    // else {
    //   // document.getElementById(id).setAttribute("disabled", "");
    // }
  }

}

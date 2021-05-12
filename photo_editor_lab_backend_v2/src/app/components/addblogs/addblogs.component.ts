/*
 * Optimumbrew Technology

 * Project       : Photo Editor Lab
 * File          : addblogs.component.ts
 * File Created  : Monday, 19th October 2020 02:03:07 pm
 * Author        : Optimumbrew
 * Auther Email  : info@optimumbrew.com
 * Last Modified : Thursday, 29th October 2020 11:15:34 am
 * -----
 * Copyright 2018 - 2020 Optimumbrew Technology
 */


import { Component, OnInit } from '@angular/core';
import { NbDialogRef, NbDialogService } from '@nebular/theme';
import { DataService } from 'app/data.service';
import { UtilService } from 'app/util.service';
declare var $;

import { ERROR, ENV_CONFIG } from '../../app.constants';

@Component({
  selector: 'ngx-addblogs',
  templateUrl: './addblogs.component.html',
  styleUrls: ['./addblogs.component.scss']
})
export class AddblogsComponent implements OnInit {
  upBlogData: any;
  blogImg: any;
  content: any;
  formData = new FormData();
  fileList: any;
  titleCol: any;
  titleSize: any;
  subTitleCol: any;
  subTitleSize: any;
  blogTitle: any;
  blogSubTitle: any;
  file: any;
  catalogId: any;
  selectDevice: any = "1";
  openTmpltBtn: any;
  srcTmpltBtn: any;
  globalStyle: any = '<style>body{padding: 5px; text-indent: 0px !important; font-size: 16px !important; font-family: "verdana";} img{vertical-align: middle;} span{text-indent: 0px !important} p{margin: 0 !important; text-indent: 0px !important;} table { border: none;margin: 0 !important;width: 100%;} .sttc-button {font-family: -apple-system, system-ui, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; font-size: 1rem; font-weight: 500; border: none; border-radius: 4px; box-shadow: none; color: #ffffff; cursor: pointer; display: inline-block; margin: 0px; padding: 8px 18px; text-decoration: none; background-color: #ffcd00; overflow-wrap: break-word; user-select:none !important;}.sttc-button:hover, .sttc-button:focus{ background-color: #d9ae00;}</style>';
  token: any;
  constructor(private dialogRef: NbDialogRef<AddblogsComponent>, private utils: UtilService, private dataService: DataService) {
    this.token = localStorage.getItem("at");
    this.utils.dialogref = this.dialogRef;
  }

  ngOnInit(): void {
    
    let that = this;
    $(document).ready(function () {
      var testData = $('#summernote').summernote({
        placeholder: 'Your HTML content here...',
        height: 300,
        disableResizeEditor: true,
        blockquoteBreakingLevel: 2,
        disableDragAndDrop: true,
        // dialogsFade: true,
        prettifyHtml: true,
        codemirror: {
          theme: 'monokai',
          htmlMode: true,
          lineNumbers: true,
          mode: 'text/html'
        },
        fontNames: [
          'Arial', 'Arial Black', 'Comic Sans MS', 'Courier New',
          'Helvetica Neue', 'Helvetica', 'Impact', 'Lucida Grande',
          'Tahoma', 'Times New Roman', 'Verdana', 'Azo Sans'
        ],
        //'font-family': 'Azo Sans',
        fontSizes: ['8', '9', '10', '11', '12', '14', '15', '16', '17', '18', '24', '36', '48', '64', '82', '150', "152", "154", "156", "158", "160", "162", "164", "166", "168", "170", "172", "174", "176", "178", "180", "182", "184", "186", "188", "190", "192", "194", "196", "198", "200"],
        fontNamesIgnoreCheck: ['Azo Sans'],
        toolbar: [
          ['fontsize', ['fontsize']],
          ['color', ['color']],
          ['font', ['bold', 'italic', 'underline', 'clear', 'strikethrough', 'superscript', 'subscript']],
          ['fontname', ['fontname']],
          ['para', ['ul', 'ol', 'paragraph']],
          ['height', ['height']],
          ['table', ['table']],
          ['insert', ['link', 'picture', 'hr']],
          ['view', ['fullscreen', 'codeview']],
          ['help', ['help']],
          ['mybutton', ['ClearFormat', 'refreshStyle']]
        ],
        popover: {
          image: [
            ['image', ['resizeFull', 'resize85', 'resize75', 'resize60', 'resizeHalf', 'resize40', 'resizeQuarter', 'resizeNone']],
            ['float', ['floatLeft', 'floatRight', 'floatNone']],
            ['remove', ['removeMedia']]
          ],
        },
        buttons: {
          ClearFormat: function (context) {
            var ui = $.summernote.ui;
            var button = ui.button({
              contents: '<i class="fa fa-retweet"/>  Clear Formating',
              click: function () {
                $("#summernote").summernote("removeFormat");
              }
            })
            return button.render();
          },
          refreshStyle: function (context) {
            var ui = $.summernote.ui;
            var button = ui.button({
              contents: '<i class="fa fa-sync-alt"/>  Refresh Style',
              click: function () {
                that.updateStyle();
              }
            })
            return button.render();
          }
        }
      });
      if (that.upBlogData) {
        $('#summernote').summernote('code', JSON.parse(that.upBlogData.blog_json).blog_data);
      } else {
        if (that.selectDevice == '1') {
          $('#summernote').summernote('code', that.globalStyle + '<script>function OpenTemplate(templateID) { if(templateID){ Android.moveToNextScreen(templateID); } } function searchTemplate(searchTag) { Android.goToSearchScreen(searchTag);} </script>');
        }
        else if (that.selectDevice == '2') {
          $('#summernote').summernote('code', that.globalStyle + '<script> function OpenTemplate(templateID) { if(templateID){ window.location = "videoadking://?templateID="+ templateID } } function searchTemplate(searchTag) { window.location = "videoadking://?searchTag="+ searchTag} </script>');
        }
        else {
          // $('#summernote').summernote('code', that.globalStyle + '<script>var userAgent = navigator.userAgent || navigator.vendor; function OpenTemplate(templateID) { if (templateID) {if (/android/i.test(userAgent)) {Android.moveToNextScreen(templateID);}if (/iPad|iPhone|iPod/.test(userAgent) && !window.MSStream) {window.location = "videoadking://?templateID=" + templateID;}}}if (/android/i.test(userAgent)) {function searchTemplate(searchTag) { Android.goToSearchScreen(searchTag); }}if (/iPad|iPhone|iPod/.test(userAgent) && !window.MSStream) { function searchTemplate(searchTag) { window.location = "videoadking://?searchTag=" + searchTag }}</script>');
          $('#summernote').summernote('code', that.globalStyle + '<script> function OpenTemplate(templateID) { } function searchTemplate(searchTag) { } </script>');
        }
      }
    });
    this.openTmpltBtn = '<br><button class="sttc-button" onclick="OpenTemplate(' + "10" + ')">Open Template</button><br><br>';
    this.srcTmpltBtn = '<br><button class="sttc-button" onclick="searchTemplate(' + "'illustration'" + ')">Search Template</button><br><br>';

    if (this.upBlogData) {
      var titleData = JSON.parse(this.upBlogData.title);
      var subTitleData = JSON.parse(this.upBlogData.subtitle);
      this.blogImg = this.upBlogData.compressed_img;
      this.selectDevice = this.upBlogData.platform.toString();
      this.blogTitle = titleData.text_value;
      this.titleCol = titleData.text_color;
      this.titleSize = titleData.text_size;
      this.blogSubTitle = subTitleData.text_value;
      this.subTitleCol = subTitleData.text_color;
      this.subTitleSize = subTitleData.text_size;
    }
    else {
      this.titleCol = this.subTitleCol = "#000000";
      this.subTitleSize = 36;
      this.titleSize = 16;
    }
  }

  updateStyle() {
    var fullCode = $('#summernote').summernote('code');
    var content = fullCode.replace(/<style[^>]*>.*?<\/style>/gi, '');
    $('#summernote').summernote('code', content + this.globalStyle);
  }
  fileChange(event) {
    if (event.target.files && event.target.files[0]) {
      var reader = new FileReader();
      reader.onload = (event: any) => {
        this.blogImg = event.target.result;
        document.getElementById("blogimagError").innerHTML = "";
      }
      reader.readAsDataURL(event.target.files[0]);

    }
    this.fileList = event.target.files;
    if (this.fileList.length > 0) {
      this.file = this.fileList[0];
      this.formData.append('file', this.file, this.file.name);
    }
  }
  changePlatform(radioVal) {
    var fullCode = $('#summernote').summernote('code');
    var content = fullCode.replace(/<script[^>]*>.*?<\/script>/gi, '');
    if (radioVal == '1') {
      $('#summernote').summernote('code', content + '<script>function OpenTemplate(templateID) { if(templateID){ Android.moveToNextScreen(templateID); } } function searchTemplate(searchTag) { Android.goToSearchScreen(searchTag);} </script>');
    
    }
    else if (radioVal == '2') {
      $('#summernote').summernote('code', content + '<script> function OpenTemplate(templateID) { if(templateID) { window.location = "videoadking://?templateID="+ templateID } } function searchTemplate(searchTag) { window.location = "videoadking://?searchTag="+ searchTag} </script>');
     
    }
    else {
      $('#summernote').summernote('code', content + '<script> function OpenTemplate(templateID) { } function searchTemplate(searchTag) { } </script>');
      // $('#summernote').summernote('code', content + '<script>var userAgent = navigator.userAgent || navigator.vendor; function OpenTemplate(templateID) { if (templateID) {if (/android/i.test(userAgent)) {Android.moveToNextScreen(templateID);}if (/iPad|iPhone|iPod/.test(userAgent) && !window.MSStream) {window.location = "videoadking://?templateID=" + templateID;}}}if (/android/i.test(userAgent)) {function searchTemplate(searchTag) { Android.goToSearchScreen(searchTag); }}if (/iPad|iPhone|iPod/.test(userAgent) && !window.MSStream) { function searchTemplate(searchTag) { window.location = "videoadking://?searchTag=" + searchTag }}</script>');
    }
  }
  closeDialog() {
    this.dialogRef.close({ res: "" });
  }
  getCode(id) {
    var tmpltData = $(id).summernote('isEmpty') ? '' : $(id).summernote('code');
    return tmpltData;
  }
  blankTitle(){
    document.getElementById("blogTitleError").innerHTML = "";
  }
  trim(str) {
    return str.replace(/^\s+|\s+$/g, "");
  }
  removeTextMsg(id) {
    document.getElementById(id).innerHTML = "";
  }
  blogImgValid(){
    if (this.blogImg == undefined || this.blogImg == "" || this.blogImg == null) {
      document.getElementById("blogimagError").innerHTML = ERROR.IMG_REQ;
    }
    else
    { 
      document.getElementById("blogimagError").innerHTML = "";
      return true;
    }
  }
  blogTitleValid(){
    if (typeof this.blogTitle == 'undefined' || this.blogTitle.trim() == "" || this.blogTitle == null) {
      document.getElementById("blogTitleError").innerHTML = ERROR.TITLE_EMPTY;
    }
    else
    {
      document.getElementById("blogTitleError").innerHTML = "";
      return true;
    }
  }
  blogCodeValid(){
    if (typeof this.getCode('#summernote') == 'undefined' || this.trim(this.getCode('#summernote')) == "" || this.getCode('#summernote') == null) {
     
      document.getElementById("blogContentError").innerHTML = ERROR.CONTENT_EMPTY;
    }
    else
    {
      document.getElementById("blogContentError").innerHTML = "";
      return true;
    }
  }
  uploadBlog() {
    var imageStatus = this.blogImgValid();
    var titleStatus = this.blogTitleValid();
    var codeStatus = this.blogCodeValid();
    if(imageStatus && titleStatus && codeStatus) {
      this.utils.showLoader();
      var requestData;
      var apiURL;
      if (this.upBlogData) {
        requestData = {
          "blog_id": this.upBlogData.blog_id,
          "platform": this.selectDevice,
          "catalog_id": this.catalogId,
          "title": { "text_value": this.blogTitle, "text_color": this.titleCol, "text_size": this.titleSize },
          "subtitle": { "text_value": this.blogSubTitle, "text_color": this.subTitleCol, "text_size": this.subTitleSize },
          "blog_data": "<body><meta name='viewport' content='width=device-width,initial-scale=1,maximum-scale=1'/>" + this.getCode('#summernote') + "</body>",
          "fg_image": this.blogImg
        }
        apiURL = "updateBlogContent";
      }
      else {
        requestData = {
          "platform": this.selectDevice,
          "catalog_id": this.catalogId,
          "title": { "text_value": this.blogTitle, "text_color": this.titleCol, "text_size": this.titleSize },
          "subtitle": { "text_value": this.blogSubTitle, "text_color": this.subTitleCol, "text_size": this.subTitleSize },
          "blog_data": "<body><meta name='viewport' content='width=device-width,initial-scale=1,maximum-scale=1'/>" + this.getCode('#summernote') + "</body>"
        }
        apiURL = "addBlogContent";
      }
      this.formData.append('request_data', JSON.stringify(requestData));
      this.dataService.postData(apiURL, this.formData,
        {
          headers: {
            'Authorization': 'Bearer ' + this.token
          }
        }).then((results: any) => {
          if (results.code == 200) {
            this.utils.hideLoader();
            this.dialogRef.close({ res: "add" });
            this.utils.showSuccess(results.message, 4000);
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
  }
  imageLoad(event){
    if(event.target.previousElementSibling != null)
    {
      event.target.previousElementSibling.remove();
    }
  }
}
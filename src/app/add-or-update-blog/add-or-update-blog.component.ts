import { Component, OnInit, ElementRef, ViewChild, Renderer, Inject } from '@angular/core';
import { MdDialog, MdDialogRef, MdSnackBar, MdSnackBarConfig, MD_DIALOG_DATA } from '@angular/material';
import { DataService } from 'app/data.service';
import { Router } from '@angular/router';
import { LoadingComponent } from 'app/loading/loading.component';
import { ERROR } from 'app/app.constants';

@Component({
  selector: 'app-add-or-update-blog',
  templateUrl: './add-or-update-blog.component.html',
  styleUrls: ['./add-or-update-blog.component.css']
})
export class AddOrUpdateBlogComponent implements OnInit {
  token: any;
  blog_data: any = {};
  blog_details: any = {};
  fileList: any;
  file: any;
  formData = new FormData();
  successMsg: any;
  errorMsg: any;
  loading: any;
  platform: any = 'android';
  catalog_id: any;
  openTmpltBtn: any;
  srcTmpltBtn: any;
  @ViewChild('fileInput') fileInputElement: ElementRef;

  constructor(public dialogRef: MdDialogRef<AddOrUpdateBlogComponent>, @Inject(MD_DIALOG_DATA) public data: any, private dataService: DataService, private router: Router, private renderer: Renderer, public dialog: MdDialog, public snackBar: MdSnackBar) {
    this.token = localStorage.getItem('photoArtsAdminToken');
    if (data) {
      this.openTmpltBtn = '<br><style>body{padding: 10px;} p{margin: 0 !important;} table { border: none;margin: 0 !important;width: 100%;} .sttc-button {font-family: -apple-system, system-ui, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; font-size: 1rem; font-weight: 500; border: none; border-radius: 4px; box-shadow: none; color: #ffffff; cursor: pointer; display: inline-block; margin: 0px; padding: 8px 18px; text-decoration: none; background-color: #ffcd00; overflow-wrap: break-word; user-select:none;}.sttc-button:hover, .sttc-button:focus{ background-color: #d9ae00;}</style><button class="sttc-button" onclick="OpenTemplate(' + "'10'" + ')">Open Template</button><br><br>';
      this.srcTmpltBtn = '<br><style>body{padding: 10px;} p{margin: 0 !important;} table { border: none;margin: 0 !important;width: 100%;} .sttc-button {font-family: -apple-system, system-ui, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; font-size: 1rem; font-weight: 500; border: none; border-radius: 4px; box-shadow: none; color: #ffffff; cursor: pointer; display: inline-block; margin: 0px; padding: 8px 18px; text-decoration: none; background-color: #ffcd00; overflow-wrap: break-word; user-select:none;}.sttc-button:hover, .sttc-button:focus{ background-color: #d9ae00;}</style><button class="sttc-button" onclick="searchTemplate(' + "'illustration'" + ')">Search Template</button><br><br>';
      this.blog_data = {
        "title": { "text_color": "#ffffff" },
        "subtitle": { "text_color": "#ffffff" }
      };
      this.blog_details = this.data.blog_details;
      this.blog_data = JSON.parse(this.blog_details.blog_json);
      this.blog_data.title && this.blog_data.title.text_color ? this.blog_data.title.text_color : this.blog_data.title.text_color = "#000000";
      this.blog_data.subtitle && this.blog_data.subtitle.text_color ? this.blog_data.subtitle.text_color : this.blog_data.subtitle.text_color = "#000000";
      this.blog_data.title && this.blog_data.title.text_size ? this.blog_data.title.text_size : this.blog_data.title.text_size = 24;
      this.blog_data.subtitle && this.blog_data.subtitle.text_size ? this.blog_data.subtitle.text_size : this.blog_data.subtitle.text_size = 18;
      this.blog_data.compressed_img = this.blog_details.compressed_img;
      this.platform = this.blog_details.platform == 1 ? 'android' : this.blog_details.platform == 2 ? 'ios' : 'both';
    } else {
      this.openTmpltBtn = '<br><button class="sttc-button" onclick="OpenTemplate(' + "10" + ')">Open Template</button><br><br>';
      this.srcTmpltBtn = '<br><button class="sttc-button" onclick="searchTemplate(' + "'illustration'" + ')">Search Template</button><br><br>';
      this.blog_data = {
        "title": { "text_color": "#000000", "text_size": 16 },
        "subtitle": { "text_color": "#000000", "text_size": 36 }
      };
    }
  }

  ngOnInit() {
    let that = this;
    $(document).ready(function () {
      var testData = $('#summernote').summernote({
        placeholder: 'Your HTML content...',
        minHeight: null,
        maxHeight: null,
        disableResizeEditor: true,
        blockquoteBreakingLevel: 2,
        dialogsFade: true,
        prettifyHtml: true,
        codemirror: {
          theme: 'monokai',
          htmlMode: true,
          lineNumbers: true,
          mode: 'text/html'
        },
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
          ['help', ['help']]
        ]
      });
      if (that.data) {
        $('#summernote').summernote('code', that.blog_data.blog_data);
      } else {
        if (that.platform == 'android') {
          $('#summernote').summernote('code', '<style>body{padding: 10px;} p{margin: 0 !important;} table { border: none;margin: 0 !important;width: 100%;} .sttc-button {font-family: -apple-system, system-ui, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; font-size: 1rem; font-weight: 500; border: none; border-radius: 4px; box-shadow: none; color: #ffffff; cursor: pointer; display: inline-block; margin: 0px; padding: 8px 18px; text-decoration: none; background-color: #ffcd00; overflow-wrap: break-word; user-select:none !important;}.sttc-button:hover, .sttc-button:focus{ background-color: #d9ae00;}</style><script>function OpenTemplate(templateID) { if(templateID){ Android.moveToNextScreen(templateID); } } function searchTemplate(searchTag) { Android.goToSearchScreen(searchTag);} </script>');
        }
        else if (this.platform == 'ios') {
          $('#summernote').summernote('code', '<style>body{padding: 10px;} p{margin: 0 !important;} table { border: none;margin: 0 !important;width: 100%;} .sttc-button {font-family: -apple-system, system-ui, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; font-size: 1rem; font-weight: 500; border: none; border-radius: 4px; box-shadow: none; color: #ffffff; cursor: pointer; display: inline-block; margin: 0px; padding: 8px 18px; text-decoration: none; background-color: #ffcd00; overflow-wrap: break-word; user-select:none !important;}.sttc-button:hover, .sttc-button:focus{ background-color: #d9ae00;}</style><script> function OpenTemplate(templateID) { if(templateID){ window.location = "videoadking://?templateID="+ templateID } } function searchTemplate(searchTag) { window.location = "videoadking://?searchTag="+ searchTag} </script>');
        }
        else {
          $('#summernote').summernote('code', '<style>body{padding: 10px;} p{margin: 0 !important;} table { border: none;margin: 0 !important;width: 100%;} .sttc-button {font-family: -apple-system, system-ui, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; font-size: 1rem; font-weight: 500; border: none; border-radius: 4px; box-shadow: none; color: #ffffff; cursor: pointer; display: inline-block; margin: 0px; padding: 8px 18px; text-decoration: none; background-color: #ffcd00; overflow-wrap: break-word; user-select:none !important;}.sttc-button:hover, .sttc-button:focus{ background-color: #d9ae00;}</style><script> function OpenTemplate(templateID) { } function searchTemplate(searchTag) { } </script>');
        }
      }
    });
  }

  changePlatform() {
    var fullCode = $('#summernote').summernote('code');
    var content = fullCode.replace(/<script[^>]*>.*?<\/script>/gi, '');
    if (this.platform == 'android') {
      $('#summernote').summernote('code', content + '<script>function OpenTemplate(templateID) { if(templateID){ Android.moveToNextScreen(templateID); } } function searchTemplate(searchTag) { Android.goToSearchScreen(searchTag);} </script>');
    }
    else if (this.platform == 'ios') {
      $('#summernote').summernote('code', content + '<script> function OpenTemplate(templateID) { if(templateID) { window.location = "videoadking://?templateID="+ templateID } } function searchTemplate(searchTag) { window.location = "videoadking://?searchTag="+ searchTag} </script>');
    }
    else {
      $('#summernote').summernote('code', content + '<script> function OpenTemplate(templateID) { } function searchTemplate(searchTag) { } </script>');
    }
  }

  onImageClicked(event) {
    this.renderer.invokeElementMethod(this.fileInputElement.nativeElement, 'click');
  }

  fileChange(event) {
    if (event.target.files && event.target.files[0]) {
      this.formData.delete('file');
      var reader = new FileReader();
      reader.onload = (event: any) => {
        this.blog_data.compressed_img = event.target.result;
      }
      reader.readAsDataURL(event.target.files[0]);
    }
    this.fileList = event.target.files;
    if (this.fileList.length > 0) {
      this.file = this.fileList[0];
      this.formData.append('file', this.file, this.file.name);
    }
  }

  getCode(id) {
    var tmpltData = $(id).summernote('isEmpty') ? '' : $(id).summernote('code');
    return tmpltData;
  }

  addBlog(blog_data) {
    this.formData.delete('request_data');
    this.token = localStorage.getItem('photoArtsAdminToken');
    let blog_data_tmp = JSON.parse(JSON.stringify(blog_data));
    if (typeof this.file == 'undefined' || this.file == "" || this.file == null) {
      this.showError("Image required", false);
      return false;
    }
    else if (typeof blog_data.title.text_value == 'undefined' || this.trim(blog_data.title.text_value) == "" || blog_data.title.text_value == null) {
      this.showError("Please enter title", false);
      return false;
    }
    /* else if (typeof blog_data.subtitle.text_value == 'undefined' || this.trim(blog_data.subtitle.text_value) == "" || blog_data.subtitle.text_value == null) {
      this.showError("Please enter sub-title", false);
      return false;
    } */
    else if (typeof this.getCode('#summernote') == 'undefined' || this.trim(this.getCode('#summernote')) == "" || this.getCode('#summernote') == null) {
      this.showError("Please enter blog content", false);
      return false;
    }
    else {
      this.errorMsg = "";
      this.loading = this.dialog.open(LoadingComponent);
      let request_data = {
        "platform": this.platform == 'android' ? 1 : this.platform == 'ios' ? 2 : 3,
        "catalog_id": this.catalog_id,
        "title": blog_data.title,
        "subtitle": blog_data.subtitle,
        "blog_data": this.getCode('#summernote')
      };
      this.formData.append('request_data', JSON.stringify(request_data));
      this.dataService.postData('addBlogContent', this.formData,
        {
          headers: {
            'Authorization': 'Bearer ' + this.token
          }
        }).subscribe(results => {
          if (results.code == 200) {
            // this.successMsg = results.message;
            this.showSuccess(results.message, false);
            this.loading.close();
            this.dialogRef.close();
          }
          else if (results.code == 400) {
            this.loading.close();
            localStorage.removeItem("photoArtsAdminToken");
            this.router.navigate(['/admin']);
          }
          else if (results.code == 401) {
            this.token = results.data.new_token;
            this.loading.close();
            localStorage.setItem("photoArtsAdminToken", this.token);
            this.addBlog(blog_data);
          }
          else {
            this.loading.close();
            this.formData.delete("request_data");
            // this.errorMsg = results.message;
            this.showError(results.message, false);
          }
        }, (error: any) => {
          this.loading.close();
          this.formData.delete("request_data");
          // this.errorMsg = results.message;
          this.showError(ERROR.SERVER_ERR, false);
        });
    }
  }

  trim(str) {
    return str.replace(/^\s+|\s+$/g, "");
  }

  showError(message, action) {
    let config = new MdSnackBarConfig();
    config.extraClasses = ['snack-error'];
    /* config.horizontalPosition = "right";
    config.verticalPosition = "top"; */
    config.duration = 5000;
    this.snackBar.open(message, action ? 'Okay!' : undefined, config);
  }

  showSuccess(message, action) {
    let config = new MdSnackBarConfig();
    config.extraClasses = ['snack-success'];
    /* config.horizontalPosition = "right";
    config.verticalPosition = "top"; */
    config.duration = 5000;
    this.snackBar.open(message, action ? 'Okay!' : undefined, config);
  }


  updateBlogdata(blog_data) {
    this.formData.delete('request_data');
    this.token = localStorage.getItem('photoArtsAdminToken');
    let blog_data_tmp = JSON.parse(JSON.stringify(blog_data));
    if (typeof blog_data.title.text_value == 'undefined' || this.trim(blog_data.title.text_value) == "" || blog_data.title.text_value == null) {
      this.showError("Please enter title", false);
      return false;
    }
    /* else if (typeof blog_data.subtitle.text_value == 'undefined' || this.trim(blog_data.subtitle.text_value) == "" || blog_data.subtitle.text_value == null) {
      this.showError("Please enter sub-title", false);
      return false;
    } */
    else if (typeof this.getCode('#summernote') == 'undefined' || this.trim(this.getCode('#summernote')) == "" || this.getCode('#summernote') == null) {
      this.showError("Please enter blog content", false);
      return false;
    }
    else {
      this.errorMsg = "";
      this.loading = this.dialog.open(LoadingComponent);
      let request_data = {
        "blog_id": this.blog_details.blog_id,
        "platform": this.platform == 'android' ? 1 : this.platform == 'ios' ? 2 : 3,
        "title": blog_data.title,
        "subtitle": blog_data.subtitle,
        "blog_data": this.getCode('#summernote'),
        "fg_image": this.blog_details.fg_image
      };
      this.formData.append('request_data', JSON.stringify(request_data));
      this.dataService.postData('updateBlogContent', this.formData,
        {
          headers: {
            'Authorization': 'Bearer ' + this.token
          }
        }).subscribe(results => {
          if (results.code == 200) {
            // this.successMsg = results.message;
            this.showSuccess(results.message, false);
            this.loading.close();
            this.dialogRef.close();
          }
          else if (results.code == 400) {
            this.loading.close();
            localStorage.removeItem("photoArtsAdminToken");
            this.router.navigate(['/admin']);
          }
          else if (results.code == 401) {
            this.token = results.data.new_token;
            this.loading.close();
            localStorage.setItem("photoArtsAdminToken", this.token);
            this.uploadBlog(blog_data);
          }
          else {
            this.loading.close();
            this.formData.delete("request_data");
            // this.errorMsg = results.message;
            this.showError(results.message, false);
          }
        }, (error: any) => {
          this.loading.close();
          this.formData.delete("request_data");
          // this.errorMsg = results.message;
          this.showError(ERROR.SERVER_ERR, false);
        });
    }

  }

  uploadBlog(blog_data) {
    if (this.data) {
      this.updateBlogdata(blog_data);
    } else {
      this.addBlog(blog_data);
    }
  }



}

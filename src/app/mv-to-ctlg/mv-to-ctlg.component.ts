import { Component, OnInit, Inject } from '@angular/core';
import { MdDialog, MdDialogRef, MD_DIALOG_DATA, MdSnackBar, MdSnackBarConfig } from '@angular/material';
import { Router, ActivatedRoute } from '@angular/router';
import { DataService } from '../data.service';
import { LoadingComponent } from '../loading/loading.component';


@Component({
  selector: 'app-mv-to-ctlg',
  templateUrl: './mv-to-ctlg.component.html',
  styleUrls: ['./mv-to-ctlg.component.css']
})
export class MvToCtlgComponent implements OnInit {

  token: any;
  sub_category_list: any = [];
  catalog_data: any = {};
  sl_ctlg: any = {};
  sc_dtls: any = {};
  ctlg_dtls: any = {};
  total_record: any;
  successMsg: any;
  errorMsg: any;
  loading: any;

  constructor(public dialogRef: MdDialogRef<MvToCtlgComponent>, @Inject(MD_DIALOG_DATA) public data: any, public route: ActivatedRoute, public snackBar: MdSnackBar, private dataService: DataService, private router: Router, public dialog: MdDialog) {
    this.token = localStorage.getItem("photoArtsAdminToken");
    this.catalog_data = data.catalog_data;
    this.sc_dtls = data.sc_dtls;
    this.ctlg_dtls = data.ctlg_dtls;
    this.gtSbCatLst(this.catalog_data);
  }

  ngOnInit() {
    this.token = localStorage.getItem("photoArtsAdminToken");
  }

  gtSbCatLst(catalog_data) {
    this.loading = this.dialog.open(LoadingComponent);
    this.errorMsg = "";
    this.successMsg = "";
    this.dataService.postData('getAllSubCategoryToMoveTemplate',
      {
        "img_id": catalog_data.img_id,
        "category_id": this.sc_dtls.category_id,
        "is_featured": this.ctlg_dtls.is_featured
      }, {
        headers: {
          'Authorization': 'Bearer ' + this.token
        }
      }).subscribe(results => {
        if (results.code == 200) {
          this.successMsg = "";
          this.sub_category_list = results.data.sub_category_list;
          this.total_record = this.sub_category_list.length;
          this.sub_category_list.forEach((sb_ct_dtl: any) => {
            sb_ct_dtl.has_tplt = false;
            sb_ct_dtl.catalog_list.forEach((ctlg_dtl: any) => {
              if (ctlg_dtl.is_linked == 1 || ctlg_dtl.is_linked == true) {
                this.sl_ctlg = ctlg_dtl;
                sb_ct_dtl.has_tplt = true;
              }
            });
          });
          this.loading.close();
        }
        else if (results.code == 400) {
          this.loading.close();
          localStorage.removeItem("photoArtsAdminToken");
          this.router.navigate(['/admin']);
        }
        else if (results.code == 401) {
          this.errorMsg = "";
          this.successMsg = "";
          this.token = results.data.new_token;
          this.loading.close();
          localStorage.setItem("photoArtsAdminToken", this.token);
          this.gtSbCatLst(catalog_data);
        }
        else {
          this.loading.close();
          this.errorMsg = results.message;
          this.successMsg = "";
        }
      });
  }

  upChkVls(catalog_details, checkbox) {
    this.sl_ctlg = catalog_details;
    $('input[name="' + checkbox.target.name + '"]').not(checkbox.target).prop('checked', false);
    this.sub_category_list.forEach((sb_ct_dtl: any) => {
      sb_ct_dtl.has_tplt = false;
      sb_ct_dtl.catalog_list.forEach((ctlg_dtl: any) => {
        if (this.sl_ctlg.catalog_id == ctlg_dtl.catalog_id) {
          ctlg_dtl.is_linked = checkbox.target.checked == true ? 1 : 0;
          this.sl_ctlg = ctlg_dtl;
          sb_ct_dtl.has_tplt = true;
        }
        else {
          ctlg_dtl.is_linked = 0;
        }
      });
    });
  }

  mvTplt() {
    this.errorMsg = "";
    if (this.sl_ctlg.is_linked == 0 || this.sl_ctlg.is_linked == false) {
      this.errorMsg = "Please select a catalog to move the template.";
      return false;
    }
    else {
      this.loading = this.dialog.open(LoadingComponent);
      this.errorMsg = "";
      this.successMsg = "";
      this.dataService.postData('moveTemplate',
        {
          "catalog_id": this.sl_ctlg.catalog_id,
          "template_list": [this.catalog_data.img_id]
        }, {
          headers: {
            'Authorization': 'Bearer ' + this.token
          }
        }).subscribe(results => {
          if (results.code == 200) {
            this.successMsg = "";
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
            this.errorMsg = "";
            this.successMsg = "";
            this.token = results.data.new_token;
            this.loading.close();
            localStorage.setItem("photoArtsAdminToken", this.token);
            this.mvTplt();
          }
          else {
            this.loading.close();
            this.errorMsg = results.message;
            this.successMsg = "";
          }
        });
    }
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

}

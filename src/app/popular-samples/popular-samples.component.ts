import { Component, OnInit } from '@angular/core';
import { MdDialog } from '@angular/material';
import { Router, ActivatedRoute } from '@angular/router';
import { DataService } from '../data.service';
import { LoadingComponent } from '../loading/loading.component';
import { PopularSamplesAddComponent } from '../popular-samples-add/popular-samples-add.component';
import { PopularSamplesUpdateComponent } from '../popular-samples-update/popular-samples-update.component';
import { DeleteSubcategoryImageByIdComponent } from '../delete-subcategory-image-by-id/delete-subcategory-image-by-id.component';

@Component({
  selector: 'app-popular-samples',
  templateUrl: './popular-samples.component.html'
})
export class PopularSamplesComponent implements OnInit {

  token: any;
  private sub: any; //route subscriber
  private categoryId: any;
  subCategoryName: any;
  private subCategoryId: any;
  private catalogName: any;
  private catalogId: any;
  successMsg: any;
  errorMsg: any;
  category_list: any;
  total_record: any;
  itemsPerPageArray: any[];
  itemsPerPage: any;
  currentPage: number = 1;
  searchArray: any[];
  searchErr: string;
  searchTag: any;
  searchQuery: any;
  loading: any;
  current_path: any = "";

  constructor(public route: ActivatedRoute, private dataService: DataService, private router: Router, public dialog: MdDialog) {
    this.loading = this.dialog.open(LoadingComponent);
    this.itemsPerPageArray = [
      { 'itemPerPageValue': '15', 'itemPerPageName': '15' },
      { 'itemPerPageValue': '30', 'itemPerPageName': '30' },
      { 'itemPerPageValue': '45', 'itemPerPageName': '45' },
      { 'itemPerPageValue': '60', 'itemPerPageName': '60' },
      { 'itemPerPageValue': '75', 'itemPerPageName': '75' },
      { 'itemPerPageValue': '90', 'itemPerPageName': '90' },
      { 'itemPerPageValue': '100', 'itemPerPageName': '100' }
    ];
    this.itemsPerPage = this.itemsPerPageArray[0].itemPerPageValue;
  }

  ngOnInit() {
    this.token = localStorage.getItem('photoArtsAdminToken');
    this.current_path = this.getLocalStorageData();
    this.sub = this.route.params
      .subscribe(params => {
        this.subCategoryName = params['subCategoryName'];
        this.subCategoryId = params['subCategoryId'];
        this.catalogId = params['catalogId'];
        this.catalogName = params['catalogName'];
        this.categoryId = params['categoryId'];
      });
    this.getAllBackgroundCatogory(this.catalogId);
  }

  itemPerPageChanged(itemsPerPage) {
    this.loading = this.dialog.open(LoadingComponent);
    this.itemsPerPage = itemsPerPage;
    this.getAllBackgroundCatogory(this.catalogId);
  }

  do_reset() {
    this.loading = this.dialog.open(LoadingComponent);
    this.searchQuery = "";
    this.searchErr = "";
    this.currentPage = 1;
    this.getAllBackgroundCatogory(this.catalogId);
  }

  deleteSample(category) {
    let dialogRef = this.dialog.open(DeleteSubcategoryImageByIdComponent);
    dialogRef.componentInstance.sub_category_img_id = category.img_id;
    dialogRef.afterClosed().subscribe(result => {
      this.getAllBackgroundCatogory(this.catalogId);
    });
  }

  addSample() {
    let dialogRef = this.dialog.open(PopularSamplesAddComponent);
    dialogRef.componentInstance.sample_data.catalog_id = this.catalogId;
    dialogRef.afterClosed().subscribe(result => {
      this.getAllBackgroundCatogory(this.catalogId);
    });
  }

  updateSample(category) {
    let dialogRef = this.dialog.open(PopularSamplesUpdateComponent);
    let sample_data = category;
    sample_data.catalog_id = this.catalogId;
    dialogRef.componentInstance.sample_data = JSON.parse(JSON.stringify(sample_data));
    dialogRef.afterClosed().subscribe(result => {
      this.getAllBackgroundCatogory(this.catalogId);
    });
  }

  pageChanged(event) {
    this.loading = this.dialog.open(LoadingComponent);
    this.currentPage = event;
    this.getAllBackgroundCatogory(this.catalogId);
  }

  getAllBackgroundCatogory(catalogId) {
    this.token = localStorage.getItem('photoArtsAdminToken');
    this.dataService.postData('getSampleImagesForAdmin',
      {
        "catalog_id": catalogId
      }, {
        headers: {
          'Authorization': 'Bearer ' + this.token
        }
      }).subscribe(results => {
        if (results.code == 200) {
          this.category_list = results.data.image_list;
          this.total_record = this.category_list.length;
          this.loading.close();
          this.errorMsg = "";
          this.successMsg = results.message;
        }
        else if (results.code == 400) {
          this.loading.close();
          localStorage.removeItem("photoArtsAdminToken");
          this.router.navigate(['/admin']);
        }
        else if (results.code == 401) {
          this.token = results.data.new_token;
          localStorage.setItem("photoArtsAdminToken", this.token);
          this.getAllBackgroundCatogory(this.catalogId);
        }
        else {
          this.loading.close();
          this.successMsg = "";
          this.errorMsg = results.message;
        }
      }, error => {
        /* console.log(error.status); */
        /* console.log(error); */
      });
  }

  getLocalStorageData() {
    let tmp_selected_catagory = JSON.parse(localStorage.getItem("selected_catagory"));
    let tmp_selected_sub_catagory = JSON.parse(localStorage.getItem("selected_sub_catagory"));
    let tmp_selected_catalog = JSON.parse(localStorage.getItem("selected_catalog"));
    let tmp_current_path = tmp_selected_catagory.name + " / " + tmp_selected_sub_catagory.name + " / " + tmp_selected_catalog.name;
    return tmp_current_path;
  }

}

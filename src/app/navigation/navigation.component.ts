import { Component, OnInit } from '@angular/core';
import { MdDialog } from '@angular/material';
import { Router, ActivatedRoute } from '@angular/router';
import { DataService } from '../data.service';
import { LoadingComponent } from '../loading/loading.component';

@Component({
  selector: 'app-navigation',
  templateUrl: './navigation.component.html',
  styleUrls: ['./navigation.component.css']
})
export class NavigationComponent implements OnInit {

  selected_nav_categories: any;
  selected_nav_images_with_details: any;
  selected_nav_all_users: any;
  selected_nav_purchases: any;
  selected_nav_restored: any;
  selected_nav_notifications: any;
  selected_nav_settings: any;
  selected_nav_advertisements: any;
  selected_nav_redis_cache: any;
  selected_nav_promocodes: any;
  selected_nav_search_tags: any;
  selected_nav_statistics: any;

  token: any;
  loading: any;
  private sub: any; //route subscriber
  private categoryId: any;
  private subCategoryName: any;
  private subCategoryId: any;
  private sub_category_id: any;
  private catalogId: any;
  private catalogName: any;

  constructor(private router: Router, private route: ActivatedRoute, private dataService: DataService, public dialog: MdDialog) { }

  ngOnInit() {
    this.sub = this.route.params
      .subscribe(params => {
        this.categoryId = params['categoryId'];
        this.subCategoryName = params['subCategoryName'];
        this.sub_category_id = params['sub_category_id'];
        this.subCategoryId = params['subCategoryId'];
        this.catalogId = params['catalogId'];
        this.catalogName = params['catalogName'];
      });
    this.getCurrentRoute();
  }

  getCurrentRoute() {
    if (this.router.url == "/admin/categories") {
      this.selected_nav_categories = "selected";
    }
    if (this.router.url == "/admin/categories/:", this.categoryId) {
      this.selected_nav_categories = "selected";
    }
    if (this.router.url == "/admin/admob-ads") {
      this.selected_nav_categories = "selected";
    }
    if (this.router.url == "/admin/users/:", this.sub_category_id) {
      this.selected_nav_categories = "selected";
    }
    if (this.router.url == "/admin/purchases/:", this.sub_category_id) {
      this.selected_nav_categories = "selected";
    }
    if (this.router.url == "/admin/restores/:", this.sub_category_id) {
      this.selected_nav_categories = "selected";
    }
    if (this.router.url == "/admin/notification/:", this.sub_category_id) {
      this.selected_nav_categories = "selected";
    }
    if (this.router.url == "/admin/advertisements/:", this.sub_category_id) {
      this.selected_nav_categories = "selected";
    }
    if (this.router.url == "/admin/categories/:", this.subCategoryName, this.subCategoryId) {
      this.selected_nav_categories = "selected";
    }
    if (this.router.url == "/admin/categories/:", this.subCategoryName, this.catalogName, this.catalogId) {
      this.selected_nav_categories = "selected";
    }
    if (this.router.url == "/admin/advertisements") {
      this.selected_nav_advertisements = "selected";
    }
    if (this.router.url == "/admin/promocode-management") {
      this.selected_nav_promocodes = "selected";
    }
    if (this.router.url == "/admin/search-tags") {
      this.selected_nav_search_tags = "selected";
    }
    if (this.router.url == "/admin/statistics") {
      this.selected_nav_statistics = "selected";
    }
    if (this.router.url == "/admin/image-details") {
      this.selected_nav_images_with_details = "selected";
    }
    if (this.router.url == "/admin/redis-cache") {
      this.selected_nav_redis_cache = "selected";
    }
    if (this.router.url == "/admin/settings") {
      this.selected_nav_settings = "selected";
    }
  }


  nav_categories() {
    this.router.navigate(['/admin/categories']);
  }
  nav_images_with_details() {
    this.router.navigate(['/admin/image-details']);
  }
  nav_advertisements() {
    this.router.navigate(['/admin/advertisements']);
  }
  nav_promocodes() {
    this.router.navigate(['/admin/promocode-management']);
  }
  nav_search_tags() {
    this.router.navigate(['/admin/search-tags']);
  }
  nav_statistics() {
    this.router.navigate(['/admin/statistics']);
  }
  nav_redis_cache() {
    this.router.navigate(['/admin/redis-cache']);
  }
  nav_settings() {
    this.router.navigate(['/admin/settings']);
  }

  nav_logout() {
    this.loading = this.dialog.open(LoadingComponent);
    this.dataService.postData('doLogout', {}, {
      headers: {
        'Authorization': 'Bearer ' + this.token
      }
    }).subscribe(results => {
      this.loading.close();
      localStorage.removeItem("photoArtsAdminToken");
      this.router.navigate(['/admin']);
    });
  }

}

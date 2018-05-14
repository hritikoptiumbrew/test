import { Component, OnInit } from '@angular/core';
import { Router, ActivatedRoute } from '@angular/router';
import { Http, RequestOptions, Headers, Response, RequestMethod, RequestOptionsArgs } from '@angular/http';
import { MdDialog, MdDialogRef } from '@angular/material';
import { Observable } from 'rxjs/Rx';
import { DataService } from '../data.service';
import { HOST } from '../app.constants';
import { LoadingComponent } from '../loading/loading.component';

@Component({
  selector: 'app-header',
  templateUrl: './header.component.html',
  styleUrls: ['./header.component.css']
})
export class HeaderComponent implements OnInit {

  selected_nav_top_settings: any;
  selected_nav_top_profile: any;
  selected_nav_categories: any;
  selected_nav_images_with_details: any;
  selected_nav_all_users: any;
  selected_nav_purchases: any;
  selected_nav_restored: any;
  selected_nav_notifications: any;
  selected_nav_settings: any;
  loading: any;
  private sub: any; //route subscriber
  private categoryId: any;
  count: number = 0;
  token: any;

  constructor(private router: Router, private route: ActivatedRoute, private dataService: DataService, public dialog: MdDialog) {
  }

  ngOnInit() {
    this.token = localStorage.getItem('photoArtsAdminToken');
    this.getCurrentRoute();
  }

  getCurrentRoute() {
    if (this.router.url == "/admin/settings") {
      this.selected_nav_top_settings = "selected_nav";
    }
  }

  nav_categories() {
    this.router.navigate(['/admin/categories']);
  }
  nav_images_with_details() {
    this.router.navigate(['/admin/image-details']);
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

  open_nav() {
    if (this.count == 0) {
      document.getElementById("mySidenav").style.width = "250px";
      this.count = 1;
    } else {
      document.getElementById("mySidenav").style.width = "0";
      this.count = 0;
    }
  }

  close_nav() {
    document.getElementById("mySidenav").style.width = "0";
    this.count = 0;
  }

}

import { Injectable } from '@angular/core';
import { Http, Response, RequestOptions, Headers, RequestMethod, RequestOptionsArgs } from '@angular/http';
import 'rxjs/Rx';
import 'rxjs/add/operator/toPromise';
import { Router, ActivatedRoute } from '@angular/router';
import { HOST } from './app.constants';
import { Observable } from 'rxjs/Observable';
import { MdDialog, MdDialogRef } from '@angular/material';
import { ViewImageComponent } from './view-image/view-image.component';

@Injectable()
export class DataService {

  token: any;
  response: any;

  constructor(private http: Http, private router: Router, public dialog: MdDialog) {
  }

  getData(q) {
    return this.http.get(HOST.BASE_URL + q)
      .map((results: Response) => results.json());
  }

  postData(q, object, headers): Observable<any> {
    return this.http.post(HOST.BASE_URL + q, object, headers)
      .map((results: Response) => {
        if (results.status < 200 || results.status >= 300) {
          /* console.log("in result.status<200||result.status>300..."); */
          throw new Error('This request has failed ' + results.status);
        }
        else {
          return results.json();
        }
      }, (err) => {
        if (err.status == 400) {
          localStorage.removeItem("photoArtsAdminToken");
          this.router.navigate(['/admin']);
        }
      });
  }

  viewImage(imageURL) {
    let dialogRef = this.dialog.open(ViewImageComponent);
    dialogRef.componentInstance.imageSRC = imageURL;
  }

}

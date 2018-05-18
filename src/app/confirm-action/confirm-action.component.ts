import { Component, OnInit, Renderer, ViewChild, ElementRef, Input, Output, EventEmitter } from '@angular/core';
import { MdDialog, MdDialogRef } from '@angular/material';
import { Observable } from 'rxjs/Rx';
import { Router, ActivatedRoute } from '@angular/router';
import { DataService } from '../data.service';
import { HOST } from '../app.constants';
import { LoadingComponent } from '../loading/loading.component';

@Component({
  selector: 'app-confirm-action',
  templateUrl: './confirm-action.component.html',
  styleUrls: ['./confirm-action.component.css']
})
export class ConfirmActionComponent implements OnInit {

  token: any;
  catalog_id: any = {};

  constructor(public dialogRef: MdDialogRef<ConfirmActionComponent>, private dataService: DataService, private router: Router) {
    this.token = localStorage.getItem('photoArtsAdminToken');
  }

  ngOnInit() {
  }

  deleteCatalog() {
    this.dialogRef.close();
  }


}

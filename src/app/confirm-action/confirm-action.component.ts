import { Component, OnInit } from '@angular/core';
import { MdDialogRef } from '@angular/material';
import { Router } from '@angular/router';
import { DataService } from '../data.service';

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

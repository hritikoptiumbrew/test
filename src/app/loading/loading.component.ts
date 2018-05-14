import { Component, OnInit } from '@angular/core';
import { MdDialog, MdDialogRef } from '@angular/material';

@Component({
  selector: 'app-loading',
  templateUrl: './loading.component.html'
})
export class LoadingComponent implements OnInit {

  constructor(public dialogRef: MdDialogRef<LoadingComponent>) { }

  ngOnInit() {
  }

}

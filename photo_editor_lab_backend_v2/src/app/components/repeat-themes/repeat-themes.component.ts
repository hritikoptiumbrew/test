import { Component, OnInit } from '@angular/core';
import { NbDialogRef } from '@nebular/theme';
import { ERROR } from 'app/app.constants';
import { DataService } from 'app/data.service';
import { UtilService } from 'app/util.service';

@Component({
  selector: 'ngx-repeat-themes',
  templateUrl: './repeat-themes.component.html',
  styleUrls: ['./repeat-themes.component.scss']
})
export class RepeatThemesComponent implements OnInit {
  selected_month:any;
  from_month:any;
  from_month_str:any;
  to_year:any;
  from_year:any;
  remaining_months:any = [];
  isChecked:any = false;
  selected_sub_category_id:any;
  selected_industry_id:any;
  months = [
    {id:1,name:'Jan'},
    {id:2,name:'Feb'},
    {id:3,name:'Mar'},
    {id:4,name:'Apr'},
    {id:5,name:'May'},
    {id:6,name:'Jun'},
    {id:7,name:'Jul'},
    {id:8,name:'Aug'},
    {id:9,name:'Sep'},
    {id:10,name:'Oct'},
    {id:11,name:'Nov'},
    {id:12,name:'Dec'}
  ];

  constructor(private dialogref: NbDialogRef<RepeatThemesComponent>,private dataService: DataService,private utils: UtilService) {
    this.utils.dialogref = this.dialogref;
   }

  ngOnInit(): void {
    // const d = new Date();
    // let month = d.getMonth();
    this.from_month_str = this.getMonthName(this.from_month);
    if(this.from_month == 12){
      this.to_year = Number(this.from_year) + 1;
    }
    else{
      this.to_year = this.from_year
    } 
    this.months.forEach(element => {
      if(element.id > this.from_month){
        this.remaining_months.push(element);
      }
      if(this.from_month == 12){
        this.remaining_months.push(element);
      }
    });
  }

  closedialog() {
    this.dialogref.close({ res: "" });
  }

  getMonthName(monthNumber) {
    const date = new Date();
    date.setMonth(monthNumber - 1);
  
    return date.toLocaleString('en-US', {
      month: 'long',
    });
  }

  repeatPostThemes(){
    this.utils.showLoader();
    let request_data = {
      "sub_category_id": this.selected_sub_category_id,
      "from_month": this.from_month,
      "from_year": this.from_year,
      "to_month" : Number(this.selected_month),
      "to_year": this.to_year,
      "replace_with_existing": this.isChecked == true ? 1 : 0,
      "industry_id": this.selected_industry_id
    }
    this.dataService.postData('repeatPostThemes', request_data,
      {
        headers:
          { 'Authorization': 'Bearer ' + localStorage.getItem('at') }
      })
      .then(response => {
        if (response.code == 200) {
          this.dialogref.close({ res: "repeatPost" });
          this.utils.hideLoader();
          this.utils.showSuccess(response.message, 3000);
        } else if (response.code == 201) {
          this.utils.hideLoader();
          this.utils.showError(response.message, 3000);
        }
        else {
          this.utils.hideLoader();
          this.utils.showError(ERROR.SERVER_ERR, 3000);
        }
      })
      .catch(e => {
        this.utils.hideLoader();
        console.log(e);
        this.utils.showError(ERROR.SERVER_ERR, 3000);
      })
  }
}

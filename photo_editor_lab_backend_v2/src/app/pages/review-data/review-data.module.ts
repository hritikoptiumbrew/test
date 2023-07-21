import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ReviewDataComponent } from './review-data.component';
import { NbCardModule, NbInputModule, NbSelectModule } from '@nebular/theme';
import { Ng2SmartTableModule } from 'ng2-smart-table';
import { FormsModule } from '@angular/forms';
import { NgxPaginationModule } from 'ngx-pagination';
import { SizePipe } from 'app/size.pipe';
import { LazyLoadImageModule } from 'ng-lazyload-image';



@NgModule({
  declarations: [ReviewDataComponent],
  imports: [
    CommonModule,
    CommonModule,
    NbCardModule,
    NbInputModule,
    Ng2SmartTableModule,
    FormsModule,
    NbSelectModule,
    NgxPaginationModule,
    LazyLoadImageModule
  ]
})
export class ReviewDataModule { }

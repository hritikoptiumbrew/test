import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { AiPosterComponent } from './ai-poster.component';
import { NbCardModule, NbInputModule, NbSelectModule } from '@nebular/theme';
import { Ng2SmartTableModule } from 'ng2-smart-table';
import { FormsModule } from '@angular/forms';
import { NgxPaginationModule } from 'ngx-pagination';
import { SizePipe } from 'app/size.pipe';
import { LazyLoadImageModule } from 'ng-lazyload-image';


@NgModule({
  declarations: [AiPosterComponent],
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
export class AiPosterModule { }

import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { NbAutocompleteModule, NbCardModule, NbFormFieldModule, NbIconModule, NbInputModule, NbPopoverModule, NbSelectModule, NbTabsetModule, NbToggleModule, NbTooltipModule } from '@nebular/theme';
import { FormsModule } from '@angular/forms';
import { Ng2SmartTableModule } from 'ng2-smart-table';
import { PostcalenderComponent } from './postcalender.component';
import { NgxPaginationModule } from 'ngx-pagination';
import { CalendarModule, DateAdapter, CalendarMonthModule, CalendarMonthViewComponent} from 'angular-calendar';
import { adapterFactory } from 'angular-calendar/date-adapters/date-fns';
import { NgbModule } from '@ng-bootstrap/ng-bootstrap';

@NgModule({
  declarations: [PostcalenderComponent],
  imports: [
    CommonModule,
    NbTabsetModule,
    FormsModule,
    NbCardModule,
    NbFormFieldModule,
    NbInputModule,
    NbIconModule,
    Ng2SmartTableModule,
    NbSelectModule,
    NbTooltipModule,
    NbPopoverModule,
    NgxPaginationModule,
    NbAutocompleteModule,
    NgbModule,
    CalendarModule.forRoot({
      provide: DateAdapter,
      useFactory: adapterFactory,
    }),
    NbToggleModule
  ]
})
export class PostCalenderModule { }
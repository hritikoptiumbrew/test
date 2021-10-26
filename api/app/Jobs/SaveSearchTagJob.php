<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use App\Http\Controllers\ImageController;
use Illuminate\Support\Facades\DB;
use Exception;
use Log;

class SaveSearchTagJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $template_count;
    public $search_category;
    public $sub_category_id;
    public $is_success;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($template_count, $seacrh_tag, $sub_category_id, $is_success)
    {
        try {
            $this->template_count = $template_count;
            $this->search_category = $seacrh_tag;
            $this->sub_category_id = $sub_category_id;
            $this->is_success = $is_success;

        } catch (Exception $e) {
            Log::error("SaveSearchTagJob construct() : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
        }
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $week_start_date = date( 'Y-m-d', strtotime( 'monday this week'));
            $week_end_date = date( 'Y-m-d', strtotime( 'sunday this week' ));

            $tag_deatils = DB::select('SELECT
                                            id
                                        FROM tag_analysis_master
                                        WHERE
                                            tag = ? AND
                                            is_success = ? AND
                                            sub_category_id = ? AND 
                                            DATE(week_start_date) >= ? AND
                                            DATE(week_end_date) <= ?',[$this->search_category, $this->is_success, $this->sub_category_id, $week_start_date, $week_end_date]);

            if(count($tag_deatils) > 0) {
                $id = $tag_deatils[0]->id;
                DB::update('UPDATE tag_analysis_master SET search_count = search_count + 1, content_count= ? WHERE id=?',[$this->template_count,$id]);
            } else {
                DB::insert('INSERT INTO
                               tag_analysis_master(tag, is_success, content_count, search_count, sub_category_id, week_start_date, week_end_date)
                               VALUES(?, ?, ?, ?, ?, ?, ?)', [$this->search_category, $this->is_success, $this->template_count, 1, $this->sub_category_id, $week_start_date, $week_end_date]);
            }

        } catch (Exception $e) {
            Log::error("SaveSearchTagJob handle() : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
        }
    }
}

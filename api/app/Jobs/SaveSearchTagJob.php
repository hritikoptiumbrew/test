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
    public $is_featured;
    public $category_id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($template_count, $seacrh_tag, $sub_category_id,  $is_success, $is_featured, $category_id)
    {
        try {
            $this->template_count = $template_count;
            $this->search_category = $seacrh_tag;
            $this->sub_category_id = $sub_category_id;
            $this->is_success = $is_success;
            $this->is_featured = $is_featured;
            $this->category_id = $category_id;
            Log::info('SaveSearchTagJob: Value', ['template_count' => $this->template_count , 'search_tag' => $this->search_category , 'sub_category_id' => $this->sub_category_id , 'is_success' => $this->is_success , 'is_featured' => $this->is_featured , 'category_id' => $this->category_id]);
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

            $tag_details = DB::select('SELECT
                                            id
                                        FROM tag_analysis_master
                                        WHERE
                                            tag = ? AND
                                            is_success = ? AND
                                            is_featured = ? AND 
                                            category_id = ? AND 
                                            sub_category_id = ?',[$this->search_category, $this->is_success, $this->is_featured, $this->category_id, $this->sub_category_id]);

            Log::info("SaveSearchTagJob: Tag is exist or not.", ['tag_result' => $tag_details, 'search_tag' => $this->search_category, 'is_success' => $this->is_success, 'is_featured' => $this->is_featured, 'category_id' => $this->category_id, 'sub_category_id' => $this->sub_category_id]);

            if(count($tag_details) > 0) {
                $id = $tag_details[0]->id;
                $update_query = DB::update('UPDATE tag_analysis_master SET search_count = search_count + 1, content_count = ? WHERE id = ?',[$this->template_count, $id]);

                if (!$update_query) {
                    Log::info('SaveSearchTagJob : Search tag count is not increase', ["id" => $id]);
                }else{
                    Log::info('SaveSearchTagJob : Search tag count is increase', ["result" => $update_query]);
                }

            } else {
                $insert_query = DB::insert('INSERT INTO
                               tag_analysis_master(tag, is_success, is_featured, category_id, content_count, search_count, sub_category_id)
                               VALUES(?, ?, ?, ?, ?, ?, ?)', [$this->search_category, $this->is_success, $this->is_featured, $this->category_id, $this->template_count, 1, $this->sub_category_id]);

                if (!$insert_query) {
                    Log::info('SaveSearchTagJob : Search tag not insert');
                }else{
                    Log::info('SaveSearchTagJob : Search tag insert', ["result" => $insert_query]);
                }
            }

        } catch (Exception $e) {
            Log::error("SaveSearchTagJob handle() : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
        }
    }
}

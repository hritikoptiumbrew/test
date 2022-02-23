<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Response;
use Exception;
use Log;
use Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class SendStickerTagReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'SendStickerTagReport';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send weekly search sticker tag report mail to admin.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try{
            $sub_category_array = array();
            $tags_detail = DB::select('(SELECT 
                                            tam.id,
                                            tam.tag,
                                            tam.is_success,
                                            tam.content_count,
                                            tam.search_count,
                                            tam.sub_category_id,
                                            tam.update_time 
                                        FROM
                                            tag_analysis_master as tam
                                        WHERE 
                                            tam.is_success = 1 AND
                                            tam.is_featured = 0 AND
                                            tam.category_id = ? 
                                        ORDER BY
                                            update_time DESC
                                            LIMIT 20)
                                        UNION
                                         (SELECT
                                             tam.id,
                                            tam.tag,
                                            tam.is_success,
                                            tam.content_count,
                                            tam.search_count,
                                            tam.sub_category_id,
                                            tam.update_time 
                                        FROM 
                                            tag_analysis_master AS tam
                                        WHERE 
                                            tam.is_success = 0 AND
                                            tam.is_featured = 0 AND
                                            tam.category_id = ?
                                        ORDER BY
                                            update_time DESC
                                            LIMIT 20)
                                        ORDER BY is_success DESC, search_count DESC',
            [Config::get('constant.CATEGORY_ID_OF_STICKER'), Config::get('constant.CATEGORY_ID_OF_STICKER')]);

            foreach ($tags_detail AS $i => $tag_detail){
                if (!isset($sub_category_array[$tag_detail->sub_category_id])){
                    $sub_category = DB::select('SELECT GROUP_CONCAT(sc.name SEPARATOR " + ") AS sub_category_name FROM sub_category AS sc WHERE sc.id IN ('.$tag_detail->sub_category_id.')');
                    $sub_category_array[$tag_detail->sub_category_id] = $sub_category[0]->sub_category_name;
                }
                $tag_detail->sub_category_name = $sub_category_array[$tag_detail->sub_category_id];
            }

            if(count($tags_detail) > 0) {

                $host_name = Config::get('constant.APP_HOST_NAME');
                $subject = "PhotoEditorLab: Search tag analysis report (host: $host_name).";
                $heading = "Weekly search sticker tag report";
                $template = "search_tag_report_v2";
                $data = array("template" => $template, "subject" => $subject, "message_body" => $tags_detail, "app_name" => $host_name, "heading" => $heading);

                Mail::send($data['template'], $data, function ($message) use ($data) {
                    $message->to(Config::get('constant.SUPPORTER_EMAIL_ID'))->subject($data['subject']);
                    $message->bcc(Config::get('constant.ADMIN_EMAIL_ID'))->subject($data['subject']);
                    $message->bcc(Config::get('constant.SUB_ADMIN_EMAIL_ID'))->subject($data['subject']);
                });

            }else{
                Log::info('SendCardTagReport : No search card tag found for last week.');
            }

        } catch(Exception $e) {
            Log::error("SendCardTagReport command handle() : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
        }
    }
}

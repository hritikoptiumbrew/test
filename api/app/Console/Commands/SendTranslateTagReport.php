<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;
use Response;
use Exception;
use Log;
use Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use stdClass;
use Illuminate\Support\Facades\Cache;

class SendTranslateTagReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'SendTranslateTagReport';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send weekly search translate tag report mail to admin.';

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
            $old_data = Cache::get('translationReport');
            $old_data = isset($old_data) ? $old_data : array();

            foreach ($old_data AS $i => $data){
                if(!isset($sub_category_array[$data['sub_category_id']])){
                    $sub_category = DB::select('SELECT GROUP_CONCAT(sc.name SEPARATOR " + ") AS sub_category_name FROM sub_category AS sc WHERE sc.id IN ('.$data['sub_category_id'].') ');
                    $sub_category_array[$data['sub_category_id']] = isset($sub_category[0]->sub_category_name) ? $sub_category[0]->sub_category_name : "N/A";
                }
                $old_data[$i]['sub_category_name'] = $sub_category_array[$data['sub_category_id']];
            }

            if(count($old_data) > 0) {

                $host_name = Config::get('constant.APP_HOST_NAME');
                $subject = "PhotoEditorLab: Search tag analysis report (host: $host_name).";
                $heading = "Weekly search translate tag report";
                $template = "translate_tag_report";
                $data = array("template" => $template, "subject" => $subject, "message_body" => $old_data, "app_name" => $host_name, "heading" => $heading);

                Mail::send($data['template'], $data, function ($message) use ($data) {
                    $message->to(Config::get('constant.ADMIN_EMAIL_ID'))->subject($data['subject']);
                    $message->bcc(Config::get('constant.SUB_ADMIN_EMAIL_ID'))->subject($data['subject']);
                });

            }else{
                Log::info('SendTranslateTag : No search tag found for last week.');
            }

            Redis::del('pel:translationReport');

        } catch (Exception $e) {
            Log::error("SendTagReport command handle() : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
        }
    }
}

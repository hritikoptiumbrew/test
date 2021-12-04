<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Response;
use Exception;
use Log;
use Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class SendTagReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'SendTagReportMail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send weekly search tag report mail to admin.';

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

            $tags_detail = DB::select('(SELECT 
                                            tam.id,
                                            tam.tag,
                                            tam.is_success,
                                            tam.content_count,
                                            scm.name AS sub_category_name,
                                            tam.search_count,
                                            tam.update_time 
                                        FROM
                                            tag_analysis_master as tam
                                        LEFT JOIN  
                                            sub_category as scm
                                        ON 
                                            scm.id = tam.sub_category_id
                                        WHERE 
                                            tam.is_success = 1
                                        ORDER BY
                                            update_time DESC
                                            LIMIT 20)
                                        UNION
                                         (SELECT
                                             tam.id,
                                            tam.tag,
                                            tam.is_success,
                                            tam.content_count,
                                            scm.name AS sub_category_name,
                                            tam.search_count,
                                            tam.update_time 
                                        FROM 
                                            tag_analysis_master AS tam
                                        LEFT JOIN  
                                            sub_category AS scm
                                        ON 
                                            scm.id = tam.sub_category_id
                                        WHERE 
                                            tam.is_success = 0
                                        ORDER BY
                                            update_time DESC
                                            LIMIT 20)
                                        ORDER BY is_success DESC, search_count DESC');

            if(count($tags_detail) > 0) {

                $host_name = Config::get('constant.APP_HOST_NAME');
                $subject = "PhotoEditorLab: Search tag analysis report (host: $host_name).";
                $template = "search_tag_report";
                $data = array("template" => $template, "subject" => $subject, "message_body" => $tags_detail, "app_name" => $host_name);

                Mail::send($data['template'], $data, function ($message) use ($data) {
                    $message->to(Config::get('constant.SUPPORTER_EMAIL_ID'))->subject($data['subject']);
                    $message->bcc(Config::get('constant.ADMIN_EMAIL_ID'))->subject($data['subject']);
                    $message->bcc(Config::get('constant.SUB_ADMIN_EMAIL_ID'))->subject($data['subject']);
                });

            }else{
                Log::info('SendTagReport : No search tag found for last week.');
            }

        } catch (Exception $e) {
            Log::error("SendTagReport command handle() : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
        }
    }

}

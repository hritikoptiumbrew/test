<?php

namespace App\Console\Commands;

use App\Http\Controllers\ImageController;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use ZipArchive;

class SendDeletedSearchTagFile extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'SendDeletedSearchTagFile';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Used to send deleted search tag file by mail.';

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
        try {

            DB::beginTransaction();
            $tag_analysis_count = DB::delete('DELETE FROM tag_analysis_master WHERE TIMESTAMPDIFF(DAY, update_time, NOW()) > ?', [Config::get('constant.DAYS_TO_KEEP_SEARCH_TAG')]);
            //We delete record only de-active session. That's why we have to calculate session time from config/jwt.php
            $user_session_time = (config('jwt.ttl') + config('jwt.refresh_ttl')) * 2;
            $user_session_count = DB::delete('DELETE FROM user_session WHERE TIMESTAMPDIFF(MINUTE, update_time, NOW()) > ?', [$user_session_time]);
            DB::commit();
            Log::info('SendDeletedSearchTagFile : Total deleted search tag record.', ['tag_analysis_count' => $tag_analysis_count, 'user_session_count' => $user_session_count]);
            return 1;

            // Currently we do not send backup file in mail
            $api_name = "SendDeletedSearchTagFile";
            $zip = new ZipArchive();
            $milliseconds = intval(microtime(true) * 1000);
            $csv_file_name = $milliseconds . ".csv";
            $zip_file_name = $milliseconds . ".zip";
            $dir = Config::get('constant.TEMP_FILE_DIRECTORY');
            $original_csv_path = "./.." . $dir . $csv_file_name;
            $original_zip_path = "./.." . $dir . $zip_file_name;

            $tag_details = DB::select('SELECT 
                                            tam.id,
                                            c.name AS category_name,
                                            tam.sub_category_id AS all_sub_category_name,
                                            scm.name,
                                            IF(tam.is_featured = 1, "Featured", "Non-Featured") AS catalog_type,
                                            tam.tag,
                                            tam.content_count,
                                            tam.search_count,
                                            tam.create_time,
                                            tam.update_time,
                                            IF(tam.is_success = 0, "Fail", "Success") AS is_success
                                        FROM
                                            tag_analysis_master AS tam
                                            LEFT JOIN sub_category AS scm ON scm.id = tam.main_sub_category_id
                                            LEFT JOIN category AS c ON c.id = tam.category_id
                                        ORDER BY tam.category_id, tam.main_sub_category_id, tam.is_featured, tam.is_success');

            $tag_details_array = json_decode(json_encode($tag_details), 1);
//            dd($tag_details_array);

            $all_sub_category_id_array = array_unique(array_column($tag_details, 'all_sub_category_name'));
//            dd($all_sub_category_id_array);

            $sub_category_array = [];
            foreach ($all_sub_category_id_array as $item) {
                $sub_category = DB::select('SELECT GROUP_CONCAT(sc.name SEPARATOR " + ") AS sub_category_name FROM sub_category AS sc WHERE sc.id IN (' . $item . ') ');
                $sub_category_array[$item] = isset($sub_category[0]->sub_category_name) ? $sub_category[0]->sub_category_name : "N/A";
            }

//            dd($all_sub_category_details, $tag_details_array);

            $heading = array("Sr No", "Category Name", "All Sub Category Name", "Sub Category Name", "Catalog Type", "Tag Name", "Content Count", "Search Count", "Created At", "Updated At", "Status");
            $file = fopen($original_csv_path, "w");
            fputcsv($file, $heading);
            foreach ($tag_details_array as $record) {
                $record['all_sub_category_name'] = $sub_category_array[$record['all_sub_category_name']];
                fputcsv($file, $record);
            }
            fclose($file);

            //make zip file from csv file in temp folder
            if ($zip->open($original_zip_path, ZipArchive::CREATE) === TRUE) {
                foreach (glob($original_csv_path) as $key => $value) {
                    $relative_name_in_zip_file = basename($value);
                    $zip->addFile($value, $relative_name_in_zip_file);
                }
                $zip->close();
            }

            $host_name = Config::get('constant.APP_HOST_NAME');
            $subject = "(host: $host_name) PhotoEditorLab: Backup Search tag File.";
            $template = "simple";
            $message_body = array(
                'message' => 'PhotoEditorLab: The search tag file is attached with the mail. <br> Api Name = ' . $api_name,
                'user_name' => 'Admin'
            );
            $data = array('template' => $template, 'subject' => $subject, 'message_body' => $message_body, 'original_zip_path' => $original_zip_path, 'zip_file_name' => $zip_file_name);

            //send report to super admin attech with zip file
            Mail::send($data['template'], $data, function ($message) use ($data) {
                $message->to(Config::get('constant.SUPPORTER_EMAIL_ID'))->subject($data['subject']);
                $message->bcc(Config::get('constant.SUPER_ADMIN_EMAIL_ID'))->subject($data['subject']);
                $message->bcc(Config::get('constant.ADMIN_EMAIL_ID'))->subject($data['subject']);
                $message->bcc(Config::get('constant.SUB_ADMIN_EMAIL_ID'))->subject($data['subject']);
                $message->attach($data['original_zip_path'], array('as' => $data['zip_file_name'], 'mime' => 'application/zip'));
            });

            //remove zip & csv file in temp folder
            if (($is_exist = ((new ImageController())->checkFileExist($original_csv_path)) != 0)) {
                unlink($original_csv_path);
            }
            if (($is_exist = ((new ImageController())->checkFileExist($original_zip_path)) != 0)) {
                unlink($original_zip_path);
            }

            DB::statement('TRUNCATE TABLE tag_analysis_master');

        } catch (Exception $e) {
            Log::error("SendDeletedSearchTagFile command Exception : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            return 0;
        }
    }
}

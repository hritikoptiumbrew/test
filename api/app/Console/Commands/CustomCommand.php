<?php
namespace App\Console\Commands;
use App\Mail\SuccessMail;
use Illuminate\Console\Command;
use DB;
use Log;
use Mail;
use Config;
use Exception;
use Response;
class CustomCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'custom:command'; //run this command for run sheduling
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Invalid token';
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
            $device_result = DB::select('SELECT dm.device_reg_id  FROM device_master dm WHERE dm.is_active=1 AND dm.device_platform = "ios"  AND LENGTH(dm.device_reg_id) = 64 AND dm.device_reg_id != "" ');
            //Log::info('device_reg_id',['device_reg_id'=>$device_result]);
            $no_of_token_scan = sizeof($device_result);

            if ($no_of_token_scan > 0) {

                //for development purpose
                //$certFile = app_path() . '/certificates/aps_development.pem';

                //for purpose production purpose
                $certFile = app_path() . '/certificates/aps_production.pem';

                $cert_pw = 'Optimumbrew';
                //Log::info('no_of_token_scan',['no_of_token_scan'=>$no_of_token_scan]);
                $deviceIds = (array)$device_result;

                $payload = '{
                 "aps":{ "title":"", "alert":"","sound":"","content-available" : 1, "thread-id": "","user_id": "", "extra_id": "", "messageType": "", "action_user_id": ""}
                }';

                $ctx = stream_context_create();
                stream_context_set_option($ctx, 'ssl', 'local_cert', $certFile);
                stream_context_set_option($ctx, 'ssl', 'verify_peer', false);
                stream_context_set_option($ctx, 'ssl', 'passphrase', $cert_pw);
                $feedback = array();
                foreach ($deviceIds as $item) {
                    // wait for some time
                    sleep(1);

//if you are use local server comment 1st $fp1 line. else you are use live server comment 2nd  $fp2 line
                    // Open a connection to the APNS server
                    $fp = stream_socket_client('ssl://gateway.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);
                    //$fp = stream_socket_client('ssl://gateway.sandbox.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);
                    stream_set_blocking($fp, 0);

                    if (!$fp) {
                        throw new Exception("Failed to connect: $err $errstr");
                        // exit("Failed to connect: $err $errstr" . '<br />');
                    } else {
                        // echo 'Apple service is online. ' . '<br />';
                    }

                    // Build the binary notification
                    $msg = chr(0) . pack('n', 32) . pack('H*', str_replace(' ', '', $item->device_reg_id)) . pack('n', strlen($payload)) . $payload;
                    //$msg = chr(0) . pack('n', 32) . pack('H*',$item) . pack('n', strlen($payload)) . $payload;

                    // Send it to the server
                    $result = fwrite($fp, $msg, strlen($msg));
                    //Log::info('result',['result'=>$result]);

                    if (!$result) {
                        //  echo 'Undelivered message count: ' . $item . '<br />';
                        //Log::info('sendMessageToIOS', ['result' => 'Undelivered message To: ' . $item->device_reg_id]);

                    } else {
                        //Log::info('sendMessageToIOS', ['result' => 'Delivered message To: ' . $item->device_reg_id]);
                    }
                    fclose($fp);

//if you are use local server comment 1st $fp1 line. else you are use live server comment 2nd  $fp2 line
                    $fp1 = stream_socket_client('ssl://feedback.push.apple.com:2196', $error, $errorString, 60, STREAM_CLIENT_CONNECT, $ctx);
                    //  $fp1 = stream_socket_client('ssl://feedback.sandbox.push.apple.com:2196', $error, $errorString, 60, STREAM_CLIENT_CONNECT, $ctx);
                    if (!$fp1) {
                        echo "ERROR $error: $errorString\n";
                        return;
                    } else {
                        while ($devcon = fread($fp1, 38)) {
                            $arr = unpack("H*", $devcon);
                            $rawhex = trim(implode("", $arr));
                            $feedback[] = substr($rawhex, 12, 64);
                            //$feedback1[] = unpack("N1timestamp/n1length/H*devtoken", $devcon);

                        }

                    }


                    fclose($fp1);
                }
                //   Log::info('feedback_array', ['Exception' => $feedback]);

                // echo 'The connection has been closed by the client' . '<br />';

                $no_of_feedback_scan = sizeof($feedback);
                //Log::info('no_of_feedback_scan', ['Exception' => $no_of_feedback_scan]);
                if ($no_of_feedback_scan > 0) {
                    //  $data = json_decode(json_encode($feedback), true);
                    foreach ($feedback as $f) {
                        //Log::info('token', ['Exception' => $f]);
                        DB::beginTransaction();
                        DB::update('UPDATE device_master
                               SET is_active = ?
                               WHERE device_reg_id = ?', [0, $f]);
                        DB::commit();

                    }

                }

                $this->info('All invalid token became inactive!');
                //Log::info('feedback_array', ['Exception' => 'All invalid token became inactive!']);

                $email_id = Config::get('constant.ADMIN_EMAIL_ID');
                //$url = Config::get('constant.path');
                $url = "Invalid Token deleted";
                $subject = 'Ob Photolab : Ios invalid token';
                $message_body = "APNS token result : " . $no_of_token_scan .
                                "  \n\nAPNS feedback token result : " . $no_of_feedback_scan ;
                //Mail::to($email_id)->send(new SuccessMail($url,$subject,$message_body));
                Mail::to($email_id)->bcc('gabanibhavesh9@gmail.com')->send(new SuccessMail($url,$subject,$message_body));

                $this->info('Invalid Token deleted!');

            } else {
                $this->info('Invalid Token was not found!');
                //Log::info('feedback_array', ['Exception' => 'Invalid Token was not found!']);
            }


        } catch (\Exception $e) {
            $this->info('Error : '.$e->getMessage());
            //Log::error('invalideTokenHandle', ['Exception' => $e->getMessage()]);
        }
    }
}
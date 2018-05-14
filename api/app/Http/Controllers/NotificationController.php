<?php

//////////////////////////////////////////////////////////////////////////////
//                   OptimumBrew Technology Pvt. Ltd.                       //
//                                                                          //
// Title:            Qr-Code                                             //
// File:             NotificationController.php                             //
// Since:            11-August-2016                                         //
//                                                                          //
// Author:           Dipali Dhanani                                         //
// Email:            dipali.dhanani@optimumbrew.com                         //
//                                                                          //
//////////////////////////////////////////////////////////////////////////////

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Config;
use Illuminate\Support\Facades\Input;
use Log;
use DB;
use Exception;
use Response;

class NotificationController extends Controller
{
    /**
     * @api {post} sendPushNotification send push notification
     * @apiName sendPushNotification
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "data": {
     * "GCM_DATA": {
     * "sub_category_id":1,
     * "title": "title1",
     * "message": "message1"
     * }
     * }
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Notification sent successfully.",
     * "cause": "",
     * "response": {
     * "notification_id":1
     * }
     * }
     */
    public function sendPushNotification(Request $request_body)
    {
        try {
            $request = json_decode($request_body->getContent());

            if (($response = (new VerificationController())->validateRequiredParameter(array('data'), $request)) != '')
                return $response;

            $data = $request->data;
            if (($response = (new VerificationController())->validateRequiredParameter(array('GCM_DATA'), $data)) != '')
                return $response;
            $GCM_DATA = $data->GCM_DATA;
            $GCM_DATA->messageType = 1;
            if (($response = (new VerificationController())->validateRequiredParameter(array('title', 'message','sub_category_id'), $GCM_DATA)) != '')
                return $response;

            $notification_id = $this->sendNotification($data);

            $result = ['notification_id' => $notification_id];
            $response = Response::json(array('code' => '200', 'message' => 'Notification sent successfully.', 'cause' => '', 'response' => $result));

        } catch (Exception $e) {
            Log::error('sendPushNotification', ['Exception' => $e->getMessage()]);
            //DB::rollBack();
        }
        return $response;
    }

    /** URL Notification
     *
     *
     * {
     * "aps": {
     * "title": "title1",
     * "alert": "message1",
     * "sound": "default",
     * "category": "2",
     * "url": "http://www.google.com/xyz.pdf"
     * }
     * }
     * @param Request $request_body
     * @return string
     */

    public function sendURLPushNotification(Request $request_body)
    {
        try {

            if (!$request_body->has('request_data'))
                return Response::json(array('code' => '201', 'message' => 'required field request_data is missing or empty', 'cause' => '', 'response' => json_decode("{}")));


            /* if (!$request_body->hasFile('file'))
                 return Response::json(array('code' => '201', 'message' => 'required field file is missing or empty', 'cause' => '', 'response' => json_decode("{}")));*/
            $request = json_decode($request_body->input('request_data'));

            $data = $request->data;
            if (($response = (new VerificationController())->validateRequiredParameter(array('GCM_DATA'), $data)) != '')
                return $response;

            $GCM_DATA = $data->GCM_DATA;
            if (($response = (new VerificationController())->validateRequiredParameter(array('title', 'message'), $GCM_DATA)) != '')
                return $response;


            if ($request_body->hasFile('file')) {
                $pdf = Input::file('file');
                if (($response = (new ImageController())->verifyPDF($pdf)) != '')
                    return $response;

                $notification_action_url = (new ImageController())->generatePDFFileName('Qr-code', $pdf);

                (new ImageController())->savePDF($notification_action_url);

                // "URL":"www.Qr-code.com",
                // "messageType":2
                //Log::info('sendURLPushNotification', ['Data 3' => 'inside if']);
                $notification_type = '2'; // For URL Notification
                $base_url = (new Utils())->getBaseUrl();
                $notification_action_url = $base_url . Config::get('constant.PDF_DIRECTORY') . $notification_action_url;

                //Log::info('pdf_url', ['url_path' => $notification_action_url]);

                $request_object = json_decode($request_body->input('request_data'), true);
                $GCM_DATA_object = $request_object['data'];
                $GCM_DATA_object['GCM_DATA']['messageType'] = $notification_type;
                $GCM_DATA_object['GCM_DATA']['url'] = $notification_action_url;
                $data = json_encode($GCM_DATA_object);
                /* $data = '{
                         "GCM_DATA": {
                           "title": "' .$GCM_DATA->title. '",
                           "message": "' .$GCM_DATA->message . '",
                           "messageType": "' . $notification_type . '",
                           "url": "' . $notification_action_url . '"
                         }
                      }';*/

                $data = json_decode($data);

            } else {
                $notification_type = '1';
                $request_object = json_decode($request_body->input('request_data'), true);
                $GCM_DATA_object = $request_object['data'];
                $GCM_DATA_object['GCM_DATA']['messageType'] = $notification_type;
                $data = json_encode($GCM_DATA_object);
                /* $data = '{
                         "GCM_DATA": {
                           "title": "' .$GCM_DATA->title. '",
                           "message": "' .$GCM_DATA->message . '",
                           "messageType": "' . $notification_type . '"
                         }
                      }';*/

                $data = json_decode($data);
            }
            //DB::beginTransaction();
            $notification_id = $this->sendNotification($data);

            $result = ['notification_id' => $notification_id];
            //DB::commit();
            $response = Response::json(array('code' => '200', 'message' => 'Notification sent successfully.', 'cause' => '', 'response' => $result));


        } catch (Exception $e) {
            Log::error('sendURLPushNotification', ['Exception' => $e->getMessage(), 'ExceptionDetail: ' => $e->getTraceAsString()]);
            //DB::commit();
        }
        return $response;
    }

    public function sendURLPushNotificationTest(Request $request_body)
    {
        try {
            $title = Input::get('title');
            $message = Input::get('message');
            $notification_type = 1;

            $array_data = array("GCM_DATA" => array("title" => $title, "message" => $message));
            $data = json_decode(json_encode($array_data));

            $GCM_DATA = $data->GCM_DATA;

            if (($response = (new VerificationController())->validateRequiredParameter(array('title', 'message'), $GCM_DATA)) != '')
                return $response;

            //return Input::get('title');

            $data = '{
                     "GCM_DATA": {
                       "title": "' . $title . '",
                       "message": "' . $message . '",
                       "messageType": "' . $notification_type . '"
                     }
                  }';
            //Log::info($data);

            $data = json_decode($data);
            //DB::beginTransaction();
            $notification_id = $this->sendNotification($data);

            $result = ['notification_id' => $notification_id];
            //DB::commit();
            $response = Response::json(array('code' => '200', 'message' => 'Notification sent successfully.', 'cause' => '', 'response' => $result));


        } catch (Exception $e) {
            Log::error('sendURLPushNotification', ['Exception' => $e->getMessage(), 'ExceptionDetail: ' => $e->getTraceAsString()]);
            //DB::commit();
        }
        return $response;
    }


    public function sendNotification($data)
    {
        try {
            //DB::beginTransaction();
            $GCM_DATA=$data->GCM_DATA;
            $all_devices = $this->getAllDevices($GCM_DATA->sub_category_id);
            if (is_null($all_devices))
                return Response::json(array('code' => '201', 'message' => 'No device registered with this application.', 'cause' => '', 'response' => json_decode("{}")));

            $notification_id = $this->getNotificationId($data->GCM_DATA, $all_devices['total_all']);
            if (is_null($notification_id))
                return Response::json(array('code' => '201', 'message' => 'ob_photolab is unable to get Notification Id.', 'cause' => '', 'response' => json_decode("{}")));

            if ($all_devices['total_android'] > 0) {

                //Log::info('sendNotification', ['total_android' => $all_devices['total_android']]);

                $android_gcm_server_key = Config::get('constant.GCM_SERVER_KEY');
                if (is_null($android_gcm_server_key))
                    return Response::json(array('code' => '201', 'message' => 'Please enter valid android GCM API key.', 'cause' => '', 'response' => json_decode("{}")));

                $android_notification_response = $this->sentAndroidNotification($data->GCM_DATA, $all_devices, $notification_id, $android_gcm_server_key);
                if ($android_notification_response !== 200)
                    return Response::json(array('code' => '201', 'message' => 'Error Code: ' . $android_notification_response, 'cause' => '', 'response' => json_decode("{}")));
            }

            if ($all_devices['total_ios'] > 0) {

                //Log::info('sendNotification', ['total_ios' => $all_devices['total_ios']]);

                $iOS_p12_path = $this->getIosP12($data->GCM_DATA);
                if (is_null($iOS_p12_path))
                    return Response::json(array('code' => '201', 'message' => 'Please upload valid Apple Push Certificate.', 'cause' => '', 'response' => json_decode("{}")));

                $ios_notification_status = $this->sentIosNotification($data->GCM_DATA, $all_devices, $notification_id);
                if ($ios_notification_status !== 200)
                    return Response::json(array('code' => '201', 'message' => 'Error Code: ' . $ios_notification_status, 'cause' => '', 'response' => json_decode("{}")));
            }

            //DB::commit();

        } catch (Exception $e) {
            Log::error('sendNotification', ['Exception' => $e->getMessage(), 'ExceptionDetail: ' => $e->getTraceAsString()]);
            //DB::rollBack();
        }
        return $notification_id;
    }

    // get all filtered device
    public function getFilteredDevices($filter)
    {
        try {
            $platform = $filter->platform;

            if ($platform === "all") {

                $device['android'] = $this->getAndroidDevices($filter);
                $device['ios'] = $this->getIosDevices($filter);

                if (count($device['android']) == 0 && count($device['ios']) == 0)
                    return null;

                $device['total_android'] = count($device['android']);
                $device['total_ios'] = count($device['ios']);
                $device['total_all'] = $device['total_android'] + $device['total_ios'];

            } else if ($platform === "android") {

                $device['android'] = $this->getAndroidDevices();

                if (count($device['android']) == 0)
                    return null;

                $device['total_android'] = count($device['android']);
                $device['total_ios'] = 0;
                $device['total_all'] = $device['total_android'] + $device['total_ios'];

            } else if ($platform === "ios") {

                $device['ios'] = $this->getIosDevices($filter);

                if (count($device['ios']) == 0)
                    return null;

                $device['total_android'] = 0;
                $device['total_ios'] = count($device['ios']);
                $device['total_all'] = $device['total_android'] + $device['total_ios'];
            } else
                $device = null;

        } catch (Exception $e) {
            Log::error('getFilteredDevices', ['Exception' => $e->getMessage(), 'ExceptionDetail: ' => $e->getTraceAsString()]);
            //DB::rollBack();
        }
        return $device;
    }

    // get all all device
    public function getAllDevices($sub_category_id)
    {
        try {
            $device['android'] = $this->getAndroidDevices($sub_category_id);
            $device['ios'] = $this->getIosDevices($sub_category_id);
            if (count($device['android']) == 0 && count($device['ios']) == 0)
                return null;

            $device['total_android'] = count($device['android']);
            $device['total_ios'] = count($device['ios']);
            $device['total_all'] = $device['total_android'] + $device['total_ios'];

        } catch (Exception $e) {
            Log::error('getFilteredDevices', ['Exception' => $e->getMessage(), 'ExceptionDetail: ' => $e->getTraceAsString()]);
            //DB::rollBack();
        }
        return $device;
    }

    // get android devices
    public function getAndroidDevices($sub_category_id)
    {
        try {
            $result = DB::select('SELECT device_id,
                                         device_reg_id
                                      FROM device_master
                                      WHERE device_platform = "android" AND
                                      sub_category_id=? AND
                                            is_active = 1
                                      ORDER BY device_id DESC ',[$sub_category_id]);
            return $result;

        } catch (Exception $e) {
            Log::error('getFilteredDevices', ['Exception' => $e->getMessage(), 'ExceptionDetail: ' => $e->getTraceAsString()]);
            //DB::rollBack();
        }
    }

    // get ios devices
    public function getIosDevices($sub_category_id)
    {
        try {
            $result = DB::select('SELECT device_id,
                                         device_reg_id
                                      FROM device_master
                                      WHERE device_platform = "ios" AND
                                      sub_category_id=? AND
                                            is_active = 1
                                      ORDER BY device_id DESC ',[$sub_category_id]);
            return $result;

        } catch (Exception $e) {
            Log::error('getIosDevices', ['Exception' => $e->getMessage(), 'ExceptionDetail: ' => $e->getTraceAsString()]);
        }
    }

    // get notification id
    public function getNotificationId($data, $total_devices)
    {
        try {
            $ntf_title = $data->title;
            $ntf_message = $data->message;
            $sub_category_id = $data->sub_category_id;
            $ntf_url = isset($data->url) ? $data->url : '';
            $ntf_type = isset($data->messageType) ? $data->messageType : '';
            $ntf_filter = '';
            $ntf_total_device = $total_devices;

            //DB::beginTransaction();

            $notification_id = DB::table('sent_notification_master')->insertGetId(
                array(
                    'sub_category_id' => $sub_category_id,
                    'ntf_title' => $ntf_title,
                    'ntf_message' => $ntf_message,
                    'ntf_icon_path' => "",
                    'ntf_type' => $ntf_type,
                    'ntf_filter' => $ntf_filter,
                    'ntf_total_device' => $ntf_total_device,
                    'url' => $ntf_url,
                    'ntf_status' => '200',
                    'was_scheduled' => 0)
            );

            //DB::commit();

            $response = $notification_id;

        } catch (Exception $e) {
            $response = null;
            Log::error('getNotificationId', ['Exception' => $e->getMessage(), 'ExceptionDetail: ' => $e->getTraceAsString()]);
            //DB::rollBack();
        }
        return $response;
    }

    // sent android notification
    public function sentAndroidNotification($data, $filtered_devices, $notification_id, $android_gcm_server_key)
    {
        $android_devices = $filtered_devices["total_android"];
        $android_devices_chunk = $this->getAndroidDevicesChunk($filtered_devices["android"]);
        //Log::info($android_devices_chunk);

        for ($x = 0; $x < count($android_devices_chunk); $x++) {
            $android_notification_status = $this->sendMessageToGCM($data, $android_devices_chunk[$x], $notification_id, $android_gcm_server_key);
            if ($android_notification_status !== 200)
                break;
        }
        return $android_notification_status;
    }

    // get android devices chunk
    public function getAndroidDevicesChunk($filtered_devices)
    {
        try {
            $reg_id_array = array();

            foreach ($filtered_devices as $device) {
                array_push($reg_id_array, $device->device_reg_id);
            }
            $reg_id_chunk = array_chunk($reg_id_array, 1000, true);

        } catch (Exception $e) {
            Log::error('getAndroidDevicesChunk', ['Exception' => $e->getMessage(), 'ExceptionDetail: ' => $e->getTraceAsString()]);
            //DB::rollBack();
        }
        return $reg_id_chunk;
    }

    // send message to GCM
    public function sendMessageToGCM($data, $android_devices_chunk, $notification_id, $android_gcm_server_key)
    {
        try {
            $gcm_data["GCM_DATA"] = $data;
            $request_body["registration_ids"] = $android_devices_chunk;
            $request_body["data"] = $gcm_data;
            $data_string = json_encode($request_body);
            $devices_chunk_count = count($android_devices_chunk);
            $curl_opt_url = 'https://fcm.googleapis.com/fcm/send';

            // init the resource
            $ch = curl_init();

            // ... or an array of options
            curl_setopt_array($ch, array(
                CURLOPT_URL => $curl_opt_url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json',
                    'Authorization: key=' . $android_gcm_server_key),
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_POSTFIELDS => $data_string,
                CURLOPT_VERBOSE => 1,
                CURLOPT_HEADER => 1,
                CURLINFO_HEADER_OUT => 1
            ));
            $output = curl_exec($ch);

            $request_header = curl_getinfo($ch, CURLINFO_HEADER_OUT); // request headers
            $request_body = $data_string;
            $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
            $response_header = substr($output, 0, $header_size);
            $response_body = substr($output, $header_size);
            $response_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            /*$response_data = json_decode($response_body);
            $success = $response_data->success;
            $failure = $response_data->failure;*/
            curl_close($ch);

            if ($response_code === 200) {
                $this->addRequestLog($notification_id, $request_header, $request_body, $response_header, $response_body, $response_code);
                $request_body_json = json_decode($response_body);
                $this->addNotificationDetail($notification_id, 'android', $devices_chunk_count, $request_body_json->success, $request_body_json->failure, json_encode($request_body_json->canonical_ids));
            } else {
                $this->addRequestLog($notification_id, $request_header, $request_body, $response_header, $response_body, $response_code);
                $this->updateNotificationByID($notification_id, $response_code);
            }
        } catch (Exception $e) {
            Log::error('sendMessageToGCM', ['Exception' => $e->getMessage(), 'ExceptionDetail: ' => $e->getTraceAsString()]);
            //DB::rollBack();
        }
        return $response_code;
    }

    // add request log
    public function addRequestLog($notification_id, $request_header, $request_body, $response_header, $response_body, $response_code)
    {
        try {

            //DB::beginTransaction();

            DB::insert('INSERT INTO sent_notification_logs
                          (ntf_id,
                          request_header,
                          request_body,
                          response_header,
                          response_body,
                          response_header_code)
                          VALUES (?,?,?,?,?,?)',
                [$notification_id,
                    $request_header,
                    $request_body,
                    $response_header,
                    $response_body,
                    $response_code]);

            //DB::commit();

        } catch (Exception $e) {
            Log::error('addRequestLog', ['Exception' => $e->getMessage(), 'ExceptionDetail: ' => $e->getTraceAsString()]);
            //DB::rollBack();
        }
    }

    // add notification detail
    public function addNotificationDetail($notification_id, $platform, $devices_chunk_count, $success, $failure, $canonical_ids)
    {
        try {
            //DB::beginTransaction();

            DB::insert('INSERT INTO sent_notification_detail
                            (ntf_id,
                            device_platform,
                            ntf_sent,
                            ntf_success,
                            ntf_failure,
                            ntf_canonical)
                            VALUES (?,?,?,?,?,?)',
                [$notification_id,
                    $platform,
                    $devices_chunk_count,
                    $success,
                    $failure,
                    $canonical_ids]);

            //DB::commit();

        } catch (Exception $e) {
            Log::error('addNotificationDetail', ['Exception' => $e->getMessage(), 'ExceptionDetail: ' => $e->getTraceAsString()]);
            //DB::rollBack();
        }
    }

    // update notification status by id
    public function updateNotificationByID($notification_id, $response_code)
    {
        try {
            //DB::beginTransaction();

            DB::update('UPDATE sent_notification_master SET
                                  ntf_status = ?
                                  WHERE ntf_id = ?',
                [$response_code,
                    $notification_id]);

            //DB::commit();

        } catch (Exception $e) {
            Log::error('updateNotificationByID', ['Exception' => $e->getMessage(), 'ExceptionDetail: ' => $e->getTraceAsString()]);
            //DB::rollBack();
        }
    }

    // get ios p12
    public function getIosP12($data)
    {
        try {
            //local path
            //$ios_certificate_path = app_path() . '/certificates/aps_development'.$data->sub_category_id.'.pem';

            //live path
            $ios_certificate_path = app_path() . '/certificates/aps_production'.$data->sub_category_id.'.pem';//'Certificates.pem';
        } catch (Exception $e) {
            Log::error('getIosP12', ['Exception' => $e->getMessage(), 'ExceptionDetail: ' => $e->getTraceAsString()]);
            //DB::rollBack();
            return null;
        }
        return $ios_certificate_path;
    }

    // sent ios notification
    public function sentIosNotification($data, $filtered_devices, $notification_id)
    {

        $ios_devices = $filtered_devices["total_ios"];
        $ios_devices_chunk = $this->getIosDevicesChunk($filtered_devices["ios"]);

        for ($x = 0; $x < count($ios_devices_chunk); $x++) {
            $status = $this->sendMessageToIOS($data, $ios_devices_chunk[$x], $notification_id);
            if ($status !== 200)
                break;
        }
        return $status;
    }

    // get ios devices chunk
    public function getIosDevicesChunk($filtered_devices)
    {
        $reg_id_array = array();

        foreach ($filtered_devices as $device) {
            array_push($reg_id_array, $device->device_reg_id);
        }

        $reg_id_chunk = array_chunk($reg_id_array, 1000, true);
        return $reg_id_chunk;
    }

    // send message to ios
    public function sendMessageToIOS($data, $ios_devices_chunk, $notification_id)
    {
        $device_token = $ios_devices_chunk;
        //Log::info($device_token);
        try {
            // set time limit to zero in order to avoid timeout
            set_time_limit(0);

            // charset header for output
            header('content-type: text/html; charset: utf-8');

            // this is the pass phrase you defined when creating the key

            //for development purpose
            //$certFile = app_path() . '/certificates/aps_development.pem';
            //$certFile = app_path() . '/certificates/aps_development'.$data->sub_category_id.'.pem';


            //for production purpose
            //$certFile = app_path() . '/certificates/aps_production.pem';
            $certFile = app_path() . '/certificates/aps_production'.$data->sub_category_id.'.pem';

            $passphrase = 'Optimumbrew';


            // tr_to_utf function needed to fix the Turkish characters
            $message = $data->message;//"Qr-code message from push notification server. it is testing is messages. please ignore this message. SuiteScene message from push notification server.";
            $title = $data->title;//"Qr-code message from push notification server. it is testing is messages. please ignore this message. SuiteScene message from push notification server.";
            $category = isset($data->messageType) ? $data->messageType : null;
            $url = isset($data->url) ? $data->url : null;

            $deviceIds = (array)$device_token;

            /*if(isset($data->messageType))
            {
                // this is where you can customize your notification
                $payload = '{"aps":{ "title":"' . $title . '", "alert":"' . $message . '","sound":"default", "category":"'.$category.'", "url": "'.$url.'" }}';

            }else{
                // this is where you can customize your notification
                $payload = '{"aps":{ "title":"' . $title . '", "alert":"' . $message . '","sound":"default"}}';

            }*/

            // $deviceIds = (array)$device_token;
            //Log::info('deviceIds', ['deviceIds' => $deviceIds]);

            $device_token_with_badge_list = [];
            foreach ($deviceIds as $item1) {
                $count_data = DB::select('SELECT * FROM device_master WHERE  device_reg_id  = "' . $item1 . '"  ORDER BY update_time DESC LIMIT 1 ');
                //Log::info('before_push', ['messageType' => $count_data[0]->device_reg_id, 'Exception' => $count_data]);
                DB::beginTransaction();
                DB::update('UPDATE device_master
                               SET is_count  = ?
                               WHERE device_id = ?', [$count_data[0]->is_count + 1, $count_data[0]->device_id]);
                DB::commit();
                //$new_count_data = DB::select('SELECT * FROM device_master WHERE  device_reg_id  = "' . $item1 . '"  ORDER BY update_time DESC LIMIT 1 ');

                $token_with_badge = array(
                    'token' => $item1,
                    'badge' => $count_data[0]->is_count + 1
                );
                array_push($device_token_with_badge_list, $token_with_badge);

                DB::commit();

            }

            foreach ($device_token_with_badge_list as $device_token_with_badge) {

                Log::info('device_token_with_badge_list', ['Token' => $device_token_with_badge['token'], 'Badge' => $device_token_with_badge['badge']]);
            }

            if (isset($data->messageType)) {

                // this is where you can customize your notification
                // $payload = '{"aps":{ "title":"' . $title . '", "badge":' . 1 . ', "alert":"' . $message . '","sound":"default", "category":"' . $category . '", "url": "' . $url . '" }}';

                $payloadObj['aps'] = array(
                    'title' => $title,
                    'badge' => 0,
                    'alert' => $message,
                    'sound' => 'default',
                    'category' => $category,
                    'url' => $url
                );
                // $payload= json_encode($payloadObj);

            } else {
                // this is where you can customize your notification
                // $payload = '{"aps":{ "title":"' . $title . '", "badge":' . 1 . ', "alert":"' . $message . '","sound":"default"}}';
                $payloadObj['aps'] = array(
                    'title' => $title,
                    'badge' => 0,
                    'alert' => $message,
                    'sound' => 'default'
                );
                // $payload= json_encode($payloadObj);

            }
            $result = 'Start' . '<br />';

            // start to create connection
            $ctx = stream_context_create();
            //Old Working
            stream_context_set_option($ctx, 'ssl', 'local_cert', $certFile);
            //stream_context_set_option($ctx, 'ssl', 'local_cert', 'aps_production.pem');

            stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);

            // Open a connection to the APNS server
            $fp = stream_socket_client('ssl://gateway.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);
            //$fp = stream_socket_client('ssl://gateway.sandbox.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);
            stream_set_blocking($fp, 0);
            if (!$fp) {
                throw new Exception("Failed to connect: $err $errstr");
                // exit("Failed to connect: $err $errstr" . '<br />');
            } else {
                $apple_expiry = time() + (90 * 24 * 60 * 60); //Keep push alive (waiting for delivery) for 90 days

                // echo 'Apple service is online. ' . '<br />';
            }
            $apple_identifier_id = 0;
            foreach ($device_token_with_badge_list as $device_token_with_badge) {

                $payloadObj['aps']['badge'] = $device_token_with_badge['badge'];
                $payload = json_encode($payloadObj);

                // Build the binary notification
                // $msg = chr(0) . pack('n', 32) . pack('H*', str_replace(' ', '', $item)) . pack('n', strlen($payload)) . $payload;
                //Enhanced Notification
                $apple_identifier = $apple_identifier_id + 1;
                $msg = pack("C", 1) . pack("N", $apple_identifier) . pack("N", $apple_expiry) . pack("n", 32) . pack('H*', str_replace(' ', '', $device_token_with_badge['token'])) . pack("n", strlen($payload)) . $payload;

                // Send it to the server
                $isError = 'false';
                try {
                    $result = fwrite($fp, $msg, strlen($msg));
                    sleep(1); // Sleep for 1 Second
                    $isError = $this->checkAppleErrorResponse($fp);
                    if ($isError) {
                        //DeActivate Token
                        $this->deActivateUDID($device_token_with_badge['token']);
                        throw new Exception("Error while sending Notification");
                    }
                } catch (Exception $e) {
                    fclose($fp);
                    Log::error('ConnectionRestart', ['Exception' => $e->getMessage(), 'ExceptionDetail: ' => $e->getTraceAsString()]);
                    sleep(5); // Sleep for 5 Second
                    $fp = stream_socket_client('ssl://gateway.sandbox.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);
                    if (!$fp) {
                        throw new Exception("Failed to connect: $err $errstr");
                        // exit("Failed to connect: $err $errstr" . '<br />');
                    }
                }


                if (!$result) {
                    //  echo 'Undelivered message count: ' . $item . '<br />';
                    Log::info('sendMessageToIOS', ['result' => $result, 'Undelivered iOS Token: ' => $device_token_with_badge['token'], 'isError' => $isError]);

                } else {
                    //echo 'Delivered message count: ' . $item . '<br />';
                    Log::info('sendMessageToIOS', ['result' => $result, 'Delivered iOS Token: ' => $device_token_with_badge['token'], 'isError' => $isError]);
                }
            }
            if ($fp) {
                fclose($fp);
                //  echo 'The connection has been closed by the client' . '<br />';
            }


            // echo count($deviceIds) . ' devices have received notifications.<br />';
            $result_data = "Success";
            // set time limit back to a normal value
            set_time_limit(30);

        } catch (Exception $e) {
            Log::error('sendMessageToIOS', ['Exception' => $e->getMessage(), 'ExceptionDetail: ' => $e->getTraceAsString()]);
            ////DB::commit();
        }
        return $status = 200;
    }

//clear Badge data


    /**
     * @api {post} clearBadgeCountData clearBadgeCountData
     * @apiName clearBadgeCountData
     * @apiGroup User
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * { }
     * @apiSuccessExample Request-Body:
     * {
     * "device_reg_id":"e12f306fb34ca680aa07d02f44842717ceaf4b35176599a4967017af6b75e247"
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Badge count clear successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function clearBadgeCountData(Request $request_body)
    {
        try {
            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('device_reg_id'), $request)) != '')
                return $response;

            $device_reg_id = $request->device_reg_id;


            DB::beginTransaction();
            $result = DB::update('UPDATE device_master
                               SET is_count  = ?
                               WHERE device_reg_id = ?', [0, $device_reg_id]);
            DB::commit();

            $response = Response::json(array('code' => 200, 'message' => 'Badge count clear successfully.', 'cause' => '', 'data' => json_decode("{}")));

        } catch (Exception $e) {
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'clear badge count data.', 'cause' => '', 'data' => json_decode("{}")));
            Log::error('clearBadgeCountData', ['Exception' => $e->getMessage()]);
            DB::rollBack();
        }
        return $response;
    }


    //FUNCTION to check if there is an error response from Apple
//         Returns TRUE if there was and FALSE if there was not
    public function checkAppleErrorResponse($fp)
    {
        //byte1=always 8, byte2=StatusCode, bytes3,4,5,6=identifier(rowID). Should return nothing if OK.
        $apple_error_response = fread($fp, 6);
        //NOTE: Make sure you set stream_set_blocking($fp, 0) or else fread will pause your script and wait forever when there is no response to be sent.
        if ($apple_error_response) {
            //unpack the error response (first byte 'command" should always be 8)
            $error_response = unpack('Ccommand/Cstatus_code/Nidentifier', $apple_error_response);

            if ($error_response['status_code'] == '0') {
                $error_response['status_code'] = '0-No errors encountered';
            } else if ($error_response['status_code'] == '1') {
                $error_response['status_code'] = '1-Processing error';
            } else if ($error_response['status_code'] == '2') {
                $error_response['status_code'] = '2-Missing device token';
            } else if ($error_response['status_code'] == '3') {
                $error_response['status_code'] = '3-Missing topic';
            } else if ($error_response['status_code'] == '4') {
                $error_response['status_code'] = '4-Missing payload';
            } else if ($error_response['status_code'] == '5') {
                $error_response['status_code'] = '5-Invalid token size';
            } else if ($error_response['status_code'] == '6') {
                $error_response['status_code'] = '6-Invalid topic size';
            } else if ($error_response['status_code'] == '7') {
                $error_response['status_code'] = '7-Invalid payload size';
            } else if ($error_response['status_code'] == '8') {
                $error_response['status_code'] = '8-Invalid token';
            } else if ($error_response['status_code'] == '255') {
                $error_response['status_code'] = '255-None (unknown)';
            } else {
                $error_response['status_code'] = $error_response['status_code'] . '-Not listed';
            }

            Log::error("checkAppleErrorResponse", ['Response' => $error_response, 'Response Command' => $error_response['command'], 'Identifier' => $error_response['identifier'], 'Status' => $error_response['status_code']]);;
            return true;
        }
        return false;
    }


    public function deActivateUDID($device_reg_id)
    {
        Log::error('deActivateUDID', ['Log' => "Debug 1:" . $device_reg_id]);
        try {

            //DB::beginTransaction();
            //DB::delete('DELETE FROM device_master WHERE device_reg_id = ?', [$device_reg_id]);
            //DB::delete('DELETE FROM device_master WHERE device_reg_id = ?', [$device_reg_id]);
            DB::update('UPDATE device_master SET
                                  is_active = ?
                                  WHERE device_reg_id = ?',
                [0,
                    $device_reg_id]);
            //DB::commit();

        } catch (Exception $e) {
            Log::error('deActivateUDID', ['Exception' => $e->getMessage()]);
            //DB::rollBack();
        }

    }

    function sendAPNSNotificationToAll($app, $apns_reg_id, $msg)
    {
        $device_token = $apns_reg_id;
        try {
            /**
             * iOS APNS Notification
             */
            // set time limit to zero in order to avoid timeout
            set_time_limit(0);

            // charset header for output
            header('content-type: text/html; charset: utf-8');

            // this is the pass phrase you defined when creating the key
            $passphrase = 'boutiquemaster';

            // tr_to_utf function needed to fix the Turkish characters
            $message = $msg;

            // load your device ids to an array
            $deviceIds = array(
                $device_token
            );

            // this is where you can customize your notification
            $payload = '{"aps":{ "title":"Ob Photolab", "alert":"' . $message . '","sound":"default"}}';

            $result = 'Start' . '<br />';

            ////////////////////////////////////////////////////////////////////////////////
            // start to create connection
            $ctx = stream_context_create();
            // stream_context_set_option($ctx, 'ssl', 'local_cert','../certi_bucket/ck.pem');
            stream_context_set_option($ctx, 'ssl', 'local_cert', 'ios_apns_master_dist.pem');
            stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);

            // echo count($deviceIds) . ' devices will receive notifications.<br />';

            foreach ($deviceIds as $item) {
                // wait for some time
                sleep(1);

                // Open a connection to the APNS server
                $fp = stream_socket_client('ssl://gateway.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);

                if (!$fp) {
                    throw new Exception("Failed to connect: $err $errstr");
                    // exit("Failed to connect: $err $errstr" . '<br />');
                } else {
                    // echo 'Apple service is online. ' . '<br />';
                }

                // Build the binary notification
                $msg = chr(0) . pack('n', 32) . pack('H*', str_replace(' ', '', $item)) . pack('n', strlen($payload)) . $payload;
                //$msg = chr(0) . pack('n', 32) . pack('H*',$item) . pack('n', strlen($payload)) . $payload;

                // Send it to the server
                $result = fwrite($fp, $msg, strlen($msg));

                if (!$result) {
                    //  echo 'Undelivered message count: ' . $item . '<br />';
                } else {
                    //echo 'Delivered message count: ' . $item . '<br />';
                }

                if ($fp) {
                    fclose($fp);
                    //  echo 'The connection has been closed by the client' . '<br />';
                }
            }

            // echo count($deviceIds) . ' devices have received notifications.<br />';
            $result_data = "Success";
            // set time limit back to a normal value
            set_time_limit(30);

            //   echo "Exits";
        } catch (Exception $e) {
            return false;
        }
    }
}
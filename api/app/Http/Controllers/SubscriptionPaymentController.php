<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\SuccessMail;
use JWTAuth;
use JWTFactory;
use Response;
use DB;
use Config;
use Exception;
use Log;
use Auth;
use ReceiptValidator\iTunes\Validator as iTunesValidator;

class SubscriptionPaymentController extends Controller
{
    /**
     * @api {post} appPurchasePayment   appPurchasePayment
     * @apiName appPurchasePayment
     * @apiGroup Payment Subscription
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * "order_info": {
     * "sub_category_id": 1,
     * "auto_renewing": "false",
     * "order_id": "Null1494322134113",
     * "package_name": "com.optimumbrewlab.bgchanger",
     * "product_id": "com.optimumbrewlab.bgchanger_remove_ads",
     * "purchase_state": "0",
     * "purchase_time": "1494248457019",
     * "purchase_token": "fflngnjoghggeahffeigjcba.AO-J1OwSyrfoOpvnkHeucfplRQno1CKJ40McIPpqioJBjEisg1B8g4bVJgEkeOjZ6-EUMN-M4_r1kbFIkmcXqRuSGRZ3awoOInx0_OMCgecwXLtCBUy8BnMSYuLJhRNg3qZ8d7mVwGaICXFmPwSC9m_4ScsxSHBP0Outfi0fVWU8R7UOZia7flM",
     * "tot_order_amount": 2.99
     * },
     * "device_info": {
     * "sub_category_id": 1,
     * "device_application_version": "2.0.2",
     * "device_carrier": "IND AirTel",
     * "device_country_code": "in",
     * "device_default_time_zone": "Asia/Calcutta",
     * "device_language": "en_us",
     * "device_latitude": "",
     * "device_library_version": "2",
     * "device_local_code": "NA",
     * "device_longitude": "",
     * "device_model_name": "Micromax AQ4501",
     * "device_os_version": "6.0.1",
     * "device_platform": "android",
     * "device_reg_id": "eqZf82uRnws:APA91bHl4Mt0y1gTgFXuR63COt-IalksVGHy8Pb9y8JyluqQhdJKUJeOWBINd8fKRBmjnTV47hclfjiRN330vSe1E2l8GJ43O91BqpELhFUWnrjUICmb63XGNwwJXgXdwG8isMdUf3eC",
     * "device_registration_date": "2017-05-09T14:58:54 +0530",
     * "device_resolution": "480x782",
     * "device_type": "phone",
     * "device_udid": "ff505a7a500c931a",
     * "device_vendor_name": "Micromax",
     * "project_package_name": "com.optimumbrewlab.bgchanger"
     * }
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Payment was successful.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function appPurchasePayment(Request $request_body)
    {
        try {
            $request = json_decode($request_body->getContent());
            Log::info("request", [$request]);

            if (($response = (new VerificationController())->validateRequiredParameter(array('order_info'), $request)) != '')
                return $response;
            $order_info = $request->order_info;

            if (($response = (new VerificationController())->validateRequiredParameter(array('sub_category_id','order_id', 'tot_order_amount', 'package_name', 'product_id', 'purchase_time', 'purchase_state'), $order_info)) != '')
                return $response;

            if (($response = (new VerificationController())->validateRequiredParameter(array('device_info'), $request)) != '')
                return $response;

            $device_info = $request->device_info;
            if (($response = (new VerificationController())->validateRequiredParameter(array('sub_category_id','device_platform', 'device_udid'), $device_info)) != '')
                return $response;

            //Device Information
            $sub_category_id = $device_info->sub_category_id;
            $device_udid = $device_info->device_udid;
            $device_reg_id = isset($device_info->device_reg_id) ? $device_info->device_reg_id : '';
            $device_platform = isset($device_info->device_platform) ? $device_info->device_platform : '';
            $device_model_name = 'NA';//isset($device_info->device_model_name) ? $device_info->device_model_name : '';
            $device_vendor_name = 'NA';//isset($device_info->device_vendor_name) ? $device_info->device_vendor_name : '';
            $device_os_version = isset($device_info->device_os_version) ? $device_info->device_os_version : '';
            $device_resolution = isset($device_info->device_resolution) ? $device_info->device_resolution : '';
            $device_carrier = isset($device_info->device_carrier) ? $device_info->device_carrier : '';
            $device_country_code = isset($device_info->device_country_code) ? $device_info->device_country_code : '';
            $device_language = isset($device_info->device_language) ? $device_info->device_language : '';
            $device_local_code = isset($device_info->device_local_code) ? $device_info->device_local_code : '';
            $device_default_time_zone = isset($device_info->device_default_time_zone) ? $device_info->device_default_time_zone : '';
            $device_application_version = isset($device_info->device_application_version) ? $device_info->device_application_version : '';
            $device_type = isset($device_info->device_type) ? $device_info->device_type : '';
            $device_registration_date = isset($device_info->device_registration_date) ? $device_info->device_registration_date : '';


            //Mandatory field
            $sub_category_id = $order_info->sub_category_id;
            $order_id = $order_info->order_id;
            $package_name = $order_info->package_name;
            $product_id = $order_info->product_id;
            $purchase_time = $order_info->purchase_time;
            $purchase_state = $order_info->purchase_state;
            $purchase_token = isset($order_info->purchase_token) ? $order_info->purchase_token : '';
            $auto_renewing = isset($order_info->auto_renewing) ? $order_info->auto_renewing : '';
            $tot_order_amount = $order_info->tot_order_amount;
            $currency_code = "USD";
            $device_platform = $device_info->device_platform;
            Log::info('order_info', [$order_info]);

            DB::beginTransaction();


            DB::insert('INSERT INTO order_logger
                            (request_data,attribute1)
                            values (?,?)',
                [json_encode($request), "Insert from API: addSubscriptionPayment"]);

            (new LoginController())->addNewDeviceToUser($sub_category_id,$device_reg_id, $device_platform, $device_model_name, $device_vendor_name, $device_os_version, $device_udid, $device_resolution, $device_carrier, $device_country_code, $device_language, $device_local_code, $device_default_time_zone, $device_application_version, $device_type, $device_registration_date);


            $is_exist_order = DB::table('order_master')->where(['order_number' => $order_id,'sub_category_id' => $sub_category_id])->exists();

            if ($is_exist_order) {
                //Restore Logic
                Log::info("order no. is exist.");
                $is_exist_device = DB::select('select * from restore_device WHERE device_udid = ? and sub_category_id = ?', [$device_udid, $sub_category_id]);

                if ($is_exist_device) {
                    //total_increment
                    Log::info("device is exist.");
                    DB::update('update restore_device set restore= restore +  1 WHERE device_udid = ? AND order_number = ? AND sub_category_id = ?', [$device_udid, $order_id, $sub_category_id]);

                } else {
                    //new device
                    Log::info("device is new.");
                    DB::insert('insert into restore_device (sub_category_id,order_number,device_udid,create_time) VALUES (?,?,?,?)', [$sub_category_id, $order_id, $device_udid,date('Y-m-d H:i:s')]);
                }
                DB::commit();
                $response = Response::json(array('code' => 200, 'message' => 'Your payment has been received.', 'cause' => '', 'data' => json_decode("{}")));

            } else {
                //New Order_number
                Log::info("new Order.");
                $order_table_id = DB::table('order_master')->insertGetId(
                    array(//'user_id' => $user_id,
                        'sub_category_id' => $sub_category_id,
                        'order_number' => $order_id,
                        'package_name' => $package_name,
                        'tot_order_amount' => $tot_order_amount,
                        'currency_code' => $currency_code,
                        'product_id' => $product_id,
                        'purchase_time' => $purchase_time,
                        'order_status' => $purchase_state,
                        'neft_transaction_id' => $purchase_token,
                        'auto_renewing' => $auto_renewing,
                        'device_platform' => $device_platform,
                        'create_time'=>date('Y-m-d H:i:s'),
                    )
                );
                Log::info('order_table_id', ['Exception' => $order_table_id]);


                DB::insert('insert into restore_device (sub_category_id,order_number,device_udid,create_time) VALUES (?,?,?,?)', [$sub_category_id,$order_id, $device_udid, date('Y-m-d H:i:s')]);
                DB::commit();
                $response = Response::json(array('code' => 200, 'message' => 'Payment was successful.', 'cause' => '', 'data' => json_decode("{}")));
            }

        } catch (Exception $e) {
            $email_id = Config::get('constant.ADMIN_EMAIL_ID');
            $subject = 'cut-paste photo editor: Failed Payment';
            $title = 'appPurchasePayment Error';
            $message_body = $e->getMessage();
            //Mail::to($email_id)->send(new SuccessMail($title,$subject, $message_body));
            Mail::to($email_id)->bcc('pmpatel1415160@gmail.com')->send(new SuccessMail($title,$subject, $message_body));

            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'add app Purchase Payment.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            Log::error('appPurchasePayment', ['Exception' => $e->getMessage(),'TraceAsString'=>$e->getTraceAsString()]);
            DB::rollBack();
        }

        return $response;
    }

    /**
     * @api {post} appPurchasePaymentForIOS   appPurchasePaymentForIOS
     * @apiName appPurchasePaymentForIOS
     * @apiGroup Payment Subscription
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "order_info": {
     * "sub_category_id": 1,
     * "purchase_token": "",
     * "auto_renewing": "false",
     * "order_id": "1000000297266758",
     * "product_id": "qrcodescanner_pro",
     * "tot_order_amount": 1.99,
     * "package_name": "com.optimumbrewlab.bgchanger-",
     * "purchase_time": "1494339022669",
     * "purchase_state": "1",
     * "receipt_base64_data": "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"
     * },
     * "device_info": {
     * "sub_category_id": 1,
     * "device_udid": "8DC1456E-3EBC-43FA-985A-DDAC492418FD",
     * "device_reg_id": "911F5C2F54F94DC6037C1A249B0BC98BFF5B17DF2A1C8CE5A546201A5B8DCF6B",
     * "device_local_code": "en-US",
     * "device_platform": "ios",
     * "device_resolution": "320.0568.0",
     * "device_library_version": "",
     * "device_carrier": "",
     * "device_os_version": "10.0.2",
     * "device_default_time_zone": "Asia/Kolkata",
     * "device_type": "phone",
     * "project_package_name": "com.optimumbrewlab.bgchanger-",
     * "device_vendor_name": "Apple",
     * "device_model_name": "iPhone",
     * "device_longitude": "0.0",
     * "device_latitude": "0.0",
     * "device_country_code": "US",
     * "device_application_version": "1",
     * "device_language": "en-US",
     * "device_registration_date": "2017-05-09 14:10:22 +0000"
     * }
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Payment was successful.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function appPurchasePaymentForIOS(Request $request_body)
    {
        try {
            $request = json_decode($request_body->getContent());
            Log::info("request", [$request]);

            if (($response = (new VerificationController())->validateRequiredParameter(array('order_info'), $request)) != '')
                return $response;

            $order_info = $request->order_info;

            if (($response = (new VerificationController())->validateRequiredParameter(array('sub_category_id','order_id', 'tot_order_amount', 'package_name', 'product_id', 'purchase_time', 'purchase_state', 'receipt_base64_data'), $order_info)) != '')
                return $response;

            if (($response = (new VerificationController())->validateRequiredParameter(array('device_info'), $request)) != '')
                return $response;

            $device_info = $request->device_info;

            if (($response = (new VerificationController())->validateRequiredParameter(array('sub_category_id','device_platform', 'device_udid'), $device_info)) != '')
                return $response;

            //Device info
            $sub_category_id=$device_info->sub_category_id;
            $device_udid = $device_info->device_udid;
            $device_reg_id = isset($device_info->device_reg_id) ? $device_info->device_reg_id : '';
            //$device_platform = isset($device_info->device_platform) ? $device_info->device_platform : '';
            $device_model_name = 'NA';//isset($device_info->device_model_name) ? $device_info->device_model_name : '';
            $device_vendor_name = 'NA';//isset($device_info->device_vendor_name) ? $device_info->device_vendor_name : '';
            $device_os_version = isset($device_info->device_os_version) ? $device_info->device_os_version : '';
            $device_resolution = isset($device_info->device_resolution) ? $device_info->device_resolution : '';
            $device_carrier = isset($device_info->device_carrier) ? $device_info->device_carrier : '';
            $device_country_code = isset($device_info->device_country_code) ? $device_info->device_country_code : '';
            $device_language = isset($device_info->device_language) ? $device_info->device_language : '';
            $device_local_code = isset($device_info->device_local_code) ? $device_info->device_local_code : '';
            $device_default_time_zone = isset($device_info->device_default_time_zone) ? $device_info->device_default_time_zone : '';
            $device_application_version = isset($device_info->device_application_version) ? $device_info->device_application_version : '';
            $device_type = isset($device_info->device_type) ? $device_info->device_type : '';
            $device_registration_date = isset($device_info->device_registration_date) ? $device_info->device_registration_date : '';


            //Mandatory field
            $sub_category_id=$order_info->sub_category_id;
            $order_id = $order_info->order_id;
            $package_name = $order_info->package_name;
            $product_id = $order_info->product_id;
            $purchase_time = $order_info->purchase_time;
            $purchase_state = $order_info->purchase_state;
            $purchase_token = isset($order_info->purchase_token) ? $order_info->purchase_token : '';
            $auto_renewing = $order_info->auto_renewing;
            $tot_order_amount = $order_info->tot_order_amount;
            $currency_code = "USD";
            $device_platform = $device_info->device_platform;
            $receipt_base64_data = isset($order_info->receipt_base64_data) ? $order_info->receipt_base64_data : null;
            //Log::debug('addSubscriptionPaymentForIOS:', ['Receipt Data:' => $receipt_base64_data]);

            Log::info('addSubscriptionPaymentForIOS', ['order_info' => $order_info]);
            //Log the request
            DB::beginTransaction();

            DB::insert('INSERT INTO order_logger
                            (request_data,attribute1)
                            values (?,?)',
                [json_encode($request), "Insert from API: addSubscriptionPaymentForIOS"]);
            DB::commit();

            (new LoginController())->addNewDeviceToUser($sub_category_id,$device_reg_id, $device_platform, $device_model_name, $device_vendor_name, $device_os_version, $device_udid, $device_resolution, $device_carrier, $device_country_code, $device_language, $device_local_code, $device_default_time_zone, $device_application_version, $device_type, $device_registration_date);

            // Get Latest Transaction ID from the receipt
            $latestReceiptData = $this->getLatestReceiptData($receipt_base64_data);

            if (is_null($latestReceiptData)) {
                Log::error("Latest Receipt Data is null.");
                Log::error('addSubscriptionPaymentForIOS', ['Sorry, ob photoab is unable to process your request. Please try again later.']);
                return $response = Response::json(array('code' => 201, 'message' => 'Sorry, ob photoab is unable to process your request. Please try again later.', 'cause' => '', 'data' => json_decode("{}")));
            } else if ((isset($latestReceiptData['product_id']) && (strcmp($latestReceiptData['product_id'], 'auto_legal_entity_yearly_sub') == 0))) {
                Log::error('addSubscriptionPaymentForIOS', ['Invalid Product Id: Receipt Data: ' => $latestReceiptData]);
                return $response = Response::json(array('code' => 201, 'message' => 'Sorry, ob photoab is unable to process your request. Please try again later.', 'cause' => '', 'data' => json_decode("{}")));
            } else {
                $transaction_id = $latestReceiptData['transaction_id'];
                Log::info('addSubscriptionPaymentForIOS', ['transaction_id' => $transaction_id]);
            }

            $activation_time = date("Y-m-d H:i:s", ($latestReceiptData['purchase_date_ms'] / 1000));
            $expiration_time = date('Y-m-d H:i:s', strtotime($activation_time . " + 1 year"));

            Log::debug('addSubscriptionPaymentForIOS', ['1 expiration_time' => $expiration_time, 'activation_time' => $activation_time,]);

            // $is_exist_order =  DB::table('order_master')->where('user_id', $user_id)->where('order_number',$transaction_id)->exists();

            $is_orderId_exist = DB::select('SELECT * FROM order_master WHERE order_number = ? AND sub_category_id = ? ORDER BY id DESC LIMIT 1', [$order_id,$sub_category_id]);
            if ($is_orderId_exist) {
                Log::info("Order id is exist.", [$order_id]);
                //Restore Logic
                $is_exist_device = DB::select('select * from restore_device WHERE device_udid = ? AND sub_category_id = ?', [$device_udid,$sub_category_id]);
                if ($is_exist_device) {
                    Log::info("device Udid id is exist.", [$is_exist_device]);
                    DB::beginTransaction();
                    //total_increment
                    DB::update('update restore_device set restore= restore + 1 WHERE device_udid = ? and order_number = ? AND sub_category_id = ?', [$device_udid, $order_id, $sub_category_id]);
                } else {
                    Log::info("device Udid id is not exist.", [$is_exist_device]);
                    //new device
                    DB::insert('insert into restore_device (sub_category_id,order_number,device_udid,create_time) VALUES (?,?,?,?)', [$sub_category_id, $order_id, $device_udid, date('Y-m-d H:i:s')]);
                }
                DB::commit();
                $response = Response::json(array('code' => 200, 'message' => 'Your payment has been received.', 'cause' => '', 'data' => json_decode("{}")));

            } else {
                Log::info('tranId not exist');
                $order_table_id = DB::table('order_master')->insertGetId(
                    array(//'user_id' => $user_id,
                        'sub_category_id' => $sub_category_id,
                        'order_number' => $order_id,
                        'package_name' => $package_name,
                        'tot_order_amount' => $tot_order_amount,
                        'currency_code' => $currency_code,
                        'product_id' => $product_id,
                        'purchase_time' => $purchase_time,
                        'order_status' => $purchase_state,
                        'neft_transaction_id' => $purchase_token,
                        'auto_renewing' => $auto_renewing,
                        'device_platform' => $device_platform,
                        'create_time'=>date('Y-m-d H:i:s'),
                    )
                );
                Log::info('addSubscriptionPaymentForIOS', ['order_table_id' => $order_table_id]);

                DB::insert('insert into restore_device (sub_category_id,order_number,device_udid,create_time) VALUES (?,?,?,?)', [$sub_category_id, $order_id, $device_udid, date('Y-m-d H:i:s')]);
                DB::commit();
                $response = Response::json(array('code' => 200, 'message' => 'Payment was successful.', 'cause' => '', 'data' => json_decode("{}")));
            }


        } catch (Exception $e) {
            $email_id = Config::get('constant.ADMIN_EMAIL_ID');
            $subject = 'ob photolab : Failed Payment';
            $title = 'appPurchasePaymentForIos Error';
            $message_body = $e->getMessage();
            //Mail::to($email_id)->send(new SuccessMail($title,$subject, $message_body));
            Mail::to($email_id)->bcc('pmpatel1415160@gmail.com')->send(new SuccessMail($title,$subject, $message_body));

            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'add app Purchase Payment.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            Log::error('appPurchasePayment', ['Exception' => $e->getMessage(),'TraceAsString'=>$e->getTraceAsString()]);
            DB::rollBack();

        }
        return $response;
    }


    public function restoreSubscriptionPayment(Request $request_body)
    {
        try {
            $request = json_decode($request_body->getContent());
            Log::debug('Request_body:' . $request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('is_session_start'), $request)) != '')
                return $response;
            $order_info = $request->order_info;
            if (($response = (new VerificationController())->validateRequiredParameter(array('user_id', 'order_id', 'tot_order_amount', 'package_name', 'product_id', 'purchase_time', 'purchase_state', 'auto_renewing'), $order_info)) != '')
                return $response;

            $device_info = $request->device_info;
            if (($response = (new VerificationController())->validateRequiredParameter(array('device_platform', 'device_udid'), $device_info)) != '')
                return $response;

            //Mandatory field
            $is_session_start = $request->is_session_start;
            $user_id = $order_info->user_id;
            // $order_id = $order_info->order_id;
            $package_name = $order_info->package_name;
            $product_id = $order_info->product_id;
            $purchase_time = $order_info->purchase_time;
            $purchase_state = $order_info->purchase_state;
            $purchase_token = isset($order_info->purchase_token) ? $order_info->purchase_token : '';
            $auto_renewing = $order_info->auto_renewing;
            $tot_order_amount = $order_info->tot_order_amount;
            $token = $order_info->token;
            $currency_code = "USD";
            $device_platform = $device_info->device_platform;
            $receipt_base64_data = isset($order_info->receipt_base64_data) ? $order_info->receipt_base64_data : null;
            Log::debug('Receipt_Data:', ['Receipt Data:' => $receipt_base64_data]);

            Log::info('is_session_start', ['Exception' => $is_session_start]);
            Log::info('order_info', ['Exception' => $order_info]);

            //$activation_time = date(Config::get('constant.DATE_FORMAT'));
            //$expiration_time  = date('Y-m-d H:i:s', strtotime("+1 year"));

            //Log the request
            DB::beginTransaction();

            DB::insert('INSERT INTO order_logger
                            (request_data,attribute1)
                            values (?,?)',
                [json_encode($request), "Insert from API: restoreSubscriptionPayment"]);
            DB::commit();

            // Get Latest Transaction ID from the receipt
            $latestReceiptData = $this->getLatestReceiptData($receipt_base64_data);

            if (is_null($latestReceiptData)) {
                Log::error('restoreSubscriptionPayment', ['Sorry, ob photoab is unable to process your request. Please try again later.']);
                return $response = Response::json(array('code' => 201, 'message' => 'Sorry, ob photoab is unable to process your request. Please try again later.', 'cause' => '', 'data' => json_decode("{}")));
            } else {
                $transaction_id = $latestReceiptData['transaction_id'];
                Log::info('restoreSubscriptionPayment', ['latestReceiptData' => $latestReceiptData]);
                Log::info('restoreSubscriptionPayment', ['transaction_id' => $transaction_id]);
            }

            //Check if Transaction id already exist ?
            $is_tranId_exist = DB::select('SELECT * FROM order_master WHERE order_number = ?  ORDER BY id DESC LIMIT 1', [$transaction_id]);
            if (count($is_tranId_exist) > 0) {

                //Check if user id and transaction id is same ?
                if (strcmp($is_tranId_exist[0]->user_id, $user_id) == 0) {

                    // User have already registered the payment.
                    //Check if subscription exist ?
                    $subscriptions_result = DB::select('SELECT expiration_time FROM subscriptions WHERE user_id = ?', [$user_id]);
                    if (count($subscriptions_result) > 0) {

                        // Subscription already exist.

                        $current_expiration_date = date_create($subscriptions_result[0]->expiration_time);
                        $current_expiration_date_ms = strtotime($subscriptions_result[0]->expiration_time) * 1000;

                        Log::debug('restoreSubscriptionPayment', ['Current_expiration_time_ms' => $current_expiration_date_ms]);

                        $new_expiration_date_ms = ($latestReceiptData['expires_date_ms']);
                        Log::debug('restoreSubscriptionPayment', ['new_expiration_time_ms' => $new_expiration_date_ms]);

                        if ($new_expiration_date_ms > $current_expiration_date_ms) {
                            Log::debug('restoreSubscriptionPayment', ['new_expiration_time_ms' => "Update Expiration time."]);
                            $new_expiration_date = date("Y-m-d H:i:s", $new_expiration_date_ms / 1000);

                            DB::beginTransaction();

                            DB::update('UPDATE subscriptions
                                SET expiration_time = ?,
                                    attribute1 = ?
                                WHERE user_id = ?', [$new_expiration_date, 'Error : Payment By Restore : Code 1', $user_id]);

                            DB::commit();

                        } else {
                            Log::error('restoreSubscriptionPayment', ['New expiration time is less then or equal to current expiration time.Error code 1 =>' . date("Y-m-d H:i:s", ($new_expiration_date_ms / 1000)) . ' > ' . date("Y-m-d H:i:s", ($current_expiration_date_ms / 1000))]);
                        }
                        $current_milliseconds = round(microtime(true) * 1000);
                        Log::debug('restoreSubscriptionPayment', ["current_milliseconds :" . $current_milliseconds . "  new_expiration_date_ms :" . $new_expiration_date_ms]);
                        if ($new_expiration_date_ms <= $current_milliseconds) {
                            Log::error('restoreSubscriptionPayment', ['Expiration time is less then current time. Error code 2']);
                            return $response = Response::json(array('code' => 201, 'message' => 'Your subscription has been expired. Please renew your subscription.', 'cause' => 'Expiration time is less then current time.', 'data' => json_decode("{}")));
                        }
                    } else {
                        $activation_time = date("Y-m-d H:i:s", ($latestReceiptData['original_purchase_date_ms'] / 1000));
                        $new_expiration_date = date("Y-m-d H:i:s", ($latestReceiptData['expires_date_ms'] / 1000));

                        // User id doing first time subscription.
                        DB::beginTransaction();
                        DB::insert('INSERT INTO subscriptions
                               (order_id,user_id,transaction_id,activation_time,expiration_time,attribute1)
                               values (?,?,?,?,?,?)',
                            [$is_tranId_exist[0]->id, $user_id, $transaction_id, $activation_time, $new_expiration_date, 'Error : Payment By Restore : Code 2']);
                        DB::commit();
                    }


                } else {
                    Log::error('restoreSubscriptionPayment', ['This transaction id already exit for another user. Error code 3 Trans ID:' . $transaction_id]);
                    return $response = Response::json(array('code' => 201, 'message' => 'There are no items available to restore at this time. Please try again later.', 'cause' => 'This transaction id already exit for another user. Trans ID:' . $transaction_id, 'data' => json_decode("{}")));
                }

            } else {

                DB::beginTransaction();

                /*DB::insert('INSERT INTO order_logger
                            (request_data)
                            values (?)',
                    [json_encode($request)]);*/

                $order_table_id = DB::table('order_master')->insertGetId(
                    array('user_id' => $user_id,
                        'order_number' => $transaction_id,
                        'package_name' => $package_name,
                        'tot_order_amount' => $tot_order_amount,
                        'currency_code' => $currency_code,
                        'product_id' => $product_id,
                        'purchase_time' => $purchase_time,
                        'order_status' => $purchase_state,
                        'neft_transaction_id' => $purchase_token,
                        'auto_renewing' => $auto_renewing,
                        'device_platform' => $device_platform
                    )
                );
                Log::info('restoreSubscriptionPayment -> order_table_id', ['Exception' => $order_table_id]);
                //$is_exist = DB::table('subscriptions')->where('user_id', $user_id)->exists();
                $subscriptions_result = DB::select('SELECT * FROM subscriptions WHERE user_id = ?', [$user_id]);
                if (count($subscriptions_result) > 0) {
                    // User Already exist. so update the expiration time.

                    $current_expiration_date = date_create($subscriptions_result[0]->expiration_time);
                    $current_expiration_date_ms = strtotime($subscriptions_result[0]->expiration_time) * 1000;

                    Log::debug('restoreSubscriptionPayment', ['Current_expiration_time_ms' => $current_expiration_date_ms]);

                    $new_expiration_date_ms = ($latestReceiptData['expires_date_ms']);
                    Log::debug('restoreSubscriptionPayment', ['new_expiration_time_ms' => $new_expiration_date_ms]);

                    Log::debug('restoreSubscriptionPayment', ["new_expiration_date_ms:" . $new_expiration_date_ms . "  current_expiration_date_ms :" . $current_expiration_date_ms]);
                    if ($new_expiration_date_ms > $current_expiration_date_ms) {
                        Log::debug('restoreSubscriptionPayment', ['new_expiration_time_ms' => "Update Expiration time."]);
                        $new_expiration_date = date("Y-m-d H:i:s", $new_expiration_date_ms / 1000);

                        DB::update('UPDATE subscriptions
                                SET expiration_time = ?,
                                 attribute1 = ?
                                WHERE user_id = ?', [$new_expiration_date, 'Error : Payment By Restore : Code 3', $user_id]);


                    } else {
                        Log::error('restoreSubscriptionPayment', ['New expiration time is less then current expiration time. Error code 4']);
                    }

                    $current_milliseconds = round(microtime(true) * 1000);
                    Log::debug('restoreSubscriptionPayment', ["current_milliseconds :" . $current_milliseconds . "  new_expiration_date_ms :" . $new_expiration_date_ms]);
                    if ($new_expiration_date_ms <= $current_milliseconds) {
                        Log::error('restoreSubscriptionPayment', ['Expiration time is less then current time. Error code 5']);
                        return $response = Response::json(array('code' => 201, 'message' => 'Your subscription has been expired. Please renew your subscription.', 'cause' => 'Expiration time is less then current time.', 'data' => json_decode("{}")));
                    }
                } else {
                    // Transaction & Subscription not exist.
                    $activation_time = date("Y-m-d H:i:s", ($latestReceiptData['original_purchase_date_ms'] / 1000));
                    $new_expiration_date = date("Y-m-d H:i:s", ($latestReceiptData['expires_date_ms'] / 1000));

                    // User id doing first time subscription.

                    DB::insert('INSERT INTO subscriptions
                               (order_id,user_id,transaction_id,activation_time,expiration_time,attribute1)
                               values (?,?,?,?,?,?)',
                        [$order_table_id, $user_id, $transaction_id, $activation_time, $new_expiration_date, 'Error : Payment By Restore : Code 4']);

                }


            }
            DB::update('UPDATE user_master
                                SET is_active = ?
                                WHERE id = ?', [1, $user_id]);
            DB::commit();
            if ($is_session_start == 1) {
                $device_udid = $device_info->device_udid;
                $device_reg_id = isset($device_info->device_reg_id) ? $device_info->device_reg_id : '';
                $device_platform = isset($device_info->device_platform) ? $device_info->device_platform : '';
                $device_model_name = 'NA';//isset($device_info->device_model_name) ? $device_info->device_model_name : '';
                $device_vendor_name = 'NA';//isset($device_info->device_vendor_name) ? $device_info->device_vendor_name : '';
                $device_os_version = isset($device_info->device_os_version) ? $device_info->device_os_version : '';
                $device_resolution = isset($device_info->device_resolution) ? $device_info->device_resolution : '';
                $device_carrier = isset($device_info->device_carrier) ? $device_info->device_carrier : '';
                $device_country_code = isset($device_info->device_country_code) ? $device_info->device_country_code : '';
                $device_language = isset($device_info->device_language) ? $device_info->device_language : '';
                $device_local_code = isset($device_info->device_local_code) ? $device_info->device_local_code : '';
                $device_default_time_zone = isset($device_info->device_default_time_zone) ? $device_info->device_default_time_zone : '';
                $device_application_version = isset($device_info->device_application_version) ? $device_info->device_application_version : '';
                $device_type = isset($device_info->device_type) ? $device_info->device_type : '';
                $device_registration_date = isset($device_info->device_registration_date) ? $device_info->device_registration_date : '';

                (new LoginController())->createNewSession($user_id, $token, $device_udid);

                (new LoginController())->addNewDeviceToUser($user_id, $device_reg_id, $device_platform, $device_model_name, $device_vendor_name, $device_os_version, $device_udid, $device_resolution, $device_carrier, $device_country_code, $device_language, $device_local_code, $device_default_time_zone, $device_application_version, $device_type, $device_registration_date);
            }

            $user_profile = (new LoginController())->getUserInfoByUserId($user_id);

            $subscriptions_result1 = DB::select('SELECT expiration_time FROM subscriptions WHERE user_id = ?', [$user_id]);
            $user_profile->expiration_time = $subscriptions_result1[0]->expiration_time;
            Log::info('expiration_time', ['Exception' => $subscriptions_result1[0]->expiration_time]);
            $response = Response::json(array('code' => 200, 'message' => 'Payment was successful.', 'cause' => '', 'data' => ['token' => $token, 'user_profile' => $user_profile]));
            Log::info('data', ['response ' => $response]);
            // return   $response = Response::json(array('code' => 200, 'message' => 'Payment was successful.', 'cause' => '', 'data' =>json_decode("{}")));

        } catch (Exception $e) {
            $email_id = Config::get('constant.ADMIN_EMAIL_ID');
            $url = Config::get('constant.path');
            $subject = 'ob photoab: Failed Payment';
            $title = 'restoreSubscriptionPayment';
            $message_body = $e->getMessage();
            //Mail::to($email_id)->send(new SuccessMail($title,$subject, $message_body));
            Mail::to($email_id)->bcc('gabanibhavesh9@gmail.com')->send(new SuccessMail($title,$subject, $message_body, $url));

            Log::error('addSubscriptionPaymentForIOS', ['Exception' => $e->getMessage(), 'getTraceAsString' => $e->getTraceAsString()]);
            DB::rollBack();
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'add SubscriptionPayment.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));

        }
        return $response;
    }


    public function getLatestReceiptData($receiptBase64Data)
    {
        $latestReceiptData = array();
        //iTunesValidator::ENDPOINT_PRODUCTION
        $validator = new iTunesValidator(iTunesValidator::ENDPOINT_PRODUCTION);

        try {
            $response = $validator->setReceiptData($receiptBase64Data)->setSharedSecret("4d218de1c18845c1ac53b3e071311083")->validate();
            if ($response->isValid()) {
                $latestReceipts = $response->getPurchases();
                $receiptCount = count($latestReceipts);
                $latestReceiptDataTemp = $latestReceipts[$receiptCount - 1];

                $latestReceiptData = $latestReceiptDataTemp->getRawResponse();

                return $latestReceiptData;
            } else {
                Log::error('validateIOSReceipt', ['Receipt result code = ' => $response->getResultCode()]);
            }

            return null;
        } catch (Exception $e) {
            Log::error('validateIOSReceipt', ['Exception' => $e->getMessage()]);
        }

        return null;
    }

    public function validateIOSReceipt($receiptBase64Data)
    {
        $validator = new iTunesValidator(Config::get('constant.receipt_validator_endpoint'));

        try {
            $response = $validator->setReceiptData($receiptBase64Data)->setSharedSecret("4d218de1c18845c1ac53b3e071311083")->validate();
        } catch (Exception $e) {
            //echo 'got error = ' . $e->getMessage() . PHP_EOL;
            Log::error('validateIOSReceipt', ['Exception' => $e->getMessage()]);
        }

        echo $response;
    }

    public function testiOSIAP(Request $request_body)
    {
        $receiptBase64Data = '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';
        $receiptBase64Data = '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';
        $latestReceiptData = $this->getLatestReceiptData($receiptBase64Data);
        var_dump($latestReceiptData);

        if ((isset($latestReceiptData['product_id']) && (strcmp($latestReceiptData['product_id'], 'auto_legal_entity_yearly_sub') == 0))) {
            Log::error('addSubscriptionPaymentForIOS', ['Invalid Product Id: Receipt Data: ' => $latestReceiptData]);
            //return $response = Response::json(array('code' => 201, 'message' => 'Sorry, WOC is unable to process your request. Please try again later.', 'cause' => '', 'data' => json_decode("{}")));
            echo 'Sorry, ob photoab is unable to process your request. Please try again later.';
        } else {
            echo 'success';
        }
    }


}

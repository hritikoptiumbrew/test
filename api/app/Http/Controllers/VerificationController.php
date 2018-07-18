<?php

//////////////////////////////////////////////////////////////////////////////
//                   OptimumBrew Technology Pvt. Ltd.                       //
//                                                                          //
// Title:            suitescene                                             //
// File:             VerificationController.php                             //
// Since:            11-August-2016                                         //
//                                                                          //
// Author:           Dipali Dhanani                                         //
// Email:            dipali.dhanani@optimumbrew.com                         //
//                                                                          //
//////////////////////////////////////////////////////////////////////////////

namespace App\Http\Controllers;

use App\Http\Requests;
use Response;
use DB;
use Exception;
use Log;
use Config;

class VerificationController extends Controller
{
    // validate required and empty field
    public function validateRequiredParameter($required_fields,$request_params)
    {
        $error = false;
        $error_fields = '';

        foreach ($required_fields as $key => $value) {
            if(isset($request_params->$value)) {
                if(!is_object($request_params->$value)){
                    if(strlen($request_params->$value) == 0){
                        $error = true;
                        $error_fields .= ' ' . $value . ',';
                    }
                }
            }else{
                $error = true;
                $error_fields .= ' ' . $value . ',';
            }
        }

        if ($error) {
            // Required field(s) are missing or empty
            $error_fields = substr($error_fields, 0, -1);
            $message = 'Required field(s)' . $error_fields . ' is missing or empty.';
            $response = Response::json(array('code' => '201', 'message' => $message, 'cause' => '', 'response' => json_decode("{}")));
        } else
            $response = '';

        return $response;
    }

    public function validateRequiredArrayParameter($required_fields,$request_params)
    {
        $error = false;
        $error_fields = '';

        foreach ($required_fields as $key => $value) {
            if(isset($request_params->$value)) {
                if(!is_array($request_params->$value)){
                    $error = true;
                    $error_fields .= ' ' . $value . ',';
                }else{
                    if(count($request_params->$value) == 0){
                        $error = true;
                        $error_fields .= ' ' . $value . ',';
                    }
                }
            }else{
                $error = true;
                $error_fields .= ' ' . $value . ',';
            }
        }

        if ($error) {
            // Required field(s) are missing or empty
            $error_fields = substr($error_fields, 0, -1);
            $message = 'Required field(s)' . $error_fields . ' is missing or empty.';
            $response = Response::json(array('code' => '201', 'message' => $message, 'cause' => '', 'response' => json_decode("{}")));
        } else
            $response = '';

        return $response;
    }

    // validate required field
    public function validateRequiredParam($required_fields,$request_params)
    {
        $error = false;
        $error_fields = '';

        foreach ($required_fields as $key => $value) {
            if (!(isset($request_params->$value))) {
                $error = true;
                $error_fields .= ' '.$value.',';
            }
        }

        if ($error) {
            // Required field(s) are missing or empty
            $error_fields = substr($error_fields,0,-1);
            $message = 'Required field(s)'.$error_fields.' is missing.';
            $response =  Response::json(array('code'=>'201','message'=>$message,'cause'=>'','response'=>json_decode("{}")));
        }
        else
            $response = '';
        return $response;
    }

    // verify otp
    public function verifyOTP($registration_id,$otp_token)
    {
        try{
            $result = DB::select('SELECT otp_token_expire
                                  FROM otp_codes
                                  WHERE user_registration_temp_id = ? AND
                                        otp_token = ?',[$registration_id,$otp_token]);
            if(count($result) == 0){
                $response =  Response::json(array('code'=>'201','message'=>'OTP is invalid.','cause'=>'','response'=>json_decode("{}")));
            }elseif(strtotime(date(Config::get('constant.DATE_FORMAT'))) > strtotime($result[0]->otp_token_expire)) {
                $response = Response::json(array('code' => '201', 'message' => 'OTP token expired.', 'cause' => '', 'response' => json_decode("{}")));
            }else{
                $response = '';
            }
        }catch (Exception $e) {
            $response =  Response::json(array('code'=>'201','message'=>$e->getMessage(),'cause'=>'','response'=>json_decode("{}")));
            Log::error('verifyOTP',['Exception'=>$e->getMessage()]);
        }
        return $response;
    }

    // check if user is active
    public function checkIfUserIsActive($user_id){
        try{

            $result = DB::select('SELECT
                                        um.is_active
                                        FROM user_master um
                                        WHERE um.email_id = ?',[$user_id]);
            $response = ($result[0]->is_active == '1') ? '' : Response::json(array('code' => '201','message'=>'You are inactive user. Please contact administrator.','cause'=>'', 'response' =>json_decode("{}")));

        }catch (Exception $e) {
            $response =  Response::json(array('code'=>'201','message'=>$e->getMessage(),'cause'=>'','response'=>json_decode("{}")));
        }
        return $response;
    }

    // check if user is Subscribed
    public function checkIfUserIsSubscribed($user_id){
        try{
            $current_time=date("Y-m-d H:i:s");
            $result = DB::select('SELECT sub.user_id
                                        FROM subscriptions sub,
                                             user_master um
                                        WHERE sub.user_id=um.user_id AND
                                              sub.expiration_time > ? AND
                                              um.user_id = ?',[$current_time,$user_id]);


            if(count($result) == 0) {
                $reActivationURL= (new Utils())->getBaseUrl()."/join/#/resubscribe/".$user_id;
                $response = Response::json(array('code' => '402', 'message' => 'Your subscription has been expired.', 'cause' => $reActivationURL, 'response' => json_decode("{}")));
            }else{
                $response = '';
            }

        }catch (Exception $e) {
            $response =  Response::json(array('code'=>'201','message'=>$e->getMessage(),'cause'=>'Your subscription has been expired.','response'=>json_decode("{}")));
        }
        return $response;
    }

    // check if  user is active
    public function checkIfUserExist($user_id)
    {
        try{
            $result = DB::select('SELECT 1 FROM user_master WHERE email_id = ?',[$user_id]);
            $response = (sizeof($result) != 0) ? 1 : 0;

        }catch (Exception $e) {
            $response =  Response::json(array('code'=>'201','message'=>$e->getMessage(),'cause'=>'','response'=>json_decode("{}")));
            Log::error('checkIfUserExist',['Exception'=>$e->getMessage()]);
        }
        return $response;
    }

    // verify user
    public function verifyUser($user_id,$role_name){
        try{
            $result = DB::select('SELECT r.name
                                  FROM role_user ru, roles r, user_master um
                                  WHERE r.id = ru.role_id AND
                                        um.id = ru.user_id AND
                                        um.email_id = ?',[$user_id]);
            $response = (sizeof($result) > 0 && $result[0]->name == $role_name) ? '' : Response::json(array('code' => '201', 'message' => 'Unauthorized user.', 'cause' => '', 'response' => json_decode("{}")));

        }catch (Exception $e) {
            $response =  Response::json(array('code'=>'201','message'=>$e->getMessage(),'cause'=>'','response'=>json_decode("{}")));
            Log::error('verifyUser',['Exception'=>$e->getMessage()]);
        }
        return $response;
    }

    // verify created by user
    public function verifyCreatedByUser($user_id,$vehicle_id){
        try{
            $result = DB::select('SELECT 1 FROM customer_vehicle_master WHERE user_id = ? AND id = ?',[$user_id,$vehicle_id]);
            if(sizeof($result) != 0)
                $response =  '';
            else
                $response = Response::json(array('code'=>'201','message'=>'Unauthorized user.','cause'=>'','response'=>json_decode("{}")));
        }catch (Exception $e) {
            $response =  Response::json(array('code'=>'201','message'=>$e->getMessage(),'cause'=>'','response'=>json_decode("{}")));
            Log::error('verifyCreatedByUser',['Exception'=>$e->getMessage()]);
        }
        return $response;
    }

    // get user role
    public function getUserRole($user_id){
        try{
            $result = DB::select('SELECT
                                        r.name
                                        FROM role_user ru, user_master um, roles r
                                        WHERE
                                          um.id = ru.user_id AND
                                          ru.role_id = r.id AND
                                          um.user_id = ?',[$user_id]);

            $response = (count($result) > 0) ? $result[0]->name : '';

        }catch (Exception $e) {
            $response =  Response::json(array('code'=>'201','message'=>$e->getMessage(),'cause'=>'','response'=>json_decode("{}")));
        }
        return $response;
    }

    // verify setup
    public function verifySetup($column, $user_id){
        try{
            $result = DB::select('SELECT
                                    '.$column.'
                                    FROM user_master um
                                    WHERE
                                      user_id = ?',[$user_id]);

            $response = $result[0]->$column;

        }catch (Exception $e) {
            $response =  Response::json(array('code'=>'201','message'=>$e->getMessage(),'cause'=>'','response'=>json_decode("{}")));
        }
        return $response;
    }

    // check if service is other
    public function checkIfServiceIsOther($professional_category_id){
        try{
            $result = DB::select('SELECT
                                      pc.category_id
                                    FROM professional_category pc
                                    WHERE
                                      id = ?',[$professional_category_id]);

            $response = $result[0]->category_id;

        }catch (Exception $e) {
            $response =  Response::json(array('code'=>'201','message'=>$e->getMessage(),'cause'=>'','response'=>json_decode("{}")));
        }
        return $response;
    }

    // check if service is other
    public function checkIfOtherServiceExist($professional_category_id){
        try{
            $result = DB::select('SELECT
                                      DISTINCT pc.category_id
                                    FROM professional_category pc, professional_sub_category psc
                                    WHERE
                                      pc.id = psc.professional_category_id AND
                                      psc.professional_category_id = ?',[$professional_category_id]);

            $response = count($result) > 0 ? $result[0]->category_id : 0;

        }catch (Exception $e) {
            $response =  Response::json(array('code'=>'201','message'=>$e->getMessage(),'cause'=>'','response'=>json_decode("{}")));
        }
        return $response;
    }

    // check if valid sub category
    public function checkIfValidSubCategory($professional_category_id, $sub_category_id){
        try{
            $result = DB::select('SELECT
                                      ssc.id
                                    FROM professional_category pc, professional_sub_category psc, service_category sc, service_sub_category ssc
                                    WHERE
                                      psc.professional_category_id = pc.id AND
                                      pc.category_id = sc.id AND
                                      ssc.category_id = pc.category_id AND
                                      psc.professional_category_id = ?',[$professional_category_id]);


            $sub_category_array = array();
            foreach($result as $object)
                array_push($sub_category_array, $object->id);

            if(count($result) > 0 ){
                if(in_array($sub_category_id, $sub_category_array))
                    $response = 1;
                else
                    $response = 0;
            }else
                $response = 1;


        }catch (Exception $e) {
            $response =  Response::json(array('code'=>'201','message'=>$e->getMessage(),'cause'=>'','response'=>json_decode("{}")));
        }
        return $response;
    }

    // check if service exist
    public function checkIfServiceExist($professional_sub_category_id){
        try{
            $result = DB::select('SELECT
                                      (SELECT COUNT(professional_category_id) FROM professional_sub_category WHERE professional_category_id = psc.professional_category_id) as total_services
                                    FROM professional_sub_category psc
                                    WHERE
                                      psc.id = ?',[$professional_sub_category_id]);

            $response = count($result) > 0 ? $result[0]->total_services : 0;

        }catch (Exception $e) {
            $response =  Response::json(array('code'=>'201','message'=>$e->getMessage(),'cause'=>'','response'=>json_decode("{}")));
        }
        return $response;
    }

    // check if service exist
    public function checkIfSubCategoryExist($professional_category_id, $sub_category_id){
        try{
            $result = DB::select('SELECT 1
                                    FROM professional_sub_category psc
                                    WHERE
                                      psc.professional_category_id = ? AND
                                      psc.sub_category_id = ?',[$professional_category_id, $sub_category_id]);

            $response = count($result);

        }catch (Exception $e) {
            $response =  Response::json(array('code'=>'201','message'=>$e->getMessage(),'cause'=>'','response'=>json_decode("{}")));
        }
        return $response;
    }

    // check if portfolio exist
    public function checkIfPortfolioExist($professional_id){
        try{
            $result = DB::select('SELECT
                                      COUNT(id) as total
                                    FROM professional_portfolio
                                    WHERE
                                      professional_id = ?',[$professional_id]);

            $response = count($result) > 0 ? $result[0]->total : 0;

        }catch (Exception $e) {
            $response =  Response::json(array('code'=>'201','message'=>$e->getMessage(),'cause'=>'','response'=>json_decode("{}")));
        }
        return $response;
    }

    // check if system user
    public function checkIfSystemUser($client_id){
        try{
            $result = DB::select('SELECT
                                      user_id
                                    FROM my_client
                                    WHERE
                                      id = ?',[$client_id]);

            $response = (count($result) > 0 && $result[0]->user_id == '') ? '' : Response::json(array('code'=>'201','message'=>'You can not update system user\'s  detail.','cause'=>'','response'=>json_decode("{}")));

        }catch (Exception $e) {
            $response =  Response::json(array('code'=>'201','message'=>$e->getMessage(),'cause'=>'','response'=>json_decode("{}")));
        }
        return $response;
    }

    // verify favourite professional already exist
    public function verifyAlreadyFavourite($professional_id, $user_id){
        try{
            $result = DB::select('SELECT 1 FROM favourite_professional
                                    WHERE user_id = ? AND professional_id = ?',
                                    [$user_id, $professional_id]);

            $response = (count($result) == 0 ) ? '' : Response::json(array('code'=>'201','message'=>'Favourite professional already exist.','cause'=>'','response'=>json_decode("{}")));

        }catch (Exception $e) {
            $response =  Response::json(array('code'=>'201','message'=>$e->getMessage(),'cause'=>'','response'=>json_decode("{}")));
        }
        return $response;
    }

    // verify open slot already added
    public function verifyOpenSlotExist($professional_id, $start_at, $end_at){
        try{
            $result = DB::select('SELECT
                                      pos.professional_id,
                                      pos.professional_category_id,
                                      pos.start_at,
                                      pos.end_at
                                    FROM professional_open_slot pos
                                    WHERE
                                    pos.professional_id = ? AND
                                    ((pos.start_at BETWEEN ? AND ?) OR (pos.end_at BETWEEN ? AND ?)',
                                    [$professional_id, $start_at, $end_at, $start_at, $end_at]);

            $response = (count($result) == 0 ) ? '' : Response::json(array('code'=>'201','message'=>'Favourite professional already exist.','cause'=>'','response'=>json_decode("{}")));

        }catch (Exception $e) {
            $response =  Response::json(array('code'=>'201','message'=>$e->getMessage(),'cause'=>'','response'=>json_decode("{}")));
        }
        return $response;
    }

    // get minimum time duration
    public function getMinimumTimeDuration($professional_category_id,$professional_id){
        try{
            $result = DB::select('SELECT
                                          MIN(psc.time_duration) as min_time_duration
                                        FROM professional_category pc, professional_sub_category psc
                                        WHERE pc.id = psc.professional_category_id AND
                                              psc.professional_category_id = ? AND
                                              pc.professional_id = ?',[$professional_category_id, $professional_id]);
            if(count($result) != 0){
                $response = $result[0]->min_time_duration;
            }else{
                $response = '';
            }
        }catch (Exception $e) {
            $response =  Response::json(array('code'=>'201','message'=>$e->getMessage(),'cause'=>'','response'=>json_decode("{}")));
        }
        return $response;
    }

    // get slot from result
    public function getSlotsFromResult($slot_result){
        try{
            $slot_array = array();
            foreach($slot_result as $slot){
                $start_at = $slot->start_at;
                $end_at = $slot->end_at;
                $interval = round(abs(strtotime($end_at) - strtotime($start_at)) / 60,2);
                while ($interval > 15) {
                    array_push($slot_array, $start_at);

                    $start_at = date('H:i', strtotime('+15 minutes', strtotime($start_at)));
                    $interval = round(abs(strtotime($end_at) - strtotime($start_at)) / 60,2);
                }
                array_push($slot_array, $start_at);
            }
        }catch (Exception $e) {
            return  Response::json(array('code'=>'201','message'=>$e->getMessage(),'cause'=>'','response'=>json_decode("{}")));
        }
        return $slot_array;
    }

    // get slot from result
    public function getSlotsFromResultForWeb($slot_result){
        try{

            $slot_array = array();
            foreach($slot_result as $slot){
                $start_at = $slot->start_at;
                $end_at = $slot->end_at;
                $interval = round(abs(strtotime($end_at) - strtotime($start_at)) / 60,2);
                while ($interval > 15) {
                    $next_end_at = date(Config::get('constant.DATE_FORMAT'), strtotime('+15 minutes', strtotime($start_at)));
                    $slot_obj = ['start_at'=>$start_at, 'end_at'=>$next_end_at];
                    array_push($slot_array, $slot_obj);

                    $start_at = date(Config::get('constant.DATE_FORMAT'), strtotime('+15 minutes', strtotime($start_at)));
                    $interval = round(abs(strtotime($end_at) - strtotime($start_at)) / 60,2);
                }
                $slot_obj = ['start_at'=>$start_at, 'end_at'=>$end_at];
                array_push($slot_array, $slot_obj);
            }
        }catch (Exception $e) {
            Log::error('getSlotsFromResultForWeb',['Exception'=>$e->getMessage(),'cause'=>$e->getTraceAsString()]);
            return  Response::json(array('code'=>'201','message'=>$e->getMessage(),'cause'=>'','response'=>json_decode("{}")));
        }
        return $slot_array;
    }

    // get slot from result for professional
    public function getSlotsFromResultForProfessional($slot_result){
        try{
            $i = 0;
            $slot_array = array();
            foreach($slot_result as $slot){
                $start_at = $slot->start_at;
                $end_at = $slot->end_at;
                $is_booked = $slot->is_booked;
                $interval = round(abs(strtotime($end_at) - strtotime($start_at)) / 60,2);
                while ($interval > 15) {
                    $slot_array[$i] = (object)['start_at' => $start_at,'is_booked' => $is_booked];
                    $start_at = date('H:i', strtotime('+15 minutes', strtotime($start_at)));
                    $interval = round(abs(strtotime($end_at) - strtotime($start_at)) / 60,2);
                    $i++;
                }
                $slot_array[$i] = (object)['start_at' => $start_at,'is_booked' => $is_booked];
                $i++;
            }
        }catch (Exception $e) {
            return  Response::json(array('code'=>'201','message'=>$e->getMessage(),'cause'=>'','response'=>json_decode("{}")));
        }
        return $slot_array;
    }

    // merge slot from array
    public function mergeSlotsFromArray($slot_list, $app_date){
        try{
            $total_slot = count($slot_list);
            $j = 0;
            $new_start_at = date(Config::get('constant.DATE_FORMAT'), strtotime($app_date.' '.current($slot_list)));
            for($i = 0; $i < $total_slot; $i++) {

                $start_at = date(Config::get('constant.DATE_FORMAT'), strtotime($app_date.' '.current($slot_list)));
                $end_at = date(Config::get('constant.DATE_FORMAT'), strtotime('+15 minutes', strtotime($start_at)));
                $next_start_at = date(Config::get('constant.DATE_FORMAT'), strtotime($app_date.' '.next($slot_list)));

                if($end_at != $next_start_at){

                   /* $slot_time_duration = round(abs(strtotime($end_at) - strtotime($new_start_at)) / 60,2);
                    if($slot_time_duration < $minimum_time)
                        return Response::json(array('code'=>'201','message'=>'Your minimum service time is '.$minimum_time.' minutes.','cause'=>'','response'=> json_decode('{}')));*/

                    $slot_array[$j]['start_at'] = $new_start_at;
                    $slot_array[$j]['end_at'] = $end_at;

                    $new_start_at = $next_start_at;
                    $j++;
                }
            }
        }catch (Exception $e) {
            return  Response::json(array('code'=>'201','message'=>$e->getMessage(),'cause'=>'','response'=>json_decode("{}")));
        }
        return $slot_array;
    }

    //check if slot exist
    public function checkIfSlotExist($professional_category_id, $professional_id, $date, $start_at, $end_at){
        try{
            $result = DB::select('SELECT
                                        pos.start_at,
                                        pos.end_at
                                        FROM professional_open_slot pos
                                        WHERE
                                        pos.professional_category_id = ? AND
                                        pos.date = ? AND
                                        pos.start_at <= ? AND
                                        pos.end_at >= ? AND
                                        pos.professional_id = ?',[$professional_category_id, $date, $start_at, $end_at, $professional_id]);
            $exist = (count($result) > 0) ? 1 : 0;

        }catch (Exception $e) {
            return  Response::json(array('code'=>'201','message'=>$e->getMessage(),'cause'=>'','response'=>json_decode("{}")));
        }
        return $exist;
    }

    // merge slot from array
    public function getOpenSlotByProfessionalId($professional_category_id, $professional_id, $date, $start_at, $end_at){
        try{
            $result = DB::select('SELECT
                                        pos.start_at,
                                        pos.end_at
                                        FROM professional_open_slot pos
                                        WHERE
                                        pos.professional_category_id = ? AND
                                        pos.date = ? AND
                                        pos.start_at <= ? AND
                                        pos.end_at >= ? AND
                                        pos.professional_id = ?',[$professional_category_id, $date, $start_at, $end_at, $professional_id]);

        }catch (Exception $e) {
            return  Response::json(array('code'=>'201','message'=>$e->getMessage(),'cause'=>'','response'=>json_decode("{}")));
        }
        return $result;
    }

    // get service time
    public function getServiceTime($professional_category_id, $sub_category_id){
        try{
            $service_time_result = DB::select('SELECT
                                                  psc.time_duration
                                                FROM professional_sub_category psc
                                                WHERE psc.professional_category_id = ? AND
                                                      psc.sub_category_id = ?',[$professional_category_id, $sub_category_id]);
            if(count($service_time_result) > 0){
                $response = $service_time = $service_time_result[0]->time_duration;
            }else{
                $response = '';
            }
        }catch (Exception $e) {
            return  Response::json(array('code'=>'201','message'=>$e->getMessage(),'cause'=>'','response'=>json_decode("{}")));
        }
        return $response;
    }

    // get service name
    public function getServiceName($sub_category_id){
        try{
            $result = DB::select('SELECT
                                          ssc.sub_category_name
                                        FROM service_sub_category ssc
                                        WHERE ssc.id = ?',[$sub_category_id]);
            $response = (count($result) != 0) ? $result[0]->sub_category_name : '';
        }catch (Exception $e) {
            $response =  Response::json(array('code'=>'201','message'=>$e->getMessage(),'cause'=>'','response'=>json_decode("{}")));
        }
        return $response;
    }

    // get service name
    public function getServiceColor($category_id){
        try{
            $result = DB::select('SELECT
                                          sc.category_color
                                        FROM service_category sc
                                        WHERE sc.id = ?',[$category_id]);
            $response = (count($result) != 0) ? $result[0]->category_color : '';
        }catch (Exception $e) {
            $response =  Response::json(array('code'=>'201','message'=>$e->getMessage(),'cause'=>'','response'=>json_decode("{}")));
        }
        return $response;
    }

    /**
     *  Schedule appointment validation
     *  This api will check if schedule is in between the booked slot or included the booked slot.
     * @param $professional_id
     * @param $date
     * @param $start_at
     * @param $end_at
     * @return string
     */
    public function checkIfScheduleExit($professional_id, $date, $start_at, $end_at){
        try{
            $result = DB::select('SELECT bs.start_at,bs.end_at,bs.professional_sub_category_id,bs.service_type
                                              FROM book_service as bs
                                              WHERE bs.is_active=1 AND
                                              bs.professional_id = ? AND
                                              ((bs.start_at = ? AND bs.end_at = ?) OR
                                              (bs.start_at = ? AND bs.end_at > ?)  OR
                                              (bs.start_at < ? AND bs.end_at > ?) OR
                                              (bs.start_at < ? AND bs.end_at = ?) OR
                                              (bs.end_at > ? AND bs.end_at <= ?) OR
                                              (bs.start_at > ? AND bs.start_at < ?))',
                [$professional_id, $start_at, $end_at, $start_at, $end_at, $start_at, $end_at, $start_at, $end_at, $start_at, $end_at, $start_at, $end_at]);


            if(count($result) > 0){
                //Yes, Schedule exits+

                Log::info("Start_at ".$start_at." To ".$end_at);
                if($result[0]->service_type != 3){
                    $response = '';
                }else{
                    $openslot = DB::select('SELECT
                                        pos.start_at,
                                        pos.end_at
                                        FROM professional_open_slot pos
                                        WHERE
                                        pos.date = ? AND
                                        pos.start_at <= ? AND
                                        pos.end_at >= ? AND
                                        pos.professional_id = ?',[$date, $start_at, $end_at, $professional_id]);
                    //Log::info($openslot);
                    if(!$openslot){
                        //Log::info("NoBooked");
                        $response = '';
                    }else{
                        $response = 'Booked Slot.';
                    }

                }

            }else{
                //No, Schedule exits
                $response = 'NoSchedule';
            }
        }catch (Exception $e) {
            return  Response::json(array('code'=>'201','message'=>$e->getMessage(),'cause'=>'','response'=>json_decode("{}")));
        }
        return $response;
    }

    public function checkIfAdvertisementExist($url)
    {
        try
        {
            $result = DB::select('SELECT * from advertise_links WHERE url = ?',[$url]);
            if(count($result)>=1)
            {
                $response = Response::json(array('code'=>'201','message'=>'This advertisement has already existed.','cause'=>'','response'=>json_decode("{}")));
            }
            else
            {
                $response = '';
            }
        }
        catch(Exception $e)
        {
            Log::error(["Exception :",$e->getMessage(),"TraceAsString :",$e->getTraceAsString()]);
            return  Response::json(array('code'=>'201','message'=>$e->getMessage(),'cause'=>'','response'=>json_decode("{}")));
        }

        return $response;
    }

    public function checkIfPromoCodeExist($promo_code, $package_name)
    {
        try
        {
            $result = DB::select('SELECT * from promocode_master WHERE promo_code = ?',[$promo_code, $package_name]);
            if(count($result)>=1)
            {
                $response = Response::json(array('code'=>'201','message'=>'Promo code already exists.','cause'=>'','response'=>json_decode("{}")));
            }
            else
            {
                $response = '';
            }
        }
        catch(Exception $e)
        {
            Log::error("checkIfPromoCodeExist Exception :", ['Exception' => $e->getMessage(), "\nTraceAsString :" => $e->getTraceAsString()]);
            return  Response::json(array('code'=>'201','message'=>$e->getMessage(),'cause'=>'','response'=>json_decode("{}")));
        }

        return $response;
    }

    //validateItemCount
    public function validateItemCount($item_count)
    {
        try
        {

            if($item_count < 3 or $item_count > 200)
            {
                $response = Response::json(array('code'=>'201','message'=>'Item count must be >= 3 and <= 200.','cause'=>'','response'=>json_decode("{}")));
            }
            else
            {
                $response = '';
            }
        }
        catch(Exception $e)
        {
            Log::error("validateItemCount Exception :", ['Exception' => $e->getMessage(), "\nTraceAsString :" => $e->getTraceAsString()]);
            return  Response::json(array('code'=>'201','message'=>$e->getMessage(),'cause'=>'','response'=>json_decode("{}")));
        }

        return $response;
    }

    //validateItemCount
    public function validateAdvertiseServerId($server_id)
    {
        try
        {

            $result = DB::select('SELECT * from sub_category_advertise_server_id_master WHERE server_id = ?',[$server_id]);
            if(count($result)>=1)
            {
                $response = Response::json(array('code'=>'201','message'=>'Server id already exists.','cause'=>'','response'=>json_decode("{}")));
            }
            else
            {
                $response = '';
            }
        }
        catch(Exception $e)
        {
            Log::error("validateAdvertiseServerId Exception :", ['Exception' => $e->getMessage(), "\nTraceAsString :" => $e->getTraceAsString()]);
            return  Response::json(array('code'=>'201','message'=>$e->getMessage(),'cause'=>'','response'=>json_decode("{}")));
        }

        return $response;
    }
}

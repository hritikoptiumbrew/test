<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Log;
use DB;
use Response;
use Exception;
use QueryException;
use Config;

class RegisterController extends Controller
{

    /**
     * @api {post} registerUserDeviceByDeviceUdid registerUserDeviceByDeviceUdid
     * @apiName registerUserDeviceByDeviceUdid
     * @apiGroup User
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     *
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "sub_category_id":1,
     * "device_carrier": "",
     * "device_country_code": "IN",
     * "device_reg_id": "115a1a110", //Mandatory
     * "device_default_time_zone": "Asia/Calcutta",
     * "device_language": "en",
     * "device_latitude": "",
     * "device_library_version": "1",
     * "device_local_code": "NA",
     * "device_longitude": "",
     * "device_model_name": "Micromax AQ4501",
     * "device_os_version": "6.0.1",
     * "device_platform": "android", //Mandatory
     * "device_registration_date": "2016-05-06T15:58:11 +0530",
     * "device_resolution": "480x782",
     * "device_type": "phone",
     * "device_udid": "109111aa1121", //Mandatory
     * "device_vendor_name": "Micromax",
     * "project_package_name": "com.optimumbrew.projectsetup"
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Device registered successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function registerUserDeviceByDeviceUdid(Request $request_body)
    {
        try {
            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('sub_category_id', 'device_udid', 'device_reg_id', 'device_platform'), $request)) != '')
                return $response;

            //$user_id = isset($user_id) ? $user_id : '';
            $user_id = isset($request->user_id) ? $request->user_id : '';

            $sub_category_id = $request->sub_category_id;
            $device_udid = $request->device_udid;
            $device_reg_id = $request->device_reg_id;
            $device_platform = $request->device_platform;
            //Log::debug('after_login_device_reg_id', ['Exception' => $device_reg_id]);
            //Optional field
            $device_model_name = 'NA';//isset($request->device_model_name) ? $request->device_model_name : '';
            $device_vendor_name = 'NA';//isset($request->device_vendor_name) ? $request->device_vendor_name : '';
            $device_os_version = isset($request->device_os_version) ? $request->device_os_version : '';
            $device_resolution = isset($request->device_resolution) ? $request->device_resolution : '';
            $device_carrier = isset($request->device_carrier) ? $request->device_carrier : '';
            $device_country_code = isset($request->device_country_code) ? $request->device_country_code : '';
            $device_language = isset($request->device_language) ? $request->device_language : '';
            $device_local_code = isset($request->device_local_code) ? $request->device_local_code : '';
            $device_default_time_zone = isset($request->device_default_time_zone) ? $request->device_default_time_zone : '';
            $device_application_version = isset($request->device_application_version) ? $request->device_application_version : '';
            $device_type = isset($request->device_type) ? $request->device_type : '';
            $device_registration_date = isset($request->device_registration_date) ? $request->device_registration_date : '';
            $device_library_version = isset($request->device_library_version) ? $request->device_library_version : '';

            DB::beginTransaction();

            $result = DB::select('SELECT 1 FROM device_master WHERE device_udid = ? and sub_category_id = ?', [$device_udid, $sub_category_id]);
            //Log::info('registerUserDeviceByDeviceUdid', ['total device having udid from request' => sizeof($result)]);
            if (sizeof($result) == 0) {
                //Log::info('registerUserDeviceByDeviceUdid', ['device_reg_id' => $device_reg_id]);
                /*DB::insert('INSERT INTO device_master
                            (sub_category_id,
                            user_id,
                            device_reg_id,
                            device_platform,
                            device_model_name,
                            device_vendor_name,
                            device_os_version,
                            device_udid,
                            device_resolution,
                            device_country_code,
                            device_language,
                            device_local_code,
                            device_default_time_zone,
                            device_library_version,
                            device_application_version,
                            device_type,
                            device_registration_date)
                            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?) ',
                    [$sub_category_id,
                        $user_id,
                        $device_reg_id,
                        $device_platform,
                        $device_model_name,
                        $device_vendor_name,
                        $device_os_version,
                        $device_udid,
                        $device_resolution,
                        $device_country_code,
                        $device_language,
                        $device_local_code,
                        $device_default_time_zone,
                        $device_library_version,
                        $device_application_version,
                        $device_type,
                        $device_registration_date
                    ]);*/
            } else {
                /*DB::update('UPDATE device_master
                            SET device_reg_id = ?
                                WHERE device_udid = ? AND sub_category_id = ?',
                    [$device_reg_id, $device_udid, $sub_category_id]);*/
            }

            DB::commit();

            $response = Response::json(array('code' => 200, 'message' => 'Device registered successfully.', 'cause' => '', 'data' => json_decode("{}")));

        } catch (Exception $e) {
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'Unable to register device.', 'cause' => '', 'data' => json_decode("{}")));
            Log::error('registerUserDeviceByDeviceRegId', ['Exception' => $e->getMessage()]);
            DB::rollBack();
        }
        return $response;
    }
}


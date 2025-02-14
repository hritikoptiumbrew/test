<?php

namespace App\Http\Controllers;

use App\User_master;
use Illuminate\Http\Request;
use Config;
use DB;
use Hash;
use Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Response;
use Exception;
use JWTAuth;

class LoginController extends Controller
{

    /**
     * @api {post} doLoginForGuest doLoginForGuest
     * @apiName doLoginForGuest
     * @apiGroup User
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Body:
     * {}
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Login Success.",
     * "cause": "",
     * "data": {
     * "token": ""
     * }
     * }
     */
    public function doLoginForGuest()
    {
        try {
            //Mandatory field
            $email_id = Config::get('constant.GUEST_USER_UD');
            $password = Config::get('constant.GUEST_PASSWORD');
            $role_name = Config::get('constant.ROLE_FOR_USER');

            $credential = ['email_id' => $email_id, 'password' => $password];
            if (!$token = JWTAuth::attempt($credential))
                return Response::json(array('code' => 201, 'message' => 'Invalid email or password.', 'cause' => '', 'data' => json_decode("{}")));

//            if (($response = (new VerificationController())->verifyUser($email_id, $role_name)) != '')
//                return $response;

//            if (($response = (new VerificationController())->checkIfUserIsActive($email_id)) != '')
//                return $response;

            $response = Response::json(array('code' => 200, 'message' => 'Login successfully.', 'cause' => '', 'data' => ['token' => $token]));

            //Log::info("Login token",["token :" => $token,"time" => date('H:m:s')]);

        } catch (JWTException $e) {
            Log::error("doLoginForGuest(JWTException) : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => 'Could not create token.' . $e->getMessage(), 'cause' => '', 'data' => json_decode("{}")));
        } catch (Exception $e) {
            Log::error("doLoginForGuest : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'login.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    public function doLoginForContentUploader_v2(Request $request_body)
    {
        try {
            $request = json_decode($request_body->getContent());

            //Mandatory Field
            if (($response = (new VerificationController())->validateRequiredParameter(array(
                    'email_id',
                    'password'), $request)) != ''
            )
                return $response;

            $email_id = $request->email_id;
            $password = $request->password;
//            $role_name = Config::get('constant.ROLE_FOR_CONTENT_UPLOADER');

            $credential = ['email_id' => $email_id, 'password' => $password];
            if (!$token = JWTAuth::attempt($credential))
                return Response::json(array('code' => 201, 'message' => 'Invalid email or password.', 'cause' => '', 'data' => json_decode("{}")));

//            if (($response = (new VerificationController())->verifyUser($email_id, $role_name)) != '')
//                return $response;

            if (($response = (new VerificationController())->checkIfUserIsActive($email_id)) != '')
                return $response;

            $response = Response::json(array('code' => 200, 'message' => 'Login successfully.', 'cause' => '', 'data' => ['token' => $token]));

            //Log::info("Login token",["token :" => $token,"time" => date('H:m:s')]);

        } catch (JWTException $e) {
            Log::error("doLoginForContentUploader_v2(JWTException) : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => 'Could not create token.' . $e->getMessage(), 'cause' => '', 'data' => json_decode("{}")));
        } catch (Exception $e) {
            Log::error("doLoginForContentUploader_v2 : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'login.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /**
     * @api {post} doLogin  doLogin
     * @apiName doLogin
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     *
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "email_id":"demo@gmail.com",
     * "password":"123456"
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Login Successfully.",
     * "cause": "",
     * "data": {
     * "token": "",
     * "user_detail": {
     * "id": 1,
     * "user_name": "admin",
     * "email_id": "admin@gmail.com",
     * "social_uid": null,
     * "signup_type": null,
     * "profile_setup": 0,
     * "is_active": 1,
     * "create_time": "2017-05-05 09:57:26",
     * "update_time": "2017-07-06 13:19:13",
     * "attribute1": null,
     * "attribute2": null,
     * "attribute3": null,
     * "attribute4": null
     * }
     * }
     * }
     */
    public function doLogin(Request $request_body)
    {
        try {
            $request = json_decode($request_body->getContent());

            //Mandatory Field
            if (($response = (new VerificationController())->validateRequiredParameter(array(
                    'email_id',
                    'password'), $request)) != ''
            )
                return $response;

            $email_id = $request->email_id;
            $password = $request->password;

            $credential = ['email_id' => $email_id, 'password' => $password];
            if (!$token = JWTAuth::attempt($credential))
            {
                return Response::json(array('code' => 201, 'message' => 'The email and password you entered did not match our records. Please try again.', 'cause' => '', 'data' => json_decode("{}")));
            }


            $user_id = JWTAuth::toUser($token)->id;
            if ($user_id == 1) {

                $google2fa_enable = JWTAuth::toUser($token)->google2fa_enable;
                if($google2fa_enable == 1 && !isset($_COOKIE[$user_id])){
                    $this->createNewSession($user_id, $token);
                    $response = Response::json(array('code' => 200, 'message' => 'Login successfully.', 'cause' => '', 'data' => ['token' => "", 'user_detail' => JWTAuth::toUser($token)]));

                } elseif ($google2fa_enable != 1) {
                    $this->createNewSession($user_id, $token);
                    $response = Response::json(array('code' => 200, 'message' => 'Login successfully.', 'cause' => '', 'data' => ['token' => $token, 'user_detail' => JWTAuth::toUser($token)]));

                } elseif (Hash::check($password, $_COOKIE[$user_id])) {
                    $this->createNewSession($user_id, $token);
                    $response = Response::json(array('code' => 200, 'message' => 'Login successfully.', 'cause' => '', 'data' => ['token' => $token, 'user_detail' => JWTAuth::toUser($token)]));

                } else {
                    $this->createNewSession($user_id, $token);
                    $response = Response::json(array('code' => 200, 'message' => 'Login successfully.', 'cause' => '', 'data' => ['token' => "", 'user_detail' => JWTAuth::toUser($token)]));

                }

                /*$this->createNewSession($user_id, $token);
                return Response::json(array('code' => 200, 'message' => 'Login successfully.', 'cause' => '', 'data' => ['token' => $token, 'user_detail' => JWTAuth::toUser($token)]));*/

            } else {
                $response = Response::json(array('code' => 201, 'message' => 'The email and password you entered did not match our records. Please try again..', 'cause' => '', 'data' => json_decode("{}")));
            }

        } catch (Exception $e) {
            Log::error("doLogin : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'login.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    /**
     * @api {post} doLogout   doLogout
     * @apiName doLogout
     * @apiGroup Common For All
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     *
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "User have successfully logged out.",
     * "cause": "",
     * "data": {
     *
     * }
     * }
     */
    public function doLogout()
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $user_data = JWTAuth::parseToken()->authenticate();
            $user_id = $user_data->id;

            DB::beginTransaction();
            DB::delete('DELETE FROM user_session WHERE token = ? AND user_id = ?', [$token, $user_id]);
            DB::commit();
            $response = Response::json(array('code' => 200, 'message' => 'User have successfully logged out.', 'cause' => '', 'data' => json_decode("{}")));

        } catch (Exception $e) {
            Log::error("doLogout : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'logout.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /**
     * @api {post} changePassword   changePassword
     * @apiName changePassword
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "current_password":"**********",
     * "new_password":"***********"
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Password updated successfully.",
     * "cause": "",
     * "data": {
     * "token": ""
     * }
     * }
     */
    public function changePassword(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('current_password', 'new_password'), $request)) != '')
                return $response;

            $user_data = JWTAuth::parseToken()->authenticate();
            $email_id = $user_data->email_id;
            $user_id = $user_data->id;

            //Mandatory field
            $current_password = $request->current_password;
            $new_password = Hash::make($request->new_password);

            $credential = ['email_id' => $email_id, 'password' => $current_password];
            if (!$old_token = JWTAuth::attempt($credential))
                return Response::json(array('code' => 201, 'message' => 'Current password is incorrect.', 'cause' => '', 'data' => json_decode("{}")));

            DB::beginTransaction();

            DB::update('UPDATE user_master
                          SET
                          password = ?
                          WHERE email_id = ?', [$new_password, $email_id]);

            DB::commit();

            $credential = ['email_id' => $email_id, 'password' => $request->new_password];

            if ($new_token = JWTAuth::attempt($credential)) {
                //Log::info('change pass  :', ['old_token' => $token, 'new_token' => $new_token]);
                DB::beginTransaction();
                DB::update('UPDATE user_session
                          SET
                          token = ?
                          WHERE token = ?', [$new_token, $token]);
                DB::commit();

                DB::delete('DELETE FROM user_session WHERE token != ? AND user_id = ?', [$new_token, $user_id]);
                DB::commit();
            }

            $response = Response::json(array('code' => 200, 'message' => 'Password changed successfully.', 'cause' => '', 'data' => ['token' => $new_token]));

        } catch (Exception $e) {
            Log::error("changePassword : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'change password.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    // create new user session
    public function createNewSession($user_id, $token)
    {
        try {
            DB::beginTransaction();

            DB::insert('INSERT INTO user_session
                                    (user_id, token)
                                    VALUES (?,?)',
                [$user_id, $token]);
            DB::commit();
            $response = '';

        } catch (Exception $e) {
            Log::error("createNewSession : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'create new session.', 'cause' => '', 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /**
     * @api {post} getUserProfile   getUserProfile
     * @apiName getUserProfile
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     *
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "getUserProfile Successfully.",
     * "cause": "",
     * "data": {
     * "user_details": [
     * {device_platform
     * "id": 1,
     * "first_name": "admin",
     * "last_name": "admin",
     * "phone_number_1": "9173527938",
     * "profile_img": "http://localhost/bgchanger/image_bucket/thumbnail/595b4076a8c8c_profile_img_1499152502.jpg",
     * "about_me": "i'm Admin.",
     * "address_line_1": "Rander Road",
     * "city": "surat",
     * "state": "gujarat",
     * "zip_code": "395010",
     * "contry": "India",
     * "latitude": "",
     * "longitude": ""
     * }
     * ]
     * }
     * }
     */
    public function getUserProfile(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $user_data = JWTAuth::parseToken()->authenticate();
            $user_id = $user_data->email_id;

            $result = DB::select('SELECT
                                  um.id,
                                  ud.first_name,
                                  ud.last_name,
                                  COALESCE (ud.phone_number_1,"") as phone_number_1,
                                  IF(ud.profile_img != "",CONCAT("' . Config::get('constant.GET_THUMBNAIL_IMAGES_DIRECTORY_BASE_URL') . '",ud.profile_img),"") AS profile_img,
                                  ud.about_me,
                                  ud.address_line_1,
                                  ud.city,
                                  ud.state,
                                  ud.zip_code,
                                  ud.contry,
                                  COALESCE (ud.latitude,"") as latitude,
                                  COALESCE (ud.longitude,"") as longitude
                                FROM
                                  user_detail AS ud,
                                  user_master AS um
                                WHERE
                                  ud.user_id = um.id AND
                                  ud.email_id = ?', [$user_id]);

            $result = ['user_details' => $result];

            $response = Response::json(array('code' => 200, 'message' => 'Profile fetched successfully.', 'cause' => '', 'data' => $result));

        } catch (Exception $e) {
            Log::error("getUserProfile : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get user profile.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    // create new device
    public function addNewDeviceToUser($sub_category_id, $device_reg_id, $device_platform, $device_model_name, $device_vendor_name, $device_os_version, $device_udid, $device_resolution, $device_carrier, $device_country_code, $device_language, $device_local_code, $device_default_time_zone, $device_application_version, $device_type, $device_registration_date)
    {
        try {
            //$created_date = date(Config::get('constant.DATE_FORMAT'));
            $device_result = DB::select('SELECT device_id FROM device_master WHERE  device_udid = ?', [$device_udid]);

            DB::beginTransaction();

            if (count($device_result) != 0) {
                /*DB::update('UPDATE device_master SET device_reg_id = ? WHERE device_udid = ? AND sub_category_id = ?',
                    [$device_reg_id, $device_udid,$sub_category_id]);*/
                $response = $device_result[0]->device_id;
            } else {
                $results = '';/*DB::table('device_master')->insertGetId(
                    array( //'user_id' => $user_id,
                        'sub_category_id' => $sub_category_id,
                        'device_reg_id' => $device_reg_id,
                        'device_platform' => $device_platform,
                        'device_model_name' => 'NA',
                        'device_vendor_name' => 'NA',
                        'device_os_version' => $device_os_version,
                        'device_udid' => $device_udid,
                        'device_resolution' => $device_resolution,
                        'device_country_code' => $device_country_code,
                        'device_language' => $device_language,
                        'device_local_code' => $device_local_code,
                        'device_default_time_zone' => $device_default_time_zone,
                        'device_application_version' => $device_application_version,
                        'device_type' => $device_type,
                        'device_registration_date' => $device_registration_date)
                );*/
                $response = $results;
                //Log::info($response);
            }

            DB::commit();

        } catch (Exception $e) {
            Log::error("addNewDeviceToUser : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'add user device.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

}

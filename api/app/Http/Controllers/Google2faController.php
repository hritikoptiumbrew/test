<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Hash;
use JWTAuth;
use JWTFactory;
use Response;
use DB;
use Config;
use QueryException;
use Exception;
use Log;
use Auth;
use Cache;
use File;
use UploadedFile;
use Image;
use JWTException;
use Mail;

/**
 * Class Google2faController
 *
 * @package api\app\Http\Controllers\api
 */
class Google2faController extends Controller
{

    /**
     * @api {post} enable2faByAdmin enable2faByAdmin
     * @apiName enable2faByAdmin
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     *{
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "2FA has been enabled successfully.",
     * "cause": "",
     * "data": {
     * "google2fa_url": "https://chart.googleapis.com/chart?chs=200x200&chld=M|0&cht=qr&chl=otpauth%3A%2F%2Ftotp%2FOB%2520ADS%3Aadmin%2540gmail.com%3Fsecret%3D3WJMFHPL2XBLWNT3%26issuer%3DOB%2520ADS",
     * "google2fa_secret": "JMFHPL2XBLWNT3"
     * }
     * }
     */
    public function enable2faByAdmin()
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);
            $user = Auth::user();
            $user_id = $user->id;
            $email_id = $user->email_id;
            $google2fa = app('pragmarx.google2fa');
            $google2fa_secret = $google2fa->generateSecretKey();

            // Generate the QR image. This is the image the user will scan with their app
            $google2fa->setAllowInsecureCallToGoogleApis(true);
            $google2fa_url = $google2fa->getQRCodeGoogleUrl(
                Config::get('constant.APP_HOST_NAME'),
                $email_id,
                $google2fa_secret
            );
            DB::beginTransaction();

            DB::update('UPDATE user_master
                                SET google2fa_secret = ?, google2fa_enable = 1
                                WHERE id = ?', [$google2fa_secret, $user_id]);
            DB::commit();

            DB::delete('DELETE FROM user_session WHERE user_id = ? AND token != ?', [$user_id, $token]);
            DB::commit();

            $result = array(
                'google2fa_url' => $google2fa_url,
                'google2fa_secret' => $google2fa_secret
            );

            $response = Response::json(array('code' => 200, 'message' => '2FA has been enabled successfully.', 'cause' => '', 'data' => $result));

        } catch (Exception $e) {
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'enable 2fa by admin.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            Log::error("enable2faByAdmin : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
        }
        return $response;
    }

    /**
     * @api {post} verify2faOPT verify2faOPT
     * @apiName verify2faOPT
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "verify_code": "557537", //compulsory
     * "user_id": "557537", //compulsory
     * "google2fa_secret": "557537" //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "OTP verified successfully.",
     * "cause": "",
     * "data": {
     * "user_detail": {
     * "id": 1,
     * "user_name": "admin",
     * "email_id": "admin@gmail.com",
     * "google2fa_enable": 1,
     * "google2fa_secret": "CY3VRNFBMJBA75EA",
     * "social_uid": null,
     * "signup_type": null,
     * "profile_setup": 0,
     * "is_active": 1,
     * "create_time": "2017-08-02 12:08:30",
     * "update_time": "2018-10-20 06:11:38",
     * "attribute1": null,
     * "attribute2": null,
     * "attribute3": null,
     * "attribute4": null
     * }
     * }
     * }
     */
    public function verify2faOPT(Request $request_body)
    {
        try {

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('verify_code', 'user_id', 'google2fa_secret'), $request)) != '') {
                return $response;
            }
            $secret = $request->verify_code;
            $google2fa_secret = $request->google2fa_secret;
            $user_id = $request->user_id;

            $user_detail = DB::select('SELECT * FROM user_master WHERE id = ? AND google2fa_secret = ?', [$user_id, $google2fa_secret]);
            if (!$user_detail) {
                $response = Response::json(array('code' => 201, 'message' => 'Invalid user.', 'cause' => '', 'data' => json_decode("{}")));
            } else {

                $google2fa = app('pragmarx.google2fa');
                $valid = $google2fa->verifyKey($google2fa_secret, $secret);
                if ($valid) {
                    $user_token = DB::select('SELECT token FROM user_session WHERE user_id = ? ORDER by id DESC limit 1', [$user_id]);
                    if (!$user_token) {
                        $response = Response::json(array('code' => 201, 'message' => 'Invalid verification, You have to login first.', 'cause' => '', 'data' => json_decode("{}")));
                    } else {
                        $token = $user_token[0]->token;
                        if (!isset($_COOKIE[$user_detail[0]->id])) {
                            setcookie($user_detail[0]->id, $user_detail[0]->password, time() + Config::get('constant.EXPIRATION_TIME_OF_2FA_COOKIE'), "/");
                        }
                        $response = Response::json(array('code' => 200, 'message' => 'OTP verified successfully.', 'cause' => '', 'data' => ['token' => $token, 'user_detail' => JWTAuth::toUser($token)]));
                    }
                } else {
                    $response = Response::json(array('code' => 201, 'message' => 'Invalid verification code, Please try again.', 'cause' => '', 'data' => json_decode("{}")));
                }

            }

        } catch (Exception $e) {
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'verify the code, please try again.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            Log::error("verify2faOPT : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
        }
        return $response;
    }

    /**
     * @api {post} disable2faByAdmin disable2faByAdmin
     * @apiName disable2faByAdmin
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "verify_code": 123456,
     * "google2fa_secret":"ABCDEF"
     *
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "2FA has been disabled successfully",
     * "cause": "",
     * "data": {
     * "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjEsImlzcyI6Imh0dHA6Ly8xOTIuMTY4LjAuMTEzL3Bob3RvYWRraW5nX3Rlc3RpbmcvYXBpL3B1YmxpYy9hcGkvZG9Mb2dpbkZvckFkbWluIiwiaWF0IjoxNTQ3MzQ5NDY2LCJleHAiOjE1NDc5NTQyNjYsIm5iZiI6MTU0NzM0OTQ2NiwianRpIjoieDA5WUNoWUtudHlwYklWdiJ9.SifYqWURQBhpTG3jocKV1ng-zLx2KSeiCebwUKbl-E0",
     * "user_detail": {
     * "id": 1,
     * "user_name": "admin@gmail.com",
     * "email_id": "admin@gmail.com",
     * "google2fa_enable": 0,
     * "google2fa_secret": "7A7RMQ33CHLQQU5E",
     * "social_uid": null,
     * "signup_type": null,
     * "profile_setup": 1,
     * "mailchimp_subscr_id": null,
     * "is_active": 1,
     * "is_verify": 1,
     * "create_time": "2018-09-21 06:37:46",
     * "update_time": "2019-01-13 07:40:51",
     * "attribute1": null,
     * "attribute2": null,
     * "attribute3": null,
     * "attribute4": null
     * }
     * }
     * }
     */
    public function disable2faByAdmin(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);
            $user = Auth::user();
            $user_id = $user->id;

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('verify_code', 'google2fa_secret'), $request)) != '') {
                return $response;
            }

            $secret = $request->verify_code;
            $google2fa_secret = $request->google2fa_secret;

            $user_detail = DB::select('SELECT * FROM user_master WHERE id = ? AND google2fa_secret = ?', [$user_id, $google2fa_secret]);
            if (!$user_detail) {
                $response = Response::json(array('code' => 201, 'message' => 'Invalid user.', 'cause' => '', 'data' => json_decode("{}")));
            } else {

                $google2fa = app('pragmarx.google2fa');
                $valid = $google2fa->verifyKey($google2fa_secret, $secret);
                if ($valid) {

                    $requestHeaders = apache_request_headers();
                    $jwt_token = str_ireplace('Bearer ', '', $requestHeaders['Authorization']);

                    DB::beginTransaction();
                    DB::update('UPDATE user_master SET google2fa_enable = 0 WHERE id = ?', [$user_id]);
                    DB::commit();

                    $response = Response::json(array('code' => 200, 'message' => '2FA has been disabled successfully', 'cause' => '', 'data' => ['token' => $jwt_token, 'user_detail' => JWTAuth::toUser($token)]));

                } else {
                    $response = Response::json(array('code' => 201, 'message' => 'Invalid verification code, Please try again.', 'cause' => '', 'data' => json_decode("{}")));
                }

            }

        } catch (Exception $e) {
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'disable 2fa by admin.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            Log::error("disable2faByAdmin : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
        }
        return $response;
    }

}

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
use FontLib\Font;

class VerificationController extends Controller
{
    // validate required and empty field
    public function validateRequiredParameter($required_fields, $request_params)
    {
        $error = false;
        $error_fields = '';

        foreach ($required_fields as $key => $value) {
            if (isset($request_params->$value)) {
                if (!is_object($request_params->$value)) {
                    if (strlen($request_params->$value) == 0) {
                        $error = true;
                        $error_fields .= ' ' . $value . ',';
                    }
                }
            } else {
                $error = true;
                $error_fields .= ' ' . $value . ',';
            }
        }

        if ($error) {
            // Required field(s) are missing or empty
            $error_fields = substr($error_fields, 0, -1);
            $message = 'Required field(s)' . $error_fields . ' is missing or empty.';
            $response = Response::json(array('code' => 201, 'message' => $message, 'cause' => '', 'response' => json_decode("{}")));
        } else
            $response = '';

        return $response;
    }

    public function validateRequiredArrayParameter($required_fields, $request_params)
    {
        $error = false;
        $error_fields = '';

        foreach ($required_fields as $key => $value) {
            if (isset($request_params->$value)) {
                if (!is_array($request_params->$value)) {
                    $error = true;
                    $error_fields .= ' ' . $value . ',';
                } else {
                    if (count($request_params->$value) == 0) {
                        $error = true;
                        $error_fields .= ' ' . $value . ',';
                    }
                }
            } else {
                $error = true;
                $error_fields .= ' ' . $value . ',';
            }
        }

        if ($error) {
            // Required field(s) are missing or empty
            $error_fields = substr($error_fields, 0, -1);
            $message = 'Required field(s)' . $error_fields . ' is missing or empty.';
            $response = Response::json(array('code' => 201, 'message' => $message, 'cause' => '', 'response' => json_decode("{}")));
        } else
            $response = '';

        return $response;
    }

    // validate required field
    public function validateRequiredParam($required_fields, $request_params)
    {
        $error = false;
        $error_fields = '';

        foreach ($required_fields as $key => $value) {
            if (!(isset($request_params->$value))) {
                $error = true;
                $error_fields .= ' ' . $value . ',';
            }
        }

        if ($error) {
            // Required field(s) are missing or empty
            $error_fields = substr($error_fields, 0, -1);
            $message = 'Required field(s)' . $error_fields . ' is missing.';
            $response = Response::json(array('code' => 201, 'message' => $message, 'cause' => '', 'response' => json_decode("{}")));
        } else
            $response = '';
        return $response;
    }

    // verify otp
    public function verifyOTP($registration_id, $otp_token)
    {
        try {
            $result = DB::select('SELECT otp_token_expire
                                  FROM otp_codes
                                  WHERE user_registration_temp_id = ? AND
                                        otp_token = ?', [$registration_id, $otp_token]);
            if (count($result) == 0) {
                $response = Response::json(array('code' => 201, 'message' => 'OTP is invalid.', 'cause' => '', 'response' => json_decode("{}")));
            } elseif (strtotime(date(Config::get('constant.DATE_FORMAT'))) > strtotime($result[0]->otp_token_expire)) {
                $response = Response::json(array('code' => 201, 'message' => 'OTP token expired.', 'cause' => '', 'response' => json_decode("{}")));
            } else {
                $response = '';
            }
        } catch (Exception $e) {
            $response = Response::json(array('code' => 201, 'message' => $e->getMessage(), 'cause' => '', 'response' => json_decode("{}")));
            Log::error('verifyOTP', ['Exception' => $e->getMessage()]);
        }
        return $response;
    }

    // check if user is active
    public function checkIfUserIsActive($user_id)
    {
        try {

            $result = DB::select('SELECT
                                        um.is_active
                                        FROM user_master um
                                        WHERE um.email_id = ?', [$user_id]);
            $response = ($result[0]->is_active == '1') ? '' : Response::json(array('code' => 201, 'message' => 'You are inactive user. Please contact administrator.', 'cause' => '', 'response' => json_decode("{}")));

        } catch (Exception $e) {
            $response = Response::json(array('code' => 201, 'message' => $e->getMessage(), 'cause' => '', 'response' => json_decode("{}")));
        }
        return $response;
    }

    // check if user is Subscribed
    public function checkIfUserIsSubscribed($user_id)
    {
        try {
            $current_time = date("Y-m-d H:i:s");
            $result = DB::select('SELECT sub.user_id
                                        FROM subscriptions sub,
                                             user_master um
                                        WHERE sub.user_id=um.user_id AND
                                              sub.expiration_time > ? AND
                                              um.user_id = ?', [$current_time, $user_id]);


            if (count($result) == 0) {
                $reActivationURL = (new Utils())->getBaseUrl() . "/join/#/resubscribe/" . $user_id;
                $response = Response::json(array('code' => 402, 'message' => 'Your subscription has been expired.', 'cause' => $reActivationURL, 'response' => json_decode("{}")));
            } else {
                $response = '';
            }

        } catch (Exception $e) {
            $response = Response::json(array('code' => 201, 'message' => $e->getMessage(), 'cause' => 'Your subscription has been expired.', 'response' => json_decode("{}")));
        }
        return $response;
    }

    // check if  user is active
    public function checkIfUserExist($user_id)
    {
        try {
            $result = DB::select('SELECT 1 FROM user_master WHERE email_id = ?', [$user_id]);
            $response = (sizeof($result) != 0) ? 1 : 0;

        } catch (Exception $e) {
            $response = Response::json(array('code' => 201, 'message' => $e->getMessage(), 'cause' => '', 'response' => json_decode("{}")));
            Log::error('checkIfUserExist', ['Exception' => $e->getMessage()]);
        }
        return $response;
    }

    // verify user
    public function verifyUser($user_id, $role_name)
    {
        try {
            $result = DB::select('SELECT r.name
                                  FROM role_user ru, roles r, user_master um
                                  WHERE r.id = ru.role_id AND
                                        um.id = ru.user_id AND
                                        um.email_id = ?', [$user_id]);
            $response = (sizeof($result) > 0 && $result[0]->name == $role_name) ? '' : Response::json(array('code' => 201, 'message' => 'Unauthorized user.', 'cause' => '', 'response' => json_decode("{}")));

        } catch (Exception $e) {
            $response = Response::json(array('code' => 201, 'message' => $e->getMessage(), 'cause' => '', 'response' => json_decode("{}")));
            Log::error('verifyUser', ['Exception' => $e->getMessage()]);
        }
        return $response;
    }

    // get user role
    public function getUserRole($user_id)
    {
        try {
            $result = DB::select('SELECT
                                        r.name
                                        FROM role_user ru, user_master um, roles r
                                        WHERE
                                          um.id = ru.user_id AND
                                          ru.role_id = r.id AND
                                          um.user_id = ?', [$user_id]);

            $response = (count($result) > 0) ? $result[0]->name : '';

        } catch (Exception $e) {
            $response = Response::json(array('code' => 201, 'message' => $e->getMessage(), 'cause' => '', 'response' => json_decode("{}")));
        }
        return $response;
    }

    // verify setup
    public function verifySetup($column, $user_id)
    {
        try {
            $result = DB::select('SELECT
                                    ' . $column . '
                                    FROM user_master um
                                    WHERE
                                      user_id = ?', [$user_id]);

            $response = $result[0]->$column;

        } catch (Exception $e) {
            $response = Response::json(array('code' => 201, 'message' => $e->getMessage(), 'cause' => '', 'response' => json_decode("{}")));
        }
        return $response;
    }

    // checkIfSubCategoryExist
    public function checkIfSubCategoryExist($sub_category_name, $id)
    {
        try {
            if ($id != 0) {
                $result = DB::select('SELECT *
                                  FROM sub_category
                                  WHERE name = ? AND id != ?', [$sub_category_name, $id]);
            } else {
                $result = DB::select('SELECT *
                                  FROM sub_category
                                  WHERE name = ?', [$sub_category_name]);
            }

            if (count($result) > 0) {
                $response = Response::json(array('code' => 201, 'message' => 'Sub category already exist.', 'cause' => '', 'data' => json_decode("{}")));
            } else {
                $response = '';
            }
        } catch (Exception $e) {
            $response = Response::json(array('code' => 201, 'message' => $e->getMessage(), 'cause' => '', 'data' => json_decode("{}")));
            Log::error("checkIfSubCategoryExist Exception :", ['Exception' => $e->getMessage(), "\nTraceAsString :" => $e->getTraceAsString()]);
        }
        return $response;
    }

    public function checkIfAdvertisementExist($url)
    {
        try {
            $result = DB::select('SELECT * from advertise_links WHERE url = ?', [$url]);
            if (count($result) >= 1) {
                $response = Response::json(array('code' => 201, 'message' => 'Advertisement already exist.', 'cause' => '', 'response' => json_decode("{}")));
            } else {
                $response = '';
            }
        } catch (Exception $e) {
            Log::error(["Exception :", $e->getMessage(), "TraceAsString :", $e->getTraceAsString()]);
            return Response::json(array('code' => 201, 'message' => $e->getMessage(), 'cause' => '', 'response' => json_decode("{}")));
        }

        return $response;
    }

    public function checkIfPromoCodeExist($promo_code, $package_name)
    {
        try {
            $result = DB::select('SELECT * from promocode_master WHERE promo_code = ?', [$promo_code, $package_name]);
            if (count($result) >= 1) {
                $response = Response::json(array('code' => 201, 'message' => 'Promo code already exists.', 'cause' => '', 'response' => json_decode("{}")));
            } else {
                $response = '';
            }
        } catch (Exception $e) {
            Log::error("checkIfPromoCodeExist Exception :", ['Exception' => $e->getMessage(), "\nTraceAsString :" => $e->getTraceAsString()]);
            return Response::json(array('code' => 201, 'message' => $e->getMessage(), 'cause' => '', 'response' => json_decode("{}")));
        }

        return $response;
    }

    //validateItemCount
    public function validateItemCount($item_count)
    {
        try {

            if ($item_count < 3 or $item_count > 200) {
                $response = Response::json(array('code' => 201, 'message' => 'Item count must be >= 3 and <= 200.', 'cause' => '', 'response' => json_decode("{}")));
            } else {
                $response = '';
            }
        } catch (Exception $e) {
            Log::error("validateItemCount Exception :", ['Exception' => $e->getMessage(), "\nTraceAsString :" => $e->getTraceAsString()]);
            return Response::json(array('code' => 201, 'message' => $e->getMessage(), 'cause' => '', 'response' => json_decode("{}")));
        }

        return $response;
    }

    //validateItemCount
    public function validateAdvertiseServerId($server_id)
    {
        try {

            $result = DB::select('SELECT * from sub_category_advertise_server_id_master WHERE server_id = ?', [$server_id]);
            if (count($result) >= 1) {
                $response = Response::json(array('code' => 201, 'message' => 'Server id already exists.', 'cause' => '', 'response' => json_decode("{}")));
            } else {
                $response = '';
            }
        } catch (Exception $e) {
            Log::error("validateAdvertiseServerId Exception :", ['Exception' => $e->getMessage(), "\nTraceAsString :" => $e->getTraceAsString()]);
            return Response::json(array('code' => 201, 'message' => $e->getMessage(), 'cause' => '', 'response' => json_decode("{}")));
        }

        return $response;
    }

    //verifySearchCategory
    public function verifySearchCategory($search_category)
    {
        try {
            $count = 0;
            $array_of_search_text = (explode(",", $search_category));
            $result = array();
            foreach ($array_of_search_text as $key) {
                if (!in_array($key, $result) == true) {
                    $result[] = $key;
                } else {
                    $count = $count + 1;
                }
            }

            if ($count > 0) {
                return $response = Response::json(array('code' => 201, 'message' => 'Please remove duplicate entry of tag from selection.', 'cause' => '', 'response' => json_decode("{}")));
            } else {
                $response = '';
            }
        } catch (Exception $e) {
            Log::error("verifySearchCategory Exception :", ['Exception' => $e->getMessage(), "\nTraceAsString :" => $e->getTraceAsString()]);
            return Response::json(array('code' => 201, 'message' => $e->getMessage(), 'cause' => '', 'response' => json_decode("{}")));
        }

        return $response;
    }

    public function validateIssetRequiredParameter($required_fields, $request_params)
    {
        $error = false;
        $error_fields = '';

        foreach ($required_fields as $key => $value) {
            if (isset($request_params->$value)) {
                if (!is_object($request_params->$value)) {
                    if (strlen($request_params->$value) == 0) {
                        $error = true;
                        $error_fields .= ' ' . $value . ',';
                    }
                }
            }
        }

        if ($error) {
            // Required field(s) are missing or empty
            $error_fields = substr($error_fields, 0, -1);
            $message = 'Required field(s)' . $error_fields . ' is missing or empty.';
            $response = Response::json(array('code' => 201, 'message' => $message, 'cause' => '', 'data' => json_decode("{}")));
        } else
            $response = '';

        return $response;
    }

    // checkIfCatalogExist
    public function checkIfCatalogExist($sub_category_id, $catalog_name, $catalog_id)
    {
        try {
            if ($catalog_id) {
                $result = DB::select('SELECT
                                      ct.id
                                    FROM
                                      catalog_master as ct,
                                      sub_category_catalog as sct
                                    WHERE
                                      sct.sub_category_id = ? AND
                                      sct.catalog_id = ct.id AND
                                      ct.name = ? AND
                                      ct.id != ? AND
                                      sct.is_active = 1', [$sub_category_id, trim($catalog_name), $catalog_id]);
            } else {
                $result = DB::select('SELECT
                                      ct.id
                                    FROM
                                      catalog_master as ct,
                                      sub_category_catalog as sct
                                    WHERE
                                      sct.sub_category_id = ? AND
                                      sct.catalog_id = ct.id AND
                                      ct.name = ? AND
                                      sct.is_active = 1', [$sub_category_id, trim($catalog_name)]);
            }

            if (count($result) > 0) {
                $response = Response::json(array('code' => 201, 'message' => "'$catalog_name' already exist in this category.", 'cause' => '', 'data' => json_decode("{}")));
            } else {
                $response = '';
            }
        } catch (Exception $e) {
            $response = Response::json(array('code' => 201, 'message' => $e->getMessage(), 'cause' => '', 'data' => json_decode("{}")));
            Log::error("checkIfCatalogExist Exception :", ['Exception' => $e->getMessage(), "\nTraceAsString :" => $e->getTraceAsString()]);
        }
        return $response;
    }

    //checkIsFontExist
    public function checkIsFontExist($file_array)
    {
        try {

            $file_name = $file_array->getClientOriginalName();
            $temp_directory = Config::get('constant.TEMP_FILE_DIRECTORY');

            $file_path = '../..' . $temp_directory . $file_name;
            $destination_path = '../..' . $temp_directory;
            $file_array->move($destination_path, $file_name);

            $font = Font::load($file_path);
            $FontFullName = $font->getFontFullName();
            $font->close();

            $result = DB::select('SELECT
                                      cm.id AS catalog_id,
                                      cm.name
                                    FROM
                                      font_master AS fm,
                                      catalog_master AS cm
                                    WHERE
                                      fm.catalog_id = cm.id AND
                                      (fm.font_file = ? OR fm.font_name = ?)', [$file_name, $FontFullName]);

            if (count($result) > 0) {
                (new ImageController())->unlinkFileFromLocalStorage($file_name, $temp_directory);
                $catalog_name = $result[0]->name;
                $response = Response::json(array('code' => 420, 'message' => "Font already exist in '$catalog_name' category.", 'cause' => '', 'data' => json_decode("{}")));
            } else {
                $response = '';
            }

        } catch (Exception $e) {
            Log::error("checkIsFontExist Error :", ['Error : ' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'check font is exist or not.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    //getFontName
    public function getFontName($file_name)
    {
        try {

            $file_path = '../..' . Config::get('constant.FONT_FILE_DIRECTORY') . $file_name;

            $font = Font::load($file_path);
            //$font->parse();  // for getFontWeight() to work this call must be done first!
            $FontFullName = $font->getFontFullName();
            $font->close(); //This is must be compulsory to close font object
            return $FontFullName;
            /*$FontName = $font->getFontName();
            $FontSubfamily = $font->getFontSubfamily();
            $FontSubfamilyID = $font->getFontSubfamilyID();
            $FontVersion = $font->getFontVersion();
            $FontWeight = $font->getFontWeight();
            $FontPostscriptName = $font->getFontPostscriptName();
            $result = array(
                'FontName' => $FontName,
                'FontSubfamily' => $FontSubfamily,
                'FontSubfamilyID' => $FontSubfamilyID,
                'FontFullName' => $FontFullName,
                'FontVersion' => $FontVersion,
                'FontWeight' => $FontWeight,
                'FontPostscriptName' => $FontPostscriptName,
            );*/


        } catch (Exception $e) {
            Log::error("getFontName Error :", ['Error : ' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
        }
    }

}

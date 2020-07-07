<?php

//////////////////////////////////////////////////////////////////////////////
//                   OptimumBrew Technology Pvt. Ltd.                       //
//                                                                          //
// Title:            Photo Editor Lab                                       //
// File:             VerificationController.php                             //
// Modified:         17-June-2019                                           //
//                                                                          //
// Author:           Pinal Patel                                            //
// Email:            pinal.optimumbrew@gmail.com                            //
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
    /* ==================================| Common verifications for all projects |====================================*/

    //validate required and empty field
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
            $response = Response::json(array('code' => 201, 'message' => $message, 'cause' => '', 'data' => json_decode("{}")));
        } else
            $response = '';

        return $response;
    }

    //validate required array field
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
            $response = Response::json(array('code' => 201, 'message' => $message, 'cause' => '', 'data' => json_decode("{}")));
        } else
            $response = '';

        return $response;
    }

    //validate required field
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
            $response = Response::json(array('code' => 201, 'message' => $message, 'cause' => '', 'data' => json_decode("{}")));
        } else
            $response = '';
        return $response;
    }

    //validate required parameter by isset(), it will returns error when parameter is set but value is empty
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

    //check if user is active
    public function checkIfUserIsActive($email_id)
    {
        try {

            $result = DB::select('SELECT
                                        um.is_active
                                        FROM user_master um
                                        WHERE um.email_id = ?', [$email_id]);
            $response = ($result[0]->is_active == '1') ? '' : Response::json(array('code' => 201, 'message' => 'You are inactive user. Please contact to administrator.', 'cause' => '', 'data' => json_decode("{}")));

        } catch (Exception $e) {
            Log::error("checkIfUserIsActive : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'validate active user.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    //check if user is exist
    public function checkIfUserExist($user_id)
    {
        try {
            $result = DB::select('SELECT 1 FROM user_master WHERE email_id = ?', [$user_id]);
            $response = (sizeof($result) != 0) ? 1 : 0;

        } catch (Exception $e) {
            Log::error("checkIfUserExist : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'validate user.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    //verify user by email & role
    public function verifyUser($email_id, $role_name)
    {
        try {
            $result = DB::select('SELECT r.name,um.is_active
                                  FROM role_user ru, roles r, user_master um
                                  WHERE r.id = ru.role_id AND
                                        um.id = ru.user_id AND
                                        um.email_id = ?', [$email_id]);

            if(count($result) > 0 && $result[0]->name != $role_name){
               $response = Response::json(array('code' => 201, 'message' => 'Unauthorized user.', 'cause' => '', 'data' => json_decode("{}")));
            }elseif (count($result) > 0 && $result[0]->is_active != '1'){
                $response = Response::json(array('code' => 201, 'message' => 'You are inactive user. Please contact to administrator.', 'cause' => '', 'data' => json_decode("{}")));
            }elseif (count($result) == 0){
                $response = Response::json(array('code' => 201, 'message' => 'Invalid email id or password.', 'cause' => '', 'data' => json_decode("{}")));
            }else{
                $response = "";
            }
//            $response = (sizeof($result) > 0 && $result[0]->name == $role_name) ? '' : Response::json(array('code' => 201, 'message' => 'Unauthorized user.', 'cause' => '', 'data' => json_decode("{}")));
        } catch (Exception $e) {
            Log::error("verifyUser : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'verify user.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    //get user role
    public function getUserRole($user_id)
    {
        try {
            $result = DB::select('SELECT
                                        r.name
                                        FROM role_user ru, user_master um, roles r
                                        WHERE
                                          um.id = ru.user_id AND
                                          ru.role_id = r.id AND
                                          um.id = ?', [$user_id]);

            $response = (count($result) > 0) ? $result[0]->name : '';

        } catch (Exception $e) {
            Log::error("getUserRole : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get user role.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    /* ==========================================| Customize verifications |==========================================*/

    //check sub_category is exist or not
    public function checkIsSubCategoryExist($sub_category_name, $id)
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
            Log::error("checkIsSubCategoryExist : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'validate sub_category.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    //check advertise url/link is exist or not
    public function checkIsAdvertiseLinkExist($url)
    {
        try {
            $result = DB::select('SELECT * from advertise_links WHERE url = ?', [$url]);
            if (count($result) >= 1) {
                $response = Response::json(array('code' => 201, 'message' => 'Advertisement already exist.', 'cause' => '', 'data' => json_decode("{}")));
            } else {
                $response = '';
            }
        } catch (Exception $e) {
            Log::error("checkIsAdvertiseLinkExist : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'validate advertise link.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    //check promo code is exist or not
    public function checkIsPromoCodeExist($promo_code, $package_name)
    {
        try {
            $result = DB::select('SELECT * from promocode_master WHERE promo_code = ?', [$promo_code, $package_name]);
            if (count($result) >= 1) {
                $response = Response::json(array('code' => 201, 'message' => 'Promo code already exists.', 'cause' => '', 'data' => json_decode("{}")));
            } else {
                $response = '';
            }
        } catch (Exception $e) {
            Log::error("checkIsPromoCodeExist : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'validate promo code.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));

        }

        return $response;
    }

    //validate item count
    public function validateItemCount($item_count)
    {
        try {

            if ($item_count < 3 or $item_count > 200) {
                $response = Response::json(array('code' => 201, 'message' => 'Item count must be >= 3 and <= 200.', 'cause' => '', 'data' => json_decode("{}")));
            } else {
                $response = '';
            }
        } catch (Exception $e) {
            Log::error("validateItemCount : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'validate item count.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    //check advertise server_id is exist or not
    public function checkIsAdvertiseServerIdExist($server_id)
    {
        try {

            $result = DB::select('SELECT * from sub_category_advertise_server_id_master WHERE server_id = ?', [$server_id]);
            if (count($result) >= 1) {
                $response = Response::json(array('code' => 201, 'message' => 'Server id already exists.', 'cause' => '', 'data' => json_decode("{}")));
            } else {
                $response = '';
            }
        } catch (Exception $e) {
            Log::error("checkIsAdvertiseServerIdExist : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'validate server id.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    //verify search category
    public function verifySearchCategory($search_category)
    {
        try {
            $count = 0;
            $array_of_search_text = (explode(",", strtolower($search_category)));
            $result = array();
            $repeated_tags = array();
            foreach ($array_of_search_text as $key) {
                if (!in_array($key, $result) == true) {
                    $result[] = $key;
                } else {
                    $count = $count + 1;
                    $repeated_tags[] = $key;
                }
            }
            //Log::info('verifySearchCategory search_tags : ',['search_tags' => implode(',',$result)]);

            if ($count > 0) {
                return $response = Response::json(array('code' => 201, 'message' => 'Please remove duplicate entry of "'. implode(',',$repeated_tags) .'" from tag selection.', 'cause' => '', 'data' => ['search_tags' => implode(',',$result)]));
            } else {
                $response = '';
            }
        } catch (Exception $e) {
            Log::error("verifySearchCategory : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'verify search category.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    //validate catalog is exist or not
    public function checkIsCatalogExist($sub_category_id, $catalog_name, $catalog_id)
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
                                      sct.is_active = ?', [$sub_category_id, trim($catalog_name), $catalog_id, 1]);
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
                                      sct.is_active = ?', [$sub_category_id, trim($catalog_name), 1]);
            }

            if (count($result) > 0) {
                $response = Response::json(array('code' => 201, 'message' => "'$catalog_name' already exist in this category.", 'cause' => '', 'data' => json_decode("{}")));
            } else {
                $response = '';
            }
        } catch (Exception $e) {
            Log::error("checkIsCatalogExist : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'validate catalog.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    //check font is exist or not
    public function checkIsFontExist($file_array)
    {
        try {

            //$file_name = $file_array->getClientOriginalName();
            $file_name = str_replace(" ", "", strtolower($file_array->getClientOriginalName()));
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
            Log::error("checkIsFontExist : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'check font is exist or not.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    //get font name
    public function getFontName($file_name)
    {
        try {

            $file_path = '../..' . Config::get('constant.FONT_FILE_DIRECTORY') . $file_name;

            $font = Font::load($file_path);
            //$font->parse();  // for getFontWeight() to work this call must be done first!
            $FontFullName = $font->getFontFullName();
            $font->close(); //This is must be compulsory to close font object
            $response = $FontFullName;
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
            Log::error("getFontName : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get font name.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    //Validate string to restrict some special characters
    public function verifySearchText($text)
    {

        /*
         * Here following special characters are restricted
         * @%*()-+\'"<>/
         * also only allow some special characters & alphanumeric values
         * */

        $string_array = str_split($text);
        foreach ($string_array as $key)
        {
            $is_valid = preg_match ('/[[:alnum:] `!#$₹^&_={}[]|:;,.?]+/', $key);
            if($is_valid == 0)
            {
                return $is_valid;
            }
        }

        //return 1 if search text is valid 0 otherwise
        return $is_valid;
    }

}

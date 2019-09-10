<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UnsedAPIsController extends Controller
{
    //

    /*This API is unused bcz no need to get list of catalog to link catalog functionality*/
    /**
     * @api {post} getAllCatalog   getAllCatalog
     * @apiName getAllCatalog
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Catalog Fetched Successfully.",
     * "cause": "",
     * "data": {
     * "total_record": 2,
     * "catalog_list": [
     * {
     * "catalog_id": 17,
     * "sub_category_id": 4,
     * "name": "Dragon Tattoo",
     * "thumbnail_img": "http://192.168.0.102/ob_photolab_backend/image_bucket/thumbnail/59895317393b0_catalog_img_1502171927.png",
     * "compressed_img": "http://192.168.0.102/ob_photolab_backend/image_bucket/compressed/59895317393b0_catalog_img_1502171927.png",
     * "original_img": "http://192.168.0.102/ob_photolab_backend/image_bucket/original/59895317393b0_catalog_img_1502171927.png",
     * "is_free": 1
     * },
     * {
     * "catalog_id": 17,
     * "sub_category_id": 10,
     * "name": "Dragon Tattoo",
     * "thumbnail_img": "http://192.168.0.102/ob_photolab_backend/image_bucket/thumbnail/59895317393b0_catalog_img_1502171927.png",
     * "compressed_img": "http://192.168.0.102/ob_photolab_backend/image_bucket/compressed/59895317393b0_catalog_img_1502171927.png",
     * "original_img": "http://192.168.0.102/ob_photolab_backend/image_bucket/original/59895317393b0_catalog_img_1502171927.png",
     * "is_free": 1
     * }
     * ]
     * }
     * }
     */
    public function getAllCatalog()
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            if (!Cache::has("pel:getAllCatalog")) {
                $result = Cache::rememberforever("getAllCatalog", function () {

                    $total_row_result = DB::select('SELECT COUNT(*) as total FROM  sub_category_catalog WHERE is_active = ?', [1]);
                    $total_row = $total_row_result[0]->total;

                    $result = DB::select('SELECT
                                        ct.id as catalog_id,
                                        sct.sub_category_id,
                                        ct.name,
                                        IF(ct.image != "",CONCAT("' . Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.image),"") as thumbnail_img,
                                        IF(ct.image != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.image),"") as compressed_img,
                                        IF(ct.image != "",CONCAT("' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.image),"") as original_img,
                                        ct.is_free
                                      FROM
                                        catalog_master as ct,
                                        sub_category_catalog as sct
                                      WHERE
                                        sct.catalog_id=ct.id AND
                                        sct.is_active=1
                                      order by ct.updated_at DESC');

                    return array('total_record' => $total_row, 'catalog_list' => $result);
                });
            }

            $redis_result = Cache::get("getAllCatalog");

            if (!$redis_result) {
                $redis_result = [];
            }

            $response = Response::json(array('code' => 200, 'message' => 'Catalogs fetched successfully.', 'cause' => '', 'data' => $redis_result));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            Log::error("getAllCatalog : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get all catalogs.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    public function addJsonOld(Request $request_body)
    {

        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->input('request_data'));

            if (($response = (new VerificationController())->validateRequiredParameter(array('catalog_id', 'is_featured', 'is_free'), $request)) != '')
                return $response;

            $catalog_id = $request->catalog_id;
            //dd($json_list);

            $json_data = $request->json_data;
            $is_free = $request->is_free;
            $is_featured = $request->is_featured;
            $is_portrait = isset($request->is_portrait) ? $request->is_portrait : NULL;
            $created_at = date('Y-m-d H:i:s');

            //Log::info('request_data', ['request_data' => $request]);


            DB::beginTransaction();
            if (!$request_body->hasFile('file')) {
                return Response::json(array('code' => 201, 'message' => 'Required field file is missing or empty.', 'cause' => '', 'data' => json_decode("{}")));
            } else {

                $image_array = Input::file('file');
                //return $images_array;
                if (($response = (new ImageController())->verifyImage($image_array)) != '')
                    return $response;

                $catalog_image = (new ImageController())->generateNewFileName('json_image', $image_array);
                //(new ImageController())->saveOriginalImage($background_img);

                $original_path = '../..' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY');
                $image_array->move($original_path, $catalog_image);

                (new ImageController())->saveCompressedImage($catalog_image);
                (new ImageController())->saveThumbnailImage($catalog_image);
                //(new ImageController())->saveImageInToS3($catalog_image);


                DB::insert('INSERT
                                INTO
                                  images(catalog_id, image, json_data, is_free, is_featured, is_portrait, created_at)
                                VALUES(?, ?, ?, ?, ?, ?, ?) ', [$catalog_id, $catalog_image, json_encode($json_data), $is_free, $is_featured, $is_portrait, $created_at]);


                DB::commit();
            }


            $response = Response::json(array('code' => 200, 'message' => 'Json added successfully!.', 'cause' => '', 'data' => json_decode('{}')));

        } catch
        (Exception $e) {
            Log::error("addJson : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'add json.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    public function editJsonDataOld(Request $request_body)
    {

        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->input('request_data'));

            if (($response = (new VerificationController())->validateRequiredParameter(array('is_featured', 'is_free', 'img_id'), $request)) != '')
                return $response;

            //$catalog_id = $request->catalog_id;
            //dd($json_list);
            $img_id = $request->img_id;
            $json_data = isset($request->json_data) ? $request->json_data : '';
            $is_free = $request->is_free;
            $is_featured = $request->is_featured;
            $is_portrait = isset($request->is_portrait) ? $request->is_portrait : 0;
            $created_at = date('Y-m-d H:i:s');

            //Log::info('request_data', ['request_data' => $request]);


            DB::beginTransaction();
            if ($request_body->hasFile('file')) {
                $image_array = Input::file('file');
                //return $images_array;
                if (($response = (new ImageController())->verifyImage($image_array)) != '')
                    return $response;

                $catalog_image = (new ImageController())->generateNewFileName('json_image', $image_array);
                (new ImageController())->saveOriginalImage($catalog_image);

                /*$original_path = '../..' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY');
                $image_array->move($original_path, $catalog_image);*/

                (new ImageController())->saveCompressedImage($catalog_image);
                (new ImageController())->saveThumbnailImage($catalog_image);
                //(new ImageController())->saveImageInToS3($catalog_image);

                //Log::info('Encoded json_data', ['json_data' => json_encode($json_data)]);

                DB::update('UPDATE
                                images SET image = ?, json_data = ?, is_free = ?, is_featured = ?, is_portrait = ?
                                WHERE id = ?', [$catalog_image, json_encode($json_data), $is_free, $is_featured, $is_portrait, $img_id]);

            } else {

                DB::update('UPDATE
                                images SET json_data = ?, is_free = ?, is_featured = ?, is_portrait = ?
                                WHERE id = ?', [json_encode($json_data), $is_free, $is_featured, $is_portrait, $img_id]);


            }
            DB::commit();

            $response = Response::json(array('code' => 200, 'message' => 'Json data updated successfully!.', 'cause' => '', 'data' => json_decode('{}')));

        } catch
        (Exception $e) {
            Log::error("editJsonData : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'edit json data.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /* ====================================| Add Advertise |===========================================*/
    /*This API is unused in photo_editor_lab bcz change the functionally to add advertise link*/
    /**
     * @api {post} getAdvertiseLink   getAdvertiseLink
     * @apiName getAdvertiseLink
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "sub_category_id":31,
     * "platform":"ios"
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Advertise Link Fetched Successfully.",
     * "cause": "",
     * "data": {
     * "link_list": [
     * {
     * "advertise_link_id": 44,
     * "name": "Suitescene",
     * "platform": "iOS",
     * "linked": 1
     * },
     * {
     * "advertise_link_id": 41,
     * "name": "Bhavesh Gabani",
     * "platform": "iOS",
     * "linked": 0
     * },
     * {
     * "advertise_link_id": 40,
     * "name": "Visa",
     * "platform": "iOS",
     * "linked": 0
     * },
     * {
     * "advertise_link_id": 39,
     * "name": "QR Code Scanner : Barcode QR-Code Generator App",
     * "platform": "iOS",
     * "linked": 0
     * },
     * {
     * "advertise_link_id": 38,
     * "name": "PhotoEditorLab � Stickers , Filters & Frames",
     * "platform": "iOS",
     * "linked": 0
     * },
     * {
     * "advertise_link_id": 37,
     * "name": "QR Barcode Scanner : QR Bar Code Generator App",
     * "platform": "iOS",
     * "linked": 0
     * },
     * {
     * "advertise_link_id": 36,
     * "name": "Cut Paste - Background Eraser",
     * "platform": "iOS",
     * "linked": 0
     * }
     * ]
     * }
     * }
     */
    public function getAdvertiseLink(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());

            if (($response = (new VerificationController())->validateRequiredParameter(array('sub_category_id', 'platform'), $request)) != '')
                return $response;

            $this->sub_category_id = $request->sub_category_id;
            $this->platform = $request->platform;

            if (!Cache::has("pel:getAdvertiseLink$this->sub_category_id:$this->platform")) {
                $result = Cache::rememberforever("getAdvertiseLink$this->sub_category_id:$this->platform", function () {
                    return DB::select('SELECT
                                          adl.id AS advertise_link_id,
                                          adl.name,
                                          adl.platform,
                                          IF((SELECT advertise_link_id
                                              FROM sub_category_advertise_links AS scal
                                              WHERE sub_category_id = ? AND adl.id = scal.advertise_link_id AND scal.is_active = 1
                                              LIMIT 1), 1, 0) AS linked
                                        FROM
                                          advertise_links AS adl
                                        WHERE
                                          adl.is_active = 1 AND
                                          adl.platform = ?
                                        ORDER BY adl.updated_at DESC', [$this->sub_category_id, $this->platform]);
                });
            }

            $redis_result = Cache::get("getAdvertiseLink$this->sub_category_id:$this->platform");

            if (!$redis_result) {
                $redis_result = [];
            }

            $response = Response::json(array('code' => 200, 'message' => 'Advertise links fetched successfully.', 'cause' => '', 'data' => ['link_list' => $redis_result]));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            Log::error("getAdvertiseLink : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get advertise link.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    /**
     * @api {post} addAdvertiseLink addAdvertiseLink
     * @apiName addAdvertiseLink
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     *  Key: Authorization
     *  Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "advertise_link_id":2,
     * "sub_category_id":31
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Advertise Linked Successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function addAdvertiseLink(Request $request)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('advertise_link_id', 'sub_category_id'), $request)) != '')
                return $response;

            $advertise_link_id = $request->advertise_link_id;
            $sub_category_id = $request->sub_category_id;
            $create_at = date('Y-m-d H:i:s');

            DB::beginTransaction();
            DB::insert('INSERT INTO sub_category_advertise_links(sub_category_id,advertise_link_id,created_at) VALUES (?, ?, ?)', [$sub_category_id, $advertise_link_id, $create_at]);
            DB::commit();

            $response = Response::json(array('code' => 200, 'message' => 'Advertise linked successfully.', 'cause' => '', 'data' => json_decode('{}')));
        } catch (Exception $e) {
            Log::error("addAdvertiseLink : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'link advertisement.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /**
     * @api {post} unlinkAdvertise unlinkAdvertise
     * @apiName unlinkAdvertise
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     *  Key: Authorization
     *  Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "advertise_link_id":2,
     * "sub_category_id":31
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Advertise unLinked Successfully!.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function unlinkAdvertise(Request $request)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('advertise_link_id', 'sub_category_id'), $request)) != '')
                return $response;

            $advertise_link_id = $request->advertise_link_id;
            $sub_category_id = $request->sub_category_id;
            $is_active = 0;

            $result = DB::select('SELECT sub_category_id FROM sub_category_advertise_links WHERE sub_category_id = ? AND is_active = 1', [$sub_category_id]);

            if (sizeOf($result) > 1) {
                DB::beginTransaction();
                DB::update('UPDATE sub_category_advertise_links SET is_active = ? WHERE sub_category_id = ? AND advertise_link_id = ? ', [$is_active, $sub_category_id, $advertise_link_id]);
                DB::commit();
                $response = Response::json(array('code' => 200, 'message' => 'Advertise unlinked successfully.', 'cause' => '', 'data' => json_decode('{}')));

            } else {
                $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'unLink this advertise, it is not linked with any other app.', 'cause' => '', 'data' => json_decode("{}")));

            }

        } catch (Exception $e) {
            Log::error("unlinkAdvertise : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'unLinked advertisement.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /* =========================================| User |===============================================*/
    /*This API is unused because As it never uses the user's device information*/
    /**
     * @api {post} getAllUser   getAllUser
     * @apiName getAllUser
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "sub_category_id":1,
     * "page":1,
     * "item_count":30,
     * "order_by":"device_id",//optinal
     * "order_type":"ASC"//optional
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "All User Fetched Successfully.",
     * "cause": "",
     * "data": {
     * "total_record": 15,
     * "list_user": [
     * {
     * "device_id": 15,
     * "device_reg_id": "AD64EF08F134750CC09D489F90B9DFF623A91FC84AD3A9468A068E679B5C83D7",
     * "device_platform": "ios",
     * "device_model_name": "iPhone",
     * "device_vendor_name": "Apple",
     * "device_os_version": "10.0.2",
     * "device_udid": "A70B3F58-75EF-4C76-ADEC-14E93D57887E",
     * "device_resolution": "320.0568.0",
     * "device_carrier": "",
     * "device_country_code": "US",
     * "device_language": "en-US",
     * "device_local_code": "en-US",
     * "device_default_time_zone": "Pacific/Chatham",
     * "device_library_version": "",
     * "device_application_version": "1.0.3",
     * "device_type": "phone",
     * "device_registration_date": "2017-05-12 05:07:33 +0000",
     * "is_active": 1,
     * "is_count": 5,
     * "create_time": "2017-06-03 10:38:03",
     * "update_time": "2017-06-24 13:17:14"
     * },
     * {
     * "device_id": 14,
     * "device_reg_id": "",
     * "device_platform": "ios",
     * "device_model_name": "iPhone",
     * "device_vendor_name": "Apple",
     * "device_os_version": "10.3.1",
     * "device_udid": "C104766F-B9CE-49D8-BEB3-101F04E849CF",
     * "device_resolution": "375.0667.0",
     * "device_carrier": "",
     * "device_country_code": "US",
     * "device_language": "en-US",
     * "device_local_code": "en-US",
     * "device_default_time_zone": "Asia/Kolkata",
     * "device_library_version": "",
     * "device_application_version": "1.0.3",
     * "device_type": "phone",
     * "device_registration_date": "2017-05-24 05:43:18 +0000",
     * "is_active": 1,
     * "is_count": 5,
     * "create_time": "2017-05-24 11:13:18",
     * "update_time": "2017-06-24 13:17:14"
     * }
     * ]
     * }
     * }
     */
    public function getAllUser(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('sub_category_id', 'page', 'item_count'), $request)) != '')
                return $response;

            $this->sub_category_id = $request->sub_category_id;
            $this->item_count = $request->item_count;
            $this->page = $request->page;
            $this->order_by = isset($request->order_by) ? $request->order_by : 'device_id';
            $this->order_type = isset($request->order_type) ? $request->order_type : 'DESC';
            $this->offset = ($this->page - 1) * $this->item_count;


            if (!Cache::has("pel:getAllUser$this->page:$this->item_count:$this->order_by:$this->order_type:$this->sub_category_id")) {
                $result = Cache::rememberforever("getAllUser$this->page:$this->item_count:$this->order_by:$this->order_type:$this->sub_category_id", function () {

                    $total_row_result = DB::select('SELECT COUNT(*) as total FROM  device_master where sub_category_id = ?', [$this->sub_category_id]);
                    $total_row = $total_row_result[0]->total;

                    $result = DB::select('SELECT
                                    dm.device_id,
                                    dm.device_reg_id,
                                    dm.device_platform,
                                    dm.device_model_name,
                                    dm.device_vendor_name,
                                    dm.device_os_version,
                                    dm.device_udid,
                                    dm.device_resolution,
                                    dm.device_carrier,
                                    dm.device_country_code,
                                    dm.device_language,
                                    dm.device_local_code,
                                    dm.device_default_time_zone,
                                    dm.device_library_version,
                                    dm.device_application_version,
                                    dm.device_type,
                                    dm.device_registration_date,
                                    dm.is_active,
                                    dm.is_count,
                                    dm.create_time,
                                    dm.update_time
                                  FROM
                                  device_master AS dm
                                  WHERE
                                  dm.sub_category_id = ?
                                  ORDER BY dm.' . $this->order_by . ' ' . $this->order_type . '
                                  LIMIT ?,?', [$this->sub_category_id, $this->offset, $this->item_count]);

                    return array('total_record' => $total_row, 'list_user' => $result);
                });
            }

            $redis_result = Cache::get("getAllUser$this->page:$this->item_count:$this->order_by:$this->order_type:$this->sub_category_id");

            if (!$redis_result) {
                $redis_result = [];
            }


            $response = Response::json(array('code' => 200, 'message' => 'All users fetched successfully.', 'cause' => '', 'data' => $redis_result));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            Log::error("getAllUser : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get all user.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    /**
     * @api {post} getPurchaseUser   getPurchaseUser
     * @apiName getPurchaseUser
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "sub_category_id":1,
     * "page":1,
     * "item_count":30,
     * "order_by":"order_number",//optional
     * "order_type":"ASC"//optional
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "All Purchase User Fetched Successfully.",
     * "cause": "",
     * "data": {
     * "total_record": 9,
     * "list_user": [
     * {
     * "order_number": "Null1494507231573",
     * "tot_order_amount": 2.99,
     * "currency_code": "USD",
     * "device_platform": "android",
     * "create_time": "2017-05-11 18:23:49"
     * },
     * {
     * "order_number": "Null1494508061564",
     * "tot_order_amount": 2.99,
     * "currency_code": "USD",
     * "device_platform": "android",
     * "create_time": "2017-05-11 18:37:39"
     * }
     * ]
     * }
     * }
     */
    public function getPurchaseUser(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('sub_category_id', 'page', 'item_count'), $request)) != '')
                return $response;

            $this->sub_category_id = $request->sub_category_id;
            $this->item_count = $request->item_count;
            $this->page = $request->page;
            $this->order_by = isset($request->order_by) ? $request->order_by : 'id';
            $this->order_type = isset($request->order_type) ? $request->order_type : 'DESC';
            $this->offset = ($this->page - 1) * $this->item_count;

            if (!Cache::has("pel:getPurchaseUser$this->page:$this->item_count:$this->order_by:$this->order_type:$this->sub_category_id")) {
                $result = Cache::rememberforever("getPurchaseUser$this->page:$this->item_count:$this->order_by:$this->order_type:$this->sub_category_id", function () {

                    $total_row_result = DB::select('SELECT COUNT(*) as total FROM  order_master where sub_category_id = ?', [$this->sub_category_id]);
                    $total_row = $total_row_result[0]->total;

                    $result = DB::select('SELECT
                                        om.order_number,
                                        om.tot_order_amount,
                                        om.currency_code,
                                        om.device_platform,
                                        om.create_time
                                      FROM
                                      order_master AS om
                                      WHERE
                                      om.sub_category_id = ?
                                      order by om.' . $this->order_by . ' ' . $this->order_type . '
                                      LIMIT ?,?', [$this->sub_category_id, $this->offset, $this->item_count]);

                    return array('total_record' => $total_row, 'list_user' => $result);
                });
            }

            $redis_result = Cache::get("getPurchaseUser$this->page:$this->item_count:$this->order_by:$this->order_type:$this->sub_category_id");

            if (!$redis_result) {
                $redis_result = [];
            }
            $response = Response::json(array('code' => 200, 'message' => 'All purchased user fetched successfully.', 'cause' => '', 'data' => $redis_result));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            Log::error("getPurchaseUser : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get purchased user.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    /**
     * @api {post} getAllRestoreDevice   getAllRestoreDevice
     * @apiName getAllRestoreDevice
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "sub_category_id":1,
     * "page":1,
     * "item_count":30,
     * "order_by":"order_number",
     * "order_type":"ASC"
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "App Restore Device Fetched Successfully.",
     * "cause": "",
     * "data": {
     * "total_record": 9,
     * "is_next_page": false,
     * "list_device": [
     * {
     * "id": 1,
     * "order_number": "Null1494507231573",
     * "device_udid": "ff505a7a500c931a",
     * "restore": 1,
     * "create_time": "2017-05-11 12:53:49",
     * "update_time": "2017-05-11 18:23:49"
     * },
     * {
     * "id": 2,
     * "order_number": "Null1494508061564",
     * "device_udid": "ff505a7a500c931a",
     * "restore": 1,
     * "create_time": "2017-05-11 13:07:39",
     * "update_time": "2017-05-11 18:37:39"
     * },
     * {
     * "id": 3,
     * "order_number": "Null1494508092829",
     * "device_udid": "ff505a7a500c931a",
     * "restore": 1,
     * "create_time": "2017-05-11 13:08:11",
     * "update_time": "2017-05-11 18:38:11"
     * }
     * ]
     * }
     * }
     */
    public function getAllRestoreDevice(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('sub_category_id', 'item_count'), $request)) != '')
                return $response;

            $this->sub_category_id = $request->sub_category_id;
            $this->item_count = $request->item_count;
            $this->page = $request->page;
            $this->order_by = isset($request->order_by) ? $request->order_by : 'id';
            $this->order_type = isset($request->order_type) ? $request->order_type : 'DESC';
            $this->offset = ($this->page - 1) * $this->item_count;


            if (!Cache::has("pel:getAllRestoreDevice$this->page:$this->item_count:$this->order_by:$this->order_type:$this->sub_category_id")) {
                $result = Cache::rememberforever("getAllRestoreDevice$this->page:$this->item_count:$this->order_by:$this->order_type:$this->sub_category_id", function () {

                    $total_row_result = DB::select('SELECT COUNT(*) as total FROM  restore_device where sub_category_id = ?', [$this->sub_category_id]);
                    $total_row = $total_row_result[0]->total;

                    $result = DB::select('SELECT
                                    *
                                  FROM
                                  restore_device AS rd
                                  WHERE
                                  sub_category_id = ?
                                  ORDER BY rd.' . $this->order_by . ' ' . $this->order_type . '
                                  LIMIT ?,?', [$this->sub_category_id, $this->offset, $this->item_count]);

                    return array('total_record' => $total_row, 'list_device' => $result);
                });
            }

            $redis_result = Cache::get("getAllRestoreDevice$this->page:$this->item_count:$this->order_by:$this->order_type:$this->sub_category_id");

            if (!$redis_result) {
                $redis_result = [];
            }

            $response = Response::json(array('code' => 200, 'message' => 'Restore devices fetched successfully.', 'cause' => '', 'data' => $redis_result));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            Log::error("getAllRestoreDevice : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get all restore devices.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    /**
     * @api {post} searchUser   searchUser
     * @apiName searchUser
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     *  Key: Authorization
     *  Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     *  "sub_category_id":20,
     *  "search_type":"device_id",
     *  "search_query":"10"
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Search User Fetched Successfully.",
     * "cause": "",
     * "data": {
     * "list_user": [
     * {
     * "sub_category_id": 20,
     * "device_id": 10,
     * "device_reg_id": "911F5C2F54F94DC6037C1A249B0BC98BFF5B17DF2A1C8CE5A546201A5B8DCF6B",
     * "device_platform": "android",
     * "device_model_name": "Micromax AQ4501",
     * "device_vendor_name": "Micromax",
     * "device_os_version": "6.0.1",
     * "device_udid": "809111aa1121",
     * "device_resolution": "480x782",
     * "device_carrier": "",
     * "device_country_code": "IN",
     * "device_language": "en",
     * "device_local_code": "NA",
     * "device_default_time_zone": "Asia/Calcutta",
     * "device_library_version": "1",
     * "device_application_version": "",
     * "device_type": "phone",
     * "device_registration_date": "2017-07-06T15:58:11 +0530",
     * "is_active": 1,
     * "is_count": 0,
     * "create_time": "2017-07-07 15:48:05",
     * "update_time": "2017-07-07 15:48:05"
     * }
     * ]
     * }
     * }
     */
    public function searchUser(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('sub_category_id', 'search_type', 'search_query'), $request)) != '')
                return $response;

            $sub_category_id = $request->sub_category_id;
            $search_type = $request->search_type;
            $search_query = '%' . $request->search_query . '%';

            $result = DB::select('SELECT
                                    dm.sub_category_id,
                                    dm.device_id,
                                    dm.device_reg_id,
                                    dm.device_platform,
                                    dm.device_model_name,
                                    dm.device_vendor_name,
                                    dm.device_os_version,
                                    dm.device_udid,
                                    dm.device_resolution,
                                    dm.device_carrier,
                                    dm.device_country_code,
                                    dm.device_language,
                                    dm.device_local_code,
                                    dm.device_default_time_zone,
                                    dm.device_library_version,
                                    dm.device_application_version,
                                    dm.device_type,
                                    dm.device_registration_date,
                                    dm.is_active,
                                    dm.is_count,
                                    dm.create_time,
                                    dm.update_time
                                  FROM
                                    device_master AS dm
                                  WHERE
                                    dm.sub_category_id = ? AND
                                    dm.' . $search_type . '
                                  LIKE ?', [$sub_category_id, $search_query]);


            $response = Response::json(array('code' => 200, 'message' => 'Users fetched successfully.', 'cause' => '', 'data' => ['list_user' => $result]));

        } catch (Exception $e) {
            Log::error("searchUser : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'search user.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    /**
     * @api {post} searchPurchaseUser   searchPurchaseUser
     * @apiName searchPurchaseUser
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     *  Key: Authorization
     *  Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     *  "sub_category_id":20,
     *  "search_type":"order_number",
     *  "search_query":"10"
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Purchase User Successfully fetch.",
     * "cause": "",
     * "data": {
     * "list_user": [
     * {
     * "sub_category_id":20,
     * "order_number": "1000000297266758",
     * "tot_order_amount": 1.99,
     * "currency_code": "USD",
     * "device_platform": "ios",
     * "create_time": null
     * },
     * {
     * "sub_category_id":20,
     * "order_number": "1100000297266758",
     * "tot_order_amount": 1.99,
     * "currency_code": "USD",
     * "device_platform": "ios",
     * "create_time": null
     * }
     * ]
     * }
     * }
     */
    public function searchPurchaseUser(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('sub_category_id', 'search_type', 'search_query'), $request)) != '')
                return $response;

            $sub_category_id = $request->sub_category_id;
            $search_type = $request->search_type;
            $search_query = '%' . $request->search_query . '%';

            $result = DB::select('SELECT
                                    om.sub_category_id,
                                    om.order_number,
                                    om.tot_order_amount,
                                    om.currency_code,
                                    om.device_platform,
                                    om.create_time
                                  FROM
                                    order_master AS om
                                  WHERE
                                    om.sub_category_id = ? AND
                                    om.' . $search_type . '
                                  LIKE ?', [$sub_category_id, $search_query]);


            $response = Response::json(array('code' => 200, 'message' => 'Purchase users fetched successfully.', 'cause' => '', 'data' => ['list_user' => $result]));

        } catch (Exception $e) {
            Log::error("searchPurchaseUser : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'search purchased user.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    /**
     * @api {post} searchRestoreDevice   searchRestoreDevice
     * @apiName searchRestoreDevice
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     *  Key: Authorization
     *  Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     *  "sub_category_id":20,
     *  "search_type":"order_number", // or device_udid
     *  "search_query":"249"
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "App Restore Device Fetched Successfully.",
     * "cause": "",
     * "data": {
     * "list_device": [
     * {
     * "id": 3,
     * "sub_category_id":20,
     * "order_number": "2494322134113",
     * "device_udid": "ff505a7a500c931a",
     * "restore": 1,
     * "create_time": "2017-07-07 10:19:58",
     * "update_time": "2017-07-07 15:49:58"
     * }
     * ]
     * }
     * }
     */
    public function searchRestoreDevice(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('sub_category_id', 'search_type', 'search_query'), $request)) != '')
                return $response;


            $sub_category_id = $request->sub_category_id;
            $search_type = $request->search_type;
            $search_query = '%' . $request->search_query . '%';

            $result = DB::select('SELECT
                            *
                          FROM
                          restore_device AS rd
                          WHERE
                            rd.sub_category_id = ? AND
                            ' . $search_type . '
                          LIKE ?', [$sub_category_id, $search_query]);


            $response = Response::json(array('code' => 200, 'message' => 'Restore devices fetched successfully.', 'cause' => '', 'data' => ['list_device' => $result]));

        } catch (Exception $e) {
            Log::error("searchRestoreDevice : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'search restore devices.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    /**
     * @api {post} updateUserProfile   updateUserProfile
     * @apiName updateUserProfile
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * request_data:{
     * "first_name":"jitu",
     * "last_name":"admin",
     * "phone_number_1":"9173527938",
     * "address_line_1":"Rander",
     * "city":"surat",
     * "state":"Gujarat",
     * "pincode":"395010",
     * "latitude":"",
     * "longitude":""
     * }
     * file:image //optional
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Profile Updated Successfully.",
     * "cause": "",
     * "data": ""
     * }
     */
    public function updateUserProfile(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);
            $user_data = JWTAuth::parseToken()->authenticate();
            $user_id = $user_data->email_id;

            //Required parameter
            if (!$request_body->has('request_data'))
                return Response::json(array('code' => 201, 'message' => 'Required field request_data is missing or empty.', 'cause' => '', 'data' => json_decode("{}")));

            $request = json_decode($request_body->input('request_data'));

            if (($response = (new VerificationController())->validateRequiredParameter(array('first_name',
                    'last_name',
                    'phone_number_1',
                    'address_line_1',
                    'city',
                    'state',
                    'pincode'), $request)) != ''
            )
                return $response;

            if ($request_body->hasFile('file')) {
                $image_array = Input::file('file');

                /* Here we passed value following parameters as 0 bcs we use common validation for user profile
                 * category = 0
                 * is_featured = 0
                 * is_catalog = 0
                 * */
                if (($response = (new ImageController())->verifyImage($image_array, 0, 0, 0)) != '')
                    return $response;

                $profile_img = (new ImageController())->generateNewFileName('profile_img', $image_array);
                //Log::info("file size :",[filesize($profile_img)]);
                (new ImageController())->saveOriginalImage($profile_img);
                (new ImageController())->saveCompressedImage($profile_img);
                (new ImageController())->saveThumbnailImage($profile_img);
                //(new ImageController())->saveImageInToS3($profile_img);

                if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
                    (new ImageController())->saveImageInToS3($profile_img);
                }

            } else {
                $result = DB::table("user_detail")->where('user_id', $user_id)->get();
                $profile_img = isset($result[0]->profile_img) ? $result[0]->profile_img : "";
            }

            $first_name = isset($request->first_name) ? $request->first_name : "";
            $last_name = isset($request->last_name) ? $request->last_name : "";
            $phone_number_1 = isset($request->phone_number_1) ? $request->phone_number_1 : "";
            $address_line_1 = isset($request->address_line_1) ? $request->address_line_1 : "";

            DB::beginTransaction();

            DB::update('UPDATE
                          user_detail
                        SET
                          first_name = ?,
                          last_name = ?,
                          profile_img = ?,
                          phone_number_1 = ?,
                          address_line_1 = ?
                        WHERE
                          email_id = ? ',
                [$first_name, $last_name, $profile_img, $phone_number_1, $address_line_1, $user_id]);

            DB::commit();

            $response = Response::json(array('code' => 200, 'message' => 'Profile updated successfully.', 'cause' => '', 'data' => json_decode('{}')));

        } catch (Exception $e) {
            Log::error("updateUserProfile : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'update user profile.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }


}

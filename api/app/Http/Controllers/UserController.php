<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Permission;
use App\Role;
use Response;
use Config;
use DB;
use Log;
use File;
use Cache;
use Exception;
use Illuminate\Support\Facades\Input;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Redis;

class UserController extends Controller
{
    //
    public function __construct()
    {
        $this->item_count = Config::get('constant.PAGINATION_ITEM_LIMIT');
        $this->base_url = (new ImageController())->getBaseUrl();

    }

    /* =================================| Json |=============================*/

    /**
     * @api {post} getLinkWithLastSyncTime   getLinkWithLastSyncTime
     * @apiName getLinkWithLastSyncTime
     * @apiGroup User
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "sub_category_id": 85,
     * "platform": "Android",
     * "last_sync_time": "2017-11-28 00:00:00",
     * "advertise_id_list": [
     * 70,
     * 71,
     * 72,
     * 77,
     * 100,
     * 200
     * ]
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Advertise Link Fetched Successfully.",
     * "cause": "",
     * "data": {
     * "total_record": 2,
     * "link_list": [
     * {
     * "advertise_link_id": 77,
     * "name": "Romantic Love Photo Editor",
     * "thumbnail_img": "http://192.168.0.113/photo_editor_lab_backend/image_bucket/thumbnail/5a1e813f47368_banner_image_1511948607.png",
     * "compressed_img": "http://192.168.0.113/photo_editor_lab_backend/image_bucket/compressed/5a1e813f47368_banner_image_1511948607.png",
     * "original_img": "http://192.168.0.113/photo_editor_lab_backend/image_bucket/original/5a1e813f47368_banner_image_1511948607.png",
     * "app_logo_thumbnail_img": "http://192.168.0.113/photo_editor_lab_backend/image_bucket/thumbnail/5a1e814000aa9_app_logo_image_1511948608.png",
     * "app_logo_compressed_img": "http://192.168.0.113/photo_editor_lab_backend/image_bucket/compressed/5a1e814000aa9_app_logo_image_1511948608.png",
     * "app_logo_original_img": "http://192.168.0.113/photo_editor_lab_backend/image_bucket/original/5a1e814000aa9_app_logo_image_1511948608.png",
     * "url": "https://play.google.com/store/apps/details?id=com.optimumbrewlab.lovephotoeditor",
     * "platform": "Android",
     * "app_description": "Romantic Love Photo Editor - Realistic Photo Effects, Beautiful Photo Frames, Stickers, etc.",
     * "updated_at": "2018-06-25 10:55:05"
     * }
     * ],
     * "last_sync_time": "2018-06-25 10:55:05",
     * "advertise_id_list": [
     * 100
     * ]
     * }
     * }
     */
    public function getLinkWithLastSyncTime(Request $request_body)
    {

        try {

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('sub_category_id', 'platform', 'last_sync_time'), $request)) != '')
                return $response;


            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $this->platform = $request->platform;
            $this->sub_category_id = $request->sub_category_id;
            $this->last_sync_time = $request->last_sync_time;
            $this->advertise_id_list = $request->advertise_id_list;
            $new_array = array();

            if (!Cache::has("pel:getLinkWithLastSyncTime$this->platform:$this->sub_category_id:$this->last_sync_time")) {
                $result = Cache::rememberforever("getLinkWithLastSyncTime$this->platform:$this->sub_category_id:$this->last_sync_time", function () {

                    if (count($this->advertise_id_list) > 0) {

                        foreach ($this->advertise_id_list as $key) {

                            $result = DB::select('SELECT advertise_link_id
                                            FROM sub_category_advertise_links
                                            WHERE advertise_link_id = ? AND
                                            sub_category_id = ?', [$key, $this->sub_category_id]);

                            if (count($result) == 0) {
                                $new_array[] = $key;
                            }
                        }

                    }


                    $result = DB::select('SELECT
                                        adl.id as advertise_link_id,
                                        adl.name,
                                        IF(adl.image != "",CONCAT("' . Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",adl.image),"") as thumbnail_img,
                                        IF(adl.image != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",adl.image),"") as compressed_img,
                                        IF(adl.image != "",CONCAT("' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",adl.image),"") as original_img,
                                        IF(adl.app_logo_img != "",CONCAT("' . Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",adl.app_logo_img),"") as app_logo_thumbnail_img,
                                        IF(adl.app_logo_img != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",adl.app_logo_img),"") as app_logo_compressed_img,
                                        IF(adl.app_logo_img != "",CONCAT("' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",adl.app_logo_img),"") as app_logo_original_img,
                                        adl.url,
                                        adl.platform,
                                        if(adl.app_description!="",adl.app_description,"") as app_description,
                                        sadl.updated_at
                                      FROM
                                        advertise_links as adl,
                                        sub_category_advertise_links as sadl
                                      WHERE
                                        adl.platform = ? AND
                                        sadl.advertise_link_id=adl.id AND
                                        sadl.sub_category_id = ? AND
                                        sadl.is_active = 1 AND
                                        sadl.updated_at >= ?
                                      order by sadl.updated_at DESC', [$this->platform, $this->sub_category_id, $this->last_sync_time]);


                    $result_array = array('link_list' => $result, 'advertise_id_list' => $new_array);

                    return $result_array;
                });
            }

            $redis_result = Cache::get("getLinkWithLastSyncTime$this->platform:$this->sub_category_id:$this->last_sync_time");

            if (!$redis_result) {
                $redis_result = [];
            }


            if (count($redis_result) >= 1) {
                $result = $redis_result['link_list'];
                $last_sync_time = $result[0]->updated_at;

            } else {
                $last_sync_time = date("Y-m-d H:i:s");
            }

            $response = Response::json(array('code' => 200, 'message' => 'Advertise Link Fetched Successfully.', 'cause' => '', 'data' => ['total_record' => count($redis_result), 'link_list' => $redis_result['link_list'], 'last_sync_time' => $last_sync_time, 'advertise_id_list' => $redis_result['advertise_id_list']]));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));


        } catch
        (Exception $e) {
            Log::error("getLinkWithLastSyncTime Error :", ['Error : ' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get advertise link.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /* =================================| Json |=============================*/

    public function getFeaturedJsonImages(Request $request_body)
    {

        try {
            $request = json_decode($request_body->getContent());

            if (($response = (new VerificationController())->validateRequiredParameter(array('catalog_id'), $request)) != '')
                return $response;


            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $this->catalog_id = $request->catalog_id;

            //Log::info('request_data', ['request_data' => $request]);

            if (!Cache::has("pel:getFeaturedJsonImages$this->catalog_id")) {
                $result = Cache::rememberforever("getFeaturedJsonImages$this->catalog_id", function () {
                    $result = DB::select('SELECT
                                        id as json_id,
                                        IF(image != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",image),"") as sample_image,
                                        json_data as json_list,
                                        is_free,
                                        is_featured
                                      FROM
                                        images
                                      WHERE
                                        catalog_id = ? and is_featured = 1
                                      order by updated_at DESC', [$this->catalog_id]);

                    foreach ($result as $key) {
                        $key->json_list = json_decode($key->json_list);
                    }
                    return $result;
                });
            }

            $redis_result = Cache::get("getFeaturedJsonImages$this->catalog_id");

            if (!$redis_result) {
                $redis_result = [];
            }

            $response = Response::json(array('code' => 200, 'message' => 'Featured json images fetch successfully.', 'cause' => '', 'data' => $redis_result));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));


            //$response = Response::json(array('code' => 200, 'message' => 'Featured Background Images added successfully!.', 'cause' => '', 'data' => json_decode('{}')));

        } catch
        (Exception $e) {
            Log::error("getFeaturedJsonImages Error :", ['Error : ' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'Add Featured Background Images.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /**
     * @api {post} getJsonSampleData   getJsonSampleData
     * @apiName getJsonSampleData
     * @apiGroup User
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "page":1,
     * "item_count":10,
     * "catalog_id":0,
     * "sub_category_id":45
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "All Link Fetched Successfully.",
     * "cause": "",
     * "data": {
     * "total_record": 3,
     * "is_next_page": false,
     * "data": [
     * {
     * "json_id": 355,
     * "sample_image": "http://192.168.0.113/ob_photolab_backend/image_bucket/compressed/5a0d7faa3b1bc_catalog_image_1510834090.jpg",
     * "is_free": 1,
     * "is_featured": 1,
     * "updated_at": "2018-06-21 12:04:36"
     * },
     * {
     * "json_id": 354,
     * "sample_image": "http://192.168.0.113/ob_photolab_backend/image_bucket/compressed/5a0d7f89953aa_catalog_image_1510834057.jpg",
     * "is_free": 1,
     * "is_featured": 1,
     * "updated_at": "2018-06-21 12:04:36"
     * },
     * {
     * "json_id": 342,
     * "sample_image": "http://192.168.0.113/ob_photolab_backend/image_bucket/compressed/5a059c4cbadaa_catalog_image_1510317132.jpg",
     * "is_free": 1,
     * "is_featured": 1,
     * "updated_at": "2018-06-21 12:04:36"
     * }
     * ]
     * }
     * }
     */
    public function getJsonSampleData(Request $request_body)
    {

        try {

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('sub_category_id', 'catalog_id', 'page', 'item_count'), $request)) != '')
                return $response;


            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $this->catalog_id = $request->catalog_id;
            $this->sub_category_id = $request->sub_category_id;

            $this->item_count = $request->item_count;
            $this->page = $request->page;
            $this->order_by = isset($request->order_by) ? $request->order_by : 'size';
            $this->order_type = isset($request->order_type) ? $request->order_type : 'DESC';
            $this->offset = ($this->page - 1) * $this->item_count;

            if ($this->catalog_id == 0) {
                $total_row_result = DB::select('SELECT COUNT(*) AS total
                                                    FROM images
                                                    WHERE catalog_id IN (SELECT catalog_id
                                                                     FROM sub_category_catalog
                                                                     WHERE sub_category_id = ?) AND is_featured = 1', [$this->sub_category_id]);
                $total_row = $total_row_result[0]->total;
            } else {
                $total_row_result = DB::select('SELECT COUNT(*) as total FROM images WHERE catalog_id = ?', [$this->catalog_id]);
                $total_row = $total_row_result[0]->total;
            }


            //Log::info('request_data', ['request_data' => $request]);

            if (!Cache::has("pel:getJsonSampleData$this->page:$this->item_count:$this->catalog_id:$this->sub_category_id")) {
                $result = Cache::rememberforever("getJsonSampleData$this->page:$this->item_count:$this->catalog_id:$this->sub_category_id", function () {

                    if ($this->catalog_id == 0) {
                        $result = DB::select('SELECT
                                                  id as json_id,
                                                  IF(image != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",image),"") as sample_image,
                                                  is_free,
                                                  is_featured,
                                                  is_portrait,
                                                  updated_at
                                                FROM
                                                  images
                                                WHERE
                                                  catalog_id in(select catalog_id FROM sub_category_catalog WHERE sub_category_id = ?) and is_featured = 1
                                                order by updated_at DESC LIMIT ?, ?', [$this->sub_category_id, $this->offset, $this->item_count]);

                    } else {
                        $result = DB::select('SELECT
                                               id as json_id,
                                               IF(image != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",image),"") as sample_image,
                                               is_free,
                                               is_featured,
                                               is_portrait,
                                               updated_at
                                                FROM
                                                images
                                                WHERE
                                                catalog_id = ?
                                                order by updated_at DESC LIMIT ?, ?', [$this->catalog_id, $this->offset, $this->item_count]);


                    }
                    return $result;
                });
            }

            $redis_result = Cache::get("getJsonSampleData$this->page:$this->item_count:$this->catalog_id:$this->sub_category_id");

            if (!$redis_result) {
                $redis_result = [];
            }
            $is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;

            $response = Response::json(array('code' => 200, 'message' => 'All Link Fetched Successfully.', 'cause' => '', 'data' => ['total_record' => $total_row, 'is_next_page' => $is_next_page, 'data' => $redis_result]));
            //$response = Response::json(array('code' => 200, 'message' => 'Sample images fetched successfully.', 'cause' => '', 'data' => $redis_result));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));


            //$response = Response::json(array('code' => 200, 'message' => 'Featured Background Images added successfully!.', 'cause' => '', 'data' => json_decode('{}')));

        } catch
        (Exception $e) {
            Log::error("getJsonSampleData Error :", ['Error : ' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'Add Featured Background Images.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /**
     * @api {post} getJsonData   getJsonData
     * @apiName getJsonData
     * @apiGroup User
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "json_id":1 //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Featured json images fetch successfully.",
     * "cause": "",
     * "data": {
     * "text_json": [],
     * "sticker_json": [],
     * "image_sticker_json": [
     * {
     * "xPos": 0,
     * "yPos": 0,
     * "image_sticker_image": "",
     * "angle": 0,
     * "is_round": 0,
     * "height": 800,
     * "width": 500
     * }
     * ],
     * "frame_json": {
     * "frame_image": "frame_15.7"
     * },
     * "background_json": {},
     * "sample_image": "sample_15.7",
     * "is_featured": 0,
     * "height": 800,
     * "width": 800
     * }
     * }
     */
    public function getJsonData(Request $request_body)
    {

        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());

            if (($response = (new VerificationController())->validateRequiredParameter(array('json_id'), $request)) != '')
                return $response;

            $this->json_id_to_get_json_data = $request->json_id;

            //Log::info('request_data', ['request_data' => $request]);

            if (!Cache::has("pel:getJsonData$this->json_id_to_get_json_data")) {
                $result = Cache::rememberforever("getJsonData$this->json_id_to_get_json_data", function () {
                    $result = DB::select('SELECT
                                                json_data
                                                FROM
                                                images
                                                WHERE
                                                id= ?
                                                order by updated_at DESC', [$this->json_id_to_get_json_data]);
                    if (count($result) > 0) {
                        return json_decode($result[0]->json_data);
                    } else {
                        return json_decode("{}");
                    }


                });
            }

            $redis_result = Cache::get("getJsonData$this->json_id_to_get_json_data");

            if (!$redis_result) {
                $redis_result = [];
            }
            //$is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;

            //$response = Response::json(array('code' => 200, 'message' => 'All Link Fetched Successfully.', 'cause' => '', 'data' => ['total_record' => $total_row, 'is_next_page' => $is_next_page, 'data' => $redis_result]));
            $response = Response::json(array('code' => 200, 'message' => 'Featured json images fetch successfully.', 'cause' => '', 'data' => $redis_result));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));


            //$response = Response::json(array('code' => 200, 'message' => 'Featured Background Images added successfully!.', 'cause' => '', 'data' => json_decode('{}')));

        } catch
        (Exception $e) {
            Log::error("getJsonData Error :", ['Error : ' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'Add Featured Background Images.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /* =====================================| Api with last_sync_time(catalog and json data) |==================================*/

    /**
     * @api {post} getCatalogBySubCategoryIdWithLastSyncTime   getCatalogBySubCategoryIdWithLastSyncTime
     * @apiName getCatalogBySubCategoryIdWithLastSyncTime
     * @apiGroup User
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "sub_category_id":51,
     * "last_sync_time":"0"
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Catalog Fetched Successfully.",
     * "cause": "",
     * "data": {
     * "total_record": 2,
     * "last_sync_time": "2017-11-28 06:55:14",
     * "category_list": [
     * {
     * "catalog_id": 168,
     * "name": "Business Card Catalog2",
     * "thumbnail_img": "http://192.168.0.113/ob_photolab_backend/image_bucket/thumbnail/5a1d0851d6d32_catalog_img_1511852113.png",
     * "compressed_img": "http://192.168.0.113/ob_photolab_backend/image_bucket/compressed/5a1d0851d6d32_catalog_img_1511852113.png",
     * "original_img": "http://192.168.0.113/ob_photolab_backend/image_bucket/original/5a1d0851d6d32_catalog_img_1511852113.png",
     * "is_free": 1,
     * "is_featured": 0,
     * "updated_at": "2018-06-21 12:04:36"
     * },
     * {
     * "catalog_id": 167,
     * "name": "Business Card Catalog1",
     * "thumbnail_img": "http://192.168.0.113/ob_photolab_backend/image_bucket/thumbnail/5a17fab520a09_catalog_img_1511520949.png",
     * "compressed_img": "http://192.168.0.113/ob_photolab_backend/image_bucket/compressed/5a17fab520a09_catalog_img_1511520949.png",
     * "original_img": "http://192.168.0.113/ob_photolab_backend/image_bucket/original/5a17fab520a09_catalog_img_1511520949.png",
     * "is_free": 1,
     * "is_featured": 1,
     * "updated_at": "2018-06-21 12:04:36"
     * }
     * ]
     * }
     * }
     */
    public function getCatalogBySubCategoryIdWithLastSyncTime(Request $request_body)
    {
        try {
            $request = json_decode($request_body->getContent());
            //Log::info("getCatalogBySubCategoryIdWithLastSyncTime Request:", [$request]);
            if (($response = (new VerificationController())->validateRequiredParameter(array('sub_category_id', 'last_sync_time'), $request)) != '')
                return $response;

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $this->last_sync_time = $request->last_sync_time;
            $this->sub_category_id = $request->sub_category_id;

//            //sub Category Name
//            $name = DB::select('SELECT sc.name FROM  sub_category as sc WHERE sc.id = ? AND sc.is_active = ?', [$this->sub_category_id, 1]);
//            $category_name = $name[0]->name;

            //return $this->last_sync_time.$this->sub_category_id;

            /*$total_row_result = DB::select('SELECT COUNT(*) AS total
                                                FROM sub_category_catalog
                                                WHERE sub_category_id = ? AND
                                                updated_at >= ? AND
                                                is_active = 1
                                                ', [$request->sub_category_id, $request->last_sync_time, 1]);
            $total_row = $total_row_result[0]->total;*/
            //return $total_row;
            /*$last_created_record = DB::select('SELECT updated_at FROM sub_category_catalog WHERE sub_category_id = ? ORDER BY updated_at DESC LIMIT 1', [$this->sub_category_id]);

            if (count($last_created_record) >= 1) {
                $last_sync_time = $last_created_record[0]->updated_at;

            } else {
                $last_sync_time = date("Y-m-d H:i:s");
            }*/

            if (!Cache::has("pel:getCatalogBySubCategoryIdWithLastSyncTime$request->sub_category_id:$request->last_sync_time")) {
                $result = Cache::rememberforever("getCatalogBySubCategoryIdWithLastSyncTime$request->sub_category_id:$request->last_sync_time", function () {


                    if ($this->last_sync_time == 0) {
                        return DB::select('SELECT
                                          ct.id as catalog_id,
                                          ct.name,
                                          IF(ct.image != "",CONCAT("' . Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.image),"") as thumbnail_img,
                                          IF(ct.image != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.image),"") as compressed_img,
                                          IF(ct.image != "",CONCAT("' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.image),"") as original_img,
                                          ct.is_free,
                                          ct.is_featured,
                                          ct.updated_at
                                        FROM
                                          catalog_master as ct,
                                          sub_category_catalog as sct
                                        WHERE
                                          sct.sub_category_id = ? AND
                                          sct.catalog_id=ct.id AND
                                          sct.is_active=1
                                        order by ct.updated_at DESC', [$this->sub_category_id]);
                    } else {
                        return DB::select('SELECT
                                          ct.id as catalog_id,
                                          ct.name,
                                          IF(ct.image != "",CONCAT("' . Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.image),"") as thumbnail_img,
                                          IF(ct.image != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.image),"") as compressed_img,
                                          IF(ct.image != "",CONCAT("' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.image),"") as original_img,
                                          ct.is_free,
                                          ct.is_featured,
                                          ct.updated_at
                                        FROM
                                          catalog_master as ct,
                                          sub_category_catalog as sct
                                        WHERE
                                          sct.sub_category_id = ? AND
                                          sct.catalog_id=ct.id AND
                                          sct.is_active=1 AND
                                          (ct.updated_at >= ? OR
                                          sct.created_at >= ?)
                                        order by ct.updated_at DESC', [$this->sub_category_id, $this->last_sync_time, $this->last_sync_time]);


                    }


                });


            }

            $redis_result = Cache::get("getCatalogBySubCategoryIdWithLastSyncTime$request->sub_category_id:$request->last_sync_time");

            if (!$redis_result) {
                $redis_result = [];
            }


            if (count($redis_result) >= 1) {
                $last_sync_time = $redis_result[0]->updated_at;

            } else {
                $last_sync_time = date("Y-m-d H:i:s");
            }


            $response = Response::json(array('code' => 200, 'message' => 'Catalog Fetched Successfully.', 'cause' => '', 'data' => ['total_record' => count($redis_result), 'last_sync_time' => $last_sync_time, 'category_list' => $redis_result]));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            Log::error("getCatalogBySubCategoryIdWithLastSyncTime Error :", ['Error : ' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . ' get Catalog with last_sync_time.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    /**
     * @api {post} getJsonSampleDataWithLastSyncTime   getJsonSampleDataWithLastSyncTime
     * @apiName getJsonSampleDataWithLastSyncTime
     * @apiGroup User
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "page":2,
     * "item_count":10,
     * "catalog_id":167,
     * "sub_category_id":51,
     * "last_sync_time": "2017-11-28 00:00:00"
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "All Link Fetched Successfully.",
     * "cause": "",
     * "data": {
     * "total_record": 18,
     * "is_next_page": true,
     * "last_sync_time": "2017-11-28 06:42:11",
     * "data": [
     * {
     * "json_id": 407,
     * "sample_image": "http://192.168.0.113/ob_photolab_backend/image_bucket/compressed/5a1cfe841fd30_json_image_1511849604.jpg",
     * "is_free": 1,
     * "is_featured": 1,
     * "is_portrait": 1,
     * "updated_at": "2018-06-21 12:04:36"
     * },
     * {
     * "json_id": 406,
     * "sample_image": "http://192.168.0.113/ob_photolab_backend/image_bucket/compressed/5a1cfd7fadfc0_json_image_1511849343.jpg",
     * "is_free": 1,
     * "is_featured": 1,
     * "is_portrait": 1,
     * "updated_at": "2018-06-21 12:04:36"
     * },
     * {
     * "json_id": 405,
     * "sample_image": "http://192.168.0.113/ob_photolab_backend/image_bucket/compressed/5a1cfc994b4bd_json_image_1511849113.jpg",
     * "is_free": 1,
     * "is_featured": 1,
     * "is_portrait": 1,
     * "updated_at": "2018-06-21 12:04:36"
     * },
     * {
     * "json_id": 404,
     * "sample_image": "http://192.168.0.113/ob_photolab_backend/image_bucket/compressed/5a1cf9656d54c_json_image_1511848293.jpg",
     * "is_free": 1,
     * "is_featured": 1,
     * "is_portrait": 1,
     * "updated_at": "2018-06-21 12:04:36"
     * },
     * {
     * "json_id": 401,
     * "sample_image": "http://192.168.0.113/ob_photolab_backend/image_bucket/compressed/5a1cefcb29a2b_json_image_1511845835.jpg",
     * "is_free": 0,
     * "is_featured": 1,
     * "is_portrait": 0,
     * "updated_at": "2018-06-21 12:04:36"
     * }
     * ]
     * }
     * }
     */
    public function getJsonSampleDataWithLastSyncTime(Request $request_body)
    {

        try {

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('sub_category_id', 'catalog_id', 'page', 'item_count', 'last_sync_time'), $request)) != '')
                return $response;


            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $this->catalog_id = $request->catalog_id;
            $this->sub_category_id = $request->sub_category_id;
            $this->last_sync_date = $request->last_sync_time;

            $this->item_count = $request->item_count;
            $this->page = $request->page;
            $this->order_by = isset($request->order_by) ? $request->order_by : 'size';
            $this->order_type = isset($request->order_type) ? $request->order_type : 'DESC';
            $this->offset = ($this->page - 1) * $this->item_count;

            if ($this->catalog_id == 0) {
                $total_row_result = DB::select('SELECT COUNT(*) AS total
                                                    FROM images
                                                    WHERE catalog_id IN (SELECT catalog_id
                                                                     FROM sub_category_catalog
                                                                     WHERE sub_category_id = ?) AND is_featured = 1 and updated_at >= ?', [$this->sub_category_id, $request->last_sync_time]);
                $total_row = $total_row_result[0]->total;
            } else {
                $total_row_result = DB::select('SELECT COUNT(*) as total FROM images WHERE catalog_id = ? AND updated_at >= ?', [$this->catalog_id, $request->last_sync_time]);
                $total_row = $total_row_result[0]->total;
            }

            $last_created_record = DB::select('SELECT updated_at FROM images WHERE catalog_id = ? ORDER BY updated_at DESC LIMIT 1', [$this->catalog_id]);

            if (count($last_created_record) >= 1) {
                $last_sync_time = $last_created_record[0]->updated_at;

            } else {
                $last_sync_time = date("Y-m-d H:i:s");
            }


            //Log::info('request_data', ['request_data' => $request]);

            if (!Cache::has("pel:getJsonSampleDataWithLastSyncTime$this->page:$this->item_count:$this->catalog_id:$this->sub_category_id:$request->last_sync_time")) {
                $result = Cache::rememberforever("getJsonSampleDataWithLastSyncTime$this->page:$this->item_count:$this->catalog_id:$this->sub_category_id:$request->last_sync_time", function () {

                    if ($this->catalog_id == 0) {
                        $result = DB::select('SELECT
                                                  id as json_id,
                                                  IF(image != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",image),"") as sample_image,
                                                  is_free,
                                                  is_featured,
                                                  is_portrait,
                                                  updated_at
                                                FROM
                                                  images
                                                WHERE
                                                  catalog_id in(select catalog_id FROM sub_category_catalog WHERE sub_category_id = ?) and
                                                  is_featured = 1 AND
                                                  updated_at >= ?
                                                order by updated_at DESC LIMIT ?, ?', [$this->sub_category_id, $this->last_sync_date, $this->offset, $this->item_count]);

                    } else {
                        $result = DB::select('SELECT
                                               id as json_id,
                                               IF(image != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",image),"") as sample_image,
                                               is_free,
                                               is_featured,
                                               is_portrait,
                                               updated_at
                                                FROM
                                                images
                                                WHERE
                                                catalog_id = ? AND
                                                updated_at >= ?
                                                order by updated_at DESC LIMIT ?, ?', [$this->catalog_id, $this->last_sync_date, $this->offset, $this->item_count]);


                    }
                    return $result;
                });
            }

            $redis_result = Cache::get("getJsonSampleDataWithLastSyncTime$this->page:$this->item_count:$this->catalog_id:$this->sub_category_id:$request->last_sync_time");

            if (!$redis_result) {
                $redis_result = [];
            }
            $is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;

            $response = Response::json(array('code' => 200, 'message' => 'All json fetched successfully.', 'cause' => '', 'data' => ['total_record' => $total_row, 'is_next_page' => $is_next_page, 'last_sync_time' => $last_sync_time, 'data' => $redis_result]));
            //$response = Response::json(array('code' => 200, 'message' => 'Sample images fetched successfully.', 'cause' => '', 'data' => $redis_result));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));


            //$response = Response::json(array('code' => 200, 'message' => 'Featured Background Images added successfully!.', 'cause' => '', 'data' => json_decode('{}')));

        } catch
        (Exception $e) {
            Log::error("getJsonSampleDataWithLastSyncTime Error :", ['Error : ' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get json data with last_sync_time.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /**
     * @api {post} getJsonSampleDataWithLastSyncTime_webp   getJsonSampleDataWithLastSyncTime_webp
     * @apiName getJsonSampleDataWithLastSyncTime_webp
     * @apiGroup User
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "page":2,
     * "item_count":10,
     * "catalog_id":167,
     * "sub_category_id":51,
     * "last_sync_time": "2017-11-28 00:00:00"
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "All json fetched successfully.",
     * "cause": "",
     * "data": {
     * "total_record": 23,
     * "is_next_page": true,
     * "last_sync_time": "2018-06-21 12:04:36",
     * "data": [
     * {
     * "json_id": 1190,
     * "sample_image": "http://192.168.0.113/photo_editor_lab_backend/image_bucket/webp_original/5ab0ae54e2105_json_image_1521528404.webp",
     * "is_free": 1,
     * "is_featured": 0,
     * "is_portrait": 1,
     * "updated_at": "2018-06-21 12:04:36"
     * },
     * {
     * "json_id": 1185,
     * "sample_image": "http://192.168.0.113/photo_editor_lab_backend/image_bucket/webp_original/5ab0af12d9bae_json_image_1521528594.webp",
     * "is_free": 1,
     * "is_featured": 0,
     * "is_portrait": 1,
     * "updated_at": "2018-06-21 12:04:32"
     * }
     * ]
     * }
     * }
     */
    public function getJsonSampleDataWithLastSyncTime_webp(Request $request_body)
    {

        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('sub_category_id', 'catalog_id', 'page', 'item_count', 'last_sync_time'), $request)) != '')
                return $response;

            $this->catalog_id = $request->catalog_id;
            $this->sub_category_id = $request->sub_category_id;
            $this->last_sync_date = $request->last_sync_time;

            $this->item_count = $request->item_count;
            $this->page = $request->page;
            $this->order_by = isset($request->order_by) ? $request->order_by : 'size';
            $this->order_type = isset($request->order_type) ? $request->order_type : 'DESC';
            $this->offset = ($this->page - 1) * $this->item_count;

            if ($this->catalog_id == 0) {
                $total_row_result = DB::select('SELECT COUNT(*) AS total
                                                    FROM images
                                                    WHERE catalog_id IN (SELECT catalog_id
                                                                     FROM sub_category_catalog
                                                                     WHERE sub_category_id = ?) AND is_featured = 1 and updated_at >= ?', [$this->sub_category_id, $request->last_sync_time]);
                $total_row = $total_row_result[0]->total;
            } else {
                $total_row_result = DB::select('SELECT COUNT(*) as total FROM images WHERE catalog_id = ? AND updated_at >= ?', [$this->catalog_id, $request->last_sync_time]);
                $total_row = $total_row_result[0]->total;
            }

            $last_created_record = DB::select('SELECT updated_at FROM images WHERE catalog_id = ? ORDER BY updated_at DESC LIMIT 1', [$this->catalog_id]);

            if (count($last_created_record) >= 1) {
                $last_sync_time = $last_created_record[0]->updated_at;

            } else {
                $last_sync_time = date("Y-m-d H:i:s");
            }


            //Log::info('request_data', ['request_data' => $request]);

            if (!Cache::has("pel:getJsonSampleDataWithLastSyncTime_webp$this->page:$this->item_count:$this->catalog_id:$this->sub_category_id:$request->last_sync_time")) {
                $result = Cache::rememberforever("getJsonSampleDataWithLastSyncTime_webp$this->page:$this->item_count:$this->catalog_id:$this->sub_category_id:$request->last_sync_time", function () {

                    $host_name = request()->getHttpHost(); // With port if there is. Eg: mydomain.com:81
                    $certificate_maker_host_name = Config::get('constant.HOST_NAME_OF_CERTIFICATE_MAKER');

                    if ($this->catalog_id == 0) {
                        if ($host_name == $certificate_maker_host_name && $this->sub_category_id == 4) {

                            $result = DB::select('SELECT
                                                  id as json_id,
                                                  IF(image != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",image),"") as sample_image,
                                                  is_free,
                                                  is_featured,
                                                  is_portrait,
                                                  coalesce(height,0) AS height,
                                                  coalesce(width,0) AS width,
                                                  updated_at
                                                FROM
                                                  images
                                                WHERE
                                                  catalog_id in(select catalog_id FROM sub_category_catalog WHERE sub_category_id = ? AND is_active = 1) and
                                                  is_featured = 1 AND
                                                  updated_at >= ?
                                                order by updated_at DESC LIMIT ?, ?', [$this->sub_category_id, $this->last_sync_date, $this->offset, $this->item_count]);

                        } else {
                            $result = DB::select('SELECT
                                                  id as json_id,
                                                  IF(attribute1 != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",attribute1),"") as sample_image,
                                                  is_free,
                                                  is_featured,
                                                  is_portrait,
                                                  coalesce(height,0) AS height,
                                                  coalesce(width,0) AS width,
                                                  updated_at
                                                FROM
                                                  images
                                                WHERE
                                                  catalog_id in(select catalog_id FROM sub_category_catalog WHERE sub_category_id = ? AND is_active = 1) and
                                                  is_featured = 1 AND
                                                  updated_at >= ?
                                                order by updated_at DESC LIMIT ?, ?', [$this->sub_category_id, $this->last_sync_date, $this->offset, $this->item_count]);

                        }

                    } else {
                        if ($host_name == $certificate_maker_host_name && $this->sub_category_id == 4) {

                            $result = DB::select('SELECT
                                               id as json_id,
                                               IF(image != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",image),"") as sample_image,
                                               is_free,
                                               is_featured,
                                               is_portrait,
                                               coalesce(height,0) AS height,
                                               coalesce(width,0) AS width,
                                               updated_at
                                                FROM
                                                images
                                                WHERE
                                                catalog_id = ? AND
                                                updated_at >= ?
                                                order by updated_at DESC LIMIT ?, ?', [$this->catalog_id, $this->last_sync_date, $this->offset, $this->item_count]);

                        } else {
                            $result = DB::select('SELECT
                                               id as json_id,
                                               IF(attribute1 != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",attribute1),"") as sample_image,
                                               is_free,
                                               is_featured,
                                               is_portrait,
                                               coalesce(height,0) AS height,
                                               coalesce(width,0) AS width,
                                               updated_at
                                                FROM
                                                images
                                                WHERE
                                                catalog_id = ? AND
                                                updated_at >= ?
                                                order by updated_at DESC LIMIT ?, ?', [$this->catalog_id, $this->last_sync_date, $this->offset, $this->item_count]);

                        }

                    }

                    return $result;
                });
            }

            $redis_result = Cache::get("getJsonSampleDataWithLastSyncTime_webp$this->page:$this->item_count:$this->catalog_id:$this->sub_category_id:$request->last_sync_time");

            if (!$redis_result) {
                $redis_result = [];
            }
            $is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;

            $response = Response::json(array('code' => 200, 'message' => 'All json fetched successfully.', 'cause' => '', 'data' => ['total_record' => $total_row, 'is_next_page' => $is_next_page, 'last_sync_time' => $last_sync_time, 'data' => $redis_result]));
            //$response = Response::json(array('code' => 200, 'message' => 'Sample images fetched successfully.', 'cause' => '', 'data' => $redis_result));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));


            //$response = Response::json(array('code' => 200, 'message' => 'Featured Background Images added successfully!.', 'cause' => '', 'data' => json_decode('{}')));

        } catch
        (Exception $e) {
            Log::error("getJsonSampleDataWithLastSyncTime_webp Error :", ['Error : ' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get json data with last_sync_time.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /**
     * @api {post} getDeletedJsonId   getDeletedJsonId
     * @apiName getDeletedJsonId
     * @apiGroup User
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "device_info":"", //optional
     * "json_id_list":[
     * 101,
     * 102,
     * 103,
     * 104,
     * 105,
     * 95,
     * 106
     * ]
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Deleted json id fetched successfully.",
     * "cause": "",
     * "data": {
     * "json_id_list": [
     * 101,
     * 102,
     * 103,
     * 104,
     * 105,
     * 95
     * ]
     * }
     * }
     */
    public function getDeletedJsonId(Request $request_body)
    {

        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());

            if ($request != NULL) {
                $json_id_list = $request->json_id_list;
                $new_array = array();

                //Log::info('Request data of getDeletedJsonId :',['request' => $request]);


                foreach ($json_id_list as $key) {

                    $result = DB::select('SELECT id AS json_id
                                                    FROM images
                                                    WHERE id = ?', [$key]);

                    if (count($result) == 0) {
                        $new_array[] = $key;
                    }
                }

                $result_array = array('json_id_list' => $new_array);
                $result = json_decode(json_encode($result_array), true);

                $response = Response::json(array('code' => 200, 'message' => 'Deleted json id fetched successfully.', 'cause' => '', 'data' => $result));


                //$response = Response::json(array('code' => 200, 'message' => 'Featured Background Images added successfully!.', 'cause' => '', 'data' => json_decode('{}')));

            } else {
                $response = Response::json(array('code' => 201, 'message' => 'Invalid request parameter.', 'cause' => '', 'data' => json_decode("{}")));

            }

            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));


        } catch
        (Exception $e) {
            Log::error("getDeletedJsonId Error :", ['Error : ' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get deleted json id.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    public function getJsonSampleDataWithLastSyncTime_webpIos(Request $request_body)
    {

        try {

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('sub_category_id', 'catalog_id', 'page', 'item_count', 'last_sync_time'), $request)) != '')
                return $response;


            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $this->catalog_id = $request->catalog_id;
            $this->sub_category_id = $request->sub_category_id;
            $this->last_sync_date = $request->last_sync_time;

            $this->item_count = $request->item_count;
            $this->page = $request->page;
            $this->order_by = isset($request->order_by) ? $request->order_by : 'size';
            $this->order_type = isset($request->order_type) ? $request->order_type : 'DESC';
            $this->offset = ($this->page - 1) * $this->item_count;

            if ($this->catalog_id == 0) {
                $total_row_result = DB::select('SELECT COUNT(*) AS total
                                                    FROM images
                                                    WHERE catalog_id IN (SELECT catalog_id
                                                                     FROM sub_category_catalog
                                                                     WHERE sub_category_id = ?) AND is_featured = 1 and updated_at >= ?', [$this->sub_category_id, $request->last_sync_time]);
                $total_row = $total_row_result[0]->total;
            } else {
                $total_row_result = DB::select('SELECT COUNT(*) as total FROM images WHERE catalog_id = ? AND updated_at >= ?', [$this->catalog_id, $request->last_sync_time]);
                $total_row = $total_row_result[0]->total;
            }

            $last_created_record = DB::select('SELECT updated_at FROM images WHERE catalog_id = ? ORDER BY updated_at DESC LIMIT 1', [$this->catalog_id]);

            if (count($last_created_record) >= 1) {
                $last_sync_time = $last_created_record[0]->updated_at;

            } else {
                $last_sync_time = date("Y-m-d H:i:s");
            }


            //Log::info('request_data', ['request_data' => $request]);

            if (!Cache::has("pel:getJsonSampleDataWithLastSyncTime_webpIos$this->page:$this->item_count:$this->catalog_id:$this->sub_category_id:$request->last_sync_time")) {
                $result = Cache::rememberforever("getJsonSampleDataWithLastSyncTime_webpIos$this->page:$this->item_count:$this->catalog_id:$this->sub_category_id:$request->last_sync_time", function () {

                    if ($this->catalog_id == 0) {
                        $result = DB::select('SELECT
                                                  id as json_id,
                                                  IF(attribute1 != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_NEW_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",attribute1),"") as sample_image,
                                                  is_free,
                                                  is_featured,
                                                  is_portrait,
                                                  coalesce(height,0) AS height,
                                                  coalesce(width,0) AS width,
                                                  updated_at
                                                FROM
                                                  images
                                                WHERE
                                                  catalog_id in(select catalog_id FROM sub_category_catalog WHERE sub_category_id = ?) and
                                                  is_featured = 1 AND
                                                  updated_at >= ?
                                                order by updated_at DESC LIMIT ?, ?', [$this->sub_category_id, $this->last_sync_date, $this->offset, $this->item_count]);


                    } else {
                        $result = DB::select('SELECT
                                               id as json_id,
                                               IF(attribute1 != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_NEW_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",attribute1),"") as sample_image,
                                               is_free,
                                               is_featured,
                                               is_portrait,
                                               coalesce(height,0) AS height,
                                               coalesce(width,0) AS width,
                                               updated_at
                                                FROM
                                                images
                                                WHERE
                                                catalog_id = ? AND
                                                updated_at >= ?
                                                order by updated_at DESC LIMIT ?, ?', [$this->catalog_id, $this->last_sync_date, $this->offset, $this->item_count]);


                    }

                    return $result;
                });
            }

            $redis_result = Cache::get("getJsonSampleDataWithLastSyncTime_webpIos$this->page:$this->item_count:$this->catalog_id:$this->sub_category_id:$request->last_sync_time");

            if (!$redis_result) {
                $redis_result = [];
            }
            $is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;

            $response = Response::json(array('code' => 200, 'message' => 'All json fetched successfully.', 'cause' => '', 'data' => ['total_record' => $total_row, 'is_next_page' => $is_next_page, 'last_sync_time' => $last_sync_time, 'data' => $redis_result]));
            //$response = Response::json(array('code' => 200, 'message' => 'Sample images fetched successfully.', 'cause' => '', 'data' => $redis_result));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));


            //$response = Response::json(array('code' => 200, 'message' => 'Featured Background Images added successfully!.', 'cause' => '', 'data' => json_decode('{}')));

        } catch
        (Exception $e) {
            Log::error("getJsonSampleDataWithLastSyncTime_webpIos Error :", ['Error : ' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get json data with last_sync_time.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /**
     * @api {post} getFeaturedJsonSampleData_webp   getFeaturedJsonSampleData_webp
     * @apiName getFeaturedJsonSampleData_webp
     * @apiGroup User
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "sub_category_id":51
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "All json fetched successfully.",
     * "cause": "",
     * "data": {
     * "result": [
     * {
     * "catalog_id": 168,
     * "name": "Business Card Catalog2",
     * "thumbnail_img": "http://192.168.0.113/photo_editor_lab_backend/image_bucket/thumbnail/5a1d0851d6d32_catalog_img_1511852113.png",
     * "compressed_img": "http://192.168.0.113/photo_editor_lab_backend/image_bucket/compressed/5a1d0851d6d32_catalog_img_1511852113.png",
     * "original_img": "http://192.168.0.113/photo_editor_lab_backend/image_bucket/original/5a1d0851d6d32_catalog_img_1511852113.png",
     * "is_free": 1,
     * "is_featured": 1,
     * "updated_at": "2018-08-11 04:13:20",
     * "featured_cards": [
     * {
     * "json_id": 414,
     * "sample_image": "http://192.168.0.113/photo_editor_lab_backend/image_bucket/webp_thumbnail/5a1f9747c534f_json_image_1512019783.webp",
     * "is_free": 1,
     * "is_featured": 1,
     * "is_portrait": 0,
     * "height": 300,
     * "width": 525,
     * "updated_at": "2018-08-31 10:02:15"
     * },
     * {
     * "json_id": 415,
     * "sample_image": "http://192.168.0.113/photo_editor_lab_backend/image_bucket/webp_thumbnail/5a1f974dc5c1a_json_image_1512019789.webp",
     * "is_free": 1,
     * "is_featured": 1,
     * "is_portrait": 0,
     * "height": 300,
     * "width": 525,
     * "updated_at": "2018-08-31 10:02:03"
     * },
     * {
     * "json_id": 417,
     * "sample_image": "http://192.168.0.113/photo_editor_lab_backend/image_bucket/webp_thumbnail/5a1f97592443d_json_image_1512019801.webp",
     * "is_free": 1,
     * "is_featured": 1,
     * "is_portrait": 0,
     * "height": 300,
     * "width": 525,
     * "updated_at": "2018-08-31 10:02:03"
     * },
     * {
     * "json_id": 418,
     * "sample_image": "http://192.168.0.113/photo_editor_lab_backend/image_bucket/webp_thumbnail/5a1f975f6f461_json_image_1512019807.webp",
     * "is_free": 1,
     * "is_featured": 1,
     * "is_portrait": 0,
     * "height": 300,
     * "width": 525,
     * "updated_at": "2018-08-31 10:02:02"
     * },
     * {
     * "json_id": 419,
     * "sample_image": "http://192.168.0.113/photo_editor_lab_backend/image_bucket/webp_thumbnail/5a1f9765255c2_json_image_1512019813.webp",
     * "is_free": 1,
     * "is_featured": 1,
     * "is_portrait": 0,
     * "height": 300,
     * "width": 525,
     * "updated_at": "2018-08-31 10:02:02"
     * }
     * ]
     * },
     * {
     * "catalog_id": 167,
     * "name": "Business Card Catalog1",
     * "thumbnail_img": "http://192.168.0.113/photo_editor_lab_backend/image_bucket/thumbnail/5a17fab520a09_catalog_img_1511520949.png",
     * "compressed_img": "http://192.168.0.113/photo_editor_lab_backend/image_bucket/compressed/5a17fab520a09_catalog_img_1511520949.png",
     * "original_img": "http://192.168.0.113/photo_editor_lab_backend/image_bucket/original/5a17fab520a09_catalog_img_1511520949.png",
     * "is_free": 1,
     * "is_featured": 1,
     * "updated_at": "2017-11-28 07:42:02",
     * "featured_cards": []
     * }
     * ]
     * }
     * }
     */
    public function getFeaturedJsonSampleData_webp(Request $request_body)
    {

        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('sub_category_id'), $request)) != '')
                return $response;

            $this->sub_category_id = $request->sub_category_id;
            $this->item_count = Config::get('constant.ITEM_COUNT_OF_FEATURED_JSON');
            $this->offset = 0;

            //Log::info('request_data', ['request_data' => $request]);

            if (!Cache::has("pel:getFeaturedJsonSampleData_webp$this->item_count:$this->sub_category_id")) {
                $result = Cache::rememberforever("getFeaturedJsonSampleData_webp$this->item_count:$this->sub_category_id", function () {

                    $catalogs = DB::select('SELECT
                                                  ct.id as catalog_id,
                                                  ct.name,
                                                  IF(ct.image != "",CONCAT("' . Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.image),"") as thumbnail_img,
                                                  IF(ct.image != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.image),"") as compressed_img,
                                                  IF(ct.image != "",CONCAT("' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.image),"") as original_img,
                                                  ct.is_free,
                                                  ct.is_featured,
                                                  ct.updated_at
                                                FROM
                                                  catalog_master as ct,
                                                  sub_category_catalog as sct
                                                WHERE
                                                  sct.sub_category_id = ? AND
                                                  sct.catalog_id=ct.id AND
                                                  sct.is_active=1 AND
                                                  ct.is_featured = 1
                                                order by ct.updated_at DESC', [$this->sub_category_id]);

                    foreach ($catalogs as $key) {
                        $featured_cards = DB::select('SELECT
                                               id as json_id,
                                               IF(attribute1 != "",CONCAT("' . Config::get('constant.WEBP_THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",attribute1),"") as sample_image,
                                               is_free,
                                               is_featured,
                                               is_portrait,
                                               coalesce(height,0) AS height,
                                               coalesce(width,0) AS width,
                                               updated_at
                                                FROM
                                                images
                                                WHERE
                                                catalog_id = ?
                                                order by updated_at DESC LIMIT ?, ?', [$key->catalog_id, $this->offset, $this->item_count]);

                        $key->featured_cards = $featured_cards;

                    }


                    return $catalogs;
                });
            }

            $redis_result = Cache::get("getFeaturedJsonSampleData_webp$this->item_count:$this->sub_category_id");

            if (!$redis_result) {
                $redis_result = [];
            }

            $response = Response::json(array('code' => 200, 'message' => 'Featured cards fetched successfully.', 'cause' => '', 'data' => ['result' => $redis_result]));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));


        } catch
        (Exception $e) {
            Log::error("getFeaturedJsonSampleData_webp Error :", ['Error : ' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get featured json sample data.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /**
     * @api {post} searchCardsBySubCategoryId   searchCardsBySubCategoryId
     * @apiName searchCardsBySubCategoryId
     * @apiGroup User
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "sub_category_id":56,//compulsory
     * "search_category":"Wedd",//compulsory
     * "page":1,//compulsory
     * "item_count":10//compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200, //return 427 when server not find any result related to your search_category
     * "message": "Templates fetched successfully.",
     * "cause": "",
     * "data": {
     * "total_record": 4,
     * "is_next_page": false,
     * "result": [
     * {
     * "json_id": 470,
     * "sample_image": "http://192.168.0.113/photo_editor_lab_backend/image_bucket/webp_thumbnail/5a30ceb599c91_json_image_1513148085.webp",
     * "is_free": 1,
     * "is_featured": 0,
     * "is_portrait": 1,
     * "height": 400,
     * "width": 325,
     * "updated_at": "2018-10-02 11:29:29"
     * },
     * {
     * "json_id": 463,
     * "sample_image": "http://192.168.0.113/photo_editor_lab_backend/image_bucket/webp_thumbnail/5a30cbb7d3d62_json_image_1513147319.webp",
     * "is_free": 1,
     * "is_featured": 0,
     * "is_portrait": 1,
     * "height": 400,
     * "width": 325,
     * "updated_at": "2018-10-02 11:28:40"
     * }
     * ]
     * }
     * }
     */
    public function searchCardsBySubCategoryId(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            //Log::info("getFeaturedCatalogBySubCategoryId Request:", [$request]);
            if (($response = (new VerificationController())->validateRequiredParameter(array('sub_category_id', 'search_category', 'page', 'item_count'), $request)) != '')
                return $response;

            $this->sub_category_id = $request->sub_category_id;
            //$this->search_category = "%" . $request->search_category . "%";
            $this->search_category = $request->search_category;
            $this->page = $request->page;
            $this->item_count = $request->item_count;
            $this->offset = ($this->page - 1) * $this->item_count;

            if (!Cache::has("pel:searchCardsBySubCategoryId$this->sub_category_id:$this->search_category:$this->offset:$this->item_count")) {
                $result = Cache::rememberforever("searchCardsBySubCategoryId$this->sub_category_id:$this->search_category:$this->offset:$this->item_count", function () {


                    $total_row_result = DB::select('SELECT count(*) as total
                                                FROM
                                                  images as im,
                                                  catalog_master AS cm,
                                                  sub_category_catalog AS scc
                                                WHERE
                                                  im.is_active = 1 AND
                                                  im.catalog_id = scc.catalog_id AND
                                                  cm.id = scc.catalog_id AND
                                                  cm.is_featured = 1 AND
                                                  scc.sub_category_id = ? AND
                                                  isnull(im.original_img) AND
                                                  isnull(im.display_img) AND
                                                  MATCH(im.search_category) AGAINST("' . $this->search_category . '")
                                                ORDER BY im.search_category DESC', [$this->sub_category_id]);

                    $total_row = $total_row_result[0]->total;

                    $search_result = DB::select('SELECT
                                                  im.id as json_id,
                                                  IF(im.attribute1 != "",CONCAT("' . Config::get('constant.WEBP_THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",im.attribute1),"") as sample_image,
                                                  im.is_free,
                                                  im.is_featured,
                                                  im.is_portrait,
                                                  coalesce(im.height,0) AS height,
                                                  coalesce(im.width,0) AS width,
                                                  im.updated_at,
                                                  MATCH(im.search_category) AGAINST("' . $this->search_category . '") AS search_text
                                                FROM
                                                  images as im,
                                                  catalog_master AS cm,
                                                  sub_category_catalog AS scc
                                                WHERE
                                                  im.is_active = 1 AND
                                                  im.catalog_id = scc.catalog_id AND
                                                  cm.id = scc.catalog_id AND
                                                  cm.is_featured = 1 AND
                                                  scc.sub_category_id = ? AND
                                                  isnull(im.original_img) AND
                                                  isnull(im.display_img) AND
                                                  MATCH(im.search_category) AGAINST("' . $this->search_category . '")
                                                ORDER BY search_text DESC LIMIT ?, ?', [$this->sub_category_id, $this->offset, $this->item_count]);
                    $code = 200;
                    $message = "Templates fetched successfully.";
                    if (count($search_result) <= 0) {


                        $total_row_result = DB::select('SELECT count(*) as total
                                                            FROM
                                                              images as im,
                                                              catalog_master AS cm,
                                                              sub_category_catalog AS scc
                                                            WHERE
                                                              im.is_active = 1 AND
                                                              im.catalog_id = scc.catalog_id AND
                                                              cm.id = scc.catalog_id AND
                                                              cm.is_featured = 1 AND
                                                              scc.sub_category_id = ? AND
                                                              isnull(im.original_img) AND
                                                              isnull(im.display_img)
                                                            ORDER BY im.updated_at DESC', [$this->sub_category_id]);

                        $total_row = $total_row_result[0]->total;

                        $search_result = DB::select('SELECT
                                                  im.id as json_id,
                                                  IF(im.attribute1 != "",CONCAT("' . Config::get('constant.WEBP_THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",im.attribute1),"") as sample_image,
                                                  im.is_free,
                                                  im.is_featured,
                                                  im.is_portrait,
                                                  coalesce(im.height,0) AS height,
                                                  coalesce(im.width,0) AS width,
                                                  im.updated_at
                                                FROM
                                                  images as im,
                                                  catalog_master AS cm,
                                                  sub_category_catalog AS scc
                                                WHERE
                                                  im.is_active = 1 AND
                                                  im.catalog_id = scc.catalog_id AND
                                                  cm.id = scc.catalog_id AND
                                                  cm.is_featured = 1 AND
                                                  scc.sub_category_id = ? AND
                                                  isnull(im.original_img) AND
                                                  isnull(im.display_img)
                                                ORDER BY im.updated_at DESC LIMIT ?, ?', [$this->sub_category_id, $this->offset, $this->item_count]);
                        $code = 427;
                        $search_text = trim($this->search_category, "%");
                        $message = "Sorry, we couldn't find any templates for '$search_text', but we found some other templates you might like:";
                    }

                    $is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;
                    $search_result = array('total_record' => $total_row, 'is_next_page' => $is_next_page, 'result' => $search_result);

                    $result = array('result' => $search_result, 'code' => $code, 'message' => $message);

                    return $result;
                });


            }

            $redis_result = Cache::get("searchCardsBySubCategoryId$this->sub_category_id:$this->search_category:$this->offset:$this->item_count");

            if (!$redis_result) {
                $redis_result = [];
            }
            //return $redis_result['result'];

            $response = Response::json(array('code' => $redis_result['code'], 'message' => $redis_result['message'], 'cause' => '', 'data' => $redis_result['result']));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            Log::error("searchCardsBySubCategoryId Error :", ['Error : ' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'search cards.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    /* =================================| User Feeds |=============================*/

    /**
     * @api {post} saveUserFeeds saveUserFeeds
     * @apiName saveUserFeeds
     * @apiGroup User
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     *  Key: Authorization
     *  Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * request_data:{
     * "sub_category_id":45,
     * "json_id":2146
     * },
     * "file":"ob.png"
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Image saved successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function saveUserFeeds(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            /*if (!$request_body->has('request_data'))
                return Response::json(array('code' => 201, 'message' => 'required field request_data is missing or empty', 'cause' => '', 'data' => json_decode("{}")));

            $request = json_decode($request_body->input('request_data'));

            //Log::info("request data :", [$request]);
            if (($response = (new VerificationController())->validateRequiredParameter(array('sub_category_id', 'json_id'), $request)) != '')
                return $response;

            if (!$request_body->hasFile('file')) {
                return Response::json(array('code' => 201, 'message' => 'required field file is missing or empty.', 'cause' => '', 'data' => json_decode("{}")));
            } else {
                $image_array = Input::file('file');
                if (($response = (new ImageController())->verifyImage($image_array)) != '')
                    return $response;

                $image = (new ImageController())->generateNewFileName('user_feeds', $image_array);
                //Log::info("file size :",[filesize($profile_img)]);
                (new ImageController())->saveOriginalImage($image);
                (new ImageController())->saveCompressedImage($image);
                (new ImageController())->saveThumbnailImage($image);
                //(new ImageController())->saveImageInToSpaces($image);

            }

            $sub_category_id = $request->sub_category_id;
            $json_id = $request->json_id;
            $create_at = date('Y-m-d H:i:s');

            $data = array('sub_category_id' => $sub_category_id,
                'json_id' => $json_id,
                'image' => $image,
                'create_time' => $create_at);

            DB::beginTransaction();

            $id = DB::table('user_feeds_master')->insertGetId($data);

            DB::commit();*/

            $response = Response::json(array('code' => 200, 'message' => 'Image saved successfully.', 'cause' => '', 'data' => json_decode('{}')));

        } catch (Exception $e) {
            Log::error("saveUserFeeds Error :", ['Error : ' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'save user feeds.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /**
     * @api {post} getUserFeedsBySubCategoryId   getUserFeedsBySubCategoryId
     * @apiName getUserFeedsBySubCategoryId
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "sub_category_id":45, //compulsory
     * "page":1, //compulsory
     * "item_count":10 //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Images fetched successfully.",
     * "cause": "",
     * "data": {
     * "total_record": 1,
     * "is_next_page": false,
     * "result": [
     * {
     * "user_feeds_id": 1,
     * "sub_category_id": 45,
     * "json_id": 2146,
     * "thumbnail_img": "http://192.168.0.113/ob_photolab_backend/image_bucket/thumbnail/5ae99587dc771_user_feeds_1525257607.jpg",
     * "compressed_img": "http://192.168.0.113/ob_photolab_backend/image_bucket/compressed/5ae99587dc771_user_feeds_1525257607.jpg",
     * "original_img": "http://192.168.0.113/ob_photolab_backend/image_bucket/original/5ae99587dc771_user_feeds_1525257607.jpg",
     * "is_active": 0,
     * "create_time": "2018-05-02 10:40:10",
     * "update_time": "2018-05-02 10:40:10"
     * }
     * ]
     * }
     * }
     */
    public function getUserFeedsBySubCategoryId(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());

            if (($response = (new VerificationController())->validateRequiredParameter(array('sub_category_id', 'page', 'item_count'), $request)) != '')
                return $response;

            $this->sub_category_id = $request->sub_category_id;
            $this->page = $request->page;
            $this->item_count = $request->item_count;
            $this->order_by = isset($request->order_by) ? $request->order_by : ''; //field name
            $this->order_type = isset($request->order_type) ? $request->order_type : ''; //asc or desc

            $this->offset = ($this->page - 1) * $this->item_count;
            $total_row_result = DB::select('SELECT COUNT(*) as total FROM user_feeds_master WHERE sub_category_id = ?', [$this->sub_category_id]);
            $total_row = $total_row_result[0]->total;
            $this->base_url = (new ImageController())->getBaseUrl();

            if (!Cache::has("pel:getUserFeedsBySubCategoryId$this->page:$this->item_count:$this->sub_category_id")) {
                $result = Cache::rememberforever("getUserFeedsBySubCategoryId$this->page:$this->item_count:$this->sub_category_id", function () {


                    return DB::select('SELECT
                                          id As user_feeds_id,
                                          sub_category_id,
                                          json_id,
                                          IF(image != "",CONCAT("' . Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",image),"") as thumbnail_img,
                                          IF(image != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",image),"") as compressed_img,
                                          IF(image != "",CONCAT("' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",image),"") as original_img,
                                          is_active,
                                          create_time,
                                          update_time
                                        FROM user_feeds_master
                                        WHERE sub_category_id = ?
                                        ORDER BY
                                            create_time LIMIT ?,?', [$this->sub_category_id, $this->offset, $this->item_count]);


                });
            }

            $redis_result = Cache::get("getUserFeedsBySubCategoryId$this->page:$this->item_count:$this->sub_category_id");

            if (!$redis_result) {
                $redis_result = [];
            }

            $is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;

            $response = Response::json(array('code' => 200, 'message' => 'Images fetched successfully.', 'cause' => '', 'data' => ['total_record' => $total_row, 'is_next_page' => $is_next_page, 'result' => $redis_result]));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));


        } catch (Exception $e) {
            Log::error("getUserFeedsBySubCategoryId Error :", ['Error : ' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get user feeds.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    /**
     * @api {post} deleteUserFeeds deleteUserFeeds
     * @apiName deleteUserFeeds
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     *  Key: Authorization
     *  Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     *  "user_feeds_id":1 //compulsory
     * }
     * @apiSuccessExample Success-Response:
     *{
     * "code": 200,
     * "message": "Image deleted successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function deleteUserFeeds(Request $request_body)
    {
        try {
            $request = json_decode($request_body->getContent());
            //Log::info("request data :", [$request]);
            if (($response = (new VerificationController())->validateRequiredParameter(array('user_feeds_id'), $request)) != '')
                return $response;

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $user_feeds_id = $request->user_feeds_id;

            $fetch_images = DB::select('SELECT * FROM user_feeds_master WHERE id = ?', [$user_feeds_id]);

            foreach ($fetch_images as $key) {

                //(new ImageController())->unlinkImage($image_array);
                $cmp_image_path = '../..' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY') . $key->image;
                $org_image_path = '../..' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY') . $key->image;
                $thumb_image_path = '../..' . Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY') . $key->image;

                if (File::exists($cmp_image_path)) {
                    //File::delete($image_path);
                    unlink($cmp_image_path);
                }
                if (File::exists($org_image_path)) {
                    //File::delete($image_path);
                    unlink($org_image_path);
                }
                if (File::exists($thumb_image_path)) {
                    //File::delete($image_path);
                    unlink($thumb_image_path);
                }
            }


            DB::beginTransaction();
            DB::delete('delete FROM user_feeds_master where id = ? ', [$user_feeds_id]);
            DB::commit();

            $response = Response::json(array('code' => 200, 'message' => 'Image deleted successfully.', 'cause' => '', 'data' => json_decode('{}')));
        } catch (Exception $e) {
            Log::error("deleteUserFeeds Error :", ['Error : ' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'delete image.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /**
     * @api {post} deleteAllUserFeeds deleteAllUserFeeds
     * @apiName deleteAllUserFeeds
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     *  Key: Authorization
     *  Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     *  "sub_category_id":1 //compulsory
     * }
     * @apiSuccessExample Success-Response:
     *{
     * "code": 200,
     * "message": "All images deleted successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function deleteAllUserFeeds(Request $request_body)
    {
        try {
            $request = json_decode($request_body->getContent());
            //Log::info("request data :", [$request]);
            if (($response = (new VerificationController())->validateRequiredParameter(array('sub_category_id'), $request)) != '')
                return $response;

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $sub_category_id = $request->sub_category_id;

            $fetch_images = DB::select('SELECT * FROM user_feeds_master WHERE sub_category_id = ?', [$sub_category_id]);

            foreach ($fetch_images as $key) {

                //(new ImageController())->unlinkImage($image_array);
                $cmp_image_path = '../..' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY') . $key->image;
                $org_image_path = '../..' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY') . $key->image;
                $thumb_image_path = '../..' . Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY') . $key->image;

                if (File::exists($cmp_image_path)) {
                    //File::delete($image_path);
                    unlink($cmp_image_path);
                }
                if (File::exists($org_image_path)) {
                    //File::delete($image_path);
                    unlink($org_image_path);
                }
                if (File::exists($thumb_image_path)) {
                    //File::delete($image_path);
                    unlink($thumb_image_path);
                }
            }

            DB::beginTransaction();
            DB::delete('delete FROM user_feeds_master where sub_category_id = ? ', [$sub_category_id]);
            DB::commit();

            $response = Response::json(array('code' => 200, 'message' => 'All images deleted successfully.', 'cause' => '', 'data' => json_decode('{}')));
        } catch (Exception $e) {
            Log::error("deleteAllUserFeeds Error :", ['Error : ' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'delete all image.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /**
     * @api {post} getDeletedCatalogId   getDeletedCatalogId
     * @apiName getDeletedCatalogId
     * @apiGroup User
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "sub_category_id":81, //optional
     * "catalog_id_list":[ //compulsory
     * 75,
     * 76
     * ]
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Deleted catalog id fetched successfully.",
     * "cause": "",
     * "data": {
     * "catalog_id_list": [
     * 75,
     * 76
     * ]
     * }
     * }
     */
    public function getDeletedCatalogId(Request $request_body)
    {

        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());

            if ($request != NULL) {

                $catalog_id_list = $request->catalog_id_list;
                $sub_category_id = isset($request->sub_category_id) ? $request->sub_category_id : '';
                //Log::info('Request data of getDeletedCatalogId :',['request' => $request]);

                if ($sub_category_id != '') {
                    $new_array = array();
                    foreach ($catalog_id_list as $key) {

                        $result = DB::select('SELECT catalog_id
                                                FROM sub_category_catalog
                                                WHERE
                                                  sub_category_id = ? AND
                                                  catalog_id = ? AND
                                                  is_active = 1
                                                ', [$sub_category_id, $key]);

                        if (count($result) == 0) {
                            $new_array[] = $key;
                        }
                    }

                    $result_array = array('catalog_id_list' => $new_array);
                    $result = json_decode(json_encode($result_array), true);
                } else {
                    $new_array = array();
                    foreach ($catalog_id_list as $key) {

                        $result = DB::select('SELECT id AS catalog_id
                                        FROM
                                          catalog_master
                                        WHERE
                                          is_featured = 1 AND
                                          id = ?', [$key]);

                        if (count($result) == 0) {
                            $new_array[] = $key;
                        }
                    }

                    $result_array = array('catalog_id_list' => $new_array);
                    $result = json_decode(json_encode($result_array), true);
                }

                $response = Response::json(array('code' => 200, 'message' => 'Deleted catalog id fetched successfully.', 'cause' => '', 'data' => $result));


                //$response = Response::json(array('code' => 200, 'message' => 'Featured Background Images added successfully!.', 'cause' => '', 'data' => json_decode('{}')));

            } else {
                $response = Response::json(array('code' => 201, 'message' => 'Invalid request parameter.', 'cause' => '', 'data' => json_decode("{}")));

            }
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));


        } catch
        (Exception $e) {
            Log::error("getDeletedCatalogId Error :", ['Error : ' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get catalog json id.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /**
     * @api {post} getLink   getLink
     * @apiName getLink
     * @apiGroup User
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "sub_category_id":2,
     * "platform":"Android" //Or iOS
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Advertise Link Fetched Successfully.",
     * "cause": "",
     * "data": {
     * "total_record": 1,
     * "link_list": [
     * {
     * "advertise_link_id": 51,
     * "name": "QR Scanner",
     * "thumbnail_img": "http://localhost/ob_photolab_backend/image_bucket/thumbnail/5a043096329d3_banner_image_1510224022.jpg",
     * "compressed_img": "http://localhost/ob_photolab_backend/image_bucket/compressed/5a043096329d3_banner_image_1510224022.jpg",
     * "original_img": "http://localhost/ob_photolab_backend/image_bucket/original/5a043096329d3_banner_image_1510224022.jpg",
     * "app_logo_thumbnail_img": "http://localhost/ob_photolab_backend/image_bucket/thumbnail/5a043096329d3_app_logo_image_1510224022.jpg",
     * "app_logo_compressed_img": "http://localhost/ob_photolab_backend/image_bucket/compressed/5a043096329d3_app_logo_image_1510224022.jpg",
     * "app_logo_original_img": "http://localhost/ob_photolab_backend/image_bucket/original/5a043096329d3_app_logo_image_1510224022.jpg",
     * "url": "https://play.google.com/store/apps/details?id=com.optimumbrewlab.dqnentrepreneur&hl=en",
     * "platform": "Android",
     * "app_description": "This is test description."
     * }
     * ]
     * }
     * }
     */
    public function getLink(Request $request_body)
    {
        try {
            $request = json_decode($request_body->getContent());
            //Log::info([$request]);
            if (($response = (new VerificationController())->validateRequiredParameter(array('sub_category_id', 'platform'), $request)) != '')
                return $response;

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $this->sub_category_id = $request->sub_category_id;
            $this->platform = $request->platform;


            $total_row_result = DB::select('SELECT COUNT(*) as total FROM  advertise_links as adl, sub_category_advertise_links as sadl WHERE adl.platform = ? AND sadl.advertise_link_id=adl.id AND sadl.sub_category_id = ? AND sadl.is_active = ?', [$this->platform, $this->sub_category_id, 1]);
            $total_row = $total_row_result[0]->total;

            if (!Cache::has("pel:getLink$this->platform->$this->sub_category_id")) {
                $result = Cache::rememberforever("getLink$this->platform->$this->sub_category_id", function () {
                    return DB::select('SELECT
                                        adl.id as advertise_link_id,
                                        adl.name,
                                        IF(adl.image != "",CONCAT("' . Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",adl.image),"") as thumbnail_img,
                                        IF(adl.image != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",adl.image),"") as compressed_img,
                                        IF(adl.image != "",CONCAT("' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",adl.image),"") as original_img,
                                        IF(adl.app_logo_img != "",CONCAT("' . Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",adl.app_logo_img),"") as app_logo_thumbnail_img,
                                        IF(adl.app_logo_img != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",adl.app_logo_img),"") as app_logo_compressed_img,
                                        IF(adl.app_logo_img != "",CONCAT("' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",adl.app_logo_img),"") as app_logo_original_img,
                                        adl.url,
                                        adl.platform,
                                        if(adl.app_description!="",adl.app_description,"") as app_description
                                      FROM
                                        advertise_links as adl,
                                        sub_category_advertise_links as sadl
                                      WHERE
                                        adl.platform = ? AND
                                        sadl.advertise_link_id=adl.id AND
                                        sadl.sub_category_id = ? AND
                                        sadl.is_active = 1
                                      order by adl.updated_at DESC', [$this->platform, $this->sub_category_id]);
                });
            }

            $redis_result = Cache::get("getLink$this->platform->$this->sub_category_id");

            if (!$redis_result) {
                $redis_result = [];
            }

            $response = Response::json(array('code' => 200, 'message' => 'Advertise Link Fetched Successfully.', 'cause' => '', 'data' => ['total_record' => $total_row, 'link_list' => $redis_result]));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            Log::error("getLink Error :", ['Error : ' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . ' get Link.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    /**
     * @api {post} verifyPromoCode   verifyPromoCode
     * @apiName verifyPromoCode
     * @apiGroup User
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "promo_code":"123", //compulsory
     * "package_name":"com.bg.invitationcardmaker", //compulsory
     * "device_udid":"e9e24a9ce6ca5498", //compulsory
     * "device_platform":1 //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Promo code verified successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function verifyPromoCode(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());

            if (($response = (new VerificationController())->validateRequiredParameter(array('promo_code', 'package_name', 'device_udid', 'device_platform'), $request)) != '')
                return $response;

            $promo_code = $request->promo_code;
            $package_name = $request->package_name;
            $device_udid = $request->device_udid;
            $device_platform = $request->device_platform;

            $is_matched = DB::select('SELECT * FROM promocode_master WHERE
                                          promo_code = ? AND
                                          package_name = ? AND
                                          device_udid = ? AND
                                          device_platform = ?', [$promo_code, $package_name, $device_udid, $device_platform]);

            if (count($is_matched) > 0) {
                if ($is_matched[0]->status == 0) {
                    DB::beginTransaction();
                    DB::update('UPDATE promocode_master SET status = 1 WHERE id = ?', [$is_matched[0]->id]);
                    DB::commit();
                    $response = Response::json(array('code' => 200, 'message' => 'Promo code verified successfully.', 'cause' => '', 'data' => json_decode("{}")));

                } else {
                    $response = Response::json(array('code' => 201, 'message' => 'Promo code already used.', 'cause' => '', 'data' => json_decode("{}")));

                }
            } else {
                $response = Response::json(array('code' => 201, 'message' => 'Invalid promo code.', 'cause' => '', 'data' => json_decode("{}")));
            }

            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));


        } catch (Exception $e) {
            Log::error("verifyPromoCode Error :", ['Error : ' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'verify promo code.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    /* =================================| Advertise Server Id |============================= */

    /**
     * @api {post} getAdvertiseServerIdForUser   getAdvertiseServerIdForUser
     * @apiName getAdvertiseServerIdForUser
     * @apiGroup User
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "sub_category_id":1 //compulsory
     * "device_platform":1 //compulsory 1=ios, 2=android
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Advertise server id fetched successfully.",
     * "cause": "",
     * "data": {
     * "result": [
     * {
     * "advertise_category_id": 3,
     * "advertise_category": "Rewarded Video",
     * "is_active": 1,
     * "create_time": "2018-07-16 09:07:07",
     * "update_time": "2018-07-16 09:07:07",
     * "server_id_list": [
     * {
     * "sub_category_advertise_server_id": 14,
     * "advertise_category_id": 3,
     * "sub_category_id": 66,
     * "server_id": "test Rewarded Video ID 0",
     * "is_active": 1,
     * "create_time": "2018-07-16 13:35:49",
     * "update_time": "2018-07-16 13:42:45"
     * }
     * ]
     * },
     * {
     * "advertise_category_id": 1,
     * "advertise_category": "Banner",
     * "is_active": 1,
     * "create_time": "2018-07-16 09:06:47",
     * "update_time": "2018-07-16 09:06:47",
     * "server_id_list": [
     * {
     * "sub_category_advertise_server_id": 16,
     * "advertise_category_id": 1,
     * "sub_category_id": 66,
     * "server_id": "test Banner ID 2",
     * "is_active": 1,
     * "create_time": "2018-07-16 13:38:24",
     * "update_time": "2018-07-16 13:38:24"
     * }
     * ]
     * },
     * {
     * "advertise_category_id": 2,
     * "advertise_category": "Intertial",
     * "is_active": 1,
     * "create_time": "2018-07-16 09:06:47",
     * "update_time": "2018-07-16 09:06:47",
     * "server_id_list": []
     * }
     * ]
     * }
     * }
     */
    public function getAdvertiseServerIdForUser(Request $request)
    {
        try {

            $request = json_decode($request->getContent());
            //Log::info("getAllAdvertisementToLinkAdvertisement Request :", [$request]);

            if (($response = (new VerificationController())->validateRequiredParameter(array('sub_category_id', 'device_platform'), $request)) != '')
                return $response;

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $this->sub_category_id = $request->sub_category_id;
            $this->device_platform = $request->device_platform;


            //return $total_row;

            if (!Cache::has("pel:getAdvertiseServerIdForUser$this->sub_category_id:$this->device_platform")) {
                $result = Cache::rememberforever("getAdvertiseServerIdForUser$this->sub_category_id:$this->device_platform", function () {

                    $category = DB::select('SELECT
                                          id AS advertise_category_id,
                                          advertise_category,
                                          is_active,
                                          create_time,
                                          update_time
                                        FROM
                                          advertise_category_master
                                        WHERE
                                          is_active=1
                                        order by update_time DESC');

                    foreach ($category as $key) {
                        $server_id_group_by = DB::select('SELECT
                                          id AS sub_category_advertise_server_id,
                                          advertise_category_id,
                                          sub_category_id,
                                          server_id,
                                          is_active,
                                          create_time,
                                          update_time
                                        FROM
                                          sub_category_advertise_server_id_master
                                        WHERE
                                          sub_category_id = ? AND
                                          advertise_category_id = ? AND
                                          device_platform = ? AND
                                          is_active=1
                                        order by update_time DESC', [$this->sub_category_id, $key->advertise_category_id, $this->device_platform]);

                        $key->server_id_list = $server_id_group_by;
                    }
                    return $category;

                });

            }

            $redis_result = Cache::get("getAdvertiseServerIdForUser$this->sub_category_id:$this->device_platform");

            if (!$redis_result) {
                $redis_result = [];
            }

            $response = Response::json(array('code' => 200, 'message' => 'Advertise server id fetched successfully.', 'cause' => '', 'data' => ['result' => $redis_result]));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            Log::error("getAdvertiseServerIdForUser Error :", ['Error : ' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . ' get advertise server id.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    /**
     * @api {post} getLinkWithoutToken   getLinkWithoutToken
     * @apiName getLinkWithoutToken
     * @apiGroup User
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "sub_category_id":2,
     * "platform":"Android" //Or iOS
     * }
     * @apiSuccessExample Success-Response:
     *{
     * "code": 200,
     * "message": "Advertise Link Fetched Successfully.",
     * "cause": "",
     * "data": {
     * "total_record": 14,
     * "link_list": [
     * {
     * "advertise_link_id": 77,
     * "name": "Romantic Love Photo Editor",
     * "thumbnail_img": "http://192.168.0.113/photo_editor_lab_backend/image_bucket/thumbnail/5a1e813f47368_banner_image_1511948607.png",
     * "compressed_img": "http://192.168.0.113/photo_editor_lab_backend/image_bucket/compressed/5a1e813f47368_banner_image_1511948607.png",
     * "original_img": "http://192.168.0.113/photo_editor_lab_backend/image_bucket/original/5a1e813f47368_banner_image_1511948607.png",
     * "app_logo_thumbnail_img": "http://192.168.0.113/photo_editor_lab_backend/image_bucket/thumbnail/5a1e814000aa9_app_logo_image_1511948608.png",
     * "app_logo_compressed_img": "http://192.168.0.113/photo_editor_lab_backend/image_bucket/compressed/5a1e814000aa9_app_logo_image_1511948608.png",
     * "app_logo_original_img": "http://192.168.0.113/photo_editor_lab_backend/image_bucket/original/5a1e814000aa9_app_logo_image_1511948608.png",
     * "url": "https://play.google.com/store/apps/details?id=com.optimumbrewlab.lovephotoeditor",
     * "platform": "Android",
     * "app_description": "Romantic Love Photo Editor - Realistic Photo Effects, Beautiful Photo Frames, Stickers, etc."
     * }
     * ]
     * }
     * }
     */
    public function getLinkWithoutToken(Request $request_body)
    {
        try {
            $request = json_decode($request_body->getContent());
            //Log::info([$request]);
            if (($response = (new VerificationController())->validateRequiredParameter(array('sub_category_id', 'platform'), $request)) != '')
                return $response;

            $this->sub_category_id = $request->sub_category_id;
            $this->platform = $request->platform;


            $total_row_result = DB::select('SELECT COUNT(*) as total FROM  advertise_links as adl, sub_category_advertise_links as sadl WHERE adl.platform = ? AND sadl.advertise_link_id=adl.id AND sadl.sub_category_id = ? AND sadl.is_active = ?', [$this->platform, $this->sub_category_id, 1]);
            $total_row = $total_row_result[0]->total;

            if (!Cache::has("pel:getLinkWithoutToken$this->platform->$this->sub_category_id")) {
                $result = Cache::rememberforever("getLinkWithoutToken$this->platform->$this->sub_category_id", function () {
                    return DB::select('SELECT
                                        adl.id as advertise_link_id,
                                        adl.name,
                                        IF(adl.image != "",CONCAT("' . Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",adl.image),"") as thumbnail_img,
                                        IF(adl.image != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",adl.image),"") as compressed_img,
                                        IF(adl.image != "",CONCAT("' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",adl.image),"") as original_img,
                                        IF(adl.app_logo_img != "",CONCAT("' . Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",adl.app_logo_img),"") as app_logo_thumbnail_img,
                                        IF(adl.app_logo_img != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",adl.app_logo_img),"") as app_logo_compressed_img,
                                        IF(adl.app_logo_img != "",CONCAT("' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",adl.app_logo_img),"") as app_logo_original_img,
                                        adl.url,
                                        adl.platform,
                                        if(adl.app_description!="",adl.app_description,"") as app_description
                                      FROM
                                        advertise_links as adl,
                                        sub_category_advertise_links as sadl
                                      WHERE
                                        adl.platform = ? AND
                                        sadl.advertise_link_id=adl.id AND
                                        sadl.sub_category_id = ? AND
                                        sadl.is_active = 1
                                      order by adl.updated_at DESC', [$this->platform, $this->sub_category_id]);
                });
            }

            $redis_result = Cache::get("getLinkWithoutToken$this->platform->$this->sub_category_id");

            if (!$redis_result) {
                $redis_result = [];
            }

            $response = Response::json(array('code' => 200, 'message' => 'Advertise Link Fetched Successfully.', 'cause' => '', 'data' => ['total_record' => $total_row, 'link_list' => $redis_result]));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            Log::error("getLink Error :", ['Error : ' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . ' get Link.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    /**
     * @api {post} addZipFile   addZipFile
     * @apiName addZipFile
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * file[]:1.jpg,
     * file[]:2.jpg,
     * file[]:3.jpg,
     * file[]:4.jpg
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Json images added successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function addZipFile(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            if ($request_body->hasFile('file')) {
                $file = Input::file('file');

                if (($response = (new ImageController())->verifyZipFile($file)) != '')
                    return $response;

                (new ImageController())->saveZipFile($file);

            }

            $response = Response::json(array('code' => 200, 'message' => 'Zip file uploaded successfully.', 'cause' => '', 'data' => json_decode('{}')));

        } catch
        (Exception $e) {
            Log::error("addCatalogImagesForJson Error :", ['Error : ' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'add json images.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /**
     * @api {post} getZipFile   getZipFile
     * @apiName getZipFile
     * @apiGroup User
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "file_name":"sample.zip" //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Featured json images fetch successfully.",
     * "cause": "",
     * "data": {
     * "url": "sample.zip"
     * }
     * }
     */
    public function getZipFile(Request $request_body)
    {

        try {

            $request = json_decode($request_body->getContent());

            if (($response = (new VerificationController())->validateRequiredParameter(array('file_name'), $request)) != '')
                return $response;

            $file_name = $request->file_name;

            $result_array = array('url' => Config::get('constant.ZIP_FILE_DIRECTORY_OF_DIGITAL_OCEAN') . $file_name);

            $result = json_decode(json_encode($result_array), true);


            $response = Response::json(array('code' => 200, 'message' => 'Zip file fetched successfully.', 'cause' => '', 'data' => $result));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch
        (Exception $e) {
            Log::error("getZipFile Error :", ['Error : ' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get zip file.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }


}

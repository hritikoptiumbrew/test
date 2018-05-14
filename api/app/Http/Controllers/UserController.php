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
                                        IF(image != "",CONCAT("' . $this->base_url . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY') . '",image),"") as sample_image,
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
     * "is_featured": 1
     * },
     * {
     * "json_id": 354,
     * "sample_image": "http://192.168.0.113/ob_photolab_backend/image_bucket/compressed/5a0d7f89953aa_catalog_image_1510834057.jpg",
     * "is_free": 1,
     * "is_featured": 1
     * },
     * {
     * "json_id": 342,
     * "sample_image": "http://192.168.0.113/ob_photolab_backend/image_bucket/compressed/5a059c4cbadaa_catalog_image_1510317132.jpg",
     * "is_free": 1,
     * "is_featured": 1
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
                                                  IF(image != "",CONCAT("' . $this->base_url . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY') . '",image),"") as sample_image,
                                                  is_free,
                                                  is_featured,
                                                  is_portrait
                                                FROM
                                                  images
                                                WHERE
                                                  catalog_id in(select catalog_id FROM sub_category_catalog WHERE sub_category_id = ?) and is_featured = 1
                                                order by updated_at DESC LIMIT ?, ?', [$this->sub_category_id, $this->offset, $this->item_count]);

                    } else {
                        $result = DB::select('SELECT
                                               id as json_id,
                                               IF(image != "",CONCAT("' . $this->base_url . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY') . '",image),"") as sample_image,
                                               is_free,
                                               is_featured,
                                               is_portrait
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
                    if(count($result) > 0)
                    {
                        return json_decode($result[0]->json_data);
                    }
                    else
                    {
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
     * "is_featured": 0
     * },
     * {
     * "catalog_id": 167,
     * "name": "Business Card Catalog1",
     * "thumbnail_img": "http://192.168.0.113/ob_photolab_backend/image_bucket/thumbnail/5a17fab520a09_catalog_img_1511520949.png",
     * "compressed_img": "http://192.168.0.113/ob_photolab_backend/image_bucket/compressed/5a17fab520a09_catalog_img_1511520949.png",
     * "original_img": "http://192.168.0.113/ob_photolab_backend/image_bucket/original/5a17fab520a09_catalog_img_1511520949.png",
     * "is_free": 1,
     * "is_featured": 1
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

            $total_row_result = DB::select('SELECT COUNT(*) AS total
                                                FROM sub_category_catalog
                                                WHERE sub_category_id = ? AND
                                                created_at >= ? AND
                                                is_active = 1
                                                ', [$request->sub_category_id, $request->last_sync_time, 1]);
            $total_row = $total_row_result[0]->total;
            //return $total_row;
            $last_created_record = DB::select('SELECT created_at FROM sub_category_catalog WHERE sub_category_id = ? ORDER BY created_at DESC LIMIT 1', [$this->sub_category_id]);

            if (count($last_created_record) >= 1) {
                $last_sync_time = $last_created_record[0]->created_at;

            } else {
                $last_sync_time = date("Y-m-d H:i:s");
            }


            if (!Cache::has("pel:getCatalogBySubCategoryIdWithLastSyncTime$request->sub_category_id:$request->last_sync_time")) {
                $result = Cache::rememberforever("getCatalogBySubCategoryIdWithLastSyncTime$request->sub_category_id:$request->last_sync_time", function () {


                    if ($this->last_sync_time == 0) {
                        return DB::select('SELECT
                                          ct.id as catalog_id,
                                          ct.name,
                                          IF(ct.image != "",CONCAT("' . $this->base_url . Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY') . '",ct.image),"") as thumbnail_img,
                                          IF(ct.image != "",CONCAT("' . $this->base_url . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY') . '",ct.image),"") as compressed_img,
                                          IF(ct.image != "",CONCAT("' . $this->base_url . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY') . '",ct.image),"") as original_img,
                                          ct.is_free,
                                          ct.is_featured
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
                                          IF(ct.image != "",CONCAT("' . $this->base_url . Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY') . '",ct.image),"") as thumbnail_img,
                                          IF(ct.image != "",CONCAT("' . $this->base_url . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY') . '",ct.image),"") as compressed_img,
                                          IF(ct.image != "",CONCAT("' . $this->base_url . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY') . '",ct.image),"") as original_img,
                                          ct.is_free,
                                          ct.is_featured
                                        FROM
                                          catalog_master as ct,
                                          sub_category_catalog as sct
                                        WHERE
                                          sct.sub_category_id = ? AND
                                          sct.catalog_id=ct.id AND
                                          sct.is_active=1 AND
                                          sct.created_at >= ?
                                        order by ct.updated_at DESC', [$this->sub_category_id, $this->last_sync_time]);
                    }


                });


            }

            $redis_result = Cache::get("getCatalogBySubCategoryIdWithLastSyncTime$request->sub_category_id:$request->last_sync_time");

            if (!$redis_result) {
                $redis_result = [];
            }

            $response = Response::json(array('code' => 200, 'message' => 'Catalog Fetched Successfully.', 'cause' => '', 'data' => ['total_record' => $total_row, 'last_sync_time' => $last_sync_time, 'category_list' => $redis_result]));
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
     * "is_portrait": 1
     * },
     * {
     * "json_id": 406,
     * "sample_image": "http://192.168.0.113/ob_photolab_backend/image_bucket/compressed/5a1cfd7fadfc0_json_image_1511849343.jpg",
     * "is_free": 1,
     * "is_featured": 1,
     * "is_portrait": 1
     * },
     * {
     * "json_id": 405,
     * "sample_image": "http://192.168.0.113/ob_photolab_backend/image_bucket/compressed/5a1cfc994b4bd_json_image_1511849113.jpg",
     * "is_free": 1,
     * "is_featured": 1,
     * "is_portrait": 1
     * },
     * {
     * "json_id": 404,
     * "sample_image": "http://192.168.0.113/ob_photolab_backend/image_bucket/compressed/5a1cf9656d54c_json_image_1511848293.jpg",
     * "is_free": 1,
     * "is_featured": 1,
     * "is_portrait": 1
     * },
     * {
     * "json_id": 401,
     * "sample_image": "http://192.168.0.113/ob_photolab_backend/image_bucket/compressed/5a1cefcb29a2b_json_image_1511845835.jpg",
     * "is_free": 0,
     * "is_featured": 1,
     * "is_portrait": 0
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
                                                  IF(image != "",CONCAT("' . $this->base_url . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY') . '",image),"") as sample_image,
                                                  is_free,
                                                  is_featured,
                                                  is_portrait
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
                                               IF(image != "",CONCAT("' . $this->base_url . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY') . '",image),"") as sample_image,
                                               is_free,
                                               is_featured,
                                               is_portrait
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
            $json_id_list = $request->json_id_list;
            $new_array = array();

            //Log::debug('request of getDeletedJsonId : ',[$request]);

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
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));


            //$response = Response::json(array('code' => 200, 'message' => 'Featured Background Images added successfully!.', 'cause' => '', 'data' => json_decode('{}')));

        } catch
        (Exception $e) {
            Log::error("getDeletedJsonId Error :", ['Error : ' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get deleted json id.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
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
                                          IF(image != "",CONCAT("' . $this->base_url . Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY') . '",image),"") as thumbnail_img,
                                          IF(image != "",CONCAT("' . $this->base_url . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY') . '",image),"") as compressed_img,
                                          IF(image != "",CONCAT("' . $this->base_url . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY') . '",image),"") as original_img,
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
                                        IF(adl.image != "",CONCAT("' . $this->base_url . Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY') . '",adl.image),"") as thumbnail_img,
                                        IF(adl.image != "",CONCAT("' . $this->base_url . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY') . '",adl.image),"") as compressed_img,
                                        IF(adl.image != "",CONCAT("' . $this->base_url . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY') . '",adl.image),"") as original_img,
                                        IF(adl.app_logo_img != "",CONCAT("' . $this->base_url . Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY') . '",adl.app_logo_img),"") as app_logo_thumbnail_img,
                                        IF(adl.app_logo_img != "",CONCAT("' . $this->base_url . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY') . '",adl.app_logo_img),"") as app_logo_compressed_img,
                                        IF(adl.app_logo_img != "",CONCAT("' . $this->base_url . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY') . '",adl.app_logo_img),"") as app_logo_original_img,
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



}

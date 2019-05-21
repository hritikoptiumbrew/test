<?php

namespace App\Http\Controllers;

use App\Jobs\EmailJob;
use App\Permission;
use App\Role;
use Aws\Credentials\Credentials;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Artisan;
use Response;
use Config;
use DB;
use Log;
use File;
use Cache;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Swift_TransportException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Redis;
use Image;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\Storage;
use Aws\CloudFront\CloudFrontClient;

class AdminController extends Controller
{

    public $item_count;

    public function __construct()
    {
        $this->item_count = Config::get('constant.PAGINATION_ITEM_LIMIT');
        $this->base_url = (new ImageController())->getBaseUrl();

    }

    /* ========================================= Promo Code =========================================*/

    /**
     * @api {post} addPromoCode   addPromoCode
     * @apiName addPromoCode
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     *  Key: Authorization
     *  Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "promo_code":"123",
     * "package_name":"com.bg.invitationcardmaker",
     * "device_udid":"e9e24a9ce6ca5498",
     * "device_platform":1 //1=android, 2=ios
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Promo code added successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function addPromoCode(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            //Log::info("request data :", [$request]);
            if (($response = (new VerificationController())->validateRequiredParameter(array('promo_code', 'package_name', 'device_udid', 'device_platform'), $request)) != '')
                return $response;

            $promo_code = $request->promo_code;
            $package_name = $request->package_name;
            $device_udid = $request->device_udid;
            $device_platform = $request->device_platform;

            if (($response = (new VerificationController())->checkIfPromoCodeExist($promo_code, $package_name)) != '')
                return $response;

            DB::beginTransaction();
            DB::insert('insert into promocode_master(promo_code, package_name, device_udid, device_platform, status) VALUES (?, ?, ?, ?, ?)', [$promo_code, $package_name, $device_udid, $device_platform, 0]);
            DB::commit();

            $response = Response::json(array('code' => 200, 'message' => 'Promo code added successfully.', 'cause' => '', 'data' => json_decode('{}')));
        } catch (Exception $e) {
            Log::error("addPromoCode : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'add promo code.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /**
     * @api {post} getAllPromoCode   getAllPromoCode
     * @apiName getAllPromoCode
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "page":1, //compulsory
     * "item_count":10, //compulsory
     * "order_type":"asc",
     * "order_by":"promo_code"
     * }
     * @apiSuccessExample Success-Response:
     *{
     * "code": 200,
     * "message": "Promo codes fetched successfully.",
     * "cause": "",
     * "data": {
     * "total_record": 2,
     * "is_next_page": false,
     * "result": [
     * {
     * "promo_code_id": 1,
     * "promo_code": "123",
     * "package_name": "com.bg.invitationcardmaker",
     * "device_udid": "e9e24a9ce6ca5498",
     * "device_platform": 1,
     * "status": 0,
     * "create_time": "2018-05-15 09:50:49",
     * "update_time": "2018-05-15 09:50:49"
     * },
     * {
     * "promo_code_id": 2,
     * "promo_code": "test 1",
     * "package_name": "test 2",
     * "device_udid": "test 3",
     * "device_platform": 1,
     * "status": 0,
     * "create_time": "2018-05-15 10:02:35",
     * "update_time": "2018-05-15 10:02:35"
     * }
     * ]
     * }
     * }
     */
    public function getAllPromoCode(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            //Log::info([$request]);
            if (($response = (new VerificationController())->validateRequiredParameter(array('page', 'item_count'), $request)) != '')
                return $response;


            $page = $request->page;
            $item_count = $request->item_count;
            $order_by = isset($request->order_by) ? $request->order_by : ''; //field name
            $order_type = isset($request->order_type) ? $request->order_type : ''; //asc or desc
            $offset = ($page - 1) * $item_count;

            $total_row_result = DB::select('SELECT COUNT(*) as total FROM  promocode_master');
            $total_row = $total_row_result[0]->total;

            if ($order_by == '' && $order_type == '') {


                $result = DB::select('SELECT
                                    id as promo_code_id,
                                    promo_code,
                                    package_name,
                                    device_udid,
                                    device_platform,
                                    status,
                                    create_time,
                                    update_time
                                  FROM
                                  promocode_master
                                  ORDER BY create_time DESC
                                  LIMIT ?,?', [$offset, $item_count]);
            } else {
                $result = DB::select('SELECT
                                    id as promo_code_id,
                                    promo_code,
                                    package_name,
                                    device_udid,
                                    device_platform,
                                    status,
                                    create_time,
                                    update_time
                                  FROM
                                  promocode_master
                                  ORDER BY ' . $order_by . ' ' . $order_type . ' LIMIT ?,?', [$offset, $item_count]);

            }


            $is_next_page = ($total_row > ($offset + $item_count)) ? true : false;

            $response = Response::json(array('code' => 200, 'message' => 'Promo codes fetched successfully.', 'cause' => '', 'data' => ['total_record' => $total_row, 'is_next_page' => $is_next_page, 'result' => $result]));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            Log::error("getAllPromoCode : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get all category.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    /**
     * @api {post} searchPromoCode   searchPromoCode
     * @apiName searchPromoCode
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "search_type":"promo_code", //compulsory
     * "search_query":"12" //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Promo code fetched successfully.",
     * "cause": "",
     * "data": {
     * "result": [
     * {
     * "promo_code_id": 1,
     * "promo_code": "123",
     * "package_name": "com.bg.invitationcardmaker",
     * "device_udid": "e9e24a9ce6ca5498",
     * "device_platform": 1,
     * "status": 0,
     * "create_time": "2018-05-15 09:50:49",
     * "update_time": "2018-05-15 09:50:49"
     * }
     * ]
     * }
     * }
     */
    public function searchPromoCode(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('search_type', 'search_query'), $request)) != '')
                return $response;

            $search_type = $request->search_type;
            $search_query = '%' . $request->search_query . '%';

            $result = DB::select('SELECT
                                    id as promo_code_id,
                                    promo_code,
                                    package_name,
                                    device_udid,
                                    device_platform,
                                    status,
                                    create_time,
                                    update_time
                                  FROM
                                  promocode_master
                                  WHERE ' . $search_type . ' LIKE ?', [$search_query]);

            $response = Response::json(array('code' => 200, 'message' => 'Promo code fetched successfully.', 'cause' => '', 'data' => ['result' => $result]));

        } catch (Exception $e) {
            Log::error("searchPromoCode : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'search promo code.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }


    /* ========================================= Category =========================================*/

    /**
     * @api {post} addCategory   addCategory
     * @apiName AddCategory
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     *  Key: Authorization
     *  Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "name":"Nature"
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Category added successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function addCategory(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('name'), $request)) != '')
                return $response;

            $name = $request->name;
            $create_at = date('Y-m-d H:i:s');
            DB::beginTransaction();

            DB::insert('INSERT INTO category (name,created_at) VALUES(?,?)', [$name, $create_at]);

            DB::commit();

            $response = Response::json(array('code' => 200, 'message' => 'Category added successfully.', 'cause' => '', 'data' => json_decode('{}')));
        } catch (Exception $e) {
            Log::error("addCategory : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'add category.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /**
     * @api {post} updateCategory   updateCategory
     * @apiName updateCategory
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "category_id":1,
     * "name":"Featured"
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Category updated successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function updateCategory(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('category_id', 'name'), $request)) != '')
                return $response;

            $category_id = $request->category_id;
            $name = $request->name;

            DB::beginTransaction();

            DB::update('UPDATE
                              category
                            SET
                              name = ?
                            WHERE
                              id = ? ',
                [$name, $category_id]);


            DB::commit();

            $response = Response::json(array('code' => 200, 'message' => 'Category updated successfully.', 'cause' => '', 'data' => json_decode('{}')));

        } catch (Exception $e) {
            Log::error("updateCategory : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'update category.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }

        return $response;
    }

    /**
     * @api {post} deleteCategory   deleteCategory
     * @apiName deleteCategory
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "category_id":1
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Category deleted successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function deleteCategory(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('category_id'), $request)) != '')
                return $response;

            $category_id = $request->category_id;

            DB::beginTransaction();

            $is_active = 0;

            DB::update('UPDATE category SET is_active=? WHERE id = ? ', [$is_active, $category_id]);

            DB::update('UPDATE sub_category SET is_active = ? WHERE category_id = ?', [$is_active, $category_id]);

            DB::commit();

            $response = Response::json(array('code' => 200, 'message' => 'Category deleted successfully.', 'cause' => '', 'data' => json_decode('{}')));
        } catch (Exception $e) {
            Log::error("deleteCategory : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'delete category.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /**
     * @api {post} getAllCategory   getAllCategory
     * @apiName getAllCategory
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     *  "page":1 //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "All category fetched successfully.",
     * "cause": "",
     * "data": {
     * "total_record": 8,
     * "is_next_page": false,
     * "category_list": [
     * {
     * "category_id": 9,
     * "name": "demo 3"
     * },
     * {
     * "category_id": 8,
     * "name": "demo 2"
     * },
     * {
     * "category_id": 7,
     * "name": "demo1"
     * }
     * ]
     * }
     * }
     */
    public function getAllCategory(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('page'), $request)) != '')
                return $response;


            $page = $request->page;
            //$item_count = Config::get('constant.PAGINATION_ITEM_LIMIT');
            $this->offset = ($page - 1) * $this->item_count;
            $this->is_active = 1;

            if (!Cache::has("pel:getAllCategory$page")) {
                $result = Cache::rememberforever("getAllCategory$page", function () {

                    $total_row_result = DB::select('SELECT COUNT(*) as total FROM  category where is_active=?', [$this->is_active]);
                    $total_row = $total_row_result[0]->total;

                    $result = DB::select('SELECT
                                          ct.id as category_id,
                                          ct.name
                                        FROM
                                          category as ct
                                        WHERE is_active = ?
                                        LIMIT ?,?', [$this->is_active, $this->offset, $this->item_count]);

                    $is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;

                    return array('total_record' => $total_row, 'is_next_page' => $is_next_page, 'category_list' => $result);

                });
            }

            $redis_result = Cache::get("getAllCategory$page");

            if (!$redis_result) {
                $redis_result = [];
            }

            $response = Response::json(array('code' => 200, 'message' => 'All category fetched successfully.', 'cause' => '', 'data' => $redis_result));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            Log::error("getAllCategory : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get all category.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    /**
     * @api {post} searchCategoryByName   searchCategoryByName
     * @apiName searchCategoryByName
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     *  "name":"fea"
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Search category fetched successfully.",
     * "cause": "",
     * "data": {
     * "category_list": [
     * {
     * "category_id": 1,
     * "name": "Featured"
     * }
     * ]
     * }
     * }
     */
    public function searchCategoryByName(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('name'), $request)) != '')
                return $response;

            $name = '%' . $request->name . '%';
            $is_active = 1;

            $result = DB::select('SELECT
                                    ct.id AS category_id,
                                    ct.name
                                  FROM
                                     category AS ct
                                  WHERE
                                    ct.is_active = ? AND
                                    ct.name LIKE ? ', [$is_active, $name]);

            $response = Response::json(array('code' => 200, 'message' => 'Search category fetched successfully.', 'cause' => '', 'data' => ['category_list' => $result]));

        } catch (Exception $e) {
            Log::error("searchCategoryByName : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'search category.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    /* ========================================= Store image into s3 bucket =========================================*/

    public function storeFileIntoS3Bucket(Request $request_body)
    {
        try {

            $create_time = date("Y-m-d H:i:s");
            $base_url = (new ImageController())->getBaseUrl();
            if ($request_body->hasFile('file')) {
                $file = Input::file('file');
                $file_type = $file->getMimeType();

                if (($response = (new ImageController())->verifyImage($file)) != '')
                    return $response;

                $image = (new ImageController())->generateNewFileName('test_image', $file);
                (new ImageController())->saveOriginalImage($image);
                (new ImageController())->saveCompressedImage($image);
                (new ImageController())->saveThumbnailImage($image);

                $original_sourceFile = $base_url . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY') . $image;
                $compressed_sourceFile = $base_url . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY') . $image;
                $thumbnail_sourceFile = $base_url . Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY') . $image;

                //return array($original_sourceFile,$compressed_sourceFile, $thumbnail_sourceFile);
                $disk = Storage::disk('s3');
                $original_targetFile = "imageflyer/original/" . $image;
                $compressed_targetFile = "imageflyer/compressed/" . $image;
                $thumbnail_targetFile = "imageflyer/thumbnail/" . $image;

                $disk->put($original_targetFile, file_get_contents($original_sourceFile), 'public');
                $disk->put($compressed_targetFile, file_get_contents($compressed_sourceFile), 'public');
                $disk->put($thumbnail_targetFile, file_get_contents($thumbnail_sourceFile), 'public');


            } else {
                return $response = Response::json(array('code' => 201, 'message' => 'Required field file is missing or empty.', 'cause' => '', 'data' => json_decode('{}')));
            }

            //$contents = Storage::get();

            //https://s3-ap-southeast-1.amazonaws.com/maystr/original/5ac1b68fe3738_post_img_1522644623.png
            $value = "imageflyer/original/" . $image;
            $disk = \Storage::disk('s3');
            $config = \Config::get('filesystems.disks.s3.bucket');
            if ($disk->exists($value)) {

                $url = "'" . $disk->getDriver()->getAdapter()->getClient()->getObjectUrl($config, $value) . "'";

//                $command = $disk->getDriver()->getAdapter()->getClient()->getCommand('GetObject', [
//                    'Bucket' => $config,
//                    'Key' => $value,
//                    //�ResponseContentDisposition� => �attachment;�//for download
//                ]);
//
//                //return $command;
//               $request = $disk->getDriver()->getAdapter()->getClient()->createPresignedRequest($command, '+10 minutes');
//
//                $generate_url = "'".$request->getUri()."'";
                $result = $url;//array('image_url' => $generate_url);
                $response = Response::json(array('code' => 200, 'message' => 'Post uploaded successfully.', 'cause' => '', 'data' => $result));

                //return $generate_url;
            }


        } catch (Exception $e) {
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'store file into S3 bucket.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            Log::error("storeFileIntoS3Bucket : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            DB::rollback();
        }
        return $response;
    }

    public function getHostName()
    {

        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $host = request()->getHttpHost(); // With port if there is. Eg: mydomain.com:81

            $result_array = array('host_name' => $host);
            $result = json_decode(json_encode($result_array), true);

            $response = Response::json(array('code' => 200, 'message' => 'Host name fetched successfully.', 'cause' => '', 'data' => $result));


        } catch
        (Exception $e) {
            Log::error("getHostName : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get host name.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /* ========================================= Sub Category =========================================*/

    /**
     * @api {post} addSubCategory   addSubCategory
     * @apiName AddSubCategory
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     *  Key: Authorization
     *  Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "request_data":{
     * "category_id":1, //compulsory
     * "name":"Nature", //compulsory
     * "is_featured":1 //compulsory 1=featured (for templates), 0=normal (shapes, textArt,etc...)
     * },
     * "file":image.jpeg //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Sub category added successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function addSubCategory(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            if (!$request_body->has('request_data'))
                return Response::json(array('code' => 201, 'message' => 'Required field request_data is missing or empty.', 'cause' => '', 'data' => json_decode("{}")));

            $request = json_decode($request_body->input('request_data'));

            if (($response = (new VerificationController())->validateRequiredParameter(array('category_id', 'name', 'is_featured'), $request)) != '')
                return $response;
            $category_id = $request->category_id;
            $name = trim($request->name);
            $is_featured = $request->is_featured;
            $create_at = date('Y-m-d H:i:s');

            if (($response = (new VerificationController())->checkIfSubCategoryExist($name, 0)) != '')
                return $response;

            if (!$request_body->hasFile('file')) {
                return Response::json(array('code' => 201, 'message' => 'Required field file is missing or empty.', 'cause' => '', 'data' => json_decode("{}")));
            } else {
                $image_array = Input::file('file');
                if (($response = (new ImageController())->verifyImage($image_array)) != '')
                    return $response;

                $category_img = (new ImageController())->generateNewFileName('sub_category_img', $image_array);
                (new ImageController())->saveOriginalImage($category_img);
                (new ImageController())->saveCompressedImage($category_img);
                (new ImageController())->saveThumbnailImage($category_img);

                if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
                    (new ImageController())->saveImageInToS3($category_img);
                }

            }


            DB::beginTransaction();

            DB::insert('insert into sub_category
                        (name,category_id,image,is_featured,created_at) VALUES(?,?,?,?,?)',
                [$name, $category_id, $category_img, $is_featured, $create_at]);

            DB::commit();

            $response = Response::json(array('code' => 200, 'message' => 'Sub category added successfully.', 'cause' => '', 'data' => json_decode('{}')));
        } catch (Exception $e) {
            Log::error("addSubCategory : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'add sub category.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /**
     * @api {post} updateSubCategory   updateSubCategory
     * @apiName updateSubCategory
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "request_data":{
     * "sub_category_id":2, //compulsory
     * "name":"Love-Category", //optional
     * "is_featured":1 //compulsory 1=featured (for templates), 0=normal (shapes, textArt,etc...)
     * }
     * "file":image.png //optional
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Sub category updated successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function updateSubCategory(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            //Required parameter
            if (!$request_body->has('request_data'))
                return Response::json(array('code' => 201, 'message' => 'Required field request_data is missing or empty.', 'cause' => '', 'data' => json_decode("{}")));

            $request = json_decode($request_body->input('request_data'));

            if (($response = (new VerificationController())->validateRequiredParameter(array('sub_category_id', 'name', 'is_featured'), $request)) != '')
                return $response;

            $sub_category_id = $request->sub_category_id;
            $name = trim($request->name);
            $is_featured = $request->is_featured;
            $image_name = '';

            if (($response = (new VerificationController())->checkIfSubCategoryExist($name, $sub_category_id)) != '')
                return $response;

            if ($request_body->hasFile('file')) {
                $image_array = Input::file('file');

                if (($response = (new ImageController())->verifyImage($image_array)) != '')
                    return $response;

                $sub_category_img = (new ImageController())->generateNewFileName('sub_category_img', $image_array);
                //Log::info("file size :",[filesize($profile_img)]);
                (new ImageController())->saveOriginalImage($sub_category_img);
                (new ImageController())->saveCompressedImage($sub_category_img);
                (new ImageController())->saveThumbnailImage($sub_category_img);

                if (Config::get('constant.STORAGE') === 'S3_BUCKET') {

                    (new ImageController())->saveImageInToS3($sub_category_img);
                }

                $result = DB::select('SELECT image FROM sub_category WHERE id = ?', [$sub_category_id]);
                $image_name = $result[0]->image;
                DB::beginTransaction();
                DB::update('UPDATE
                              sub_category
                            SET
                              name = ?,
                              image = ?,
                              is_featured = ?
                            WHERE
                              id = ? ',
                    [$name, $sub_category_img, $is_featured, $sub_category_id]);

            } else {
                DB::update('UPDATE
                              sub_category
                            SET
                              name = ?,
                              is_featured = ?
                            WHERE
                              id = ? ',
                    [$name, $is_featured, $sub_category_id]);
            }


            DB::commit();

            if ($image_name) {
                //Image Delete in image_bucket
                (new ImageController())->deleteImage($image_name);
            }
            $response = Response::json(array('code' => 200, 'message' => 'Sub category updated successfully.', 'cause' => '', 'data' => json_decode('{}')));

        } catch (Exception $e) {
            Log::error("updateSubCategory : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'update sub category.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /**
     * @api {post} deleteSubCategory   deleteSubCategory
     * @apiName deleteSubCategory
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "sub_category_id":3 //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Sub category deleted successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function deleteSubCategory(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('sub_category_id'), $request)) != '')
                return $response;

            $sub_category_id = $request->sub_category_id;
            $is_active = 0;
            DB::beginTransaction();

            DB::update('UPDATE sub_category SET is_active=? WHERE id = ? ', [$is_active, $sub_category_id]);

            DB::commit();

            $response = Response::json(array('code' => 200, 'message' => 'Sub category deleted successfully.', 'cause' => '', 'data' => json_decode('{}')));
        } catch (Exception $e) {
            Log::error("deleteSubCategory : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'delete sub category.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /**
     * @api {post} getSubCategoryByCategoryId   getSubCategoryByCategoryId
     * @apiName getSubCategoryByCategoryId
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     *  "category_id":1, //compulsory
     *  "page":1, //compulsory
     *  "item_count":100 //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Sub categories fetched successfully.",
     * "cause": "",
     * "data": {
     * "total_record": 33,
     * "is_next_page": true,
     * "category_name": "Sticker",
     * "category_list": [
     * {
     * "sub_category_id": 66,
     * "category_id": 2,
     * "name": "All Templates",
     * "thumbnail_img": "http://192.168.0.113/photo_editor_lab_backend/image_bucket/thumbnail/5c85fb452c3d4_sub_category_img_1552284485.jpg",
     * "compressed_img": "http://192.168.0.113/photo_editor_lab_backend/image_bucket/compressed/5c85fb452c3d4_sub_category_img_1552284485.jpg",
     * "original_img": "http://192.168.0.113/photo_editor_lab_backend/image_bucket/original/5c85fb452c3d4_sub_category_img_1552284485.jpg",
     * "is_featured": 0
     * },
     * {
     * "sub_category_id": 97,
     * "category_id": 2,
     * "name": "Brand Maker",
     * "thumbnail_img": "http://192.168.0.113/photo_editor_lab_backend/image_bucket/thumbnail/5c6d33c860e1e_sub_category_img_1550660552.jpg",
     * "compressed_img": "http://192.168.0.113/photo_editor_lab_backend/image_bucket/compressed/5c6d33c860e1e_sub_category_img_1550660552.jpg",
     * "original_img": "http://192.168.0.113/photo_editor_lab_backend/image_bucket/original/5c6d33c860e1e_sub_category_img_1550660552.jpg",
     * "is_featured": 0
     * }
     * ]
     * }
     * }
     */
    public function getSubCategoryByCategoryId(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('page', 'item_count', 'category_id'), $request)) != '')
                return $response;

            $page = $request->page;
            $this->category_id = $request->category_id;
            $this->item_count_of_sub_category = $request->item_count;
            //$item_count = Config::get('constant.PAGINATION_ITEM_LIMIT');
            $this->offset = ($page - 1) * $this->item_count_of_sub_category;

            if (!Cache::has("pel:getSubCategoryByCategoryId$this->category_id:$page:$this->item_count_of_sub_category")) {
                $result = Cache::rememberforever("getSubCategoryByCategoryId$this->category_id:$page:$this->item_count_of_sub_category", function () {

                    $is_active = 1;

                    //get category name
                    $name = DB::select('SELECT sc.name FROM  category as sc WHERE id = ? and is_active=?', [$this->category_id, $is_active]);
                    $category_name = $name[0]->name;

                    $total_row_result = DB::select('SELECT COUNT(*) as total FROM sub_category WHERE is_active=? and category_id = ?', [$is_active, $this->category_id]);
                    $total_row = $total_row_result[0]->total;


                    $result = DB::select('SELECT
                                        sct.id as sub_category_id,
                                        sct.category_id,
                                        sct.name,
                                        IF(sct.image != "",CONCAT("' . Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",sct.image),"") as thumbnail_img,
                                        IF(sct.image != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",sct.image),"") as compressed_img,
                                        IF(sct.image != "",CONCAT("' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",sct.image),"") as original_img,
                                        sct.is_featured
                                      FROM
                                        sub_category as sct
                                      WHERE
                                        sct.category_id = ?
                                        and
                                        sct.is_active = ?
                                      order by sct.updated_at DESC
                                      LIMIT ?,?', [$this->category_id, $is_active, $this->offset, $this->item_count_of_sub_category]);

                    $is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;

                    return array('total_record' => $total_row, 'is_next_page' => $is_next_page, 'category_name' => $category_name, 'category_list' => $result);

                });
            }

            $redis_result = Cache::get("getSubCategoryByCategoryId$this->category_id:$page:$this->item_count_of_sub_category");

            if (!$redis_result) {
                $redis_result = [];
            }

            $response = Response::json(array('code' => 200, 'message' => 'Sub categories fetched successfully.', 'cause' => '', 'data' => $redis_result));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            Log::error("getSubCategoryByCategoryId : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get all sub category.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    /**
     * @api {post} getAllSubCategory   getAllSubCategory
     * @apiName getAllSubCategory
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "category_id":1 //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Sub categories fetched successfully.",
     * "cause": "",
     * "data": {
     * "total_record": 13,
     * "category_list": [
     * {
     * "sub_category_id": 86,
     * "category_id": 1,
     * "sub_category_name": "Background Changer Frame",
     * "thumbnail_img": "http://192.168.0.113/photo_editor_lab_backend/image_bucket/thumbnail/5b333358a7cf6_category_img_1530082136.png",
     * "compressed_img": "http://192.168.0.113/photo_editor_lab_backend/image_bucket/compressed/5b333358a7cf6_category_img_1530082136.png",
     * "original_img": "http://192.168.0.113/photo_editor_lab_backend/image_bucket/original/5b333358a7cf6_category_img_1530082136.png",
     * "is_featured": 0
     * },
     * {
     * "sub_category_id": 79,
     * "category_id": 1,
     * "sub_category_name": "Video Flyer Frame",
     * "thumbnail_img": "http://192.168.0.113/photo_editor_lab_backend/image_bucket/thumbnail/5afe5d71c4f60_category_img_1526619505.jpg",
     * "compressed_img": "http://192.168.0.113/photo_editor_lab_backend/image_bucket/compressed/5afe5d71c4f60_category_img_1526619505.jpg",
     * "original_img": "http://192.168.0.113/photo_editor_lab_backend/image_bucket/original/5afe5d71c4f60_category_img_1526619505.jpg",
     * "is_featured": 0
     * }
     * ]
     * }
     * }
     */
    public function getAllSubCategory(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('category_id'), $request)) != '')
                return $response;

            $this->category_id = $request->category_id;
            $this->is_active = 1;

            if (!Cache::has("pel:getAllSubCategory$this->category_id")) {
                $result = Cache::rememberforever("getAllSubCategory$this->category_id", function () {

                    $is_active = 1;
                    $total_row_result = DB::select('SELECT COUNT(*) as total FROM sub_category WHERE is_active=? and category_id = ?', [$is_active, $this->category_id]);
                    $total_row = $total_row_result[0]->total;

                    $result = DB::select('SELECT
                                        sct.id as sub_category_id,
                                        sct.category_id,
                                        sct.name as sub_category_name,
                                        IF(sct.image != "",CONCAT("' . Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",sct.image),"") as thumbnail_img,
                                        IF(sct.image != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",sct.image),"") as compressed_img,
                                        IF(sct.image != "",CONCAT("' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",sct.image),"") as original_img,
                                        sct.is_featured
                                      FROM
                                        sub_category as sct
                                      WHERE
                                        sct.category_id = ?
                                        and
                                        sct.is_active=?
                                      order by sct.updated_at DESC', [$this->category_id, $is_active]);

                    return array('total_record' => $total_row, 'category_list' => $result);
                });
            }

            $redis_result = Cache::get("getAllSubCategory$this->category_id");

            if (!$redis_result) {
                $redis_result = [];
            }


            $response = Response::json(array('code' => 200, 'message' => 'Sub categories fetched successfully.', 'cause' => '', 'data' => $redis_result));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            Log::error("getAllSubCategory : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get all sub category.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;

    }

    /**
     * @api {post} searchSubCategoryByName   searchSubCategoryByName
     * @apiName searchSubCategoryByName
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "category_id":1, //compulsory
     * "name":"ca" //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Sub category fetched successfully.",
     * "cause": "",
     * "data": {
     * "category_list": [
     * {
     * "sub_category_id": 28,
     * "name": "Sub-category",
     * "thumbnail_img": "http://192.168.0.102/ob_photolab_backend/image_bucket/thumbnail/597c6e5045aa8_category_img_1501326928.png",
     * "compressed_img": "http://192.168.0.102/ob_photolab_backend/image_bucket/compressed/597c6e5045aa8_category_img_1501326928.png",
     * "original_img": "http://192.168.0.102/ob_photolab_backend/image_bucket/original/597c6e5045aa8_category_img_1501326928.png",
     * "is_featured":1
     * }
     * ]
     * }
     * }
     */
    public function searchSubCategoryByName(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('category_id', 'name'), $request)) != '')
                return $response;

            $category_id = $request->category_id;
            $name = '%' . $request->name . '%';

            $result = DB::select('SELECT
                                    sct.id AS sub_category_id,
                                    sct.name,
                                    IF(sct.image != "",CONCAT("' . Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",sct.image),"") as thumbnail_img,
                                    IF(sct.image != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",sct.image),"") as compressed_img,
                                    IF(sct.image != "",CONCAT("' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",sct.image),"") as original_img,
                                    sct.is_featured
                                   FROM
                                      sub_category AS sct
                                    WHERE
                                      sct.category_id = ? AND
                                      sct.is_active = ? AND
                                      sct.name LIKE ? ', [$category_id, 1, $name]);

            $response = Response::json(array('code' => 200, 'message' => 'Sub category fetched successfully.', 'cause' => '', 'data' => ['category_list' => $result]));

        } catch (Exception $e) {
            Log::error("searchSubCategoryByName : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'search sub category.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    /* ========================================= Catalog Category =========================================*/

    /**
     * @api {post} addCatalog   addCatalog
     * @apiName addCatalog
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     *  Key: Authorization
     *  Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * request_data:{
     * "sub_category_id":1,
     * "is_free":1,//optional
     * "name":"Nature-2017",
     * "is_featured":1 //compulsory
     * },
     * file:image.jpeg //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "sub category added successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function addCatalog(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            if (!$request_body->has('request_data'))
                return Response::json(array('code' => 201, 'message' => 'Required field request_data is missing or empty.', 'cause' => '', 'data' => json_decode("{}")));

            $request = json_decode($request_body->input('request_data'));

            if (($response = (new VerificationController())->validateRequiredParameter(array('sub_category_id', 'name', 'is_featured'), $request)) != '')
                return $response;

            $sub_category_id = $request->sub_category_id;
            $name = $request->name;
            $is_free = isset($request->is_free) ? $request->is_free : 1;
            $is_featured = $request->is_featured;
            $create_at = date('Y-m-d H:i:s');

            if (($response = (new VerificationController())->checkIfCatalogExist($sub_category_id, $name, '')) != '')
                return $response;

            if (!$request_body->hasFile('file')) {
                return Response::json(array('code' => 201, 'message' => 'Required field file is missing or empty.', 'cause' => '', 'data' => json_decode("{}")));
            } else {
                $file_array = Input::file('file');
                if (($response = (new ImageController())->verifyImage($file_array)) != '')
                    return $response;

                $file_name = (new ImageController())->generateNewFileName('catalog_img', $file_array);
                (new ImageController())->saveOriginalImage($file_name);
                (new ImageController())->saveCompressedImage($file_name);
                (new ImageController())->saveThumbnailImage($file_name);
                $webp_file_name = (new ImageController())->saveWebpOriginalImage($file_name);
                (new ImageController())->saveWebpThumbnailImage($file_name);

                if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
                    (new ImageController())->saveImageInToS3($file_name);
                    (new ImageController())->saveWebpImageInToS3($webp_file_name);
                }
            }

            $data = array('name' => $name,
                'image' => $file_name,
                'is_free' => $is_free,
                'is_featured' => $is_featured,
                'created_at' => $create_at,
                'attribute1' => $webp_file_name
            );

            DB::beginTransaction();
            $catalog_id = DB::table('catalog_master')->insertGetId($data);
            DB::insert('INSERT INTO sub_category_catalog(sub_category_id,catalog_id,created_at) VALUES (?, ?, ?)', [$sub_category_id, $catalog_id, $create_at]);
            DB::commit();

            $response = Response::json(array('code' => 200, 'message' => 'Catalog added successfully.', 'cause' => '', 'data' => json_decode('{}')));

        } catch (Exception $e) {
            Log::error("addCatalog : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'add catalog.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /**
     * @api {post} updateCatalog   updateCatalog
     * @apiName updateCatalog
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * request_data:{
     * "catalog_id":1,
     * "name":"bg-catalog",
     * "is_free":1,
     * "is_featured":1
     * },
     * file:image.png //optional
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Catalog updated successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function updateCatalog(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            //Required parameter
            if (!$request_body->has('request_data'))
                return Response::json(array('code' => 201, 'message' => 'Required field request_data is missing or empty.', 'cause' => '', 'data' => json_decode("{}")));

            $request = json_decode($request_body->input('request_data'));

            if (($response = (new VerificationController())->validateRequiredParameter(array('catalog_id', 'name', 'is_free', 'is_featured'), $request)) != '')
                return $response;

            $catalog_id = $request->catalog_id;
            $name = trim($request->name);
            $is_free = isset($request->is_free) ? $request->is_free : '';
            $is_featured = isset($request->is_featured) ? $request->is_featured : '';

            $sub_category_id = DB::select('SELECT id from sub_category_catalog WHERE catalog_id = ?', [$catalog_id]);

            if (($response = (new VerificationController())->checkIfCatalogExist($sub_category_id[0]->id, $name, $catalog_id)) != '')
                return $response;

            if ($request_body->hasFile('file')) {
                $file_array = Input::file('file');

                if (($response = (new ImageController())->verifyImage($file_array)) != '')
                    return $response;

                $catalog_img_name = (new ImageController())->generateNewFileName('catalog_img', $file_array);
                (new ImageController())->saveOriginalImage($catalog_img_name);
                (new ImageController())->saveCompressedImage($catalog_img_name);
                (new ImageController())->saveThumbnailImage($catalog_img_name);
                $file_name = (new ImageController())->saveWebpOriginalImage($catalog_img_name);
                (new ImageController())->saveWebpThumbnailImage($catalog_img_name);

                if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
                    (new ImageController())->saveImageInToS3($catalog_img_name);
                    (new ImageController())->saveWebpImageInToS3($file_name);
                }

                $result = DB::select('SELECT image FROM catalog_master WHERE id = ?', [$catalog_id]);

                $image_name = $result[0]->image;

            } else {
                $catalog_img_name = '';
                $image_name = '';
                $file_name = NULL;
            }

            DB::beginTransaction();
            DB::update('UPDATE catalog_master SET
                                name = IF(? != "",?,name),
                                image = IF(? != "",?,image),
                                is_free = IF(? != is_free,?,is_free),
                                is_featured = IF(? != is_featured,?,is_featured),
                                attribute1 = IF(? != "",?,attribute1)
                              WHERE id = ?', [$name, $name, $catalog_img_name, $catalog_img_name, $is_free, $is_free, $is_featured, $is_featured, $file_name, $file_name, $catalog_id]);

            DB::commit();

            if ($image_name) {
                //Image Delete in image_bucket
                (new ImageController())->deleteImage($image_name);
            }
            $response = Response::json(array('code' => 200, 'message' => 'Catalog updated successfully.', 'cause' => '', 'data' => json_decode('{}')));

        } catch (Exception $e) {
            Log::error("updateCatalog : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'update catalog.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /**
     * @api {post} deleteCatalog   deleteCatalog
     * @apiName deleteCatalog
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "catalog_id":3 //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Catalog deleted successfully!.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function deleteCatalog(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('catalog_id'), $request)) != '')
                return $response;

            $catalog_id = $request->catalog_id;
            $is_active = 0;
            DB::beginTransaction();

            $result = DB::select('SELECT image FROM catalog_master WHERE id = ?', [$catalog_id]);
            $image_name = $result[0]->image;

            DB::update('UPDATE catalog_master SET is_active=?, is_featured= ? WHERE id = ? ', [$is_active, 0, $catalog_id]);
            DB::update('UPDATE sub_category_catalog SET is_active=? WHERE catalog_id = ? ', [$is_active, $catalog_id]);

            DB::delete('DELETE FROM images WHERE catalog_id = ?', [$catalog_id]);
            DB::commit();

            if ($image_name) {
                //Image Delete in image_bucket
                (new ImageController())->deleteImage($image_name);
            }

//            Log::info("catalog_id:", [$image_name]);
//            foreach ($result as $rw) {
//                //Image Delete in image_bucket
//                (new ImageController())->deleteImage($rw->image);
//            }

            $response = Response::json(array('code' => 200, 'message' => 'Catalog deleted successfully.', 'cause' => '', 'data' => json_decode('{}')));
        } catch (Exception $e) {
            Log::error("deleteCatalog : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'delete catalog.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /**
     * @api {post} getFeaturedCatalogBySubCategoryId   getFeaturedCatalogBySubCategoryId
     * @apiName getFeaturedCatalogBySubCategoryId
     * @apiGroup User
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "sub_category_id":10
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Catalog Fetched Successfully.",
     * "cause": "",
     * "data": {
     * "category_list": [
     * {
     * "catalog_id": 32,
     * "name": "Frame-2022",
     * "thumbnail_img": "http://192.168.0.102/ob_photolab_backend/image_bucket/thumbnail/598affcc7cc80_catalog_img_1502281676.jpg",
     * "compressed_img": "http://192.168.0.102/ob_photolab_backend/image_bucket/compressed/598affcc7cc80_catalog_img_1502281676.jpg",
     * "original_img": "http://192.168.0.102/ob_photolab_backend/image_bucket/original/598affcc7cc80_catalog_img_1502281676.jpg",
     * "is_free": 1,
     * "is_featured": 1
     * }
     * ]
     * }
     * }
     */
    public function getFeaturedCatalogBySubCategoryId(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('sub_category_id'), $request)) != '')
                return $response;

            $this->sub_category_id = $request->sub_category_id;
            //$item_count = Config::get('constant.PAGINATION_ITEM_LIMIT');

            if (!Cache::has("pel:getFeaturedCatalogBySubCategoryId$this->sub_category_id")) {
                $result = Cache::rememberforever("getFeaturedCatalogBySubCategoryId$this->sub_category_id", function () {
                    return DB::select('SELECT
                                        ct.id as catalog_id,
                                        ct.name,
                                        IF(ct.image != "",CONCAT("' . Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.image),"") as thumbnail_img,
                                        IF(ct.image != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.image),"") as compressed_img,
                                        IF(ct.image != "",CONCAT("' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.image),"") as original_img,
                                        ct.is_free,
                                        ct.is_featured
                                      FROM
                                        catalog_master as ct,
                                        sub_category_catalog as sct
                                      WHERE
                                        sct.sub_category_id = ? AND
                                        sct.catalog_id=ct.id AND
                                        ct.is_active=1 AND
                                        ct.is_featured=1
                                      order by ct.updated_at DESC', [$this->sub_category_id]);
                });


            }

            $redis_result = Cache::get("getFeaturedCatalogBySubCategoryId$this->sub_category_id");

            if (!$redis_result) {
                $redis_result = [];
            }

            $response = Response::json(array('code' => 200, 'message' => 'Catalogs fetched successfully.', 'cause' => '', 'data' => ['category_list' => $redis_result]));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            Log::error("getFeaturedCatalogBySubCategoryId : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get catalog.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    /**
     * @api {post} getCatalogBySubCategoryId   getCatalogBySubCategoryId
     * @apiName getCatalogBySubCategoryId
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "sub_category_id":1
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Catalog Fetched Successfully.",
     * "cause": "",
     * "data": {
     * "total_record": 5,
     * "category_name": "Independence Day Stickers",
     * "category_list": [
     * {
     * "catalog_id": 84,
     * "name": "Misc",
     * "thumbnail_img": "http://localhost/ob_photolab_backend/image_bucket/thumbnail/598d551036b09_catalog_img_1502434576.png",
     * "compressed_img": "http://localhost/ob_photolab_backend/image_bucket/compressed/598d551036b09_catalog_img_1502434576.png",
     * "original_img": "http://localhost/ob_photolab_backend/image_bucket/original/598d551036b09_catalog_img_1502434576.png",
     * "is_free": 0,
     * "is_featured": 1
     * },
     * {
     * "catalog_id": 80,
     * "name": "Circle",
     * "thumbnail_img": "http://localhost/ob_photolab_backend/image_bucket/thumbnail/598d64d7c306f_catalog_img_1502438615.png",
     * "compressed_img": "http://localhost/ob_photolab_backend/image_bucket/compressed/598d64d7c306f_catalog_img_1502438615.png",
     * "original_img": "http://localhost/ob_photolab_backend/image_bucket/original/598d64d7c306f_catalog_img_1502438615.png",
     * "is_free": 0,
     * "is_featured": 0
     * },
     * {
     * "catalog_id": 81,
     * "name": "Flag",
     * "thumbnail_img": "http://localhost/ob_photolab_backend/image_bucket/thumbnail/598d64c7af06f_catalog_img_1502438599.png",
     * "compressed_img": "http://localhost/ob_photolab_backend/image_bucket/compressed/598d64c7af06f_catalog_img_1502438599.png",
     * "original_img": "http://localhost/ob_photolab_backend/image_bucket/original/598d64c7af06f_catalog_img_1502438599.png",
     * "is_free": 0,
     * "is_featured": 0
     * },
     * {
     * "catalog_id": 82,
     * "name": "Map",
     * "thumbnail_img": "http://localhost/ob_photolab_backend/image_bucket/thumbnail/598d64afc90f8_catalog_img_1502438575.png",
     * "compressed_img": "http://localhost/ob_photolab_backend/image_bucket/compressed/598d64afc90f8_catalog_img_1502438575.png",
     * "original_img": "http://localhost/ob_photolab_backend/image_bucket/original/598d64afc90f8_catalog_img_1502438575.png",
     * "is_free": 0,
     * "is_featured": 0
     * },
     * {
     * "catalog_id": 83,
     * "name": "Text",
     * "thumbnail_img": "http://localhost/ob_photolab_backend/image_bucket/thumbnail/598d649f4442e_catalog_img_1502438559.png",
     * "compressed_img": "http://localhost/ob_photolab_backend/image_bucket/compressed/598d649f4442e_catalog_img_1502438559.png",
     * "original_img": "http://localhost/ob_photolab_backend/image_bucket/original/598d649f4442e_catalog_img_1502438559.png",
     * "is_free": 0,
     * "is_featured": 0
     * }
     * ]
     * }
     * }
     */
    /**
     * @api {post} getCatalogBySubCategoryId   getCatalogBySubCategoryId
     * @apiName getCatalogBySubCategoryId
     * @apiGroup User
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "sub_category_id":1
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Catalog Fetched Successfully.",
     * "cause": "",
     * "data": {
     * "total_record": 5,
     * "category_name": "Independence Day Stickers",
     * "category_list": [
     * {
     * "catalog_id": 84,
     * "name": "Misc",
     * "thumbnail_img": "http://localhost/ob_photolab_backend/image_bucket/thumbnail/598d551036b09_catalog_img_1502434576.png",
     * "compressed_img": "http://localhost/ob_photolab_backend/image_bucket/compressed/598d551036b09_catalog_img_1502434576.png",
     * "original_img": "http://localhost/ob_photolab_backend/image_bucket/original/598d551036b09_catalog_img_1502434576.png",
     * "is_free": 0,
     * "is_featured": 1
     * },
     * {
     * "catalog_id": 80,
     * "name": "Circle",
     * "thumbnail_img": "http://localhost/ob_photolab_backend/image_bucket/thumbnail/598d64d7c306f_catalog_img_1502438615.png",
     * "compressed_img": "http://localhost/ob_photolab_backend/image_bucket/compressed/598d64d7c306f_catalog_img_1502438615.png",
     * "original_img": "http://localhost/ob_photolab_backend/image_bucket/original/598d64d7c306f_catalog_img_1502438615.png",
     * "is_free": 0,
     * "is_featured": 0
     * },
     * {
     * "catalog_id": 81,
     * "name": "Flag",
     * "thumbnail_img": "http://localhost/ob_photolab_backend/image_bucket/thumbnail/598d64c7af06f_catalog_img_1502438599.png",
     * "compressed_img": "http://localhost/ob_photolab_backend/image_bucket/compressed/598d64c7af06f_catalog_img_1502438599.png",
     * "original_img": "http://localhost/ob_photolab_backend/image_bucket/original/598d64c7af06f_catalog_img_1502438599.png",
     * "is_free": 0,
     * "is_featured": 0
     * },
     * {
     * "catalog_id": 82,
     * "name": "Map",
     * "thumbnail_img": "http://localhost/ob_photolab_backend/image_bucket/thumbnail/598d64afc90f8_catalog_img_1502438575.png",
     * "compressed_img": "http://localhost/ob_photolab_backend/image_bucket/compressed/598d64afc90f8_catalog_img_1502438575.png",
     * "original_img": "http://localhost/ob_photolab_backend/image_bucket/original/598d64afc90f8_catalog_img_1502438575.png",
     * "is_free": 0,
     * "is_featured": 0
     * },
     * {
     * "catalog_id": 83,
     * "name": "Text",
     * "thumbnail_img": "http://localhost/ob_photolab_backend/image_bucket/thumbnail/598d649f4442e_catalog_img_1502438559.png",
     * "compressed_img": "http://localhost/ob_photolab_backend/image_bucket/compressed/598d649f4442e_catalog_img_1502438559.png",
     * "original_img": "http://localhost/ob_photolab_backend/image_bucket/original/598d649f4442e_catalog_img_1502438559.png",
     * "is_free": 0,
     * "is_featured": 0
     * }
     * ]
     * }
     * }
     */
    public function getCatalogBySubCategoryId(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('sub_category_id'), $request)) != '')
                return $response;
            //Log::info('getCatalogBySubCategoryId request : ',['request' => $request]);

            $this->sub_category_id = $request->sub_category_id;

            if (!Cache::has("pel:getCatalogBySubCategoryId$this->sub_category_id")) {
                $result = Cache::rememberforever("getCatalogBySubCategoryId$this->sub_category_id", function () {

                    //sub Category Name
                    $name = DB::select('SELECT sc.name FROM  sub_category as sc WHERE sc.id = ? AND sc.is_active = ?', [$this->sub_category_id, 1]);
                    $category_name = $name[0]->name;

                    $total_row_result = DB::select('SELECT COUNT(*) as total FROM  sub_category_catalog WHERE sub_category_id = ? AND is_active = ?', [$this->sub_category_id, 1]);
                    $total_row = $total_row_result[0]->total;

                    $result = DB::select('SELECT
                                        ct.id as catalog_id,
                                        ct.name,
                                        IF(ct.image != "",CONCAT("' . Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.image),"") as thumbnail_img,
                                        IF(ct.image != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.image),"") as compressed_img,
                                        IF(ct.image != "",CONCAT("' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.image),"") as original_img,
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

                    return array('total_record' => $total_row, 'category_name' => $category_name, 'category_list' => $result);
                });
            }

            $redis_result = Cache::get("getCatalogBySubCategoryId$this->sub_category_id");

            if (!$redis_result) {
                $redis_result = [];
            }

            $response = Response::json(array('code' => 200, 'message' => 'Catalogs fetched successfully.', 'cause' => '', 'data' => $redis_result));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            Log::error("getCatalogBySubCategoryId : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get catalogs.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    /**
     * @api {post} getBackgroundCatalogBySubCategoryId   getBackgroundCatalogBySubCategoryId
     * @apiName getBackgroundCatalogBySubCategoryId
     * @apiGroup User
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "sub_category_id":1
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Catalog Fetched Successfully.",
     * "cause": "",
     * "data": {
     * "total_record": 5,
     * "category_name": "Independence Day Stickers",
     * "category_list": [
     * {
     * "catalog_id": 80,
     * "name": "Circle",
     * "thumbnail_img": "http://localhost/ob_photolab_backend/image_bucket/thumbnail/598d64d7c306f_catalog_img_1502438615.png",
     * "compressed_img": "http://localhost/ob_photolab_backend/image_bucket/compressed/598d64d7c306f_catalog_img_1502438615.png",
     * "original_img": "http://localhost/ob_photolab_backend/image_bucket/original/598d64d7c306f_catalog_img_1502438615.png",
     * "is_free": 0,
     * "is_featured": 0
     * },
     * {
     * "catalog_id": 81,
     * "name": "Flag",
     * "thumbnail_img": "http://localhost/ob_photolab_backend/image_bucket/thumbnail/598d64c7af06f_catalog_img_1502438599.png",
     * "compressed_img": "http://localhost/ob_photolab_backend/image_bucket/compressed/598d64c7af06f_catalog_img_1502438599.png",
     * "original_img": "http://localhost/ob_photolab_backend/image_bucket/original/598d64c7af06f_catalog_img_1502438599.png",
     * "is_free": 0,
     * "is_featured": 0
     * },
     * {
     * "catalog_id": 82,
     * "name": "Map",
     * "thumbnail_img": "http://localhost/ob_photolab_backend/image_bucket/thumbnail/598d64afc90f8_catalog_img_1502438575.png",
     * "compressed_img": "http://localhost/ob_photolab_backend/image_bucket/compressed/598d64afc90f8_catalog_img_1502438575.png",
     * "original_img": "http://localhost/ob_photolab_backend/image_bucket/original/598d64afc90f8_catalog_img_1502438575.png",
     * "is_free": 0,
     * "is_featured": 0
     * },
     * {
     * "catalog_id": 83,
     * "name": "Text",
     * "thumbnail_img": "http://localhost/ob_photolab_backend/image_bucket/thumbnail/598d649f4442e_catalog_img_1502438559.png",
     * "compressed_img": "http://localhost/ob_photolab_backend/image_bucket/compressed/598d649f4442e_catalog_img_1502438559.png",
     * "original_img": "http://localhost/ob_photolab_backend/image_bucket/original/598d649f4442e_catalog_img_1502438559.png",
     * "is_free": 0,
     * "is_featured": 0
     * }
     * ]
     * }
     * }
     */
    public function getBackgroundCatalogBySubCategoryId(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('sub_category_id'), $request)) != '')
                return $response;

            $this->sub_category_id = $request->sub_category_id;

            if (!Cache::has("pel:getBackgroundCatalogBySubCategoryId$this->sub_category_id")) {
                $result = Cache::rememberforever("getBackgroundCatalogBySubCategoryId$this->sub_category_id", function () {

                    //sub Category Name
                    $name = DB::select('SELECT sc.name FROM  sub_category as sc WHERE id = ? AND is_active = ?', [$this->sub_category_id, 1]);
                    $category_name = $name[0]->name;

                    $total_row_result = DB::select('SELECT COUNT(*) as total FROM  sub_category_catalog WHERE sub_category_id = ? AND is_active = ?', [$this->sub_category_id, 1]);
                    $total_row = $total_row_result[0]->total;

                    $result = DB::select('SELECT
                                        ct.id as catalog_id,
                                        ct.name,
                                        IF(ct.image != "",CONCAT("' . Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.image),"") as thumbnail_img,
                                        IF(ct.image != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.image),"") as compressed_img,
                                        IF(ct.image != "",CONCAT("' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.image),"") as original_img,
                                        ct.is_free,
                                        ct.is_featured
                                      FROM
                                        catalog_master as ct,
                                        sub_category_catalog as sct
                                      WHERE
                                        sct.sub_category_id = ? AND
                                        sct.catalog_id = ct.id AND
                                        sct.is_active = 1 AND
                                        ct.is_featured = 0
                                      order by ct.updated_at DESC', [$this->sub_category_id]);

                    return array('total_record' => $total_row, 'category_name' => $category_name, 'category_list' => $result);
                });
            }

            $redis_result = Cache::get("getBackgroundCatalogBySubCategoryId$this->sub_category_id");

            if (!$redis_result) {
                $redis_result = [];
            }


            $response = Response::json(array('code' => 200, 'message' => 'Catalogs fetched successfully.', 'cause' => '', 'data' => $redis_result));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            Log::error("getBackgroundCatalogBySubCategoryId : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get catalogs.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    /**
     * @api {post} searchCatalogByName   searchCatalogByName
     * @apiName searchCatalogByName
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
     * "name":"black"
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Catalog Search Successfully.",
     * "cause": "",
     * "data": {
     * "category_list": [
     * {
     * "catalog_id": 26,
     * "name": "Black",
     * "thumbnail_img": "http://192.168.0.102/ob_photolab_backend/image_bucket/thumbnail/597ec7b66b2cb_catalog_img_1501480886.png",
     * "compressed_img": "http://192.168.0.102/ob_photolab_backend/image_bucket/compressed/597ec7b66b2cb_catalog_img_1501480886.png",
     * "original_img": "http://192.168.0.102/ob_photolab_backend/image_bucket/original/597ec7b66b2cb_catalog_img_1501480886.png",
     * is_free = 0
     * }
     * ]
     * }
     * }
     */
    public function searchCatalogByName(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('sub_category_id', 'name'), $request)) != '')
                return $response;

            $sub_category_id = $request->sub_category_id;
            $name = '%' . $request->name . '%';

            $result = DB::select('SELECT
                                    cm.id AS catalog_id,
                                    cm.name,
                                    IF(cm.image != "",CONCAT("' . Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",cm.image),"") as thumbnail_img,
                                    IF(cm.image != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",cm.image),"") as compressed_img,
                                    IF(cm.image != "",CONCAT("' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",cm.image),"") as original_img,
                                    cm.is_free,
                                    cm.is_featured
                                   FROM
                                      catalog_master AS cm,
                                      sub_category_catalog as sct
                                    WHERE
                                      sct.sub_category_id = ? AND
                                      sct.catalog_id=cm.id AND
                                      sct.is_active=1 AND
                                      cm.name LIKE ? ', [$sub_category_id, $name]);

            $response = Response::json(array('code' => 200, 'message' => 'Catalog search successfully.', 'cause' => '', 'data' => ['category_list' => $result]));

        } catch (Exception $e) {
            Log::error("searchCatalogByName : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'search catalog.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    /* =========================================Catalog Images =========================================*/

    /**
     * @api {post} addCatalogImages   addCatalogImages
     * @apiName addCatalogImages
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * request_data:{"catalog_id":1}
     * file[]:image.jpeg
     * file[]:image12.jpeg
     * file[]:image.png
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "sub category images added successfully!.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function addCatalogImages(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            if (!$request_body->has('request_data'))
                return Response::json(array('code' => 201, 'message' => 'Required field request_data is missing or empty.', 'cause' => '', 'data' => json_decode("{}")));

            $request = json_decode($request_body->input('request_data'));

            if (($response = (new VerificationController())->validateRequiredParameter(array('catalog_id'), $request)) != '')
                return $response;

            $catalog_id = $request->catalog_id;
            $create_at = date('Y-m-d H:i:s');
            DB::beginTransaction();
            if (!$request_body->hasFile('file')) {
                return Response::json(array('code' => 201, 'message' => 'Required field file is missing or empty.', 'cause' => '', 'data' => json_decode("{}")));
            } else {

                $images_array = Input::file('file');
                foreach ($images_array as $image_array) {
                    if (($response = (new ImageController())->verifyImage($image_array)) != '')
                        return $response;

                    $tag_list = (new TagDetectController())->getTagInImageByBytes($image_array);
                    if (($tag_list == "" or $tag_list == NULL) and Config::get('constant.CLARIFAI_API_KEY') != "") {
                        return Response::json(array('code' => 201, 'message' => 'Tag not detected from clarifai.com.', 'cause' => '', 'data' => json_decode("{}")));
                    }

                    $normal_image = (new ImageController())->generateNewFileName('normal_image', $image_array);
                    (new ImageController())->saveOriginalImageFromArray($image_array, $normal_image);
                    (new ImageController())->saveCompressedImage($normal_image);
                    (new ImageController())->saveThumbnailImage($normal_image);

                    if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
                        (new ImageController())->saveImageInToS3($normal_image);
                    }

                    DB::insert('INSERT
                                INTO
                                  images(catalog_id, image, search_category, created_at)
                                VALUES(?, ?, ?, ?) ', [$catalog_id, $normal_image, $tag_list, $create_at]);
                }

                DB::commit();
            }

            $response = Response::json(array('code' => 200, 'message' => 'Normal images added successfully.', 'cause' => '', 'data' => json_decode('{}')));
        } catch (Exception $e) {
            Log::error("addCatalogImages : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'add normal images.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /**
     * @api {post} addFeaturedBackgroundCatalogImage   addFeaturedBackgroundCatalogImage
     * @apiName addFeaturedBackgroundCatalogImage
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * request_data:{"catalog_id":1,
     * "image_type":1},
     * original_img:image1.jpeg,
     * display_img:image12.jpeg
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Featured Background Images added successfully!.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function addFeaturedBackgroundCatalogImage(Request $request_body)
    {

        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->input('request_data'));
            if (($response = (new VerificationController())->validateRequiredParameter(array('catalog_id', 'image_type'), $request)) != '')
                return $response;

            $created_at = date('Y-m-d H:i:s');
            $catalog_id = $request->catalog_id;
            $image_type = $request->image_type;

            if (!$request_body->hasFile('original_img') and !$request_body->hasFile('display_img')) {
                return Response::json(array('code' => 201, 'message' => 'Required field original_img or display_img is missing or empty.', 'cause' => '', 'data' => json_decode("{}")));
            } elseif (!$request_body->hasFile('original_img')) {

                return Response::json(array('code' => 201, 'message' => 'Required field original_img is missing or empty.', 'cause' => '', 'data' => json_decode("{}")));

            } elseif (!$request_body->hasFile('display_img')) {
                return Response::json(array('code' => 201, 'message' => 'Required field display_img is missing or empty.', 'cause' => '', 'data' => json_decode("{}")));

            } else {
                if ($request_body->hasFile('original_img')) {
                    $image_array = Input::file('original_img');
                    if (($response = (new ImageController())->verifyImage($image_array)) != '')
                        return $response;

                    $original_img = (new ImageController())->generateNewFileName('original_img', $image_array);
                    $file_name = 'original_img';
                    (new ImageController())->saveMultipleOriginalImage($original_img, $file_name);
                    (new ImageController())->saveMultipleCompressedImage($original_img, $file_name);
                    (new ImageController())->saveMultipleThumbnailImage($original_img, $file_name);

                    if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
                        (new ImageController())->saveImageInToS3($original_img);
                    }

                }
                if ($request_body->hasFile('display_img')) {

                    $image_array = Input::file('display_img');
                    if (($response = (new ImageController())->verifyImage($image_array)) != '')
                        return $response;

                    $display_img = (new ImageController())->generateNewFileName('display_img', $image_array);

                    $file_name = 'display_img';
                    (new ImageController())->saveMultipleOriginalImage($display_img, $file_name);
                    (new ImageController())->saveMultipleCompressedImage($display_img, $file_name);
                    (new ImageController())->saveMultipleThumbnailImage($display_img, $file_name);

                    if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
                        (new ImageController())->saveImageInToS3($display_img);
                    }

                }

                DB::beginTransaction();

                $data = array(
                    'catalog_id' => $catalog_id,
                    'original_img' => $original_img,
                    'display_img' => $display_img,
                    'image_type' => $image_type,
                    'created_at' => $created_at
                );

                $sample_image_id = DB::table('images')->insertGetId($data);

                //DB::update('update images set original_img = ?,display_img = ?,image_type = ?,created_at = ? where catalog_id = ?',[$original_img, $display_img, $image_type, $created_at, $catalog_id]);
                //log::info('Inserted featured background image');

                DB::commit();

                $response = Response::json(array('code' => 200, 'message' => 'Featured background images added successfully.', 'cause' => '', 'data' => json_decode('{}')));

            }

        } catch (Exception $e) {
            Log::error("addFeaturedBackgroundCatalogImage : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'add featured background images.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /**
     * @api {post} updateFeaturedBackgroundCatalogImage   updateFeaturedBackgroundCatalogImage
     * @apiName updateFeaturedBackgroundCatalogImage
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * request_data:{"img_id":1,
     * "image_type":1},
     * original_img:image1.jpeg,
     * display_img:image12.jpeg
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Featured Background Images updated successfully!.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function updateFeaturedBackgroundCatalogImage(Request $request_body)
    {

        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->input('request_data'));
            if (($response = (new VerificationController())->validateRequiredParameter(array('img_id', 'image_type'), $request)) != '')
                return $response;

            $created_at = date('Y-m-d H:i:s');
            $img_id = $request->img_id;
            $image_type = $request->image_type;
            if ($request_body->hasFile('original_img')) {
                $image_array = Input::file('original_img');
                if (($response = (new ImageController())->verifyImage($image_array)) != '')
                    return $response;

                $original_img = (new ImageController())->generateNewFileName('original_img', $image_array);
                $file_name = 'original_img';
                (new ImageController())->saveMultipleOriginalImage($original_img, $file_name);
                (new ImageController())->saveMultipleCompressedImage($original_img, $file_name);
                (new ImageController())->saveMultipleThumbnailImage($original_img, $file_name);

                if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
                    (new ImageController())->saveImageInToS3($original_img);
                }

                DB::beginTransaction();
                DB::update('UPDATE images SET original_img = ?,image_type = ?,created_at = ? WHERE id = ?', [$original_img, $image_type, $created_at, $img_id]);


            } else {
                $original_img = '';
            }
            if ($request_body->hasFile('display_img')) {

                $image_array = Input::file('display_img');
                if (($response = (new ImageController())->verifyImage($image_array)) != '')
                    return $response;

                $display_img = (new ImageController())->generateNewFileName('display_img', $image_array);

                $file_name = 'display_img';
                (new ImageController())->saveMultipleOriginalImage($display_img, $file_name);
                (new ImageController())->saveMultipleCompressedImage($display_img, $file_name);
                (new ImageController())->saveMultipleThumbnailImage($display_img, $file_name);

                if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
                    (new ImageController())->saveImageInToS3($display_img);
                }

                DB::update('UPDATE images SET display_img = ?,image_type = ?,created_at = ? WHERE id = ?', [$display_img, $image_type, $created_at, $img_id]);

            } else {
                $display_img = '';
            }

            if ($original_img == '' && $display_img == '') {
                DB::update('UPDATE images SET image_type = ?,created_at = ? WHERE id = ?', [$image_type, $created_at, $img_id]);
            }

            DB::commit();

            $response = Response::json(array('code' => 200, 'message' => 'Image updated successfully.', 'cause' => '', 'data' => json_decode('{}')));
        } catch (Exception $e) {
            Log::error("updateFeaturedBackgroundCatalogImage : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'update image.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /**
     * @api {post} getSampleImagesForAdmin   getSampleImagesForAdmin
     * @apiName getSampleImagesForAdmin
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "catalog_id":13
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Catalog Images Fetched Successfully.",
     * "cause": "",
     * "data": {
     * "image_list": [
     * {
     * "img_id": 220,
     * "original_thumbnail_img": "http://192.168.0.102/ob_photolab_backend/image_bucket/thumbnail/598c33bf5cd88_original_img_1502360511.png",
     * "original_compressed_img": "http://192.168.0.102/ob_photolab_backend/image_bucket/compressed/598c33bf5cd88_original_img_1502360511.png",
     * "original_original_img": "http://192.168.0.102/ob_photolab_backend/image_bucket/original/598c33bf5cd88_original_img_1502360511.png",
     * "display_thumbnail_img": "http://192.168.0.102/ob_photolab_backend/image_bucket/thumbnail/598c33c010ed8_display_img_1502360512.png",
     * "display_compressed_img": "http://192.168.0.102/ob_photolab_backend/image_bucket/compressed/598c33c010ed8_display_img_1502360512.png",
     * "display_original_img": "http://192.168.0.102/ob_photolab_backend/image_bucket/original/598c33c010ed8_display_img_1502360512.png",
     * "image_type": 1
     * },
     * {
     * "img_id": 219,
     * "original_thumbnail_img": "http://192.168.0.102/ob_photolab_backend/image_bucket/thumbnail/598c3141d844a_original_img_1502359873.png",
     * "original_compressed_img": "http://192.168.0.102/ob_photolab_backend/image_bucket/compressed/598c3141d844a_original_img_1502359873.png",
     * "original_original_img": "http://192.168.0.102/ob_photolab_backend/image_bucket/original/598c3141d844a_original_img_1502359873.png",
     * "display_thumbnail_img": "http://192.168.0.102/ob_photolab_backend/image_bucket/thumbnail/598c314294e53_display_img_1502359874.png",
     * "display_compressed_img": "http://192.168.0.102/ob_photolab_backend/image_bucket/compressed/598c314294e53_display_img_1502359874.png",
     * "display_original_img": "http://192.168.0.102/ob_photolab_backend/image_bucket/original/598c314294e53_display_img_1502359874.png",
     * "image_type": 1
     * },
     * {
     * "img_id": 216,
     * "original_thumbnail_img": "http://192.168.0.102/ob_photolab_backend/image_bucket/thumbnail/598bfa4e07757_original_img_1502345806.jpg",
     * "original_compressed_img": "http://192.168.0.102/ob_photolab_backend/image_bucket/compressed/598bfa4e07757_original_img_1502345806.jpg",
     * "original_original_img": "http://192.168.0.102/ob_photolab_backend/image_bucket/original/598bfa4e07757_original_img_1502345806.jpg",
     * "display_thumbnail_img": "http://192.168.0.102/ob_photolab_backend/image_bucket/thumbnail/598bfa4e39443_display_img_1502345806.jpg",
     * "display_compressed_img": "http://192.168.0.102/ob_photolab_backend/image_bucket/compressed/598bfa4e39443_display_img_1502345806.jpg",
     * "display_original_img": "http://192.168.0.102/ob_photolab_backend/image_bucket/original/598bfa4e39443_display_img_1502345806.jpg",
     * "image_type": 1
     * }
     * ]
     * }
     * }
     */
    public function getSampleImagesForAdmin(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            //Log::info("getSampleImagesForAdmin Request :", [$request]);

            if (($response = (new VerificationController())->validateRequiredParameter(array('catalog_id'), $request)) != '')
                return $response;


            $this->catalog_id = $request->catalog_id;

            if (!Cache::has("pel:getSampleImagesForAdmin$this->catalog_id")) {
                $result = Cache::rememberforever("getSampleImagesForAdmin$this->catalog_id", function () {
                    return DB::select('SELECT
                                          im.id as img_id,
                                          IF(im.original_img != "",CONCAT("' . Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",im.original_img),"") as original_thumbnail_img,
                                          IF(im.original_img != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",im.original_img),"") as original_compressed_img,
                                          IF(im.original_img != "",CONCAT("' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",im.original_img),"") as original_original_img,
                                          IF(im.display_img != "",CONCAT("' . Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",im.display_img),"") as display_thumbnail_img,
                                          IF(im.display_img != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",im.display_img),"") as display_compressed_img,
                                          IF(im.display_img != "",CONCAT("' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",im.display_img),"") as display_original_img,
                                          image_type
                                        FROM
                                          catalog_master as cm JOIN images as im ON
                                          cm.id = im.catalog_id AND
                                          im.is_active = 1 AND
                                          im.catalog_id = ? AND
                                          isnull(im.image) AND
                                          cm.is_featured=1
                                        ORDER BY im.updated_at DESC', [$this->catalog_id]);
                });
            }
            $redis_result = Cache::get("getSampleImagesForAdmin$this->catalog_id");

            if (!$redis_result) {
                $redis_result = [];
            }


            $response = Response::json(array('code' => 200, 'message' => 'Sample images fetched successfully.', 'cause' => '', 'data' => ['image_list' => $redis_result]));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            Log::error("getSampleImagesForAdmin : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get sample images.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    /**
     * @api {post} getSampleImagesForMobile   getSampleImagesForMobile
     * @apiName getSampleImagesForMobile
     * @apiGroup User
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "sub_category_id":13
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Catalog Images Fetched Successfully.",
     * "cause": "",
     * "data": {
     * "image_list": [
     * {
     * "img_id": 220,
     * "original_thumbnail_img": "http://192.168.0.102/ob_photolab_backend/image_bucket/thumbnail/598c33bf5cd88_original_img_1502360511.png",
     * "original_compressed_img": "http://192.168.0.102/ob_photolab_backend/image_bucket/compressed/598c33bf5cd88_original_img_1502360511.png",
     * "original_original_img": "http://192.168.0.102/ob_photolab_backend/image_bucket/original/598c33bf5cd88_original_img_1502360511.png",
     * "display_thumbnail_img": "http://192.168.0.102/ob_photolab_backend/image_bucket/thumbnail/598c33c010ed8_display_img_1502360512.png",
     * "display_compressed_img": "http://192.168.0.102/ob_photolab_backend/image_bucket/compressed/598c33c010ed8_display_img_1502360512.png",
     * "display_original_img": "http://192.168.0.102/ob_photolab_backend/image_bucket/original/598c33c010ed8_display_img_1502360512.png",
     * "image_type": 1 // 1 = Background , 2 = Frame
     * },
     * {
     * "img_id": 219,
     * "original_thumbnail_img": "http://192.168.0.102/ob_photolab_backend/image_bucket/thumbnail/598c3141d844a_original_img_1502359873.png",
     * "original_compressed_img": "http://192.168.0.102/ob_photolab_backend/image_bucket/compressed/598c3141d844a_original_img_1502359873.png",
     * "original_original_img": "http://192.168.0.102/ob_photolab_backend/image_bucket/original/598c3141d844a_original_img_1502359873.png",
     * "display_thumbnail_img": "http://192.168.0.102/ob_photolab_backend/image_bucket/thumbnail/598c314294e53_display_img_1502359874.png",
     * "display_compressed_img": "http://192.168.0.102/ob_photolab_backend/image_bucket/compressed/598c314294e53_display_img_1502359874.png",
     * "display_original_img": "http://192.168.0.102/ob_photolab_backend/image_bucket/original/598c314294e53_display_img_1502359874.png",
     * "image_type": 1 // 1 = Background , 2 = Frame
     * },
     * {
     * "img_id": 216,
     * "original_thumbnail_img": "http://192.168.0.102/ob_photolab_backend/image_bucket/thumbnail/598bfa4e07757_original_img_1502345806.jpg",
     * "original_compressed_img": "http://192.168.0.102/ob_photolab_backend/image_bucket/compressed/598bfa4e07757_original_img_1502345806.jpg",
     * "original_original_img": "http://192.168.0.102/ob_photolab_backend/image_bucket/original/598bfa4e07757_original_img_1502345806.jpg",
     * "display_thumbnail_img": "http://192.168.0.102/ob_photolab_backend/image_bucket/thumbnail/598bfa4e39443_display_img_1502345806.jpg",
     * "display_compressed_img": "http://192.168.0.102/ob_photolab_backend/image_bucket/compressed/598bfa4e39443_display_img_1502345806.jpg",
     * "display_original_img": "http://192.168.0.102/ob_photolab_backend/image_bucket/original/598bfa4e39443_display_img_1502345806.jpg",
     * "image_type": 1 // 1 = Background , 2 = Frame
     * }
     * ]
     * }
     * }
     */
    public function getSampleImagesForMobile(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            //Log::info("getSampleImagesForMobile Request :", [$request]);

            if (($response = (new VerificationController())->validateRequiredParameter(array('sub_category_id'), $request)) != '')
                return $response;

            $this->sub_category_id = $request->sub_category_id;


            if (!Cache::has("pel:getSampleImagesForMobile$this->sub_category_id")) {
                $result = Cache::rememberforever("getSampleImagesForMobile$this->sub_category_id", function () {
                    return DB::select('SELECT
                                          im.id as img_id,
                                          IF(im.original_img != "",CONCAT("' . Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",im.original_img),"") as original_thumbnail_img,
                                          IF(im.original_img != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",im.original_img),"") as original_compressed_img,
                                          IF(im.original_img != "",CONCAT("' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",im.original_img),"") as original_original_img,
                                          IF(im.display_img != "",CONCAT("' . Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",im.display_img),"") as display_thumbnail_img,
                                          IF(im.display_img != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",im.display_img),"") as display_compressed_img,
                                          IF(im.display_img != "",CONCAT("' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",im.display_img),"") as display_original_img,
                                          image_type
                                        FROM
                                          images as im
                                        where
                                          im.is_active = 1 AND
                                          isnull(im.image) AND
                                          im.catalog_id in(select
                                                             DISTINCT catalog_id
                                                           from
                                                             sub_category_catalog as scc,
                                                             catalog_master as cm
                                                           where
                                                             cm.id=scc.catalog_id AND
                                                             cm.is_featured=1 AND
                                                             scc.sub_category_id = ?)
                                        order by im.updated_at DESC', [$this->sub_category_id]);
                });
            }
            $redis_result = Cache::get("getSampleImagesForMobile$this->sub_category_id");

            if (!$redis_result) {
                $redis_result = [];
            }


            $response = Response::json(array('code' => 200, 'message' => 'Sample images fetched successfully.', 'cause' => '', 'data' => ['image_list' => $redis_result]));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            Log::error("getSampleImagesForMobile : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get sample images.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    /**
     * @api {post} updateCatalogImage   updateCatalogImage
     * @apiName updateSubCategoryImage
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * request_data:{
     * "img_id":1, //compulsory
     * "search_category":"test,abc" //optional
     * }
     * file:"" //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Normal image updated successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function updateCatalogImage(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            //Required parameter
            if (!$request_body->has('request_data'))
                return Response::json(array('code' => 201, 'message' => 'Required field request_data is missing or empty.', 'cause' => '', 'data' => json_decode("{}")));

            $request = json_decode($request_body->input('request_data'));

            if (($response = (new VerificationController())->validateRequiredParameter(array('img_id'), $request)) != '')
                return $response;

            $img_id = $request->img_id;
            $search_category = isset($request->search_category) ? $request->search_category : '';

            if ($search_category != NULL or $search_category != "") {
                if (($response = (new VerificationController())->verifySearchCategory($search_category)) != '')
                    return $response;
            }

            if ($request_body->hasFile('file')) {
                /*return Response::json(array('code' => 201, 'message' => 'Required field file is missing or empty.', 'cause' => '', 'data' => json_decode("{}")));
            } else {*/
                $image_array = Input::file('file');

                if (($response = (new ImageController())->verifyImage($image_array)) != '')
                    return $response;

                $tag_list = (new TagDetectController())->getTagInImageByBytes($image_array);
                if (($tag_list == "" or $tag_list == NULL) and Config::get('constant.CLARIFAI_API_KEY') != "") {
                    return Response::json(array('code' => 201, 'message' => 'Tag not detected from clarifai.com.', 'cause' => '', 'data' => json_decode("{}")));
                }

                $catalog_img = (new ImageController())->generateNewFileName('catalog_img', $image_array);
                (new ImageController())->saveOriginalImage($catalog_img);
                (new ImageController())->saveCompressedImage($catalog_img);
                (new ImageController())->saveThumbnailImage($catalog_img);

                if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
                    (new ImageController())->saveImageInToS3($catalog_img);
                }

                $result = DB::select('SELECT image FROM images WHERE id = ?', [$img_id]);
                $image_name = $result[0]->image;


                if ($image_name) {
                    //Image Delete in image_bucket
                    (new ImageController())->deleteImage($image_name);
                }
            } else {
                $catalog_img = "";
                $tag_list = $search_category;
            }

            DB::beginTransaction();
            DB::update('UPDATE
                              images
                            SET
                              image = IF(? != "",?,image),
                              search_category = ?
                            WHERE
                              id = ? ',
                [$catalog_img, $catalog_img, $tag_list, $img_id]);
            DB::commit();


            $response = Response::json(array('code' => 200, 'message' => 'Normal image updated successfully.', 'cause' => '', 'data' => json_decode('{}')));

        } catch (Exception $e) {
            Log::error("updateCatalogImage : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'update normal image.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /**
     * @api {post} deleteCatalogImage   deleteCatalogImage
     * @apiName deleteCatalogImage
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "img_id":1 //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Normal image deleted successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function deleteCatalogImage(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('img_id'), $request)) != '')
                return $response;

            $img_id = $request->img_id;

            $result = DB::select('SELECT image FROM images WHERE id = ?', [$img_id]);


            DB::beginTransaction();

            DB::delete('DELETE FROM images WHERE id = ? ', [$img_id]);

            DB::commit();

            if (count($result) > 0) {
                $image_name = $result[0]->image;
                //Image Delete in image_bucket
                (new ImageController())->deleteImage($image_name);

            }

            $response = Response::json(array('code' => 200, 'message' => 'Normal image deleted successfully.', 'cause' => '', 'data' => json_decode('{}')));
        } catch (Exception $e) {
            Log::error("deleteCatalogImage : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'delete normal image.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /**
     * @api {post} getDataByCatalogIdForAdmin   getDataByCatalogIdForAdmin
     * @apiName getDataByCatalogIdForAdmin
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     *  "catalog_id":1 //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Data fetched successfully.",
     * "cause": "",
     * "data": {
     * "image_list": [
     * {
     * "img_id": 182,
     * "thumbnail_img": "http://192.168.0.113/photo_editor_lab_backend/image_bucket/thumbnail/598d5644e1424_catalog_image_1502434884.png",
     * "compressed_img": "http://192.168.0.113/photo_editor_lab_backend/image_bucket/compressed/598d5644e1424_catalog_image_1502434884.png",
     * "original_img": "http://192.168.0.113/photo_editor_lab_backend/image_bucket/original/598d5644e1424_catalog_image_1502434884.png",
     * "is_json_data": 0,
     * "json_data": "",
     * "is_featured": "",
     * "is_free": 0,
     * "is_portrait": 0,
     * "search_category": ""
     * }
     * ]
     * }
     * }
     */
    public function getDataByCatalogIdForAdmin(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());

            if (($response = (new VerificationController())->validateRequiredParameter(array('catalog_id'), $request)) != '')
                return $response;

            $this->catalog_id = $request->catalog_id;

            if (!Cache::has("pel:getDataByCatalogIdForAdmin$this->catalog_id")) {
                $result = Cache::rememberforever("getDataByCatalogIdForAdmin$this->catalog_id", function () {

                    $result = DB::select('SELECT
                                              im.id as img_id,
                                              IF(im.image != "",CONCAT("' . Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",im.image),"") as thumbnail_img,
                                              IF(im.image != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",im.image),"") as compressed_img,
                                              IF(im.image != "",CONCAT("' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",im.image),"") as original_img,
                                              IF(im.json_data IS NOT NULL,1,0) as is_json_data,
                                              coalesce(im.json_data,"") as json_data,
                                              coalesce(im.is_featured,"") as is_featured,
                                              coalesce(im.is_free,0) as is_free,
                                              coalesce(im.is_portrait,0) as is_portrait,
                                              coalesce(im.search_category,"") as search_category
                                            FROM
                                              images as im
                                            where
                                              im.is_active = 1 AND
                                              im.catalog_id = ? AND
                                              isnull(im.original_img) AND
                                              isnull(im.display_img)
                                            order by im.updated_at DESC', [$this->catalog_id]);

                    foreach ($result as $key) {
                        if ($key->json_data != "") {
                            $key->json_data = json_decode($key->json_data);
                        }

                    }
                    return $result;
                });
            }
            $redis_result = Cache::get("getDataByCatalogIdForAdmin$this->catalog_id");

            if (!$redis_result) {
                $redis_result = [];
            }


            $response = Response::json(array('code' => 200, 'message' => 'Data fetched successfully.', 'cause' => '', 'data' => ['image_list' => $redis_result]));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            Log::error("getDataByCatalogIdForAdmin : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get data by catalog id.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    /**
     * @api {post} getImagesByCatalogId   getImagesByCatalogId
     * @apiName getImagesByCatalogId
     * @apiGroup User
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     *  "catalog_id":1
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Images fetched successfully.",
     * "cause": "",
     * "data": {
     * "image_list": [
     * {
     * "img_id": 3303,
     * "thumbnail_img": "http://192.168.0.113/photo_editor_lab_backend/image_bucket/thumbnail/5c6bb53019f81_normal_image_1550562608.jpg",
     * "compressed_img": "http://192.168.0.113/photo_editor_lab_backend/image_bucket/compressed/5c6bb53019f81_normal_image_1550562608.jpg",
     * "original_img": "http://192.168.0.113/photo_editor_lab_backend/image_bucket/original/5c6bb53019f81_normal_image_1550562608.jpg",
     * "is_json_data": 0,
     * "json_data": "",
     * "is_featured": "",
     * "is_free": 0,
     * "is_portrait": 0,
     * "search_category": ""
     * },
     * {
     * "img_id": 3304,
     * "thumbnail_img": "http://192.168.0.113/photo_editor_lab_backend/image_bucket/thumbnail/5c6bb530289e5_normal_image_1550562608.jpg",
     * "compressed_img": "http://192.168.0.113/photo_editor_lab_backend/image_bucket/compressed/5c6bb530289e5_normal_image_1550562608.jpg",
     * "original_img": "http://192.168.0.113/photo_editor_lab_backend/image_bucket/original/5c6bb530289e5_normal_image_1550562608.jpg",
     * "is_json_data": 0,
     * "json_data": "",
     * "is_featured": "",
     * "is_free": 0,
     * "is_portrait": 0,
     * "search_category": ""
     * }
     * ]
     * }
     * }
     */
    public function getImagesByCatalogId(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());

            if (($response = (new VerificationController())->validateRequiredParameter(array('catalog_id'), $request)) != '')
                return $response;

            $this->catalog_id = $request->catalog_id;

            if (!Cache::has("pel:getImagesByCatalogId$this->catalog_id")) {
                $result = Cache::rememberforever("getImagesByCatalogId$this->catalog_id", function () {
                    $result = DB::select('SELECT
                                          im.id as img_id,
                                          IF(im.image != "",CONCAT("' . Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",im.image),"") as thumbnail_img,
                                          IF(im.image != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",im.image),"") as compressed_img,
                                          IF(im.image != "",CONCAT("' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",im.image),"") as original_img,
                                          IF(im.json_data IS NOT NULL,1,0) as is_json_data,
                                          coalesce(im.json_data,"") as json_data,
                                          coalesce(im.is_featured,"") as is_featured,
                                          coalesce(im.is_free,0) as is_free,
                                          coalesce(im.is_portrait,0) as is_portrait,
                                          coalesce(im.search_category,"") as search_category
                                        FROM
                                          images as im
                                        where
                                          im.is_active = 1 AND
                                          im.catalog_id = ? AND
                                          isnull(im.original_img) AND
                                          isnull(im.display_img)
                                        order by im.updated_at DESC', [$this->catalog_id]);

                    /*foreach ($result as $key) {
                         if ($key->json_data != "") {
                             $key->json_data = json_decode($key->json_data);
                         }
                     }*/

                    return $result;
                });
            }
            $redis_result = Cache::get("getImagesByCatalogId$this->catalog_id");

            if (!$redis_result) {
                $redis_result = [];
            }


            $response = Response::json(array('code' => 200, 'message' => 'Images fetched successfully.', 'cause' => '', 'data' => ['image_list' => $redis_result]));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            Log::error("getImagesByCatalogId : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get image.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    /* ======================================== Link Catalog ===================================== */

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

    /**
     * @api {post} linkCatalog linkCatalog
     * @apiName linkCatalog
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     *  Key: Authorization
     *  Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "catalog_id":2,
     * "sub_category_id":10
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Catalog Linked Successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function linkCatalog(Request $request)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request->getContent());
            //Log::info("linkCatalog Request :", [$request]);

            if (($response = (new VerificationController())->validateRequiredParameter(array('catalog_id', 'sub_category_id'), $request)) != '')
                return $response;

            //$query=DB::select('select * from sub_category_catalog WHERE sub_category_id = ? AND catalog_id = ?',[$sub_category_id,$catalog_id]);
            $catalog_id = $request->catalog_id;
            $sub_category_id = $request->sub_category_id;
            $create_at = date('Y-m-d H:i:s');

            $catalog_name = DB::select('SELECT name from catalog_master WHERE id = ?', [$catalog_id]);

            if (($response = (new VerificationController())->checkIfCatalogExist($sub_category_id, $catalog_name[0]->name, $catalog_id)) != '') {
                $sub_category_name = DB::select('SELECT name from sub_category WHERE id = ?', [$sub_category_id]);
                return $response = Response::json(array('code' => 201, 'message' => '"' . $catalog_name[0]->name . '" already exist in "' . $sub_category_name[0]->name . '" category.', 'cause' => '', 'data' => json_decode("{}")));
            }


            DB::beginTransaction();
            DB::insert('INSERT INTO sub_category_catalog(sub_category_id,catalog_id,created_at) VALUES (?, ?, ?)', [$sub_category_id, $catalog_id, $create_at]);
            DB::commit();

            $response = Response::json(array('code' => 200, 'message' => 'Catalog linked successfully.', 'cause' => '', 'data' => json_decode('{}')));
        } catch (Exception $e) {
            Log::error("linkCatalog : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'link catalog.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /**
     * @api {post} getAllSubCategoryForLinkCatalog   getAllSubCategoryForLinkCatalog
     * @apiName getAllSubCategoryForLinkCatalog
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "catalog_id":18,
     * "sub_category_id":20
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "SubCategory Fetched Successfully.",
     * "cause": "",
     * "data": {
     * "category_list": [
     * {
     * "sub_category_id": 13,
     * "name": "Background",
     * "linked": 0
     * },
     * {
     * "sub_category_id": 12,
     * "name": "Frames",
     * "linked": 1
     * },
     * {
     * "sub_category_id": 10,
     * "name": "Goggles",
     * "linked": 0
     * },
     * {
     * "sub_category_id": 9,
     * "name": "Hair Style",
     * "linked": 0
     * },
     * {
     * "sub_category_id": 4,
     * "name": "Tattoos",
     * "linked": 0
     * },
     * {
     * "sub_category_id": 11,
     * "name": "Turbans",
     * "linked": 0
     * }
     * ]
     * }
     * }
     */
    public function getAllSubCategoryForLinkCatalog(Request $request)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request->getContent());
            //Log::info("getImagesByCatalogId Request :", [$request]);

            if (($response = (new VerificationController())->validateRequiredParameter(array('catalog_id', 'category_id'), $request)) != '')
                return $response;

            $this->catalog_id = $request->catalog_id;
            $this->category_id = $request->category_id;

            if (!Cache::has("pel:getAllSubCategoryForLinkCatalog$this->catalog_id:$this->category_id")) {
                $result = Cache::rememberforever("getAllSubCategoryForLinkCatalog$this->catalog_id:$this->category_id", function () {

                    return DB::select('SELECT
                                          id AS sub_category_id,
                                          name,
                                          IF((SELECT sub_category_id
                                              FROM sub_category_catalog scc
                                              WHERE catalog_id = ? and sc.id=scc.sub_category_id and scc.is_active=1 LIMIT 1) ,1,0) as linked
                                          FROM sub_category sc
                                          WHERE
                                            sc.is_active = 1 AND
                                            sc.category_id=?
                                          ORDER BY name', [$this->catalog_id, $this->category_id]);

                    /*return DB::select('SELECT
                                          id AS sub_category_id,
                                          name,
                                          IF((SELECT sub_category_id
                                           FROM sub_category_catalog scc
                                           WHERE catalog_id = ? and sc.id=scc.sub_category_id and scc.is_active=1 LIMIT 1) ,1,0) as linked
                                        FROM sub_category sc
                                        WHERE is_active = 1
                                        ORDER BY name',[$this->catalog_id]);*/
                });

            }

            $redis_result = Cache::get("getAllSubCategoryForLinkCatalog$this->catalog_id:$this->category_id");

            if (!$redis_result) {
                $redis_result = [];
            }

            $response = Response::json(array('code' => 200, 'message' => 'Sub categories fetched successfully.', 'cause' => '', 'data' => ['category_list' => $redis_result]));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            Log::error("getAllSubCategoryForLinkCatalog : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get all sub categories.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    /**
     * @api {post} deleteLinkedCatalog deleteLinkedCatalog
     * @apiName deleteLinkedCatalog
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     *  Key: Authorization
     *  Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "catalog_id":2,
     * "sub_category_id":10
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Catalog unlinked Successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function deleteLinkedCatalog(Request $request)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('catalog_id', 'sub_category_id'), $request)) != '')
                return $response;

            $catalog_id = $request->catalog_id;
            $sub_category_id = $request->sub_category_id;

            DB::beginTransaction();

            //$result = DB::select('select image from catalog_master where id = ?', [$catalog_id]);
            //$image_name = $result[0]->image;

            //$result = DB::select('SELECT sub_category_id from sub_category_catalog WHERE sub_category_id=? and is_active=? and catalog_id = ?', [$sub_category_id, 1, $catalog_id]);
            $result = DB::select('SELECT count(*) as count_catalog FROM sub_category_catalog WHERE catalog_id = ? AND is_active = 1', [$catalog_id]);
            if ($result[0]->count_catalog > 1) {
                DB::delete('DELETE FROM sub_category_catalog WHERE sub_category_id = ? AND catalog_id = ? ', [$sub_category_id, $catalog_id]);
                $response = Response::json(array('code' => 200, 'message' => 'Catalog unlinked successfully.', 'cause' => '', 'data' => json_decode('{}')));

            } else {
                $response = Response::json(array('code' => 201, 'message' => 'Unable to de-link this catalog, it is not linked with any other application.', 'cause' => '', 'data' => json_decode("{}")));

            }

            DB::commit();

//            //Image Delete in image_bucket
//            (new ImageController())->deleteImage($image_name);
//
//            Log::info("catalog_id:", [$image_name]);
//            foreach ($result as $rw) {
//                //Image Delete in image_bucket
//                (new ImageController())->deleteImage($rw->image);
//            }

        } catch (Exception $e) {
            Log::error("deleteLinkedCatalog : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'delete linked catalog.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /* ======================================== Move Template ===================================== */

    /**
     * @api {post} moveTemplate moveTemplate
     * @apiName moveTemplate
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     *  Key: Authorization
     *  Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "catalog_id":201, //compulsory
     * "template_list":[3386] //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Template moved successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function moveTemplate(Request $request)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('catalog_id'), $request)) != '')
                return $response;

            $response = (new VerificationController())->validateRequiredArrayParameter(array('template_list'), $request);
            if ($response != '') {
                return $response;
            }

            $catalog_id = $request->catalog_id;
            $template_list = $request->template_list;

            foreach ($template_list as $key) {
                DB::beginTransaction();
                DB::insert('UPDATE images SET catalog_id = ? where id = ?', [$catalog_id, $key]);
                DB::commit();
            }

            $response = Response::json(array('code' => 200, 'message' => 'Template moved successfully.', 'cause' => '', 'data' => json_decode('{}')));
        } catch (Exception $e) {
            Log::error("moveTemplate : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'move template.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /**
     * @api {post} getAllSubCategoryToMoveTemplate   getAllSubCategoryToMoveTemplate
     * @apiName getAllSubCategoryToMoveTemplate
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "img_id":3386 //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Sub categories are fetched successfully.",
     * "cause": "",
     * "data": {
     * "sub_category_list": [
     * {
     * "sub_category_id": 66,
     * "sub_category_name": "All Templates",
     * "catalog_list": [
     * {
     * "catalog_id": 508,
     * "catalog_name": "Dhruvit",
     * "is_linked": 0
     * }
     * ]
     * },
     * {
     * "sub_category_id": 88,
     * "sub_category_name": "Baby Photo Maker",
     * "catalog_list": [
     * {
     * "catalog_id": 274,
     * "catalog_name": "Baby Collage",
     * "is_linked": 0
     * },
     * {
     * "catalog_id": 249,
     * "catalog_name": "Baby with Parents",
     * "is_linked": 0
     * }
     * ]
     * }
     * ]
     * }
     * }
     */
    public function getAllSubCategoryToMoveTemplate(Request $request)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('img_id'), $request)) != '')
                return $response;

            $this->img_id = $request->img_id;

            if (!Cache::has("pel:getAllSubCategoryToMoveTemplate$this->img_id")) {
                $result = Cache::rememberforever("getAllSubCategoryToMoveTemplate$this->img_id", function () {

                    $sub_categories = DB::select('SELECT
                                                        distinct sc.id AS sub_category_id,
                                                        sc.name AS sub_category_name
                                                      FROM sub_category sc
                                                        LEFT JOIN sub_category_catalog AS scc ON sc.id=scc.sub_category_id AND scc.is_active=1
                                                      WHERE
                                                        sc.is_active = 1 AND 
                                                        sc.is_featured = 1
                                                      ORDER BY name');

                    foreach ($sub_categories as $key) {
                        $catalogs = DB::select('SELECT
                                                      DISTINCT scc.catalog_id,
                                                      cm.name AS catalog_name,
                                                      ifnull ((SELECT 1 FROM images AS im WHERE im.id = ? AND scc.catalog_id = im.catalog_id),0) AS is_linked
                                                    FROM sub_category_catalog AS scc
                                                      JOIN catalog_master AS cm
                                                        ON cm.id=scc.catalog_id AND
                                                           cm.is_active=1 AND
                                                           cm.is_featured = 1
                                                    WHERE
                                                      scc.is_active = 1 AND
                                                      scc.sub_category_id = ?
                                                    ORDER BY name', [$this->img_id, $key->sub_category_id]);

                        $key->catalog_list = $catalogs;

                    }

                    return $sub_categories;

                });

            }

            $redis_result = Cache::get("getAllSubCategoryToMoveTemplate$this->img_id");

            if (!$redis_result) {
                $redis_result = [];
            }

            $response = Response::json(array('code' => 200, 'message' => 'Sub categories are fetched successfully.', 'cause' => '', 'data' => ['sub_category_list' => $redis_result]));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            Log::error("getAllSubCategoryToLinkTemplate : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . ' get all sub category.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    /* ========================================= URL ============================================== */

    /**
     * @api {post} addLink addLink
     * @apiName addLink
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     *  Key: Authorization
     *  Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * request_data:{
     * "sub_category_id":46,
     * "name":"QR Scanner",
     * "url":"https://play.google.com/store/apps/details?id=com.optimumbrewlab.dqnentrepreneur&hl=en",
     * "platform":"Android",
     * "app_description":"This is test description."
     * }
     * file:ob.png
     * logo_file:logo_image.png
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Link Added Successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function addLink(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            if (!$request_body->has('request_data'))
                return Response::json(array('code' => 201, 'message' => 'Required field request_data is missing or empty.', 'cause' => '', 'data' => json_decode("{}")));

            $request = json_decode($request_body->input('request_data'));
            if (($response = (new VerificationController())->validateRequiredParameter(array('platform', 'url', 'platform', 'app_description'), $request)) != '')
                return $response;

            $name = $request->name;
            $url = $request->url;
            $platform = $request->platform;
            $app_description = $request->app_description;
            $create_at = date('Y-m-d H:i:s');

            if (($response = (new VerificationController())->checkIfAdvertisementExist($url)) != '')
                return $response;

            if (!$request_body->hasFile('file')) {
                return Response::json(array('code' => 201, 'message' => 'Required field file is missing or empty.', 'cause' => '', 'data' => json_decode("{}")));
            } else {
                $image_array = Input::file('file');
                if (($response = (new ImageController())->verifyImage($image_array)) != '')
                    return $response;

                $app_image = (new ImageController())->generateNewFileName('banner_image', $image_array);
                (new ImageController())->saveOriginalImage($app_image);
                (new ImageController())->saveCompressedImage($app_image);
                (new ImageController())->saveThumbnailImage($app_image);

                if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
                    (new ImageController())->saveImageInToS3($app_image);
                }

            }

            //Logo Image
            if (!$request_body->hasFile('logo_file')) {
                return Response::json(array('code' => 201, 'message' => 'required field logo_file is missing or empty', 'cause' => '', 'data' => json_decode("{}")));
            } else {
                $logo_image_array = Input::file('logo_file');
                if (($response = (new ImageController())->verifyImage($logo_image_array)) != '')
                    return $response;

                $app_logo = (new ImageController())->generateNewFileName('app_logo_image', $logo_image_array);
                (new ImageController())->saveMultipleOriginalImage($app_logo, 'logo_file');
                (new ImageController())->saveCompressedImage($app_logo);
                (new ImageController())->saveThumbnailImage($app_logo);

                if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
                    (new ImageController())->saveImageInToS3($app_logo);
                }
            }

            $data = array('name' => $name,
                'image' => $app_image,//Application banner
                'app_logo_img' => $app_logo,//$app_logo,
                'url' => $url,
                'platform' => $platform,
                'app_description' => $app_description,
                'created_at' => $create_at);
            DB::beginTransaction();
            $advertise_link_id = DB::table('advertise_links')->insertGetId($data);

            //DB::insert('insert into sub_category_advertise_links(sub_category_id, advertise_link_id, is_active, created_at) VALUES (?, ?, ?, ?)', [$sub_category_id, $advertise_link_id, 1, $create_at]);

            DB::commit();

            $response = Response::json(array('code' => 200, 'message' => 'Link added successfully.', 'cause' => '', 'data' => json_decode('{}')));
        } catch (Exception $e) {
            Log::error("addLink :", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'add link.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /**
     * @api {post} updateLink updateLink
     * @apiName updateLink
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     *  Key: Authorization
     *  Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * request_data:{
     * "sub_category_id": 46,
     * "advertise_link_id": 51,
     * "name": "QR Scanner",
     * "url": "https://play.google.com/store/apps/details?id=com.optimumbrewlab.dqnentrepreneur&hl=en",
     * "platform": "Android",
     * "app_description": "This is test description"
     * }
     * file:ob.png //optional
     * logo_file:ob.png //optional
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Link Updated Successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function updateLink(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

//            if (!$request_body->has('request_data'))
//                return Response::json(array('code' => 201, 'message' => 'Required field request_data is missing or empty.', 'cause' => '', 'data' => json_decode("{}")));
//
//            $request = json_decode($request_body->input('request_data'));
//
//            Log::info("request data :", [$request]);
//            if (($response = (new VerificationController())->validateRequiredParameter(array('advertise_link_id', 'name', 'platform', 'url'), $request)) != '')
//                return $response;
//
//            $token = JWTAuth::getToken();
//            JWTAuth::toUser($token);
//
//            $advertise_link_id = $request->advertise_link_id;
//            $name = $request->name;
//            $url = $request->url;
//            $platform = $request->platform;
//            $create_at = date('Y-m-d H:i:s');
//            $image_name = '';
//            $logo_image_name = '';
//            DB::beginTransaction();
//
//            if ((!$request_body->hasFile('file'))) {
//                //Log::info("Without Both file.");
//                DB::update('update advertise_links
//                            SET name = ?,
//                                url = ?,
//                                platform = ?,
//                                created_at = ?
//                            WHERE
//                                id = ?', [$name, $url, $platform, $create_at, $advertise_link_id]);
//            } else {
//                Log::info("Only File");
//                $image_array = Input::file('file');
//                if (($response = (new ImageController())->verifyImage($image_array)) != '')
//                    return $response;
//
//                $app_image = (new ImageController())->generateNewFileName('app_image', $image_array);
//                //Log::info("file size :",[filesize($profile_img)]);
//                (new ImageController())->saveMultipleOriginalImage($app_image, 'file');
//                (new ImageController())->saveCompressedImage($app_image);
//                (new ImageController())->saveThumbnailImage($app_image);
//
//                $result = DB::select('select image from advertise_links where id = ?', [$advertise_link_id]);
//                $image_name = $result[0]->image;
//
//                DB::update('update advertise_links
//                            SET name = ?,
//                                image = ?,
//                                url = ?,
//                                platform = ?,
//                                created_at = ?
//                            WHERE
//                                id = ?', [$name, $app_image, $url, $platform, $create_at, $advertise_link_id]);
//
//            }
//            DB::commit();
//
//            //Image Delete in image_bucket
//            if ($image_name) {
//                (new ImageController())->deleteImage($image_name);
//            }
//            if ($logo_image_name) {
//                (new ImageController())->deleteImage($logo_image_name);
//            }

            if (!$request_body->has('request_data'))
                return Response::json(array('code' => 201, 'message' => 'Required field request_data is missing or empty.', 'cause' => '', 'data' => json_decode("{}")));

            $request = json_decode($request_body->input('request_data'));

            //Log::info("request data :", [$request]);
            if (($response = (new VerificationController())->validateRequiredParameter(array('advertise_link_id', 'name', 'platform', 'url', 'app_description'), $request)) != '')
                return $response;

            $advertise_link_id = $request->advertise_link_id;
            $name = $request->name;
            $url = $request->url;
            $platform = $request->platform;
            $app_description = $request->app_description;
            $create_at = date('Y-m-d H:i:s');
            $image_name = '';
            $logo_image_name = '';
            DB::beginTransaction();

            if ((!$request_body->hasFile('file')) && (!$request_body->hasFile('logo_file'))) {
                //Log::info("Without Both file.");
                DB::update('UPDATE advertise_links
                            SET name = ?,
                                url = ?,
                                platform = ?,
                                app_description = ?,
                                created_at = ?
                            WHERE
                                id = ?', [$name, $url, $platform, $app_description, $create_at, $advertise_link_id]);
            } elseif ((!$request_body->hasFile('logo_file')) && $request_body->hasFile('file')) {
                //Log::info("Only File");
                $image_array = Input::file('file');
                if (($response = (new ImageController())->verifyImage($image_array)) != '')
                    return $response;

                $app_image = (new ImageController())->generateNewFileName('banner_image', $image_array);
                //Log::info("file size :",[filesize($profile_img)]);
                (new ImageController())->saveMultipleOriginalImage($app_image, 'file');
                (new ImageController())->saveCompressedImage($app_image);
                (new ImageController())->saveThumbnailImage($app_image);

                if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
                    (new ImageController())->saveImageInToS3($app_image);
                }

                $result = DB::select('SELECT image FROM advertise_links WHERE id = ?', [$advertise_link_id]);
                $image_name = $result[0]->image;

                DB::update('UPDATE advertise_links
                            SET name = ?,
                                image = ?,
                                url = ?,
                                platform = ?,
                                app_description = ?,
                                created_at = ?
                            WHERE
                                id = ?', [$name, $app_image, $url, $platform, $app_description, $create_at, $advertise_link_id]);

            } elseif ($request_body->hasFile('logo_file') && (!$request_body->hasFile('file'))) {
                //Log::info("Only Logo-File");
                $image_array = Input::file('logo_file');
                if (($response = (new ImageController())->verifyImage($image_array)) != '')
                    return $response;

                $logo_image = (new ImageController())->generateNewFileName('app_logo_image', $image_array);
                //Log::info("file size :",[filesize($profile_img)]);
                (new ImageController())->saveMultipleOriginalImage($logo_image, 'logo_file');
                (new ImageController())->saveCompressedImage($logo_image);
                (new ImageController())->saveThumbnailImage($logo_image);

                if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
                    (new ImageController())->saveImageInToS3($logo_image);
                }

                $result = DB::select('SELECT app_logo_img FROM advertise_links WHERE id = ?', [$advertise_link_id]);
                $logo_image_name = $result[0]->app_logo_img;

                DB::update('UPDATE advertise_links
                            SET name = ?,
                                app_logo_img = ?,
                                url = ?,
                                platform = ?,
                                app_description = ?,
                                created_at = ?
                            WHERE
                                id = ?', [$name, $logo_image, $url, $platform, $app_description, $create_at, $advertise_link_id]);

            } else {
                //Log::info("Both File");
                $image_array = Input::file('file');
                $logo_image_array = Input::file('logo_file');

                if (($response = (new ImageController())->verifyImage($image_array)) != '')
                    return $response;

                if (($response = (new ImageController())->verifyImage($logo_image_array)) != '')
                    return $response;

                $app_image = (new ImageController())->generateNewFileName('banner_image', $image_array);
                $logo_image = (new ImageController())->generateNewFileName('app_logo_image', $logo_image_array);
                //Log::info("file size :",[filesize($profile_img)]);
                (new ImageController())->saveOriginalImage($app_image);
                (new ImageController())->saveCompressedImage($app_image);
                (new ImageController())->saveThumbnailImage($app_image);

                if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
                    (new ImageController())->saveImageInToS3($app_image);
                }


                (new ImageController())->saveMultipleOriginalImage($logo_image, 'logo_file');
                (new ImageController())->saveCompressedImage($logo_image);
                (new ImageController())->saveThumbnailImage($logo_image);

                if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
                    (new ImageController())->saveImageInToS3($logo_image);
                }


                $result = DB::select('SELECT image,app_logo_img FROM advertise_links WHERE id = ?', [$advertise_link_id]);
                $image_name = $result[0]->image;
                $logo_image_name = $result[0]->app_logo_img;

                DB::update('UPDATE advertise_links
                            SET name = ?,
                                image = ?,
                                app_logo_img = ?,
                                url = ?,
                                platform = ?,
                                app_description = ?,
                                created_at = ?
                            WHERE
                                id = ?', [$name, $app_image, $logo_image, $url, $platform, $app_description, $create_at, $advertise_link_id]);

            }


            DB::commit();

            //Image Delete in image_bucket
            if ($image_name) {
                (new ImageController())->deleteImage($image_name);
            }
            if ($logo_image_name) {
                (new ImageController())->deleteImage($logo_image_name);
            }

            $response = Response::json(array('code' => 200, 'message' => 'Link updated successfully.', 'cause' => '', 'data' => json_decode('{}')));
        } catch (Exception $e) {
            Log::error("updateLink : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'update link.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /**
     * @api {post} deleteLink deleteLink
     * @apiName deleteLink
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     *  Key: Authorization
     *  Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     *  "advertise_link_id:1
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Link Deleted Successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function deleteLink(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('advertise_link_id'), $request)) != '')
                return $response;

            $advertise_link_id = $request->advertise_link_id;

            DB::beginTransaction();

            //DB::delete('delete FROM sub_category_advertise_links WHERE advertise_link_id = ?', [$advertise_link_id]);

            DB::delete('DELETE FROM advertise_links WHERE id = ? ', [$advertise_link_id]);

            DB::commit();

            $response = Response::json(array('code' => 200, 'message' => 'Link deleted successfully.', 'cause' => '', 'data' => json_decode('{}')));
        } catch (Exception $e) {
            Log::error("deleteLink : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'delete link.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /**
     * @api {post} getAllLink   getAllLink
     * @apiName getAllLink
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
     * "item_count":10
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "All Link Fetched Successfully.",
     * "cause": "",
     * "data": {
     * "total_record": 2,
     * "is_next_page": false,
     * "link_list": [
     * {
     * "advertise_link_id": 51,
     * "name": "QR Scanner",
     * "thumbnail_img": "http://localhost/ob_photolab_backend/image_bucket/thumbnail/5a0437f82a94f_banner_image_1510225912.jpg",
     * "compressed_img": "http://localhost/ob_photolab_backend/image_bucket/compressed/5a0437f82a94f_banner_image_1510225912.jpg",
     * "original_img": "http://localhost/ob_photolab_backend/image_bucket/original/5a0437f82a94f_banner_image_1510225912.jpg",
     * "app_logo_thumbnail_img": "http://localhost/ob_photolab_backend/image_bucket/thumbnail/5a0437f82ad37_app_logo_image_1510225912.jpg",
     * "app_logo_compressed_img": "http://localhost/ob_photolab_backend/image_bucket/compressed/5a0437f82ad37_app_logo_image_1510225912.jpg",
     * "app_logo_original_img": "http://localhost/ob_photolab_backend/image_bucket/original/5a0437f82ad37_app_logo_image_1510225912.jpg",
     * "url": "https://play.google.com/store/apps/details?id=com.optimumbrewlab.dqnentrepreneur&hl=en",
     * "platform": "Android",
     * "app_description": "This is test description"
     * },
     * {
     * "advertise_link_id": 52,
     * "name": "QR Scanner",
     * "thumbnail_img": "http://localhost/ob_photolab_backend/image_bucket/thumbnail/5a04375d4c4ed_banner_image_1510225757.jpg",
     * "compressed_img": "http://localhost/ob_photolab_backend/image_bucket/compressed/5a04375d4c4ed_banner_image_1510225757.jpg",
     * "original_img": "http://localhost/ob_photolab_backend/image_bucket/original/5a04375d4c4ed_banner_image_1510225757.jpg",
     * "app_logo_thumbnail_img": "http://localhost/ob_photolab_backend/image_bucket/thumbnail/5a0437600e172_app_logo_image_1510225760.jpeg",
     * "app_logo_compressed_img": "http://localhost/ob_photolab_backend/image_bucket/compressed/5a0437600e172_app_logo_image_1510225760.jpeg",
     * "app_logo_original_img": "http://localhost/ob_photolab_backend/image_bucket/original/5a0437600e172_app_logo_image_1510225760.jpeg",
     * "url": "https://play.google.com/store/apps/details?id=com.optimumbrewlab.dqnentrepreneur&hl=en",
     * "platform": "Android",
     * "app_description": "This is test description."
     * }
     * ]
     * }
     * }
     */
    public function getAllLink(Request $request_body)
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
            //$item_count = Config::get('constant.PAGINATION_ITEM_LIMIT');
            $this->offset = ($this->page - 1) * $this->item_count;

            $total_row_result = DB::select('SELECT COUNT(*) as total FROM  sub_category_advertise_links where is_active = ? AND sub_category_id = ?', [1, $this->sub_category_id]);
            $total_row = $total_row_result[0]->total;
            //return $total_row_result;
            if (!Cache::has("pel:getAllLink$this->page:$this->item_count:$this->sub_category_id")) {
                $result = Cache::rememberforever("getAllLink$this->page:$this->item_count:$this->sub_category_id", function () {
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
                                        sadl.sub_category_id = ? AND
                                        sadl.advertise_link_id=adl.id AND
                                        sadl.is_active=1
                                      order by adl.updated_at DESC
                                      LIMIT ?,?', [$this->sub_category_id, $this->offset, $this->item_count]);

//                    return DB::select('SELECT
//                                        adl.id as advertise_link_id,
//                                        adl.name,
//                                        IF(adl.image != "",CONCAT("' . $this->base_url . Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY') . '",adl.image),"") as thumbnail_img,
//                                        IF(adl.image != "",CONCAT("' . $this->base_url . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY') . '",adl.image),"") as compressed_img,
//                                        IF(adl.image != "",CONCAT("' . $this->base_url . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY') . '",adl.image),"") as original_img,
//                                        adl.url,
//                                        adl.platform
//                                      FROM
//                                        advertise_links as adl,
//                                        sub_category_advertise_links as sadl
//                                      WHERE
//                                        sadl.sub_category_id = ? AND
//                                        sadl.advertise_link_id=adl.id AND
//                                        sadl.is_active=1
//                                      order by adl.updated_at DESC
//                                      LIMIT ?,?', [$this->sub_category_id, $this->offset, $this->item_count]);
                });
            }
            $redis_result = Cache::get("getAllLink$this->page:$this->item_count:$this->sub_category_id");

            if (!$redis_result) {
                $redis_result = [];
            }

            $is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;

            $response = Response::json(array('code' => 200, 'message' => 'All links fetched successfully.', 'cause' => '', 'data' => ['total_record' => $total_row, 'is_next_page' => $is_next_page, 'link_list' => $redis_result]));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            Log::error("getAllLink : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get all links.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

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

    /* ========================================= Other ==============================================*/

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

                if (($response = (new ImageController())->verifyImage($image_array)) != '')
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

    /**
     * @api {post} getImageDetails   getImageDetails
     * @apiName getImageDetails
     * @apiGroup Admin
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
     * "order_by":"size",
     * "order_type":"ASC"
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "All User Fetched Successfully.",
     * "cause": "",
     * "data": {
     * "total_record": 36,
     * "is_next_page": true,
     * "image_details": [
     * {
     * "name": "59687a44dcae2_background_img_1500019268.png",
     * "thumbnail_img": "http://192.168.0.102/ob_photolab_backend/image_bucket/thumbnail/59687a44dcae2_background_img_1500019268.png",
     * "compressed_img": "http://192.168.0.102/ob_photolab_backend/image_bucket/compressed/59687a44dcae2_background_img_1500019268.png",
     * "original_img": "http://192.168.0.102/ob_photolab_backend/image_bucket/original/59687a44dcae2_background_img_1500019268.png",
     * "directory_name": "compress",
     * "type": "png",
     * "size": 4572880,
     * "height": 1440,
     * "width": 1920,
     * "created_at": "2017-07-14 08:01:11"
     * },
     * {
     * "name": "59687aeb86626_background_img_1500019435.png",
     * "thumbnail_img": "http://192.168.0.102/ob_photolab_backend/image_bucket/thumbnail/59687aeb86626_background_img_1500019435.png",
     * "compressed_img": "http://192.168.0.102/ob_photolab_backend/image_bucket/compressed/59687aeb86626_background_img_1500019435.png",
     * "original_img": "http://192.168.0.102/ob_photolab_backend/image_bucket/original/59687aeb86626_background_img_1500019435.png",
     * "directory_name": "compress",
     * "type": "png",
     * "size": 3820520,
     * "height": 1904,
     * "width": 2000,
     * "created_at": "2017-07-14 08:03:57"
     * },
     * {
     * "name": "59687aa95d6be_background_img_1500019369.png",
     * "thumbnail_img": "http://192.168.0.102/ob_photolab_backend/image_bucket/thumbnail/59687aa95d6be_background_img_1500019369.png",
     * "compressed_img": "http://192.168.0.102/ob_photolab_backend/image_bucket/compressed/59687aa95d6be_background_img_1500019369.png",
     * "original_img": "http://192.168.0.102/ob_photolab_backend/image_bucket/original/59687aa95d6be_background_img_1500019369.png",
     * "directory_name": "original",
     * "type": "png",
     * "size": 2863220,
     * "height": 2000,
     * "width": 2000,
     * "created_at": "2017-07-14 08:02:50"
     * }
     * ]
     * }
     * }
     */
    public function getImageDetails(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('page', 'item_count'), $request)) != '')
                return $response;

            $this->item_count = $request->item_count;
            $this->page = $request->page;
            $this->order_by = isset($request->order_by) ? $request->order_by : 'size';
            $this->order_type = isset($request->order_type) ? $request->order_type : 'DESC';
            $this->offset = ($this->page - 1) * $this->item_count;

            if (!Cache::has("pel:getImageDetails$this->page:$this->item_count:$this->order_by:$this->order_type")) {
                $result = Cache::rememberforever("getImageDetails$this->page:$this->item_count:$this->order_by:$this->order_type", function () {

                    $total_row_result = DB::select('SELECT COUNT(*) as total FROM image_details');
                    $total_row = $total_row_result[0]->total;

                    $result = DB::select('SELECT
                                        id.name,
                                        IF(id.name != "",CONCAT("' . Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",id.name),"") as thumbnail_img,
                                        IF(id.name != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",id.name),"") as compressed_img,
                                        IF(id.name != "",CONCAT("' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",id.name),"") as original_img,
                                        id.directory_name,
                                        id.type,
                                        id.size,
                                        id.height,
                                        id.width,
                                        id.created_at
                                      FROM
                                        image_details AS id
                                      ORDER BY id.' . $this->order_by . ' ' . $this->order_type . '
                                      LIMIT ?,?', [$this->offset, $this->item_count]);

                    return array('total_record' => $total_row, 'image_details' => $result);
                });
            }

            $redis_result = Cache::get("getImageDetails$this->page:$this->item_count:$this->order_by:$this->order_type");

            if (!$redis_result) {
                $redis_result = [];
            }

            $response = Response::json(array('code' => 200, 'message' => 'Image details fetched successfully.', 'cause' => '', 'data' => $redis_result));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            Log::error("getImageDetails : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get image details.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    /* ====================================| Json |==================================*/

    /**
     * @api {post} addCatalogImagesForJson   addCatalogImagesForJson
     * @apiName addCatalogImagesForJson
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * request_data:{
     * "is_replace":0 //compulsory 0=do not replace the existing file, 2=replace the existing file
     * },
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
    public function addCatalogImagesForJson(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            //Required parameter
            if (!$request_body->has('request_data'))
                return Response::json(array('code' => 201, 'message' => 'Required field request_data is missing or empty.', 'cause' => '', 'data' => json_decode("{}")));

            $request = json_decode($request_body->input('request_data'));

            if (($response = (new VerificationController())->validateRequiredParameter(array('is_replace'), $request)) != '')
                return $response;

            $is_replace = $request->is_replace;

            if ($request_body->hasFile('file')) {
                $images_array = Input::file('file');

                if ($is_replace == 0) {
                    if (($response = (new ImageController())->checkIsImageExist($images_array,0)) != '')
                        return $response;
                }

                foreach ($images_array as $image_array) {

                    if (($response = (new ImageController())->verifyImage($image_array)) != '')
                        return $response;

                    (new ImageController())->saveResourceImage($image_array);

                    $image = $image_array->getClientOriginalName();

                    if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
                        (new ImageController())->saveResourceImageInToS3($image);
                    }

                }
            }

            $response = Response::json(array('code' => 200, 'message' => 'Json images added successfully.', 'cause' => '', 'data' => json_decode('{}')));

        } catch
        (Exception $e) {
            Log::error("addCatalogImagesForJson : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'add json images.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /**
     * @api {post} addJson   addJson
     * @apiName addJson
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * request_data:{
     * "catalog_id": 155,
     * "is_free": 1,
     * "is_featured": 1,
     * "json_data": {
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
     * },
     * file:image1.jpeg
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Json added successfully!.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function addJson(Request $request_body)
    {

        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->input('request_data'));

            if (($response = (new VerificationController())->validateRequiredParameter(array('catalog_id', 'is_featured', 'is_free'), $request)) != '')
                return $response;

            $catalog_id = $request->catalog_id;
            $json_data = $request->json_data;
            $is_free = $request->is_free;
            $is_featured = $request->is_featured;
            $is_portrait = isset($request->is_portrait) ? $request->is_portrait : NULL;
            //$search_category = $request->search_category;
            $search_category = isset($request->search_category) ? strtolower($request->search_category) : NULL;
            $created_at = date('Y-m-d H:i:s');

            if ($search_category != NULL or $search_category != "") {
                $search_category = $search_category . ',';
            }

            if (($response = (new ImageController())->validateFonts($json_data)) != '')
                return $response;

            DB::beginTransaction();
            if (!$request_body->hasFile('file')) {
                return Response::json(array('code' => 201, 'message' => 'Required field file is missing or empty.', 'cause' => '', 'data' => json_decode("{}")));
            } else {

                $image_array = Input::file('file');
                if (($response = (new ImageController())->verifySampleImage($image_array)) != '')
                    return $response;

                if (($response = (new ImageController())->validateHeightWidthOfSampleImage($image_array, $json_data)) != '')
                    return $response;

                $tag_list = strtolower((new TagDetectController())->getTagInImageByBytes($image_array));
                if ($tag_list == "" or $tag_list == NULL) {

                    if (Config::get('constant.CLARIFAI_API_KEY') != "") {
                        return Response::json(array('code' => 201, 'message' => 'Tag not detected from clarifai.com.', 'cause' => '', 'data' => json_decode("{}")));

                    } else {
                        //remove "," from the end
                        $search_category = str_replace(",", "", $search_category);
                    }
                }

                if (($response = (new VerificationController())->verifySearchCategory("$search_category$tag_list")) != '') {
                    $response_details = (json_decode(json_encode($response), true));
                    $data = $response_details['original']['data'];
                    $tag_list = $data['search_tags'];
                } else {
                    $tag_list = "$search_category$tag_list";
                }

                $catalog_image = (new ImageController())->generateNewFileName('json_image', $image_array);
                (new ImageController())->saveOriginalImage($catalog_image);
                (new ImageController())->saveCompressedImage($catalog_image);
                (new ImageController())->saveThumbnailImage($catalog_image);
                $file_name = (new ImageController())->saveWebpOriginalImage($catalog_image);
                $dimension = (new ImageController())->saveWebpThumbnailImage($catalog_image);

                if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
                    (new ImageController())->saveImageInToS3($catalog_image);
                    (new ImageController())->saveWebpImageInToS3($file_name);
                }


                /*DB::insert('INSERT
                                INTO
                                  images(catalog_id, image, json_data, is_free, is_featured, is_portrait, created_at, attribute1)
                                VALUES(?, ?, ?, ?, ?, ?, ?, ?) ', [$catalog_id, $catalog_image, json_encode($json_data), $is_free, $is_featured, $is_portrait, $created_at, $file_name]);*/


                DB::insert('INSERT
                                INTO
                                  images(catalog_id, image, json_data, is_free, is_featured, is_portrait, search_category, height, width, original_img_height, original_img_width, created_at, attribute1)
                                VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?) ', [$catalog_id, $catalog_image, json_encode($json_data), $is_free, $is_featured, $is_portrait, $tag_list, $dimension['height'], $dimension['width'], $dimension['org_img_height'], $dimension['org_img_width'], $created_at, $file_name]);


                DB::commit();
            }

            if (strstr($file_name, '.webp')) {

                $response = Response::json(array('code' => 200, 'message' => 'Json added successfully.', 'cause' => '', 'data' => json_decode('{}')));


            } else {
                $response = Response::json(array('code' => 200, 'message' => 'Json added successfully. Note: webp is not converted due to size grater than original.', 'cause' => '', 'data' => json_decode('{}')));

            }


        } catch
        (Exception $e) {
            Log::error("addJson : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'add json.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
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

    /**
     * @api {post} editJsonData   editJsonData
     * @apiName editJsonData
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * request_data:{
     * "img_id": 356,
     * "is_free": 1,
     * "is_featured": 1,
     * "json_data": {
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
     * },
     * file:image1.jpeg
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Json data updated successfully!.",
     * "cause": "",
     * "data": {}
     * }
     */
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

    public function editJsonData(Request $request_body)
    {

        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->input('request_data'));
            if (($response = (new VerificationController())->validateRequiredParameter(array('is_featured', 'is_free', 'img_id'), $request)) != '')
                return $response;

            $img_id = $request->img_id;
            $json_data = isset($request->json_data) ? $request->json_data : '';
            $is_free = $request->is_free;
            $is_featured = $request->is_featured;
            $is_portrait = isset($request->is_portrait) ? $request->is_portrait : 0;
            //$search_category = isset($request->search_category) ? $request->search_category : NULL;
            $search_category = isset($request->search_category) ? strtolower($request->search_category) : NULL;

            //Log::info('request_data', ['request_data' => $request]);
            if (($response = (new VerificationController())->verifySearchCategory($search_category)) != '')
                return $response;

            if (($response = (new ImageController())->validateFonts($json_data)) != '')
                return $response;


            DB::beginTransaction();
            if ($request_body->hasFile('file')) {
                $image_array = Input::file('file');
                if (($response = (new ImageController())->verifySampleImage($image_array)) != '')
                    return $response;

                if (($response = (new ImageController())->validateHeightWidthOfSampleImage($image_array, $json_data)) != '')
                    return $response;

                $tag_list = strtolower((new TagDetectController())->getTagInImageByBytes($image_array));
                if (($tag_list == "" or $tag_list == NULL) and Config::get('constant.CLARIFAI_API_KEY') != "") {
                    return Response::json(array('code' => 201, 'message' => 'Tag not detected from clarifai.com.', 'cause' => '', 'data' => json_decode("{}")));
                }

                $catalog_image = (new ImageController())->generateNewFileName('json_image', $image_array);
                (new ImageController())->saveOriginalImage($catalog_image);
                (new ImageController())->saveCompressedImage($catalog_image);
                (new ImageController())->saveThumbnailImage($catalog_image);
                $file_name = (new ImageController())->saveWebpOriginalImage($catalog_image);
                $dimension = (new ImageController())->saveWebpThumbnailImage($catalog_image);

                if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
                    (new ImageController())->saveImageInToS3($catalog_image);
                    (new ImageController())->saveWebpImageInToS3($file_name);
                }


                /*DB::update('UPDATE
                                images SET image = ?, json_data = ?, is_free = ?, is_featured = ?, is_portrait = ?, attribute1 = ?
                                WHERE id = ?', [$catalog_image, json_encode($json_data), $is_free, $is_featured, $is_portrait, $file_name, $img_id]);*/

                DB::update('UPDATE
                                images SET image = ?, json_data = ?, is_free = ?, is_featured = ?, is_portrait = ?, search_category = ?, height = ?, width = ?, original_img_height = ?, original_img_width = ?, attribute1 = ?
                                WHERE id = ?', [$catalog_image, json_encode($json_data), $is_free, $is_featured, $is_portrait, $tag_list, $dimension['height'], $dimension['width'], $dimension['org_img_height'], $dimension['org_img_width'], $file_name, $img_id]);
                DB::commit();

                if (strstr($file_name, '.webp')) {

                    $response = Response::json(array('code' => 200, 'message' => 'Json data updated successfully.', 'cause' => '', 'data' => json_decode('{}')));


                } else {
                    $response = Response::json(array('code' => 200, 'message' => 'Json data updated successfully. Note: webp is not converted due to size grater than original.', 'cause' => '', 'data' => json_decode('{}')));

                }
            } else {

                /* generate webp original & thumbnail from original image (genearte webp for uploaded jpg/png samples) */
                $is_exist = DB::select('SELECT * FROM images WHERE id = ? AND attribute1 IS NULL', [$img_id]);
                DB::beginTransaction();
                if (count($is_exist) > 0) {

                    //Log::info('webp original');
                    $file_data = (new ImageController())->saveWebpImage($is_exist[0]->image);

                    if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
                        (new ImageController())->saveWebpImageInToS3($file_data['filename']);
                    }

                    DB::update('UPDATE
                                images SET height = ?, width = ?, attribute1 = ?
                                WHERE id = ?', [$file_data['height'], $file_data['width'], $file_data['filename'], $img_id]);
                    DB::commit();

                }

                /* generate webp thumbnail from original image when webp thumbnail is not exist */
                $is_exist = DB::select('SELECT * FROM images WHERE id = ? AND width IS NULL AND height IS NULL AND attribute1 IS NOT NULL', [$img_id]);
                if (count($is_exist) > 0) {

                    //Log::info('webp thumbnail');
                    $dimension = (new ImageController())->saveWebpThumbnailImageFromS3($is_exist[0]->image);

                    if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
                        (new ImageController())->saveWebpThumbnailImageInToS3($is_exist[0]->attribute1);
                    }

                    DB::update('UPDATE
                                images SET height = ?, width =?
                                WHERE id = ?', [$dimension['height'], $dimension['width'], $img_id]);
                    DB::commit();
                }


                DB::update('UPDATE
                                images SET json_data = ?, is_free = ?, is_featured = ?, is_portrait = ?, search_category = ?
                                WHERE id = ?', [json_encode($json_data), $is_free, $is_featured, $is_portrait, $search_category, $img_id]);

                DB::commit();

                $response = Response::json(array('code' => 200, 'message' => 'Json data updated successfully!.', 'cause' => '', 'data' => json_decode('{}')));

            }
            DB::commit();


        } catch
        (Exception $e) {
            Log::error("editJsonData : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'edit json data.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /* =================================| Link Advertisement to another subcategory |=============================*/

    /**
     * @api {post} linkAdvertisementWithSubCategory linkAdvertisementWithSubCategory
     * @apiName linkAdvertisementWithSubCategory
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     *  Key: Authorization
     *  Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "advertise_link_id":57,
     * "sub_category_id":47
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Advertisement Linked Successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function linkAdvertisementWithSubCategory(Request $request)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request->getContent());
            //Log::info("linkAdvertisementWithSubCategory Request :", [$request]);

            if (($response = (new VerificationController())->validateRequiredParameter(array('advertise_link_id', 'sub_category_id'), $request)) != '')
                return $response;

            //$query=DB::select('select * from sub_category_catalog WHERE sub_category_id = ? AND catalog_id = ?',[$sub_category_id,$catalog_id]);
            $advertise_link_id = $request->advertise_link_id;
            $sub_category_id = $request->sub_category_id;
            $create_at = date('Y-m-d H:i:s');

            DB::beginTransaction();
            DB::insert('INSERT INTO sub_category_advertise_links(sub_category_id,advertise_link_id,created_at) VALUES (?, ?, ?)', [$sub_category_id, $advertise_link_id, $create_at]);
            DB::commit();

            $response = Response::json(array('code' => 200, 'message' => 'Advertisement linked successfully.', 'cause' => '', 'data' => json_decode('{}')));
        } catch (Exception $e) {
            Log::error("linkAdvertisementWithSubCategory : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'link advertisement with sub_category.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /**
     * @api {post} getAllAdvertisementForLinkAdvertisement   getAllAdvertisementForLinkAdvertisement
     * @apiName getAllAdvertisementForLinkAdvertisement
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "advertise_link_id":57,
     * "category_id":2
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "SubCategory Fetched Successfully.",
     * "cause": "",
     * "data": {
     * "category_list": [
     * {
     * "sub_category_id": 33,
     * "name": "All Sticker Catalogs",
     * "linked": 0
     * },
     * {
     * "sub_category_id": 47,
     * "name": "Collage maker Stickers",
     * "linked": 1
     * },
     * {
     * "sub_category_id": 31,
     * "name": "Fancy QR Generator",
     * "linked": 0
     * },
     * {
     * "sub_category_id": 36,
     * "name": "GreetingsCard Stickers",
     * "linked": 0
     * },
     * {
     * "sub_category_id": 49,
     * "name": "Quotes Creator Stickers",
     * "linked": 1
     * },
     * {
     * "sub_category_id": 28,
     * "name": "Selfie With Ganesha Stickers",
     * "linked": 0
     * }
     * ]
     * }
     * }
     */
    public function getAllAdvertisementForLinkAdvertisement(Request $request)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('advertise_link_id', 'category_id'), $request)) != '')
                return $response;

            $this->advertise_link_id = $request->advertise_link_id;
            $this->category_id = $request->category_id;

            if (!Cache::has("pel:getAllAdvertisementForLinkAdvertisement$this->advertise_link_id:$this->category_id")) {
                $result = Cache::rememberforever("getAllAdvertisementForLinkAdvertisement$this->advertise_link_id:$this->category_id", function () {

                    /* api is updated from fetch advertisement to sub_categories */
                    return DB::select('SELECT
                                            id AS sub_category_id,
                                            name,
                                            IF((SELECT sub_category_id
                                              FROM sub_category_advertise_links scc
                                              WHERE advertise_link_id = ? and sc.id=scc.sub_category_id and scc.is_active=1 LIMIT 1) ,1,0) as linked
                                            FROM sub_category sc
                                            WHERE
                                            sc.is_active = 1 AND
                                            sc.category_id = ?
                                            ORDER BY name', [$this->advertise_link_id, $this->category_id]);
                });

            }

            $redis_result = Cache::get("getAllAdvertisementForLinkAdvertisement$this->advertise_link_id:$this->category_id");

            if (!$redis_result) {
                $redis_result = [];
            }

            $response = Response::json(array('code' => 200, 'message' => 'Sub categories fetched successfully.', 'cause' => '', 'data' => ['category_list' => $redis_result]));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            Log::error("getAllAdvertisementForLinkAdvertisement : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get all sub categories.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    /**
     * @api {post} deleteLinkedAdvertisement deleteLinkedAdvertisement
     * @apiName deleteLinkedAdvertisement
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     *  Key: Authorization
     *  Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "advertise_link_id":57,
     * "sub_category_id":47
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Advertisement unlinked successfully!.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function deleteLinkedAdvertisement(Request $request)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request->getContent());
            //Log::info("Request Data:", [$request]);
            if (($response = (new VerificationController())->validateRequiredParameter(array('advertise_link_id', 'sub_category_id'), $request)) != '')
                return $response;

            $advertise_link_id = $request->advertise_link_id;
            $sub_category_id = $request->sub_category_id;

            DB::beginTransaction();
//
//            $result = DB::select('SELECT count(*) as count_catalog from sub_category_advertise_links WHERE advertise_link_id = ? and is_active = 1', [$advertise_link_id]);
//
//            if ($result[0]->count_catalog > 1) {
            DB::delete('DELETE FROM sub_category_advertise_links WHERE sub_category_id = ? AND advertise_link_id = ? ', [$sub_category_id, $advertise_link_id]);
            $response = Response::json(array('code' => 200, 'message' => 'Advertisement unlinked successfully.', 'cause' => '', 'data' => json_decode('{}')));

//            } else {
//                $response = Response::json(array('code' => 201, 'message' => 'Unable to de-link this advertisement, it is not linked with any other application.', 'cause' => '', 'data' => json_decode("{}")));
//
//            }

            DB::commit();

        } catch (Exception $e) {
            Log::error("deleteLinkedAdvertisement : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'delete linked advertisement.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /* =================================| Link Advertisement |=============================*/

    /**
     * @api {post} getAllAdvertisements   getAllAdvertisements
     * @apiName getAllAdvertisements
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "page":1,
     * "item_count":2
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "All Link Fetched Successfully.",
     * "cause": "",
     * "data": {
     * "total_record": 42,
     * "is_next_page": true,
     * "result": [
     * {
     * "advertise_link_id": 79,
     * "name": "Invitation Maker Card Creator",
     * "thumbnail_img": "http://192.168.0.113/ob_photolab_backend/image_bucket/thumbnail/5a55a1e834d53_banner_image_1515561448.jpg",
     * "compressed_img": "http://192.168.0.113/ob_photolab_backend/image_bucket/compressed/5a55a1e834d53_banner_image_1515561448.jpg",
     * "original_img": "http://192.168.0.113/ob_photolab_backend/image_bucket/original/5a55a1e834d53_banner_image_1515561448.jpg",
     * "app_logo_thumbnail_img": "http://192.168.0.113/ob_photolab_backend/image_bucket/thumbnail/5a55a1e88910f_app_logo_image_1515561448.png",
     * "app_logo_compressed_img": "http://192.168.0.113/ob_photolab_backend/image_bucket/compressed/5a55a1e88910f_app_logo_image_1515561448.png",
     * "app_logo_original_img": "http://192.168.0.113/ob_photolab_backend/image_bucket/original/5a55a1e88910f_app_logo_image_1515561448.png",
     * "url": "https://itunes.apple.com/mu/app/invitation-maker-card-creator/id1320828574?mt=8",
     * "platform": "iOS",
     * "app_description": "Create your own invitation card for party, birthday, wedding ceremony, engagement/ring ceremony within seconds using beautiful and professional templates."
     * },
     * {
     * "advertise_link_id": 78,
     * "name": "Digital Business Card Maker",
     * "thumbnail_img": "http://192.168.0.113/ob_photolab_backend/image_bucket/thumbnail/5a55a158980cd_banner_image_1515561304.jpg",
     * "compressed_img": "http://192.168.0.113/ob_photolab_backend/image_bucket/compressed/5a55a158980cd_banner_image_1515561304.jpg",
     * "original_img": "http://192.168.0.113/ob_photolab_backend/image_bucket/original/5a55a158980cd_banner_image_1515561304.jpg",
     * "app_logo_thumbnail_img": "http://192.168.0.113/ob_photolab_backend/image_bucket/thumbnail/5a55a15977977_app_logo_image_1515561305.png",
     * "app_logo_compressed_img": "http://192.168.0.113/ob_photolab_backend/image_bucket/compressed/5a55a15977977_app_logo_image_1515561305.png",
     * "app_logo_original_img": "http://192.168.0.113/ob_photolab_backend/image_bucket/original/5a55a15977977_app_logo_image_1515561305.png",
     * "url": "https://itunes.apple.com/mu/app/digital-business-card-maker/id1316860834?mt=8",
     * "platform": "iOS",
     * "app_description": "Create your own business card within seconds using beautiful and professional templates."
     * }
     * ]
     * }
     * }
     */
    public function getAllAdvertisements(Request $request)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('page', 'item_count'), $request)) != '')
                return $response;

            $this->page = $request->page;
            $this->item_count = $request->item_count;
            //$item_count = Config::get('constant.PAGINATION_ITEM_LIMIT');
            $this->offset = ($this->page - 1) * $this->item_count;

            if (!Cache::has("pel:getAllAdvertisements$this->page:$this->item_count")) {
                $result = Cache::rememberforever("getAllAdvertisements$this->page:$this->item_count", function () {

                    $total_row_result = DB::select('SELECT COUNT(*) as total FROM  advertise_links where is_active = ?', [1]);
                    $total_row = $total_row_result[0]->total;

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
                                          coalesce(adl.app_description,"") as app_description
                                        FROM
                                          advertise_links as adl
                                        WHERE
                                          is_active=1
                                        order by adl.updated_at DESC LIMIT ?, ?', [$this->offset, $this->item_count]);

                    $is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;

                    return array('total_record' => $total_row, 'is_next_page' => $is_next_page, 'result' => $result);

                });

            }

            $redis_result = Cache::get("getAllAdvertisements$this->page:$this->item_count");

            if (!$redis_result) {
                $redis_result = [];
            }

            $response = Response::json(array('code' => 200, 'message' => 'Advertisements fetched successfully.', 'cause' => '', 'data' => $redis_result));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            Log::error("getAllAdvertisements : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get advertisements.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    /**
     * @api {post} getAllAdvertisementToLinkAdvertisement   getAllAdvertisementToLinkAdvertisement
     * @apiName getAllAdvertisementToLinkAdvertisement
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "sub_category_id":63
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Advertisements fetched successfully.",
     * "cause": "",
     * "data": {
     * "result": [
     * {
     * "advertise_link_id": 79,
     * "name": "Invitation Maker Card Creator",
     * "thumbnail_img": "http://192.168.0.113/ob_photolab_backend/image_bucket/thumbnail/5a55a1e834d53_banner_image_1515561448.jpg",
     * "compressed_img": "http://192.168.0.113/ob_photolab_backend/image_bucket/compressed/5a55a1e834d53_banner_image_1515561448.jpg",
     * "original_img": "http://192.168.0.113/ob_photolab_backend/image_bucket/original/5a55a1e834d53_banner_image_1515561448.jpg",
     * "app_logo_thumbnail_img": "http://192.168.0.113/ob_photolab_backend/image_bucket/thumbnail/5a55a1e88910f_app_logo_image_1515561448.png",
     * "app_logo_compressed_img": "http://192.168.0.113/ob_photolab_backend/image_bucket/compressed/5a55a1e88910f_app_logo_image_1515561448.png",
     * "app_logo_original_img": "http://192.168.0.113/ob_photolab_backend/image_bucket/original/5a55a1e88910f_app_logo_image_1515561448.png",
     * "url": "https://itunes.apple.com/mu/app/invitation-maker-card-creator/id1320828574?mt=8",
     * "platform": "iOS",
     * "app_description": "Create your own invitation card for party, birthday, wedding ceremony, engagement/ring ceremony within seconds using beautiful and professional templates.",
     * "linked": 1
     * },
     * {
     * "advertise_link_id": 78,
     * "name": "Digital Business Card Maker",
     * "thumbnail_img": "http://192.168.0.113/ob_photolab_backend/image_bucket/thumbnail/5a55a158980cd_banner_image_1515561304.jpg",
     * "compressed_img": "http://192.168.0.113/ob_photolab_backend/image_bucket/compressed/5a55a158980cd_banner_image_1515561304.jpg",
     * "original_img": "http://192.168.0.113/ob_photolab_backend/image_bucket/original/5a55a158980cd_banner_image_1515561304.jpg",
     * "app_logo_thumbnail_img": "http://192.168.0.113/ob_photolab_backend/image_bucket/thumbnail/5a55a15977977_app_logo_image_1515561305.png",
     * "app_logo_compressed_img": "http://192.168.0.113/ob_photolab_backend/image_bucket/compressed/5a55a15977977_app_logo_image_1515561305.png",
     * "app_logo_original_img": "http://192.168.0.113/ob_photolab_backend/image_bucket/original/5a55a15977977_app_logo_image_1515561305.png",
     * "url": "https://itunes.apple.com/mu/app/digital-business-card-maker/id1316860834?mt=8",
     * "platform": "iOS",
     * "app_description": "Create your own business card within seconds using beautiful and professional templates.",
     * "linked": 1
     * },
     * {
     * "advertise_link_id": 77,
     * "name": "Romantic Love Photo Editor",
     * "thumbnail_img": "http://192.168.0.113/ob_photolab_backend/image_bucket/thumbnail/5a1e813f47368_banner_image_1511948607.png",
     * "compressed_img": "http://192.168.0.113/ob_photolab_backend/image_bucket/compressed/5a1e813f47368_banner_image_1511948607.png",
     * "original_img": "http://192.168.0.113/ob_photolab_backend/image_bucket/original/5a1e813f47368_banner_image_1511948607.png",
     * "app_logo_thumbnail_img": "http://192.168.0.113/ob_photolab_backend/image_bucket/thumbnail/5a1e814000aa9_app_logo_image_1511948608.png",
     * "app_logo_compressed_img": "http://192.168.0.113/ob_photolab_backend/image_bucket/compressed/5a1e814000aa9_app_logo_image_1511948608.png",
     * "app_logo_original_img": "http://192.168.0.113/ob_photolab_backend/image_bucket/original/5a1e814000aa9_app_logo_image_1511948608.png",
     * "url": "https://play.google.com/store/apps/details?id=com.optimumbrewlab.lovephotoeditor",
     * "platform": "Android",
     * "app_description": "Romantic Love Photo Editor - Realistic Photo Effects, Beautiful Photo Frames, Stickers, etc.",
     * "linked": 0
     * }
     * ]
     * }
     * }
     */
    public function getAllAdvertisementToLinkAdvertisement(Request $request)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('sub_category_id'), $request)) != '')
                return $response;

            $this->sub_category_id = $request->sub_category_id;

            if (!Cache::has("pel:getAllAdvertisementToLinkAdvertisement$this->sub_category_id")) {
                $result = Cache::rememberforever("getAllAdvertisementToLinkAdvertisement$this->sub_category_id", function () {

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
                                          coalesce(adl.app_description,"") as app_description,
                                          IF((SELECT sub_category_id
                                              FROM sub_category_advertise_links scc
                                              WHERE sub_category_id = ? and adl.id=scc.advertise_link_id and scc.is_active=1 LIMIT 1) ,1,0) as linked
                                        FROM
                                          advertise_links as adl
                                        WHERE
                                          adl.is_active=1
                                        order by adl.updated_at DESC', [$this->sub_category_id]);
                });

            }

            $redis_result = Cache::get("getAllAdvertisementToLinkAdvertisement$this->sub_category_id");

            if (!$redis_result) {
                $redis_result = [];
            }

            $response = Response::json(array('code' => 200, 'message' => 'Advertisements fetched successfully.', 'cause' => '', 'data' => ['result' => $redis_result]));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            Log::error("getAllAdvertisementToLinkAdvertisement : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get all advertisements.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    /* =====================================| Content Migrations |=============================================*/

    /**
     * @api {post} addAppContentViaMigration   addAppContentViaMigration
     * @apiName addAppContentViaMigration
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     *  Key: Authorization
     *  Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * request_data:{
     * "sub_category_id":1,
     * "is_free":1,//optional
     * "name":"Nature-2017",
     * "is_featured":1 //compulsory
     * }
     * file:image.jpeg
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "sub category added successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function addAppContentViaMigration(Request $request)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request->getContent());

            //Log::info("Request Data:", [$request]);

            if (($response = (new VerificationController())->validateRequiredParameter(array('catalog_id'), $request)) != '')
                return $response;

            $catalog_id = $request->catalog_id;

            ////(new ImageController())->saveImageInToS3ForMigration('5aac98f036be9_sub_category_img_1521260784.png');


            $result = DB::select('SELECT
                              im.id,
                              scm.sub_category_id,
                              im.image,
                              im.original_img,
                              im.display_img,
                              im.image_type,
                              im.json_data,
                              im.created_at,
                              im.updated_at
                            FROM images AS im,
                               sub_category_catalog AS scm WHERE scm.catalog_id = im.catalog_id AND scm.catalog_id = ?', [$catalog_id]);

            $sub_category_and_catalog = DB::select('SELECT
                                                          DISTINCT scm.id,
                                                          cm.image AS catalog_image,
                                                          sc.image AS sub_category_image,
                                                          scm.sub_category_id
                                                        FROM
                                                          sub_category_catalog AS scm,
                                                          catalog_master AS cm,
                                                          sub_category AS sc
                                                          WHERE sc.id = scm.sub_category_id AND cm.id = scm.catalog_id AND scm.catalog_id = ?', [$catalog_id]);
            foreach ($sub_category_and_catalog as $key) {

                if ($key->sub_category_image) {

                    //(new ImageController())->saveImageInToS3ForMigration($key->sub_category_image);

                }

            }

            if (count($sub_category_and_catalog) > 0) {

                /*if ($sub_category_and_catalog[0]->sub_category_image) {

                    //(new ImageController())->saveImageInToS3($sub_category_and_catalog[0]->sub_category_image);


                }*/

                if ($sub_category_and_catalog[0]->catalog_image) {

                    //(new ImageController())->saveImageInToS3ForMigration($sub_category_and_catalog[0]->catalog_image);
                }

            }

            foreach ($result as $key) {


                if ($key->image) {

                    //(new ImageController())->saveImageInToS3ForMigration($key->image);


                }

                if ($key->original_img) {

                    //(new ImageController())->saveImageInToS3ForMigration($key->original_img);


                }

                if ($key->display_img) {

                    //(new ImageController())->saveImageInToS3ForMigration($key->display_img);


                }

                /*if($key->json_data){

                    $data = array(json_decode($key->json_data));
                    dd($data['image_sticker_json']);


                }*/

            }

            if (count($result) > 0) {

                $response = Response::json(array('code' => 200, 'message' => 'Content uploaded successfully.', 'cause' => '', 'data' => json_decode('{}')));

            } else {
                $response = Response::json(array('code' => 201, 'message' => 'Invalid catalog id.', 'cause' => '', 'data' => json_decode('{}')));

            }


        } catch (Exception $e) {
            Log::error("addAppContentViaMigration : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'Add Catalog.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }

        return $response;
    }

    /* =========================================| Advertise Category |=========================================*/

    /**
     * @api {post} addAdvertisementCategory   addAdvertisementCategory
     * @apiName addAdvertisementCategory
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     *  Key: Authorization
     *  Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * request_data:{
     * "category_id":1,
     * "name":"Nature"
     * }
     * file:image.jpeg
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "sub category added successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function addAdvertisementCategory(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('advertise_category'), $request)) != '')
                return $response;

            $advertise_category = $request->advertise_category;
            $create_at = date('Y-m-d H:i:s');

            DB::beginTransaction();
            DB::insert('INSERT INTO advertise_category_master (advertise_category, is_active, create_time)  VALUES(?, ?, ?)', [$advertise_category, 1, $create_at]);
            DB::commit();

            $response = Response::json(array('code' => 200, 'message' => 'Advertise category added successfully.', 'cause' => '', 'data' => json_decode('{}')));
        } catch (Exception $e) {
            Log::error("addAdvertisementCategory : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'add advertise category.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /**
     * @api {post} editAdvertisementCategory   editAdvertisementCategory
     * @apiName editAdvertisementCategory
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     *  Key: Authorization
     *  Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * request_data:{
     * "category_id":1,
     * "name":"Nature"
     * }
     * file:image.jpeg
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "sub category added successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function editAdvertisementCategory(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('advertise_category', 'advertise_category_id'), $request)) != '')
                return $response;

            $advertise_category = $request->advertise_category;
            $advertise_category_id = $request->advertise_category_id;

            DB::beginTransaction();
            DB::update('UPDATE advertise_category_master SET advertise_category = ? WHERE id = ?', [$advertise_category, $advertise_category_id]);
            DB::commit();

            $response = Response::json(array('code' => 200, 'message' => 'Advertise category updated successfully.', 'cause' => '', 'data' => json_decode('{}')));
        } catch (Exception $e) {
            Log::error("editAdvertisementCategory : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'update advertise category.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /**
     * @api {post} deleteAdvertisementCategory   deleteAdvertisementCategory
     * @apiName deleteAdvertisementCategory
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     *  Key: Authorization
     *  Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * request_data:{
     * "category_id":1,
     * "name":"Nature"
     * }
     * file:image.jpeg
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "sub category added successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function deleteAdvertisementCategory(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('advertise_category_id'), $request)) != '')
                return $response;

            $advertise_category_id = $request->advertise_category_id;

            DB::beginTransaction();
            DB::delete('DELETE FROM advertise_category_master WHERE id = ?', [$advertise_category_id]);
            DB::commit();

            $response = Response::json(array('code' => 200, 'message' => 'Advertise category deleted successfully.', 'cause' => '', 'data' => json_decode('{}')));
        } catch (Exception $e) {
            Log::error("deleteAdvertisementCategory : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'delete advertise category.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /**
     * @api {post} getAllAdvertiseCategory   getAllAdvertiseCategory
     * @apiName getAllAdvertiseCategory
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
     * "message": "Advertise categories fetched successfully.",
     * "cause": "",
     * "data": [
     * {
     * "advertise_category_id": 3,
     * "advertise_category": "Rewarded Video",
     * "is_active": 1,
     * "create_time": "2018-07-16 09:07:07",
     * "update_time": "2018-07-16 09:07:07"
     * },
     * {
     * "advertise_category_id": 1,
     * "advertise_category": "Banner",
     * "is_active": 1,
     * "create_time": "2018-07-16 09:06:47",
     * "update_time": "2018-07-16 09:06:47"
     * },
     * {
     * "advertise_category_id": 2,
     * "advertise_category": "Intertial",
     * "is_active": 1,
     * "create_time": "2018-07-16 09:06:47",
     * "update_time": "2018-07-16 09:06:47"
     * }
     * ]
     * }
     */
    public function getAllAdvertiseCategory()
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            if (!Cache::has("pel:getAllAdvertiseCategory")) {
                $result = Cache::rememberforever("getAllAdvertiseCategory", function () {

                    return DB::select('SELECT
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
                });

            }

            $redis_result = Cache::get("getAllAdvertiseCategory");

            if (!$redis_result) {
                $redis_result = [];
            }

            $response = Response::json(array('code' => 200, 'message' => 'Advertise categories fetched successfully.', 'cause' => '', 'data' => $redis_result));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            Log::error("getAllAdvertiseCategory : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get all advertise categories.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    /* =========================================| Link Advertise Category |=========================================*/

    /**
     * @api {post} addAdvertiseServerId   addAdvertiseServerId
     * @apiName addAdvertiseServerId
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     *  Key: Authorization
     *  Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "advertise_category_id":1, //compulsory
     * "sub_category_id":10, //compulsory
     * "server_id":"vdfjdsjhfbhjbjd" //compulsory
     * "sub_category_advertise_server_id":"vdfjdsjhfbhjbjd"
     * "device_platform":1 //compulsory 1=Ios, 2=Android
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Advertise server id added successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function addAdvertiseServerId(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('advertise_category_id', 'sub_category_id', 'server_id', 'device_platform'), $request)) != '')
                return $response;

            $advertise_category_id = $request->advertise_category_id;
            $sub_category_id = $request->sub_category_id;
            $server_id = $request->server_id;
            $device_platform = $request->device_platform;
            $sub_category_advertise_server_id = isset($request->sub_category_advertise_server_id) ? $request->sub_category_advertise_server_id : 0;

            if (($response = (new VerificationController())->validateAdvertiseServerId($server_id)) != '')
                return $response;

            if ($sub_category_advertise_server_id === 0) {
                $create_time = date('Y-m-d H:i:s');

                DB::beginTransaction();
                DB::insert('INSERT INTO sub_category_advertise_server_id_master (advertise_category_id, sub_category_id, server_id, device_platform, is_active, create_time)    VALUES(?, ?, ?, ?, ?, ?)', [$advertise_category_id, $sub_category_id, $server_id, $device_platform, 1, $create_time]);
                DB::commit();

                $response = Response::json(array('code' => 200, 'message' => 'Advertise server id added successfully.', 'cause' => '', 'data' => json_decode('{}')));
            } else {
                if (($response = (new VerificationController())->validateRequiredParameter(array('sub_category_advertise_server_id'), $request)) != '')
                    return $response;

                DB::beginTransaction();
                DB::update('UPDATE sub_category_advertise_server_id_master SET server_id = ? WHERE id = ?', [$server_id, $sub_category_advertise_server_id]);
                DB::commit();

                $response = Response::json(array('code' => 200, 'message' => 'Advertise server id updated successfully.', 'cause' => '', 'data' => json_decode('{}')));
            }

        } catch (Exception $e) {
            Log::error("addAdvertiseServerId : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'add or update advertise server id.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /**
     * @api {post} updateAdvertiseServerId   updateAdvertiseServerId
     * @apiName updateAdvertiseServerId
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     *  Key: Authorization
     *  Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "sub_category_advertise_server_id":1, //compulsory
     * "advertise_category_id":1, //compulsory
     * "server_id":"absdjdfgjfj" //compulsory
     * }
     * @apiSuccessExample Success-Response:
     *{
     * "code": 200,
     * "message": "Advertise server id updated successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function updateAdvertiseServerId(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('sub_category_advertise_server_id', 'advertise_category_id', 'server_id'), $request)) != '')
                return $response;

            $sub_category_advertise_server_id = $request->sub_category_advertise_server_id;
            $advertise_category_id = $request->advertise_category_id;
            $server_id = $request->server_id;

            DB::beginTransaction();
            DB::update('UPDATE sub_category_advertise_server_id_master SET advertise_category_id = ?, server_id = ? WHERE id = ?', [$advertise_category_id, $server_id, $sub_category_advertise_server_id]);
            DB::commit();

            $response = Response::json(array('code' => 200, 'message' => 'Advertise server id updated successfully.', 'cause' => '', 'data' => json_decode('{}')));
        } catch (Exception $e) {
            Log::error("updateAdvertiseServerId : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'update advertise server id.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /**
     * @api {post} deleteAdvertiseServerId   deleteAdvertiseServerId
     * @apiName deleteAdvertiseServerId
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     *  Key: Authorization
     *  Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "sub_category_advertise_server_id":1 //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Advertise server id deleted successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function deleteAdvertiseServerId(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('sub_category_advertise_server_id'), $request)) != '')
                return $response;

            $sub_category_advertise_server_id = $request->sub_category_advertise_server_id;

            DB::beginTransaction();
            DB::delete('DELETE FROM sub_category_advertise_server_id_master WHERE id = ?', [$sub_category_advertise_server_id]);
            DB::commit();

            $response = Response::json(array('code' => 200, 'message' => 'Advertise server id deleted successfully.', 'cause' => '', 'data' => json_decode('{}')));
        } catch (Exception $e) {
            Log::error("deleteAdvertiseServerId : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'delete advertise server id.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /**
     * @api {post} getAdvertiseServerIdForAdmin   getAdvertiseServerIdForAdmin
     * @apiName getAdvertiseServerIdForAdmin
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     *{
     * "sub_category_id":66 //compulsory
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
     * "android": [],
     * "ios": [
     * {
     * "sub_category_advertise_server_id": 1,
     * "advertise_category_id": 3,
     * "sub_category_id": 66,
     * "server_id": "Test Rewarded Video Ad Id 1",
     * "device_platform": 1,
     * "is_active": 1,
     * "create_time": "2018-07-18 09:09:22",
     * "update_time": "2018-07-18 09:09:22"
     * }
     * ]
     * },
     * {
     * "advertise_category_id": 1,
     * "advertise_category": "Banner",
     * "is_active": 1,
     * "create_time": "2018-07-16 09:06:47",
     * "update_time": "2018-07-16 09:06:47",
     * "android": [
     * {
     * "sub_category_advertise_server_id": 2,
     * "advertise_category_id": 1,
     * "sub_category_id": 66,
     * "server_id": "Test Banner Ad Id 1",
     * "device_platform": 2,
     * "is_active": 1,
     * "create_time": "2018-07-18 09:10:23",
     * "update_time": "2018-07-18 09:10:23"
     * }
     * ],
     * "ios": []
     * },
     * {
     * "advertise_category_id": 2,
     * "advertise_category": "Intertial",
     * "is_active": 1,
     * "create_time": "2018-07-16 09:06:47",
     * "update_time": "2018-07-16 09:06:47",
     * "android": [],
     * "ios": []
     * }
     * ]
     * }
     * }
     */
    public function getAdvertiseServerIdForAdmin(Request $request)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('sub_category_id'), $request)) != '')
                return $response;

            $this->sub_category_id = $request->sub_category_id;

            if (!Cache::has("pel:getAdvertiseServerIdForAdmin$this->sub_category_id")) {
                $result = Cache::rememberforever("getAdvertiseServerIdForAdmin$this->sub_category_id", function () {

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
                        $android_server_id = DB::select('SELECT
                                          id AS sub_category_advertise_server_id,
                                          advertise_category_id,
                                          sub_category_id,
                                          server_id,
                                          device_platform,
                                          is_active,
                                          create_time,
                                          update_time
                                        FROM
                                          sub_category_advertise_server_id_master
                                        WHERE
                                          sub_category_id = ? AND
                                          advertise_category_id = ? AND
                                          device_platform = 2 AND
                                          is_active=1
                                        order by update_time DESC', [$this->sub_category_id, $key->advertise_category_id]);

                        $ios_server_id = DB::select('SELECT
                                          id AS sub_category_advertise_server_id,
                                          advertise_category_id,
                                          sub_category_id,
                                          server_id,
                                          device_platform,
                                          is_active,
                                          create_time,
                                          update_time
                                        FROM
                                          sub_category_advertise_server_id_master
                                        WHERE
                                          sub_category_id = ? AND
                                          advertise_category_id = ? AND
                                          device_platform = 1 AND
                                          is_active=1
                                        order by update_time DESC', [$this->sub_category_id, $key->advertise_category_id]);

                        $key->android = $android_server_id;
                        $key->ios = $ios_server_id;
                    }
                    return $category;


                });

            }

            $redis_result = Cache::get("getAdvertiseServerIdForAdmin$this->sub_category_id");

            if (!$redis_result) {
                $redis_result = [];
            }

            $response = Response::json(array('code' => 200, 'message' => 'Advertise server id fetched successfully.', 'cause' => '', 'data' => ['result' => $redis_result]));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            Log::error("getAdvertiseServerIdForAdmin : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get advertise server id.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    /* =====================================| Update All Sample Images |==============================================*/

    /**
     * @api {post} updateAllSampleImages   updateAllSampleImages
     * @apiName updateAllSampleImages
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * request_data:{
     * "img_id": 356,
     * "is_free": 1,
     * "is_featured": 1,
     * "json_data": {
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
     * },
     * file:image1.jpeg
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Json data updated successfully!.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function updateAllSampleImages(Request $request)
    {

        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request->getContent());

            if (($response = (new VerificationController())->validateRequiredParameter(array('sub_category_id', 'item_count', 'page', 'no_of_times_update'), $request)) != '')
                return $response;

            $sub_category_id = $request->sub_category_id;
            $item_count = $request->item_count;
            $page = $request->page;
            $no_of_times_update = $request->no_of_times_update;
            $offset = ($page - 1) * $item_count;

            $total_sample_images = DB::select('SELECT i.*
                                    FROM images AS i,
                                        sub_category_catalog AS scc
                                    WHERE
                                    i.json_data IS NOT NULL AND
                                    i.json_data !="" AND
                                    i.catalog_id = scc.catalog_id AND
                                    scc.sub_category_id = ? AND scc.is_active = 1
                                    ORDER BY i.updated_at ASC', [$sub_category_id]);


            $sample_images = DB::select('SELECT i.*
                                    FROM images AS i,
                                        sub_category_catalog AS scc
                                    WHERE
                                    i.json_data IS NOT NULL AND
                                    i.json_data !="" AND
                                    i.catalog_id = scc.catalog_id AND
                                    scc.sub_category_id = ? AND scc.is_active = 1
                                    ORDER BY i.updated_at ASC LIMIT ?, ?', [$sub_category_id, $offset, $item_count]);


            $count = 0;
            $remaining_images = array();
            $updated_images = array();
            foreach ($sample_images as $key) {
                //Log::info('sample images : ',['image' => $key->image]);

                $file_name = (new ImageController())->saveOriginalImageFromToS3($key->image);

                if ($file_name != "") {
                    $dimension = (new ImageController())->saveThumbnailImageFromS3($key->image);
                    if ($dimension != "") {
                        if (Config::get('constant.STORAGE') === 'S3_BUCKET') {

                            (new ImageController())->saveNewWebpImageInToS3($file_name);

                            (new ImageController())->unlinkFile($key->image);
                        }
                        sleep(1);

                        DB::beginTransaction();
                        DB::update('UPDATE
                                images SET height = ?, width = ?, attribute1 = ?, attribute2 = ?
                                WHERE id = ?', [$dimension['height'], $dimension['width'], $file_name, $no_of_times_update, $key->id]);
                        DB::commit();
                        $count = $count + 1;
                        $updated_images[] = $key->image;
                    } else {
                        $remaining_images[] = $key->image;
                    }
                } else {
                    $remaining_images[] = $key->image;
                }
            }

            $result_array = array('total_updated_images' => count($total_sample_images), 'updated_images' => $updated_images, 'remaining_images' => $remaining_images);
            $result = json_decode(json_encode($result_array), true);

            $response = Response::json(array('code' => 200, 'message' => 'Sample images updated successfully.', 'cause' => '', 'data' => $result));

        } catch
        (Exception $e) {
            Log::error("updateAllSampleImages : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'update sample images.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /**
     * @api {post} createInvalidation   createInvalidation
     * @apiName createInvalidation
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * request_data:{
     * "img_id": 356,
     * "is_free": 1,
     * "is_featured": 1,
     * "json_data": {
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
     * },
     * file:image1.jpeg
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Json data updated successfully!.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function createInvalidation(Request $request)
    {

        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);


            /* This api used to create Invalidation for S3 to delete CDN caching of s3_bucket */
            $base_url = (new ImageController())->getBaseUrl();
            if ($request->hasFile('file')) {
                $file = Input::file('file');
                //$file_type = $file->getMimeType();

                if (($response = (new ImageController())->verifyImage($file)) != '')
                    return $response;

                $image = $file->getClientOriginalName();//(new ImageController())->generateNewFileName('test_webp_image', $file);

                (new ImageController())->saveOriginalImage($image);

                $original_sourceFile = $base_url . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY') . $image;

                //return array($original_sourceFile);
                $disk = Storage::disk('s3');
                $original_targetFile = "imageflyer/webp_original_new/" . $image;

                $disk->put($original_targetFile, file_get_contents($original_sourceFile), 'public');
                return $response = Response::json(array('code' => 200, 'message' => 'File uploaded successfully.', 'cause' => '', 'data' => "http://d2738jkpoo0kon.cloudfront.net/imageflyer/webp_original_new/" . $image));

            } else {
                return $response = Response::json(array('code' => 201, 'message' => 'Required field file is missing or empty.', 'cause' => '', 'data' => json_decode('{}')));
            }


            /*//$request = json_decode($request->getContent());
            $client = CloudFrontClient::factory(array(
                'credentials' => array(
                    'key' => 'AKIAIATYIJ5MPKANS7QA',
                    'secret' => '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',
                ),
                'region' => 'us-east-2',
                'version' => 'latest',
            ));

            $caller_reference = time();


            $result = $client->createInvalidation([
                'DistributionId' => 'E1CURG7RSP7L0C', // REQUIRED
                'InvalidationBatch' => [ // REQUIRED
                    'CallerReference' => $caller_reference, // REQUIRED
                    'Paths' => [ // REQUIRED
                        'Items' => ['http://d2738jkpoo0kon.cloudfront.net/imageflyer/webp_original_new/best_wallpapers_for_girl.jpg'],
                        'Quantity' => 1, // REQUIRED
                    ],
                ],
            ]);

            dd($result);*/


        } catch
        (Exception $e) {
            Log::error("createInvalidation : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'create invalidation.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /* ========================================= Tags =========================================*/

    /**
     * @api {post} addTag   addTag
     * @apiName addTag
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     *  Key: Authorization
     *  Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "tag_name":"Nature" //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Tag added successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function addTag(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('tag_name'), $request)) != '')
                return $response;

            $tag_name = trim($request->tag_name);
            $create_at = date('Y-m-d H:i:s');

            $result = DB::select('SELECT * FROM tag_master WHERE tag_name = ?', [$tag_name]);
            if (count($result) > 0) {
                return $response = Response::json(array('code' => 201, 'message' => 'Tag already exist.', 'cause' => '', 'data' => json_decode('{}')));
            }

            DB::beginTransaction();

            DB::insert('INSERT INTO tag_master (tag_name,is_active, create_time) VALUES(?, ?, ?)', [$tag_name, 1, $create_at]);

            DB::commit();

            $response = Response::json(array('code' => 200, 'message' => 'Tag added successfully.', 'cause' => '', 'data' => json_decode('{}')));
        } catch (Exception $e) {
            Log::error("addTag : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'add tag.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /**
     * @api {post} updateTag   updateTag
     * @apiName updateTag
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "tag_id":1, //compulsory
     * "tag_name":"Featured" //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Tag updated successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function updateTag(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('tag_id', 'tag_name'), $request)) != '')
                return $response;

            $tag_id = $request->tag_id;
            $tag_name = trim($request->tag_name);

            $result = DB::select('SELECT * FROM tag_master WHERE tag_name = ? AND id != ?', [$tag_name, $tag_id]);
            if (count($result) > 0) {
                return $response = Response::json(array('code' => 201, 'message' => 'Tag already exist.', 'cause' => '', 'data' => json_decode('{}')));
            }

            DB::beginTransaction();

            DB::update('UPDATE
                              tag_master
                            SET
                              tag_name = ?
                            WHERE
                              id = ? ',
                [$tag_name, $tag_id]);


            DB::commit();

            $response = Response::json(array('code' => 200, 'message' => 'Tag updated successfully.', 'cause' => '', 'data' => json_decode('{}')));

        } catch (Exception $e) {
            Log::error("updateTag : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'update tag.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }

        return $response;
    }

    /**
     * @api {post} deleteTag   deleteTag
     * @apiName deleteTag
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "tag_id":1 //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Tag deleted successfully!.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function deleteTag(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('tag_id'), $request)) != '')
                return $response;

            $tag_id = $request->tag_id;

            DB::beginTransaction();

            DB::delete('DELETE FROM tag_master where id = ? ', [$tag_id]);

            DB::commit();

            $response = Response::json(array('code' => 200, 'message' => 'Tag deleted successfully.', 'cause' => '', 'data' => json_decode('{}')));
        } catch (Exception $e) {
            Log::error("deleteTag : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'delete tag.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /**
     * @api {post} getAllTags   getAllTags
     * @apiName getAllTags
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
     * "message": "All tags fetched successfully.",
     * "cause": "",
     * "data": {
     * "total_record": 4,
     * "result": [
     * {
     * "tag_id": 1,
     * "tag_name": "test"
     * },
     * {
     * "tag_id": 2,
     * "tag_name": "Offer & Sales"
     * },
     * {
     * "tag_id": 3,
     * "tag_name": "Mobile Apps"
     * },
     * {
     * "tag_id": 4,
     * "tag_name": "Photography"
     * }
     * ]
     * }
     * }
     */
    public function getAllTags()
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            if (!Cache::has("pel:getAllTags")) {
                $result = Cache::rememberforever("getAllTags", function () {
                    return DB::select('SELECT
                                        id AS tag_id,
                                        tag_name
                                        FROM
                                        tag_master
                                        WHERE is_active = ? ORDER BY update_time DESC', [1]);
                });
            }

            $redis_result = Cache::get("getAllTags");

            if (!$redis_result) {
                $redis_result = [];
            }

            $response = Response::json(array('code' => 200, 'message' => 'All tags fetched successfully.', 'cause' => '', 'data' => ['total_record' => count($redis_result), 'result' => $redis_result]));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            Log::error("getAllTags : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get all tags.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    /* =========================================| Get Old Font (Non-commercial) |=========================================*/

    /**
     * @api {post} getSamplesOfNonCommercialFont   getSamplesOfNonCommercialFont
     * @apiName getSamplesOfNonCommercialFont
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     *  "catalog_id":1, //compulsory
     *  "order_by":1,
     *  "order_type":1
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Catalog Images Fetched Successfully.",
     * "cause": "",
     * "data": {
     * "image_list": [
     * {
     * "img_id": 360,
     * "thumbnail_img": "http://192.168.0.113/ob_photolab_backend/image_bucket/thumbnail/5a169952c71b0_catalog_image_1511430482.jpg",
     * "compressed_img": "http://192.168.0.113/ob_photolab_backend/image_bucket/compressed/5a169952c71b0_catalog_image_1511430482.jpg",
     * "original_img": "http://192.168.0.113/ob_photolab_backend/image_bucket/original/5a169952c71b0_catalog_image_1511430482.jpg",
     * "is_json_data": 0,
     * "json_data": "",
     * "is_featured": "",
     * "is_free": 0
     * },
     * {
     * "img_id": 359,
     * "thumbnail_img": "http://192.168.0.113/ob_photolab_backend/image_bucket/thumbnail/5a1697482f0a2_json_image_1511429960.jpg",
     * "compressed_img": "http://192.168.0.113/ob_photolab_backend/image_bucket/compressed/5a1697482f0a2_json_image_1511429960.jpg",
     * "original_img": "http://192.168.0.113/ob_photolab_backend/image_bucket/original/5a1697482f0a2_json_image_1511429960.jpg",
     * "is_json_data": 1,
     * "json_data": "test",
     * "is_featured": "0",
     * "is_free": 0
     * },
     * {
     * "img_id": 352,
     * "thumbnail_img": "http://192.168.0.113/ob_photolab_backend/image_bucket/thumbnail/5a0d7f290a6df_catalog_image_1510833961.jpg",
     * "compressed_img": "http://192.168.0.113/ob_photolab_backend/image_bucket/compressed/5a0d7f290a6df_catalog_image_1510833961.jpg",
     * "original_img": "http://192.168.0.113/ob_photolab_backend/image_bucket/original/5a0d7f290a6df_catalog_image_1510833961.jpg",
     * "is_json_data": 1,
     * "json_data": {
     * "text_json": [],
     * "sticker_json": [],
     * "image_sticker_json": [
     * {
     * "xPos": 440,
     * "yPos": 0,
     * "image_sticker_image": "",
     * "angle": 0,
     * "is_round": 0,
     * "height": 210,
     * "width": 210
     * },
     * {
     * "xPos": 0,
     * "yPos": 211,
     * "image_sticker_image": "",
     * "angle": 0,
     * "is_round": 0,
     * "height": 270,
     * "width": 430
     * },
     * {
     * "xPos": 353,
     * "yPos": 439,
     * "image_sticker_image": "",
     * "angle": 0,
     * "is_round": 0,
     * "height": 320,
     * "width": 297
     * }
     * ],
     * "frame_json": {
     * "frame_image": "frame_1.6.png"
     * },
     * "background_json": {},
     * "sample_image": "sample_1.6.jpg",
     * "height": 800,
     * "width": 650,
     * "is_featured": 0
     * },
     * "is_featured": "0",
     * "is_free": 1
     * },
     * {
     * "img_id": 355,
     * "thumbnail_img": "http://192.168.0.113/ob_photolab_backend/image_bucket/thumbnail/5a0d7faa3b1bc_catalog_image_1510834090.jpg",
     * "compressed_img": "http://192.168.0.113/ob_photolab_backend/image_bucket/compressed/5a0d7faa3b1bc_catalog_image_1510834090.jpg",
     * "original_img": "http://192.168.0.113/ob_photolab_backend/image_bucket/original/5a0d7faa3b1bc_catalog_image_1510834090.jpg",
     * "is_json_data": 1,
     * "json_data": {
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
     * "frame_image": "frame_15.7.png"
     * },
     * "background_json": {},
     * "sample_image": "sample_15.7.jpg",
     * "is_featured": 0,
     * "height": 800,
     * "width": 800
     * },
     * "is_featured": "1",
     * "is_free": 1
     * }
     * ]
     * }
     * }
     */
    public function getSamplesOfNonCommercialFont()
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            /*define follwoing variables into constant before use this API

            'NON_COMMERCIAL_FONT_PATH' => "fonts/American Typewriter Condensed.ttf,fonts/style10.ttf,fonts/Blanch Condensed Inline.ttf,fonts/CoronetLTStd-Bold.ttf,fonts/daunpenh.ttf,fonts/Filxgirl.TTF,fonts/LFAX.TTF,fonts/LFAXI.TTF,fonts/ufonts.com_lydian-cursive-bt.ttf,fonts/Medusa Gothic.otf,fonts/PrestigeEliteStd-Bd.otf,fonts/VAGRoundedStd-Bold.ttf,fonts/VAGRoundedStd-Light.ttf",
            'NON_COMMERCIAL_FONT_NAME' => "AmericanTypewriter-Condensed,BacktoBlackDemo,Blanch-CondensedInline,CoronetLTStd-Bold,DaunPenh,FiolexGirls-Regular,LucidaFax,LucidaFax-Italic,LydianCursiveBT-Regular,MedusaGothic,PrestigeEliteStd-Bd,VAGRoundedStd-Bold,VAGRoundedStd-Light"

            */


            $non_commercial_fonts_android = explode(",", Config::get('constant.NON_COMMERCIAL_FONT_PATH')); //get non-commercial fonts using fontPath
            $non_commercial_fonts_ios = explode(",", Config::get('constant.NON_COMMERCIAL_FONT_NAME')); //get non-commercial fonts using fontName

            $json_list_of_android = array(); //array of json which contains non-commercial fonts (android)
            $json_list_of_ios = array(); //array of json which contains non-commercial fonts (ios)
            $json_list_of_android_new = array();
            foreach ($non_commercial_fonts_android as $key) {

                $list_of_android_match = DB::select('SELECT
                                    DISTINCT id
                                  FROM images
                                  WHERE
                                    JSON_SEARCH(json_data,"all",?, NULL , "$.text_json[*].fontPath") IS NOT NULL', [$key]);


                foreach ($list_of_android_match as $id) {
                    //$json_list_of_android[] = $id;
                    $json_list_of_android[] = $id;

                }


            }
            //return $json_list_of_android;

            foreach ($non_commercial_fonts_ios as $key) {

                $list_of_ios_match = DB::select('SELECT
                                    DISTINCT id
                                  FROM images
                                  WHERE
                                    JSON_SEARCH(json_data,"all",?, NULL , "$.text_json[*].fontName") IS NOT NULL', [$key]);

                /*$list_of_ios_match = DB::select('SELECT
                                    DISTINCT id,
                                    IF(image != "",CONCAT("' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",image),"") as original_img
                                  FROM images
                                  WHERE
                                    JSON_SEARCH(json_data,"all",?, NULL , "$.text_json[*].fontName") IS NOT NULL', [$key]);*/

                foreach ($list_of_ios_match as $id) {
                    //$json_list_of_ios[] = $id->img_ids;
                    if (!in_array($id, $json_list_of_android)) {
                        $json_list_of_ios[] = $id;
                    }

                }

            }

            //return $json_list_of_ios;

            //return array_merge($json_list_of_android, $json_list_of_ios);

            $result = array_merge($json_list_of_android, $json_list_of_ios);
            $not_existed_id = array_unique($result);


            $response = Response::json(array('code' => 200, 'message' => 'Fonts fetched successfully.', 'cause' => '', 'data' => ['result' => $not_existed_id]));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            Log::error("getSamplesOfNonCommercialFont : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get fonts.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    /* =========================================| Font Module |=========================================*/

    /**
     * @api {post} addFont   addFont
     * @apiName addFont
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * request_data:{
     * "catalog_id":280, //compulsory
     * "ios_font_name":"3d", //optional
     * "is_replace":1 //compulsory 1=replace font file, 0=don't replace font file
     * }
     * file:3d.ttf //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Font added successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function addFont(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            if (!$request_body->has('request_data'))
                return Response::json(array('code' => 201, 'message' => 'Required field request_data is missing or empty.', 'cause' => '', 'data' => json_decode("{}")));

            $request = json_decode($request_body->input('request_data'));
            if (($response = (new VerificationController())->validateRequiredParameter(array(
                    'catalog_id',
                    'is_replace'
                ), $request)) != ''
            )
                return $response;

            $catalog_id = $request->catalog_id;
            //$ios_font_name = $request->ios_font_name;
            //$android_font_name = $request->android_font_name;
            $is_replace = $request->is_replace;
            $create_at = date('Y-m-d H:i:s');
            DB::beginTransaction();
            if (!$request_body->hasFile('file')) {
                return Response::json(array('code' => 201, 'message' => 'Required field file is missing or empty.', 'cause' => '', 'data' => json_decode("{}")));
            } else {

                $file_array = Input::file('file');

                if (($response = (new ImageController())->verifyFontFile($file_array)) != '')
                    return $response;

                if ($is_replace == 0) {
                    if (($response = (new VerificationController())->checkIsFontExist($file_array)) != '')
                        return $response;
                    $file_name = str_replace(" ", "", strtolower($file_array->getClientOriginalName()));
                } else {
                    $file_name = $file_array->getClientOriginalName();
                }

                //$file_name = $file_array->getClientOriginalName();
                $font_name = (new ImageController())->saveFontFile($file_name, $is_replace);

                if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
                    (new ImageController())->saveFontInToS3($file_name);
                }

                $android_font_name = "fonts/$file_name";
                //$ios_font_name = $file_name;
                $ios_font_name = isset($request->ios_font_name) ? $request->ios_font_name : $file_name;

                DB::insert('INSERT
                                INTO
                                  font_master(catalog_id, font_name, font_file, ios_font_name, android_font_name, is_active, create_time)
                                VALUES(?, ?, ?, ?, ?, ?, ?) ', [$catalog_id, $font_name, $file_name, $ios_font_name, $android_font_name, 1, $create_at]);

            }
            DB::commit();

            $response = Response::json(array('code' => 200, 'message' => 'Font added successfully.', 'cause' => '', 'data' => json_decode('{}')));
        } catch (Exception $e) {
            Log::error("addFont : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'add font.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /**
     * @api {post} editFont   editFont
     * @apiName editFont
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "font_id":1, //compulsory
     * "ios_font_name":"3d", //optional
     * "android_font_name":"3d.ttf" //optional
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Font edited successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function editFont(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('font_id'), $request)) != '')
                return $response;

            $font_id = $request->font_id;
            $ios_font_name = isset($request->ios_font_name) ? $request->ios_font_name : '';
            $android_font_name = isset($request->android_font_name) ? $request->android_font_name : '';

            DB::beginTransaction();
            /*if ($request_body->hasFile('file')) {

                $file_array = Input::file('file');
                if (($response = (new ImageController())->verifyFontFile($file_array)) != '')
                    return $response;

                if (($response = (new VerificationController())->checkIsFontExist($file_array)) != '')
                    return $response;

                //$file_name = (new ImageController())->generateNewFileName('font_file', $image_array);
                $file_name = $file_array->getClientOriginalName();
                (new ImageController())->saveFontFile($file_name, 1);

                if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
                    (new ImageController())->saveImageInToS3($file_array);
                }

                DB::update('UPDATE font_master AS fm SET
                                font_file = ?,
                                fm.ios_font_name = IF(? != "",?,fm.ios_font_name),
                                fm.android_font_name = IF(? != "",?,fm.android_font_name)
                              WHERE fm.id = ?', [$ios_font_name, $ios_font_name, $android_font_name, $android_font_name, $file_name, $font_id]);


            } else {*/
            DB::update('UPDATE font_master AS fm SET
                                fm.ios_font_name = IF(? != "",?,fm.ios_font_name),
                                fm.android_font_name = IF(? != "",?,fm.android_font_name)
                              WHERE fm.id = ?', [$ios_font_name, $ios_font_name, $android_font_name, $android_font_name, $font_id]);
            /*}*/
            DB::commit();

            $response = Response::json(array('code' => 200, 'message' => 'Font edited successfully.', 'cause' => '', 'data' => json_decode('{}')));
        } catch (Exception $e) {
            Log::error("editFont : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'edit font.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /**
     * @api {post} deleteFont   deleteFont
     * @apiName deleteFont
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "font_id":1 //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Font deleted successfully!.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function deleteFont(Request $request)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request->getContent());
            //Log::info("request data of deleteFont :", [$request]);
            if (($response = (new VerificationController())->validateRequiredParameter(array('font_id'), $request)) != '')
                return $response;

            $font_id = $request->font_id;

            DB::beginTransaction();

            DB::delete('DELETE FROM font_master where id = ? ', [$font_id]);

            DB::commit();

            $response = Response::json(array('code' => 200, 'message' => 'Font deleted successfully.', 'cause' => '', 'data' => json_decode('{}')));
        } catch (Exception $e) {
            Log::error("deleteFont : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'delete font.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /**
     * @api {post} getAllFontsByCatalogIdForAdmin   getAllFontsByCatalogIdForAdmin
     * @apiName getAllFontsByCatalogIdForAdmin
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     *  "catalog_id":1, //compulsory
     *  "order_by":1, //optional
     *  "order_type":1 //optional
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Fonts fetched successfully.",
     * "cause": "",
     * "data": {
     * "total_count": 10,
     * "result": [
     * {
     * "font_id": 94,
     * "font_name": "Baloo Thambi Regular",
     * "font_file": "http://192.168.0.113/photo_editor_lab_backend/image_bucket/fonts/baloo_thambi_regular.ttf",
     * "ios_font_name": "Baloo Thambi Regular",
     * "android_font_name": "fonts/baloo_thambi_regular.ttf",
     * "is_active": 1
     * },
     * {
     * "font_id": 93,
     * "font_name": "Baloo Tammudu Regular",
     * "font_file": "http://192.168.0.113/photo_editor_lab_backend/image_bucket/fonts/baloo_tammudu_regular.ttf",
     * "ios_font_name": "Baloo Tammudu Regular",
     * "android_font_name": "fonts/baloo_tammudu_regular.ttf",
     * "is_active": 1
     * }
     * ]
     * }
     * }
     */
    public function getAllFontsByCatalogIdForAdmin(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('catalog_id'), $request)) != '')
                return $response;

            $this->catalog_id = $request->catalog_id;
            $this->order_by = isset($request->order_by) ? $request->order_by : 'update_time'; //field name
            $this->order_type = strtolower(isset($request->order_type) ? $request->order_type : 'DESC'); //asc or desc

            if (!Cache::has("pel:getAllFontsByCatalogIdForAdmin$this->catalog_id:$this->order_by:$this->order_type")) {
                $result = Cache::rememberforever("getAllFontsByCatalogIdForAdmin$this->catalog_id:$this->order_by:$this->order_type", function () {

                    $result = DB::select('SELECT
                                              fm.id as font_id,
                                              fm.font_name,
                                              IF(fm.font_file != "",CONCAT("' . Config::get('constant.FONT_FILE_DIRECTORY_OF_DIGITAL_OCEAN') . '",fm.font_file),"") as font_file,
                                              coalesce(fm.ios_font_name,"") as ios_font_name,
                                              coalesce(fm.android_font_name,"") as android_font_name,
                                              fm.is_active
                                            FROM
                                              font_master as fm
                                            where
                                              fm.is_active = 1 AND
                                              fm.catalog_id = ?
                                              ORDER BY fm.' . $this->order_by . ' ' . $this->order_type, [$this->catalog_id]);

                    return $result;
                });
            }
            $redis_result = Cache::get("getAllFontsByCatalogIdForAdmin$this->catalog_id:$this->order_by:$this->order_type");

            if (!$redis_result) {
                $redis_result = [];
            }


            $response = Response::json(array('code' => 200, 'message' => 'Fonts fetched successfully.', 'cause' => '', 'data' => ['total_count' => count($redis_result), 'result' => $redis_result]));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            Log::error("getAllFontsByCatalogIdForAdmin : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get fonts.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    /**
     * @api {post} getAllFonts   getAllFonts
     * @apiName getAllFonts
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     *  "catalog_id":1, //compulsory
     *  "order_by":1, //optional
     *  "order_type":1 //optional
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Fonts fetched successfully.",
     * "cause": "",
     * "data": {
     * "total_count": 10,
     * "result": [
     * {
     * "font_id": 94,
     * "font_name": "Baloo Thambi Regular",
     * "font_file": "http://192.168.0.113/photo_editor_lab_backend/image_bucket/fonts/baloo_thambi_regular.ttf",
     * "ios_font_name": "Baloo Thambi Regular",
     * "android_font_name": "fonts/baloo_thambi_regular.ttf",
     * "is_active": 1
     * },
     * {
     * "font_id": 93,
     * "font_name": "Baloo Tammudu Regular",
     * "font_file": "http://192.168.0.113/photo_editor_lab_backend/image_bucket/fonts/baloo_tammudu_regular.ttf",
     * "ios_font_name": "Baloo Tammudu Regular",
     * "android_font_name": "fonts/baloo_tammudu_regular.ttf",
     * "is_active": 1
     * }
     * ]
     * }
     * }
     */
    public function getAllFonts(Request $request_body)
    {
        try {
            /*$token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('sub_category_id'), $request)) != '')
                return $response;

            $this->sub_category_id = $request->sub_category_id;*/

            if (!Cache::has("pel:getAllFonts")) {
                $result = Cache::rememberforever("getAllFonts", function () {

                    $result = DB::select('SELECT
                                              fm.id as font_id,
                                              fm.catalog_id,
                                              cm.name,
                                              fm.font_name,
                                              fm.font_file,
                                              coalesce(fm.ios_font_name,"") as ios_font_name,
                                              coalesce(fm.android_font_name,"") as android_font_name,
                                              fm.is_active
                                            FROM
                                              catalog_master AS cm LEFT JOIN
                                              font_master as fm ON cm.id = fm.catalog_id
                                            where
                                              fm.is_active = 1
                                            ORDER BY cm.name');

                    return $result;
                });
            }
            $redis_result = Cache::get("getAllFonts");

            if (!$redis_result) {
                $redis_result = [];
            }


            $response = Response::json(array('code' => 200, 'message' => 'Fonts fetched successfully.', 'cause' => '', 'data' => ['total_count' => count($redis_result), 'result' => $redis_result]));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            Log::error("getAllFonts : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get fonts.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    /* =========================================| Statistics Module |=========================================*/

    /**
     * @api {post} addServerUrl   addServerUrl
     * @apiName addServerUrl
     * @apiGroup Statistics (admin)
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     *  Key: Authorization
     *  Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "server_url":"http://192.168.0.113/photo_editor_lab_backend" //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Server url added successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function addServerUrl(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('server_url'), $request)) != '')
                return $response;

            $server_url = trim($request->server_url);
            $create_at = date('Y-m-d H:i:s');

            $result = DB::select('SELECT * FROM server_url_master WHERE server_url = ?', [$server_url]);
            if (count($result) > 0) {
                return $response = Response::json(array('code' => 201, 'message' => 'URL already exist.', 'cause' => '', 'data' => json_decode('{}')));
            }

            $api_url = "$server_url/api/public/api/";

            if ((filter_var($server_url, FILTER_VALIDATE_URL))) {
                DB::beginTransaction();

                DB::insert('INSERT INTO server_url_master (server_url, api_url, is_active, create_time)  VALUES(?, ?, ?, ?)', [$server_url, $api_url, 1, $create_at]);

                DB::commit();
            } else {
                return $response = Response::json(array('code' => 201, 'message' => 'Invalid server url.', 'cause' => '', 'data' => json_decode('{}')));

            }


            $response = Response::json(array('code' => 200, 'message' => 'Server url added successfully.', 'cause' => '', 'data' => json_decode('{}')));
        } catch (Exception $e) {
            Log::error("addServerUrl : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'add server url.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /**
     * @api {post} updateServerUrl   updateServerUrl
     * @apiName updateServerUrl
     * @apiGroup Statistics (admin)
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "server_url_id":1, //compulsory
     * "server_url":"http://192.168.0.113/photo_editor_lab_backend" //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Server url updated successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function updateServerUrl(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('server_url_id', 'server_url'), $request)) != '')
                return $response;

            $server_url_id = $request->server_url_id;
            $server_url = trim($request->server_url);

            $result = DB::select('SELECT * FROM server_url_master WHERE server_url = ? AND id != ?', [$server_url, $server_url_id]);
            if (count($result) > 0) {
                return $response = Response::json(array('code' => 201, 'message' => 'URL already exist.', 'cause' => '', 'data' => json_decode('{}')));
            }

            $api_url = "$server_url/api/public/api/";

            DB::beginTransaction();

            DB::update('UPDATE
                              server_url_master
                            SET
                              server_url = ?,
                              api_url = ?
                            WHERE
                              id = ? ',
                [$server_url, $api_url, $server_url_id]);


            DB::commit();

            $response = Response::json(array('code' => 200, 'message' => 'Server url updated successfully.', 'cause' => '', 'data' => json_decode('{}')));

        } catch (Exception $e) {
            Log::error("updateServerUrl : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'update server url.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }

        return $response;
    }

    /**
     * @api {post} deleteServerUrl   deleteServerUrl
     * @apiName deleteServerUrl
     * @apiGroup Statistics (admin)
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "server_url_id":1 //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "URL deleted successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function deleteServerUrl(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('server_url_id'), $request)) != '')
                return $response;

            $server_url_id = $request->server_url_id;

            DB::beginTransaction();

            DB::delete('DELETE FROM server_url_master where id = ? ', [$server_url_id]);

            DB::commit();

            $response = Response::json(array('code' => 200, 'message' => 'URL deleted successfully.', 'cause' => '', 'data' => json_decode('{}')));
        } catch (Exception $e) {
            Log::error("deleteServerUrl : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'delete server url.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /**
     * @api {post} getAllServerUrls   getAllServerUrls
     * @apiName getAllServerUrls
     * @apiGroup Statistics (admin)
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
     * "message": "All urls fetched successfully.",
     * "cause": "",
     * "data": {
     * "total_record": 2,
     * "result": [
     * {
     * "server_url_id": 1,
     * "server_url": "http://localhost/photo_editor_lab_backend",
     * "api_url": "http://localhost/photo_editor_lab_backend/api/public/api/"
     * },
     * {
     * "server_url_id": 2,
     * "server_url": "http://192.168.0.113/photo_editor_lab_backend_v1",
     * "api_url": "http://192.168.0.113/photo_editor_lab_backend_v1/api/public/api/"
     * }
     * ]
     * }
     * }
     */
    public function getAllServerUrls()
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            if (!Cache::has("pel:getAllServerUrls")) {
                $result = Cache::rememberforever("getAllServerUrls", function () {
                    return DB::select('SELECT
                                        id AS server_url_id,
                                        server_url,
                                        api_url
                                        FROM
                                        server_url_master
                                        WHERE is_active = ? ORDER BY update_time DESC', [1]);
                });
            }

            $redis_result = Cache::get("getAllServerUrls");

            if (!$redis_result) {
                $redis_result = [];
            }

            $response = Response::json(array('code' => 200, 'message' => 'All urls fetched successfully.', 'cause' => '', 'data' => ['total_record' => count($redis_result), 'result' => $redis_result]));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            Log::error("getAllServerUrls : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get all urls.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    /**
     * @api {post} getSummaryByAdmin   getSummaryByAdmin
     * @apiName getSummaryByAdmin
     * @apiGroup Statistics (admin)
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * }
     * @apiSuccessExample Request-Body:
     * {
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Summary fetched successfully.",
     * "cause": "",
     * "data": {
     * "total_record": 33,
     * "result": [
     * {
     * "sub_category_id": 20,
     * "category_id": 2,
     * "name": "Independence Day Stickers",
     * "thumbnail_img": "http://192.168.0.113/photo_editor_lab_backend/image_bucket/thumbnail/598d56c20e5bf_sub_category_img_1502435010.png",
     * "compressed_img": "http://192.168.0.113/photo_editor_lab_backend/image_bucket/compressed/598d56c20e5bf_sub_category_img_1502435010.png",
     * "original_img": "http://192.168.0.113/photo_editor_lab_backend/image_bucket/original/598d56c20e5bf_sub_category_img_1502435010.png",
     * "no_of_catalogs": 7,
     * "content_count": 81,
     * "free_content": 6,
     * "paid_content": 75,
     * "is_featured": 10,
     * "last_uploaded_date": "2018-03-10 07:02:54",
     * "is_active": 1,
     * "last_uploaded_count": 6
     * },
     * {
     * "sub_category_id": 28,
     * "category_id": 2,
     * "name": "Selfie With Ganesha Stickers",
     * "thumbnail_img": "http://192.168.0.113/photo_editor_lab_backend/image_bucket/thumbnail/59957acc474a9_category_img_1502968524.png",
     * "compressed_img": "http://192.168.0.113/photo_editor_lab_backend/image_bucket/compressed/59957acc474a9_category_img_1502968524.png",
     * "original_img": "http://192.168.0.113/photo_editor_lab_backend/image_bucket/original/59957acc474a9_category_img_1502968524.png",
     * "no_of_catalogs": 5,
     * "content_count": 9,
     * "free_content": 0,
     * "paid_content": 9,
     * "is_featured": 10,
     * "last_uploaded_date": "2017-08-18 05:18:33",
     * "is_active": 1,
     * "last_uploaded_count": 5
     * }
     * ]
     * }
     * }
     */
    public function getSummaryByAdmin()
    {
        try {
            /*$token = JWTAuth::getToken();
            JWTAuth::toUser($token);*/

            $result = DB::select('SELECT
                                      DISTINCT scm.id AS sub_category_id,
                                      scm.category_id,
                                      scm.name,
                                      IF(scm.image != "",CONCAT("' . Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",scm.image),"") as thumbnail_img,
                                      IF(scm.image != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",scm.image),"") as compressed_img,
                                      IF(scm.image != "",CONCAT("' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",scm.image),"") as original_img,
                                      count(DISTINCT ctm.id) AS no_of_catalogs,
                                      count(cm.id) AS content_count,
                                      count(IF(cm.is_free=1,1, NULL)) AS free_content,
                                      count(IF(cm.is_free=0,1, NULL)) AS paid_content,
                                      count(IF(cm.is_featured=1,1, NULL)) AS is_featured,
                                      coalesce((max(cm.created_at)),"") AS last_uploaded_date,
                                      scm.is_featured,
                                      scm.is_active
                                    FROM
                                      sub_category AS scm LEFT JOIN sub_category_catalog AS scc
                                      LEFT JOIN catalog_master AS ctm ON ctm.id = scc.catalog_id AND ctm.is_featured = 1 AND ctm.is_active = 1
                                      LEFT JOIN images AS cm
                                        ON cm.catalog_id = scc.catalog_id AND cm.is_active = 1 AND (cm.json_data IS NOT NULL OR cm.json_data!="")
                                        ON scm.id = scc.sub_category_id AND scc.is_active = 1
                                    GROUP BY scm.id HAVING scm.is_active = 1 AND scm.category_id = ? AND scm.is_featured = 1 ORDER BY last_uploaded_date DESC', [2]);

            foreach ($result as $key) {
                $last_uploaded_count = DB::select('SELECT
                                      DISTINCT scm.id AS sub_category_id,
                                      scm.category_id,
                                      count(IF(DATE(cm.created_at)=date(?),1,NULL)) AS last_uploaded_count,
                                      scm.is_active
                                    FROM
                                      sub_category AS scm LEFT JOIN sub_category_catalog AS scc
                                      LEFT JOIN images AS cm
                                        ON cm.catalog_id = scc.catalog_id AND cm.is_active = 1
                                        ON scm.id = scc.sub_category_id AND scc.is_active = 1
                                    GROUP BY scm.id HAVING scm.is_active = 1 AND scm.category_id = ? AND scm.id = ?
                                    ORDER BY scm.category_id DESC', [$key->last_uploaded_date, $key->category_id, $key->sub_category_id]);

                $key->last_uploaded_count = $last_uploaded_count[0]->last_uploaded_count;
            }

            $response = Response::json(array('code' => 200, 'message' => 'Summary fetched successfully.', 'cause' => '', 'data' => ['total_record' => count($result), 'result' => $result]));

        } catch (Exception $e) {
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get summary.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            Log::error("getSummaryByAdmin : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
        }
        return $response;
    }

    /**
     * @api {post} getSummaryOfAllServersByAdmin   getSummaryOfAllServersByAdmin
     * @apiName getSummaryOfAllServersByAdmin
     * @apiGroup Statistics (admin)
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
     * "message": "Summary fetched successfully.",
     * "cause": "",
     * "data": {
     * "total_record": 2,
     * "summary_of_all_servers": [
     * {
     * "total_record": 33,
     * "result": [
     * {
     * "sub_category_id": 20,
     * "category_id": 2,
     * "name": "Independence Day Stickers",
     * "thumbnail_img": "http://192.168.0.113/photo_editor_lab_backend/image_bucket/thumbnail/598d56c20e5bf_sub_category_img_1502435010.png",
     * "compressed_img": "http://192.168.0.113/photo_editor_lab_backend/image_bucket/compressed/598d56c20e5bf_sub_category_img_1502435010.png",
     * "original_img": "http://192.168.0.113/photo_editor_lab_backend/image_bucket/original/598d56c20e5bf_sub_category_img_1502435010.png",
     * "no_of_catalogs": 7,
     * "content_count": 81,
     * "free_content": 6,
     * "paid_content": 75,
     * "last_uploaded_date": "2018-03-10 07:02:54",
     * "is_active": 1,
     * "last_uploaded_count": 6
     * }
     * ],
     * "server_url": "localhost",
     * "api_url": "http://localhost/photo_editor_lab_backend/api/public/api/"
     * },
     * {
     * "total_record": 33,
     * "result": [
     * {
     * "sub_category_id": 95,
     * "category_id": 2,
     * "name": "Test",
     * "thumbnail_img": "http://192.168.0.113/photo_editor_lab_backend/image_bucket/thumbnail/5c4ac74046e7a_sub_category_img_1548404544.jpg",
     * "compressed_img": "http://192.168.0.113/photo_editor_lab_backend/image_bucket/compressed/5c4ac74046e7a_sub_category_img_1548404544.jpg",
     * "original_img": "http://192.168.0.113/photo_editor_lab_backend/image_bucket/original/5c4ac74046e7a_sub_category_img_1548404544.jpg",
     * "no_of_catalogs": 2,
     * "content_count": 19,
     * "free_content": 0,
     * "paid_content": 19,
     * "last_uploaded_date": "2019-01-25 09:42:17",
     * "is_active": 1,
     * "last_uploaded_count": 19
     * }
     * ],
     * "server_url": "192.168.0.113",
     * "api_url": "http://192.168.0.113/photo_editor_lab_backend/api/public/api/"
     * }
     * ]
     * }
     * }
     */
    public function getSummaryOfAllServersByAdmin()
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $server_list = DB::select('SELECT
                                        api_url
                                        FROM
                                        server_url_master
                                        WHERE is_active = ? ORDER BY update_time DESC', [1]);

            $all_server_list = array();
            $i = 0; // used for array indexing
            foreach ($server_list as $key) {
                $client = new Client();
                $api_url = $key->api_url . "getSummaryByAdmin";

                if ((filter_var($api_url, FILTER_VALIDATE_URL))) {
                    Log::debug("getSummaryOfAllServersByAdmin CURL : ", ["api_url" => $api_url]);
                    $output = $client->post($api_url); //$key is a url of api
                    $data = json_decode($output->getBody()->getContents(), true);
                    $data['data']['server_url'] = parse_url($api_url, PHP_URL_HOST);
                    $data['data']['api_url'] = str_replace("getSummaryByAdmin", "", $api_url);//parse_url($key, PHP_URL_HOST);
                    $all_server_list[$i] = $data['data'];
                    $i++;

                } else {
                    Log::debug("getSummaryOfAllServersByAdmin error in CURL : ", ["api_url" => $api_url]);
                }


            }

            $response = Response::json(array('code' => 200, 'message' => 'Summary fetched successfully.', 'cause' => '', 'data' => ['total_record' => count($all_server_list), 'summary_of_all_servers' => $all_server_list]));

        } catch (Exception $e) {
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get summary of all servers.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            Log::error("getSummaryOfAllServersByAdmin : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
        }
        return $response;
    }

    /**
     * @api {post} getSummaryOfIndividualServerByAdmin   getSummaryOfIndividualServerByAdmin
     * @apiName getSummaryOfIndividualServerByAdmin
     * @apiGroup Statistics (admin)
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "api_url":"http://localhost/photo_editor_lab_backend/api/public/api/" //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Summary fetched successfully.",
     * "cause": "",
     * "data": {
     * "total_record": 4,
     * "result": [
     * {
     * "sub_category_id": 66,
     * "category_id": 2,
     * "name": "All Templates",
     * "thumbnail_img": "http://192.168.0.113/photo_editor_lab_backend/image_bucket/thumbnail/5c85fb452c3d4_sub_category_img_1552284485.jpg",
     * "compressed_img": "http://192.168.0.113/photo_editor_lab_backend/image_bucket/compressed/5c85fb452c3d4_sub_category_img_1552284485.jpg",
     * "original_img": "http://192.168.0.113/photo_editor_lab_backend/image_bucket/original/5c85fb452c3d4_sub_category_img_1552284485.jpg",
     * "no_of_catalogs": 7,
     * "content_count": 650,
     * "free_content": 650,
     * "paid_content": 0,
     * "is_featured": 1,
     * "last_uploaded_date": "2019-03-25 12:22:22",
     * "is_active": 1,
     * "last_uploaded_count": 70
     * }
     * ]
     * }
     * }
     */
    public function getSummaryOfIndividualServerByAdmin(Request $request)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('api_url'), $request)) != '')
                return $response;

            $api_url = $request->api_url . "getSummaryByAdmin";

            $client = new Client();
            $output = $client->post($api_url);
            $data = json_decode($output->getBody()->getContents(), true);

            $response = Response::json(array('code' => 200, 'message' => 'Summary fetched successfully.', 'cause' => '', 'data' => $data['data']));

        } catch (Exception $e) {
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get summary of individual servers.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            Log::error("getSummaryOfIndividualServerByAdmin : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
        }
        return $response;
    }

    /**
     * @api {post} getSummaryDetailFromDiffServer   getSummaryDetailFromDiffServer
     * @apiName getSummaryDetailFromDiffServer
     * @apiGroup Statistics (admin)
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "api_url":"http://192.168.0.113/photo_editor_lab_backend_v1/api/public/api/", //compulsory
     * "category_id":2, //compulsory
     * "sub_category_id":66, //compulsory
     * "from_date":"2018-01-01", //compulsory
     * "to_date":"2019-05-06", //compulsory
     * "page":2, //compulsory
     * "item_count":2, //compulsory
     * "order_by":"date",
     * "order_type":"desc"
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Summary details fetched successfully.",
     * "cause": "",
     * "data": {
     * "code": 200,
     * "message": "Summary fetched successfully.",
     * "cause": "",
     * "data": {
     * "total_record": 59,
     * "is_next_page": true,
     * "result": [
     * {
     * "date": "2018-09-11",
     * "uploaded_content_count": 1
     * },
     * {
     * "date": "2018-09-07",
     * "uploaded_content_count": 2
     * }
     * ]
     * }
     * }
     * }
     */
    public function getSummaryDetailFromDiffServer(Request $request)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('api_url', 'category_id', 'sub_category_id', 'from_date', 'to_date', 'page', 'item_count'), $request)) != '')
                return $response;

            $api_url = $request->api_url . "getSummaryByDateRange";
            $category_id = $request->category_id;
            $sub_category_id = $request->sub_category_id;
            $from_date = $request->from_date;
            $to_date = $request->to_date;
            $page = $request->page;
            $item_count = $request->item_count;
            $order_by = isset($request->order_by) ? $request->order_by : 'date'; //field name
            $order_type = isset($request->order_type) ? $request->order_type : 'DESC'; //asc or desc

            $request_body = array(
                'json' => array(
                    'category_id' => $category_id,
                    'sub_category_id' => $sub_category_id,
                    'from_date' => $from_date,
                    'to_date' => $to_date,
                    'page' => $page,
                    'item_count' => $item_count,
                    'order_by' => $order_by,
                    'order_type' => $order_type,
                )
            );

            $client = new Client();
            $output = $client->post($api_url, $request_body);
            $data = json_decode($output->getBody()->getContents(), true);

            $response = Response::json(array('code' => 200, 'message' => 'Summary details fetched successfully.', 'cause' => '', 'data' => $data));

        } catch (Exception $e) {
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get summary details.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            Log::error("getSummaryDetailFromDiffServer : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
        }
        return $response;
    }

    /**
     * @api {post} getSummaryByDateRange   getSummaryByDateRange
     * @apiName getSummaryByDateRange
     * @apiGroup Statistics (admin)
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "category_id":2, //compulsory
     * "sub_category_id":66, //compulsory
     * "from_date":"2018-01-01", //compulsory yy-mm-dd
     * "to_date":"2019-05-06", //compulsory
     * "page":1, //compulsory
     * "item_count":10, //compulsory
     * "order_by":"date",
     * "order_type":"desc"
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Summary fetched successfully.",
     * "cause": "",
     * "data": {
     * "total_record": 59,
     * "is_next_page": true,
     * "result": [
     * {
     * "date": "2018-01-09",
     * "uploaded_content_count": 86
     * },
     * {
     * "date": "2018-03-16",
     * "uploaded_content_count": 50
     * }
     * ]
     * }
     * }
     */
    public function getSummaryByDateRange(Request $request)
    {
        try {
            /*$token = JWTAuth::getToken();
            JWTAuth::toUser($token);*/

            $request = json_decode($request->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('category_id', 'sub_category_id', 'from_date', 'to_date', 'page', 'item_count'), $request)) != '')
                return $response;

            $category_id = $request->category_id;
            $sub_category_id = $request->sub_category_id;
            $from_date = $request->from_date;
            $to_date = $request->to_date;
            $page = $request->page;
            $item_count = $request->item_count;
            $offset = ($page - 1) * $item_count;
            $order_by = isset($request->order_by) ? $request->order_by : 'date'; //field name
            $order_type = isset($request->order_type) ? $request->order_type : 'DESC'; //asc or desc

            $total_row_result = DB::select('SELECT
                                                  DATE (cm.created_at) AS date,
                                                  count(*) AS total
                                                FROM
                                                  sub_category AS scm LEFT JOIN sub_category_catalog AS scc
                                                  LEFT JOIN images AS cm
                                                    ON cm.catalog_id = scc.catalog_id AND cm.is_active = 1 AND (cm.json_data IS NOT NULL OR cm.json_data!="")
                                                    ON scm.id = scc.sub_category_id AND scc.is_active = 1 AND scm.category_id = ? AND scm.id = ?
                                                  WHERE DATE (cm.created_at) BETWEEN ? AND ?
                                                GROUP BY date', [$category_id, $sub_category_id, $from_date, $to_date]);

            $total_row = count($total_row_result);


            $uploaded_content_count = DB::select('SELECT
                                                  DATE (cm.created_at) AS date,
                                                  count(*) AS uploaded_content_count
                                                FROM
                                                  sub_category AS scm LEFT JOIN sub_category_catalog AS scc
                                                  LEFT JOIN images AS cm
                                                    ON cm.catalog_id = scc.catalog_id AND cm.is_active = 1 AND (cm.json_data IS NOT NULL OR cm.json_data!="")
                                                    ON scm.id = scc.sub_category_id AND scc.is_active = 1 AND scm.category_id = ? AND scm.id = ?
                                                  WHERE DATE (cm.created_at) BETWEEN ? AND ?
                                                GROUP BY date
                                                ORDER BY  ' . $order_by . ' ' . $order_type . ' LIMIT ?,?', [$category_id, $sub_category_id, $from_date, $to_date, $offset, $item_count]);

            $is_next_page = ($total_row > ($offset + $item_count)) ? true : false;

            $response = Response::json(array('code' => 200, 'message' => 'Summary fetched successfully.', 'cause' => '', 'data' => ['total_record' => $total_row, 'is_next_page' => $is_next_page, 'result' => $uploaded_content_count]));

        } catch (Exception $e) {
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get summary by date range.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            Log::error("getSummaryByDateRange : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
        }
        return $response;
    }

    /**
     * @api {post} getSummaryOfCatalogsFromDiffServer   getSummaryOfCatalogsFromDiffServer
     * @apiName getSummaryOfCatalogsFromDiffServer
     * @apiGroup Statistics (admin)
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "api_url":"http://192.168.0.113/photo_editor_lab_backend/api/public/api/", //compulsory
     * "sub_category_id":66, //compulsory
     * "from_date":"2019-03-21", //compulsory
     * "to_date":"2019-05-06", //compulsory
     * "order_by":"last_uploaded_date",
     * "order_type":"desc"
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Summary details fetched successfully.",
     * "cause": "",
     * "data": {
     * "code": 200,
     * "message": "Summary fetched successfully.",
     * "cause": "",
     * "data": {
     * "total_record": 3,
     * "result": [
     * {
     * "catalog_name": "Branding",
     * "content_count": 5,
     * "last_uploaded_date": "2019-03-22 06:29:35"
     * },
     * {
     * "catalog_name": "Birthday",
     * "content_count": 18,
     * "last_uploaded_date": "2019-03-22 06:03:02"
     * }
     * ]
     * }
     * }
     * }
     */
    public function getSummaryOfCatalogsFromDiffServer(Request $request)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('api_url', 'sub_category_id', 'from_date', 'to_date'), $request)) != '')
                return $response;

            $api_url = $request->api_url . "getSummaryOfCatalogsByDateRange";
            $sub_category_id = $request->sub_category_id;
            $from_date = $request->from_date;
            $to_date = $request->to_date;
            $order_by = isset($request->order_by) ? $request->order_by : 'last_uploaded_date'; //field name
            $order_type = isset($request->order_type) ? $request->order_type : 'desc'; //asc or desc

            $request_body = array(
                'json' => array(
                    'sub_category_id' => $sub_category_id,
                    'from_date' => $from_date,
                    'to_date' => $to_date,
                    'order_by' => $order_by,
                    'order_type' => $order_type,
                )
            );

            $client = new Client();
            $output = $client->post($api_url, $request_body);
            $data = json_decode($output->getBody()->getContents(), true);

            $response = Response::json(array('code' => 200, 'message' => 'Summary details fetched successfully.', 'cause' => '', 'data' => $data));

        } catch (Exception $e) {
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get summary details.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            Log::error("getSummaryOfCatalogsFromDiffServer : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
        }
        return $response;
    }

    /**
     * @api {post} getSummaryOfCatalogsByDateRange   getSummaryOfCatalogsByDateRange
     * @apiName getSummaryOfCatalogsByDateRange
     * @apiGroup Statistics (admin)
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "sub_category_id":66, //compulsory
     * "from_date":"2018-01-01", //compulsory yy-mm-dd
     * "to_date":"2019-05-06", //compulsory
     * "order_by":"catalog_name",
     * "order_type":"desc"
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Summary fetched successfully.",
     * "cause": "",
     * "data": {
     * "total_record": 7,
     * "result": [
     * {
     * "catalog_name": "Branding",
     * "content_count": 82,
     * "last_uploaded_date": "2019-03-20 07:39:33"
     * },
     * {
     * "catalog_name": "Birthday",
     * "content_count": 73,
     * "last_uploaded_date": "2019-03-20 10:11:52"
     * }
     * ]
     * }
     * }
     * }
     */
    public function getSummaryOfCatalogsByDateRange(Request $request)
    {
        try {
            /*$token = JWTAuth::getToken();
            JWTAuth::toUser($token);*/

            $request = json_decode($request->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('sub_category_id', 'from_date', 'to_date'), $request)) != '')
                return $response;

            $sub_category_id = $request->sub_category_id;
            $from_date = $request->from_date;
            $to_date = $request->to_date;
            $order_by = isset($request->order_by) ? $request->order_by : 'last_uploaded_date'; //field name
            $order_type = isset($request->order_type) ? strtolower($request->order_type) : 'desc'; //asc or desc

            $uploaded_content_count = DB::select('SELECT
                                                      ctm.name AS catalog_name,
                                                      count(*) AS content_count,
                                                      coalesce((max(cm.created_at)),"") AS last_uploaded_date
                                                    FROM
                                                      sub_category_catalog AS scc
                                                      JOIN catalog_master AS ctm ON ctm.id = scc.catalog_id AND ctm.is_active = 1 AND ctm.is_featured = 1 AND scc.sub_category_id = ?
                                                      LEFT JOIN images AS cm
                                                        ON cm.catalog_id = scc.catalog_id AND cm.is_active = 1
                                                    WHERE DATE (cm.created_at) BETWEEN ? AND ?
                                                    GROUP BY catalog_name
                                                    ORDER BY ' . $order_by . ' ' . $order_type, [$sub_category_id, $from_date, $to_date]);

            $response = Response::json(array('code' => 200, 'message' => 'Summary fetched successfully.', 'cause' => '', 'data' => ['total_record' => count($uploaded_content_count), 'result' => $uploaded_content_count]));

        } catch (Exception $e) {
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get summary by date range.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            Log::error("getSummaryOfCatalogsByDateRange : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
        }
        return $response;
    }

    /* =================================| Set Rank of Catalogs & Templates |===================================*/

    /**
     * @api {post} setCatalogRankOnTheTopByAdmin setCatalogRankOnTheTopByAdmin
     * @apiName setCatalogRankOnTheTopByAdmin
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     *{
     * "catalog_id":1 //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Rank set successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function setCatalogRankOnTheTopByAdmin(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('catalog_id'), $request)) != '')
                return $response;

            $catalog_id = $request->catalog_id;
            $create_time = date('Y-m-d H:i:s');
            DB::beginTransaction();
            DB::update('UPDATE
                            catalog_master
                            SET updated_at = ?
                            WHERE
                            id = ?', [$create_time, $catalog_id]);
            DB::commit();

            $response = Response::json(array('code' => 200, 'message' => 'Rank set successfully.', 'cause' => '', 'data' => json_decode("{}")));

        } catch (Exception $e) {
            Log::error("setCatalogRankOnTheTopByAdmin : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'set catalog rank.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    /**
     * @api {post} setContentRankOnTheTopByAdmin setContentRankOnTheTopByAdmin
     * @apiName setContentRankOnTheTopByAdmin
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "img_id":1963 //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Rank set successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function setContentRankOnTheTopByAdmin(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('img_id'), $request)) != '')
                return $response;

            $img_id = $request->img_id;
            $create_time = date('Y-m-d H:i:s');

            DB::beginTransaction();
            DB::update('UPDATE
                            images
                            SET updated_at = ?
                            WHERE
                            id = ?', [$create_time, $img_id]);
            DB::commit();

            $response = Response::json(array('code' => 200, 'message' => 'Rank set successfully.', 'cause' => '', 'data' => json_decode("{}")));

        } catch (Exception $e) {
            Log::error("setContentRankOnTheTopByAdmin : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'set content rank.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    /* =====================================| Set search tags of samples by sub_category |==============================================*/

    /**
     * @api {post} getSearchTagsForAllSampleImages   getSearchTagsForAllSampleImages
     * @apiName getSearchTagsForAllSampleImages
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * request_data:{
     * "img_id": 356,
     * "is_free": 1,
     * "is_featured": 1,
     * "json_data": {
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
     * },
     * file:image1.jpeg
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Json data updated successfully!.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function getSearchTagsForAllSampleImages(Request $request)
    {

        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request->getContent());

            if (($response = (new VerificationController())->validateRequiredParameter(array('sub_category_id', 'item_count', 'page', 'no_of_times_update'), $request)) != '')
                return $response;

            $sub_category_id = $request->sub_category_id;
            $item_count = $request->item_count;
            $page = $request->page;
            $no_of_times_update = $request->no_of_times_update;
            $offset = ($page - 1) * $item_count;

            $total_sample_images = DB::select('SELECT count(i.id) AS total
                                    FROM images AS i,
                                        sub_category_catalog AS scc
                                    WHERE
                                    i.json_data IS NOT NULL AND
                                    i.json_data !="" AND
                                    i.catalog_id = scc.catalog_id AND
                                    scc.sub_category_id = ? AND scc.is_active = 1
                                    ORDER BY i.updated_at ASC', [$sub_category_id]);


            $sample_images = DB::select('SELECT i.*
                                    FROM images AS i,
                                        sub_category_catalog AS scc
                                    WHERE
                                    i.json_data IS NOT NULL AND
                                    i.json_data !="" AND
                                    i.catalog_id = scc.catalog_id AND
                                    scc.sub_category_id = ? AND scc.is_active = 1
                                    ORDER BY i.updated_at ASC LIMIT ?, ?', [$sub_category_id, $offset, $item_count]);


            $count = 0;
            $remaining_samples = array();
            $updated_samples = array();
            $file_path = Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN');

            foreach ($sample_images as $key) {
                //Log::info('sample images : ',['image' => $key->image]);

                $tag_list = (new TagDetectController())->getTagInImageByViaURL($key->image, $file_path);
                if ($tag_list == "" or $tag_list == NULL) {
                    //Log::info('Normal images : ',['tags' => $tag_list]);
                    //return Response::json(array('code' => 201, 'message' => 'Tag not detected from clarifai.com.', 'cause' => '', 'data' => json_decode("{}")));
                    $remaining_samples[] = $key->image;

                }

                sleep(1);

                DB::beginTransaction();
                DB::update('UPDATE
                                images SET search_category = ?, attribute3 = ?
                                WHERE id = ?', [$tag_list, $no_of_times_update, $key->id]);
                DB::commit();
                $count = $count + 1;
                $updated_samples[] = $key->image;


            }

            $result_array = array('total_records_to_update' => $total_sample_images[0]->total, 'updated_samples' => $updated_samples, 'remaining_samples' => $remaining_samples);
            $result = json_decode(json_encode($result_array), true);

            $response = Response::json(array('code' => 200, 'message' => 'Search tags added successfully.', 'cause' => '', 'data' => $result));

        } catch
        (Exception $e) {
            Log::error("getSearchTagsForAllSampleImages : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get search tags for sample images.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /**
     * @api {post} getSearchTagsForAllNormalImages   getSearchTagsForAllNormalImages
     * @apiName getSearchTagsForAllNormalImages
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * request_data:{
     * "img_id": 356,
     * "is_free": 1,
     * "is_featured": 1,
     * "json_data": {
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
     * },
     * file:image1.jpeg
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Json data updated successfully!.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function getSearchTagsForAllNormalImages(Request $request)
    {

        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request->getContent());

            if (($response = (new VerificationController())->validateRequiredParameter(array('sub_category_id', 'item_count', 'page', 'no_of_times_update'), $request)) != '')
                return $response;

            $sub_category_id = $request->sub_category_id;
            $item_count = $request->item_count;
            $page = $request->page;
            $no_of_times_update = $request->no_of_times_update;
            $offset = ($page - 1) * $item_count;

            $total_sample_images = DB::select('SELECT count(i.id) AS total
                                                    FROM images AS i,
                                                      sub_category_catalog AS scc
                                                    WHERE
                                                      (i.json_data IS NULL OR
                                                      i.json_data = "") AND
                                                      i.catalog_id = scc.catalog_id AND
                                                      scc.sub_category_id = ? AND scc.is_active = 1
                                                    ORDER BY i.updated_at ASC', [$sub_category_id]);


            $sample_images = DB::select('SELECT i.*
                                            FROM images AS i,
                                              sub_category_catalog AS scc
                                            WHERE
                                              (i.json_data IS NULL OR
                                               i.json_data = "") AND
                                              i.catalog_id = scc.catalog_id AND
                                              scc.sub_category_id = ? AND scc.is_active = 1
                                            ORDER BY i.updated_at ASC LIMIT ?, ?', [$sub_category_id, $offset, $item_count]);


            $count = 0;
            $remaining_samples = array();
            $updated_samples = array();
            $file_path = Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN');

            foreach ($sample_images as $key) {
                //Log::info('sample images : ',['image' => $key->image]);

                $tag_list = (new TagDetectController())->getTagInImageByViaURL($key->image, $file_path);
                if ($tag_list == "" or $tag_list == NULL) {
                    //return Response::json(array('code' => 201, 'message' => 'Tag not detected from clarifai.com.', 'cause' => '', 'data' => json_decode("{}")));
                    $remaining_samples[] = $key->image;

                }

                sleep(1);

                DB::beginTransaction();
                DB::update('UPDATE
                                images SET search_category = ?, attribute3 = ?
                                WHERE id = ?', [$tag_list, $no_of_times_update, $key->id]);
                DB::commit();
                $count = $count + 1;
                $updated_samples[] = $key->image;


            }

            $result_array = array('total_records_to_update' => $total_sample_images[0]->total, 'updated_images' => $updated_samples, 'remaining_images' => $remaining_samples);
            $result = json_decode(json_encode($result_array), true);

            $response = Response::json(array('code' => 200, 'message' => 'Search tags added successfully.', 'cause' => '', 'data' => $result));

        } catch
        (Exception $e) {
            Log::error("getSearchTagsForAllNormalImages : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get search tags for normal images.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /* =====================================| Redis Cache Operation |==============================================*/

    /**
     * @api {post} getRedisKeys   getRedisKeys
     * @apiName getRedisKeys
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
     * "message": "Redis Keys Fetched Successfully.",
     * "cause": "",
     * "data": {
     * "keys_list": [
     * "pel:I4IGClzRXZAjA9u8",
     * "pel:getCatalogBySubCategoryId56-1",
     * "pel:getAllCategory1",
     * "pel:AV4SJwr8Rrf8O60a",
     * "pel:getBackgroundCategory1",
     * "pel:598068d3311b6315293306:standard_ref",
     * "pel:tag:role_user:key",
     * "pel:getLinkiOS-1",
     * "pel:Btr0iNfysqBDree8",
     * "pel:hNBS6Vxc66wL3Dux"
     * ]
     * }
     * }
     */
    public function getRedisKeys(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);
            $redis_keys = Redis::keys('pel:*');
            //$result = isset($redis_keys)?$redis_keys:'{}';
            $result = ['keys_list' => $redis_keys];
            //Log::info("Total Keys :", [count($redis_keys)]);
            $response = Response::json(array('code' => 200, 'message' => 'Redis keys fetched successfully.', 'cause' => '', 'data' => $result));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            Log::error("redisInfo : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get redis-cache keys.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    /**
     * @api {post} deleteRedisKeys   deleteRedisKeys
     * @apiName deleteRedisKeys
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "keys_list": [
     * {
     * "key": "pel:getImagesByCatalogId33-1"
     * },
     * {
     * "key": "pel:getImagesByCatalogId51-1"
     * },
     * {
     * "key":"pel:getImagesByCatalogId57-1"
     * }
     *
     * ]
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Redis Keys Deleted Successfully.",
     * "cause": "",
     * "data": "{}"
     * }
     */
    public function deleteRedisKeys(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            //Log::info("request data :", [$request]);

            if (($response = (new VerificationController())->validateRequiredParam(array('keys_list'), $request)) != '')
                return $response;

            $keys = $request->keys_list;

            foreach ($keys as $rw) {
                if (($response = (new VerificationController())->validateRequiredParameter(array('key'), $rw)) != '')
                    return $response;
            }

            foreach ($keys as $key) {
                Redis::del($key->key);
            }
            $response = Response::json(array('code' => 200, 'message' => 'Redis keys deleted successfully.', 'cause' => '', 'data' => '{}'));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            Log::error("deleteRedisKeys : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'delete redis keys.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    /**
     * @api {post} getRedisKeyDetail   getRedisKeyDetail
     * @apiName getRedisKeyDetail
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "key": "pel:getSubCategoryByCategoryId9-1"
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Redis Key Detail Fetched Successfully.",
     * "cause": "",
     * "data": {
     * "keys_detail": [
     * {
     * "category_id": 11,
     * "name": "Testing"
     * },
     * {
     * "category_id": 10,
     * "name": "Frame"
     * },
     * {
     * "category_id": 9,
     * "name": "Sticker"
     * },
     * {
     * "category_id": 1,
     * "name": "Background"
     * }
     * ]
     * }
     * }
     */
    public function getRedisKeyDetail(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            //Log::info("getRedisKeyDetails Request:", [$request]);
            if (($response = (new VerificationController())->validateRequiredParameter(array('key'), $request)) != '')
                return $response;

            $key = $request->key;
            $key_detail = \Illuminate\Support\Facades\Redis::get($key);
            //return $key_detail;
            $result = ['keys_detail' => unserialize($key_detail)];
            $response = Response::json(array('code' => 200, 'message' => 'Redis key detail fetched successfully.', 'cause' => '', 'data' => $result));
        } catch (Exception $e) {
            Log::error("getRedisKeyDetail : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get redis-cache key detail.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    /**
     * @api {post} clearRedisCache   clearRedisCache
     * @apiName clearRedisCache
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
     * "message": "Redis Keys Deleted Successfully.",
     * "cause": "",
     * "data": "{}"
     * }
     */
    public function clearRedisCache()
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            Redis::flushAll();
            $response = Response::json(array('code' => 200, 'message' => 'Redis keys deleted successfully.', 'cause' => '', 'data' => '{}'));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            Log::error("clearRedisCache : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'clear redis-cache.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    public function testMail()
    {
        try {

            $from_email_id = 'alagiyanirav@gmail.com';
            $to_email_id = 'pinal.optimumbrew@gmail.com';
            $template = 'simple';
            $subject = 'PhotoEditorLab: Test Mail';
            $message_body = array(
                'message' => 'This is a test mail from PhotoEditorLab.',
                'user_name' => 'Admin'
            );
            $api_name = 'testMail';
            $api_description = 'Send test mail.';

            $this->dispatch(new EmailJob($to_email_id, $from_email_id, $subject, $message_body, $template, $api_name, $api_description));
            $response = Response::json(array('code' => 200, 'message' => 'Email sent successfully.', 'cause' => '', 'data' => json_decode("{}")));


        } catch (Swift_TransportException $e) {
            Log::error("testMail (Swift_TransportException) : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'send mail.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        } catch (Exception $e) {
            Log::error("testMail : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'send mail.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    public function getPhpInfo()

    {
        try {

            return $php_info = phpinfo();

        } catch (Exception $e) {
            Log::error("getPhpInfo : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get php_info.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    //Fetch table information from database (use for only debugging query issue)
    public function getDatabaseInfo(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('query'), $request)) != '')
                return $response;

            $query = $request->query;
            return DB::select("$query");

        } catch (Exception $e) {
            Log::error("getDatabaseInfo : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get database information.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    //Fetch constants (use for only debugging constant variables)
    public function getConstants(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('variable_name'), $request)) != '')
                return $response;

            $variable_name = $request->variable_name;
            return Config::get("constant.$variable_name");

        } catch (Exception $e) {
            Log::error("getConstants : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get constants.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    //Artisan commands (use for only debugging artisan commands)
    public function runArtisanCommands(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('command'), $request)) != '')
                return $response;

            $command = $request->command;
            $exitCode = Artisan::call($command);
            return $exitCode;

        } catch (Exception $e) {
            Log::error("runArtisanCommands : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'run artisan command.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

}
<?php

namespace App\Http\Controllers;

use App\Permission;
use App\Role;
use Response;
use Config;
use DB;
use Log;
use File;
use Cache;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Redis;
use Image;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\Storage;


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
            Log::error("addPromoCode Error :", ['Error : ' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
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
            Log::error("getAllPromoCode Error :", ['Error : ' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . ' get all category.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
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
            Log::error("searchPromoCode Exception :", ['Exception : ' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . ' search promo code.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
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
     * "message": "category added successfully.",
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
            //Log::info("request data :", [$request]);
            if (($response = (new VerificationController())->validateRequiredParameter(array('name'), $request)) != '')
                return $response;

            $name = $request->name;
            $create_at = date('Y-m-d H:i:s');
            DB::beginTransaction();

            DB::insert('insert into category (name,created_at) VALUES(?,?)', [$name, $create_at]);

            DB::commit();

            $response = Response::json(array('code' => 200, 'message' => 'category added successfully.', 'cause' => '', 'data' => json_decode('{}')));
        } catch (Exception $e) {
            Log::error("addCategory Error :", ['Error : ' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'add a category.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
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
     * "message": "category updated successfully.",
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
            //Log::info("request data :", [$request]);
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

            $response = Response::json(array('code' => 200, 'message' => 'category updated successfully.', 'cause' => '', 'data' => json_decode('{}')));

        } catch (Exception $e) {
            Log::error("updateCategory Error :", ['error' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'update a category.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
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
     * "message": "category deleted successfully!.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function deleteCategory(Request $request_body)
    {
        try {
            $request = json_decode($request_body->getContent());
            //Log::info("request data :", [$request]);
            if (($response = (new VerificationController())->validateRequiredParameter(array('category_id'), $request)) != '')
                return $response;

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $category_id = $request->category_id;

            DB::beginTransaction();

            $is_active = 0;

            DB::update('update category set is_active=? where id = ? ', [$is_active, $category_id]);

            //DB::update('update sub_category set is_active = ? where category_id = ?',[0,$category_id]);
            $result = DB::table('sub_category')
                ->where('category_id', $category_id)
                ->update(['is_active' => $is_active]);


            DB::commit();

            //Log::info("Category " . $category_id . " Deleted");

            $response = Response::json(array('code' => 200, 'message' => 'Category Deleted Successfully.', 'cause' => '', 'data' => json_decode('{}')));
        } catch (Exception $e) {
            Log::error("deleteCategory Error :", ['Error : ' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'delete Category.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
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
     *  "page":1
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "All Category Fetched Successfully.",
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
            //Log::info([$request]);
            if (($response = (new VerificationController())->validateRequiredParameter(array('page'), $request)) != '')
                return $response;


            $page = $request->page;
            //$item_count = Config::get('constant.PAGINATION_ITEM_LIMIT');
            $this->offset = ($page - 1) * $this->item_count;
            $this->is_active = 1;

            $total_row_result = DB::select('SELECT COUNT(*) as total FROM  category where is_active=?', [$this->is_active]);
            $total_row = $total_row_result[0]->total;

            if (!Cache::has("pel:getAllCategory$page")) {
                $result = Cache::rememberforever("getAllCategory$page", function () {
                    return DB::select('SELECT
                                    ct.id as category_id,
                                    ct.name
                                  FROM
                                  category as ct
                                  where is_active=?
                                  LIMIT ?,?', [$this->is_active, $this->offset, $this->item_count]);
                });
            }

            $redis_result = Cache::get("getAllCategory$page");

            if (!$redis_result) {
                $redis_result = [];
            }

            $is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;

            $response = Response::json(array('code' => 200, 'message' => 'All Category Fetched Successfully.', 'cause' => '', 'data' => ['total_record' => $total_row, 'is_next_page' => $is_next_page, 'category_list' => $redis_result]));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            Log::error("getAllCategory Error :", ['Error : ' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . ' get all category.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
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
     * "message": "search category fetched successfully.",
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
                                  WHERE ct.is_active=? AND
                                     ct.name LIKE ? ', [$is_active, $name]);

            $response = Response::json(array('code' => 200, 'message' => 'search category fetched successfully.', 'cause' => '', 'data' => ['category_list' => $result]));

        } catch (Exception $e) {
            Log::error("searchCategoryByName Error :", ['Error : ' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'Search Category.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

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

                $image = (new ImageController())->generateNewFileName('post_img', $file);
                (new ImageController())->saveOriginalImage($image);
                (new ImageController())->saveCompressedImage($image);
                (new ImageController())->saveThumbnailImage($image);

                $original_sourceFile = $base_url . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY') . $image;
                $compressed_sourceFile = $base_url . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY') . $image;
                $thumbnail_sourceFile = $base_url . Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY') . $image;

                //return array($original_sourceFile,$compressed_sourceFile, $thumbnail_sourceFile);
                $disk = Storage::disk('spaces');
                $original_targetFile = "photoeditorlab/original/" . $image;
                $compressed_targetFile = "photoeditorlab/compressed/" . $image;
                $thumbnail_targetFile = "photoeditorlab/thumbnail/" . $image;
                $disk->put($original_targetFile, file_get_contents($original_sourceFile), 'public');
                $disk->put($compressed_targetFile, file_get_contents($compressed_sourceFile), 'public');
                $disk->put($thumbnail_targetFile, file_get_contents($thumbnail_sourceFile), 'public');


            } else {
                return $response = Response::json(array('code' => 201, 'message' => 'Required field file is missing or empty.', 'cause' => '', 'data' => json_decode('{}')));
            }

            //$contents = Storage::get();

            //https://s3-ap-southeast-1.amazonaws.com/maystr/original/5ac1b68fe3738_post_img_1522644623.png
            $value = "photoeditorlab/original/" . $image;
            $disk = \Storage::disk('spaces');
            $config = \Config::get('filesystems.disks.spaces.bucket');
            if ($disk->exists($value)) {

                $url = "'" . $disk->getDriver()->getAdapter()->getClient()->getObjectUrl($config, $value) . "'";

//                $command = $disk->getDriver()->getAdapter()->getClient()->getCommand('GetObject', [
//                    'Bucket' => $config,
//                    'Key' => $value,
//                    //’ResponseContentDisposition’ => ‘attachment;’//for download
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
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'follow user.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            Log::error('followUser', ['Exception' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
            DB::rollback();
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
    public function addSubCategory(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            if (!$request_body->has('request_data'))
                return Response::json(array('code' => 201, 'message' => 'required field request_data is missing or empty', 'cause' => '', 'data' => json_decode("{}")));

            $request = json_decode($request_body->input('request_data'));

            //Log::info("Request Data:", [$request]);

            if (($response = (new VerificationController())->validateRequiredParameter(array('category_id', 'name'), $request)) != '')
                return $response;

            if (!$request_body->hasFile('file')) {
                return Response::json(array('code' => 201, 'message' => 'required field file is missing or empty', 'cause' => '', 'data' => json_decode("{}")));
            } else {
                $image_array = Input::file('file');
                if (($response = (new ImageController())->verifyImage($image_array)) != '')
                    return $response;

                $category_img = (new ImageController())->generateNewFileName('category_img', $image_array);
                //Log::info("file size :",[filesize($profile_img)]);
                (new ImageController())->saveOriginalImage($category_img);
                (new ImageController())->saveCompressedImage($category_img);
                (new ImageController())->saveThumbnailImage($category_img);
                (new ImageController())->saveImageInToSpaces($category_img);

            }

            $category_id = $request->category_id;
            $name = $request->name;
            $create_at = date('Y-m-d H:i:s');
            DB::beginTransaction();

            DB::insert('insert into sub_category
                        (name,category_id,image,created_at) VALUES(?,?,?,?)',
                [$name, $category_id, $category_img, $create_at]);

            DB::commit();

            $response = Response::json(array('code' => 200, 'message' => 'sub category added successfully.', 'cause' => '', 'data' => json_decode('{}')));
        } catch (Exception $e) {
            Log::error("addSubCategory Error :", ['Error : ' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
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
     * request_data:{
     * "sub_category_id":2,
     * "name":"Love-Category"
     * }
     * file:image.png //optional
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "sub category updated successfully.",
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
                return Response::json(array('code' => 201, 'message' => 'required field request_data is missing or empty', 'cause' => '', 'data' => json_decode("{}")));

            $request = json_decode($request_body->input('request_data'));

            //Log::info("Request Data:", [$request]);

            if (($response = (new VerificationController())->validateRequiredParameter(array('sub_category_id', 'name'), $request)) != '')
                return $response;

            $sub_category_id = $request->sub_category_id;
            $name = $request->name;
            $image_name = '';

            if ($request_body->hasFile('file')) {
                $image_array = Input::file('file');

                if (($response = (new ImageController())->verifyImage($image_array)) != '')
                    return $response;

                $sub_category_img = (new ImageController())->generateNewFileName('sub_category_img', $image_array);
                //Log::info("file size :",[filesize($profile_img)]);
                (new ImageController())->saveOriginalImage($sub_category_img);
                (new ImageController())->saveCompressedImage($sub_category_img);
                (new ImageController())->saveThumbnailImage($sub_category_img);
                (new ImageController())->saveImageInToSpaces($sub_category_img);

                $result = DB::select('select image from sub_category where id = ?', [$sub_category_id]);
                $image_name = $result[0]->image;
                DB::beginTransaction();
                DB::update('UPDATE
                              sub_category
                            SET
                              name = ?,
                              image = ?
                            WHERE
                              id = ? ',
                    [$name, $sub_category_img, $sub_category_id]);

            } else {
                DB::update('UPDATE
                              sub_category
                            SET
                              name = ?
                            WHERE
                              id = ? ',
                    [$name, $sub_category_id]);
            }


            DB::commit();

            if ($image_name) {
                //Image Delete in image_bucket
                (new ImageController())->deleteImage($image_name);
            }
            $response = Response::json(array('code' => 200, 'message' => 'sub category updated successfully.', 'cause' => '', 'data' => json_decode('{}')));

        } catch (Exception $e) {
            Log::error("updateCategory Error :", ['error' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
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
     * "sub_category_id":3
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "sub category deleted successfully!.",
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
            //Log::info("Request Data:", [$request]);
            if (($response = (new VerificationController())->validateRequiredParameter(array('sub_category_id'), $request)) != '')
                return $response;

            $sub_category_id = $request->sub_category_id;
            $is_active = 0;
            DB::beginTransaction();

            DB::update('update sub_category set is_active=? where id = ? ', [$is_active, $sub_category_id]);

            DB::commit();

            //Log::info("Sub category " . $sub_category_id . " Deleted:");

            $response = Response::json(array('code' => 200, 'message' => 'sub category deleted successfully!.', 'cause' => '', 'data' => json_decode('{}')));
        } catch (Exception $e) {
            Log::error("deleteSubCategory Error :", ['Error : ' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
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
     * "message": "SubCategory Fetched Successfully.",
     * "cause": "",
     * "data": {
     * "total_record": 2,
     * "is_next_page": false,
     *  "category_name": "Background",
     * "category_list": [
     * {
     * "sub_category_id": 10,
     * "category_id": 1,
     * "name": "Love-3",
     * "thumbnail_img": "http://192.168.0.102/ob_photolab_backend/image_bucket/thumbnail/5971dc9c891f5_category_img_1500634268.jpg",
     * "compressed_img": "http://192.168.0.102/ob_photolab_backend/image_bucket/compressed/5971dc9c891f5_category_img_1500634268.jpg",
     * "original_img": "http://192.168.0.102/ob_photolab_backend/image_bucket/original/5971dc9c891f5_category_img_1500634268.jpg"
     * },
     * {
     * "sub_category_id": 1,
     * "category_id": 1,
     * "name": "Nature",
     * "thumbnail_img": "http://192.168.0.102/ob_photolab_backend/image_bucket/thumbnail/59719cfa423f3_category_img_1500617978.jpg",
     * "compressed_img": "http://192.168.0.102/ob_photolab_backend/image_bucket/compressed/59719cfa423f3_category_img_1500617978.jpg",
     * "original_img": "http://192.168.0.102/ob_photolab_backend/image_bucket/original/59719cfa423f3_category_img_1500617978.jpg"
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
            //Log::info("getSubCategoryByCategoryId Request:", [$request]);
            if (($response = (new VerificationController())->validateRequiredParameter(array('page', 'item_count', 'category_id'), $request)) != '')
                return $response;

            $page = $request->page;
            $this->category_id = $request->category_id;
            $this->item_count_sub_category = $request->item_count;
            //$item_count = Config::get('constant.PAGINATION_ITEM_LIMIT');
            $this->offset = ($page - 1) * $this->item_count_sub_category;
            $this->is_active = 1;
            //Category Name
            $name = DB::select('SELECT sc.name FROM  category as sc WHERE id = ? and is_active=?', [$this->category_id, $this->is_active]);
            $category_name = $name[0]->name;
            $this->is_active = 1;
            $total_row_result = DB::select('SELECT COUNT(*) as total FROM sub_category WHERE is_active=? and category_id = ?', [$this->is_active, $this->category_id]);
            $total_row = $total_row_result[0]->total;

            if (!Cache::has("pel:getSubCategoryByCategoryId$this->category_id-$page:$this->item_count_sub_category")) {
                $result = Cache::rememberforever("getSubCategoryByCategoryId$this->category_id-$page:$this->item_count_sub_category", function () {
                    /*return DB::select('SELECT
                                        sct.id as sub_category_id,
                                        sct.category_id,
                                        sct.name,
                                        IF(sct.image != "",CONCAT("' . $this->base_url . Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY') . '",sct.image),"") as thumbnail_img,
                                        IF(sct.image != "",CONCAT("' . $this->base_url . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY') . '",sct.image),"") as compressed_img,
                                        IF(sct.image != "",CONCAT("' . $this->base_url . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY') . '",sct.image),"") as original_img
                                      FROM
                                        sub_category as sct
                                      WHERE
                                        sct.category_id = ?
                                        and
                                        sct.is_active=?
                                      order by sct.updated_at DESC
                                      LIMIT ?,?', [$this->category_id, $this->is_active, $this->offset, $this->item_count_sub_category]);*/


                    return DB::select('SELECT
                                        sct.id as sub_category_id,
                                        sct.category_id,
                                        sct.name,
                                        IF(sct.image != "",CONCAT("' . Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",sct.image),"") as thumbnail_img,
                                        IF(sct.image != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",sct.image),"") as compressed_img,
                                        IF(sct.image != "",CONCAT("' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",sct.image),"") as original_img
                                      FROM
                                        sub_category as sct
                                      WHERE
                                        sct.category_id = ?
                                        and
                                        sct.is_active=?
                                      order by sct.updated_at DESC
                                      LIMIT ?,?', [$this->category_id, $this->is_active, $this->offset, $this->item_count_sub_category]);


                });
            }

            $redis_result = Cache::get("getSubCategoryByCategoryId$this->category_id-$page:$this->item_count_sub_category");

            if (!$redis_result) {
                $redis_result = [];
            }

            $is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;

            $response = Response::json(array('code' => 200, 'message' => 'SubCategory Fetched Successfully.', 'cause' => '', 'data' => ['total_record' => $total_row, 'is_next_page' => $is_next_page, 'category_name' => $category_name, 'category_list' => $redis_result]));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            Log::error("getSubCategoryByCategoryId Error :", ['Error : ' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . ' get All Sub Category.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
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
     * "category_id":1
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "SubCategory Fetched Successfully.",
     * "cause": "",
     * "data": {
     * "total_record": 0,
     * "category_list": []
     * }
     * }
     */
    public function getAllSubCategory(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            //Log::info("getSubCategoryByCategoryId Request:", [$request]);
            if (($response = (new VerificationController())->validateRequiredParameter(array('category_id'), $request)) != '')
                return $response;

            $this->category_id = $request->category_id;
            $this->is_active = 1;
            $total_row_result = DB::select('SELECT COUNT(*) as total FROM sub_category WHERE is_active=? and category_id = ?', [$this->is_active, $this->category_id]);
            $total_row = $total_row_result[0]->total;

            if (!Cache::has("pel:getSubCategoryByCategoryId$this->category_id")) {
                $result = Cache::rememberforever("getSubCategoryByCategoryId$this->category_id", function () {
                    /*return DB::select('SELECT
                                        sct.id as sub_category_id,
                                        sct.category_id,
                                        sct.name as sub_category_name,
                                        IF(sct.image != "",CONCAT("' . $this->base_url . Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY') . '",sct.image),"") as thumbnail_img,
                                        IF(sct.image != "",CONCAT("' . $this->base_url . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY') . '",sct.image),"") as compressed_img,
                                        IF(sct.image != "",CONCAT("' . $this->base_url . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY') . '",sct.image),"") as original_img
                                      FROM
                                        sub_category as sct
                                      WHERE
                                        sct.category_id = ?
                                        and
                                        sct.is_active=?
                                      order by sct.updated_at DESC', [$this->category_id, $this->is_active]);*/

                    return DB::select('SELECT
                                        sct.id as sub_category_id,
                                        sct.category_id,
                                        sct.name as sub_category_name,
                                        IF(sct.image != "",CONCAT("' . Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",sct.image),"") as thumbnail_img,
                                        IF(sct.image != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",sct.image),"") as compressed_img,
                                        IF(sct.image != "",CONCAT("' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",sct.image),"") as original_img
                                      FROM
                                        sub_category as sct
                                      WHERE
                                        sct.category_id = ?
                                        and
                                        sct.is_active=?
                                      order by sct.updated_at DESC', [$this->category_id, $this->is_active]);
                });
            }

            $redis_result = Cache::get("getSubCategoryByCategoryId$this->category_id");

            if (!$redis_result) {
                $redis_result = [];
            }


            $response = Response::json(array('code' => 200, 'message' => 'SubCategory Fetched Successfully.', 'cause' => '', 'data' => ['total_record' => $total_row, 'category_list' => $redis_result]));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            Log::error("getSubCategoryByCategoryId Error :", ['Error : ' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . ' get All Sub Category.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
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
     * "category_id":1,
     * "name":"ca"
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "SubCategory Search Successfully.",
     * "cause": "",
     * "data": {
     * "category_list": [
     * {
     * "sub_category_id": 28,
     * "name": "Sub-category",
     * "thumbnail_img": "http://192.168.0.102/ob_photolab_backend/image_bucket/thumbnail/597c6e5045aa8_category_img_1501326928.png",
     * "compressed_img": "http://192.168.0.102/ob_photolab_backend/image_bucket/compressed/597c6e5045aa8_category_img_1501326928.png",
     * "original_img": "http://192.168.0.102/ob_photolab_backend/image_bucket/original/597c6e5045aa8_category_img_1501326928.png"
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
                                    IF(sct.image != "",CONCAT("' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",sct.image),"") as original_img
                                   FROM
                                      sub_category AS sct
                                    WHERE
                                      sct.category_id = ? AND
                                      sct.is_active = ? AND
                                      sct.name LIKE ? ', [$category_id, 1, $name]);

            $response = Response::json(array('code' => 200, 'message' => 'SubCategory Search Successfully.', 'cause' => '', 'data' => ['category_list' => $result]));

        } catch (Exception $e) {
            Log::error("searchSubCategoryByName Error :", ['Error : ' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'Search Sub Category.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
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
    public function addCatalog(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            if (!$request_body->has('request_data'))
                return Response::json(array('code' => 201, 'message' => 'required field request_data is missing or empty', 'cause' => '', 'data' => json_decode("{}")));

            $request = json_decode($request_body->input('request_data'));

            //Log::info("Request Data:", [$request]);

            if (($response = (new VerificationController())->validateRequiredParameter(array('sub_category_id', 'name', 'is_featured'), $request)) != '')
                return $response;

            if (!$request_body->hasFile('file')) {
                return Response::json(array('code' => 201, 'message' => 'required field file is missing or empty', 'cause' => '', 'data' => json_decode("{}")));
            } else {
                $image_array = Input::file('file');
                if (($response = (new ImageController())->verifyImage($image_array)) != '')
                    return $response;

                $catalog_img = (new ImageController())->generateNewFileName('catalog_img', $image_array);
                //Log::info("file size :",[filesize($profile_img)]);
                (new ImageController())->saveOriginalImage($catalog_img);
                (new ImageController())->saveCompressedImage($catalog_img);
                (new ImageController())->saveThumbnailImage($catalog_img);

                (new ImageController())->saveImageInToSpaces($catalog_img);

            }

            $sub_category_id = $request->sub_category_id;
            $name = $request->name;
            $is_free = isset($request->is_free) ? $request->is_free : 1;
            $is_featured = $request->is_featured;

            $create_at = date('Y-m-d H:i:s');
            $is_ative = 1;

            $data = array('name' => $name,
                'image' => $catalog_img,
                'is_free' => $is_free,
                'is_featured' => $is_featured,
                'created_at' => $create_at
            );

            $result = DB::select('select * from catalog_master where name = ?', [$name]);
            if (sizeof($result) != 1) {
                //log::info('catalog data', ['data' => $data]);
                DB::beginTransaction();
                $catalog_id = DB::table('catalog_master')->insertGetId($data);

                //return $catalog_id;

                DB::insert('insert into sub_category_catalog(sub_category_id,catalog_id,created_at) values (?, ?, ?)', [$sub_category_id, $catalog_id, $create_at]);

                DB::commit();

                $response = Response::json(array('code' => 200, 'message' => 'Catalog Added Successfully.', 'cause' => '', 'data' => json_decode('{}')));
            } else {
                $response = Response::json(array('code' => 201, 'message' => 'Duplicate entry for catalog.', 'cause' => '', 'data' => json_decode('{}')));

            }

        } catch (Exception $e) {
            Log::error("addSubCategory Error :", ['Error : ' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'Add Catalog.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
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
     * request_data:{
     * "catalog_id":1,
     * "name":"bg-catalog",
     * "is_free":1,
     * "is_featured":1
     * }
     * file:image.png //optional
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Catalog Updated Successfully.",
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
                return Response::json(array('code' => 201, 'message' => 'required field request_data is missing or empty', 'cause' => '', 'data' => json_decode("{}")));

            $request = json_decode($request_body->input('request_data'));

            //Log::info("Request Data:", [$request]);

            if (($response = (new VerificationController())->validateRequiredParameter(array('catalog_id', 'name', 'is_free', 'is_featured'), $request)) != '')
                return $response;

            $catalog_id = $request->catalog_id;
            $name = $request->name;
            $is_free = $request->is_free;
            $image_name = '';
            $is_featured = $request->is_featured;

            if ($request_body->hasFile('file')) {
                $image_array = Input::file('file');

                if (($response = (new ImageController())->verifyImage($image_array)) != '')
                    return $response;

                $catalog_img = (new ImageController())->generateNewFileName('catalog_img', $image_array);
                //Log::info("file size :",[filesize($profile_img)]);
                (new ImageController())->saveOriginalImage($catalog_img);
                (new ImageController())->saveCompressedImage($catalog_img);
                (new ImageController())->saveThumbnailImage($catalog_img);
                (new ImageController())->saveImageInToSpaces($catalog_img);

                $result = DB::select('select image from catalog_master where id = ?', [$catalog_id]);
                $image_name = $result[0]->image;
                DB::beginTransaction();
                DB::update('UPDATE
                              catalog_master
                            SET
                              name = ?,
                              image = ?,
                              is_free = ?,
                              is_featured = ?
                            WHERE
                              id = ? ',
                    [$name, $catalog_img, $is_free, $is_featured, $catalog_id]);
            } else {
                DB::update('UPDATE
                              catalog_master
                            SET
                              name = ?,
                              is_free = ?,
                              is_featured = ?
                            WHERE
                              id = ? ',
                    [$name, $is_free, $is_featured, $catalog_id]);
            }


            DB::commit();

            if ($image_name) {
                //Image Delete in image_bucket
                (new ImageController())->deleteImage($image_name);
            }
            $response = Response::json(array('code' => 200, 'message' => 'Catalog Updated Successfully.', 'cause' => '', 'data' => json_decode('{}')));

        } catch (Exception $e) {
            Log::error("updateCategory Error :", ['error' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'update Catalog.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
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
     * "catalog_id":3
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Catalog Deleted Successfully!.",
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
            //Log::info("Request Data:", [$request]);
            if (($response = (new VerificationController())->validateRequiredParameter(array('catalog_id'), $request)) != '')
                return $response;

            $catalog_id = $request->catalog_id;
            $is_active = 0;
            DB::beginTransaction();

            //$result = DB::select('select image from catalog_master where id = ?', [$catalog_id]);
            //$image_name = $result[0]->image;

            //$result = DB::select('select image from images where catalog_id = ?', [$catalog_id]);

            DB::update('update catalog_master set is_active=?, is_featured= ?  where id = ? ', [$is_active, 0, $catalog_id]);
            DB::update('update sub_category_catalog set is_active=? where catalog_id = ? ', [$is_active, $catalog_id]);

            DB::delete('DELETE FROM images WHERE catalog_id = ?', [$catalog_id]);
            DB::commit();

//            //Image Delete in image_bucket
//            (new ImageController())->deleteImage($image_name);
//
//            Log::info("catalog_id:", [$image_name]);
//            foreach ($result as $rw) {
//                //Image Delete in image_bucket
//                (new ImageController())->deleteImage($rw->image);
//            }

            $response = Response::json(array('code' => 200, 'message' => 'Catalog Deleted Successfully!.', 'cause' => '', 'data' => json_decode('{}')));
        } catch (Exception $e) {
            Log::error("deleteCatalog Error :", ['Error : ' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'Delete Catalog.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
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
            //Log::info("getFeaturedCatalogBySubCategoryId Request:", [$request]);
            if (($response = (new VerificationController())->validateRequiredParameter(array('sub_category_id'), $request)) != '')
                return $response;

            $this->sub_category_id = $request->sub_category_id;
            //$item_count = Config::get('constant.PAGINATION_ITEM_LIMIT');

            //return $total_row;

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
//            AND
//            sct.is_active=?
//            $this->is_active, $this->is_active,

            $redis_result = Cache::get("getFeaturedCatalogBySubCategoryId$this->sub_category_id");

            if (!$redis_result) {
                $redis_result = [];
            }


            $response = Response::json(array('code' => 200, 'message' => 'Catalog Fetched Successfully.', 'cause' => '', 'data' => ['category_list' => $redis_result]));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            Log::error("getAllCategory Error :", ['Error : ' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . ' get Catalog.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
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
            //Log::info("getSubCategoryByCategoryId Request:", [$request]);
            if (($response = (new VerificationController())->validateRequiredParameter(array('sub_category_id'), $request)) != '')
                return $response;

            $this->sub_category_id = $request->sub_category_id;

            //sub Category Name
            $name = DB::select('SELECT sc.name FROM  sub_category as sc WHERE sc.id = ? AND sc.is_active = ?', [$this->sub_category_id, 1]);
            $category_name = $name[0]->name;

            $total_row_result = DB::select('SELECT COUNT(*) as total FROM  sub_category_catalog WHERE sub_category_id = ? AND is_active = ?', [$this->sub_category_id, 1]);
            $total_row = $total_row_result[0]->total;
            //return $total_row;

            if (!Cache::has("pel:getCatalogBySubCategoryId$this->sub_category_id")) {
                $result = Cache::rememberforever("getCatalogBySubCategoryId$this->sub_category_id", function () {
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
                                        sct.is_active=1
                                      order by ct.updated_at DESC', [$this->sub_category_id]);
                });


            }
//            AND
//            sct.is_active=?
//            $this->is_active, $this->is_active,

            $redis_result = Cache::get("getCatalogBySubCategoryId$this->sub_category_id");

            if (!$redis_result) {
                $redis_result = [];
            }

            $response = Response::json(array('code' => 200, 'message' => 'Catalog Fetched Successfully.', 'cause' => '', 'data' => ['total_record' => $total_row, 'category_name' => $category_name, 'category_list' => $redis_result]));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            Log::error("getAllCategory Error :", ['Error : ' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . ' get Catalog.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
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
            //Log::info("getSubCategoryByCategoryId Request:", [$request]);
            if (($response = (new VerificationController())->validateRequiredParameter(array('sub_category_id'), $request)) != '')
                return $response;


            $this->sub_category_id = $request->sub_category_id;

            //sub Category Name
            $name = DB::select('SELECT sc.name FROM  sub_category as sc WHERE id = ? AND is_active = ?', [$this->sub_category_id, 1]);
            $cateory_name = $name[0]->name;

            $total_row_result = DB::select('SELECT COUNT(*) as total FROM  sub_category_catalog WHERE sub_category_id = ? AND is_active = ?', [$this->sub_category_id, 1]);
            $total_row = $total_row_result[0]->total;
            //return $total_row;

            if (!Cache::has("pel:getBackgroundCatalogBySubCategoryId$this->sub_category_id")) {
                $result = Cache::rememberforever("getBackgroundCatalogBySubCategoryId$this->sub_category_id", function () {
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
                                        sct.is_active=1 AND
                                        ct.is_featured=0
                                      order by ct.updated_at DESC', [$this->sub_category_id]);
                });


            }
//            AND
//            sct.is_active=?
//            $this->is_active, $this->is_active,

            $redis_result = Cache::get("getBackgroundCatalogBySubCategoryId$this->sub_category_id");

            if (!$redis_result) {
                $redis_result = [];
            }


            $response = Response::json(array('code' => 200, 'message' => 'Catalog Fetched Successfully.', 'cause' => '', 'data' => ['total_record' => $total_row, 'category_name' => $cateory_name, 'category_list' => $redis_result]));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            Log::error("getAllCategory Error :", ['Error : ' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . ' get Catalog.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
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
                                    cm.is_free
                                   FROM
                                      catalog_master AS cm,
                                      sub_category_catalog as sct
                                    WHERE
                                      sct.sub_category_id = ? AND
                                      sct.catalog_id=cm.id AND
                                      sct.is_active=1 AND
                                      cm.name LIKE ? ', [$sub_category_id, $name]);

            $response = Response::json(array('code' => 200, 'message' => 'Catalog Search Successfully.', 'cause' => '', 'data' => ['category_list' => $result]));

        } catch (Exception $e) {
            Log::error("searchCatalogByName Error :", ['Error : ' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'Search Catalog.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
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
                return Response::json(array('code' => 201, 'message' => 'required field request_data is missing or empty', 'cause' => '', 'data' => json_decode("{}")));

            $request = json_decode($request_body->input('request_data'));

            //Log::info("Request Data :", [$request]);

            if (($response = (new VerificationController())->validateRequiredParameter(array('catalog_id'), $request)) != '')
                return $response;

            $catalog_id = $request->catalog_id;
            $create_at = date('Y-m-d H:i:s');
            DB::beginTransaction();
            if (!$request_body->hasFile('file')) {
                return Response::json(array('code' => 201, 'message' => 'required field file is missing or empty', 'cause' => '', 'data' => json_decode("{}")));
            } else {

                $images_array = Input::file('file');
                //return $images_array;
                foreach ($images_array as $image_array) {
                    if (($response = (new ImageController())->verifyImage($image_array)) != '')
                        return $response;

                    $catalog_image = (new ImageController())->generateNewFileName('catalog_images', $image_array);
                    //(new ImageController())->saveOriginalImage($background_img);

                    $original_path = '../..' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY');
                    $image_array->move($original_path, $catalog_image);

                    (new ImageController())->saveCompressedImage($catalog_image);
                    (new ImageController())->saveThumbnailImage($catalog_image);
                    (new ImageController())->saveImageInToSpaces($catalog_image);


                    DB::insert('INSERT
                                INTO
                                  images(catalog_id, image, created_at)
                                VALUES(?, ?, ?) ', [$catalog_id, $catalog_image, $create_at]);
                }

                DB::commit();
            }


            $response = Response::json(array('code' => 200, 'message' => 'sub category images added successfully.', 'cause' => '', 'data' => json_decode('{}')));
        } catch (Exception $e) {
            Log::error("addCategoryImages Error :", ['Error : ' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'add sub category images.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
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
                return Response::json(array('code' => 201, 'message' => 'required field original_img or display_img is missing or empty', 'cause' => '', 'data' => json_decode("{}")));
            } elseif (!$request_body->hasFile('original_img')) {

                return Response::json(array('code' => 201, 'message' => 'required field original_img is missing or empty', 'cause' => '', 'data' => json_decode("{}")));

            } elseif (!$request_body->hasFile('display_img')) {
                return Response::json(array('code' => 201, 'message' => 'required field display_img is missing or empty', 'cause' => '', 'data' => json_decode("{}")));

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
                    (new ImageController())->saveImageInToSpaces($original_img);

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

                    (new ImageController())->saveImageInToSpaces($display_img);


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

                $response = Response::json(array('code' => 200, 'message' => 'Featured Background Images added successfully!.', 'cause' => '', 'data' => json_decode('{}')));

            }


        } catch (Exception $e) {
            Log::error("addFeaturedBackgroundCatalogImage Error :", ['Error : ' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'Add Featured Background Images.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /**
     * @api {post} addFeaturedImage   addFeaturedImage
     * @apiName addFeaturedImage
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * request_data:{
     * "catalog_id":10
     * },
     * original_img:image1.jpeg,
     * display_img:image12.jpeg
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Featured Images added successfully!.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function addFeaturedImage(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->input('request_data'));
            if (($response = (new VerificationController())->validateRequiredParameter(array('catalog_id'), $request)) != '')
                return $response;

            $created_at = date('Y-m-d H:i:s');
            $catalog_id = $request->catalog_id;

            if (!$request_body->hasFile('original_img') and !$request_body->hasFile('display_img')) {
                return Response::json(array('code' => 201, 'message' => 'required field file is missing or empty', 'cause' => '', 'data' => json_decode("{}")));
            } else if ($request_body->hasFile('original_img')) {
                $image_array = Input::file('original_img');
                if (($response = (new ImageController())->verifyImage($image_array)) != '')
                    return $response;

                $original_img = (new ImageController())->generateNewFileName('original_img', $image_array);
                $file_name = 'original_img';
                (new ImageController())->saveMultipleOriginalImage($original_img, $file_name);
                (new ImageController())->saveMultipleCompressedImage($original_img, $file_name);
                (new ImageController())->saveMultipleThumbnailImage($original_img, $file_name);
                (new ImageController())->saveImageInToSpaces($original_img);

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

                (new ImageController())->saveImageInToSpaces($display_img);

            }


            DB::beginTransaction();

            $data = array(
                'catalog_id' => $catalog_id,
                'original_img' => $original_img,
                'display_img' => $display_img,
                'created_at' => $created_at
            );

            $sample_image_id = DB::table('images')->insertGetId($data);

            //DB::update('update images set original_img = ?,display_img = ?,image_type = ?,created_at = ? where catalog_id = ?',[$original_img, $display_img, $image_type, $created_at, $catalog_id]);
            //log::info('Inserted featured image for Sticker/Frame');

            DB::commit();

            $response = Response::json(array('code' => 200, 'message' => 'Featured Images added successfully!.', 'cause' => '', 'data' => json_decode('{}')));
        } catch (Exception $e) {
            Log::error("addFeaturedImage Error :", ['Error : ' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'Add Featured Images.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
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
                (new ImageController())->saveImageInToSpaces($original_img);
                DB::beginTransaction();
                DB::update('update images set original_img = ?,image_type = ?,created_at = ? where id = ?', [$original_img, $image_type, $created_at, $img_id]);


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
                (new ImageController())->saveImageInToSpaces($display_img);

                DB::update('update images set display_img = ?,image_type = ?,created_at = ? where id = ?', [$display_img, $image_type, $created_at, $img_id]);

            } else {
                $display_img = '';
            }

            if ($original_img == '' && $display_img == '') {
                DB::update('update images set image_type = ?,created_at = ? where id = ?', [$image_type, $created_at, $img_id]);
            }

            //log::info('Updated featured background image');

            DB::commit();

            $response = Response::json(array('code' => 200, 'message' => 'Featured Background Images updated successfully!.', 'cause' => '', 'data' => json_decode('{}')));
        } catch (Exception $e) {
            Log::error("updateFeaturedBackgroundCatalogImage Error :", ['Error : ' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'Update Featured Background Images.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /**
     * @api {post} updateFeaturedImage   updateFeaturedImage
     * @apiName updateFeaturedImage
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * request_data:{
     * "img_id":10
     * },
     * original_img:image1.jpeg,
     * display_img:image12.jpeg
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Featured Images added successfully!.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function updateFeaturedImage(Request $request_body)
    {
        try {
            $request = json_decode($request_body->input('request_data'));
            if (($response = (new VerificationController())->validateRequiredParameter(array('img_id'), $request)) != '')
                return $response;

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $created_at = date('Y-m-d H:i:s');
            $img_id = $request->img_id;

            if (!$request_body->hasFile('original_img') and !$request_body->hasFile('display_img')) {
                return Response::json(array('code' => 201, 'message' => 'required field file is missing or empty', 'cause' => '', 'data' => json_decode("{}")));
            } else if ($request_body->hasFile('original_img')) {
                $image_array = Input::file('original_img');
                if (($response = (new ImageController())->verifyImage($image_array)) != '')
                    return $response;

                $original_img = (new ImageController())->generateNewFileName('original_img', $image_array);
                $file_name = 'original_img';
                (new ImageController())->saveMultipleOriginalImage($original_img, $file_name);
                (new ImageController())->saveMultipleCompressedImage($original_img, $file_name);
                (new ImageController())->saveMultipleThumbnailImage($original_img, $file_name);
                (new ImageController())->saveImageInToSpaces($original_img);

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

                (new ImageController())->saveImageInToSpaces($display_img);

            }


            DB::beginTransaction();


            DB::update('update images set original_img = ?,display_img = ?,created_at = ? where id = ?', [$original_img, $display_img, $created_at, $img_id]);
            //log::info('Updated featured image');

            DB::commit();

            $response = Response::json(array('code' => 200, 'message' => 'Featured Images updated successfully!.', 'cause' => '', 'data' => json_decode('{}')));

        } catch (Exception $e) {
            Log::error("updateFeaturedImage Error :", ['Error : ' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'Update Featured Images.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
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
                                          images as im,
                                          catalog_master as cm
                                        where
                                          im.is_active = 1 AND
                                          im.catalog_id = ? And
                                          cm.id=im.catalog_id And
                                          isnull(im.image) AND
                                          cm.is_featured=1
                                        order by im.updated_at DESC', [$this->catalog_id]);
                });
            }
            $redis_result = Cache::get("getSampleImagesForAdmin$this->catalog_id");

            if (!$redis_result) {
                $redis_result = [];
            }


            $response = Response::json(array('code' => 200, 'message' => 'Sample Images Fetched Successfully.', 'cause' => '', 'data' => ['image_list' => $redis_result]));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            Log::error("getSampleImagesForAdmin Error :", ['Error : ' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . ' Fetched Image By Catalog Id.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
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


            $response = Response::json(array('code' => 200, 'message' => 'Sample Images Fetched Successfully.', 'cause' => '', 'data' => ['image_list' => $redis_result]));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            Log::error("getSampleImagesForMobile Error :", ['Error : ' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . ' Fetched Images By Sub Category Id.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
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
     * request_data:{
     * "img_id":1
     * }
     * file:""
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Catalog Image Updated Successfully.",
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
                return Response::json(array('code' => 201, 'message' => 'required field request_data is missing or empty', 'cause' => '', 'data' => json_decode("{}")));

            $request = json_decode($request_body->input('request_data'));

            //Log::info("Request Data:", [$request]);

            if (($response = (new VerificationController())->validateRequiredParameter(array('img_id'), $request)) != '')
                return $response;

            $img_id = $request->img_id;
            $image_name = '';

            if (!$request_body->hasFile('file')) {
                return Response::json(array('code' => 201, 'message' => 'required field file is missing or empty', 'cause' => '', 'data' => json_decode("{}")));
            } else {
                $image_array = Input::file('file');

                if (($response = (new ImageController())->verifyImage($image_array)) != '')
                    return $response;

                $catalog_img = (new ImageController())->generateNewFileName('catalog_img', $image_array);
                //Log::info("file size :",[filesize($profile_img)]);
                (new ImageController())->saveOriginalImage($catalog_img);
                (new ImageController())->saveCompressedImage($catalog_img);
                (new ImageController())->saveThumbnailImage($catalog_img);
                (new ImageController())->saveImageInToSpaces($catalog_img);


                $result = DB::select('select image from images where id = ?', [$img_id]);
                $image_name = $result[0]->image;
                DB::beginTransaction();
                DB::update('UPDATE
                              images
                            SET
                              image = ?
                            WHERE
                              id = ? ',
                    [$catalog_img, $img_id]);
                DB::commit();
            }


            if ($image_name) {
                //Image Delete in image_bucket
                (new ImageController())->deleteImage($image_name);
            }

            $response = Response::json(array('code' => 200, 'message' => 'Catalog Image Updated Successfully.', 'cause' => '', 'data' => json_decode('{}')));

        } catch (Exception $e) {
            Log::error("updateCategoryImage Error :", ['error' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'update Category.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
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
     * "img_id":1
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Catalog Image Deleted Successfully.",
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

            $result = DB::select('select image from images where id = ?', [$img_id]);
            $image_name = $result[0]->image;
            DB::beginTransaction();

            DB::delete('delete from images where id = ? ', [$img_id]);

            DB::commit();

            //Image Delete in image_bucket
            (new ImageController())->deleteImage($image_name);

            $response = Response::json(array('code' => 200, 'message' => 'Catalog Image Deleted Successfully.', 'cause' => '', 'data' => json_decode('{}')));
        } catch (Exception $e) {
            Log::error("deleteSubCategoryImages Error :", ['Error : ' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'Delete Catalog Image.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /**
     * @api {post} getImagesByCatalogId   getImagesByCatalogId
     * @apiName getImagesByCatalogId
     * @apiGroup Admin
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
     * "message": "Catalog Images Fetched Successfully.",
     * "cause": "",
     * "data": {
     * "image_list": [
     * {
     * "img_id": 13,
     * "thumbnail_img": "http://localhost/ob_photolab_backend/image_bucket/thumbnail/598d51e5ec3f2_catalog_image_1502433765.png",
     * "compressed_img": "http://localhost/ob_photolab_backend/image_bucket/compressed/598d51e5ec3f2_catalog_image_1502433765.png",
     * "original_img": "http://localhost/ob_photolab_backend/image_bucket/original/598d51e5ec3f2_catalog_image_1502433765.png"
     * },
     * {
     * "img_id": 14,
     * "thumbnail_img": "http://localhost/ob_photolab_backend/image_bucket/thumbnail/598d51e65fdf3_catalog_image_1502433766.png",
     * "compressed_img": "http://localhost/ob_photolab_backend/image_bucket/compressed/598d51e65fdf3_catalog_image_1502433766.png",
     * "original_img": "http://localhost/ob_photolab_backend/image_bucket/original/598d51e65fdf3_catalog_image_1502433766.png"
     * },
     * {
     * "img_id": 11,
     * "thumbnail_img": "http://localhost/ob_photolab_backend/image_bucket/thumbnail/598d51e4e7d68_catalog_image_1502433764.png",
     * "compressed_img": "http://localhost/ob_photolab_backend/image_bucket/compressed/598d51e4e7d68_catalog_image_1502433764.png",
     * "original_img": "http://localhost/ob_photolab_backend/image_bucket/original/598d51e4e7d68_catalog_image_1502433764.png"
     * }
     * ]
     * }
     * }
     */
    public function getImagesByCatalogId(Request $request_body)
    {
        try {
            $request = json_decode($request_body->getContent());
            //Log::info("getImagesByCatalogId Request :", [$request]);

            if (($response = (new VerificationController())->validateRequiredParameter(array('catalog_id'), $request)) != '')
                return $response;

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

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
                                          coalesce(im.is_portrait,0) as is_portrait
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
            $redis_result = Cache::get("getImagesByCatalogId$this->catalog_id");

            if (!$redis_result) {
                $redis_result = [];
            }


            $response = Response::json(array('code' => 200, 'message' => 'Catalog Images Fetched Successfully.', 'cause' => '', 'data' => ['image_list' => $redis_result]));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            Log::error("getImagesByCatalogId Error :", ['Error : ' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . ' Fetched Image By Catalog Id.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
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

            $total_row_result = DB::select('SELECT COUNT(*) as total FROM  sub_category_catalog WHERE is_active = ?', [1]);
            $total_row = $total_row_result[0]->total;
            //return $total_row;

            if (!Cache::has("pel:getAllCatalog")) {
                $result = Cache::rememberforever("getAllCatalog", function () {
                    return DB::select('SELECT
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
                });
            }

            $redis_result = Cache::get("getAllCatalog");

            if (!$redis_result) {
                $redis_result = [];
            }

            $response = Response::json(array('code' => 200, 'message' => 'Catalog Fetched Successfully.', 'cause' => '', 'data' => ['total_record' => $total_row, 'catalog_list' => $redis_result]));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            Log::error("getAllCatalog Error :", ['Error : ' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . ' get All Catalog.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
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

            $request = json_decode($request->getContent());
            //Log::info("linkCatalog Request :", [$request]);

            if (($response = (new VerificationController())->validateRequiredParameter(array('catalog_id', 'sub_category_id'), $request)) != '')
                return $response;

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);
            //$query=DB::select('select * from sub_category_catalog WHERE sub_category_id = ? AND catalog_id = ?',[$sub_category_id,$catalog_id]);
            $catalog_id = $request->catalog_id;
            $sub_category_id = $request->sub_category_id;

            $create_at = date('Y-m-d H:i:s');
            $is_ative = 1;
            DB::beginTransaction();


            //return $catalog_id;

            DB::insert('insert into sub_category_catalog(sub_category_id,catalog_id,created_at) values (?, ?, ?)', [$sub_category_id, $catalog_id, $create_at]);

            DB::commit();

            $response = Response::json(array('code' => 200, 'message' => 'Catalog Linked Successfully.', 'cause' => '', 'data' => json_decode('{}')));
        } catch (Exception $e) {
            Log::error("linkCatalog Error :", ['Error : ' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
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

            $request = json_decode($request->getContent());
            //Log::info("getImagesByCatalogId Request :", [$request]);

            if (($response = (new VerificationController())->validateRequiredParameter(array('catalog_id', 'category_id'), $request)) != '')
                return $response;

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $this->catalog_id = $request->catalog_id;
            $this->category_id = $request->category_id;


            //return $total_row;

            if (!Cache::has("pel:getAllSubCategoryForLinkCatalog$this->catalog_id->$this->category_id")) {
                $result = Cache::rememberforever("getAllSubCategoryForLinkCatalog$this->catalog_id->$this->category_id", function () {

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

//                    return DB::select('SELECT
//                                          id AS sub_category_id,
//                                          name,
//                                          IF((SELECT sub_category_id
//                                           FROM sub_category_catalog scc
//                                           WHERE catalog_id = ? and sc.id=scc.sub_category_id and scc.is_active=1 LIMIT 1) ,1,0) as linked
//                                        FROM sub_category sc
//                                        WHERE is_active = 1
//                                        ORDER BY name',[$this->catalog_id]);
                });

            }

            $redis_result = Cache::get("getAllSubCategoryForLinkCatalog$this->catalog_id->$this->category_id");

            if (!$redis_result) {
                $redis_result = [];
            }

            $response = Response::json(array('code' => 200, 'message' => 'SubCategory Fetched Successfully.', 'cause' => '', 'data' => ['category_list' => $redis_result]));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            Log::error("getAllSubCategoryForLinkCatalog Error :", ['Error : ' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . ' get All Sub Category.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
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
            $request = json_decode($request->getContent());
            //Log::info("Request Data:", [$request]);
            if (($response = (new VerificationController())->validateRequiredParameter(array('catalog_id', 'sub_category_id'), $request)) != '')
                return $response;

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $catalog_id = $request->catalog_id;
            $sub_category_id = $request->sub_category_id;

            $is_active = 0;
            DB::beginTransaction();

            //$result = DB::select('select image from catalog_master where id = ?', [$catalog_id]);
            //$image_name = $result[0]->image;

            //$result = DB::select('select image from images where catalog_id = ?', [$catalog_id]);
            //$result = DB::select('SELECT sub_category_id from sub_category_catalog WHERE sub_category_id=? and is_active=? and catalog_id = ?', [$sub_category_id, 1, $catalog_id]);
            $result = DB::select('SELECT count(*) as count_catalog from sub_category_catalog WHERE catalog_id = ? and is_active = 1', [$catalog_id]);
            //return $result[0]->count_catalog;
            if ($result[0]->count_catalog > 1) {
                DB::delete('delete from sub_category_catalog where sub_category_id = ? AND catalog_id = ? ', [$sub_category_id, $catalog_id]);
                $response = Response::json(array('code' => 200, 'message' => 'Catalog unlinked Successfully!.', 'cause' => '', 'data' => json_decode('{}')));

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
            Log::error("deleteLinkedCatalog Error :", ['Error : ' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'delete Linked Catalog.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
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

//            if (!$request_body->has('request_data'))
//                return Response::json(array('code' => 201, 'message' => 'required field request_data is missing or empty', 'cause' => '', 'data' => json_decode("{}")));
//
//            $request = json_decode($request_body->input('request_data'));
//
//            Log::info("request data :", [$request]);
//            if (($response = (new VerificationController())->validateRequiredParameter(array('sub_category_id', 'platform', 'url', 'platform'), $request)) != '')
//                return $response;
//
//            $token = JWTAuth::getToken();
//            JWTAuth::toUser($token);
//
//            if (!$request_body->hasFile('file')) {
//                return Response::json(array('code' => 201, 'message' => 'required field file is missing or empty', 'cause' => '', 'data' => json_decode("{}")));
//            } else {
//                $image_array = Input::file('file');
//                if (($response = (new ImageController())->verifyImage($image_array)) != '')
//                    return $response;
//
//                $app_image = (new ImageController())->generateNewFileName('app_image', $image_array);
//                //Log::info("file size :",[filesize($profile_img)]);
//                (new ImageController())->saveOriginalImage($app_image);
//                (new ImageController())->saveCompressedImage($app_image);
//                (new ImageController())->saveThumbnailImage($app_image);
//            }
//
//            $sub_category_id = $request->sub_category_id;
//            $name = $request->name;
//            $url = $request->url;
//            $platform = $request->platform;
//            $create_at = date('Y-m-d H:i:s');
//
//            $data = array('name' => $name,
//                'image' => $app_image,
//                //'logo_image' => "",//$app_logo,
//                'url' => $url,
//                'platform' => $platform,
//                'created_at' => $create_at);
//            DB::beginTransaction();
//            $advertise_link_id = DB::table('advertise_links')->insertGetId($data);
//
//            DB::insert('insert into sub_category_advertise_links(sub_category_id, advertise_link_id, is_active, created_at) VALUES (?, ?, ?, ?)', [$sub_category_id, $advertise_link_id, 1, $create_at]);
//
//            DB::commit();
//
//            $response = Response::json(array('code' => 200, 'message' => 'Link Added Successfully.', 'cause' => '', 'data' => json_decode('{}')));


            if (!$request_body->has('request_data'))
                return Response::json(array('code' => 201, 'message' => 'required field request_data is missing or empty', 'cause' => '', 'data' => json_decode("{}")));

            $request = json_decode($request_body->input('request_data'));

            //Log::info("request data :", [$request]);
            if (($response = (new VerificationController())->validateRequiredParameter(array('platform', 'url', 'platform', 'app_description'), $request)) != '')
                return $response;

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            //$sub_category_id = $request->sub_category_id;
            $name = $request->name;
            $url = $request->url;
            $platform = $request->platform;
            $app_description = $request->app_description;
            $create_at = date('Y-m-d H:i:s');

//            if (($response = (new VerificationController())->checkIfAdvertisementExist($url)) != '')
//                return $response;

            if (!$request_body->hasFile('file')) {
                return Response::json(array('code' => 201, 'message' => 'required field file is missing or empty', 'cause' => '', 'data' => json_decode("{}")));
            } else {
                $image_array = Input::file('file');
                if (($response = (new ImageController())->verifyImage($image_array)) != '')
                    return $response;

                $app_image = (new ImageController())->generateNewFileName('banner_image', $image_array);
                //Log::info("file size :",[filesize($profile_img)]);
                (new ImageController())->saveOriginalImage($app_image);
                (new ImageController())->saveCompressedImage($app_image);
                (new ImageController())->saveThumbnailImage($app_image);
                (new ImageController())->saveImageInToSpaces($app_image);


            }

            //Logo Image
            if (!$request_body->hasFile('logo_file')) {
                return Response::json(array('code' => 201, 'message' => 'required field logo_file is missing or empty', 'cause' => '', 'data' => json_decode("{}")));
            } else {
                $logo_image_array = Input::file('logo_file');
                if (($response = (new ImageController())->verifyImage($logo_image_array)) != '')
                    return $response;

                $app_logo = (new ImageController())->generateNewFileName('app_logo_image', $logo_image_array);
                //Log::info("file size :",[filesize($profile_img)]);
                (new ImageController())->saveMultipleOriginalImage($app_logo, 'logo_file');
                (new ImageController())->saveCompressedImage($app_logo);
                (new ImageController())->saveThumbnailImage($app_logo);
                (new ImageController())->saveImageInToSpaces($app_logo);
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

            $response = Response::json(array('code' => 200, 'message' => 'Link Added Successfully.', 'cause' => '', 'data' => json_decode('{}')));
        } catch (Exception $e) {
            Log::error("addLink Error :", ['Error : ' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'add link.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    public function addLinkWithAppLogo(Request $request_body)
    {
        try {

            if (!$request_body->has('request_data'))
                return Response::json(array('code' => 201, 'message' => 'required field request_data is missing or empty', 'cause' => '', 'data' => json_decode("{}")));

            $request = json_decode($request_body->input('request_data'));

            //Log::info("request data :", [$request]);
            if (($response = (new VerificationController())->validateRequiredParameter(array('sub_category_id', 'platform', 'url', 'platform', 'app_description'), $request)) != '')
                return $response;

//            $token = JWTAuth::getToken();
//            JWTAuth::toUser($token);

            if (!$request_body->hasFile('file')) {
                return Response::json(array('code' => 201, 'message' => 'required field file is missing or empty', 'cause' => '', 'data' => json_decode("{}")));
            } else {
                $image_array = Input::file('file');
                if (($response = (new ImageController())->verifyImage($image_array)) != '')
                    return $response;

                $app_image = (new ImageController())->generateNewFileName('banner_image', $image_array);
                //Log::info("file size :",[filesize($profile_img)]);
                (new ImageController())->saveOriginalImage($app_image);
                (new ImageController())->saveCompressedImage($app_image);
                (new ImageController())->saveThumbnailImage($app_image);
                (new ImageController())->saveImageInToSpaces($app_image);

            }

            //Logo Image
            if (!$request_body->hasFile('logo_file')) {
                return Response::json(array('code' => 201, 'message' => 'required field logo_file is missing or empty', 'cause' => '', 'data' => json_decode("{}")));
            } else {
                $logo_image_array = Input::file('logo_file');
                if (($response = (new ImageController())->verifyImage($logo_image_array)) != '')
                    return $response;

                $app_logo = (new ImageController())->generateNewFileName('app_logo_image', $logo_image_array);
                //Log::info("file size :",[filesize($profile_img)]);
                (new ImageController())->saveMultipleOriginalImage($app_logo, 'logo_file');
                (new ImageController())->saveCompressedImage($app_logo);
                (new ImageController())->saveThumbnailImage($app_logo);

                (new ImageController())->saveImageInToSpaces($app_logo);

            }


            $sub_category_id = $request->sub_category_id;
            $name = $request->name;
            $url = $request->url;
            $platform = $request->platform;
            $app_description = $request->app_description;
            $create_at = date('Y-m-d H:i:s');

            $data = array('name' => $name,
                'image' => $app_image,//Application banner
                'app_logo_img' => $app_logo,//$app_logo,
                'url' => $url,
                'platform' => $platform,
                'app_description' => $app_description,
                'created_at' => $create_at);
            DB::beginTransaction();
            $advertise_link_id = DB::table('advertise_links')->insertGetId($data);

            DB::insert('insert into sub_category_advertise_links(sub_category_id, advertise_link_id, is_active, created_at) VALUES (?, ?, ?, ?)', [$sub_category_id, $advertise_link_id, 1, $create_at]);

            DB::commit();

            $response = Response::json(array('code' => 200, 'message' => 'Link Added Successfully.', 'cause' => '', 'data' => json_decode('{}')));
        } catch (Exception $e) {
            Log::error("addLink Error :", ['Error : ' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
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

//            if (!$request_body->has('request_data'))
//                return Response::json(array('code' => 201, 'message' => 'required field request_data is missing or empty', 'cause' => '', 'data' => json_decode("{}")));
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
                return Response::json(array('code' => 201, 'message' => 'required field request_data is missing or empty', 'cause' => '', 'data' => json_decode("{}")));

            $request = json_decode($request_body->input('request_data'));

            //Log::info("request data :", [$request]);
            if (($response = (new VerificationController())->validateRequiredParameter(array('advertise_link_id', 'name', 'platform', 'url', 'app_description'), $request)) != '')
                return $response;

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

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
                DB::update('update advertise_links
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
                (new ImageController())->saveImageInToSpaces($app_image);

                $result = DB::select('select image from advertise_links where id = ?', [$advertise_link_id]);
                $image_name = $result[0]->image;

                DB::update('update advertise_links
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
                (new ImageController())->saveImageInToSpaces($logo_image);

                $result = DB::select('select app_logo_img from advertise_links where id = ?', [$advertise_link_id]);
                $logo_image_name = $result[0]->app_logo_img;

                DB::update('update advertise_links
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
                (new ImageController())->saveImageInToSpaces($app_image);


                (new ImageController())->saveMultipleOriginalImage($logo_image, 'logo_file');
                (new ImageController())->saveCompressedImage($logo_image);
                (new ImageController())->saveThumbnailImage($logo_image);
                (new ImageController())->saveImageInToSpaces($logo_image);


                $result = DB::select('select image,app_logo_img from advertise_links where id = ?', [$advertise_link_id]);
                $image_name = $result[0]->image;
                $logo_image_name = $result[0]->app_logo_img;

                DB::update('update advertise_links
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

            $response = Response::json(array('code' => 200, 'message' => 'Link Updated Successfully.', 'cause' => '', 'data' => json_decode('{}')));
        } catch (Exception $e) {
            Log::error("updateLink Error :", ['Error : ' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'Update Link.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    public function updateLinkWithAppLogo(Request $request_body)
    {
        try {

            if (!$request_body->has('request_data'))
                return Response::json(array('code' => 201, 'message' => 'required field request_data is missing or empty', 'cause' => '', 'data' => json_decode("{}")));

            $request = json_decode($request_body->input('request_data'));

            //Log::info("request data :", [$request]);
            if (($response = (new VerificationController())->validateRequiredParameter(array('advertise_link_id', 'name', 'platform', 'url', 'app_description'), $request)) != '')
                return $response;

//            $token = JWTAuth::getToken();
//            JWTAuth::toUser($token);

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
                DB::update('update advertise_links
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
                (new ImageController())->saveImageInToSpaces($app_image);

                $result = DB::select('select image from advertise_links where id = ?', [$advertise_link_id]);
                $image_name = $result[0]->image;

                DB::update('update advertise_links
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
                (new ImageController())->saveImageInToSpaces($logo_image);

                $result = DB::select('select app_logo_img from advertise_links where id = ?', [$advertise_link_id]);
                $logo_image_name = $result[0]->app_logo_img;

                DB::update('update advertise_links
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
                (new ImageController())->saveImageInToSpaces($app_image);

                (new ImageController())->saveMultipleOriginalImage($logo_image, 'logo_file');
                (new ImageController())->saveCompressedImage($logo_image);
                (new ImageController())->saveThumbnailImage($logo_image);
                (new ImageController())->saveImageInToSpaces($logo_image);

                $result = DB::select('select image,app_logo_img from advertise_links where id = ?', [$advertise_link_id]);
                $image_name = $result[0]->image;
                $logo_image_name = $result[0]->app_logo_img;

                DB::update('update advertise_links
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

            $response = Response::json(array('code' => 200, 'message' => 'Link Updated Successfully.', 'cause' => '', 'data' => json_decode('{}')));
        } catch (Exception $e) {
            Log::error("updateLink Error :", ['Error : ' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'Update Link.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
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
            $request = json_decode($request_body->getContent());
            //Log::info("request data :", [$request]);
            if (($response = (new VerificationController())->validateRequiredParameter(array('advertise_link_id'), $request)) != '')
                return $response;

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $advertise_link_id = $request->advertise_link_id;

            DB::beginTransaction();

            //DB::delete('delete FROM sub_category_advertise_links WHERE advertise_link_id = ?', [$advertise_link_id]);

            DB::delete('delete FROM advertise_links where id = ? ', [$advertise_link_id]);

            DB::commit();

            $response = Response::json(array('code' => 200, 'message' => 'Link Deleted Successfully.', 'cause' => '', 'data' => json_decode('{}')));
        } catch (Exception $e) {
            Log::error("deleteLink Error :", ['Error : ' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'delete Link.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
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
            $request = json_decode($request_body->getContent());
            //Log::info([$request]);
            if (($response = (new VerificationController())->validateRequiredParameter(array('sub_category_id', 'page', 'item_count'), $request)) != '')
                return $response;

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

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
                                        IF(adl.image != "",CONCAT("' . Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",adl.imagee),"") as thumbnail_img,
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
            //->$this->sub_category_id
            $redis_result = Cache::get("getAllLink$this->page:$this->item_count:$this->sub_category_id");

            if (!$redis_result) {
                $redis_result = [];
            }

            $is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;

            $response = Response::json(array('code' => 200, 'message' => 'All Link Fetched Successfully.', 'cause' => '', 'data' => ['total_record' => $total_row, 'is_next_page' => $is_next_page, 'link_list' => $redis_result]));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            Log::error("getAllLink Error :", ['Error : ' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . ' Fetched All Link.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
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
     * "name": "Photo Editor Lab – Stickers , Filters & Frames",
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
            $request = json_decode($request_body->getContent());
            //Log::info([$request]);

            if (($response = (new VerificationController())->validateRequiredParameter(array('sub_category_id', 'platform'), $request)) != '')
                return $response;

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $this->sub_category_id = $request->sub_category_id;
            $this->platform = $request->platform;

            if (!Cache::has("pel:getAdvertiseLink:$this->sub_category_id-$this->platform")) {
                $result = Cache::rememberforever("getAdvertiseLink:$this->sub_category_id-$this->platform", function () {
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

            $redis_result = Cache::get("getAdvertiseLink:$this->sub_category_id-$this->platform");

            if (!$redis_result) {
                $redis_result = [];
            }

            $response = Response::json(array('code' => 200, 'message' => 'Advertise Link Fetched Successfully.', 'cause' => '', 'data' => ['link_list' => $redis_result]));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            Log::error("getAllAdvertiseLink Error :", ['Error : ' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . ' Fetched Advertise Link.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
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

            $request = json_decode($request->getContent());
            //Log::info("addAdvertiseLink Request :", [$request]);

            if (($response = (new VerificationController())->validateRequiredParameter(array('advertise_link_id', 'sub_category_id'), $request)) != '')
                return $response;

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $advertise_link_id = $request->advertise_link_id;
            $sub_category_id = $request->sub_category_id;

            $create_at = date('Y-m-d H:i:s');
            DB::beginTransaction();

            //return $catalog_id;
            DB::insert('insert into sub_category_advertise_links(sub_category_id,advertise_link_id,created_at) values (?, ?, ?)', [$sub_category_id, $advertise_link_id, $create_at]);

            DB::commit();

            $response = Response::json(array('code' => 200, 'message' => 'Advertise Linked Successfully.', 'cause' => '', 'data' => json_decode('{}')));
        } catch (Exception $e) {
            Log::error("addAdvertiseLink Error :", ['Error : ' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'Link Advertise.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
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
            $request = json_decode($request->getContent());
            //Log::info("unlinkAdvertise Request Data:", [$request]);
            if (($response = (new VerificationController())->validateRequiredParameter(array('advertise_link_id', 'sub_category_id'), $request)) != '')
                return $response;

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $advertise_link_id = $request->advertise_link_id;
            $sub_category_id = $request->sub_category_id;

            $is_active = 0;
            DB::beginTransaction();

            $result = DB::select('SELECT sub_category_id from sub_category_advertise_links WHERE sub_category_id = ? and is_active = 1', [$sub_category_id]);


            if (sizeOf($result) > 1) {
                DB::update('update sub_category_advertise_links set is_active = ? where sub_category_id = ? AND advertise_link_id = ? ', [$is_active, $sub_category_id, $advertise_link_id]);
                $response = Response::json(array('code' => 200, 'message' => 'Advertise unLinked Successfully!.', 'cause' => '', 'data' => json_decode('{}')));

            } else {
                $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'unLink this Advertise, it is not linked with any other app.', 'cause' => '', 'data' => json_decode("{}")));

            }


            DB::commit();

        } catch (Exception $e) {
            Log::error("unlinkAdvertise Error :", ['Error : ' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'unLinked Advertise.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
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
            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('sub_category_id', 'page', 'item_count'), $request)) != '')
                return $response;

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);
            $this->sub_category_id = $request->sub_category_id;
            $this->item_count = $request->item_count;
            $this->page = $request->page;
            $this->order_by = isset($request->order_by) ? $request->order_by : 'device_id';
            $this->order_type = isset($request->order_type) ? $request->order_type : 'DESC';
            $this->offset = ($this->page - 1) * $this->item_count;
            $total_row_result = DB::select('SELECT COUNT(*) as total FROM  device_master where sub_category_id = ?', [$this->sub_category_id]);
            $total_row = $total_row_result[0]->total;

            if (!Cache::has("pel:getAllUser$this->page:$this->item_count:$this->order_by:$this->order_type>$this->sub_category_id")) {
                $result = Cache::rememberforever("getAllUser$this->page:$this->item_count:$this->order_by:$this->order_type>$this->sub_category_id", function () {
                    return DB::select('SELECT
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
                });
            }

            $redis_result = Cache::get("getAllUser$this->page:$this->item_count:$this->order_by:$this->order_type>$this->sub_category_id");

            if (!$redis_result) {
                $redis_result = [];
            }


            $response = Response::json(array('code' => 200, 'message' => 'All User Fetched Successfully.', 'cause' => '', 'data' => ['total_record' => $total_row, 'list_user' => $redis_result]));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            Log::error("getAllUser Error :", ['Error : ' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . ' get all User.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
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
            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('sub_category_id', 'page', 'item_count'), $request)) != '')
                return $response;

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $this->sub_category_id = $request->sub_category_id;
            $this->item_count = $request->item_count;
            $this->page = $request->page;
            $this->order_by = isset($request->order_by) ? $request->order_by : 'id';
            $this->order_type = isset($request->order_type) ? $request->order_type : 'DESC';
            $this->offset = ($this->page - 1) * $this->item_count;

            $total_row_result = DB::select('SELECT COUNT(*) as total FROM  order_master where sub_category_id = ?', [$this->sub_category_id]);
            $total_row = $total_row_result[0]->total;
            if (!Cache::has("pel:getPurchaseUser$this->page:$this->item_count:$this->order_by:$this->order_type>$this->sub_category_id")) {
                $result = Cache::rememberforever("getPurchaseUser$this->page:$this->item_count:$this->order_by:$this->order_type>$this->sub_category_id", function () {
                    return DB::select('SELECT
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
                });
            }

            $redis_result = Cache::get("getPurchaseUser$this->page:$this->item_count:$this->order_by:$this->order_type>$this->sub_category_id");

            if (!$redis_result) {
                $redis_result = [];
            }
            $response = Response::json(array('code' => 200, 'message' => 'All Purchase User Fetched Successfully.', 'cause' => '', 'data' => ['total_record' => $total_row, 'list_user' => $redis_result]));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            Log::error("getPurchasaeUser Error :", ['Error : ' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'getUserProfile.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
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
            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('sub_category_id', 'item_count'), $request)) != '')
                return $response;

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $this->sub_category_id = $request->sub_category_id;
            $this->item_count = $request->item_count;
            $this->page = $request->page;
            $this->order_by = isset($request->order_by) ? $request->order_by : 'id';
            $this->order_type = isset($request->order_type) ? $request->order_type : 'DESC';
            $this->offset = ($this->page - 1) * $this->item_count;

            $total_row_result = DB::select('SELECT COUNT(*) as total FROM  restore_device where sub_category_id = ?', [$this->sub_category_id]);
            $total_row = $total_row_result[0]->total;

            if (!Cache::has("pel:getAllRestoreDevice$this->page:$this->item_count:$this->order_by:$this->order_type->$this->sub_category_id")) {
                $result = Cache::rememberforever("getAllRestoreDevice$this->page:$this->item_count:$this->order_by:$this->order_type->$this->sub_category_id", function () {
                    return DB::select('SELECT
                                    *
                                  FROM
                                  restore_device AS rd
                                  WHERE
                                  sub_category_id = ?
                                  ORDER BY rd.' . $this->order_by . ' ' . $this->order_type . '
                                  LIMIT ?,?', [$this->sub_category_id, $this->offset, $this->item_count]);
                });
            }

            $redis_result = Cache::get("getAllRestoreDevice$this->page:$this->item_count:$this->order_by:$this->order_type->$this->sub_category_id");

            if (!$redis_result) {
                $redis_result = [];
            }

            $response = Response::json(array('code' => 200, 'message' => 'App Restore Device Fetched Successfully.', 'cause' => '', 'data' => ['total_record' => $total_row, 'list_device' => $redis_result]));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            Log::error("getAllRestoreDevice Error :", ['Error : ' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'getAllRestoreDevice.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
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
            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('sub_category_id', 'search_type', 'search_query'), $request)) != '')
                return $response;

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

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


            $response = Response::json(array('code' => 200, 'message' => 'Search User Fetched Successfully.', 'cause' => '', 'data' => ['list_user' => $result]));

        } catch (Exception $e) {
            Log::error("searchUser Error :", ['Error : ' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . ' search user.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
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
            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('sub_category_id', 'search_type', 'search_query'), $request)) != '')
                return $response;

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

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


            $response = Response::json(array('code' => 200, 'message' => 'Purchase User Fetched Successfully.', 'cause' => '', 'data' => ['list_user' => $result]));

        } catch (Exception $e) {
            Log::error("searchPurchaseUser Error :", ['Error : ' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'search UserProfile.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
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
            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('sub_category_id', 'search_type', 'search_query'), $request)) != '')
                return $response;

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

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


            $response = Response::json(array('code' => 200, 'message' => 'App Restore Device Fetched Successfully.', 'cause' => '', 'data' => ['list_device' => $result]));

        } catch (Exception $e) {
            Log::error("searchRestoreDevice Error :", ['Error : ' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'search RestoreDevice.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
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
     * "response": ""
     * }
     */
    public function updateUserProfile(Request $request_body)
    {
        try {

            //Required parameter
            if (!$request_body->has('request_data'))
                return Response::json(array('code' => 201, 'message' => 'required field request_data is missing or empty', 'cause' => '', 'response' => json_decode("{}")));

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

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $user_data = JWTAuth::parseToken()->authenticate();
            $user_id = $user_data->email_id;

            if ($request_body->hasFile('file')) {
                $image_array = Input::file('file');

                if (($response = (new ImageController())->verifyImage($image_array)) != '')
                    return $response;

                $profile_img = (new ImageController())->generateNewFileName('profile_img', $image_array);
                //Log::info("file size :",[filesize($profile_img)]);
                (new ImageController())->saveOriginalImage($profile_img);
                (new ImageController())->saveCompressedImage($profile_img);
                (new ImageController())->saveThumbnailImage($profile_img);
                (new ImageController())->saveImageInToSpaces($profile_img);

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

            $response = Response::json(array('code' => 200, 'message' => 'Profile Updated Successfully.', 'cause' => '', 'response' => json_decode('{}')));

        } catch (Exception $e) {
            Log::error("update UserProfile Error :", ['error' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'updateUserProfile.', 'cause' => $e->getMessage(), 'response' => json_decode("{}")));
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

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('page', 'item_count'), $request)) != '')
                return $response;

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $this->item_count = $request->item_count;
            $this->page = $request->page;
            $this->order_by = isset($request->order_by) ? $request->order_by : 'size';
            $this->order_type = isset($request->order_type) ? $request->order_type : 'DESC';
            $this->offset = ($this->page - 1) * $this->item_count;

            $total_row_result = DB::select('SELECT COUNT(*) as total FROM image_details');
            $total_row = $total_row_result[0]->total;

            if (!Cache::has("pel:getImageDetails$this->page:$this->item_count:$this->order_by:$this->order_type")) {
                $result = Cache::rememberforever("getImageDetails$this->page:$this->item_count:$this->order_by:$this->order_type", function () {
                    return DB::select('SELECT
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
                });
            }

            $redis_result = Cache::get("getImageDetails$this->page:$this->item_count:$this->order_by:$this->order_type");

            if (!$redis_result) {
                $redis_result = [];
            }

            $response = Response::json(array('code' => 200, 'message' => 'All User Fetched Successfully.', 'cause' => '', 'data' => ['total_record' => $total_row, 'image_details' => $redis_result]));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            Log::error("getAllUser Error :", ['Error : ' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . ' Get Image Details.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
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

            if ($request_body->hasFile('file')) {
                $images_array = Input::file('file');
                //return $images_array;
                foreach ($images_array as $image_array) {

                    (new ImageController())->unlinkImage($image_array);


                    if (($response = (new ImageController())->verifyImage($image_array)) != '')
                        return $response;

                    (new ImageController())->saveResourceImage($image_array);

                    $bg_image = $image_array->getClientOriginalName();

                    $base_url = (new ImageController())->getBaseUrl();

                    $resourceFile = $base_url . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY') . $bg_image;


                    //return array($original_sourceFile,$compressed_sourceFile, $thumbnail_sourceFile);
                    $disk = Storage::disk('spaces');
                    $resourceFile_targetFile = "photoeditorlab/resource/" . $bg_image;


                    $disk->put($resourceFile_targetFile, file_get_contents($resourceFile), 'public');

                    (new ImageController())->unlinkfile($bg_image);
                }
            }

            $response = Response::json(array('code' => 200, 'message' => 'Json images added successfully.', 'cause' => '', 'data' => json_decode('{}')));

        } catch
        (Exception $e) {
            Log::error("addCatalogImagesForJson Error :", ['Error : ' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
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
            $request = json_decode($request_body->input('request_data'));

            if (($response = (new VerificationController())->validateRequiredParameter(array('catalog_id', 'is_featured', 'is_free'), $request)) != '')
                return $response;


            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

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
                return Response::json(array('code' => 201, 'message' => 'required field file is missing or empty', 'cause' => '', 'data' => json_decode("{}")));
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
                (new ImageController())->saveImageInToSpaces($catalog_image);


                DB::insert('INSERT
                                INTO
                                  images(catalog_id, image, json_data, is_free, is_featured, is_portrait, created_at)
                                VALUES(?, ?, ?, ?, ?, ?, ?) ', [$catalog_id, $catalog_image, json_encode($json_data), $is_free, $is_featured, $is_portrait, $created_at]);


                DB::commit();
            }


            $response = Response::json(array('code' => 200, 'message' => 'Json added successfully!.', 'cause' => '', 'data' => json_decode('{}')));

        } catch
        (Exception $e) {
            Log::error("addJson Error :", ['Error : ' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
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
    public function editJsonData(Request $request_body)
    {

        try {
            $request = json_decode($request_body->input('request_data'));

            if (($response = (new VerificationController())->validateRequiredParameter(array('is_featured', 'is_free', 'img_id'), $request)) != '')
                return $response;


            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

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
                //(new ImageController())->saveOriginalImage($background_img);

                $original_path = '../..' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY');
                $image_array->move($original_path, $catalog_image);

                (new ImageController())->saveCompressedImage($catalog_image);
                (new ImageController())->saveThumbnailImage($catalog_image);
                (new ImageController())->saveImageInToSpaces($catalog_image);

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
            Log::error("editJsonData Error :", ['Error : ' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
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

            $request = json_decode($request->getContent());
            //Log::info("linkAdvertisementWithSubCategory Request :", [$request]);

            if (($response = (new VerificationController())->validateRequiredParameter(array('advertise_link_id', 'sub_category_id'), $request)) != '')
                return $response;

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);
            //$query=DB::select('select * from sub_category_catalog WHERE sub_category_id = ? AND catalog_id = ?',[$sub_category_id,$catalog_id]);
            $advertise_link_id = $request->advertise_link_id;
            $sub_category_id = $request->sub_category_id;

            $create_at = date('Y-m-d H:i:s');
            //$is_ative = 1;
            DB::beginTransaction();


            //return $catalog_id;
            DB::insert('insert into sub_category_advertise_links(sub_category_id,advertise_link_id,created_at) values (?, ?, ?)', [$sub_category_id, $advertise_link_id, $create_at]);

            DB::commit();

            $response = Response::json(array('code' => 200, 'message' => 'Advertisement Linked Successfully.', 'cause' => '', 'data' => json_decode('{}')));
        } catch (Exception $e) {
            Log::error("linkAdvertisementWithSubCategory Error :", ['Error : ' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'link advertisement with sub_category..', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
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

            $request = json_decode($request->getContent());
            //Log::info("getAllAdvertisementForLinkAdvertisement Request :", [$request]);

            if (($response = (new VerificationController())->validateRequiredParameter(array('advertise_link_id', 'category_id'), $request)) != '')
                return $response;

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $this->advertise_link_id = $request->advertise_link_id;
            $this->category_id = $request->category_id;


            //return $total_row;

            if (!Cache::has("pel:getAllAdvertisementForLinkAdvertisement$this->advertise_link_id->$this->category_id")) {
                $result = Cache::rememberforever("getAllAdvertisementForLinkAdvertisement$this->advertise_link_id->$this->category_id", function () {

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

            $redis_result = Cache::get("getAllAdvertisementForLinkAdvertisement$this->advertise_link_id->$this->category_id");

            if (!$redis_result) {
                $redis_result = [];
            }

            $response = Response::json(array('code' => 200, 'message' => 'SubCategory Fetched Successfully.', 'cause' => '', 'data' => ['category_list' => $redis_result]));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            Log::error("getAllAdvertisementForLinkAdvertisement Error :", ['Error : ' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . ' get All Sub Category.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
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
            $request = json_decode($request->getContent());
            //Log::info("Request Data:", [$request]);
            if (($response = (new VerificationController())->validateRequiredParameter(array('advertise_link_id', 'sub_category_id'), $request)) != '')
                return $response;

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $advertise_link_id = $request->advertise_link_id;
            $sub_category_id = $request->sub_category_id;

            DB::beginTransaction();
//
//            $result = DB::select('SELECT count(*) as count_catalog from sub_category_advertise_links WHERE advertise_link_id = ? and is_active = 1', [$advertise_link_id]);
//
//            if ($result[0]->count_catalog > 1) {
            DB::delete('delete from sub_category_advertise_links where sub_category_id = ? AND advertise_link_id = ? ', [$sub_category_id, $advertise_link_id]);
            $response = Response::json(array('code' => 200, 'message' => 'Advertisement unlinked successfully!.', 'cause' => '', 'data' => json_decode('{}')));

//            } else {
//                $response = Response::json(array('code' => 201, 'message' => 'Unable to de-link this advertisement, it is not linked with any other application.', 'cause' => '', 'data' => json_decode("{}")));
//
//            }

            DB::commit();

        } catch (Exception $e) {
            Log::error("deleteLinkedAdvertisement Error :", ['Error : ' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
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
            //Log::info("getAllAdvertisement Request :", [$request]);

            if (($response = (new VerificationController())->validateRequiredParameter(array('page', 'item_count'), $request)) != '')
                return $response;

            $this->page = $request->page;
            $this->item_count = $request->item_count;
            //$item_count = Config::get('constant.PAGINATION_ITEM_LIMIT');
            $this->offset = ($this->page - 1) * $this->item_count;

            $total_row_result = DB::select('SELECT COUNT(*) as total FROM  advertise_links where is_active = ?', [1]);
            $total_row = $total_row_result[0]->total;

            //return $total_row;

            if (!Cache::has("pel:getAllAdvertisements$this->page:$this->item_count")) {
                $result = Cache::rememberforever("getAllAdvertisements$this->page:$this->item_count", function () {

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
                                          advertise_links as adl
                                        WHERE
                                          is_active=1
                                        order by adl.updated_at DESC LIMIT ?, ?', [$this->offset, $this->item_count]);
                });

            }

            $redis_result = Cache::get("getAllAdvertisements$this->page:$this->item_count");

            if (!$redis_result) {
                $redis_result = [];
            }

            $is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;

            $response = Response::json(array('code' => 200, 'message' => 'All Link Fetched Successfully.', 'cause' => '', 'data' => ['total_record' => $total_row, 'is_next_page' => $is_next_page, 'result' => $redis_result]));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            Log::error("getAllAdvertisements Error :", ['Error : ' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . ' get all advertisements.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
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

            $request = json_decode($request->getContent());
            //Log::info("getAllAdvertisementToLinkAdvertisement Request :", [$request]);

            if (($response = (new VerificationController())->validateRequiredParameter(array('sub_category_id'), $request)) != '')
                return $response;

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $this->sub_category_id = $request->sub_category_id;


            //return $total_row;

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
                                          if(adl.app_description!="",adl.app_description,"") as app_description,
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
            Log::error("getAllAdvertisementToLinkAdvertisement Error :", ['Error : ' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . ' get all advertisements.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
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
                foreach($sub_category_and_catalog as $key){

                    if ($key->sub_category_image) {

                        (new ImageController())->saveImageInToSpaces($key->sub_category_image);


                    }

                }

                if (count($sub_category_and_catalog) > 0) {

                    /*if ($sub_category_and_catalog[0]->sub_category_image) {

                        (new ImageController())->saveImageInToSpaces($sub_category_and_catalog[0]->sub_category_image);


                    }*/

                    if ($sub_category_and_catalog[0]->catalog_image) {

                        (new ImageController())->saveImageInToSpaces($sub_category_and_catalog[0]->catalog_image);
                    }

                }

                foreach ($result as $key) {


                    if ($key->image) {

                        (new ImageController())->saveImageInToSpaces($key->image);


                    }

                    if ($key->original_img) {

                        (new ImageController())->saveImageInToSpaces($key->original_img);


                    }

                    if ($key->display_img) {

                        (new ImageController())->saveImageInToSpaces($key->display_img);


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
            Log::error("addAppContentViaMigration Error :", ['Error : ' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'Add Catalog.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
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
            $response = Response::json(array('code' => 200, 'message' => 'Redis Keys Fetched Successfully.', 'cause' => '', 'data' => $result));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            Log::error("redisInfo Error :", ['Error : ' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'Get Redis-Cache Keys.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
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
            $request = json_decode($request_body->getContent());
            //Log::info("request data :", [$request]);

            if (($response = (new VerificationController())->validateRequiredParam(array('keys_list'), $request)) != '')
                return $response;

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);


            $keys = $request->keys_list;

            foreach ($keys as $rw) {
                if (($response = (new VerificationController())->validateRequiredParameter(array('key'), $rw)) != '')
                    return $response;
            }

            foreach ($keys as $key) {
                Redis::del($key->key);
            }
            $response = Response::json(array('code' => 200, 'message' => 'Redis Keys Deleted Successfully.', 'cause' => '', 'data' => '{}'));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            Log::error("redisInfo Error :", ['Error : ' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'Delete Redis Keys.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
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
            $request = json_decode($request_body->getContent());
            //Log::info("getRedisKeyDetails Request:", [$request]);
            if (($response = (new VerificationController())->validateRequiredParameter(array('key'), $request)) != '')
                return $response;

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $key = $request->key;
            $key_detail = \Illuminate\Support\Facades\Redis::get($key);
            //return $key_detail;
            $result = ['keys_detail' => unserialize($key_detail)];
            $response = Response::json(array('code' => 200, 'message' => 'Redis Key Detail Fetched Successfully.', 'cause' => '', 'data' => $result));
        } catch (Exception $e) {
            Log::error("getRedisKeyDetail Error :", ['Error : ' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'Get Redis-Cache Key Detail.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
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
            $response = Response::json(array('code' => 200, 'message' => 'Redis Keys Deleted Successfully.', 'cause' => '', 'data' => '{}'));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            Log::error("getRedisKeyDetail Error :", ['Error : ' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'Get Redis-Cache Key Detail.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

}
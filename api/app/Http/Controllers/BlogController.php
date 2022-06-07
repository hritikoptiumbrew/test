<?php

//////////////////////////////////////////////////////////////////////////////
//                   OptimumBrew Technology Pvt. Ltd.                       //
//                                                                          //
// Title:            Photo Editor Lab                                       //
// File:             BlogController.php                                     //
// Since:            26-June-2019                                           //
//                                                                          //
// Author:           Optimumbrew                                            //
// Email:            info@optimumbrew.com                                   //
//                                                                          //
//////////////////////////////////////////////////////////////////////////////


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Image;
use Exception;
use Response;
use Illuminate\Support\Facades\Config;
use File;
use Log;
use DB;
use Illuminate\Support\Facades\Input;
use Tymon\JWTAuth\Facades\JWTAuth;

class BlogController extends Controller
{
    /*================================| Admin |================================*/

    /**
     *
     * - Users ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/addBlogContent",
     *        tags={"Admin_Blog_content"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="addBlogContent",
     *        summary="Add blog content",
     *        consumes={"multipart/form-data" },
     *        produces={"application/json"},
     * 		@SWG\Parameter(
     *        in="header",
     *        name="Authorization",
     *        description="access token",
     *        required=true,
     *        type="string",
     *      ),
     *     @SWG\Parameter(
     *          in="formData",
     *          name="request_data",
     *          required=true,
     *          type="string",
     *          description="Give title, subtitle, blog_data in json object",
     *         @SWG\Schema(
     *              required={"catalog_id","platform","title","subtitle","blog_data"},
     *              @SWG\Property(property="catalog_id",  type="integer", example="895", description=""),
     *              @SWG\Property(property="platform",  type="integer", example="1", description="1=Android,2=Ios,3=Both"),
     *              @SWG\Property(property="title",  type="string", example="Title description", description=""),
     *              @SWG\Property(property="subtitle",  type="string", example="Subtitle description", description=""),
     *              @SWG\Property(property="blog_data",  type="object", example={}, description="blog json"),
     *              ),
     *     ),
     *     @SWG\Parameter(
     *         name="file",
     *         in="formData",
     *         description="Feature graphic image",
     *         required=true,
     *         type="file"
     *     ),
     *     @SWG\Response(
     *            response=200,
     *            description="Success",
     *        @SWG\Schema(
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":200,"message":"Blog content added successfully.","cause":"","data":"{}"}),),
     *        ),
     *     ),
     *      @SWG\Response(
     *            response=201,
     *            description="Error",
     *      )
     *
     */
    /**
     * @api {post} addBlogContent addBlogContent
     * @apiName addBlogContent
     * @apiGroup Admin-Blog_content
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     *  Key: Authorization
     *  Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * request_data:{
     * "platform":1,//1=Android,2=ios,3=both
     * "catalog_id":895,//compulsory
     * "title":"test", //compulsory
     * "subtitle":"demo", //compulsory
     * "blog_data":"<p></p>" //compulsory
     * }
     * file:ob.jpg //compulsory
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Blog content added successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function addBlogContent(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            if (!$request_body->has('request_data'))
                return Response::json(array('code' => 201, 'message' => 'Required field request_data is missing or empty.', 'cause' => '', 'data' => json_decode("{}")));

            $request = json_decode($request_body->input('request_data'));

            if (($response = (new VerificationController())->validateRequiredParameter(array('catalog_id','platform','title', 'subtitle', 'blog_data'), $request)) != '')
                return $response;

            $catalog_id = $request->catalog_id;
            $platform = $request->platform;
            $title = $request->title;
            $subtitle = $request->subtitle;
            $blog_data = $request->blog_data;
            $create_at = date('Y-m-d H:i:s');

            if (!$request_body->hasFile('file'))
                return Response::json(array('code' => 201, 'message' => 'Required field file is missing or empty.', 'cause' => '', 'data' => json_decode("{}")));

            $image_array = Input::file('file');

            /* Here we passed following parameters bcz blog is not related to any catalogs
             * category_id = 0
             * is_catalog = 0
             * is_featured = 1 bcz we manage validation for blog using this tag
             * */
            if (($response = (new ImageController())->verifyImage($image_array, 0, 1, 0)) != '')
                return $response;

            $blog_img = (new ImageController())->generateNewFileName('blog_image', $image_array);
            (new ImageController())->saveOriginalImage($blog_img);
            (new ImageController())->saveCompressedImage($blog_img);
            (new ImageController())->saveThumbnailImage($blog_img);

            $webp_blog_img = (new ImageController())->saveWebpOriginalImage($blog_img);
            $dimension = (new ImageController())->saveWebpThumbnailImage($blog_img);

            if (Config::get('constant.APP_ENV') != 'local') {
                (new ImageController())->saveImageInToS3($blog_img);
                (new ImageController())->saveWebpImageInToS3($webp_blog_img);
            }

            $title = json_encode($title);
            $subtitle = json_encode($subtitle);
            $blog_json = json_encode($request);
            DB::beginTransaction();

            DB::insert('INSERT INTO blog_master
                        (image,catalog_id,platform,webp_image,title,subtitle,blog_json,height, width, is_active,create_time) VALUES(?,?,?,?,?,?,?,?,?,?,?)',
                [$blog_img,$catalog_id,$platform,$webp_blog_img, $title, $subtitle, $blog_json, $dimension['height'], $dimension['width'], 1, $create_at]);

            DB::commit();

            $response = Response::json(array('code' => 200, 'message' => 'Blog content added successfully.', 'cause' => '', 'data' => json_decode('{}')));

        } catch (Exception $e) {
            Log::error("addBlogContent : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'add blog content.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /**
     *
     * - Users ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/updateBlogContent",
     *        tags={"Admin-Blog_content"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="updateBlogContent",
     *        summary="update blog content",
     *        consumes={"multipart/form-data" },
     *        produces={"application/json"},
     * 		@SWG\Parameter(
     *        in="header",
     *        name="Authorization",
     *        description="access token",
     *        required=true,
     *        type="string",
     *      ),
     *     @SWG\Parameter(
     *          in="formData",
     *          name="request_data",
     *          required=true,
     *          type="string",
     *          description="Give blog_id,fg_image,title, subtitle, blog_data in json object",
     *         @SWG\Schema(
     *              required={"blog_id","platform","fg_image","title","subtitle","blog_data"},
     *              @SWG\Property(property="blog_id",  type="integer", example=1, description=""),
     *              @SWG\Property(property="platform",  type="integer", example=1, description="1=Android,2=Ios,3=Both"),
     *              @SWG\Property(property="fg_image",  type="string", example="1.jpg", description=""),
     *              @SWG\Property(property="title",  type="string", example="Title description", description=""),
     *              @SWG\Property(property="subtitle",  type="string", example="Subtitle description", description=""),
     *              @SWG\Property(property="blog_data",  type="object", example={}, description="blog json"),
     *              ),
     *     ),
     *     @SWG\Parameter(
     *         name="file",
     *         in="formData",
     *         description="Feature graphic image",
     *         required=true,
     *         type="file"
     *     ),
     *     @SWG\Response(
     *            response=200,
     *            description="Success",
     *        @SWG\Schema(
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":200,"message":"Blog content updated successfully.","cause":"","data":"{}"}),),
     *        ),
     *      @SWG\Response(
     *            response=201,
     *            description="Error",
     *      ),
     *    )
     */

    /**
     * @api {post} updateBlogContent updateBlogContent
     * @apiName updateBlogContent
     * @apiGroup Admin-Blog_content
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     *  Key: Authorization
     *  Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * request_data:{
     * "blog_id":1,
     * "platform":1,//1=Android,2=ios,3=both
     * "title":"test",
     * "subtitle":"demo",
     * "blog_data":"<p></p>"
     * }
     * file:ob.jpg //optional
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Blog content updated successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function updateBlogContent(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            if (!$request_body->has('request_data'))
                return Response::json(array('code' => 201, 'message' => 'Required field request_data is missing or empty.', 'cause' => '', 'data' => json_decode("{}")));

            $request = json_decode($request_body->input('request_data'));

            if (($response = (new VerificationController())->validateRequiredParameter(array('blog_id','platform', 'title', 'subtitle', 'blog_data', 'fg_image'), $request)) != '')
                return $response;
            $blog_img = NULL;

            $title = $request->title;
            $platform = $request->platform;
            $subtitle = $request->subtitle;
            $blog_data = $request->blog_data;
            $old_original_img = $request->fg_image;
            $dimension['height'] = "";
            $dimension['width'] = "";
            $webp_blog_img = "";

            $blog_id = intval($request->blog_id);
            $blog_json = json_encode($request);

            //Remove blog_id into json request
            $blog_json = str_replace("\"blog_id\":$blog_id,", "", $blog_json);

            if ($request_body->hasFile('file')) {

                $image_array = Input::file('file');

                /* Here we passed following parameters bcz blog is not related to any catalogs
                 * category_id = 0
                 * is_catalog = 0
                 * is_featured = 1 bcz we manage validation for blog using this tag
                 * */
                if (($response = (new ImageController())->verifyImage($image_array, 0, 1, 0)) != '')
                    return $response;

                $blog_img = (new ImageController())->generateNewFileName('blog_image', $image_array);
                (new ImageController())->saveOriginalImage($blog_img);
                (new ImageController())->saveCompressedImage($blog_img);
                (new ImageController())->saveThumbnailImage($blog_img);

                $webp_blog_img = (new ImageController())->saveWebpOriginalImage($blog_img);
                $dimension = (new ImageController())->saveWebpThumbnailImage($blog_img);

                if (Config::get('constant.APP_ENV') != 'local') {
                    (new ImageController())->saveImageInToS3($blog_img);
                    (new ImageController())->saveWebpImageInToS3($webp_blog_img);
                }

                if ($old_original_img) {
                    (new ImageController())->deleteImage($old_original_img);
                }
            }

            $title = json_encode($title);
            $subtitle = json_encode($subtitle);

            DB::beginTransaction();

            DB::update('UPDATE blog_master
                        SET image = IF(? != "",?,image),
                            webp_image = IF(? != "",?,webp_image),
                            title = ?,
                            subtitle = ?,
                            blog_json = ?,
                            platform = IF(? !="",?,platform),
                            height = IF(? != "",?,height),
                            width = IF(? != "",?,width)
                            WHERE id = ?',
                [$blog_img, $blog_img,$webp_blog_img,$webp_blog_img, $title, $subtitle, $blog_json,$platform,$platform, $dimension['height'], $dimension['height'],
                    $dimension['width'], $dimension['width'], $blog_id]);

            DB::commit();

            $response = Response::json(array('code' => 200, 'message' => 'Blog content updated successfully.', 'cause' => '', 'data' => json_decode('{}')));

        } catch (Exception $e) {
            Log::error("updateBlogContent : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'update blog content.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /**
     *
     * - Users ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/deleteBlogContent",
     *        tags={"Admin-Blog_content"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="deleteBlogContent",
     *        summary="Delete blog content",
     *        produces={"application/json"},
     * 		@SWG\Parameter(
     *        in="header",
     *        name="Authorization",
     *        description="access token",
     *        required=true,
     *        type="string",
     *      ),
     * 		@SWG\Parameter(
     *        in="body",
     *        name="request_body",
     *   	  @SWG\Schema(
     *          required={"blog_id","fg_image"},
     *          @SWG\Property(property="blog_id",  type="integer", example=1, description=""),
     *          @SWG\Property(property="fg_image",  type="string", example="1.jpg", description=""),
     *        ),
     *      ),
     * 		@SWG\Response(
     *            response=200,
     *            description="Success",
     *        @SWG\Schema(
     *          @SWG\Property(property="Sample Response",  type="string", example={"code":200,"message":"Blog deleted successfully.","cause":"","data":{}}, description=""),),
     *        ),
     * 		@SWG\Response(
     *            response=201,
     *            description="error",
     *        ),
     *    )
     *
     */
    /**
     * @api {post} deleteBlogContent deleteBlogContent
     * @apiName deleteBlogContent
     * @apiGroup Admin-Blog_content
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     *  Key: Authorization
     *  Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "blog_id":1
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Blog deleted successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function deleteBlogContent(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('blog_id', 'fg_image'), $request)) != '')
                return $response;

            $blog_id = intval($request->blog_id);
            $fg_image = $request->fg_image;


            DB::beginTransaction();

            DB::delete('DELETE FROM blog_master WHERE id = ? ', [$blog_id]);

            DB::commit();

            if ($fg_image) {
                (new ImageController())->deleteImage($fg_image);
            }

            $response = Response::json(array('code' => 200, 'message' => 'Blog deleted successfully.', 'cause' => '', 'data' => json_decode('{}')));

        } catch (Exception $e) {
            Log::error("deleteBlogContent : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'delete blog content.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /**
     *
     * - Users ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/getBlogContent",
     *        tags={"Admin-Blog_content"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="getBlogContent",
     *        summary="getBlogContent",
     *        produces={"application/json"},
     * 		@SWG\Parameter(
     *        in="header",
     *        name="Authorization",
     *        description="access token",
     *        required=true,
     *        type="string",
     *      ),
     * 		@SWG\Parameter(
     *        in="body",
     *        name="request_body",
     *   	  @SWG\Schema(
     *          required={"catalog_id","page","item_count"},
     *          @SWG\Property(property="catalog_id",  type="integer", example=895, description=""),
     *          @SWG\Property(property="page",  type="integer", example=1, description=""),
     *          @SWG\Property(property="item_count",  type="integer", example=4, description=""),
     *        ),
     *      ),
     * 		@SWG\Response(
     *            response=200,
     *            description="Success",
     *        @SWG\Schema(
     *          @SWG\Property(property="Sample Response",  type="string", example={"code":200,"message":"Blog content fetched successfully.","cause":"","data":{"total_record":5,"is_next_page":true,"result":{{"blog_id":5,"fg_image":"5d12078922b96_blog_image_1561462665.png","thumbnail_img":"http://192.168.0.114/photo_editor_lab_backend/image_bucket/thumbnail/5d12078922b96_blog_image_1561462665.png","compressed_img":"http://192.168.0.114/photo_editor_lab_backend/image_bucket/compressed/5d12078922b96_blog_image_1561462665.png","original_img":"http://192.168.0.114/photo_editor_lab_backend/image_bucket/original/5d12078922b96_blog_image_1561462665.png","blog_json":{"title":"Content Data","subtitle":"Description","blog_data":"<div><\/div>"},"is_active":1}}}}, description=""),),
     *        ),
     * 		@SWG\Response(
     *            response=201,
     *            description="error",
     *        ),
     *    )
     *
     */
    /**
     * @api {post} getBlogContent getBlogContent
     * @apiName getBlogContent
     * @apiGroup Admin-Blog_content
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     *  Key: Authorization
     *  Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "catalog_id":895,
     * "page":1,
     * "item_count":10
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Blog content fetched successfully.",
     * "cause": "",
     * "data": {
     * "total_record": 9,
     * "is_next_page": false,
     * "result": [
     * {
     * "blog_id": 26,
     * "fg_image": "5d26bbe50f627_blog_image_1562819557.png",
     * "thumbnail_img": "http://192.168.0.114/videoflyer_backend/image_bucket/thumbnail/5d26bbe50f627_blog_image_1562819557.png",
     * "compressed_img": "http://192.168.0.114/videoflyer_backend/image_bucket/compressed/5d26bbe50f627_blog_image_1562819557.png",
     * "original_img": "http://192.168.0.114/videoflyer_backend/image_bucket/original/5d26bbe50f627_blog_image_1562819557.png",
     * "webp_original_img": "http://192.168.0.114/videoflyer_backend/image_bucket/webp_original/5d26bbe50f627_blog_image_1562819557.png",
     * "webp_thumbnail_img": "http://192.168.0.114/videoflyer_backend/image_bucket/webp_thumbnail/5d26bbe50f627_blog_image_1562819557.png",
     * "title": "{\"text_color\":\"#000000\",\"text_size\":16,\"text_value\":\"test\"}",
     * "subtitle": "{\"text_color\":\"#000000\",\"text_size\":36,\"text_value\":\"test\"}",
     * "blog_json": "{\"title\":{\"text_color\":\"#000000\",\"text_size\":16,\"text_value\":\"test\"},\"subtitle\":{\"text_color\":\"#000000\",\"text_size\":36,\"text_value\":\"test\"},\"blog_data\":\"test<style>.sttc-button {font-family: -apple-system, system-ui, BlinkMacSystemFont, \\\"Segoe UI\\\", Roboto, \\\"Helvetica Neue\\\", Arial, sans-serif; font-size: 1rem; font-weight: 500; border: none; border-radius: 4px; box-shadow: none; color: #ffffff; cursor: pointer; display: inline-block; margin: 0px; padding: 8px 18px; text-decoration: none; background-color: #ffcd00; overflow-wrap: break-word; user-select:none !important;}.sttc-button:hover, .sttc-button:focus{ background-color: #d9ae00;}<\\/style><script>function OpenTemplate(templateID) { alert(\\\"OpenTemplate - \\\" + templateID); } function searchTemplate(searchTag) { alert(\\\"searchTemplate - \\\" + searchTag); } <\\/script>\",\"fg_image\":\"5d26bbe50f627_blog_image_1562819557.png\"}",
     * "catalog_id":895,
     * "platform": 1,
     * "is_active": 1
     * }
     * ]
     * }
     * }
     */
    public function getBlogContent(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('catalog_id','page', 'item_count'), $request)) != '')
                return $response;

            $this->catalog_id = $request->catalog_id;
            $this->page = $request->page;
            $this->item_count = $request->item_count;
            $this->offset = ($this->page - 1) * $this->item_count;

            if (!Cache::has("pel:getBlogContent$this->page:$this->item_count:$this->catalog_id")) {
                $result = Cache::rememberforever("getBlogContent$this->page:$this->item_count:$this->catalog_id", function () {

                    $total_row_result = DB::select('SELECT COUNT(*) as total FROM  blog_master WHERE is_active = ? AND catalog_id =?', [1,$this->catalog_id]);
                    $total_row = $total_row_result[0]->total;

                    $result = DB::select('SELECT 
                                          id AS blog_id,
                                          image AS fg_image,
                                          IF(image != "",CONCAT("' . Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",image),"") as thumbnail_img,
                                          IF(image != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",image),"") as compressed_img,
                                          IF(webp_image != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",webp_image),"") as webp_original_img,
                                          IF(webp_image != "",CONCAT("' . Config::get('constant.WEBP_THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",webp_image),"") as webp_thumbnail_img,
                                          title,
                                          subtitle,
                                          blog_json,
                                          catalog_id,
                                          platform,
                                          is_active
                                      FROM  blog_master 
                                      WHERE is_active = ? AND
                                      catalog_id =? 
                                      ORDER BY update_time DESC 
                                      LIMIT ?, ?', [1,$this->catalog_id,$this->offset, $this->item_count]);

                    $is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;
                    return array('total_record' => $total_row, 'is_next_page' => $is_next_page, 'result' => $result);

                });
            }

            $redis_result = Cache::get("getBlogContent$this->page:$this->item_count:$this->catalog_id");

            if (!$redis_result) {
                $redis_result = [];
            }

            $response = Response::json(array('code' => 200, 'message' => 'Blog content fetched successfully.', 'cause' => '', 'data' => $redis_result));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            Log::error("getBlogContent : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'fetched blog content.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    /**
     *
     * - Users ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/setBlogRankOnTheTopByAdmin",
     *        tags={"Admin-Blog_content"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="setBlogRankOnTheTopByAdmin",
     *        summary="setBlogRankOnTheTopByAdmin",
     *        produces={"application/json"},
     * 		@SWG\Parameter(
     *        in="header",
     *        name="Authorization",
     *        description="access token",
     *        required=true,
     *        type="string",
     *      ),
     * 		@SWG\Parameter(
     *        in="body",
     *        name="request_body",
     *   	  @SWG\Schema(
     *          required={"blog_id"},
     *          @SWG\Property(property="blog_id",  type="integer", example=1, description=""),
     *        ),
     *      ),
     * 		@SWG\Response(
     *            response=200,
     *            description="Success",
     *        @SWG\Schema(
     *          @SWG\Property(property="Sample Response",  type="string", example={"code":200,"message":"Blog content rank set successfully.","cause":"","data":{}}, description=""),),
     *        ),
     * 		@SWG\Response(
     *            response=201,
     *            description="error",
     *        ),
     *    )
     *
     */
    /**
     * @api {post} setBlogRankOnTheTopByAdmin setBlogRankOnTheTopByAdmin
     * @apiName setBlogRankOnTheTopByAdmin
     * @apiGroup Admin-Blog_content
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     *  Key: Authorization
     *  Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "blog_id":10
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Blog content rank set successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function setBlogRankOnTheTopByAdmin(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('blog_id'), $request)) != '')
                return $response;


            $blog_id = $request->blog_id;
            $create_at = date('Y-m-d H:i:s');

            DB::beginTransaction();

            DB::update('UPDATE blog_master
                        SET update_time = ?
                            WHERE id = ?',
                [$create_at, $blog_id]);

            DB::commit();


            $response = Response::json(array('code' => 200, 'message' => 'Blog content rank set successfully.', 'cause' => '','data' => json_decode("{}")));

        } catch (Exception $e) {
            Log::error("setBlogRankOnTheTopByAdmin : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'set blog rank by admin.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    /*================================| User |================================*/

    //Blog list with pagination
    /**
     *
     * - Users ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/getBlogListByUser",
     *        tags={"Users_blog"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="getBlogListByUser",
     *        summary="getBlogListByUser",
     *        produces={"application/json"},
     * 		@SWG\Parameter(
     *        in="header",
     *        name="Authorization",
     *        description="access token",
     *        required=true,
     *        type="string",
     *      ),
     * 		@SWG\Parameter(
     *        in="body",
     *        name="request_body",
     *   	  @SWG\Schema(
     *          required={"catalog_id","platform","page","item_count"},
     *          @SWG\Property(property="catalog_id",  type="integer", example=895, description=""),
     *          @SWG\Property(property="platform",  type="integer", example=1, description="1=android,2=ios"),
     *         @SWG\Property(property="page",  type="integer", example=1, description=""),
     *          @SWG\Property(property="item_count",  type="integer", example=4, description=""),
     *        ),
     *      ),
     * 		@SWG\Response(
     *            response=200,
     *            description="Success",
     *        @SWG\Schema(
     *          @SWG\Property(property="Sample Response",  type="string", example={"code":200,"message":"Blog content fetched successfully.","cause":"","data":{"total_record":9,"is_next_page":false,"result":{{"blog_id":23,"thumbnail_img":"http://192.168.0.114/videoflyer_backend/image_bucket/thumbnail/5d1b4e68c98e2_blog_image_1562070632.jpg","compressed_img":"http://192.168.0.114/videoflyer_backend/image_bucket/compressed/5d1b4e68c98e2_blog_image_1562070632.jpg","original_img":"http://192.168.0.114/videoflyer_backend/image_bucket/original/5d1b4e68c98e2_blog_image_1562070632.jpg","webp_original_img":"http://192.168.0.114/videoflyer_backend/image_bucket/webp_original/5d1b4e68c98e2_blog_image_1562070632.jpg","webp_thumbnail_img":"http://192.168.0.114/videoflyer_backend/image_bucket/webp_thumbnail/5d1b4e68c98e2_blog_image_1562070632.jpg","title":{"text_color":"#ff8040","text_size":16,"text_value":"CONTENT OF THE WEEK | JUNE 28, 2019 "},"subtitle":{"text_color":"#000000\","text_size":36,"text_value":"Share the love with the Beth Ellen font"},"catalog_id":895,"is_active":1}}}}, description=""),),
     *        ),
     * 		@SWG\Response(
     *            response=201,
     *            description="error",
     *        ),
     *    )
     *
     */
    /**
     * @api {post} getBlogListByUser getBlogListByUser
     * @apiName getBlogListByUser
     * @apiGroup 1User-Blog_content
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     *  Key: Authorization
     *  Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "platform"=2,//1=Android,2=IOS
     * "catalog_id":895,
     * "page":1,
     * "item_count":10
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Blog content fetched successfully.",
     * "cause": "",
     * "data": {
     * "total_record": 10,
     * "is_next_page": true,
     * "result": [
     * {
     * "blog_id": 27,
     * "thumbnail_img": "http://192.168.0.113/videoflyer_backend/image_bucket/thumbnail/5d26bff8efadc_blog_image_1562820600.png",
     * "compressed_img": "http://192.168.0.113/videoflyer_backend/image_bucket/compressed/5d26bff8efadc_blog_image_1562820600.png",
     * "original_img": "http://192.168.0.113/videoflyer_backend/image_bucket/original/5d26bff8efadc_blog_image_1562820600.png",
     * "webp_original_img": "http://192.168.0.113/videoflyer_backend/image_bucket/webp_original/5d26bff8efadc_blog_image_1562820600.png",
     * "webp_thumbnail_img": "http://192.168.0.113/videoflyer_backend/image_bucket/webp_thumbnail/5d26bff8efadc_blog_image_1562820600.png",
     * "height": 256,
     * "width": 256,
     * "title": "{\"text_color\":\"#000000\",\"text_size\":16,\"text_value\":\"test\"}",
     * "subtitle": "{\"text_color\":\"#000000\",\"text_size\":36,\"text_value\":\"subtitle\"}",
     * "catalog_id":895,
     * "is_active": 1
     * }
     * ]
     * }
     * }
     */
    public function getBlogListByUser(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('page', 'item_count','catalog_id','platform'), $request)) != '')
                return $response;

            $this->catalog_id = $request->catalog_id;
            $this->platform = $request->platform;
            $this->page = $request->page;
            $this->item_count = $request->item_count;
            $this->offset = ($this->page - 1) * $this->item_count;
            $is_cache_enable = isset($request->is_cache_enable) ? $request->is_cache_enable : 1;

            if ($is_cache_enable) {
                $redis_result = Cache::remember("getBlogListByUser:$this->page:$this->item_count:$this->catalog_id:$this->platform", Config::get("constant.CACHE_TIME_24_HOUR"), function () {

                    $total_row_result = DB::select('SELECT COUNT(*) as total FROM  blog_master WHERE is_active = ? AND catalog_id = ? AND platform IN(?,?) ', [1,$this->catalog_id,$this->platform,3]);
                    $total_row = $total_row_result[0]->total;

                    $result = DB::select('SELECT 
                                          id AS blog_id,
                                          IF(image != "",CONCAT("' . Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",image),"") as thumbnail_img,
                                          IF(image != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",image),"") as compressed_img,
                                          IF(image != "",CONCAT("' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",image),"") as original_img,
                                          IF(webp_image != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",webp_image),"") as webp_original_img,
                                          IF(webp_image != "",CONCAT("' . Config::get('constant.WEBP_THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",webp_image),"") as webp_thumbnail_img,
                                          height,
                                          width,
                                          title,
                                          subtitle,
                                          catalog_id,
                                          is_active
                                      FROM  blog_master 
                                      WHERE is_active = ? AND
                                      catalog_id =? AND
                                      platform IN(?,?)
                                      ORDER BY update_time DESC 
                                      LIMIT ?, ?', [1,$this->catalog_id,$this->platform,3,$this->offset, $this->item_count]);

                    $is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;
                    return array('total_record' => $total_row, 'is_next_page' => $is_next_page, 'result' => $result);

                });

            } else {

                $total_row_result = DB::select('SELECT COUNT(*) as total FROM  blog_master WHERE is_active = ? AND catalog_id = ? AND platform IN(?,?) ', [1,$this->catalog_id,$this->platform,3]);
                $total_row = $total_row_result[0]->total;

                $result = DB::select('SELECT 
                                          id AS blog_id,
                                          IF(image != "",CONCAT("' . Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",image),"") as thumbnail_img,
                                          IF(image != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",image),"") as compressed_img,
                                          IF(image != "",CONCAT("' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",image),"") as original_img,
                                          IF(webp_image != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",webp_image),"") as webp_original_img,
                                          IF(webp_image != "",CONCAT("' . Config::get('constant.WEBP_THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",webp_image),"") as webp_thumbnail_img,
                                          height,
                                          width,
                                          title,
                                          subtitle,
                                          catalog_id,
                                          is_active
                                      FROM  blog_master 
                                      WHERE is_active = ? AND
                                      catalog_id =? AND
                                      platform IN(?,?)
                                      ORDER BY update_time DESC 
                                      LIMIT ?, ?', [1,$this->catalog_id,$this->platform,3,$this->offset, $this->item_count]);

                $is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;
                $redis_result = array('total_record' => $total_row, 'is_next_page' => $is_next_page, 'result' => $result);

            }

            $response = Response::json(array('code' => 200, 'message' => 'Blog content fetched successfully.', 'cause' => '', 'data' => $redis_result));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            Log::error("getBlogListByUser : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get blog list by user.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    //Blog info by blog id
    /**
     *
     * - Users ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/getBlogContentByIdForUser",
     *        tags={"Users_blog"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="getBlogContentByIdForUser",
     *        summary="getBlogContentByIdForUser",
     *        produces={"application/json"},
     * 		@SWG\Parameter(
     *        in="header",
     *        name="Authorization",
     *        description="access token",
     *        required=true,
     *        type="string",
     *      ),
     * 		@SWG\Parameter(
     *        in="body",
     *        name="request_body",
     *   	  @SWG\Schema(
     *          required={"blog_id"},
     *          @SWG\Property(property="blog_id",  type="integer", example=1, description=""),
     *        ),
     *      ),
     * 		@SWG\Response(
     *            response=200,
     *            description="Success",
     *        @SWG\Schema(
     *          @SWG\Property(property="Sample Response",  type="string", example={"code":200,"message":"Blog content fetched successfully.","cause":"","data":{"total_record":9,"is_next_page":false,"result":{{"blog_id":23,"thumbnail_img":"http://192.168.0.114/videoflyer_backend/image_bucket/thumbnail/5d1b4e68c98e2_blog_image_1562070632.jpg","compressed_img":"http://192.168.0.114/videoflyer_backend/image_bucket/compressed/5d1b4e68c98e2_blog_image_1562070632.jpg","original_img":"http://192.168.0.114/videoflyer_backend/image_bucket/original/5d1b4e68c98e2_blog_image_1562070632.jpg","webp_original_img":"http://192.168.0.114/videoflyer_backend/image_bucket/webp_original/5d1b4e68c98e2_blog_image_1562070632.jpg","webp_thumbnail_img":"http://192.168.0.114/videoflyer_backend/image_bucket/webp_thumbnail/5d1b4e68c98e2_blog_image_1562070632.jpg","title":{"text_color":"#ff8040","text_size":16,"text_value":"CONTENT OF THE WEEK | JUNE 28, 2019 "},"subtitle":{"text_color":"#000000","text_size":36,"text_value":"Share the love with the Beth Ellen font"},"is_active":1}}}}, description=""),),
     *        ),
     * 		@SWG\Response(
     *            response=201,
     *            description="error",
     *        ),
     *    )
     *
     */
    /**
     * @api {post} getBlogContentByIdForUser getBlogContentByIdForUser
     * @apiName getBlogContentByIdForUser
     * @apiGroup 1User-Blog_content
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     *  Key: Authorization
     *  Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "blog_id":1
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Blog content fetched successfully.",
     * "cause": "",
     * "data": [
     * {
     * "blog_id": 23,
     * "thumbnail_img": "http://192.168.0.114/videoflyer_backend/image_bucket/thumbnail/5d26d844c218c_blog_image_1562826820.jpg",
     * "compressed_img": "http://192.168.0.114/videoflyer_backend/image_bucket/compressed/5d26d844c218c_blog_image_1562826820.jpg",
     * "original_img": "http://192.168.0.114/videoflyer_backend/image_bucket/original/5d26d844c218c_blog_image_1562826820.jpg",
     * "webp_original_img": "http://192.168.0.114/videoflyer_backend/image_bucket/webp_original/5d26d844c218c_blog_image_1562826820.webp",
     * "webp_thumbnail_img": "http://192.168.0.114/videoflyer_backend/image_bucket/webp_thumbnail/5d26d844c218c_blog_image_1562826820.webp",
     * "height": 205,
     * "width": 460,
     * "title": "{\"text_color\":\"#ff8040\",\"text_size\":16,\"text_value\":\"CONTENT OF THE WEEK | JUNE 28, 2019 \"}",
     * "subtitle": "{\"text_color\":\"#000000\",\"text_size\":36,\"text_value\":\"Share the love with the Beth Ellen font\"}",
     * "blog_json": "{\"title\":{\"text_color\":\"#ff8040\",\"text_size\":16,\"text_value\":\"CONTENT OF THE WEEK | JUNE 28, 2019 \"},\"subtitle\":{\"text_color\":\"#000000\",\"text_size\":36,\"text_value\":\"Share the love with the Beth Ellen font\"},\"blog_data\":\"<div class=\\\"blog-content-container w-container\\\" style=\\\"margin-left: auto; margin-right: auto; max-width: 820px; margin-bottom: 0px; padding-right: 48px; padding-left: 48px; color: rgb(154, 142, 144); font-family: &quot;Proxima soft&quot;, sans-serif; font-size: 20px;\\\"><div class=\\\"blog-rich-text w-richtext\\\" style=\\\"max-width: none; margin-bottom: 0px;\\\"><p data-w-id=\\\"22c9a481-8fcd-41e6-58b6-2557f5185c67\\\" style=\\\"margin-bottom: 32px; color: rgb(103, 94, 96); line-height: 32px;\\\"><span data-w-id=\\\"ca7b771b-5085-17f2-9436-55a954d2d2d1\\\" style=\\\"font-weight: 700;\\\"><span data-w-id=\\\"256615da-5297-05cc-1fcc-5d117d06336c\\\" style=\\\"color: rgb(255, 207, 0);\\\">At Over HQ, we feel so blessed<\\/span><\\/span>&nbsp;to have such a wildly talented community. People who find beauty in the darkness, and inspire the world by sharing their own unique story. &nbsp;<br data-w-id=\\\"4146cb54-c93e-83f0-fd6c-72864595f2cc\\\"><\\/p><p data-w-id=\\\"83868078-48d0-7ddd-a395-b8918c02f15b\\\" style=\\\"margin-bottom: 32px; color: rgb(103, 94, 96); line-height: 32px;\\\">That's exactly what Over Artist&nbsp;<a data-w-id=\\\"500bd531-3a4f-03ad-76ef-84f1bee4174b\\\" href=\\\"http:\\/\\/robjelinskistudios.com\\/\\\" data-rt-link-type=\\\"external\\\" style=\\\"color: rgb(72, 66, 67); text-decoration: none;\\\">Rob Jelinski<\\/a>&nbsp;did when he created a font in his mom\\u2019s handwriting, to honor her after she passed away. &nbsp;<br data-w-id=\\\"09eb3989-c1c0-792b-9349-0838955e7c37\\\"><\\/p><p data-w-id=\\\"7018f834-79c6-438c-c240-257118722874\\\" style=\\\"margin-bottom: 32px; color: rgb(103, 94, 96); line-height: 32px;\\\">As an artist and graphic designer, Rob wanted to share the Beth Ellen font with the world and bring it to life in the Over app, so that his mom's memory would live on forever.&nbsp;<br data-w-id=\\\"088ad354-17e6-b285-ee43-98bcffc5e39c\\\"><\\/p><blockquote data-w-id=\\\"2d4c6bf4-047c-4a55-edda-f269f3c34729\\\" style=\\\"margin-top: 36px; margin-bottom: 36px; color: rgb(86, 79, 80); max-width: 720px; padding: 0px 20px 0px 36px; border-left: 5px solid rgb(255, 207, 0); font-size: 36px; line-height: 54px;\\\">My single request is that you help the legacy of Beth Ellen live on by sending a short note to someone you love each time the font is used.&nbsp;<br data-w-id=\\\"b2a8b15a-fa4f-f616-96f4-ca0d488dc733\\\"><\\/blockquote><p data-w-id=\\\"a4c898d5-f34e-9c95-e7e7-2499f5cb1dfb\\\" style=\\\"margin-bottom: 32px; color: rgb(103, 94, 96); line-height: 32px;\\\">So send a little love to someone today! \\ud83d\\udc95<br data-w-id=\\\"75a95d8d-1347-aa7b-20ae-0b776eee9545\\\"><\\/p><p data-w-id=\\\"92163f65-699a-31ab-11be-0896ed7dc813\\\" style=\\\"margin-bottom: 32px; color: rgb(103, 94, 96); line-height: 32px;\\\">And if you need some help getting started, here are our some of our fave heartfelt quotes expressing love and gratitude \\u2013 all featuring the Beth Ellen font. &nbsp;<\\/p><figure data-w-id=\\\"a1158060-9b50-4df6-cc9d-4591ec52ac51\\\" class=\\\"w-richtext-figure-type-image w-richtext-align-normal\\\" data-rt-type=\\\"image\\\" data-rt-align=\\\"normal\\\" style=\\\"display: table; margin-top: 16px; margin-bottom: 16px; position: relative; max-width: 60%; clear: both;\\\"><div data-w-id=\\\"2776b881-5753-2b87-b01b-6c8c37b508ea\\\" style=\\\"font-size: 0px; color: transparent; display: inline-block;\\\"><img data-w-id=\\\"b33107d4-8e06-99cf-ca9d-7213caa5063e\\\" src=\\\"https:\\/\\/assets-global.website-files.com\\/5c7e6a9e0899242e0da98296\\/5d16291ad639969cb99c193f_IMG_0517.jpeg\\\" style=\\\"border: 0px; max-width: 100%; display: inline-block; width: 434px;\\\"><\\/div><\\/figure><figure data-w-id=\\\"41be35ee-2ca1-c46d-cedf-41886418990f\\\" class=\\\"w-richtext-figure-type-image w-richtext-align-normal\\\" data-rt-type=\\\"image\\\" data-rt-align=\\\"normal\\\" style=\\\"display: table; margin-top: 16px; margin-bottom: 16px; position: relative; max-width: 60%; clear: both;\\\"><div data-w-id=\\\"7c31acb5-c799-708f-3314-8128518151b2\\\" style=\\\"font-size: 0px; color: transparent; display: inline-block;\\\"><img data-w-id=\\\"b185eae1-43a8-a3d0-556e-e5d9d0a6853f\\\" src=\\\"https:\\/\\/assets-global.website-files.com\\/5c7e6a9e0899242e0da98296\\/5d1629451cd58642f6b91e1c_IMG_0518.jpeg\\\" style=\\\"border: 0px; max-width: 100%; display: inline-block; width: 434px;\\\"><\\/div><\\/figure><figure data-w-id=\\\"52d8388f-2f50-8a4e-54be-0ca3b5d4caba\\\" class=\\\"w-richtext-figure-type-image w-richtext-align-normal\\\" data-rt-type=\\\"image\\\" data-rt-align=\\\"normal\\\" style=\\\"display: table; margin-top: 16px; margin-bottom: 16px; position: relative; max-width: 60%; clear: both;\\\"><div data-w-id=\\\"bbb57cdd-c599-5c81-b991-617a22f8c5df\\\" style=\\\"font-size: 0px; color: transparent; display: inline-block;\\\"><img data-w-id=\\\"9ba93941-e474-2dfe-55f4-c215647082f4\\\" src=\\\"https:\\/\\/assets-global.website-files.com\\/5c7e6a9e0899242e0da98296\\/5d16295e34e447164da049b8_IMG_0521.jpeg\\\" style=\\\"border: 0px; max-width: 100%; display: inline-block; width: 434px;\\\"><\\/div><div data-w-id=\\\"bbb57cdd-c599-5c81-b991-617a22f8c5df\\\" style=\\\"font-size: 0px; color: transparent; display: inline-block;\\\"><br><\\/div><\\/figure><figure data-w-id=\\\"f8c522d5-ce7e-947f-f572-a417b1d20d5f\\\" class=\\\"w-richtext-figure-type-image w-richtext-align-normal\\\" data-rt-type=\\\"image\\\" data-rt-align=\\\"normal\\\" style=\\\"display: table; margin-top: 16px; margin-bottom: 16px; position: relative; max-width: 60%; clear: both;\\\"><div data-w-id=\\\"99435f16-eacc-8cb8-dd54-ff688026ed3f\\\" style=\\\"font-size: 0px; color: transparent; display: inline-block;\\\"><img data-w-id=\\\"1640f001-51c6-7f52-e0e9-bdee4cba18eb\\\" src=\\\"https:\\/\\/assets-global.website-files.com\\/5c7e6a9e0899242e0da98296\\/5d162970d1287ccc9e080bf5_IMG_0523.jpeg\\\" style=\\\"border: 0px; max-width: 100%; display: inline-block; width: 434px;\\\"><\\/div><\\/figure><\\/div><\\/div><div class=\\\"container blog-content-container w-container\\\" style=\\\"max-width: 820px; padding-right: 48px; padding-bottom: 0px; padding-left: 48px; -webkit-box-pack: center; justify-content: center; -webkit-box-align: center; align-items: center; margin-bottom: 0px; color: rgb(154, 142, 144); font-family: &quot;Proxima soft&quot;, sans-serif; font-size: 20px;\\\"><div class=\\\"blog-img-gallery\\\" style=\\\"display: flex; margin-top: 32px; margin-bottom: 32px; -webkit-box-pack: center; justify-content: center; -webkit-box-align: start; align-items: flex-start;\\\"><\\/div><\\/div><div class=\\\"blog-content-container w-container\\\" style=\\\"margin-left: auto; margin-right: auto; max-width: 820px; margin-bottom: 0px; padding-right: 48px; padding-left: 48px; color: rgb(154, 142, 144); font-family: &quot;Proxima soft&quot;, sans-serif; font-size: 20px;\\\"><div class=\\\"blog-rich-text w-richtext\\\" style=\\\"max-width: none; margin-bottom: 0px;\\\"><p style=\\\"margin-bottom: 32px; color: rgb(103, 94, 96); line-height: 32px;\\\"><span style=\\\"font-weight: 700;\\\"><span style=\\\"color: rgb(255, 207, 0);\\\">Tip<\\/span>:<\\/span>&nbsp;For a realistic handwritten note effect, try using the Beth Ellen font on textured paper as a background.<br><\\/p><p style=\\\"margin-bottom: 32px; color: rgb(103, 94, 96); line-height: 32px;\\\">\\u200d<\\/p><div><br><\\/div><\\/div><button class=\\\"sttc-button\\\" onclick=\\\"searchTemplate('illustration')\\\">Search Template<\\/button><br><\\/div><style>.sttc-button {font-family: -apple-system, system-ui, BlinkMacSystemFont, \\\"Segoe UI\\\", Roboto, \\\"Helvetica Neue\\\", Arial, sans-serif; font-size: 1rem; font-weight: 500; border: none; border-radius: 4px; box-shadow: none; color: #ffffff; cursor: pointer; display: inline-block; margin: 0px; padding: 8px 18px; text-decoration: none; background-color: #ffcd00; overflow-wrap: break-word; user-select:none !important;}.sttc-button:hover, .sttc-button:focus{ background-color: #d9ae00;}<\\/style><script>function OpenTemplate(templateID) { alert(\\\"OpenTemplate\\\", templateID); } function searchTemplate(searchTag) { alert(\\\"searchTemplate\\\", searchTag); } <\\/script>\",\"fg_image\":\"5d1b4e68c98e2_blog_image_1562070632.jpg\"}",
     * "is_active": 1
     * }
     * ]
     * }
     */
    public function getBlogContentByIdForUser(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('blog_id'), $request)) != '')
                return $response;


            $this->blog_id = $request->blog_id;
            $is_cache_enable = isset($request->is_cache_enable) ? $request->is_cache_enable : 1;

            if ($is_cache_enable) {
                $redis_result = Cache::remember("getBlogContentByIdForUser:$this->blog_id", Config::get("constant.CACHE_TIME_24_HOUR"), function () {

                    return DB::select('SELECT 
                                          id AS blog_id,
                                          IF(image != "",CONCAT("' . Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",image),"") as thumbnail_img,
                                          IF(image != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",image),"") as compressed_img,
                                          IF(image != "",CONCAT("' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",image),"") as original_img,
                                          IF(webp_image != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",webp_image),"") as webp_original_img,
                                          IF(webp_image != "",CONCAT("' . Config::get('constant.WEBP_THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",webp_image),"") as webp_thumbnail_img,
                                          height,
                                          width,
                                          title,
                                          subtitle,
                                          blog_json,
                                          is_active
                                      FROM  blog_master 
                                      WHERE id = ?', [$this->blog_id]);

                });

            } else {

                $redis_result = DB::select('SELECT 
                                          id AS blog_id,
                                          IF(image != "",CONCAT("' . Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",image),"") as thumbnail_img,
                                          IF(image != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",image),"") as compressed_img,
                                          IF(image != "",CONCAT("' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",image),"") as original_img,
                                          IF(webp_image != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",webp_image),"") as webp_original_img,
                                          IF(webp_image != "",CONCAT("' . Config::get('constant.WEBP_THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",webp_image),"") as webp_thumbnail_img,
                                          height,
                                          width,
                                          title,
                                          subtitle,
                                          blog_json,
                                          is_active
                                      FROM  blog_master 
                                      WHERE id = ?', [$this->blog_id]);

            }

            $response = Response::json(array('code' => 200, 'message' => 'Blog content fetched successfully.', 'cause' => '', 'data' => $redis_result));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            Log::error("getBlogContentByIdForUser : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get blog content by id for user.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    //Not used
    /**
     *
     * - Users ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/getBlogContentByUser",
     *        tags={"Users_blog"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="getBlogContentByUser",
     *        summary="getBlogContentByUser",
     *        produces={"application/json"},
     * 		@SWG\Parameter(
     *        in="header",
     *        name="Authorization",
     *        description="access token",
     *        required=true,
     *        type="string",
     *      ),
     * 		@SWG\Parameter(
     *        in="body",
     *        name="request_body",
     *   	  @SWG\Schema(
     *          required={"page","item_count"},
     *          @SWG\Property(property="page",  type="integer", example=1, description=""),
     *          @SWG\Property(property="item_count",  type="integer", example=4, description=""),
     *        ),
     *      ),
     * 		@SWG\Response(
     *            response=200,
     *            description="Success",
     *        @SWG\Schema(
     *          @SWG\Property(property="Sample Response",  type="string", example={"code":200,"message":"Blog content fetched successfully.","cause":"","data":{"total_record":5,"is_next_page":true,"result":{{"blog_id":5,"fg_image":"5d12078922b96_blog_image_1561462665.png","thumbnail_img":"http://192.168.0.114/photo_editor_lab_backend/image_bucket/thumbnail/5d12078922b96_blog_image_1561462665.png","compressed_img":"http://192.168.0.114/photo_editor_lab_backend/image_bucket/compressed/5d12078922b96_blog_image_1561462665.png","original_img":"http://192.168.0.114/photo_editor_lab_backend/image_bucket/original/5d12078922b96_blog_image_1561462665.png","blog_json":{"title":"Content Data","subtitle":"Description","blog_data":"<div><\/div>"},"is_active":1}}}}, description=""),),
     *        ),
     * 		@SWG\Response(
     *            response=201,
     *            description="error",
     *        ),
     *    )
     *
     */
    /**
     * @api {post} getBlogContentByUser getBlogContentByUser
     * @apiName getBlogContentByUser
     * @apiGroup 1User-Blog_content
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     *  Key: Authorization
     *  Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "page":1,
     * "item_count":10
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Blog content fetched successfully.",
     * "cause": "",
     * "data": {
     * "total_record": 3,
     * "is_next_page": false,
     * "result": [
     * {
     * "blog_id": 6,
     * "fg_image": "5d13082801518_blog_image_1561528360.png",
     * "thumbnail_img": "http://192.168.0.114/photo_editor_lab_backend/image_bucket/thumbnail/5d13082801518_blog_image_1561528360.png",
     * "compressed_img": "http://192.168.0.114/photo_editor_lab_backend/image_bucket/compressed/5d13082801518_blog_image_1561528360.png",
     * "original_img": "http://192.168.0.114/photo_editor_lab_backend/image_bucket/original/5d13082801518_blog_image_1561528360.png",
     * "blog_json": "{\"title\":\"test\",\"subtitle\":\"demo\",\"blog_data\":\"<header class=\\\"entry-header\\\" style=\\\"box-sizing: inherit; margin-bottom: 27px; color: rgb(64, 64, 64); font-family: Lato, sans-serif; font-size: 16px;\\\"><\\/div>\"}",
     * "is_active": 1
     * },
     * {
     * "blog_id": 2,
     *"fg_image": "5d13082801518_blog_image_1561528360.png",
     * "thumbnail_img": "http://192.168.0.113/photo_editor_lab_backend/image_bucket/thumbnail/5d11e5af99f3f_blog_image_1561453999.png",
     * "compressed_img": "http://192.168.0.113/photo_editor_lab_backend/image_bucket/compressed/5d11e5af99f3f_blog_image_1561453999.png",
     * "original_img": "http://192.168.0.113/photo_editor_lab_backend/image_bucket/original/5d11e5af99f3f_blog_image_1561453999.png",
     * "blog_json": "{\"title\":\"Title Description\",\"subtitle\":\"Sub title sub title\",\"blog_data\":\"<header class=\\\"entry-header\\\" style=\\\"box-sizing: inherit; margin-bottom: 27px; color: rgb(64, 64, 64); font-family: Lato, sans-serif; font-size: 16px;\\\"><\\/div>\"}",
     * "is_active": 1
     * }
     * ]
     * }
     * }
     */
    public function getBlogContentByUser(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('page', 'item_count'), $request)) != '')
                return $response;


            $this->page = $request->page;
            $this->item_count = $request->item_count;
            $this->offset = ($this->page - 1) * $this->item_count;

            if (!Cache::has("pel:getBlogContentByUser$this->page:$this->item_count")) {
                $result = Cache::rememberforever("getBlogContentByUser$this->page:$this->item_count", function () {

                    $total_row_result = DB::select('SELECT COUNT(*) as total FROM  blog_master WHERE is_active = ?', [1]);
                    $total_row = $total_row_result[0]->total;

                    $result = DB::select('SELECT 
                                          id AS blog_id,
                                          image AS fg_image,
                                          IF(image != "",CONCAT("' . Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",image),"") as thumbnail_img,
                                          IF(image != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",image),"") as compressed_img,
                                          IF(image != "",CONCAT("' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",image),"") as original_img,
                                          blog_json,
                                          is_active
                                      FROM  blog_master 
                                      WHERE is_active = ?
                                      ORDER BY update_time DESC 
                                      LIMIT ?, ?', [1, $this->offset, $this->item_count]);

                    $is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;
                    return array('total_record' => $total_row, 'is_next_page' => $is_next_page, 'result' => $result);

                });
            }

            $redis_result = Cache::get("getBlogContentByUser$this->page:$this->item_count");

            if (!$redis_result) {
                $redis_result = [];
            }

            $response = Response::json(array('code' => 200, 'message' => 'Blog content fetched successfully.', 'cause' => '', 'data' => $redis_result));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            Log::error("getBlogContentByUser : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'fetched blog content by user.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

}

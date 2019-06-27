<?php

//////////////////////////////////////////////////////////////////////////////
//                   OptimumBrew Technology Pvt. Ltd.                       //
//                                                                          //
// Title:            Photo Editor Lab                                       //
// File:             ZipController.php                                      //
// Since:            21-May-2019                                            //
//                                                                          //
// Author:           Pooja Jadav                                            //
// Email:            pooja.optimumbrew@gmail.com                            //
//                                                                          //
//////////////////////////////////////////////////////////////////////////////


namespace App\Http\Controllers;

use Response;
use Config;
use DB;
use Log;
use File;
use Cache;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Redis;
use Image;
use App\Http\Controllers\AppBaseController;


class ZipController extends Controller
{

    /**
     * @api {post} addTemplateByZip addTemplateByZip
     * @apiName addTemplateByZip
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
     * "catalog_id":1,
     * "search_category":1 //optional
     * },
     * "file":1.zip
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Template added successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function addTemplateByZip(Request $request_body)
    {
        try {
            //$token = JWTAuth::getToken();
            //JWTAuth::toUser($token);

            if (!$request_body->has('request_data'))
                return Response::json(array('code' => 201, 'message' => 'Required field request_data is missing or empty', 'cause' => '', 'data' => json_decode("{}")));

            $request = json_decode($request_body->input('request_data'));

            if (($response = (new VerificationController())->validateRequiredParameter(array('catalog_id'), $request)) != '')
                return $response;
            $catalog_id = $request->catalog_id;
            $search_category = isset($request->search_category) ? strtolower($request->search_category) : NULL;

            if (!$request_body->hasFile('file'))
                return Response::json(array('code' => 201, 'message' => 'Required field file is missing or empty', 'cause' => '', 'data' => json_decode("{}")));

            $zip_array = Input::file('file');
            if (($response = (new ImageController())->verifyZipFile($zip_array)) != '')
                return $response;

            $resource_size = Config::get('constant.RESOURCE_MAXIMUM_FILESIZE');
            $resource_size = isset($resource_size) ? Config::get('constant.RESOURCE_MAXIMUM_FILESIZE') : 200;
            $sample_img = '';
            $error = '';

            $zip = new \ZipArchive();
            if ($zip->open($zip_array) === TRUE) {
                $images_array = array();
                for ($i = 0; $i < $zip->numFiles; $i++) {
                    $pathinfo = pathinfo($zip->getNameIndex($i));

                    //Log::info('pathinfo', [$pathinfo]);
                    $file_extension = $pathinfo['extension'];
                    $dirname = $pathinfo['dirname'];
                    $basename = $pathinfo['basename'];

                    $name = $zip->statIndex($i)['name'];
                    $size = $zip->statIndex($i)['size']; //Bytes

                    $MAXIMUM_FILESIZE = $resource_size * 1024;

                    //Log::info(['name'=>$name,'size'=>$size,'statIndex'=> $zip->statIndex($i), 'MAXIMUM_FILESIZE'=>$MAXIMUM_FILESIZE]);
                    if ($file_extension == 'jpg' OR $file_extension == 'png' OR $file_extension == 'jpeg') {
                        if ($size > $MAXIMUM_FILESIZE) {
                            $error = Response::json(array('code' => 201, 'message' => 'Resource file Size is greater then ' . $resource_size . ' KB', 'cause' => '', 'data' => json_decode("{}")));
                            break;
                        }
                        $images_array[] = $basename;
                    }
                    if (preg_match("/sample/", $basename)) {
                        $sample_img = $basename;
                        $sample_img_index = $i;
                    }
                }

                if ($error) {
                    $zip->close();
                    return $error;
                }

                $file_dir_in_zip = 'zip://' . $zip_array . '#' . $dirname . '/';

        if (($response = (new ImageController())->checkIsImageExist($images_array, 1)) != '') {
          $zip->close();
          return $response;
        }

                $json_data = $zip->getFromName($dirname . '/json.txt');

                $json_data = json_decode($json_data);
                $is_featured = $json_data->is_featured;
                $is_portrait = $json_data->is_portrait;
                $is_free = 1;

                $response = $this->addjsonTemplateByZip($catalog_id, $json_data, $is_free, $is_featured, $is_portrait, $search_category, $file_dir_in_zip . $sample_img);
                $data = (json_decode(json_encode($response), true));
                $status_code = $data['original']['code'];

                if ($status_code == 200) {
                    foreach ($images_array as $image_array) {

                        (new ImageController())->saveResourceImageByZip($file_dir_in_zip . $image_array, $image_array);

                        if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
                            (new ImageController())->saveResourceImageInToS3($image_array);
                        }
                    }
                }

                $zip->close();

            } else {
                $response = Response::json(array('code' => 201, 'message' => 'Zip not open', 'cause' => '', 'data' => json_decode("{}")));
            }

        } catch (Exception $e) {
            Log::error("addTemplateByZip: ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'add template by zip.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    public function addjsonTemplateByZip($catalog_id, $json_data, $is_free, $is_featured, $is_portrait, $search_category, $file_array)
    {
        try {

            $created_at = date('Y-m-d H:i:s');

            if ($search_category != NULL or $search_category != "") {
                $search_category = $search_category . ',';
            }

            if (($response = (new ImageController())->validateFonts($json_data)) != '')
                return $response;

            DB::beginTransaction();

            if (($response = (new ImageController())->validateHeightWidthOfSampleImage($file_array, $json_data)) != '')
                return $response;

            $tag_list = strtolower((new TagDetectController())->getTagInImageByBytes($file_array));
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

            $catalog_image = (new ImageController())->generateNewFileNameByZip('sample_image', $file_array);
            (new ImageController())->saveOriginalImageByZip($file_array, $catalog_image);
            (new ImageController())->saveCompressedImage($catalog_image);
            (new ImageController())->saveThumbnailImage($catalog_image);
            $file_name = (new ImageController())->saveWebpOriginalImage($catalog_image);
            $dimension = (new ImageController())->saveWebpThumbnailImage($catalog_image);

            if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
                (new ImageController())->saveImageInToS3($catalog_image);
                (new ImageController())->saveWebpImageInToS3($file_name);
            }

            DB::insert('INSERT
                                INTO
                                  images(catalog_id, image, json_data, is_free, is_featured, is_portrait, search_category, height, width, original_img_height, original_img_width, created_at, attribute1)
                                VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?) ', [$catalog_id, $catalog_image, json_encode($json_data), $is_free, $is_featured, $is_portrait, $tag_list, $dimension['height'], $dimension['width'], $dimension['org_img_height'], $dimension['org_img_width'], $created_at, $file_name]);


            DB::commit();


            if (strstr($file_name, '.webp')) {

                $response = Response::json(array('code' => 200, 'message' => 'Json added successfully.', 'cause' => '', 'data' => json_decode('{}')));


            } else {
                $response = Response::json(array('code' => 200, 'message' => 'Json added successfully. Note: webp is not converted due to size grater than original.', 'cause' => '', 'data' => json_decode('{}')));

            }

        } catch (Exception $e) {
            Log::error("addjsonTemplateByZip : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'add template.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }

        return $response;
    }

    /*---------------------------------| Category List|---------------------------------*/


    public function getCatalogBySubCategoryList(Request $request)
    {
        try {

            //$token = JWTAuth::getToken();
            //JWTAuth::toUser($token);

            if (!Cache::has("pel:getCatalogBySubCategoryList")) {
                $result = Cache::rememberforever("getCatalogBySubCategoryList", function () {

                    $sub_categories = DB::select('SELECT
                                                        distinct sc.id AS sub_category_id,
                                                        sc.name AS sub_category_name,
                                                        sc.updated_at
                                                      FROM sub_category sc
                                                        LEFT JOIN sub_category_catalog AS scc ON sc.id=scc.sub_category_id AND scc.is_active=1
                                                      WHERE
                                                        sc.is_active = 1 AND 
                                                        sc.is_featured = 1
                                                      ORDER BY sc.updated_at DESC');

                    foreach ($sub_categories as $key) {
                        $catalogs = DB::select('SELECT
                                                      DISTINCT scc.catalog_id,
                                                      cm.name AS catalog_name,
                                                      cm.updated_at
                                                    FROM sub_category_catalog AS scc
                                                      JOIN catalog_master AS cm
                                                        ON cm.id=scc.catalog_id AND
                                                           cm.is_active=1 AND
                                                           cm.is_featured = 1
                                                    WHERE
                                                      scc.is_active = 1 AND
                                                      scc.sub_category_id = ?
                                                    ORDER BY cm.updated_at DESC', [$key->sub_category_id]);

                        $key->catalog_list = $catalogs;

                    }

                    return $sub_categories;

                });
            }

            $redis_result = Cache::get("getCatalogBySubCategoryList");

            if (!$redis_result) {
                $redis_result = [];
            }

            $response = Response::json(array('code' => 200, 'message' => 'Sub categories are fetched successfully.', 'cause' => '', 'data' => ['sub_category_list' => $redis_result]));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            Log::error("getCatalogBySubCategoryList : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . ' get all sub category.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

}

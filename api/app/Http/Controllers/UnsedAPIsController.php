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
}

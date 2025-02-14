<?php

namespace App\Http\Controllers;

use Aws\CommandPool;
use Aws\Exception\AwsException;
use Aws\ResultInterface;
use Aws\S3\S3Client;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Tymon\JWTAuth\Facades\JWTAuth;

class MasterContentAdminController extends Controller
{

    /**
     * @api {post} autoUploadContent   autoUploadContent
     * @apiName autoUploadContent
     * @apiGroup MCM Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "app_catalog_id": 1 //compulsory
     * "content_details": [ //compulsory
     * {
     * "content_id": 191,
     * "image_name": "647b221e5b30c_svg_1685791262.svg",
     * "search_tags": "",
     * "content_type": 1,
     * "height": 108,
     * "width": 108,
     * "orientation": 3,
     * "image_extension": 3,
     * "is_active": 1,
     * "update_time": "2023-06-06 12:48:44"
     * }
     * ]
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Catalog deleted successfully!.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function autoUploadMCMContent(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());

            if (($response = (new VerificationController())->validateRequiredParameter(array('app_catalog_id', 'sub_category_id'), $request)) != '')
                return $response;

            if (($response = (new VerificationController())->validateRequiredArrayParameter(array('content_details'), $request)) != '')
                return $response;

            $catalog_id = $request->app_catalog_id;
            $sub_category_id = $request->sub_category_id;
            $content_details = $request->content_details;
            $insert_detail = [];
            $update_time = gmdate('Y-m-d H:i:s');
            Log::info('autoUploadMCMContent : ', ['content_details' => $content_details]);

            DB::beginTransaction();
            foreach ($content_details as $i => $content_detail) {
                $mcm_id = $content_detail->content_id;

                $result = DB::select('SELECT
                                        ctm.id AS app_catalog_id,
                                        ctm.name AS app_catalog_name,
                                        im.mcm_id
                                      FROM
                                        images AS im,
                                        catalog_master AS ctm,
                                        sub_category_catalog AS scc
                                      WHERE
                                        im.catalog_id = scc.catalog_id AND
                                        scc.catalog_id = ctm.id AND
                                        scc.sub_category_id = ? AND
                                        im.mcm_id = ?', [$sub_category_id, $mcm_id]);
                if (!$result) {

                    $increase_time = gmdate('Y-m-d H:i:s', strtotime("+$i seconds", strtotime($update_time)));
                    if ($content_detail->image_extension == 3) {
                        $content_type = config('constant.CONTENT_TYPE_FOR_SVG_RESOURCE');
                    } else {
                        $content_type = config('constant.CONTENT_TYPE_FOR_NORMAL_RESOURCE');
                    }

                    $insert_detail[$i] = [
                        'catalog_id' => $catalog_id,
                        'image' => $content_detail->image_name,
                        'search_category' => $content_detail->search_tags,
                        'original_img_height' => $content_detail->height,
                        'original_img_width' => $content_detail->width,
                        'height' => $content_detail->height,
                        'width' => $content_detail->width,
                        'content_type' => $content_type,
                        'mcm_id' => $content_detail->content_id,
                        'is_featured' => 0,
                        'is_portrait' => $content_detail->orientation,
                        'updated_at' => $increase_time
                    ];
                }
            }
            DB::table('images')->insert($insert_detail);
            DB::commit();
            $response = Response::json(array('code' => 200, 'message' => 'Content uploaded successfully.', 'cause' => '', 'data' => json_decode("{}")));

        } catch (Exception $e) {
            Log::error("autoUploadContent : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => config('constant.EXCEPTION_ERROR') . ' upload content.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /**
     * @api {post} checkUploadStatus   checkUploadStatus
     * @apiName checkUploadStatus
     * @apiGroup MCM Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "mcm_id": 192 //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Catalog deleted successfully!.",
     * "cause": "",
     * "data": {
     * "app_catalog_id" : 51673,
     * "app_catalog_name" : "Frame 1"
     * }
     * }
     */
    public function checkUploadStatus(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('mcm_id', 'sub_category_id'), $request)) != '')
                return $response;

            $sub_category_id = $request->sub_category_id;
            $mcm_id = $request->mcm_id;
            $result = DB::select('SELECT
                                    im.id AS app_catalog_id,
                                    ctm.name AS app_catalog_name
                                  FROM
                                    images AS im,
                                    catalog_master AS ctm,
                                    sub_category_catalog AS scc
                                  WHERE
                                    im.catalog_id = scc.catalog_id AND
                                    im.catalog_id = ctm.id AND
                                    scc.sub_category_id = ? AND
                                    im.mcm_id = ?', [$sub_category_id, $mcm_id]);

            $count = count($result);
            if ($count > 0 && $count < 2) {
                $response = Response::json(array('code' => 200, 'message' => 'Upload status get successfully.', 'cause' => '', 'data' => $result[0]));
            } else {
                $response = Response::json(array('code' => 201, 'message' => 'No data found.', 'cause' => '', 'data' => json_decode("{}")));
            }

        } catch (Exception $e) {
            Log::error("checkUploadStatus : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => config('constant.EXCEPTION_ERROR') . ' upload template.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /**
     * @api {post} getAppCatalogsWithContentUploadStatus   getAppCatalogsWithContentUploadStatus
     * @apiName getAppCatalogsWithContentUploadStatus
     * @apiGroup MCM Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "content_ids": "192,191" //compulsory
     * "sub_category_id": 23 //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Catalog deleted successfully!.",
     * "cause": "",
     * "data": [
     * {
     * "app_catalog_id": 78,
     * "app_catalog_name": "Frame 1",
     * "mcm_id": null
     * },
     * {
     * "app_catalog_id": 78,
     * "app_catalog_name": "Frame 1",
     * "mcm_id": 191
     * },
     * {
     * "app_catalog_id": 78,
     * "app_catalog_name": "Frame 1",
     * "mcm_id": 192
     * }
     * ]
     * }
     */
    public function getAppCatalogsWithContentUploadStatus(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('content_ids', 'sub_category_id'), $request)) != '')
                return $response;

            $sub_category_id = $request->sub_category_id;
            $content_ids = $request->content_ids;

            $uploaded_content_catalog = DB::select('SELECT
                                                        ctm.id AS app_catalog_id,
                                                        ctm.name AS app_catalog_name,
                                                        im.mcm_id
                                                      FROM
                                                        images AS im,
                                                        catalog_master AS ctm,
                                                        sub_category_catalog AS scc
                                                      WHERE
                                                        im.catalog_id = scc.catalog_id AND
                                                        scc.catalog_id = ctm.id AND
                                                        scc.sub_category_id = ? AND
                                                        scc.is_active = 1 AND
                                                        FIND_IN_SET(im.mcm_id,?)', [$sub_category_id, $content_ids]);

            $content_count = substr_count($content_ids, ",") + 1;
            $result_count = count($uploaded_content_catalog);

            if ($content_count == $result_count) {
                $result = array('uploaded_content_catalog' => $uploaded_content_catalog);
                $response = Response::json(array('code' => 200, 'message' => 'Upload status get successfully.', 'cause' => '', 'data' => $result));
            } else {
                $selection_catalog = DB::select('SELECT
                                                    ctm.id AS app_catalog_id,
                                                    ctm.name AS app_catalog_name
                                                  FROM
                                                    sub_category_catalog AS scc,
                                                    catalog_master AS ctm
                                                  WHERE
                                                    scc.catalog_id = ctm.id AND
                                                    ctm.is_featured = 0 AND
                                                    scc.is_active = 1 AND
                                                    scc.sub_category_id = ?', [$sub_category_id, $content_ids]);

                if ($uploaded_content_catalog) {
                    $app_catalog_ids = array_column($uploaded_content_catalog, 'app_catalog_id');
                    foreach ($selection_catalog as $catalog) {
                        $exist = in_array($catalog->app_catalog_id, $app_catalog_ids);
                        if ($exist) {
                            $catalog->content_exist = 1;
                        }
                    }

                    $result = array('uploaded_content_catalog' => $uploaded_content_catalog, 'selection_catalog' => $selection_catalog);
                    $response = Response::json(array('code' => 200, 'message' => 'Upload status get successfully.', 'cause' => '', 'data' => $result));
                } else {
                    $result = array('selection_catalog' => $selection_catalog);
                    $response = Response::json(array('code' => 200, 'message' => 'Upload status get successfully.', 'cause' => '', 'data' => $result));
                }
            }

        } catch (Exception $e) {
            Log::error("getAppCatalogsWithContentUploadStatus : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => config('constant.EXCEPTION_ERROR') . ' get catalog list with uploaded content status.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /**
     * @api {post} deleteMCMCatalog   deleteMCMCatalog
     * @apiName deleteMCMCatalog
     * @apiGroup MCM Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "content_ids": "191,192" //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Content deleted successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function deleteMCMContent(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('content_ids'), $request)) != '')
                return $response;

            $content_ids = $request->content_ids;

            DB::beginTransaction();
            DB::update('UPDATE images SET is_active = ? WHERE mcm_id IN(?)', [0, $content_ids]);
            DB::commit();

            $response = Response::json(array('code' => 200, 'message' => 'Content deleted successfully.', 'cause' => '', 'data' => json_decode('{}')));
        } catch (Exception $e) {
            Log::error("deleteMCMCatalog : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => config('constant.EXCEPTION_ERROR') . 'delete catalog.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /**
     * @api {post} updateMCMContent   updateMCMContent
     * @apiName updateMCMContent
     * @apiGroup MCM Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "request_data":
     * { //all parameters are compulsory
     * "content_id": 2,
     * "content_type_id": 2,
     * "search_tags": "food,fast food"
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Content updated successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function updateMCMContent(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            Log::info([$request]);

            if (($response = (new VerificationController())->validateRequiredParameter(array('search_tags'), $request)) != '')
                return $response;

            $content_id = isset($request->content_ids) ? $request->content_ids : '';
            $is_active = isset($request->is_active) ? $request->is_active : 1;
            $search_category = strtolower($request->search_tags);
            $update_time = gmdate('Y-m-d H:i:s');
            if (($response = (new VerificationController())->verifySearchCategory($search_category)) != '')
                return $response;

            DB::beginTransaction();
            if (is_array($content_id)) {
                foreach ($content_id as $i => $id) {
                    $increase_time = gmdate('Y-m-d H:i:s', strtotime("+$i seconds", strtotime($update_time)));
                    $content_details = DB::select('SELECT search_category FROM images WHERE mcm_id = ?', [$id]);

                    if ($content_details) {
                        $tags = ($content_details[0]->search_category == NULL) ? $search_category : implode(",", array_unique(explode(",", $content_details[0]->search_category . "," . $search_category)));

                        if ($tags !== $content_details[0]->search_category) {
                            DB::update('UPDATE images
                                        SET
                                            search_category = ?,
                                            is_active = ?,
                                            updated_at = ?
                                        WHERE mcm_id = ?', [$tags, $is_active, $increase_time, $id]);
                        }
                    }
                }
            } else {
                DB::update('UPDATE images
                            SET
                              search_category = ?,
                              is_active = ?
                            WHERE mcm_id = ?', [$search_category, $is_active, $content_id]);
            }
            DB::commit();

            $response = Response::json(array('code' => 200, 'message' => 'Content updated successfully.', 'cause' => '', 'data' => json_decode('{}')));

        } catch (Exception $e) {
            Log::error("updateMCMContent : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => config('constant.EXCEPTION_ERROR') . 'update content.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    public function deleteObjectsFromS3(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('catalog_id'), $request)) != '')
                return $response;

            $catalog_id = $request->catalog_id;
            $images = [];

            $content_details = DB::select('SELECT image FROM images WHERE catalog_id = ?', [$catalog_id]);

            foreach ($content_details as $content_detail) {
                $images[] = 'imageflyer/original/' . $content_detail->image;
            }

            $disk = new S3Client([
                'version' => 'latest',
                'region' => 'us-east-2',
                'credentials' => [
                    'key' => config('constant.AWS_KEY'),
                    'secret' => config('constant.AWS_SECRET')
                ]
            ]);

            foreach ($images AS $image){
                $disk->deleteObject(array(
                    'Bucket' => 'businesscardmaker',
                    'Key' => $image
                ));
            }

            $response = Response::json(array('code' => 200, 'message' => 'Catalog content images deleted successfully.', 'cause' => '', 'data' => json_decode('{}')));
        } catch (Exception $e) {
            Log::error("deleteObjectsFromS3 : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => config('constant.EXCEPTION_ERROR') . 'delete content images.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

}

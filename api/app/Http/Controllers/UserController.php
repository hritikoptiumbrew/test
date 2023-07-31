<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Permission;
use App\Role;
use Illuminate\Support\Facades\Storage;
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
use Abraham\TwitterOAuth\TwitterOAuth;
use App\Jobs\SaveSearchTagJob;
use Image;
use Google\Cloud\Translate\V2\TranslateClient;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;

class UserController extends Controller
{
    //
    public function __construct()
    {
        $this->item_count = Config::get('constant.PAGINATION_ITEM_LIMIT');
        $this->base_url = (new Utils())->getBaseUrl();

    }
    /* =================================| Ai Playground |=============================*/
    /**
     * @api {post} getDataFromPrompt getDataFromPrompt
     * @apiName getDataFromPrompt
     * @apiGroup User
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     *{
     * "industry":"test" //optional
     * "purpose":"High tea party invitation" //compulsory
     * "exactly_want":"Description" //compulsory
     * "device_json" : { //optional
     *           "country" : "us",
     *           "language" : "english"
     *  }
     * "app_json" : { //optional
     *           "app_version" : "1.0",
     *           "platform" : "1"
     *  }
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Result get successfully",
     * "cause": "",
     * "data": {
     * "id": 279,
     * "result" : "Join us for an elegant high tea party!\nDate: [Date]\nTime: [Time]\nLocation: [Location]\nRSVP: [Contact information]\n\nDress up and indulge in a delightful selection of teas, finger sandwiches, scones, and sweet treats. Don't miss this opportunity to relax, socialize, and enjoy an afternoon of sophistication and deliciousness!\n"
     * }
     * }
     */
    public function getDataFromPrompt(Request $request_body)
    {
        try {
            if ($request_body['device_json'] != null) {
                $device_json['device_country_code'] = isset($request_body['device_json']['device_country_code']) ? $request_body['device_json']['device_country_code'] : "";
                $device_json['device_language'] = isset($request_body['device_json']['device_language']) ? $request_body['device_json']['device_language'] : "";
                $device_json = json_encode($device_json);
            } else {
                $device_json = NULL;
            }

            if ($request_body['app_json'] != null) {
                $app_json['app_version'] = isset($request_body['app_json']['app_version']) ? $request_body['app_json']['app_version'] : "";
                $app_json['platform'] = isset($request_body['app_json']['platform']) ? $request_body['app_json']['platform'] : "";
                $app_json = json_encode($app_json);
            } else {
                $app_json = NULL;
            }

            $current_time = date('Y-m-d H:i:s');
            $industry = isset($request_body['industry']) ? $request_body['industry'] : NULL;
            $industry_string = $industry ? "\nUser belongs to: " . $industry : NULL;
            $purpose = $request_body['purpose'];
            $prompt = $request_body['exactly_want'];
            $system = "You are a helpful assistant whose job is to provide or create text content for poster. The text content should be concise and not too detailed to fit on the poster.\n\n$industry_string\nPurpose of the poster: $purpose\n\n\ngive a result based on this but give the exact result that the user wants. Do not give the other description.\n\n\nprovide a result text without adding any extra words so that it can be used directly in the poster.";

            $chatGpt_request = [
                "model" => "gpt-3.5-turbo",
                'messages' => [
                    [
                        "role" => "system",
                        "content" => $system
                    ],
                    [
                        "role" => "user",
                        "content" => $prompt
                    ]
                ],
            ];
            Log::info('getDataFromPrompt : ', ['key' => config('constant.OPENAI_API_KEY')]);
            $client = new Client();
            $response_client = $client->post("https://api.openai.com/v1/chat/completions", [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . config('constant.OPENAI_API_KEY'),
                ],
                'json' => $chatGpt_request
            ]);

            $data = json_decode($response_client->getBody()->getContents(), true);
            $db_data = array(
                'industry' => $industry,
                'purpose' => $purpose,
                'exactly_want' => $request_body["exactly_want"],
                'ChatGpt_response' => json_encode($data),
                'ChatGpt_request' => json_encode($chatGpt_request),
                'created_at' => $current_time,
                'device_json' => $device_json,
                'app_json' => $app_json,
            );

            DB::beginTransaction();
            $chat_number = DB::table('ai_chats')->insertGetId($db_data);
            $resutl['id'] = strval($chat_number);
            $pregString = preg_replace('/(^[\"\']|[\"\']$)/', '', $data['choices'][0]['message']['content']);
            $resutl['result'] = $pregString != null ? $pregString : $data['choices'][0]['message']['content'];
            $resutl['result'] = trim($resutl['result']);
            DB::commit();

            $response = Response::json(array('code' => 200, 'message' => 'Result get successfully.', 'cause' => '', 'data' => $resutl));

        } catch (Exception $e) {
            Log::error('getDataFromPrompt : ', ['Exception' => $e->getMessage(), "TraceAsString" => $e->getTraceAsString()]);
            DB::rollBack();
            $response = Response::json(array('code' => 201, 'message' => config('constant.EXCEPTION_ERROR') . 'get catalogs.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    /**
     * @api {post} aiFeedback   aiFeedback
     * @apiName aiFeedback
     * @apiGroup User
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     *{
     * "feedback":"yes" //compulsory
     * "feedback_msg":"Description" //compulsory
     * "chat_number":1 //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Thank You For Your FeedBack",
     * "cause": "",
     * "data": {
     * }
     * }
     */
    public function aiFeedback(Request $request_body)
    {
        try {
            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('feedback', 'feedback_msg', 'chat_number'), $request)) != '')
                return $response;

            DB::beginTransaction();
            DB::update('UPDATE `ai_chats` SET feedback = ?, feedback_msg = ?, updated_at = ? WHERE id = ?', [$request->feedback, $request->feedback_msg, date('Y-m-d H:i:s'), $request->chat_number]);
            DB::commit();

            $response = Response::json(array('code' => 200, 'message' => 'Thank you for your feedback.', 'cause' => '', 'data' => json_decode("{}")));

        } catch (Exception $e) {
            Log::error('aiFeedback : ', ['Exception' => $e->getMessage(), "TraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => config('constant.EXCEPTION_ERROR') . 'update feedback.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /**
     * @api {post} getAiChats   getAiChats
     * @apiName getAiChats
     * @apiGroup User
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     *{
     * }
     * @apiSuccessExample Success-Response:
     * {
     *  "code": 200,
     *  "message": "success",
     *  "cause": "",
     *  "data": [
     *  {
     *  "id": 4,
     *  "industry": "test",
     *  "purpose": "High tea party invitation",
     *  "exactly_want": "Description",
     *  "ChatGpt_response": "{\"id\":\"chatcmpl-7dW4rN4CAsFk2xzZ31XoPU6hHApbK\",\"object\":\"chat.completion\",\"created\":1689652453,\"model\":\"gpt-3.5-turbo-0613\",\"choices\":[{\"index\":0,\"message\":{\"role\":\"assistant\",\"content\":\"You are cordially invited to a delightful High Tea Party. Join us for an elegant afternoon filled with delectable treats, delicate teas, and delightful conversations. Dress up, bring your friends, and immerse yourself in the charm and elegance of our tea party. Save the date and RSVP now to reserve your spot for a memorable experience.\"},\"finish_reason\":\"stop\"}],\"usage\":{\"prompt_tokens\":104,\"completion_tokens\":68,\"total_tokens\":172}}",
     *  "feedback": null,
     *  "feedback_msg": null,
     *  "created_at": "2023-07-18 03:54:13",
     *  "updated_at": null,
     *  "ChatGpt_request": "{\"model\":\"gpt-3.5-turbo\",\"messages\":[{\"role\":\"system\",\"content\":\"You are a helpful assistant whose job is to provide or create text content for poster. The text content should be concise and not too detailed to fit on the poster.\\n\\nUser belongs to: test\\nPurpose of the poster: High tea party invitation\\n\\n\\ngive a result based on this but give the exact result that the user wants. Do not give the other description.\\n\\n\\nprovide a result text without adding any extra words so that it can be used directly in the poster.\"},{\"role\":\"user\",\"content\":\"Description\"}]}"
     *  }
     *]
     *}
     */
    public function getAiChats(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('page', 'item_count'), $request)) != '')
                return $response;

            $this->item_count = $request->item_count;
            $this->page = $request->page;
            if(isset($request->order_by)) {

                if(!strcasecmp($request->order_by,"country") && !strcasecmp($request->order_by,"language") && !strcasecmp($request->order_by,"app_version")  && !strcasecmp($request->order_by,"platform"))
                {
                    $this->order_by = $request->order_by;
                }else {
                    $this->order_by = "id";
                }

            } else {
                $this->order_by = "id";
            }
            $this->order_type = isset($request->order_type) ? $request->order_type : 'DESC';
            $this->offset = ($this->page - 1) * $this->item_count;

            $total_row_result = DB::select('SELECT COUNT(*) as total FROM ai_chats');
            $result['total_row'] = $total_row_result[0]->total;

            $result['ai_chats'] = DB::select('SELECT 
                                    id.id, 
                                    id.industry, 
                                    id.purpose, 
                                    id.exactly_want, 
                                    id.ChatGpt_response, 
                                    id.feedback, 
                                    id.feedback_msg, 
                                    id.created_at, 
                                    id.updated_at, 
                                    id.ChatGpt_request, 
                                    id.device_json, 
                                    id.app_json
                                    
                                FROM 
                                    ai_chats AS id 
                                ORDER BY id.'. $this->order_by . ' ' . $this->order_type . '
                                  LIMIT ?,?', [$this->offset, $this->item_count]);

            $response = Response::json(array('code' => 200, 'message' => 'AI chats fetched successfully.', 'cause' => '', 'data' => $result));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));
        } catch (Exception $e) {
            Log::error('getAiChats : ', ['Exception' => $e->getMessage(), "TraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => config('constant.EXCEPTION_ERROR') . 'get AI chats.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    /* =================================| Catalogs |=============================*/

    /**
     * @api {post} getCatalogsByType   getCatalogsByType
     * @apiName getCatalogsByType
     * @apiGroup User
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     *{
     * "sub_category_id":94, //compulsory
     * "is_free":1 //optional, 1=free & 0=paid
     * "is_featured":1 //optional, 1=featured & 0=normal
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Catalogs fetched successfully.",
     * "cause": "",
     * "data": {
     * "result": [
     * {
     * "catalog_id": 279,
     * "name": "Animal1",
     * "thumbnail_img": "http://192.168.0.113/photo_editor_lab_backend/image_bucket/thumbnail/5c39b8ba93df0_catalog_img_1547286714.png",
     * "compressed_img": "http://192.168.0.113/photo_editor_lab_backend/image_bucket/compressed/5c39b8ba93df0_catalog_img_1547286714.png",
     * "original_img": "http://192.168.0.113/photo_editor_lab_backend/image_bucket/original/5c39b8ba93df0_catalog_img_1547286714.png",
     * "is_featured": 0,
     * "is_free": 1,
     * "updated_at": "2019-01-12 09:51:55"
     * }
     * ]
     * }
     * }
     */
    public function getCatalogsByType(Request $request)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('sub_category_id'), $request)) != '')
                return $response;

            $this->sub_category_id = $request->sub_category_id;
            $this->is_free = isset($request->is_free) ? ' AND ct.is_free = ' . $request->is_free : '';
            $this->is_featured = isset($request->is_featured) ? ' AND ct.is_featured = ' . $request->is_featured : '';

            $redis_result = Cache::rememberforever("getCatalogsByType:$this->sub_category_id:$this->is_free:$this->is_featured", function () {

                $catalog_ids = Config::get('constant.OFFLINE_CATALOG_IDS_OF_FONT');

                return DB::select('SELECT
                                      ct.id AS catalog_id,
                                      ct.name,
                                      IF(ct.image != "",CONCAT("' . Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.image),"") AS thumbnail_img,
                                      IF(ct.image != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.image),"") AS compressed_img,
                                      IF(ct.image != "",CONCAT("' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.image),"") AS original_img,
                                      ct.is_featured,
                                      ct.is_free,
                                      ct.updated_at
                                    FROM
                                      catalog_master AS ct,
                                      sub_category_catalog AS sct
                                    WHERE
                                      sct.sub_category_id = ? AND
                                      sct.catalog_id = ct.id AND
                                      NOT find_in_set(ct.id,"' . $catalog_ids . '") AND
                                      sct.is_active = 1 ' . $this->is_free . ' ' . $this->is_featured . '  ORDER BY ct.updated_at DESC', [$this->sub_category_id]);

            });

            $response = Response::json(array('code' => 200, 'message' => 'Catalogs fetched successfully.', 'cause' => '', 'data' => ['result' => $redis_result]));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            Log::error("getCatalogsByType : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get catalogs.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    /**
     * @api {post} getCatalogBySubCategoryId   getCatalogBySubCategoryId
     * @apiName getCatalogBySubCategoryId
     * @apiGroup Common For All
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

            $is_cache_enable = isset($request->is_cache_enable) ? $request->is_cache_enable : 1;
            $this->sub_category_id = $request->sub_category_id;

            if ($is_cache_enable) {

                $redis_result = Cache::remember("getCatalogBySubCategoryId:$this->sub_category_id", Config::get('constant.CACHE_TIME_6_HOUR'), function () {

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
                                    IF(ct.landscape_image != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.landscape_image),"") as compressed_landscape_img,
                                    IF(ct.portrait_image != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.portrait_image),"") as compressed_portrait_img,
                                    IF(ct.icon != "",CONCAT("' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.icon),"") as icon,
                                    IF(ct.attribute1 != "",CONCAT("' . Config::get('constant.WEBP_THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.attribute1),"") as webp_thumbnail_img,
                                    IF(ct.attribute1 != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.attribute1),"") as webp_original_img,
                                    ct.is_free,
                                    ct.is_ios_free,
                                    ct.catalog_type,
                                    ct.event_date,
                                    ct.popularity_rate,
                                    ct.search_category,
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

            } else {

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
                                    IF(ct.landscape_image != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.landscape_image),"") as compressed_landscape_img,
                                    IF(ct.portrait_image != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.portrait_image),"") as compressed_portrait_img,
                                    IF(ct.icon != "",CONCAT("' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.icon),"") as icon,
                                    IF(ct.attribute1 != "",CONCAT("' . Config::get('constant.WEBP_THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.attribute1),"") as webp_thumbnail_img,
                                    IF(ct.attribute1 != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.attribute1),"") as webp_original_img,
                                    ct.is_free,
                                    ct.is_ios_free,
                                    ct.catalog_type,
                                    ct.event_date,
                                    ct.popularity_rate,
                                    ct.search_category,
                                    ct.is_featured
                                  FROM
                                    catalog_master as ct,
                                    sub_category_catalog as sct
                                  WHERE
                                    sct.sub_category_id = ? AND
                                    sct.catalog_id=ct.id AND
                                    sct.is_active=1
                                  order by ct.updated_at DESC', [$this->sub_category_id]);

                $redis_result = array('total_record' => $total_row, 'category_name' => $category_name, 'category_list' => $result);
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
     * @api {post} getCatalogsByTypeInWebp   getCatalogsByTypeInWebp
     * @apiName getCatalogsByTypeInWebp
     * @apiGroup User
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     *{
     * "sub_category_id":94, //compulsory
     * "is_free":1, //optional, 1=free & 0=paid
     * "is_featured":1 //optional, 1=featured & 0=normal
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Catalogs fetched successfully.",
     * "cause": "",
     * "data": {
     * "result": [
     * {
     * "catalog_id": 333,
     * "name": "Shonar Bangla",
     * "webp_thumbnail_img": "http://192.168.0.113/photo_editor_lab_backend/image_bucket/webp_thumbnail/5c419ca410289_catalog_img_1547803812.webp",
     * "is_featured": 0,
     * "is_free": 1,
     * "updated_at": "2019-01-18 09:30:12"
     * },
     * {
     * "catalog_id": 332,
     * "name": "Roboto",
     * "webp_thumbnail_img": "http://192.168.0.113/photo_editor_lab_backend/image_bucket/webp_thumbnail/5c419c992fbeb_catalog_img_1547803801.webp",
     * "is_featured": 0,
     * "is_free": 1,
     * "updated_at": "2019-01-18 09:30:01"
     * }
     * ]
     * }
     * }
     */
    public function getCatalogsByTypeInWebp(Request $request)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('sub_category_id'), $request)) != '')
                return $response;

            $this->sub_category_id = $request->sub_category_id;
            $this->is_free = isset($request->is_free) ? ' AND ct.is_free = ' . $request->is_free : '';
            $this->is_featured = isset($request->is_featured) ? ' AND ct.is_featured = ' . $request->is_featured : '';
            $is_cache_enable = isset($request->is_cache_enable) ? $request->is_cache_enable : 1;

            if ($is_cache_enable) {
                $redis_result = Cache::remember("getCatalogsByTypeInWebp:$this->sub_category_id:$this->is_free:$this->is_featured", Config::get('constant.CACHE_TIME_6_HOUR'), function () {

                    $catalog_ids = Config::get('constant.OFFLINE_CATALOG_IDS_OF_FONT');

                    return DB::select('SELECT
                                          ct.id as catalog_id,
                                          ct.name,
                                          IF(ct.attribute1 != "",CONCAT("' . Config::get('constant.WEBP_THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.attribute1),"") as webp_thumbnail_img,
                                          ct.is_featured,
                                          ct.is_free,
                                          ct.is_ios_free,
                                          ct.updated_at
                                        FROM
                                          catalog_master as ct,
                                          sub_category_catalog as sct
                                        WHERE
                                          sct.sub_category_id = ? AND
                                          sct.catalog_id = ct.id AND
                                          NOT find_in_set(ct.id,"' . $catalog_ids . '") AND
                                          sct.is_active = 1 AND ct.is_active = 1 ' . $this->is_free . ' ' . $this->is_featured . '  order by ct.updated_at DESC', [$this->sub_category_id]);
                });

            } else {
                $catalog_ids = Config::get('constant.OFFLINE_CATALOG_IDS_OF_FONT');
                $redis_result = DB::select('SELECT
                                          ct.id as catalog_id,
                                          ct.name,
                                          IF(ct.attribute1 != "",CONCAT("' . Config::get('constant.WEBP_THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.attribute1),"") as webp_thumbnail_img,
                                          ct.is_featured,
                                          ct.is_free,
                                          ct.is_ios_free,
                                          ct.updated_at
                                        FROM
                                          catalog_master as ct,
                                          sub_category_catalog as sct
                                        WHERE
                                          sct.sub_category_id = ? AND
                                          sct.catalog_id = ct.id AND
                                          NOT find_in_set(ct.id,"' . $catalog_ids . '") AND
                                          sct.is_active = 1 AND ct.is_active = 1 ' . $this->is_free . ' ' . $this->is_featured . '  order by ct.updated_at DESC', [$this->sub_category_id]);
            }

            $response = Response::json(array('code' => 200, 'message' => 'Catalogs fetched successfully.', 'cause' => '', 'data' => ['result' => $redis_result, 'prefix_url' => Config::get('constant.AWS_BUCKET_PATH_PHOTO_EDITOR_LAB') . '/']));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            Log::error("getCatalogsByTypeInWebp : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
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

    /* =================================| Advertisement |=============================*/

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

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('sub_category_id', 'platform', 'last_sync_time'), $request)) != '')
                return $response;

            $this->platform = $request->platform;
            $this->sub_category_id = $request->sub_category_id;
            $this->last_sync_time = $request->last_sync_time;
            $this->advertise_id_list = $request->advertise_id_list;
            $is_cache_enable = isset($request->is_cache_enable) ? $request->is_cache_enable : 1;
            $new_array = array();

            if ($is_cache_enable) {
                $redis_result = Cache::remember("getLinkWithLastSyncTime:$this->platform:$this->sub_category_id:$this->last_sync_time", Config::get("constant.CACHE_TIME_24_HOUR"), function () {

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

            } else {

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


                $redis_result = array('link_list' => $result, 'advertise_id_list' => $new_array);

            }

            /*if (count($redis_result) >= 1) {
                $result = $redis_result['link_list'];
                $last_sync_time = $result[0]->updated_at;

            } else {*/
            $last_sync_time = date("Y-m-d H:i:s");
            /*}*/

            $response = Response::json(array('code' => 200, 'message' => 'Advertise link fetched successfully.', 'cause' => '', 'data' => ['total_record' => count($redis_result), 'link_list' => $redis_result['link_list'], 'last_sync_time' => $last_sync_time, 'advertise_id_list' => $redis_result['advertise_id_list']]));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));


        } catch
        (Exception $e) {
            Log::error("getLinkWithLastSyncTime : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get advertise link.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /* =================================| Json |=============================*/

    public function getFeaturedJsonImages(Request $request_body)
    {

        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());

            if (($response = (new VerificationController())->validateRequiredParameter(array('catalog_id'), $request)) != '')
                return $response;

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
            Log::error("getFeaturedJsonImages : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'add featured background images.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
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

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('sub_category_id', 'catalog_id', 'page', 'item_count'), $request)) != '')
                return $response;

            $this->catalog_id = $request->catalog_id;
            $this->sub_category_id = $request->sub_category_id;
            $this->item_count = $request->item_count;
            $this->page = $request->page;
            $this->order_by = isset($request->order_by) ? $request->order_by : 'size';
            $this->order_type = isset($request->order_type) ? $request->order_type : 'DESC';
            $this->offset = ($this->page - 1) * $this->item_count;
            $this->is_cache_enable = isset($request->is_cache_enable) ? $request->is_cache_enable : 1;

            /*if ($this->catalog_id == 0) {
                $total_row_result = DB::select('SELECT COUNT(*) AS total
                                                    FROM images
                                                    WHERE catalog_id IN (SELECT catalog_id
                                                                     FROM sub_category_catalog
                                                                     WHERE sub_category_id = ?) AND is_featured = 1', [$this->sub_category_id]);
                $total_row = $total_row_result[0]->total;
            } else {
                $total_row_result = DB::select('SELECT COUNT(*) as total FROM images WHERE catalog_id = ?', [$this->catalog_id]);
                $total_row = $total_row_result[0]->total;
            }*/

            if ($this->is_cache_enable) {
                $redis_result = Cache::remember("key:getJsonSampleData:$this->page:$this->item_count:$this->catalog_id:$this->sub_category_id", Config::get("constant.CACHE_TIME_6_HOUR"), function () {

                    if ($this->catalog_id == 0) {

                        $total_row_result = DB::select('SELECT COUNT(*) AS total
                                                    FROM images
                                                    WHERE catalog_id IN (SELECT catalog_id
                                                                     FROM sub_category_catalog
                                                                     WHERE sub_category_id = ?) AND is_featured = 1', [$this->sub_category_id]);
                        $total_row = $total_row_result[0]->total;

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

                        $total_row_result = DB::select('SELECT COUNT(*) as total FROM images WHERE catalog_id = ?', [$this->catalog_id]);
                        $total_row = $total_row_result[0]->total;

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

                    $is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;

                    return array('total_record' => $total_row, 'is_next_page' => $is_next_page, 'data' => $result);
                });

            } else {

                if ($this->catalog_id == 0) {

                    $total_row_result = DB::select('SELECT COUNT(*) AS total
                                                        FROM images
                                                        WHERE catalog_id IN (SELECT catalog_id
                                                                         FROM sub_category_catalog
                                                                         WHERE sub_category_id = ?) AND is_featured = 1', [$this->sub_category_id]);
                    $total_row = $total_row_result[0]->total;
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

                    $total_row_result = DB::select('SELECT COUNT(*) as total FROM images WHERE catalog_id = ?', [$this->catalog_id]);
                    $total_row = $total_row_result[0]->total;

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

                $is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;

                $redis_result = array('total_record' => $total_row, 'is_next_page' => $is_next_page, 'data' => $result);
            }

            $response = Response::json(array('code' => 200, 'message' => 'All link fetched successfully.', 'cause' => '', 'data' => $redis_result));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            Log::error("getJsonSampleData : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get sample images.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
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

            $redis_result = Cache::rememberforever("getJsonData:$this->json_id_to_get_json_data", function () {

                $result = DB::select('SELECT
                                            json_data
                                      FROM
                                            images
                                      WHERE
                                            id= ? ', [$this->json_id_to_get_json_data]);

                if (count($result) > 0) {
                    $json_data = json_decode($result[0]->json_data);
                    if ($result[0]->json_data)
                        $json_data->prefix_url = Config::get('constant.AWS_BUCKET_PATH_PHOTO_EDITOR_LAB') . '/';
                    return $json_data;
                } else {
                    return json_decode("{}");
                }
            });

            $response = Response::json(array('code' => 200, 'message' => 'Json fetched successfully.', 'cause' => '', 'data' => $redis_result));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            Log::error("getJsonData : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get sample json.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    //get pages_sequence in string format
    public function getJsonDataV2(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('json_id'), $request)) != '')
                return $response;

            $this->json_id = $request->json_id;

            $redis_result = Cache::rememberforever("getJsonDataV2:$this->json_id", function () {

                $result = DB::select('SELECT
                                          json_data,
                                          COALESCE(json_pages_sequence,"") AS pages_sequence
                                      FROM
                                          images
                                      WHERE
                                          id = ?
                                      ORDER BY updated_at DESC', [$this->json_id]);

                if (count($result) > 0) {

                    $result[0]->json_data = json_decode($result[0]->json_data);

                    if($result[0]->json_data)
                        $result[0]->prefix_url = Config::get('constant.AWS_BUCKET_PATH_PHOTO_EDITOR_LAB').'/';

                    return $result[0];

                } else {
                    return json_decode("{}");
                }

            });

            if (!$redis_result) {
                $redis_result = [];
            }

            $response = Response::json(array('code' => 200, 'message' => 'Json fetched successfully.', 'cause' => '', 'data' => $redis_result));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            Log::error("getJsonDataV2 : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get sample json.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    /*public function setNullStrokeScript(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('catalog_id'), $request)) != '')
                return $response;

            $catalog_id = $request->catalog_id;
            $do_update = isset($request->do_update) ? $request->do_update : 0;
            $limit = isset($request->limit) ? $request->limit : 10;
            $keyword = "'%\"stroke\":\"#%'";

            $result = DB::select('SELECT
                                    id AS content_id,
                                    json_data,
                                    is_multipage
                                  FROM
                                    images
                                  WHERE
                                    json_data LIKE '. $keyword .' AND catalog_id = ? LIMIT ?',[$catalog_id, $limit]);

            $count = count($result);
            $content_ids = array_column($result, 'content_id');
            $message = 'Data get successfully.';

            if ($do_update == 1 && $count > 0){
                DB::beginTransaction();
                foreach ($result AS $data){
                    $json_data = json_decode($data->json_data);
                    $content_id = $data->content_id;
                    $is_multipage = $data->is_multipage;

                    if($is_multipage) {
                        foreach ($json_data AS $json){
                            $text_json = $json->text_json;
                            foreach ($text_json AS $object){
                                $object->stroke = null;
                            }
                        }
                    } else{
                        $text_json = $json_data->text_json;
                        foreach ($text_json AS $object){
                            $object->stroke = null;
                        }
                    }

                    $updated_json_data = json_encode($json_data);
                    DB::update('UPDATE images
                                SET json_data = ?,
                                    updated_at = updated_at
                                WHERE id = ?', [$updated_json_data, $content_id]);
                    $message = 'Json updated successfully.';
                }
                DB::commit();
            }

            $response = Response::json(array('code' => 200, 'message' => $message, 'cause' => '', 'data' => ['content_count' => $count, 'content_ids' => $content_ids]));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            Log::error("setNullStrokeScript : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'set stroke to null.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }*/

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
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('sub_category_id', 'last_sync_time'), $request)) != '')
                return $response;

            $this->last_sync_time = $request->last_sync_time;
            $this->sub_category_id = $request->sub_category_id;
            $is_cache_enable = isset($request->is_cache_enable) ? $request->is_cache_enable : 1;
            $this->where_condition = NULL;

            if ($this->last_sync_time) {
                $this->where_condition = ' AND (ct.updated_at >= "' . $this->last_sync_time . '" OR sct.created_at >= "' . $this->last_sync_time . '")';
            }

            if ($is_cache_enable) {

                $redis_result = Cache::remember("getCatalogBySubCategoryIdWithLastSyncTime:$request->sub_category_id:$request->last_sync_time", Config::get('constant.CACHE_TIME_6_HOUR'), function () {

                    $redis_data = DB::select('SELECT
                                          ct.id AS catalog_id,
                                          ct.name,
                                          IF(ct.image != "",CONCAT("' . Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.image),"") AS thumbnail_img,
                                          IF(ct.image != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.image),"") AS compressed_img,
                                          IF(ct.image != "",CONCAT("' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.image),"") AS original_img,
                                          ct.is_free,
                                          ct.is_featured,
                                          ct.search_category,
                                          ct.updated_at
                                        FROM
                                          catalog_master AS ct,
                                          sub_category_catalog AS sct
                                        WHERE
                                          sct.sub_category_id = ? AND
                                          sct.catalog_id = ct.id AND
                                          sct.is_active = 1 
                                          ' . $this->where_condition . '
                                        ORDER BY ct.updated_at DESC', [$this->sub_category_id]);
                    return $redis_data;
                });
            } else {

                $redis_result = DB::select('SELECT
                                          ct.id AS catalog_id,
                                          ct.name,
                                          IF(ct.image != "",CONCAT("' . Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.image),"") AS thumbnail_img,
                                          IF(ct.image != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.image),"") AS compressed_img,
                                          IF(ct.image != "",CONCAT("' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.image),"") AS original_img,
                                          ct.is_free,
                                          ct.is_featured,
                                          ct.search_category,
                                          ct.updated_at
                                        FROM
                                          catalog_master AS ct,
                                          sub_category_catalog AS sct
                                        WHERE
                                          sct.sub_category_id = ? AND
                                          sct.catalog_id = ct.id AND
                                          sct.is_active = 1 
                                          ' . $this->where_condition . '
                                        ORDER BY ct.updated_at DESC', [$this->sub_category_id]);
            }

            $last_sync_time = date("Y-m-d H:i:s");
            $response = Response::json(array('code' => 200, 'message' => 'Catalog fetched successfully.', 'cause' => '', 'data' => ['total_record' => count($redis_result), 'last_sync_time' => $last_sync_time, 'category_list' => $redis_result]));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            Log::error("getCatalogBySubCategoryIdWithLastSyncTime : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get catalogs.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
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

            /*if ($this->catalog_id == 0) {
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

            } else {*/
            $this->last_sync_time = date("Y-m-d H:i:s");
            /*}*/


            //Log::info('request_data', ['request_data' => $request]);

            if (!Cache::has("pel:getJsonSampleDataWithLastSyncTime:$this->page:$this->item_count:$this->catalog_id:$this->sub_category_id:$request->last_sync_time")) {
                $result = Cache::rememberforever("getJsonSampleDataWithLastSyncTime:$this->page:$this->item_count:$this->catalog_id:$this->sub_category_id:$request->last_sync_time", function () {

                    if ($this->catalog_id == 0) {

                        $total_row_result = DB::select('SELECT COUNT(*) AS total
                                                    FROM images
                                                    WHERE catalog_id IN (SELECT catalog_id
                                                                     FROM sub_category_catalog
                                                                     WHERE sub_category_id = ?) AND is_featured = 1 and updated_at >= ?', [$this->sub_category_id, $this->last_sync_time]);
                        $total_row = $total_row_result[0]->total;
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

                        $total_row_result = DB::select('SELECT COUNT(*) as total FROM images WHERE catalog_id = ? AND updated_at >= ?', [$this->catalog_id, $this->last_sync_time]);
                        $total_row = $total_row_result[0]->total;

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

                    $is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;
                    return array('total_record' => $total_row, 'is_next_page' => $is_next_page, 'data' => $result);
                });
            }

            $redis_result = Cache::get("getJsonSampleDataWithLastSyncTime:$this->page:$this->item_count:$this->catalog_id:$this->sub_category_id:$request->last_sync_time");

            if (!$redis_result) {
                $redis_result = [];
            } else {
                $redis_result['last_sync_time'] = $this->last_sync_time;
            }


            $response = Response::json(array('code' => 200, 'message' => 'All samples fetched successfully.', 'cause' => '', 'data' => $redis_result));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));


            //$response = Response::json(array('code' => 200, 'message' => 'Featured Background Images added successfully!.', 'cause' => '', 'data' => json_decode('{}')));

        } catch
        (Exception $e) {
            Log::error("getJsonSampleDataWithLastSyncTime : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get samples.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
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
     * {//all parameters are compulsory
     * "sub_category_id": 97,
     * "catalog_id": 398, //pass 0 if you don't have catalog_id(in this case you get all featured cards) otherwise you have to pass specific catalog_id
     * "page": 1,
     * "item_count": 2,
     * "last_sync_time": "2017-11-28 12:58:15", //pass 0 on fist api call
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Samples fetched successfully.",
     * "cause": "",
     * "data": {
     * "total_record": 3,
     * "is_next_page": false,
     * "data": [
     * {
     * "json_id": 3326,
     * "sample_image": "http://192.168.0.113/photo_editor_lab_backend/image_bucket/webp_original/5c6d3523a43b5_json_image_1550660899.webp",
     * "is_free": 1,
     * "is_featured": 0,
     * "is_portrait": 0,
     * "height": 256,
     * "width": 512,
     * "search_category": "Twitter",
     * "original_img_height": 512,
     * "original_img_width": 1024,
     * "updated_at": "2019-02-20 11:08:20"
     * },
     * {
     * "json_id": 3325,
     * "sample_image": "http://192.168.0.113/photo_editor_lab_backend/image_bucket/webp_original/5c6d34be91f98_json_image_1550660798.webp",
     * "is_free": 1,
     * "is_featured": 1,
     * "is_portrait": 0,
     * "height": 408,
     * "width": 528,
     * "search_category": "Brochure",
     * "original_img_height": 816,
     * "original_img_width": 1056,
     * "updated_at": "2019-02-20 11:07:06"
     * }
     * ],
     * "last_sync_time": "2019-02-21 09:50:37"
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
            $this->db_last_sync_time = $request->last_sync_time;
            $this->item_count = $request->item_count;
            $this->page = $request->page;
            $this->offset = ($this->page - 1) * $this->item_count;
            $is_cache_enable = isset($request->is_cache_enable) ? $request->is_cache_enable : 1;
            $last_sync_time = date("Y-m-d H:i:s");
            $this->where_condition = NULL;

            if ($this->catalog_id) {
                $this->where_condition .= " AND catalog_id = $this->catalog_id ";
            } else {
                $this->where_condition .= " AND is_featured = 1 AND catalog_id IN (SELECT catalog_id FROM sub_category_catalog WHERE sub_category_id = $this->sub_category_id AND is_active = 1) ";
            }

            if ($this->db_last_sync_time) {
                $this->where_condition .= " AND updated_at >= '" . $this->db_last_sync_time . "' ";
            }

            if ($is_cache_enable) {
                $redis_result = Cache::remember("key:getJsonSampleDataWithLastSyncTime_webp:$this->page:$this->item_count:$this->catalog_id:$this->sub_category_id:$this->db_last_sync_time", Config::get('constant.CACHE_TIME_6_HOUR'), function () {

                    $host_name = request()->getHttpHost(); // With port if there is. Eg: mydomain.com:81
                    $certificate_maker_host_name = Config::get('constant.HOST_NAME_OF_CERTIFICATE_MAKER');

                    //to pass compress image(jpg/png) only for certificate_maker app because webp is not supported there into iOS
                    $image_url = ($host_name == $certificate_maker_host_name && $this->sub_category_id == 4) ? 'IF(image != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",image),"") as sample_image,' : 'IF(attribute1 != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",attribute1),"") as sample_image,';

                    $total_row_result = DB::select('SELECT 
                                                        COUNT(id) AS total
                                                    FROM 
                                                        images
                                                    WHERE 
                                                        is_active = 1
                                                        ' . $this->where_condition . ' ');
                    $total_row = $total_row_result[0]->total;

                    if ($total_row) {

                        $result = DB::select('SELECT
                                              id AS json_id,
                                              ' . $image_url . '
                                              IF(attribute1 != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",attribute1),"") AS webp_original_img,
                                              IF(content_type = ' . Config::get('constant.CONTENT_TYPE_FOR_BEFORE_AFTER_IMAGE') . ' AND attribute1 != "",CONCAT("' . Config::get('constant.WEBP_THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '","after_image_",attribute1),"") AS after_image,
                                              IF(content_type = ' . Config::get('constant.CONTENT_TYPE_FOR_BEFORE_AFTER_IMAGE') . ' AND attribute1 != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '","after_image_",attribute1),"") AS webp_original_after_img,
                                              IF(content_type = ' . Config::get('constant.CONTENT_TYPE_FOR_SAMPLE_IMAGE_GIF') . ' AND image != "",CONCAT("' . Config::get('constant.ORIGINAL_VIDEO_DIRECTORY_OF_DIGITAL_OCEAN') . '",SUBSTRING_INDEX(image,".",1),".gif"),"") AS sample_gif,
                                              is_free,
                                              is_ios_free,
                                              is_featured,
                                              is_portrait,
                                              search_category,
                                              template_name,
                                              coalesce(height,0) AS height,
                                              coalesce(width,0) AS width,
                                              coalesce(search_category,"") AS search_category,
                                              coalesce(original_img_height,0) AS original_img_height,
                                              coalesce(original_img_width,0) AS original_img_width,
                                              COALESCE(multiple_images,"") AS multiple_images,
                                              COALESCE(json_pages_sequence,"") AS pages_sequence,
                                              COALESCE(LENGTH(json_pages_sequence) - LENGTH(REPLACE(json_pages_sequence, ",","")) + 1,1) AS total_pages,
                                              content_type,
                                              updated_at
                                            FROM
                                              images
                                            WHERE
                                              is_active = 1 
                                              ' . $this->where_condition . '
                                            ORDER BY updated_at DESC LIMIT ?, ?', [$this->offset, $this->item_count]);
                    } else {
                        $result = [];
                    }

                    $is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;
                    return array('total_record' => $total_row, 'is_next_page' => $is_next_page, 'data' => $result, 'prefix_url' => Config::get('constant.AWS_BUCKET_PATH_PHOTO_EDITOR_LAB') . '/');

                });

                $redis_result['last_sync_time'] = $last_sync_time;
            } else {

                $host_name = request()->getHttpHost(); // With port if there is. Eg: mydomain.com:81
                $certificate_maker_host_name = Config::get('constant.HOST_NAME_OF_CERTIFICATE_MAKER');

                //to pass compress image(jpg/png) only for certificate_maker app because webp is not supported there into iOS
                $image_url = ($host_name == $certificate_maker_host_name && $this->sub_category_id == 4) ? 'IF(image != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",image),"") as sample_image,' : 'IF(attribute1 != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",attribute1),"") as sample_image,';

                $total_row_result = DB::select('SELECT 
                                                        COUNT(id) AS total
                                                    FROM 
                                                        images
                                                    WHERE 
                                                        (is_active = 1 OR is_active = 0)
                                                        ' . $this->where_condition . ' ');
                $total_row = $total_row_result[0]->total;

                if ($total_row) {

                    $result = DB::select('SELECT
                                              id AS json_id,
                                              ' . $image_url . '
                                              IF(attribute1 != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",attribute1),"") AS webp_original_img,
                                              IF(content_type = ' . Config::get('constant.CONTENT_TYPE_FOR_BEFORE_AFTER_IMAGE') . ' AND attribute1 != "",CONCAT("' . Config::get('constant.WEBP_THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '","after_image_",attribute1),"") AS after_image,
                                              IF(content_type = ' . Config::get('constant.CONTENT_TYPE_FOR_BEFORE_AFTER_IMAGE') . ' AND attribute1 != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '","after_image_",attribute1),"") AS webp_original_after_img,
                                              IF(content_type = ' . Config::get('constant.CONTENT_TYPE_FOR_SAMPLE_IMAGE_GIF') . ' AND image != "",CONCAT("' . Config::get('constant.ORIGINAL_VIDEO_DIRECTORY_OF_DIGITAL_OCEAN') . '",SUBSTRING_INDEX(image,".",1),".gif"),"") AS sample_gif,
                                              is_free,
                                              is_ios_free,
                                              is_featured,
                                              is_portrait,
                                              search_category,
                                              template_name,
                                              coalesce(height,0) AS height,
                                              coalesce(width,0) AS width,
                                              coalesce(search_category,"") AS search_category,
                                              coalesce(original_img_height,0) AS original_img_height,
                                              coalesce(original_img_width,0) AS original_img_width,
                                              COALESCE(multiple_images,"") AS multiple_images,
                                              COALESCE(json_pages_sequence,"") AS pages_sequence,
                                              COALESCE(LENGTH(json_pages_sequence) - LENGTH(REPLACE(json_pages_sequence, ",","")) + 1,1) AS total_pages,
                                              content_type,
                                              updated_at
                                            FROM
                                              images
                                            WHERE
                                              (is_active = 1 OR is_active = 0)
                                              ' . $this->where_condition . '
                                            ORDER BY updated_at DESC LIMIT ?, ?', [$this->offset, $this->item_count]);
                } else {
                    $result = [];
                }

                $is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;
                $redis_result = array('total_record' => $total_row, 'is_next_page' => $is_next_page, 'data' => $result, 'prefix_url' => Config::get('constant.AWS_BUCKET_PATH_PHOTO_EDITOR_LAB') . '/');
                $redis_result['last_sync_time'] = $last_sync_time;

            }

            $response = Response::json(array('code' => 200, 'message' => 'Samples fetched successfully.', 'cause' => '', 'data' => $redis_result));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            Log::error("getJsonSampleDataWithLastSyncTime_webp : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get samples.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    public function getJsonSampleDataWithLastSyncTime_webp_v2(Request $request_body)
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
            $this->offset = ($this->page - 1) * $this->item_count;
            $is_cache_enable = isset($request->is_cache_enable) ? $request->is_cache_enable : 1;

            $last_sync_time = date("Y-m-d H:i:s");


            if ($is_cache_enable){
                $redis_result = Cache::remember("getJsonSampleDataWithLastSyncTime_webp_v2:$this->page:$this->item_count:$this->catalog_id:$this->sub_category_id:$request->last_sync_time",config('constant.CACHE_TIME_6_HOUR'), function () {

                        $host_name = request()->getHttpHost(); // With port if there is. Eg: mydomain.com:81
                        $certificate_maker_host_name = Config::get('constant.HOST_NAME_OF_CERTIFICATE_MAKER');

                        //to pass compress image(jpg/png) only for certificate_maker app because webp is not supported there into iOS
                        $image_url = ($host_name == $certificate_maker_host_name && $this->sub_category_id == 4) ? 'IF(image != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",image),"") as sample_image,' : 'IF(attribute1 != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",attribute1),"") as sample_image,';

                        if ($this->catalog_id == 0) {
                            $total_row = Cache::remember("getJsonSampleDataWithLastSyncTime_webp_v2:$this->catalog_id:$this->sub_category_id:$this->last_sync_date",config('constant.CACHE_TIME_6_HOUR'), function () {
                                $total_row_result = DB::select('SELECT COUNT(*) AS total
                                                            FROM images
                                                            WHERE 
                                                              catalog_id IN (SELECT catalog_id
                                                                             FROM sub_category_catalog
                                                                             WHERE sub_category_id = ?) AND 
                                                              is_featured = 1 AND 
                                                              updated_at >= ?', [$this->sub_category_id, $this->last_sync_date]);
                                return $total_row = $total_row_result[0]->total;
                            });

                            $result = DB::select('SELECT
                                              id AS json_id,
                                              '. $image_url .'
                                              is_free,
                                              is_featured,
                                              is_portrait,
                                              coalesce(height,0) AS height,
                                              coalesce(width,0) AS width,
                                              coalesce(search_category,"") AS search_category,
                                              coalesce(original_img_height,0) AS original_img_height,
                                              coalesce(original_img_width,0) AS original_img_width,
                                              updated_at
                                            FROM
                                              images
                                            WHERE
                                              catalog_id IN(select catalog_id FROM sub_category_catalog WHERE sub_category_id = ? AND is_active = 1) AND
                                              is_featured = 1 AND
                                              updated_at >= ?
                                            ORDER BY updated_at DESC LIMIT ?, ?', [$this->sub_category_id, $this->last_sync_date, $this->offset, $this->item_count]);

                        } else {
                                $total_row = Cache::remember("getJsonSampleDataWithLastSyncTime_webp_v2:$this->catalog_id:$this->sub_category_id:$this->last_sync_date",config('constant.CACHE_TIME_6_HOUR'), function () {
                                    $total_row_result = DB::select('SELECT COUNT(*) AS total FROM images WHERE catalog_id = ? AND updated_at >= ?', [$this->catalog_id, $this->last_sync_date]);
                                    return $total_row = $total_row_result[0]->total;
                                });

                            $result = DB::select('SELECT
                                                  id AS json_id,
                                                  '. $image_url .'
                                                  is_free,
                                                  is_featured,
                                                  is_portrait,
                                                  coalesce(height,0) AS height,
                                                  coalesce(width,0) AS width,
                                                  coalesce(search_category,"") AS search_category,
                                                  coalesce(original_img_height,0) AS original_img_height,
                                                  coalesce(original_img_width,0) AS original_img_width,
                                                  updated_at
                                                FROM
                                                  images
                                                WHERE
                                                  catalog_id = ? AND
                                                  updated_at >= ?
                                                ORDER BY updated_at DESC LIMIT ?, ?', [$this->catalog_id, $this->last_sync_date, $this->offset, $this->item_count]);
                        }
                        $is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;
                        return array('total_record' => $total_row, 'is_next_page' => $is_next_page, 'data' => $result);

                    });
            }else {

                    $host_name = request()->getHttpHost(); // With port if there is. Eg: mydomain.com:81
                    $certificate_maker_host_name = Config::get('constant.HOST_NAME_OF_CERTIFICATE_MAKER');

                    //to pass compress image(jpg/png) only for certificate_maker app because webp is not supported there into iOS
                    $image_url = ($host_name == $certificate_maker_host_name && $this->sub_category_id == 4) ? 'IF(image != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",image),"") as sample_image,' : 'IF(attribute1 != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",attribute1),"") as sample_image,';

                    if ($this->catalog_id == 0) {
                        $total_row_result = DB::select('SELECT COUNT(*) AS total
                                                            FROM images
                                                            WHERE 
                                                              catalog_id IN (SELECT catalog_id
                                                                             FROM sub_category_catalog
                                                                             WHERE sub_category_id = ?) AND 
                                                              is_featured = 1 AND 
                                                              updated_at >= ?', [$this->sub_category_id, $this->last_sync_date]);
                        $total_row = $total_row_result[0]->total;

                        $result = DB::select('SELECT
                                              id AS json_id,
                                              ' . $image_url . '
                                              is_free,
                                              is_featured,
                                              is_portrait,
                                              coalesce(height,0) AS height,
                                              coalesce(width,0) AS width,
                                              coalesce(search_category,"") AS search_category,
                                              coalesce(original_img_height,0) AS original_img_height,
                                              coalesce(original_img_width,0) AS original_img_width,
                                              updated_at
                                            FROM
                                              images
                                            WHERE
                                              catalog_id IN(select catalog_id FROM sub_category_catalog WHERE sub_category_id = ? AND is_active = 1) AND
                                              is_featured = 1 AND
                                              updated_at >= ?
                                            ORDER BY updated_at DESC LIMIT ?, ?', [$this->sub_category_id, $this->last_sync_date, $this->offset, $this->item_count]);

                    } else {
                        $total_row_result = DB::select('SELECT COUNT(*) AS total FROM images WHERE catalog_id = ? AND updated_at >= ?', [$this->catalog_id, $this->last_sync_date]);
                        $total_row = $total_row_result[0]->total;

                        $result = DB::select('SELECT
                                                  id AS json_id,
                                                  ' . $image_url . '
                                                  is_free,
                                                  is_featured,
                                                  is_portrait,
                                                  coalesce(height,0) AS height,
                                                  coalesce(width,0) AS width,
                                                  coalesce(search_category,"") AS search_category,
                                                  coalesce(original_img_height,0) AS original_img_height,
                                                  coalesce(original_img_width,0) AS original_img_width,
                                                  updated_at
                                                FROM
                                                  images
                                                WHERE
                                                  catalog_id = ? AND
                                                  updated_at >= ?
                                                ORDER BY updated_at DESC LIMIT ?, ?', [$this->catalog_id, $this->last_sync_date, $this->offset, $this->item_count]);
                    }
                    $is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;
                    $redis_result = array('total_record' => $total_row, 'is_next_page' => $is_next_page, 'data' => $result);

            }

            if (!$redis_result) {
                $redis_result = [];
            } else {
                $redis_result['last_sync_time'] = $last_sync_time;
            }

            $response = Response::json(array('code' => 200, 'message' => 'Samples fetched successfully.', 'cause' => '', 'data' => $redis_result));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch
        (Exception $e) {
            Log::error("getJsonSampleDataWithLastSyncTime_webp_v2 : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get samples.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    //shuffle all featured templates for 24hours from given sub_category_id or get templates by catalog_id with last_sync_time

    /**
     * @api {post} getTemplatesWithLastSyncTime   getTemplatesWithLastSyncTime
     * @apiName getTemplatesWithLastSyncTime
     * @apiGroup User
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {//all parameters are compulsory
     * "sub_category_id": 97,
     * "catalog_id": 398, //pass 0 if you don't have catalog_id(in this case you get all featured cards) otherwise you have to pass specific catalog_id
     * "page": 1,
     * "item_count": 2,
     * "last_sync_time": "2017-11-28 09:50:37" //pass 0 on fist api call
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Samples fetched successfully.",
     * "cause": "",
     * "data": {
     * "total_record": 3,
     * "is_next_page": false,
     * "data": [
     * {
     * "json_id": 3326,
     * "sample_image": "http://192.168.0.113/photo_editor_lab_backend/image_bucket/webp_original/5c6d3523a43b5_json_image_1550660899.webp",
     * "is_free": 1,
     * "is_featured": 0,
     * "is_portrait": 0,
     * "height": 256,
     * "width": 512,
     * "search_category": "Twitter",
     * "original_img_height": 512,
     * "original_img_width": 1024,
     * "updated_at": "2019-02-20 11:08:20"
     * },
     * {
     * "json_id": 3325,
     * "sample_image": "http://192.168.0.113/photo_editor_lab_backend/image_bucket/webp_original/5c6d34be91f98_json_image_1550660798.webp",
     * "is_free": 1,
     * "is_featured": 1,
     * "is_portrait": 0,
     * "height": 408,
     * "width": 528,
     * "search_category": "Brochure",
     * "original_img_height": 816,
     * "original_img_width": 1056,
     * "updated_at": "2019-02-20 11:07:06"
     * }
     * ],
     * "last_sync_time": "2019-02-21 09:50:37"
     * }
     * }
     */
    public function getTemplatesWithLastSyncTime(Request $request_body)
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
//            $this->item_count = $request->item_count;
            $this->item_count = Config::get('constant.ITEM_COUNT_PER_PAGE_FOR_FEATURED_TEMPLATES');
            $this->page = $request->page;
            $this->offset = ($this->page - 1) * $this->item_count;
            $last_sync_time = date("Y-m-d H:i:s");

            //return all featured templates from given sub_category_id with pagination and shuffle when catalog_id=0
            if ($this->catalog_id == 0) {

                //caching time of redis key to get all featured templates
                $this->time_of_expired_redis_key = Config::get('constant.EXPIRATION_TIME_OF_REDIS_KEY_TO_GET_ALL_FEATURED_TEMPLATES');
                if (Cache::has("getFeaturedTemplatesWithWebp$this->page:$this->item_count:$this->catalog_id:$this->sub_category_id")) {
                    $redis_result = Cache::get("getFeaturedTemplatesWithWebp$this->page:$this->item_count:$this->catalog_id:$this->sub_category_id");
                }else {
                    $featured_templates = $this->getAllFeaturedTemplatesWithShuffling($this->sub_category_id);
                    $this->total_row = $featured_templates['total_row'];
                    $this->total_page = array_chunk($featured_templates['featured_templates'], $this->item_count);
                    if ($this->page <= count($this->total_page)) {
                        $keys = Redis::keys('pel:getFeaturedTemplatesWithWebp*');
                        foreach ($keys as $key) {
                            Redis::del($key);
                        }
                        for ($this->i = 0; $this->i < count($this->total_page); $this->i++) {
                            $this->page_no = $this->i + 1;
                            $result = Cache::remember("getFeaturedTemplatesWithWebp$this->page_no:$this->item_count:$this->catalog_id:$this->sub_category_id", $this->time_of_expired_redis_key, function () {
                                if($this->page_no == count($this->total_page)) {
                                    $is_next_page = false;
                                }else{
                                    $is_next_page = true;
                                }
                                return array('total_record' => $this->total_row, 'is_next_page' => $is_next_page, 'data' => $this->total_page[$this->i], 'prefix_url' => Config::get('constant.AWS_BUCKET_PATH_PHOTO_EDITOR_LAB').'/');
                            });
                        }
                        $redis_result = Cache::get("getFeaturedTemplatesWithWebp$this->page:$this->item_count:$this->catalog_id:$this->sub_category_id");
                    } else {
                        return Response::json(array('code' => 201, 'message' => 'Page you request does not exist.', 'cause' => '', 'data' => json_decode("{}")));
                    }
                }
//                if (!Cache::has("pel:getFeaturedTemplatesWithWebp$this->page:$this->item_count:$this->catalog_id:$this->sub_category_id")) {
//                      $result = Cache::remember("getFeaturedTemplatesWithWebp$this->page:$this->item_count:$this->catalog_id:$this->sub_category_id", $this->time_of_expired_redis_key, function () {
//
//                        //to get all featured templates with {[(shuffling)((current_date) + (3:6 ratio of free/pro))] + [(shuffling)((remaining templates ORDER BY update_time DESC) + (3:6 ratio of free/pro))]}
//                        $featured_templates = $this->getAllFeaturedTemplatesWithShuffling($this->sub_category_id);
//
//                        if (!$featured_templates) {
//                            $featured_templates = [];
//                        }
//
//                        $total_row = $featured_templates['total_row'];
//
//                        //get elements from array with start & end position
//                        $result = array_slice($featured_templates['featured_templates'], $this->offset, $this->item_count);
//
//                        $is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;
//                        return array('total_record' => $total_row, 'is_next_page' => $is_next_page, 'data' => $result);
//
//                    });
//                }
//                $redis_result = Cache::get("getFeaturedTemplatesWithWebp$this->page:$this->item_count:$this->catalog_id:$this->sub_category_id");

            } else {

                //return templates by catalog_id with pagination
                if (!Cache::has("pel:getJsonSampleDataWithLastSyncTime_webp$this->page:$this->item_count:$this->catalog_id:$this->sub_category_id:$request->last_sync_time")) {
                    $result = Cache::rememberforever("getJsonSampleDataWithLastSyncTime_webp$this->page:$this->item_count:$this->catalog_id:$this->sub_category_id:$request->last_sync_time", function () {

                        $total_row_result = DB::select('SELECT COUNT(*) AS total FROM images WHERE catalog_id = ? AND updated_at >= ?', [$this->catalog_id, $this->last_sync_date]);
                        $total_row = $total_row_result[0]->total;
                        $result = DB::select('SELECT
                                                  id AS json_id,
                                                  IF(attribute1 != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",attribute1),"") AS sample_image,
                                                  is_free,
                                                  is_featured,
                                                  is_portrait,
                                                  coalesce(height,0) AS height,
                                                  coalesce(width,0) AS width,
                                                  coalesce(search_category,"") AS search_category,
                                                  coalesce(original_img_height,0) AS original_img_height,
                                                  coalesce(original_img_width,0) AS original_img_width,
                                                  COALESCE(multiple_images,"") AS multiple_images,
                                                  COALESCE(json_pages_sequence,"") AS pages_sequence,
                                                  COALESCE(LENGTH(json_pages_sequence) - LENGTH(REPLACE(json_pages_sequence, ",","")) + 1,1) AS total_pages,
                                                  updated_at
                                                FROM
                                                  images
                                                WHERE
                                                  catalog_id = ? AND
                                                  updated_at >= ?
                                                ORDER BY updated_at DESC LIMIT ?, ?', [$this->catalog_id, $this->last_sync_date, $this->offset, $this->item_count]);

                        $is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;
                        return array('total_record' => $total_row, 'is_next_page' => $is_next_page, 'data' => $result, 'prefix_url' => Config::get('constant.AWS_BUCKET_PATH_PHOTO_EDITOR_LAB').'/');


                    });
                }

                $redis_result = Cache::get("getJsonSampleDataWithLastSyncTime_webp$this->page:$this->item_count:$this->catalog_id:$this->sub_category_id:$request->last_sync_time");

            }

            if (!$redis_result) {
                $redis_result = [];
            } else {
                $redis_result['last_sync_time'] = $last_sync_time;
            }

            $response = Response::json(array('code' => 200, 'message' => 'Samples fetched successfully.', 'cause' => '', 'data' => $redis_result));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch(Exception $e) {
            Log::error("getTemplatesWithLastSyncTime : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get samples.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
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
            Log::error("getDeletedJsonId : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get deleted json id.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    public function getJsonSampleDataWithLastSyncTime_webpIos(Request $request_body)
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

            $response = Response::json(array('code' => 200, 'message' => 'Samples fetched successfully.', 'cause' => '', 'data' => ['total_record' => $total_row, 'is_next_page' => $is_next_page, 'last_sync_time' => $last_sync_time, 'data' => $redis_result]));
            //$response = Response::json(array('code' => 200, 'message' => 'Sample images fetched successfully.', 'cause' => '', 'data' => $redis_result));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch
        (Exception $e) {
            Log::error("getJsonSampleDataWithLastSyncTime_webpIos : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get samples.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
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
     * "message": "Featured cards fetched successfully.",
     * "cause": "",
     * "data": {
     * "result": [
     * {
     *     "catalog_id": 1068,
     *     "name": "BeforeAfter",
     *     "thumbnail_img": "http://192.168.0.105/photo_editor_lab_backend/image_bucket/thumbnail/61f11140895d6_catalog_img_1643188544.jpg",
     *     "compressed_img": "http://192.168.0.105/photo_editor_lab_backend/image_bucket/compressed/61f11140895d6_catalog_img_1643188544.jpg",
     *     "original_img": "http://192.168.0.105/photo_editor_lab_backend/image_bucket/original/61f11140895d6_catalog_img_1643188544.jpg",
     *     "is_free": 1,
     *     "is_ios_free": 1,
     *     "is_featured": 1,
     *     "updated_at": "2022-01-26 09:15:49",
     *     "featured_cards": [
     *         {
     *             "json_id": 43844,
     *             "sample_image": "http://192.168.0.105/photo_editor_lab_backend/image_bucket/webp_thumbnail/61f11461b7387_json_image_1643189345.webp",
     *             "webp_original_img": "http://192.168.0.105/photo_editor_lab_backend/image_bucket/webp_original/61f11461b7387_json_image_1643189345.webp",
     *             "sample_gif": "",
     *             "after_image": "http://192.168.0.105/photo_editor_lab_backend/image_bucket/webp_thumbnail/after_image_61f11461b7387_json_image_1643189345.webp",
     *             "webp_original_after_img": "http://192.168.0.105/photo_editor_lab_backend/image_bucket/webp_original/after_image_61f11461b7387_json_image_1643189345.webp",
     *             "is_free": 1,
     *             "is_ios_free": 1,
     *             "is_featured": 0,
     *             "is_portrait": 1,
     *             "height": 400,
     *             "width": 325,
     *             "multiple_images": "",
     *             "pages_sequence": "",
     *             "total_pages": 1,
     *             "content_type": 3,
     *             "updated_at": "2022-01-26 09:33:55"
     *         },
     *         {
     *             "json_id": 43845,
     *             "sample_image": "http://192.168.0.105/photo_editor_lab_backend/image_bucket/webp_thumbnail/61f1157462ad4_json_image_1643189620.webp",
     *             "webp_original_img": "http://192.168.0.105/photo_editor_lab_backend/image_bucket/webp_original/61f1157462ad4_json_image_1643189620.webp",
     *             "sample_gif": "",
     *             "after_image": "http://192.168.0.105/photo_editor_lab_backend/image_bucket/webp_thumbnail/after_image_61f1157462ad4_json_image_1643189620.webp",
     *             "webp_original_after_img": "http://192.168.0.105/photo_editor_lab_backend/image_bucket/webp_original/after_image_61f1157462ad4_json_image_1643189620.webp",
     *             "is_free": 1,
     *             "is_ios_free": 1,
     *             "is_featured": 0,
     *             "is_portrait": 1,
     *             "height": 400,
     *             "width": 325,
     *             "multiple_images": "",
     *             "pages_sequence": "",
     *             "total_pages": 1,
     *             "content_type": 3,
     *             "updated_at": "2022-01-26 09:33:41"
     *         },
     *         {
     *             "json_id": 43843,
     *             "sample_image": "http://192.168.0.105/photo_editor_lab_backend/image_bucket/webp_thumbnail/61f113edd1ada_json_image_1643189229.webp",
     *             "webp_original_img": "http://192.168.0.105/photo_editor_lab_backend/image_bucket/webp_original/61f113edd1ada_json_image_1643189229.webp",
     *             "sample_gif": "",
     *             "after_image": "http://192.168.0.105/photo_editor_lab_backend/image_bucket/webp_thumbnail/after_image_61f113edd1ada_json_image_1643189229.webp",
     *             "webp_original_after_img": "http://192.168.0.105/photo_editor_lab_backend/image_bucket/webp_original/after_image_61f113edd1ada_json_image_1643189229.webp",
     *             "is_free": 1,
     *             "is_ios_free": 1,
     *             "is_featured": 0,
     *             "is_portrait": 1,
     *             "height": 400,
     *             "width": 325,
     *             "multiple_images": "",
     *             "pages_sequence": "",
     *             "total_pages": 1,
     *             "content_type": 3,
     *             "updated_at": "2022-01-26 09:27:10"
     *         },
     *         {
     *             "json_id": 43842,
     *             "sample_image": "http://192.168.0.105/photo_editor_lab_backend/image_bucket/webp_thumbnail/61f113c4d8acb_json_image_1643189188.webp",
     *             "webp_original_img": "http://192.168.0.105/photo_editor_lab_backend/image_bucket/webp_original/61f113c4d8acb_json_image_1643189188.webp",
     *             "sample_gif": "",
     *             "after_image": "http://192.168.0.105/photo_editor_lab_backend/image_bucket/webp_thumbnail/after_image_61f113c4d8acb_json_image_1643189188.webp",
     *             "webp_original_after_img": "http://192.168.0.105/photo_editor_lab_backend/image_bucket/webp_original/after_image_61f113c4d8acb_json_image_1643189188.webp",
     *             "is_free": 1,
     *             "is_ios_free": 1,
     *             "is_featured": 0,
     *             "is_portrait": 1,
     *             "height": 400,
     *             "width": 325,
     *             "multiple_images": "",
     *             "pages_sequence": "",
     *             "total_pages": 1,
     *             "content_type": 3,
     *             "updated_at": "2022-01-26 09:26:29"
     *         },
     *         {
     *             "json_id": 43841,
     *             "sample_image": "http://192.168.0.105/photo_editor_lab_backend/image_bucket/webp_thumbnail/61f1138fd2902_json_image_1643189135.webp",
     *             "webp_original_img": "http://192.168.0.105/photo_editor_lab_backend/image_bucket/webp_original/61f1138fd2902_json_image_1643189135.webp",
     *             "sample_gif": "",
     *             "after_image": "http://192.168.0.105/photo_editor_lab_backend/image_bucket/webp_thumbnail/after_image_61f1138fd2902_json_image_1643189135.webp",
     *             "webp_original_after_img": "http://192.168.0.105/photo_editor_lab_backend/image_bucket/webp_original/after_image_61f1138fd2902_json_image_1643189135.webp",
     *             "is_free": 1,
     *             "is_ios_free": 1,
     *             "is_featured": 0,
     *             "is_portrait": 1,
     *             "height": 400,
     *             "width": 325,
     *             "multiple_images": "",
     *             "pages_sequence": "",
     *             "total_pages": 1,
     *             "content_type": 3,
     *             "updated_at": "2022-01-26 09:25:36"
     *         }
     *     ]
     * },
     * {
     *      "catalog_id": 168,
     *      "name": "Business Card Catalog2",
     *      "thumbnail_img": "http://192.168.0.105/photo_editor_lab_backend/image_bucket/thumbnail/5a1d0851d6d32_catalog_img_1511852113.png",
     *      "compressed_img": "http://192.168.0.105/photo_editor_lab_backend/image_bucket/compressed/5a1d0851d6d32_catalog_img_1511852113.png",
     *      "original_img": "http://192.168.0.105/photo_editor_lab_backend/image_bucket/original/5a1d0851d6d32_catalog_img_1511852113.png",
     *      "is_free": 1,
     *      "is_featured": 1,
     *      "updated_at": "2018-08-11 04:13:20",
     *      "featured_cards": [
     *         {
     *              "json_id": 414,
     *              "sample_image": "http://192.168.0.105/photo_editor_lab_backend/image_bucket/webp_thumbnail/5a1f9747c534f_json_image_1512019783.webp",
     *              "is_free": 1,
     *              "is_featured": 1,
     *              "is_portrait": 0,
     *              "height": 300,
     *              "width": 525,
     *              "updated_at": "2018-08-31 10:02:15"
     *         },
     *         {
     *              "json_id": 415,
     *              "sample_image": "http://192.168.0.105/photo_editor_lab_backend/image_bucket/webp_thumbnail/5a1f974dc5c1a_json_image_1512019789.webp",
     *              "is_free": 1,
     *              "is_featured": 1,
     *              "is_portrait": 0,
     *              "height": 300,
     *              "width": 525,
     *              "updated_at": "2018-08-31 10:02:03"
     *         },
     *         {
     *              "json_id": 417,
     *              "sample_image": "http://192.168.0.105/photo_editor_lab_backend/image_bucket/webp_thumbnail/5a1f97592443d_json_image_1512019801.webp",
     *              "is_free": 1,
     *              "is_featured": 1,
     *              "is_portrait": 0,
     *              "height": 300,
     *              "width": 525,
     *              "updated_at": "2018-08-31 10:02:03"
     *         },
     *         {
     *              "json_id": 418,
     *              "sample_image": "http://192.168.0.105/photo_editor_lab_backend/image_bucket/webp_thumbnail/5a1f975f6f461_json_image_1512019807.webp",
     *              "is_free": 1,
     *              "is_featured": 1,
     *              "is_portrait": 0,
     *              "height": 300,
     *              "width": 525,
     *              "updated_at": "2018-08-31 10:02:02"
     *         },
     *         {
     *               "json_id": 419,
     *               "sample_image": "http://192.168.0.105/photo_editor_lab_backend/image_bucket/webp_thumbnail/5a1f9765255c2_json_image_1512019813.webp",
     *               "is_free": 1,
     *               "is_featured": 1,
     *               "is_portrait": 0,
     *               "height": 300,
     *               "width": 525,
     *               "updated_at": "2018-08-31 10:02:02"
     *         }
     *     ]
     * },
     * {
     *       "catalog_id": 167,
     *       "name": "Business Card Catalog1",
     *       "thumbnail_img": "http://192.168.0.105/photo_editor_lab_backend/image_bucket/thumbnail/5a17fab520a09_catalog_img_1511520949.png",
     *       "compressed_img": "http://192.168.0.105/photo_editor_lab_backend/image_bucket/compressed/5a17fab520a09_catalog_img_1511520949.png",
     *       "original_img": "http://192.168.0.105/photo_editor_lab_backend/image_bucket/original/5a17fab520a09_catalog_img_1511520949.png",
     *       "is_free": 1,
     *       "is_featured": 1,
     *       "updated_at": "2017-11-28 07:42:02",
     *       "featured_cards": []
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
            $is_cache_enable = isset($request->is_cache_enable) ? $request->is_cache_enable : 1;
            $this->item_count = Config::get('constant.ITEM_COUNT_OF_FEATURED_JSON');
            $this->offset = 0;

            if ($is_cache_enable) {

                $redis_result = Cache::remember("getFeaturedJsonSampleData_webp:$this->item_count:$this->sub_category_id", Config::get('constant.CACHE_TIME_6_HOUR'), function () {

                    $catalogs = DB::select('SELECT
                                                ct.id AS catalog_id,
                                                ct.name,
                                                IF(ct.image != "",CONCAT("' . Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.image),"") AS thumbnail_img,
                                                IF(ct.image != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.image),"") AS compressed_img,
                                                IF(ct.image != "",CONCAT("' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.image),"") AS original_img,
                                                IF(ct.icon != "",CONCAT("' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.icon),"") AS icon,
                                                ct.is_free,
                                                ct.is_ios_free,
                                                ct.is_featured,
                                                ct.updated_at
                                            FROM
                                                catalog_master AS ct,
                                                sub_category_catalog AS sct
                                            WHERE
                                                sct.sub_category_id = ? AND
                                                sct.catalog_id = ct.id AND
                                                sct.is_active = 1 AND
                                                ct.is_featured = 1
                                            ORDER BY ct.updated_at DESC', [$this->sub_category_id]);

                    foreach ($catalogs as $i => $key) {

                        $featured_cards = DB::select('SELECT
                                                            id AS json_id,
                                                            IF(attribute1 != "",CONCAT("' . Config::get('constant.WEBP_THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",attribute1),"") AS sample_image,
                                                            IF(attribute1 != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",attribute1),"") AS webp_original_img,
                                                            IF(content_type = ' . Config::get('constant.CONTENT_TYPE_FOR_SAMPLE_IMAGE_GIF') . ' AND image != "",CONCAT("' . Config::get('constant.ORIGINAL_VIDEO_DIRECTORY_OF_DIGITAL_OCEAN') . '",SUBSTRING_INDEX(image,".",1),".gif"),"") AS sample_gif,
                                                            IF(content_type = ' . Config::get('constant.CONTENT_TYPE_FOR_BEFORE_AFTER_IMAGE') . ' AND attribute1 != "",CONCAT("' . Config::get('constant.WEBP_THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '","after_image_",attribute1),"") AS after_image,
                                                            IF(content_type = ' . Config::get('constant.CONTENT_TYPE_FOR_BEFORE_AFTER_IMAGE') . ' AND attribute1 != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '","after_image_",attribute1),"") AS webp_original_after_img,
                                                            is_free,
                                                            is_ios_free,
                                                            is_featured,
                                                            is_portrait,
                                                            COALESCE(height,0) AS height,
                                                            COALESCE(width,0) AS width,
                                                            COALESCE(multiple_images,"") AS multiple_images,
                                                            COALESCE(json_pages_sequence,"") AS pages_sequence,
                                                            COALESCE(LENGTH(json_pages_sequence) - LENGTH(REPLACE(json_pages_sequence, ",","")) + 1,1) AS total_pages,
                                                            content_type,
                                                            updated_at
                                                        FROM
                                                            images
                                                        WHERE
                                                            catalog_id = ? AND
                                                            is_active = 1
                                                        ORDER BY updated_at DESC LIMIT ?, ?', [$key->catalog_id, $this->offset, $this->item_count]);

                        $key->featured_cards = $featured_cards;
                    }
                    return $catalogs;
                });

            } else {

                $redis_result = DB::select('SELECT
                                                ct.id AS catalog_id,
                                                ct.name,
                                                IF(ct.image != "",CONCAT("' . Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.image),"") AS thumbnail_img,
                                                IF(ct.image != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.image),"") AS compressed_img,
                                                IF(ct.image != "",CONCAT("' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.image),"") AS original_img,
                                                IF(ct.icon != "",CONCAT("' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.icon),"") AS icon,
                                                ct.is_free,
                                                ct.is_ios_free,
                                                ct.is_featured,
                                                ct.updated_at
                                            FROM
                                                catalog_master AS ct,
                                                sub_category_catalog AS sct
                                            WHERE
                                                sct.sub_category_id = ? AND
                                                sct.catalog_id = ct.id AND
                                                sct.is_active = 1 AND
                                                ct.is_featured = 1
                                            ORDER BY ct.updated_at DESC', [$this->sub_category_id]);

                foreach ($redis_result as $i => $key) {

                    $featured_cards = DB::select('SELECT
                                                            id AS json_id,
                                                            IF(attribute1 != "",CONCAT("' . Config::get('constant.WEBP_THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",attribute1),"") AS sample_image,
                                                            IF(attribute1 != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",attribute1),"") AS webp_original_img,
                                                            IF(content_type = ' . Config::get('constant.CONTENT_TYPE_FOR_SAMPLE_IMAGE_GIF') . ' AND image != "",CONCAT("' . Config::get('constant.ORIGINAL_VIDEO_DIRECTORY_OF_DIGITAL_OCEAN') . '",SUBSTRING_INDEX(image,".",1),".gif"),"") AS sample_gif,
                                                            IF(content_type = ' . Config::get('constant.CONTENT_TYPE_FOR_BEFORE_AFTER_IMAGE') . ' AND attribute1 != "",CONCAT("' . Config::get('constant.WEBP_THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '","after_image_",attribute1),"") AS after_image,
                                                            IF(content_type = ' . Config::get('constant.CONTENT_TYPE_FOR_BEFORE_AFTER_IMAGE') . ' AND attribute1 != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '","after_image_",attribute1),"") AS webp_original_after_img,
                                                            is_free,
                                                            is_ios_free,
                                                            is_featured,
                                                            is_portrait,
                                                            COALESCE(height,0) AS height,
                                                            COALESCE(width,0) AS width,
                                                            COALESCE(multiple_images,"") AS multiple_images,
                                                            COALESCE(json_pages_sequence,"") AS pages_sequence,
                                                            COALESCE(LENGTH(json_pages_sequence) - LENGTH(REPLACE(json_pages_sequence, ",","")) + 1,1) AS total_pages,
                                                            content_type,
                                                            updated_at
                                                        FROM
                                                            images
                                                        WHERE
                                                            catalog_id = ?
                                                        ORDER BY updated_at DESC LIMIT ?, ?', [$key->catalog_id, $this->offset, $this->item_count]);

                    $key->featured_cards = $featured_cards;
                }
            }

            $response = Response::json(array('code' => 200, 'message' => 'Featured cards fetched successfully.', 'cause' => '', 'data' => ['result' => $redis_result, 'prefix_url' => Config::get('constant.AWS_BUCKET_PATH_PHOTO_EDITOR_LAB') . '/']));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));


        } catch (Exception $e) {
            Log::error("getFeaturedJsonSampleData_webp : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get featured json sample data.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /**
     * @api {post} getFeaturedSampleAndCatalogWithWebp  getFeaturedSampleAndCatalogWithWebp
     * @apiName getFeaturedSampleAndCatalogWithWebp
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
     * "result": {
     *"category_list": [
     *{
     *"catalog_id": 909,
     *"name": "catalog3",
     *"thumbnail_img": "http://192.168.0.116/photo_editor_lab_backend/image_bucket/thumbnail/5fa11c8d37cc2_catalog_img_1604394125.jpg",
     *"compressed_img": "http://192.168.0.116/photo_editor_lab_backend/image_bucket/compressed/5fa11c8d37cc2_catalog_img_1604394125.jpg",
     *"original_img": "http://192.168.0.116/photo_editor_lab_backend/image_bucket/original/5fa11c8d37cc2_catalog_img_1604394125.jpg",
     *"is_free": 1,
     *"is_featured": 1,
     *"updated_at": "2020-11-03 09:02:05"
     *},
     *{
     *"catalog_id": 908,
     *"name": "catalog2",
     *"thumbnail_img": "http://192.168.0.116/photo_editor_lab_backend/image_bucket/thumbnail/5fa11c765dcf0_catalog_img_1604394102.jpg",
     *"compressed_img": "http://192.168.0.116/photo_editor_lab_backend/image_bucket/compressed/5fa11c765dcf0_catalog_img_1604394102.jpg",
     *"original_img": "http://192.168.0.116/photo_editor_lab_backend/image_bucket/original/5fa11c765dcf0_catalog_img_1604394102.jpg",
     *"is_free": 1,
     * "is_featured": 1,
     * "updated_at": "2020-11-03 09:01:43"
     * }
     * ],
     * "sample_cards": [
     *{
     *"json_id": 18152,
     *"sample_image": "http://192.168.0.116/photo_editor_lab_backend/image_bucket/webp_original/5fa14d93ddc74_json_image_1604406675.webp",
     *"is_free": 1,
     *"is_featured": 1,
     *"is_portrait": 1,
     *"height": 400,
     *"width": 325,
     *"original_img_height": 800,
     *"original_img_width": 650,
     *"updated_at": "2020-11-03 12:36:13"
     *},
     *{
     *"json_id": 18153,
     *"sample_image": "http://192.168.0.116/photo_editor_lab_backend/image_bucket/webp_original/5fa14da652632_json_image_1604406694.webp",
     *"is_free": 1,
     *"is_featured": 1,
     *"is_portrait": 1,
     *"height": 400,
     *"width": 325,
     *"original_img_height": 800,
     *"original_img_width": 650,
     *"updated_at": "2020-11-03 12:31:34"
     *}
     * ]
     * }
     * }
     * }
     */
    public function getFeaturedSampleAndCatalogWithWebp(Request $request_body)
    {

        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('sub_category_id'), $request)) != '')
                return $response;

            $this->sub_category_id = $request->sub_category_id;
            $this->item_count = Config::get('constant.ITEM_COUNT_OF_FEATURED_CATALOGS_AND_TEMPLATES');
            $this->offset = 0;
            $is_cache_enable = isset($request->is_cache_enable) ? $request->is_cache_enable : 1;


            //Log::info('request_data', ['request_data' => $request]);

            if ($is_cache_enable) {
                $redis_result = Cache::remember("getFeaturedSampleAndCatalogWithWebp:$this->item_count:$this->sub_category_id", Config::get('constant.CACHE_TIME_6_HOUR'), function () {

                    $category_list = DB::select('SELECT
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
                                                order by ct.updated_at DESC LIMIT ?, ?', [$this->sub_category_id,$this->offset, $this->item_count]);

                    $sample_cards = DB::select('SELECT
                                              id AS json_id,
                                              IF(attribute1 != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",attribute1),"") as sample_image,
                                              is_free,
                                              is_featured,
                                              is_portrait,
                                              search_category,
                                              template_name,
                                              coalesce(height,0) AS height,
                                              coalesce(width,0) AS width,
                                              coalesce(original_img_height,0) AS original_img_height,
                                              coalesce(original_img_width,0) AS original_img_width,
                                              COALESCE(multiple_images,"") AS multiple_images,
                                              COALESCE(json_pages_sequence,"") AS pages_sequence,
                                              COALESCE(LENGTH(json_pages_sequence) - LENGTH(REPLACE(json_pages_sequence, ",","")) + 1,1) AS total_pages,                                              
                                              updated_at
                                            FROM
                                              images
                                            WHERE
                                              catalog_id IN(select catalog_id FROM sub_category_catalog WHERE sub_category_id = ? AND is_active = 1) AND
                                              is_active = 1 AND
                                              is_featured = 1
                                            ORDER BY updated_at DESC LIMIT ?, ?', [$this->sub_category_id,$this->offset, $this->item_count]);

                    return array("category_list" => $category_list ,'sample_cards' => $sample_cards);
                });

            } else {

                $category_list = DB::select('SELECT
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
                                                  ct.is_featured = 1
                                                order by ct.updated_at DESC LIMIT ?, ?', [$this->sub_category_id,$this->offset, $this->item_count]);

                $sample_cards = DB::select('SELECT
                                              id AS json_id,
                                              IF(attribute1 != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",attribute1),"") as sample_image,
                                              is_free,
                                              is_featured,
                                              is_portrait,
                                              search_category,
                                              template_name,
                                              coalesce(height,0) AS height,
                                              coalesce(width,0) AS width,
                                              coalesce(original_img_height,0) AS original_img_height,
                                              coalesce(original_img_width,0) AS original_img_width,
                                              COALESCE(multiple_images,"") AS multiple_images,
                                              COALESCE(json_pages_sequence,"") AS pages_sequence,
                                              COALESCE(LENGTH(json_pages_sequence) - LENGTH(REPLACE(json_pages_sequence, ",","")) + 1,1) AS total_pages,                                              
                                              updated_at
                                            FROM
                                              images
                                            WHERE
                                              catalog_id IN(select catalog_id FROM sub_category_catalog WHERE sub_category_id = ?) AND
                                              is_featured = 1
                                            ORDER BY updated_at DESC LIMIT ?, ?', [$this->sub_category_id,$this->offset, $this->item_count]);

                $redis_result = array("category_list" => $category_list ,'sample_cards' => $sample_cards);

            }

            $response = Response::json(array('code' => 200, 'message' => 'Featured cards fetched successfully.', 'cause' => '', 'data' => $redis_result));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            Log::error("getFeaturedSampleAndCatalogWithWebp : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get featured json sample data.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }


    //search catalog by catalog name

    /**
     * @api {post} searchCatalogByUser searchCatalogByUser
     * @apiName searchCatalogByUser
     * @apiGroup User
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {//all parameters are compulsory
     * "sub_category_id":66,
     * "search_category":"grandient logo",
     * "page":1,
     * "item_count":2
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200, //return 427 when server not find any result related to your search_category
     * "message": "Templates fetched successfully.",
     * "cause": "",
     * "data": {
     * "total_record": 2,
     * "is_next_page": false,
     * "result": [
     * {
     * "code": 200,
     * "message": "Catalogs fetched successfully.",
     * "cause": "",
     * "data": {
     * "total_record": 21,
     * "is_next_page": true,
     * "result": [
     * {
     * "catalog_id": 498,
     * "name": "Party",
     * "webp_thumbnail_img": "http://192.168.0.113/photo_editor_lab_backend/image_bucket/webp_thumbnail/5c7914a7c12f5_catalog_img_1551439015.webp",
     * "webp_original_img": "http://192.168.0.113/photo_editor_lab_backend/image_bucket/webp_original/5c7914a7c12f5_catalog_img_1551439015.webp",
     * "is_featured": 1,
     * "is_free": 1,
     * "updated_at": "2019-03-01 11:16:56"
     * }
     * ]
     * }
     * }
     */
    public function searchCatalogByUser(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('sub_category_id', 'search_category', 'page', 'item_count'), $request)) != '')
                return $response;

            $this->sub_category_id = $request->sub_category_id;
            $this->search_category = strtolower(trim($request->search_category));
            $this->page = $request->page;
            $this->item_count = $request->item_count;
            $this->offset = ($this->page - 1) * $this->item_count;
            $this->is_free = isset($request->is_free) ? ' AND ct.is_free = ' . $request->is_free : '';
            $this->is_featured = isset($request->is_featured) ? ' AND ct.is_featured = ' . $request->is_featured : '';
            $is_cache_enable = isset($request->is_cache_enable) ? $request->is_cache_enable : 1;


            //validate search text
            $this->is_verified = (new VerificationController())->verifySearchText($this->search_category);

            if ($is_cache_enable) {
                $redis_result = Cache::remember("searchCatalogByUser:$this->sub_category_id:$this->search_category:$this->offset:$this->item_count:$this->is_free:$this->is_featured", Config::get('constant.CACHE_TIME_6_HOUR'), function () {

                    $search_category = $this->search_category;
                    $code = 200;
                    $message = "Bundles fetched successfully.";
                    if ($this->is_verified == 1) {

                        $total_row_result = DB::select('SELECT
                                                      count(ct.id) as total
                                                    FROM
                                                      catalog_master as ct,
                                                      sub_category_catalog as sct
                                                    WHERE
                                                      sct.sub_category_id = ? AND
                                                      (MATCH(ct.name) AGAINST("' . $search_category . '") OR 
                                                       MATCH(ct.name) AGAINST(REPLACE(concat("' . $search_category . '"," ")," ","* ") IN BOOLEAN MODE)) AND
                                                      sct.catalog_id = ct.id AND
                                                      sct.is_active = 1 AND 
                                                      ct.is_featured = 1 ' . $this->is_free . '
                                                    ORDER BY ct.updated_at DESC', [$this->sub_category_id]);

                        $total_row = $total_row_result[0]->total;

                        $catalog_list = DB::select('SELECT
                                          ct.id as catalog_id,
                                          ct.name,
                                          MATCH(ct.name) AGAINST("' . $search_category . '") +
                                          MATCH(ct.name) AGAINST(REPLACE(concat("' . $search_category . '"," ")," ","* ") IN BOOLEAN MODE) AS search_text,
                                          IF(ct.attribute1 != "",CONCAT("' . Config::get('constant.WEBP_THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.attribute1),"") as webp_thumbnail_img,
                                          IF(ct.attribute1 != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.attribute1),"") as webp_original_img,
                                          ct.is_featured,
                                          ct.is_free,
                                          ct.updated_at
                                        FROM
                                          catalog_master as ct,
                                          sub_category_catalog as sct
                                        WHERE
                                          sct.sub_category_id = ? AND
                                          (MATCH(ct.name) AGAINST("' . $search_category . '") OR 
                                          MATCH(ct.name) AGAINST(REPLACE(concat("' . $search_category . '"," ")," ","* ") IN BOOLEAN MODE)) AND
                                          sct.catalog_id = ct.id AND
                                          sct.is_active = 1 AND 
                                          ct.is_featured = 1 ' . $this->is_free . '
                                        ORDER BY ct.updated_at DESC LIMIT ?, ?', [$this->sub_category_id, $this->offset, $this->item_count]);

                    } else {
                        $catalog_list = [];
                    }

                    if (count($catalog_list) <= 0) {

                        $total_row_result = DB::select('SELECT
                                                      count(ct.id) as total
                                                    FROM
                                                      catalog_master as ct,
                                                      sub_category_catalog as sct
                                                    WHERE
                                                      sct.sub_category_id = ? AND
                                                      sct.catalog_id = ct.id AND
                                                      sct.is_active = 1 AND 
                                                      ct.is_featured = 1 ' . $this->is_free . '
                                                    ORDER BY ct.updated_at DESC', [$this->sub_category_id]);

                        $total_row = $total_row_result[0]->total;

                        $catalog_list = DB::select('SELECT
                                          ct.id as catalog_id,
                                          ct.name,
                                          IF(ct.attribute1 != "",CONCAT("' . Config::get('constant.WEBP_THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.attribute1),"") as webp_thumbnail_img,
                                          IF(ct.attribute1 != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.attribute1),"") as webp_original_img,
                                          ct.is_featured,
                                          ct.is_free,
                                          ct.updated_at
                                        FROM
                                          catalog_master as ct,
                                          sub_category_catalog as sct
                                        WHERE
                                          sct.sub_category_id = ? AND
                                          sct.catalog_id = ct.id AND
                                          sct.is_active = 1 AND 
                                          ct.is_featured = 1 ' . $this->is_free . '
                                        ORDER BY ct.updated_at DESC LIMIT ?, ?', [$this->sub_category_id, $this->offset, $this->item_count]);

                        $code = 427;
                        $message = "Sorry, we couldn't find any bundles for '$search_category', but we found some other bundles you might like:";
                    }

                    $is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;
                    $search_result = array('total_record' => $total_row, 'is_next_page' => $is_next_page, 'result' => $catalog_list);

                    $result = array('result' => $search_result, 'code' => $code, 'message' => $message);
                    return $result;
                });

            } else {

                $search_category = $this->search_category;
                $code = 200;
                $message = "Bundles fetched successfully.";
                if ($this->is_verified == 1) {

                    $total_row_result = DB::select('SELECT
                                                      count(ct.id) as total
                                                    FROM
                                                      catalog_master as ct,
                                                      sub_category_catalog as sct
                                                    WHERE
                                                      sct.sub_category_id = ? AND
                                                      (MATCH(ct.name) AGAINST("' . $search_category . '") OR 
                                                       MATCH(ct.name) AGAINST(REPLACE(concat("' . $search_category . '"," ")," ","* ") IN BOOLEAN MODE)) AND
                                                      sct.catalog_id = ct.id AND
                                                      sct.is_active = 1 AND 
                                                      ct.is_featured = 1 ' . $this->is_free . '
                                                    ORDER BY ct.updated_at DESC', [$this->sub_category_id]);

                    $total_row = $total_row_result[0]->total;

                    $catalog_list = DB::select('SELECT
                                          ct.id as catalog_id,
                                          ct.name,
                                          MATCH(ct.name) AGAINST("' . $search_category . '") +
                                          MATCH(ct.name) AGAINST(REPLACE(concat("' . $search_category . '"," ")," ","* ") IN BOOLEAN MODE) AS search_text,
                                          IF(ct.attribute1 != "",CONCAT("' . Config::get('constant.WEBP_THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.attribute1),"") as webp_thumbnail_img,
                                          IF(ct.attribute1 != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.attribute1),"") as webp_original_img,
                                          ct.is_featured,
                                          ct.is_free,
                                          ct.updated_at
                                        FROM
                                          catalog_master as ct,
                                          sub_category_catalog as sct
                                        WHERE
                                          sct.sub_category_id = ? AND
                                          (MATCH(ct.name) AGAINST("' . $search_category . '") OR 
                                          MATCH(ct.name) AGAINST(REPLACE(concat("' . $search_category . '"," ")," ","* ") IN BOOLEAN MODE)) AND
                                          sct.catalog_id = ct.id AND
                                          sct.is_active = 1 AND 
                                          ct.is_featured = 1 ' . $this->is_free . '
                                        ORDER BY ct.updated_at DESC LIMIT ?, ?', [$this->sub_category_id, $this->offset, $this->item_count]);

                } else {
                    $catalog_list = [];
                }

                if (count($catalog_list) <= 0) {

                    $total_row_result = DB::select('SELECT
                                                      count(ct.id) as total
                                                    FROM
                                                      catalog_master as ct,
                                                      sub_category_catalog as sct
                                                    WHERE
                                                      sct.sub_category_id = ? AND
                                                      sct.catalog_id = ct.id AND
                                                      sct.is_active = 1 AND 
                                                      ct.is_featured = 1 ' . $this->is_free . '
                                                    ORDER BY ct.updated_at DESC', [$this->sub_category_id]);

                    $total_row = $total_row_result[0]->total;

                    $catalog_list = DB::select('SELECT
                                          ct.id as catalog_id,
                                          ct.name,
                                          IF(ct.attribute1 != "",CONCAT("' . Config::get('constant.WEBP_THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.attribute1),"") as webp_thumbnail_img,
                                          IF(ct.attribute1 != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.attribute1),"") as webp_original_img,
                                          ct.is_featured,
                                          ct.is_free,
                                          ct.updated_at
                                        FROM
                                          catalog_master as ct,
                                          sub_category_catalog as sct
                                        WHERE
                                          sct.sub_category_id = ? AND
                                          sct.catalog_id = ct.id AND
                                          sct.is_active = 1 AND 
                                          ct.is_featured = 1 ' . $this->is_free . '
                                        ORDER BY ct.updated_at DESC LIMIT ?, ?', [$this->sub_category_id, $this->offset, $this->item_count]);

                    $code = 427;
                    $message = "Sorry, we couldn't find any bundles for '$search_category', but we found some other bundles you might like:";
                }

                $is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;
                $search_result = array('total_record' => $total_row, 'is_next_page' => $is_next_page, 'result' => $catalog_list);

                $redis_result = array('result' => $search_result, 'code' => $code, 'message' => $message);

            }

            $response = Response::json(array('code' => $redis_result['code'], 'message' => $redis_result['message'], 'cause' => '', 'data' => $redis_result['result']));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            Log::error("searchCatalogByUser : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'search templates.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    /**
     * @api {post} searchCardsBySubCategoryId searchCardsBySubCategoryId
     * @apiName searchCardsBySubCategoryId
     * @apiGroup User
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {//all parameters are compulsory
     * "sub_category_id":66,
     * "search_category":"Flyers",
     * "page":1,
     * "item_count":2
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200, //return 427 when server not find any result related to your search_category
     * "message": "Templates fetched successfully.",
     * "cause": "",
     * "data": {
     * "total_record": 2,
     * "is_next_page": false,
     * "result": [
     * {
     * "json_id": 7355,
     * "sample_image": "http://192.168.0.115/photo_editor_lab_backend/image_bucket/compressed/5ccad5b804d96_normal_image_1556796856.png",
     * "is_free": 0,
     * "is_featured": null,
     * "is_portrait": null,
     * "height": 0,
     * "width": 0,
     * "updated_at": "2019-08-29 11:06:37",
     * "search_text": 1.91667640209198
     * },
     * {
     * "json_id": 7338,
     * "sample_image": "http://192.168.0.115/photo_editor_lab_backend/image_bucket/compressed/5ccad5800ba64_normal_image_1556796800.png",
     * "is_free": 0,
     * "is_featured": null,
     * "is_portrait": null,
     * "height": 0,
     * "width": 0,
     * "updated_at": "2019-08-29 11:06:19",
     * "search_text": 1.91667640209198
     * }
     * ]
     * }
     * }
     */
    public function searchCardsBySubCategoryIdOldVersion(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('sub_category_id', 'search_category', 'page', 'item_count'), $request)) != '')
                return $response;

            $this->sub_category_id = $request->sub_category_id;
            $this->search_category = mb_strtolower(trim($request->search_category));
            $this->page = $request->page;
            $this->item_count = $request->item_count;
            $this->offset = ($this->page - 1) * $this->item_count;

            //validate search text
            $this->is_verified = (new VerificationController())->verifySearchText($this->search_category);

            if (!Cache::has("pel:searchCardsBySubCategoryId$this->sub_category_id:$this->search_category:$this->offset:$this->item_count")) {
                $result = Cache::rememberforever("searchCardsBySubCategoryId$this->sub_category_id:$this->search_category:$this->offset:$this->item_count", function () {

                    $search_category = $this->search_category;
                    $code = 200;
                    $message = "Templates fetched successfully.";

                    if ($this->is_verified == 1) {

                        $total_row_result = DB::select('SELECT count(*) as total
                                                                FROM
                                                                images as im,
                                                                catalog_master AS cm,
                                                                sub_category_catalog AS scc
                                                                WHERE
                                                                im.is_active = 1 AND
                                                                im.catalog_id = scc.catalog_id AND
                                                                cm.is_featured = 1 AND
                                                                cm.id = scc.catalog_id AND
                                                                scc.sub_category_id = ? AND
                                                                isnull(im.original_img) AND
                                                                isnull(im.display_img) AND
                                                                (MATCH(im.search_category) AGAINST("' . $search_category . '") OR 
                                                                MATCH(im.search_category) AGAINST(REPLACE(concat("' . $search_category . '"," ")," ","* ") IN BOOLEAN MODE))
                                                                ', [$this->sub_category_id]);

                        $total_row = $total_row_result[0]->total;

                        $search_result = DB::select('SELECT
                                                    DISTINCT im.id as json_id,
                                                    IF(im.attribute1 != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",im.attribute1),"") as sample_image,
                                                    im.is_free,
                                                    im.is_ios_free,
                                                    im.is_featured,
                                                    im.is_portrait,
                                                    coalesce(im.height,0) AS height,
                                                    coalesce(im.width,0) AS width,
                                                    COALESCE(im.multiple_images,"") AS multiple_images,
                                                    COALESCE(im.json_pages_sequence,"") AS pages_sequence,
                                                    COALESCE(LENGTH(im.json_pages_sequence) - LENGTH(REPLACE(im.json_pages_sequence, ",","")) + 1,1) AS total_pages,
                                                    im.updated_at,
                                                    MATCH(im.search_category) AGAINST("' . $search_category . '") +
                                                    MATCH(im.search_category) AGAINST(REPLACE(concat("' . $search_category . '"," ")," ","* ") IN BOOLEAN MODE) AS search_text 
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
                                                    (MATCH(im.search_category) AGAINST("' . $search_category . '") OR 
                                                    MATCH(im.search_category) AGAINST(REPLACE(concat("' . $search_category . '"," ")," ","* ") IN BOOLEAN MODE))
                                                    ORDER BY search_text DESC,im.updated_at DESC LIMIT ?, ?', [$this->sub_category_id, $this->offset, $this->item_count]);
                    } else {
                        $search_result = [];
                    }

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
                                                            scc.sub_category_id = ? AND
                                                            cm.is_featured = 1 AND
                                                            isnull(im.original_img) AND
                                                            isnull(im.display_img)
                                                            ', [$this->sub_category_id]);

                        $total_row = $total_row_result[0]->total;

                        $search_result = DB::select('SELECT
                                                    DISTINCT im.id as json_id,
                                                    IF(im.attribute1 != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",im.attribute1),"") as sample_image,
                                                    im.is_free,
                                                    im.is_ios_free,
                                                    im.is_featured,
                                                    im.is_portrait,
                                                    coalesce(im.height,0) AS height,
                                                    coalesce(im.width,0) AS width,
                                                    COALESCE(im.multiple_images,"") AS multiple_images,
                                                    COALESCE(im.json_pages_sequence,"") AS pages_sequence,
                                                    COALESCE(LENGTH(im.json_pages_sequence) - LENGTH(REPLACE(im.json_pages_sequence, ",","")) + 1,1) AS total_pages,
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
                        $message = "Sorry, we couldn't find any templates for '$search_category', but we found some other templates you might like:";
                    }

                    $is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;
                    $search_result = array('total_record' => $total_row, 'is_next_page' => $is_next_page, 'result' => $search_result, 'prefix_url' => Config::get('constant.AWS_BUCKET_PATH_PHOTO_EDITOR_LAB').'/');

                    $result = array('result' => $search_result, 'code' => $code, 'message' => $message);
                    return $result;
                });

            }

            $redis_result = Cache::get("searchCardsBySubCategoryId$this->sub_category_id:$this->search_category:$this->offset:$this->item_count");

            if (!$redis_result) {
                $redis_result = [];
            }

            if($this->page == 1) {
                if($redis_result['code'] != 200){
                    SaveSearchTagJob::dispatch(0, $this->search_category, $this->sub_category_id, 0);
                }else{
                    SaveSearchTagJob::dispatch($redis_result['result']['total_record'], $this->search_category, $this->sub_category_id, 1);
                }
            }

            $response = Response::json(array('code' => $redis_result['code'], 'message' => $redis_result['message'], 'cause' => '', 'data' => $redis_result['result']));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            Log::error("searchCardsBySubCategoryId : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'search templates.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    /*
    Purpose : for search card with single sub_category_id. If result not found then translate & correction of the text
    Description : This method compulsory take 4 argument as parameter.(if any argument is optional then define it here)
    Return : return cards detail if success otherwise error with specific status code
    */
    public function searchCardsBySubCategoryId(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('sub_category_id', 'search_category', 'page', 'item_count'), $request)) != '')
                return $response;

            $sub_category_id = preg_replace('/[ A-Za-z]/', '', $request->sub_category_id);      //Remove space & alpha character from searching because if we add this character then issue occur in admin panel.
            $search_category = trim(mb_substr(preg_replace('/[\\\@()<>+*%"]/', '', mb_strtolower($request->search_category)), 0, 100));      //Remove '[\\\@()<>+*%"]' character from searching because if we add this character then mysql gives syntax error.
            $page = $request->page;
            $item_count = $request->item_count;
            $offset = ($page - 1) * $item_count;
            $is_featured = isset($request->is_featured) ? $request->is_featured : 1;   //is_featured is use for finding a proper data from DB.
            $category_id = isset($request->category_id) ? $request->category_id : Config::get('constant.CATEGORY_ID_OF_STICKER');    //Category id to find, Which category is use.
            $is_cache_enable = isset($request->is_cache_enable) ? $request->is_cache_enable : 1;
            $catalog_id = isset($request->catalog_id) ? $request->catalog_id : '';    //Catalog id to give a priority template.

            if ($is_cache_enable == 1) {
                $redis_result = $this->searchTemplatesBySearchCategory($search_category, $sub_category_id, $offset, $item_count, $is_featured, $catalog_id);
            } else {
                $redis_result = $this->searchTemplatesByDisableCache($search_category, $sub_category_id, $offset, $item_count, $is_featured, $catalog_id);
            }

            if($page == 1) {
                if($redis_result['code'] != 200){
                    SaveSearchTagJob::dispatch(0, $search_category, $sub_category_id, 0, $is_featured, $category_id);
                }else{
                    SaveSearchTagJob::dispatch($redis_result['data']['total_record'], $search_category, $sub_category_id, 1, $is_featured, $category_id);
                }
            }

            $response = Response::json(array('code' => $redis_result['code'], 'message' => $redis_result['message'], 'cause' => $redis_result['cause'], 'data' => $redis_result['data']));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            Log::error("searchCardsBySubCategoryId : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'search templates.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    public function searchCardsForIndustryPreference(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('sub_category_id', 'page', 'item_count', 'search_category'), $request)) != '')
                return $response;

            $this->sub_category_id = preg_replace('/[ A-Za-z]/', '', $request->sub_category_id);      //Remove space & alpha character from searching because if we add this character then issue occur in admin panel.
            $this->search_category = trim(mb_substr(preg_replace('/[\\\@()<>+*%"]/', '', mb_strtolower($request->search_category)), 0, 100));      //Remove '[\\\@()<>+*%"]' character from searching because if we add this character then mysql gives syntax error.
            $page = $request->page;
            $this->item_count = $request->item_count;
            $this->offset = ($page - 1) * $this->item_count;
            $is_cache_enable = isset($request->is_cache_enable) ? $request->is_cache_enable : 1;
            $search_keyword = mb_substr($this->search_category, 0, 1);
            $this->search_keyword = $search_keyword . $search_keyword . $search_keyword . $search_keyword;
            $this->industry = isset($request->industry) ? $request->industry : NULL;

            if ($is_cache_enable) {

                $redis_result = Cache::remember("searchCardsForIndustryPreference:$this->sub_category_id:$this->offset:$this->item_count:$this->industry:$this->search_category", Config::get('constant.CACHE_TIME_6_HOUR'), function () {
                    return $this->searchCardsBySearchCategory($this->sub_category_id, $this->offset, $this->item_count, $this->search_keyword, $this->industry, $this->search_category);
                });

            } else {
                $redis_result = $this->searchCardsBySearchCategory($this->sub_category_id, $this->offset, $this->item_count, $this->search_keyword, $this->industry, $this->search_category);
            }

            if ($page == 1) {
                $tag = ($this->industry) ? $this->search_keyword . ", " . $this->search_category . ", " . $this->industry : $this->search_keyword . ", " . $this->search_category;
                if ($redis_result['code'] != 200) {
                    SaveSearchTagJob::dispatch(0, $tag, $this->sub_category_id, 0, 1, Config::get('constant.CATEGORY_ID_OF_STICKER'));
                } else {
                    SaveSearchTagJob::dispatch($redis_result['data']['total_record'], $tag, $this->sub_category_id, 1, 1, Config::get('constant.CATEGORY_ID_OF_STICKER'));
                }
            }

            $response = Response::json(array('code' => $redis_result['code'], 'message' => $redis_result['message'], 'cause' => $redis_result['cause'], 'data' => $redis_result['data']));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            Log::error("searchCardsForIndustryPreference : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'search templates.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    public function searchCardsBySearchCategory($sub_category_id, $offset, $item_count, $search_keyword, $industry, $search_category)
    {
        try {
            $logo_maker_ai_catalog_id = config('constant.LOGO_MAKER_AI_CATALOG_ID');
            //dd($search_keyword, $industry, $search_category);

            $total_row_result = DB::select('SELECT 
                                                COUNT(DISTINCT(im.id)) AS total
                                            FROM
                                                images AS im,
                                                catalog_master AS cm,
                                                sub_category_catalog AS scc
                                            WHERE
                                                im.is_active = 1 AND
                                                im.catalog_id = scc.catalog_id AND
                                                cm.id = scc.catalog_id AND
                                                cm.is_featured = 1 AND
                                                scc.sub_category_id = ? AND
                                                ISNULL(im.original_img) AND
                                                ISNULL(im.display_img) AND
                                                (MATCH(im.search_category) AGAINST ("' . $search_keyword . ', ' . $industry . ', ' . $search_category . '") OR 
                                                MATCH(im.search_category) AGAINST ("' . $search_keyword . ', ' . $industry . ', ' . $search_category . '* " IN BOOLEAN MODE)) ', [$sub_category_id]);
            $total_row = $total_row_result[0]->total;

            if ($total_row) {

                $code = 200;
                $message = "Templates fetched successfully.";

                $search_result = DB::select('SELECT
                                                DISTINCT im.id AS json_id,
                                                im.catalog_id,
                                                IF(im.image != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",im.image),"") AS compressed_img,
                                                IF(im.attribute1 != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",im.attribute1),"") AS sample_image,
                                                IF(im.attribute1 != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",im.attribute1),"") AS webp_original_img,
                                                IF(im.content_type = ' . Config::get('constant.CONTENT_TYPE_FOR_BEFORE_AFTER_IMAGE') . ' AND im.attribute1 != "",CONCAT("' . Config::get('constant.WEBP_THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '","after_image_",im.attribute1),"") AS after_image,
                                                IF(im.content_type = ' . Config::get('constant.CONTENT_TYPE_FOR_BEFORE_AFTER_IMAGE') . ' AND im.attribute1 != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '","after_image_",im.attribute1),"") AS webp_original_after_img,
                                                IF(im.content_type = ' . Config::get('constant.CONTENT_TYPE_FOR_SAMPLE_IMAGE_GIF') . ' AND im.image != "",CONCAT("' . Config::get('constant.ORIGINAL_VIDEO_DIRECTORY_OF_DIGITAL_OCEAN') . '",SUBSTRING_INDEX(im.image,".",1),".gif"),"") AS sample_gif,
                                                IF(im.cover_webp_img != "", CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '", im.cover_webp_img), "") AS cover_webp_img,
                                                COALESCE(im.cover_img_height,0) AS cover_img_height,
                                                COALESCE(im.cover_img_width,0) AS cover_img_width,
                                                im.is_free,
                                                im.is_ios_free,
                                                im.is_featured,
                                                im.is_portrait,
                                                im.search_category,
                                                im.template_name,
                                                COALESCE(im.height,0) AS height,
                                                COALESCE(im.width,0) AS width,
                                                COALESCE(im.multiple_images,"") AS multiple_images,
                                                COALESCE(im.json_pages_sequence,"") AS pages_sequence,
                                                COALESCE(LENGTH(im.json_pages_sequence) - LENGTH(REPLACE(im.json_pages_sequence, ",","")) + 1,1) AS total_pages,
                                                im.content_type,
                                                im.updated_at,
                                                IF(LOCATE("' . $search_keyword . '", im.search_category) && LOCATE("' . $industry . '", im.search_category), 4, 0) AS p1,
                                                IF(LOCATE("' . $search_keyword . '", im.search_category), 3, 0) AS p2,
                                                IF(LOCATE("' . $industry . '", im.search_category), 2, 0) AS p3,
                                                IF(LOCATE("' . $search_category . '", im.search_category), 1, 0) AS p4
                                            FROM
                                                images AS im,
                                                catalog_master AS cm,
                                                sub_category_catalog AS scc
                                            WHERE
                                                im.is_active = 1 AND
                                                im.catalog_id = scc.catalog_id AND
                                                cm.id = scc.catalog_id AND
                                                cm.is_featured = 1 AND
                                                scc.sub_category_id = ? AND
                                                ISNULL(im.original_img) AND
                                                ISNULL(im.display_img) AND
                                                (MATCH(im.search_category) AGAINST ("' . $search_keyword . ', ' . $industry . ', ' . $search_category . '") OR 
                                                MATCH(im.search_category) AGAINST ("' . $search_keyword . ', ' . $industry . ', ' . $search_category . '* " IN BOOLEAN MODE)) 
                                            ORDER BY 
                                                p1 DESC, FIELD(im.catalog_id, '. $logo_maker_ai_catalog_id .') DESC, 
                                                p2 DESC, FIELD(im.catalog_id, '. $logo_maker_ai_catalog_id .') DESC,
                                                p3 DESC, FIELD(im.catalog_id, '. $logo_maker_ai_catalog_id .') DESC,
                                                p4 DESC, FIELD(im.catalog_id, '. $logo_maker_ai_catalog_id .') DESC,
                                                im.updated_at DESC
                                            LIMIT ?, ?', [$sub_category_id, $offset, $item_count]);

                $is_next_page = ($total_row > ($offset + $item_count)) ? true : false;
                $search_result = array('total_record' => $total_row, 'is_next_page' => $is_next_page, 'result' => $search_result);
                return array('code' => $code, 'message' => $message, 'cause' => '', 'data' => $search_result);

            } else {

                $code = 427;
                $message = "Sorry, we couldn't find any templates for '$search_category', but we found some other templates you might like:";
                $search_result = [];

                $total_row_result = DB::select('SELECT 
                                                    COUNT(DISTINCT(im.id)) AS total
                                                FROM
                                                    images AS im,
                                                    catalog_master AS cm,
                                                    sub_category_catalog AS scc
                                                WHERE
                                                    im.is_active = 1 AND
                                                    im.catalog_id = scc.catalog_id AND
                                                    cm.id = scc.catalog_id AND
                                                    scc.sub_category_id = ? AND
                                                    cm.is_featured = 1 AND
                                                    ISNULL(im.original_img) AND
                                                    ISNULL(im.display_img) ', [$sub_category_id]);
                $total_row = $total_row_result[0]->total;

                if ($total_row) {
                    $search_result = DB::select('SELECT
                                                    DISTINCT im.id AS json_id,
                                                    im.catalog_id,
                                                    IF(im.image != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",im.image),"") AS compressed_img,
                                                    IF(im.attribute1 != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",im.attribute1),"") AS sample_image,
                                                    IF(im.attribute1 != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",im.attribute1),"") AS webp_original_img,
                                                    IF(im.content_type = ' . Config::get('constant.CONTENT_TYPE_FOR_BEFORE_AFTER_IMAGE') . ' AND im.attribute1 != "",CONCAT("' . Config::get('constant.WEBP_THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '","after_image_",im.attribute1),"") AS after_image,
                                                    IF(im.content_type = ' . Config::get('constant.CONTENT_TYPE_FOR_BEFORE_AFTER_IMAGE') . ' AND im.attribute1 != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '","after_image_",im.attribute1),"") AS webp_original_after_img,
                                                    IF(im.content_type = ' . Config::get('constant.CONTENT_TYPE_FOR_SAMPLE_IMAGE_GIF') . ' AND im.image != "",CONCAT("' . Config::get('constant.ORIGINAL_VIDEO_DIRECTORY_OF_DIGITAL_OCEAN') . '",SUBSTRING_INDEX(im.image,".",1),".gif"),"") AS sample_gif,
                                                    IF(im.cover_webp_img != "", CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '", im.cover_webp_img), "") AS cover_webp_img,
                                                    COALESCE(im.cover_img_height,0) AS cover_img_height,
                                                    COALESCE(im.cover_img_width,0) AS cover_img_width,
                                                    im.is_free,
                                                    im.is_ios_free,
                                                    im.is_featured,
                                                    im.is_portrait,
                                                    im.search_category,
                                                    im.template_name,
                                                    COALESCE(im.height,0) AS height,
                                                    COALESCE(im.width,0) AS width,
                                                    COALESCE(im.multiple_images,"") AS multiple_images,
                                                    COALESCE(im.json_pages_sequence,"") AS pages_sequence,
                                                    COALESCE(LENGTH(im.json_pages_sequence) - LENGTH(REPLACE(im.json_pages_sequence, ",","")) + 1,1) AS total_pages,
                                                    im.content_type,
                                                    im.updated_at
                                                FROM
                                                    images AS im,
                                                    catalog_master AS cm,
                                                    sub_category_catalog AS scc
                                                WHERE
                                                    im.is_active = 1 AND
                                                    im.catalog_id = scc.catalog_id AND
                                                    cm.id = scc.catalog_id AND
                                                    cm.is_featured = 1 AND
                                                    scc.sub_category_id = ? AND
                                                    ISNULL(im.original_img) AND
                                                    ISNULL(im.display_img)
                                                ORDER BY FIELD(im.catalog_id, "'. $logo_maker_ai_catalog_id .'") DESC, im.updated_at DESC LIMIT ?, ?', [$sub_category_id, $offset, $item_count]);

                } else {
                    Log::error('searchTemplatesBySearchCategory : Default template not found', ['search_category' => $search_category, 'industry' => $industry, 'sub_category_id' => $sub_category_id, 'offset' => $offset, 'item_count' => $item_count]);
                }

                $is_next_page = ($total_row > ($offset + $item_count)) ? true : false;
                $search_result = array('total_record' => $total_row, 'is_next_page' => $is_next_page, 'result' => $search_result);

                return array('code' => $code, 'message' => $message, 'cause' => '', 'data' => $search_result);
            }

        } catch (Exception $e) {
            Log::error("searchCardsBySearchCategory : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            return array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get template.', 'cause' => $e->getMessage(), 'data' => json_decode("{}"));
        }
    }

    /*
    Purpose : for search card with single sub_category_id with translate. If result not found then translate & correction of the text
    Description : This method compulsory take 4 argument as parameter.(if any argument is optional then define it here)
    Return : return cards detail if success otherwise error with specific status code
    */
    public function searchCardsBySubCategoryIdWithTranslate(Request $request_body)
    {
        //$time_start = microtime(true);
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('sub_category_id', 'search_category', 'page', 'item_count'), $request)) != '')
                return $response;

            $sub_category_id = preg_replace('/[ A-Za-z]/', '', $request->sub_category_id);      //Remove space & alpha character from searching because if we add this character then issue occur in admin panel.
            $search_category = mb_substr(preg_replace('/[@()<>+*%"]/', '', mb_strtolower(trim($request->search_category))), 0, 100);      //Remove '[@()<>+*%"]' character from searching because if we add this character then mysql gives syntax error.
            $bcp_language_code = isset($request->bcp_language_code) ? $request->bcp_language_code : '';     //if user text language is in english that in "en" that time we don't need to call translate API.
            $page = $request->page;
            $item_count = $request->item_count;
            $offset = ($page - 1) * $item_count;
            $is_user_search_tag = isset($request->is_user_search_tag) ? $request->is_user_search_tag : 1;       //In some applications we have put search tags instead of catalog lists, So if user clicks that search tag that time we don't need to insert this tag in DB.
            $is_featured = isset($request->is_featured) ? $request->is_featured : 1;   //is_featured is use for finding a proper data from DB.
            $category_id = isset($request->category_id) ? $request->category_id : Config::get('constant.CATEGORY_ID_OF_STICKER');    //Category id to find, Which category is use.
            $is_cache_enable = isset($request->is_cache_enable) ? $request->is_cache_enable : 1;
            $fail_over_sub_category_id = isset($request->fail_over_sub_category_id) ? $request->fail_over_sub_category_id : '';
            //$this->is_template = isset($request->is_template) ? $request->is_template : 1;      //1=for template, 2=for sticker,shape,background.

            $redis_result = $this->searchTemplatesBySearchCategory_V2($search_category, $sub_category_id, $offset, $page, $item_count, $is_featured, $fail_over_sub_category_id, $bcp_language_code, $is_cache_enable);

            if ($page == 1 && $is_user_search_tag == 1) {
                if ($redis_result['code'] != 200) {
                    SaveSearchTagJob::dispatch(0, $search_category, $sub_category_id, 0, $is_featured, $category_id);
                } else {
                    SaveSearchTagJob::dispatch($redis_result['data']['total_record'], $search_category, $sub_category_id, 1, $is_featured, $category_id);
                }
            }

            $response = Response::json(array('code' => $redis_result['code'], 'message' => $redis_result['message'], 'cause' => $redis_result['cause'], 'data' => $redis_result['data']));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            Log::error("searchCardsBySubCategoryId : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'search templates.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        //$time_end = microtime(true);
        //$execution_time = $time_end - $time_start;
        //Log::info('searchCardsBySubCategoryIdWithTranslate : total execution time in milli-seconds is :',[$execution_time]);
        return $response;
    }

    /*
    Purpose : for search card with multiple sub_category_id. If result not found then translate & correction of the text
    Description : This method compulsory take 4 argument as parameter.(if any argument is optional then define it here)
    Return : return cards detail if success otherwise error with specific status code
    */
    public function searchCardsByMultipleSubCategoryId(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('sub_category_id', 'search_category', 'page', 'item_count'), $request)) != '')
                return $response;

            $sub_category_id = preg_replace('/[ A-Za-z]/', '', $request->sub_category_id);      //Remove space & alpha character from searching because if we add this character then issue occur in admin panel.
            $search_category = trim(mb_substr(preg_replace('/[\\\@()<>+*%"]/', '', mb_strtolower($request->search_category)), 0, 100));      //Remove '[\\\@()<>+*%"]' character from searching because if we add this character then mysql gives syntax error.
            $page = $request->page;
            $item_count = $request->item_count;
            $offset = ($page - 1) * $item_count;
            $is_featured = isset($request->is_featured) ? $request->is_featured : 1;                //is_featured is use for finding a proper data from DB.
            $category_id = isset($request->category_id) ? $request->category_id : Config::get('constant.CATEGORY_ID_OF_STICKER');               //Category id to find, Which category is use.
            $is_cache_enable = isset($request->is_cache_enable) ? $request->is_cache_enable : 1;
            $catalog_id = isset($request->catalog_id) ? $request->catalog_id : '';         //Catalog id to give a priority template.

            if ($is_cache_enable == 1) {
                $redis_result = $this->searchTemplatesBySearchCategory($search_category, $sub_category_id, $offset, $item_count, $is_featured, $catalog_id);
            }else{
                $redis_result = $this->searchTemplatesByDisableCache($search_category, $sub_category_id, $offset, $item_count, $is_featured, $catalog_id);
            }

            if($page == 1) {
                if($redis_result['code'] != 200){
                    SaveSearchTagJob::dispatch(0, $search_category, $sub_category_id, 0, $is_featured, $category_id);
                }else{
                    SaveSearchTagJob::dispatch($redis_result['data']['total_record'], $search_category, $sub_category_id, 1, $is_featured, $category_id);
                }
            }

            $response = Response::json(array('code' => $redis_result['code'], 'message' => $redis_result['message'], 'cause' => $redis_result['cause'], 'data' => $redis_result['data']));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            Log::error("searchCardsByMultipleSubCategoryId : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'search templates.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    /*
    Purpose : for search card with multiple sub_category_id. If result not found then take fail_over_sub_category_id
    Description : This method compulsory take 4 argument as parameter.(if any argument is optional then define it here)
    Return : return cards detail if success otherwise error with specific status code
    */
    public function searchCardsBySubCategoryIdFailOver(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('sub_category_id', 'search_category', 'fail_over_sub_category_id', 'page', 'item_count'), $request)) != '')
                return $response;

            $sub_category_id = preg_replace('/[ A-Za-z]/', '', $request->sub_category_id);      //Remove space & alpha character from searching because if we add this character then issue occur in admin panel.
            $fail_over_sub_category_id = preg_replace('/[ A-Za-z]/', '', $request->fail_over_sub_category_id);      //Remove space & alpha character from searching because if we add this character then issue occur in admin panel.
            $search_category = trim(mb_substr(preg_replace('/[\\\@()<>+*%"]/', '', mb_strtolower($request->search_category)), 0, 100));      //Remove '[\\\@()<>+*%"]' character from searching because if we add this character then mysql gives syntax error.
            $page = $request->page;
            $item_count = $request->item_count;
            $offset = ($page - 1) * $item_count;
            $is_user_search_tag = isset($request->is_user_search_tag) ? $request->is_user_search_tag : 1;       //In some applications we have put search tags instead of catalog lists, So if user clicks that search tag that time we don't need to insert this tag in DB.
            //$this->is_template = isset($request->is_template) ? $request->is_template : 1;      //1=for template, 2=for sticker,shape,background.
            //$search_category_language_code = isset($request->search_category_language_code) ? $request->search_category_language_code : "";     //if user text language is in english that in "en" that time we don't need to call translate API.
            $is_featured = isset($request->is_featured) ? $request->is_featured : 1;                //is_featured is use for finding a proper data from DB.
            $category_id = isset($request->category_id) ? $request->category_id : Config::get('constant.CATEGORY_ID_OF_STICKER');               //Category id to find, Which category is use.
            $is_cache_enable = isset($request->is_cache_enable) ? $request->is_cache_enable : 1;
            $catalog_id = isset($request->catalog_id) ? $request->catalog_id : '';    //Catalog id to give a priority template.

            if ($is_cache_enable == 1) {
                $redis_result = $this->searchTemplatesBySearchCategory($search_category, $sub_category_id, $offset, $item_count, $is_featured, $catalog_id, $fail_over_sub_category_id);
            }else{
                $redis_result = $this->searchTemplatesByDisableCache($search_category, $sub_category_id, $offset, $item_count, $is_featured, $catalog_id, $fail_over_sub_category_id);
            }

            if($page == 1 && $is_user_search_tag == 1) {
                if($redis_result['code'] != 200){
                    SaveSearchTagJob::dispatch(0, $search_category, $sub_category_id, 0, $is_featured, $category_id);
                }else{
                    SaveSearchTagJob::dispatch($redis_result['data']['total_record'], $search_category, $sub_category_id, 1,  $is_featured, $category_id);
                }
            }

            $response = Response::json(array('code' => $redis_result['code'], 'message' => $redis_result['message'], 'cause' => $redis_result['cause'], 'data' => $redis_result['data']));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            Log::error("searchCardsBySubCategoryIdFailOver : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'search templates.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    public function searchCardsBySubCategoryId_v2(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('sub_category_id', 'search_category', 'page', 'item_count'), $request)) != '')
                return $response;

            $this->sub_category_id = preg_replace('/[ A-Za-z]/', '', $request->sub_category_id);      //Remove space & alpha character from searching because if we add this character then issue occur in admin panel.
            $this->search_category = trim(mb_substr(preg_replace('/[\\\@()<>+*%"]/', '', mb_strtolower($request->search_category)), 0, 100));      //Remove '[\\\@()<>+*%"]' character from searching because if we add this character then mysql gives syntax error.
            $this->page = $request->page;
            $this->item_count = $request->item_count;
            $this->offset = ($this->page - 1) * $this->item_count;
            $this->is_featured = isset($request->is_featured) ? $request->is_featured : 1;   //is_featured is use for finding a proper data from DB.
            $category_id = isset($request->category_id) ? $request->category_id : Config::get('constant.CATEGORY_ID_OF_STICKER');    //Category id to find, Which category is use.


            //validate search text
            $this->is_verified = (new VerificationController())->verifySearchText($this->search_category);

            if (!Cache::has("pel:searchCardsBySubCategoryId_v2$this->sub_category_id:$this->search_category:$this->offset:$this->item_count")) {
                $result = Cache::rememberforever("searchCardsBySubCategoryId_v2$this->sub_category_id:$this->search_category:$this->offset:$this->item_count", function () {

                    $search_category = $this->search_category;
                    $code = 200;
                    $message = "Templates fetched successfully.";

                    if ($this->is_verified == 1) {
                        if (!Cache::has("pel:searchCardsBySubCategoryId_v2$this->sub_category_id:$this->search_category")) {
                            $result = Cache::rememberforever("searchCardsBySubCategoryId_v2$this->sub_category_id:$this->search_category", function () {
                                $total_row_result = DB::select('SELECT count(*) as total
                                                                FROM
                                                                images as im,
                                                                catalog_master AS cm,
                                                                sub_category_catalog AS scc
                                                                WHERE
                                                                im.is_active = 1 AND
                                                                im.catalog_id = scc.catalog_id AND
                                                                cm.is_featured = ? AND
                                                                cm.id = scc.catalog_id AND
                                                                scc.sub_category_id = ? AND
                                                                isnull(im.original_img) AND
                                                                isnull(im.display_img) AND
                                                                (MATCH(im.search_category) AGAINST("' . $this->search_category . '") OR 
                                                                MATCH(im.search_category) AGAINST(REPLACE(concat("' . $this->search_category . '"," ")," ","* ") IN BOOLEAN MODE))
                                                                ', [$this->is_featured, $this->sub_category_id]);

                                return $total_row_result[0]->total;
                            });
                        }
                        $total_row = Cache::get("searchCardsBySubCategoryId_v2$this->sub_category_id:$this->search_category");

                        $search_result = DB::select('SELECT
                                                    DISTINCT im.id as json_id,
                                                    IF(im.attribute1 != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",im.attribute1),"") as sample_image,
                                                    im.is_free,
                                                    im.is_featured,
                                                    im.is_portrait,
                                                    coalesce(im.height,0) AS height,
                                                    coalesce(im.width,0) AS width,
                                                    im.updated_at,
                                                    MATCH(im.search_category) AGAINST("' . $this->search_category . '") +
                                                    MATCH(im.search_category) AGAINST(REPLACE(concat("' . $this->search_category . '"," ")," ","* ") IN BOOLEAN MODE) AS search_text 
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
                                                    (MATCH(im.search_category) AGAINST("' . $this->search_category . '") OR 
                                                    MATCH(im.search_category) AGAINST(REPLACE(concat("' . $this->search_category . '"," ")," ","* ") IN BOOLEAN MODE))
                                                    ORDER BY search_text DESC,im.updated_at DESC LIMIT ?, ?', [$this->sub_category_id, $this->offset, $this->item_count]);
                    } else {
                        $search_result = [];
                    }

                    if (count($search_result) <= 0) {
                        if (!Cache::has("pel:searchCardsBySubCategoryId_v2$this->sub_category_id")) {
                            $result = Cache::rememberforever("searchCardsBySubCategoryId_v2$this->sub_category_id", function () {

                                $total_row_result = DB::select('SELECT count(*) as total
                                                            FROM
                                                            images as im,
                                                            catalog_master AS cm,
                                                            sub_category_catalog AS scc
                                                            WHERE
                                                            im.is_active = 1 AND
                                                            im.catalog_id = scc.catalog_id AND
                                                            cm.id = scc.catalog_id AND
                                                            scc.sub_category_id = ? AND
                                                            cm.is_featured = 1 AND
                                                            isnull(im.original_img) AND
                                                            isnull(im.display_img)
                                                            ', [$this->sub_category_id]);
                                return $total_row_result[0]->total;
                            });
                        }
                        $total_row = Cache::get("searchCardsBySubCategoryId_v2$this->sub_category_id");

                        $search_result = DB::select('SELECT
                                                    DISTINCT im.id as json_id,
                                                    IF(im.attribute1 != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",im.attribute1),"") as sample_image,
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
                        $message = "Sorry, we couldn't find any templates for '$this->search_category', but we found some other templates you might like:";
                    }

                    $is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;
                    $search_result = array('total_record' => $total_row, 'is_next_page' => $is_next_page, 'result' => $search_result);

                    $result = array('result' => $search_result, 'code' => $code, 'message' => $message);
                    return $result;
                });

            }

            $redis_result = Cache::get("searchCardsBySubCategoryId_v2$this->sub_category_id:$this->search_category:$this->offset:$this->item_count");

            if (!$redis_result) {
                $redis_result = [];
            }

            if($this->page == 1) {
                if($redis_result['code'] != 200){
                    SaveSearchTagJob::dispatch(0, $this->search_category, $this->sub_category_id, 0, $this->is_featured, $category_id);
                }else{
                    SaveSearchTagJob::dispatch($redis_result['result']['total_record'], $this->search_category, $this->sub_category_id, 1, $this->is_featured, $category_id);
                }
            }

            $response = Response::json(array('code' => $redis_result['code'], 'message' => $redis_result['message'], 'cause' => '', 'data' => $redis_result['result']));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            Log::error("searchCardsBySubCategoryId_v2 : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'search templates.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    /**
     * @api {post} searchNormalImagesBySubCategoryId searchNormalImagesBySubCategoryId
     * @apiName searchNormalImagesBySubCategoryId
     * @apiGroup User
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {//all parameters are compulsory
     * "sub_category_id":66,
     * "search_category":"india",
     * "page":1,
     * "item_count":1
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Content fetched successfully.",
     * "cause": "",
     * "data": {
     * "total_record": 6,
     * "is_next_page": false,
     * "catalog_list": [
     * {
     * "catalog_id": 606,
     * "name": "Flower",
     * "thumbnail_img": "http://192.168.0.115/photo_editor_lab_backend/image_bucket/thumbnail/5ccad4f1b8ee4_catalog_img_1556796657.png",
     * "compressed_img": "http://192.168.0.115/photo_editor_lab_backend/image_bucket/compressed/5ccad4f1b8ee4_catalog_img_1556796657.png",
     * "original_img": "http://192.168.0.115/photo_editor_lab_backend/image_bucket/original/5ccad4f1b8ee4_catalog_img_1556796657.png",
     * "webp_thumbnail_img": "http://192.168.0.115/photo_editor_lab_backend/image_bucket/webp_thumbnail/5ccad4f1b8ee4_catalog_img_1556796657.webp",
     * "webp_original_img": "http://192.168.0.115/photo_editor_lab_backend/image_bucket/webp_original/5ccad4f1b8ee4_catalog_img_1556796657.webp",
     * "is_featured": 0,
     * "is_free": 1,
     * "updated_at": "2019-10-14 01:16:33"
     * }
     * ],
     * "content_list": [
     * {
     * "img_id": 7281,
     * "sample_image": "http://192.168.0.115/photo_editor_lab_backend/image_bucket/compressed/5ccad50513133_normal_image_1556796677.png",
     * "is_free": 1,
     * "is_featured": null,
     * "is_portrait": null,
     * "height": 0,
     * "width": 0,
     * "updated_at": "2019-10-14 01:30:59",
     * "search_text": 20.13739013671875
     * },
     * {
     * "img_id": 7285,
     * "sample_image": "http://192.168.0.115/photo_editor_lab_backend/image_bucket/compressed/5ccad5057288c_normal_image_1556796677.png",
     * "is_free": 1,
     * "is_featured": null,
     * "is_portrait": null,
     * "height": 0,
     * "width": 0,
     * "updated_at": "2019-10-14 01:30:55",
     * "search_text": 20.13739013671875
     * },
     * {
     * "img_id": 7283,
     * "sample_image": "http://192.168.0.115/photo_editor_lab_backend/image_bucket/compressed/5ccad50541763_normal_image_1556796677.png",
     * "is_free": 1,
     * "is_featured": null,
     * "is_portrait": null,
     * "height": 0,
     * "width": 0,
     * "updated_at": "2019-10-14 01:30:54",
     * "search_text": 20.13739013671875
     * },
     * {
     * "img_id": 7282,
     * "sample_image": "http://192.168.0.115/photo_editor_lab_backend/image_bucket/compressed/5ccad5052b7d3_normal_image_1556796677.png",
     * "is_free": 1,
     * "is_featured": null,
     * "is_portrait": null,
     * "height": 0,
     * "width": 0,
     * "updated_at": "2019-10-12 10:51:37",
     * "search_text": 20.13739013671875
     * },
     * {
     * "img_id": 7284,
     * "sample_image": "http://192.168.0.115/photo_editor_lab_backend/image_bucket/compressed/5ccad5055c8fc_normal_image_1556796677.png",
     * "is_free": 1,
     * "is_featured": null,
     * "is_portrait": null,
     * "height": 0,
     * "width": 0,
     * "updated_at": "2019-10-12 10:51:31",
     * "search_text": 20.13739013671875
     * },
     * {
     * "img_id": 7280,
     * "sample_image": "http://192.168.0.115/photo_editor_lab_backend/image_bucket/compressed/5ccad504ec5c3_normal_image_1556796676.png",
     * "is_free": 1,
     * "is_featured": null,
     * "is_portrait": null,
     * "height": 0,
     * "width": 0,
     * "updated_at": "2019-10-12 07:04:36",
     * "search_text": 20.13739013671875
     * }
     * ]
     * }
     * }
     */
    public function searchNormalImagesBySubCategoryId(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('sub_category_id', 'search_category', 'page', 'item_count'), $request)) != '')
                return $response;

            $this->sub_category_id = $request->sub_category_id;
            $this->search_category = strtolower(trim($request->search_category));
            $this->page = $request->page;
            $this->item_count = $request->item_count;
            $this->offset = ($this->page - 1) * $this->item_count;

            //validate search text
            $this->is_verified = (new VerificationController())->verifySearchText($this->search_category);

            if (!Cache::has("pel:searchNormalImagesBySubCategoryId$this->sub_category_id:$this->search_category:$this->offset:$this->item_count")) {
                $result = Cache::rememberforever("searchNormalImagesBySubCategoryId$this->sub_category_id:$this->search_category:$this->offset:$this->item_count", function () {

                    $search_category = $this->search_category;
                    $code = 200;
                    $message = "Content fetched successfully.";


                    if ($this->is_verified == 1) {
                        $catalog_list = [];
                        if($this->page == 1) {
                            $catalog_list = DB::select('SELECT
                                          ct.id as catalog_id,
                                          ct.name,
                                          IF(ct.image != "",CONCAT("' . Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.image),"") as thumbnail_img,
                                          IF(ct.image != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.image),"") as compressed_img,
                                          IF(ct.image != "",CONCAT("' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.image),"") as original_img,
                                          IF(ct.attribute1 != "",CONCAT("' . Config::get('constant.WEBP_THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.attribute1),"") as webp_thumbnail_img,
                                          IF(ct.attribute1 != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.attribute1),"") as webp_original_img,
                                          ct.is_featured,
                                          ct.is_free,
                                          ct.is_ios_free,
                                          ct.updated_at
                                        FROM
                                          catalog_master as ct,
                                          sub_category_catalog as sct
                                        WHERE
                                          sct.sub_category_id = ? AND
                                          sct.catalog_id = ct.id AND
                                          (MATCH(ct.name) AGAINST("' . $search_category . '") OR 
                                          MATCH(ct.name) AGAINST(REPLACE(concat("' . $search_category . '"," ")," ","* ") IN BOOLEAN MODE)) AND
                                          sct.is_active = 1  ORDER BY ct.updated_at DESC', [$this->sub_category_id]);
                        }
                        $total_row_result = DB::select('SELECT count(*) as total
                                                                FROM
                                                                images as im,
                                                                catalog_master AS cm,
                                                                sub_category_catalog AS scc
                                                                WHERE
                                                                im.is_active = 1 AND
                                                                im.catalog_id = scc.catalog_id AND
                                                                cm.id = scc.catalog_id AND
                                                                cm.is_featured = 0 AND
                                                                scc.sub_category_id = ? AND
                                                                isnull(im.original_img) AND
                                                                isnull(im.display_img) AND
                                                                (MATCH(im.search_category) AGAINST("' . $search_category . '") OR 
                                                                MATCH(im.search_category) AGAINST(REPLACE(concat("' . $search_category . '"," ")," ","* ") IN BOOLEAN MODE))
                                                                ', [$this->sub_category_id]);

                        $total_row = $total_row_result[0]->total;

                        $search_result = DB::select('SELECT
                                                    DISTINCT im.id AS img_id,
                                                    IF(im.image != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",im.image),"") as sample_image,
                                                    IF(cm.is_free=1,1,0) AS is_free,
                                                    IF(cm.is_ios_free=1,1,0) AS is_ios_free,
                                                    im.is_featured,
                                                    im.is_portrait,
                                                    coalesce(im.height,0) AS height,
                                                    coalesce(im.width,0) AS width,
                                                    im.updated_at,
                                                    MATCH(im.search_category) AGAINST("' . $search_category . '") +
                                                    MATCH(im.search_category) AGAINST(REPLACE(concat("' . $search_category . '"," ")," ","* ") IN BOOLEAN MODE) AS search_text 
                                                    FROM
                                                    images as im,
                                                    catalog_master AS cm,
                                                    sub_category_catalog AS scc
                                                    WHERE
                                                    im.is_active = 1 AND
                                                    im.catalog_id = scc.catalog_id AND
                                                    cm.id = scc.catalog_id AND
                                                    cm.is_featured = 0 AND
                                                    scc.sub_category_id = ? AND
                                                    isnull(im.original_img) AND
                                                    isnull(im.display_img) AND
                                                    (MATCH(im.search_category) AGAINST("' . $search_category . '") OR 
                                                    MATCH(im.search_category) AGAINST(REPLACE(concat("' . $search_category . '"," ")," ","* ") IN BOOLEAN MODE))
                                                    ORDER BY search_text DESC,im.updated_at DESC LIMIT ?, ?', [$this->sub_category_id, $this->offset, $this->item_count]);
                    } else {
                        $catalog_list = [];
                        $search_result = [];
                    }

                   /* if (count($search_result) <= 0) {


                        $total_row_result = DB::select('SELECT count(*) as total
                                                            FROM
                                                            images as im,
                                                            catalog_master AS cm,
                                                            sub_category_catalog AS scc
                                                            WHERE
                                                            im.is_active = 1 AND
                                                            im.catalog_id = scc.catalog_id AND
                                                            cm.id = scc.catalog_id AND
                                                            cm.is_featured = 0 AND
                                                            scc.sub_category_id = ? AND
                                                            isnull(im.original_img) AND
                                                            isnull(im.display_img)
                                                            ', [$this->sub_category_id]);

                        $total_row = $total_row_result[0]->total;

                        $search_result = DB::select('SELECT
                                                    DISTINCT im.id AS img_id,
                                                    IF(im.image != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",im.image),"") as sample_image,
                                                    IF(cm.is_free=1,1,0) AS is_free,
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
                                                    cm.is_featured = 0 AND
                                                    scc.sub_category_id = ? AND
                                                    isnull(im.original_img) AND
                                                    isnull(im.display_img)
                                                    ORDER BY im.updated_at DESC LIMIT ?, ?', [$this->sub_category_id, $this->offset, $this->item_count]);
                        $code = 427;
                        $message = "Sorry, we couldn't find any content for '$search_category', but we found some other content you might like:";
                    }*/
                    $total_row = isset($total_row) ? $total_row : 0;
                    $is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;
                    $search_result = array('total_record' => $total_row, 'is_next_page' => $is_next_page, 'catalog_list' => $catalog_list, 'content_list' => $search_result);

                    $result = array('result' => $search_result, 'code' => $code, 'message' => $message);
                    return $result;
                });

            }

            $redis_result = Cache::get("searchNormalImagesBySubCategoryId$this->sub_category_id:$this->search_category:$this->offset:$this->item_count");

            if (!$redis_result) {
                $redis_result = [];
            }

            $response = Response::json(array('code' => $redis_result['code'], 'message' => $redis_result['message'], 'cause' => '', 'data' => $redis_result['result']));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            Log::error("searchNormalImagesBySubCategoryId : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'search content.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    public function searchNormalImagesBySubCategoryIdForFlyer(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('sub_category_id', 'search_category', 'page', 'item_count'), $request)) != '')
                return $response;

            $this->sub_category_id = $request->sub_category_id;
            //$this->search_category = strtolower(trim($request->search_category));
            //Remove '[\\\@()<>+*%"~-]' character from searching because if we add this character then mysql gives syntax error.
            $this->search_category = mb_substr(preg_replace('/[\\\@()<>+*%"~-]/', '', mb_strtolower(trim($request->search_category))), 0, 100);
            $this->page = $request->page;
            $this->item_count = $request->item_count;
            $this->offset = ($this->page - 1) * $this->item_count;
            $this->is_featured = isset($request->is_featured) ? $request->is_featured : 0;
            $category_id = isset($request->category_id) ? $request->category_id : Config::get('constant.CATEGORY_ID_OF_STICKER');
            $is_cache_enable = isset($request->is_cache_enable) ? $request->is_cache_enable : 1;

            if (!$this->search_category) {
                return Response::json(array('code' => 201, 'message' => 'Please enter valid search text.', 'cause' => '', 'data' => json_decode("{}")));
            }

            if ($is_cache_enable) {
                $redis_result = Cache::remember("key:searchNormalImagesBySubCategoryIdForFlyer:$this->sub_category_id:$this->search_category:$this->offset:$this->item_count", Config::get('constant.CACHE_TIME_6_HOUR'), function () {

                    $search_category = $this->search_category;
                    $code = 200;
                    $message = "Content fetched successfully.";

                    $catalog_list = [];
                    if ($this->page == 1) {
                        DB::statement("SET sql_mode = '' ");
                        $catalog_list = DB::select('SELECT
                                                      ct.id as catalog_id,
                                                      ct.name,
                                                      IF(ct.image != "",CONCAT("' . Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.image),"") as thumbnail_img,
                                                      IF(ct.image != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.image),"") as compressed_img,
                                                      IF(ct.image != "",CONCAT("' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.image),"") as original_img,
                                                      IF(ct.attribute1 != "",CONCAT("' . Config::get('constant.WEBP_THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.attribute1),"") as webp_thumbnail_img,
                                                      IF(ct.attribute1 != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.attribute1),"") as webp_original_img,
                                                      ct.is_featured,
                                                      sct.sub_category_id,
                                                      ct.is_free,
                                                      ct.is_ios_free,
                                                      ct.updated_at
                                                    FROM
                                                      catalog_master as ct,
                                                      sub_category_catalog as sct
                                                    WHERE
                                                      sct.sub_category_id IN (' . $this->sub_category_id . ') AND
                                                      sct.catalog_id = ct.id AND
                                                      (MATCH(ct.name) AGAINST ("' . $search_category . '") OR 
                                                      MATCH(ct.name) AGAINST (REPLACE(CONCAT("' . $search_category . '"," ")," ","* ") IN BOOLEAN MODE)) AND
                                                      sct.is_active = 1
                                                      GROUP BY catalog_id
                                                      ORDER BY FIELD(sct.sub_category_id,' . $this->sub_category_id . '),ct.updated_at DESC');
                    }

                    $total_row = Cache::remember("searchNormalImagesBySubCategoryIdForFlyer:$this->sub_category_id:$this->search_category", Config::get('constant.CACHE_TIME_6_HOUR'), function () {
                            $total_row_result = DB::select('SELECT count(DISTINCT im.id) as total
                                                            FROM
                                                            images as im,
                                                            catalog_master AS cm,
                                                            sub_category_catalog AS scc
                                                            WHERE
                                                            im.is_active = 1 AND
                                                            im.catalog_id = scc.catalog_id AND
                                                            cm.id = scc.catalog_id AND
                                                            cm.is_featured = ? AND
                                                            scc.sub_category_id IN (' . $this->sub_category_id . ') AND
                                                            isnull(im.original_img) AND
                                                            isnull(im.display_img) AND
                                                            (MATCH(im.search_category) AGAINST("' . $this->search_category . '") OR 
                                                            MATCH(im.search_category) AGAINST(REPLACE(concat("' . $this->search_category . '"," ")," ","* ") IN BOOLEAN MODE))', [$this->is_featured]);

                            return $total_row_result[0]->total;
                    });

                    if($total_row) {

                        DB::statement("SET sql_mode = '' ");
                        $search_result = DB::select('SELECT
                                                        im.id AS img_id,
                                                        @svg := IF(im.content_type = ' . Config::get('constant.CONTENT_TYPE_FOR_SVG_RESOURCE') . ' AND im.image != "", CONCAT("' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '", im.image), "") AS svg_image,
                                                        IF(@svg != "", @svg, IF(im.image != "", CONCAT("' . Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '", im.image), "")) AS thumbnail_img,
                                                        IF(@svg != "", @svg, IF(im.image != "", CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '", im.image), "")) AS compressed_img,
                                                        IF(@svg != "", @svg, IF(im.image != "", CONCAT("' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '", im.image), "")) AS original_img,
                                                        IF(im.image != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",im.image),"") as sample_image,
                                                        IF(cm.is_free=1,1,0) AS is_free,
                                                        IF(cm.is_ios_free=1,1,0) AS is_ios_free,
                                                        scc.sub_category_id,
                                                        im.is_featured,
                                                        im.is_portrait,
                                                        im.search_category,
                                                        im.template_name,                                                        
                                                        coalesce(im.height,0) AS height,
                                                        coalesce(im.width,0) AS width,
                                                        im.updated_at,
                                                        MATCH(im.search_category) AGAINST ("' . $search_category . '") +
                                                        MATCH(im.search_category) AGAINST ("' . $search_category . '* " IN BOOLEAN MODE) AS search_text 
                                                    FROM
                                                        images as im,
                                                        catalog_master AS cm,
                                                        sub_category_catalog AS scc
                                                    WHERE
                                                        im.is_active = 1 AND
                                                        im.catalog_id = scc.catalog_id AND
                                                        cm.id = scc.catalog_id AND
                                                        cm.is_featured = ? AND
                                                        scc.sub_category_id IN(' . $this->sub_category_id . ') AND
                                                        isnull(im.original_img) AND
                                                        isnull(im.display_img) AND
                                                        (MATCH(im.search_category) AGAINST ("' . $search_category . '") OR 
                                                        MATCH(im.search_category) AGAINST ("' . $search_category . '* " IN BOOLEAN MODE))
                                                    GROUP BY 
                                                        img_id
                                                    ORDER BY 
                                                        FIELD(scc.sub_category_id,' . $this->sub_category_id . '), search_text DESC, im.updated_at DESC 
                                                    LIMIT ?, ?', [$this->is_featured, $this->offset, $this->item_count]);
                    } else {
                        $code = 427;
                        $message = "Sorry, we couldn't find any templates for '$this->search_category'";
                        $catalog_list = [];
                        $search_result = [];
                    }

                    $total_row = isset($total_row) ? $total_row : 0;
                    $is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;
                    $search_result = array('total_record' => $total_row, 'is_next_page' => $is_next_page, 'catalog_list' => $catalog_list, 'content_list' => $search_result);

                    $result = array('result' => $search_result, 'code' => $code, 'message' => $message);
                    return $result;
                });

            } else {

                $search_category = $this->search_category;
                $code = 200;
                $message = "Content fetched successfully.";

                $catalog_list = [];
                if ($this->page == 1) {
                    DB::statement("SET sql_mode = '' ");
                    $catalog_list = DB::select('SELECT
                                                      ct.id as catalog_id,
                                                      ct.name,
                                                      IF(ct.image != "",CONCAT("' . Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.image),"") as thumbnail_img,
                                                      IF(ct.image != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.image),"") as compressed_img,
                                                      IF(ct.image != "",CONCAT("' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.image),"") as original_img,
                                                      IF(ct.attribute1 != "",CONCAT("' . Config::get('constant.WEBP_THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.attribute1),"") as webp_thumbnail_img,
                                                      IF(ct.attribute1 != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.attribute1),"") as webp_original_img,
                                                      ct.is_featured,
                                                      sct.sub_category_id,
                                                      ct.is_free,
                                                      ct.is_ios_free,
                                                      ct.updated_at
                                                    FROM
                                                      catalog_master as ct,
                                                      sub_category_catalog as sct
                                                    WHERE
                                                      sct.sub_category_id IN(' . $this->sub_category_id . ') AND
                                                      sct.catalog_id = ct.id AND
                                                      (MATCH(ct.name) AGAINST("' . $search_category . '") OR 
                                                      MATCH(ct.name) AGAINST(REPLACE(concat("' . $search_category . '"," ")," ","* ") IN BOOLEAN MODE)) AND
                                                      sct.is_active = 1
                                                      GROUP BY catalog_id
                                                      ORDER BY FIELD(sct.sub_category_id,' . $this->sub_category_id . '),ct.updated_at DESC');
                }

                $total_row_result = DB::select('SELECT count(DISTINCT im.id) as total
                                                        FROM
                                                        images as im,
                                                        catalog_master AS cm,
                                                        sub_category_catalog AS scc
                                                        WHERE
                                                        im.catalog_id = scc.catalog_id AND
                                                        cm.id = scc.catalog_id AND
                                                        cm.is_featured = ? AND
                                                        scc.sub_category_id IN(' . $this->sub_category_id . ') AND
                                                        isnull(im.original_img) AND
                                                        isnull(im.display_img) AND
                                                        (MATCH(im.search_category) AGAINST ("' . $this->search_category . '") OR 
                                                        MATCH(im.search_category) AGAINST ("' . $search_category . '* " IN BOOLEAN MODE))', [$this->is_featured]);

                $total_row = $total_row_result[0]->total;

                if($total_row) {

                    DB::statement("SET sql_mode = '' ");
                    $search_result = DB::select('SELECT
                                                        im.id AS img_id,
                                                        @svg := IF(im.content_type = ' . Config::get('constant.CONTENT_TYPE_FOR_SVG_RESOURCE') . ' AND im.image != "", CONCAT("' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '", im.image), "") AS svg_image,
                                                        IF(@svg != "", @svg, IF(im.image != "", CONCAT("' . Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '", im.image), "")) AS thumbnail_img,
                                                        IF(@svg != "", @svg, IF(im.image != "", CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '", im.image), "")) AS compressed_img,
                                                        IF(@svg != "", @svg, IF(im.image != "", CONCAT("' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '", im.image), "")) AS original_img,
                                                        IF(im.image != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",im.image),"") as sample_image,
                                                        IF(cm.is_free=1,1,0) AS is_free,
                                                        IF(cm.is_ios_free=1,1,0) AS is_ios_free,
                                                        scc.sub_category_id,
                                                        im.is_featured,
                                                        im.is_portrait,
                                                        im.search_category,
                                                        im.template_name,
                                                        coalesce(im.height,0) AS height,
                                                        coalesce(im.width,0) AS width,
                                                        im.updated_at,
                                                        MATCH(im.search_category) AGAINST ("' . $search_category . '") +
                                                        MATCH(im.search_category) AGAINST ("' . $search_category . '* " IN BOOLEAN MODE) AS search_text 
                                                    FROM
                                                        images as im,
                                                        catalog_master AS cm,
                                                        sub_category_catalog AS scc
                                                    WHERE
                                                        im.catalog_id = scc.catalog_id AND
                                                        cm.id = scc.catalog_id AND
                                                        cm.is_featured = ? AND
                                                        scc.sub_category_id IN(' . $this->sub_category_id . ') AND
                                                        isnull(im.original_img) AND
                                                        isnull(im.display_img) AND
                                                        (MATCH(im.search_category) AGAINST("' . $search_category . '") OR 
                                                        MATCH(im.search_category) AGAINST ("' . $search_category . '* " IN BOOLEAN MODE))
                                                    GROUP BY 
                                                        img_id
                                                    ORDER BY 
                                                        FIELD(scc.sub_category_id,' . $this->sub_category_id . '), search_text DESC, im.updated_at DESC 
                                                    LIMIT ?, ?', [$this->is_featured, $this->offset, $this->item_count]);
                } else {
                    $code = 427;
                    $message = "Sorry, we couldn't find any templates for '$this->search_category'";
                    $catalog_list = [];
                    $search_result = [];
                }

                $total_row = isset($total_row) ? $total_row : 0;
                $is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;
                $search_result = array('total_record' => $total_row, 'is_next_page' => $is_next_page, 'catalog_list' => $catalog_list, 'content_list' => $search_result);

                $redis_result = array('result' => $search_result, 'code' => $code, 'message' => $message);
            }

            if ($this->page == 1) {
                if ($redis_result['code'] != 200) {
                    SaveSearchTagJob::dispatch(0, $this->search_category, $this->sub_category_id, 0, $this->is_featured, $category_id);
                } else {
                    SaveSearchTagJob::dispatch($redis_result['result']['total_record'], $this->search_category, $this->sub_category_id, 1, $this->is_featured, $category_id);
                }
            }

            $response = Response::json(array('code' => $redis_result['code'], 'message' => $redis_result['message'], 'cause' => '', 'data' => $redis_result['result']));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            Log::error("searchNormalImagesBySubCategoryIdForFlyer : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'search content.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    public function searchNormalImagesBySubCategoryId_v2(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('sub_category_id', 'search_category', 'page', 'item_count'), $request)) != '')
                return $response;

            $this->sub_category_id = $request->sub_category_id;
            $this->search_category = strtolower(trim($request->search_category));
            $this->page = $request->page;
            $this->item_count = $request->item_count;
            $this->offset = ($this->page - 1) * $this->item_count;
            $is_cache_enable = isset($request->is_cache_enable) ? $request->is_cache_enable : 1;

            //validate search text
            $this->is_verified = (new VerificationController())->verifySearchText($this->search_category);

            if ($is_cache_enable) {
                $redis_result = Cache::remember("searchNormalImagesBySubCategoryId_v2$this->sub_category_id:$this->search_category:$this->offset:$this->item_count", Config::get('constant.CACHE_TIME_6_HOUR'), function () {

                    $search_category = $this->search_category;
                    $code = 200;
                    $message = "Content fetched successfully.";


                    if ($this->is_verified == 1) {
                        $catalog_list = [];
                        if($this->page == 1) {
                            $catalog_list = DB::select('SELECT
                                          ct.id as catalog_id,
                                          ct.name,
                                          IF(ct.image != "",CONCAT("' . Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.image),"") as thumbnail_img,
                                          IF(ct.image != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.image),"") as compressed_img,
                                          IF(ct.image != "",CONCAT("' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.image),"") as original_img,
                                          IF(ct.attribute1 != "",CONCAT("' . Config::get('constant.WEBP_THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.attribute1),"") as webp_thumbnail_img,
                                          IF(ct.attribute1 != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.attribute1),"") as webp_original_img,
                                          ct.is_featured,
                                          ct.is_free,
                                          ct.updated_at
                                        FROM
                                          catalog_master as ct,
                                          sub_category_catalog as sct
                                        WHERE
                                          sct.sub_category_id = ? AND
                                          sct.catalog_id = ct.id AND
                                          (MATCH(ct.name) AGAINST("' . $search_category . '") OR 
                                          MATCH(ct.name) AGAINST(REPLACE(concat("' . $search_category . '"," ")," ","* ") IN BOOLEAN MODE)) AND
                                          sct.is_active = 1  ORDER BY ct.updated_at DESC', [$this->sub_category_id]);
                        }
                        if (!Cache::has("pel:searchNormalImagesBySubCategoryId_v2$this->sub_category_id:$this->search_category")) {
                            $result = Cache::rememberforever("searchNormalImagesBySubCategoryId_v2$this->sub_category_id:$this->search_category", function () {
                                $total_row_result = DB::select('SELECT count(*) as total
                                                                FROM
                                                                images as im,
                                                                catalog_master AS cm,
                                                                sub_category_catalog AS scc
                                                                WHERE
                                                                im.is_active = 1 AND
                                                                im.catalog_id = scc.catalog_id AND
                                                                cm.id = scc.catalog_id AND
                                                                cm.is_featured = 0 AND
                                                                scc.sub_category_id = ? AND
                                                                isnull(im.original_img) AND
                                                                isnull(im.display_img) AND
                                                                (MATCH(im.search_category) AGAINST("' . $this->search_category . '") OR 
                                                                MATCH(im.search_category) AGAINST(REPLACE(concat("' . $this->search_category . '"," ")," ","* ") IN BOOLEAN MODE))
                                                                ', [$this->sub_category_id]);
                                return $total_row_result[0]->total;
                            });
                        }
                        $total_row = Cache::get("searchNormalImagesBySubCategoryId_v2$this->sub_category_id:$this->search_category");

                        $search_result = DB::select('SELECT
                                                    DISTINCT im.id AS img_id,
                                                    IF(im.image != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",im.image),"") as sample_image,
                                                    IF(im.image != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",im.image),"") AS compressed_img,
                                                    IF(cm.is_free=1,1,0) AS is_free,
                                                    im.is_featured,
                                                    im.is_portrait,
                                                    coalesce(im.height,0) AS height,
                                                    coalesce(im.width,0) AS width,
                                                    im.updated_at,
                                                    MATCH(im.search_category) AGAINST("' . $search_category . '") +
                                                    MATCH(im.search_category) AGAINST(REPLACE(concat("' . $search_category . '"," ")," ","* ") IN BOOLEAN MODE) AS search_text 
                                                    FROM
                                                    images as im,
                                                    catalog_master AS cm,
                                                    sub_category_catalog AS scc
                                                    WHERE
                                                    im.is_active = 1 AND
                                                    im.catalog_id = scc.catalog_id AND
                                                    cm.id = scc.catalog_id AND
                                                    cm.is_featured = 0 AND
                                                    scc.sub_category_id = ? AND
                                                    isnull(im.original_img) AND
                                                    isnull(im.display_img) AND
                                                    (MATCH(im.search_category) AGAINST("' . $search_category . '") OR 
                                                    MATCH(im.search_category) AGAINST(REPLACE(concat("' . $search_category . '"," ")," ","* ") IN BOOLEAN MODE))
                                                    ORDER BY search_text DESC,im.updated_at DESC LIMIT ?, ?', [$this->sub_category_id, $this->offset, $this->item_count]);
                    } else {
                        $catalog_list = [];
                        $search_result = [];
                    }

                    $total_row = isset($total_row) ? $total_row : 0;
                    $is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;
                    $search_result = array('total_record' => $total_row, 'is_next_page' => $is_next_page, 'catalog_list' => $catalog_list, 'content_list' => $search_result);

                    $result = array('result' => $search_result, 'code' => $code, 'message' => $message);
                    return $result;
                });

            } else {

                $search_category = $this->search_category;
                $code = 200;
                $message = "Content fetched successfully.";


                if ($this->is_verified == 1) {
                    $catalog_list = [];
                    if ($this->page == 1) {
                        $catalog_list = DB::select('SELECT
                                          ct.id as catalog_id,
                                          ct.name,
                                          IF(ct.image != "",CONCAT("' . Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.image),"") as thumbnail_img,
                                          IF(ct.image != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.image),"") as compressed_img,
                                          IF(ct.image != "",CONCAT("' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.image),"") as original_img,
                                          IF(ct.attribute1 != "",CONCAT("' . Config::get('constant.WEBP_THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.attribute1),"") as webp_thumbnail_img,
                                          IF(ct.attribute1 != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.attribute1),"") as webp_original_img,
                                          ct.is_featured,
                                          ct.is_free,
                                          ct.updated_at
                                        FROM
                                          catalog_master as ct,
                                          sub_category_catalog as sct
                                        WHERE
                                          sct.sub_category_id = ? AND
                                          sct.catalog_id = ct.id AND
                                          (MATCH(ct.name) AGAINST("' . $search_category . '") OR 
                                          MATCH(ct.name) AGAINST(REPLACE(concat("' . $search_category . '"," ")," ","* ") IN BOOLEAN MODE)) AND
                                          sct.is_active = 1  ORDER BY ct.updated_at DESC', [$this->sub_category_id]);
                    }
                    if (!Cache::has("pel:searchNormalImagesBySubCategoryId_v2$this->sub_category_id:$this->search_category")) {
                        $result = Cache::rememberforever("searchNormalImagesBySubCategoryId_v2$this->sub_category_id:$this->search_category", function () {
                            $total_row_result = DB::select('SELECT count(*) as total
                                                                FROM
                                                                images as im,
                                                                catalog_master AS cm,
                                                                sub_category_catalog AS scc
                                                                WHERE
                                                                im.is_active = 1 AND
                                                                im.catalog_id = scc.catalog_id AND
                                                                cm.id = scc.catalog_id AND
                                                                cm.is_featured = 0 AND
                                                                scc.sub_category_id = ? AND
                                                                isnull(im.original_img) AND
                                                                isnull(im.display_img) AND
                                                                (MATCH(im.search_category) AGAINST("' . $this->search_category . '") OR 
                                                                MATCH(im.search_category) AGAINST(REPLACE(concat("' . $this->search_category . '"," ")," ","* ") IN BOOLEAN MODE))
                                                                ', [$this->sub_category_id]);
                            return $total_row_result[0]->total;
                        });
                    }
                    $total_row = Cache::get("searchNormalImagesBySubCategoryId_v2$this->sub_category_id:$this->search_category");

                    $search_result = DB::select('SELECT
                                                    DISTINCT im.id AS img_id,
                                                    IF(im.image != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",im.image),"") as sample_image,
                                                    IF(cm.is_free=1,1,0) AS is_free,
                                                    im.is_featured,
                                                    im.is_portrait,
                                                    coalesce(im.height,0) AS height,
                                                    coalesce(im.width,0) AS width,
                                                    im.updated_at,
                                                    MATCH(im.search_category) AGAINST("' . $search_category . '") +
                                                    MATCH(im.search_category) AGAINST(REPLACE(concat("' . $search_category . '"," ")," ","* ") IN BOOLEAN MODE) AS search_text 
                                                    FROM
                                                    images as im,
                                                    catalog_master AS cm,
                                                    sub_category_catalog AS scc
                                                    WHERE
                                                    im.is_active = 1 AND
                                                    im.catalog_id = scc.catalog_id AND
                                                    cm.id = scc.catalog_id AND
                                                    cm.is_featured = 0 AND
                                                    scc.sub_category_id = ? AND
                                                    isnull(im.original_img) AND
                                                    isnull(im.display_img) AND
                                                    (MATCH(im.search_category) AGAINST("' . $search_category . '") OR 
                                                    MATCH(im.search_category) AGAINST(REPLACE(concat("' . $search_category . '"," ")," ","* ") IN BOOLEAN MODE))
                                                    ORDER BY search_text DESC,im.updated_at DESC LIMIT ?, ?', [$this->sub_category_id, $this->offset, $this->item_count]);
                } else {
                    $catalog_list = [];
                    $search_result = [];
                }

                $total_row = isset($total_row) ? $total_row : 0;
                $is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;
                $search_result = array('total_record' => $total_row, 'is_next_page' => $is_next_page, 'catalog_list' => $catalog_list, 'content_list' => $search_result);

                $redis_result = array('result' => $search_result, 'code' => $code, 'message' => $message);
                return $redis_result;

            }

            $response = Response::json(array('code' => $redis_result['code'], 'message' => $redis_result['message'], 'cause' => '', 'data' => $redis_result['result']));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            Log::error("searchNormalImagesBySubCategoryId_v2 : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'search content.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }



    //get all samples without pagination

    /**
     * @api {post} getAllSamplesWithWebp   getAllSamplesWithWebp
     * @apiName getAllSamplesWithWebp
     * @apiGroup User
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {//all parameters are compulsory
     * "catalog_id": 398,
     * "sub_category_id": 97
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Samples fetched successfully.",
     * "cause": "",
     * "data": {
     * "total_record": 4,
     * "result": [
     * {
     * "json_id": 3387,
     * "sample_image": "http://192.168.0.113/photo_editor_lab_backend/image_bucket/webp_original/5c6f7d03b31ef_json_image_1550810371.webp",
     * "is_free": 1,
     * "is_featured": 1,
     * "is_portrait": 0,
     * "height": 100,
     * "width": 320,
     * "search_category": "banners",
     * "original_img_height": 100,
     * "original_img_width": 320,
     * "updated_at": "2019-03-01 04:59:31"
     * },
     * {
     * "json_id": 3388,
     * "sample_image": "http://192.168.0.113/photo_editor_lab_backend/image_bucket/webp_original/5c6f7e1599291_json_image_1550810645.webp",
     * "is_free": 1,
     * "is_featured": 1,
     * "is_portrait": 0,
     * "height": 408,
     * "width": 528,
     * "search_category": "Brochures",
     * "original_img_height": 816,
     * "original_img_width": 1056,
     * "updated_at": "2019-03-01 04:59:04"
     * }
     * ]
     * }
     * }
     */
    public function getAllSamplesWithWebp(Request $request_body)
    {

        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('sub_category_id', 'catalog_id'), $request)) != '')
                return $response;

            $this->sub_category_id = $request->sub_category_id;
            $this->catalog_id = $request->catalog_id;
            $is_cache_enable = isset($request->is_cache_enable) ? $request->is_cache_enable : 1;

            if ($is_cache_enable) {
                $redis_result = Cache::remember("getAllSamplesWithWebp:$this->sub_category_id:$this->catalog_id", Config::get('constant.CACHE_TIME_6_HOUR'), function () {

                    if ($this->catalog_id == 0) {

                        $result = DB::select('SELECT
                                                  id as json_id,
                                                  IF(attribute1 != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",attribute1),"") as sample_image,
                                                  is_free,
                                                  is_featured,
                                                  is_portrait,
                                                  coalesce(height,0) AS height,
                                                  coalesce(width,0) AS width,
                                                  SUBSTRING_INDEX(SUBSTRING_INDEX(search_category, "#", -1), ",", 1) AS search_category,
                                                  original_img_height,
                                                  original_img_width,
                                                  updated_at
                                                FROM
                                                  images
                                                WHERE
                                                  catalog_id in(select catalog_id FROM sub_category_catalog WHERE sub_category_id = ? AND is_active = 1 AND is_featured = 1) AND
                                                  is_featured = 1 AND
                                                  is_active = 1
                                                ORDER BY updated_at DESC', [$this->sub_category_id]);

                    } else {

                        $result = DB::select('SELECT
                                                   id as json_id,
                                                   IF(attribute1 != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",attribute1),"") as sample_image,
                                                   is_free,
                                                   is_featured,
                                                   is_portrait,
                                                   coalesce(height,0) AS height,
                                                   coalesce(width,0) AS width,
                                                   SUBSTRING_INDEX(SUBSTRING_INDEX(search_category, "#", -1), ",", 1) AS search_category,
                                                   original_img_height,
                                                   original_img_width,
                                                   updated_at
                                                  FROM
                                                    images
                                                  WHERE
                                                    catalog_id = ? AND 
                                                    is_active = 1
                                                  ORDER BY updated_at DESC', [$this->catalog_id]);

                    }

                    return array('total_record' => count($result), 'result' => $result);

                });

            } else {

                if ($this->catalog_id == 0) {

                    $result = DB::select('SELECT
                                                  id as json_id,
                                                  IF(attribute1 != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",attribute1),"") as sample_image,
                                                  is_free,
                                                  is_featured,
                                                  is_portrait,
                                                  coalesce(height,0) AS height,
                                                  coalesce(width,0) AS width,
                                                  SUBSTRING_INDEX(SUBSTRING_INDEX(search_category, "#", -1), ",", 1) AS search_category,
                                                  original_img_height,
                                                  original_img_width,
                                                  updated_at
                                                FROM
                                                  images
                                                WHERE
                                                  catalog_id in(select catalog_id FROM sub_category_catalog WHERE sub_category_id = ? AND is_featured = 1) AND
                                                  is_featured = 1
                                                ORDER BY updated_at DESC', [$this->sub_category_id]);

                } else {

                    $result = DB::select('SELECT
                                                   id as json_id,
                                                   IF(attribute1 != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",attribute1),"") as sample_image,
                                                   is_free,
                                                   is_featured,
                                                   is_portrait,
                                                   coalesce(height,0) AS height,
                                                   coalesce(width,0) AS width,
                                                   SUBSTRING_INDEX(SUBSTRING_INDEX(search_category, "#", -1), ",", 1) AS search_category,
                                                   original_img_height,
                                                   original_img_width,
                                                   updated_at
                                                  FROM
                                                    images
                                                  WHERE
                                                    catalog_id = ?
                                                  ORDER BY updated_at DESC', [$this->catalog_id]);

                }

                $redis_result = array('total_record' => count($result), 'result' => $result);

            }

            $response = Response::json(array('code' => 200, 'message' => 'Samples fetched successfully.', 'cause' => '', 'data' => $redis_result));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch
        (Exception $e) {
            Log::error("getAllSamplesWithWebp : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get samples.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    //get catalogs by sub_category_id with pagination

    /**
     * @api {post} getCatalogBySubCategoryIdWithWebp   getCatalogBySubCategoryIdWithWebp
     * @apiName getCatalogBySubCategoryIdWithWebp
     * @apiGroup User
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "sub_category_id":97, //compulsory
     * "page":1, //compulsory
     * "item_count":2 //compulsory
     * "is_free":1 //optional, 1=free & 0=paid
     * "is_featured":1 //optional, 1=featured(Ex: get template categories) & 0=normal(Ex: et sticker categories)
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Catalogs fetched successfully.",
     * "cause": "",
     * "data": {
     * "total_record": 21,
     * "is_next_page": true,
     * "result": [
     * {
     * "catalog_id": 498,
     * "name": "Party",
     * "webp_thumbnail_img": "http://192.168.0.113/photo_editor_lab_backend/image_bucket/webp_thumbnail/5c7914a7c12f5_catalog_img_1551439015.webp",
     * "webp_original_img": "http://192.168.0.113/photo_editor_lab_backend/image_bucket/webp_original/5c7914a7c12f5_catalog_img_1551439015.webp",
     * "is_featured": 1,
     * "is_free": 1,
     * "updated_at": "2019-03-01 11:16:56"
     * },
     * {
     * "catalog_id": 497,
     * "name": "Offer & Sales",
     * "webp_thumbnail_img": "http://192.168.0.113/photo_editor_lab_backend/image_bucket/webp_thumbnail/5c7912eee5c0e_catalog_img_1551438574.webp",
     * "webp_original_img": "http://192.168.0.113/photo_editor_lab_backend/image_bucket/webp_original/5c7912eee5c0e_catalog_img_1551438574.webp",
     * "is_featured": 1,
     * "is_free": 1,
     * "updated_at": "2019-03-01 11:09:35"
     * }
     * ]
     * }
     * }
     */
    public function getCatalogBySubCategoryIdWithWebp(Request $request_body)
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
            $this->offset = ($this->page - 1) * $this->item_count;
            $this->is_free = isset($request->is_free) ? ' AND ct.is_free = ' . $request->is_free : '';
            $this->is_featured = isset($request->is_featured) ? ' AND ct.is_featured = ' . $request->is_featured : '';
            $is_cache_enable = isset($request->is_cache_enable) ? $request->is_cache_enable : 1;

            if ($is_cache_enable) {
                $redis_result = Cache::remember("getCatalogBySubCategoryIdWithWebp:$this->sub_category_id:$this->page:$this->item_count:$this->is_free:$this->is_featured", Config::get('constant.CACHE_TIME_6_HOUR'), function () {

                    $total_row_result = DB::select('SELECT
                                                      count(ct.id) as total
                                                    FROM
                                                      catalog_master as ct,
                                                      sub_category_catalog as sct
                                                    WHERE
                                                      sct.sub_category_id = ? AND
                                                      sct.catalog_id = ct.id AND
                                                      sct.is_active = 1 ' . $this->is_free . ' ' . $this->is_featured . '
                                                    ORDER BY ct.updated_at DESC', [$this->sub_category_id]);

                    $total_row = $total_row_result[0]->total;

                    $catalog_list = DB::select('SELECT
                                          ct.id as catalog_id,
                                          ct.name,
                                          IF(ct.attribute1 != "",CONCAT("' . Config::get('constant.WEBP_THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.attribute1),"") as webp_thumbnail_img,
                                          IF(ct.attribute1 != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.attribute1),"") as webp_original_img,
                                          ct.is_featured,
                                          ct.is_free,
                                          ct.updated_at
                                        FROM
                                          catalog_master as ct,
                                          sub_category_catalog as sct
                                        WHERE
                                          sct.sub_category_id = ? AND
                                          sct.catalog_id = ct.id AND
                                          sct.is_active = 1 ' . $this->is_free . ' ' . $this->is_featured . '
                                        ORDER BY ct.updated_at DESC LIMIT ?, ?', [$this->sub_category_id, $this->offset, $this->item_count]);

                    $is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;
                    return array('total_record' => $total_row, 'is_next_page' => $is_next_page, 'result' => $catalog_list);

                });

            } else {

                $total_row_result = DB::select('SELECT
                                                      count(ct.id) as total
                                                    FROM
                                                      catalog_master as ct,
                                                      sub_category_catalog as sct
                                                    WHERE
                                                      sct.sub_category_id = ? AND
                                                      sct.catalog_id = ct.id AND
                                                      sct.is_active = 1 ' . $this->is_free . ' ' . $this->is_featured . '
                                                    ORDER BY ct.updated_at DESC', [$this->sub_category_id]);

                $total_row = $total_row_result[0]->total;

                $catalog_list = DB::select('SELECT
                                          ct.id as catalog_id,
                                          ct.name,
                                          IF(ct.attribute1 != "",CONCAT("' . Config::get('constant.WEBP_THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.attribute1),"") as webp_thumbnail_img,
                                          IF(ct.attribute1 != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.attribute1),"") as webp_original_img,
                                          ct.is_featured,
                                          ct.is_free,
                                          ct.updated_at
                                        FROM
                                          catalog_master as ct,
                                          sub_category_catalog as sct
                                        WHERE
                                          sct.sub_category_id = ? AND
                                          sct.catalog_id = ct.id AND
                                          sct.is_active = 1 ' . $this->is_free . ' ' . $this->is_featured . '
                                        ORDER BY ct.updated_at DESC LIMIT ?, ?', [$this->sub_category_id, $this->offset, $this->item_count]);

                $is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;
                $redis_result = array('total_record' => $total_row, 'is_next_page' => $is_next_page, 'result' => $catalog_list);

            }

            $response = Response::json(array('code' => 200, 'message' => 'Catalogs fetched successfully.', 'cause' => '', 'data' => $redis_result));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            Log::error("getCatalogBySubCategoryIdWithWebp : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get catalogs.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    //This API is used for Brochure Maker (iOS & Android)

    /**
     * @api {post} getFeaturedSamplesWithCatalogs   getFeaturedSamplesWithCatalogs
     * @apiName getFeaturedSamplesWithCatalogs
     * @apiGroup User
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "sub_category_id":97, //compulsory
     * "catalog_id":0, //compulsory, pass 0 if you don't have catalog_id(for 1st API call)
     * "page":1, //compulsory
     * "item_count":2 //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "All featured cards are fetched successfully.",
     * "cause": "",
     * "data": {
     * "total_record": 50,
     * "is_next_page": true,
     * "category_list": [
     * {
     * "catalog_id": 646,
     * "name": "Pinal",
     * "is_featured": 1,
     * "updated_at": "2019-07-17 08:17:49"
     * },
     * {
     * "catalog_id": 642,
     * "name": "Isha",
     * "is_featured": 1,
     * "updated_at": "2019-06-26 11:15:01"
     * }
     * ],
     * "sample_cards": [
     * {
     * "json_id": 12057,
     * "sample_image": "http://192.168.0.113/photo_editor_lab_backend/image_bucket/webp_original/5d2edd42ddb44_json_image_1563352386.webp",
     * "is_free": 1,
     * "is_featured": 1,
     * "is_portrait": 0,
     * "height": 408,
     * "width": 528,
     * "original_img_height": 816,
     * "original_img_width": 1056,
     * "updated_at": "2019-07-17 08:34:04"
     * }
     * ]
     * }
     * }
     */
    public function getFeaturedSamplesWithCatalogs(Request $request_body)
    {

        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('sub_category_id', 'catalog_id', 'page', 'item_count'), $request)) != '')
                return $response;

            $this->sub_category_id = $request->sub_category_id;
            $this->catalog_id = $request->catalog_id;
            $this->page = $request->page;
            $this->item_count = $request->item_count;
            $this->offset = ($this->page - 1) * $this->item_count;
            $is_cache_enable = isset($request->is_cache_enable) ? $request->is_cache_enable : 1;

            if ($is_cache_enable) {
                $redis_result = Cache::remember("getFeaturedSamplesWithCatalogs:$this->sub_category_id:$this->catalog_id:$this->page:$this->item_count", Config::get('constant.CACHE_TIME_6_HOUR'), function () {

                    if ($this->catalog_id == 0) {

                        if ($this->page == 1) {
                            $category_list = DB::select('SELECT
                                          ct.id as catalog_id,
                                          ct.name,
                                          ct.is_featured,
                                          ct.updated_at
                                        FROM
                                          catalog_master as ct,
                                          sub_category_catalog as sct
                                        WHERE
                                          sct.sub_category_id = ? AND
                                          sct.catalog_id = ct.id AND
                                          sct.is_active = 1 AND
                                          ct.is_featured = 1
                                        ORDER BY ct.updated_at DESC', [$this->sub_category_id]);
                        } else {
                            $category_list = [];
                        }

                        $total_cards = DB::select('SELECT
                                                      COUNT(*) AS total
                                                    FROM
                                                      images as i,
                                                      sub_category_catalog as sct,
                                                      catalog_master as ct
                                                    WHERE
                                                      sct.sub_category_id = ? AND
                                                      sct.catalog_id = i.catalog_id AND
                                                      sct.catalog_id = ct.id AND
                                                      sct.is_active = ? AND
                                                      ct.is_featured = ? AND
                                                      i.is_featured = ?', [$this->sub_category_id, 1, 1, 1]);

                        $total_row = $total_cards[0]->total;

                        $sample_cards = DB::select('SELECT
                                                        i.id as json_id,
                                                        IF(i.attribute1 != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",i.attribute1),"") as sample_image,
                                                        i.is_free,
                                                        i.is_featured,
                                                        i.is_portrait,
                                                        coalesce(i.height,0) AS height,
                                                        coalesce(i.width,0) AS width,
                                                        coalesce(i.original_img_height,0) AS original_img_height,
                                                        coalesce(i.original_img_width,0) AS original_img_width,
                                                        i.updated_at
                                                        FROM
                                                        images as i,
                                                        sub_category_catalog as sct,
                                                        catalog_master as ct
                                                        WHERE
                                                        sct.sub_category_id = ? AND
                                                        sct.catalog_id = i.catalog_id AND
                                                        sct.catalog_id = ct.id AND
                                                        sct.is_active = 1 AND
                                                        ct.is_featured = 1 AND
                                                        i.is_featured = 1
                                                        ORDER BY i.updated_at DESC LIMIT ?, ?', [$this->sub_category_id, $this->offset, $this->item_count]);

                    } else {
                        $category_list = [];
                        $total_cards = DB::select('SELECT
                                                        COUNT(*) AS total
                                                      FROM
                                                        images
                                                      WHERE
                                                        catalog_id = ?', [$this->catalog_id]);


                        $total_row = $total_cards[0]->total;

                        $sample_cards = DB::select('SELECT
                                                        id as json_id,
                                                        IF(attribute1 != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",attribute1),"") as sample_image,
                                                        is_free,
                                                        is_featured,
                                                        is_portrait,
                                                        coalesce(height,0) AS height,
                                                        coalesce(width,0) AS width,
                                                        coalesce(original_img_height,0) AS original_img_height,
                                                        coalesce(original_img_width,0) AS original_img_width,
                                                        updated_at
                                                      FROM
                                                        images
                                                      WHERE
                                                         catalog_id = ?
                                                      ORDER BY updated_at DESC LIMIT ?, ?', [$this->catalog_id, $this->offset, $this->item_count]);
                    }

                    $is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;

                    $result_array = array(
                        'total_record' => $total_row,
                        'is_next_page' => $is_next_page,
                        'category_list' => $category_list,
                        'sample_cards' => $sample_cards
                    );
                    Log::info("getFeaturedSamplesWithCatalogs WITHOUT CACHE", [count($category_list)]);
                    return $result_array;
                });
                Log::info("getFeaturedSamplesWithCatalogs CACHE RETURN", [count($redis_result["category_list"])]);
            } else {
                if ($this->catalog_id == 0) {

                    if ($this->page == 1) {
                        $category_list = DB::select('SELECT
                                          ct.id as catalog_id,
                                          ct.name,
                                          ct.is_featured,
                                          ct.updated_at
                                        FROM
                                          catalog_master as ct,
                                          sub_category_catalog as sct
                                        WHERE
                                          sct.sub_category_id = ? AND
                                          sct.catalog_id = ct.id AND
                                          sct.is_active = 1 AND
                                          ct.is_featured = 1
                                        ORDER BY ct.updated_at DESC', [$this->sub_category_id]);
                    } else {
                        $category_list = [];
                    }

                    $total_cards = DB::select('SELECT
                                                      COUNT(*) AS total
                                                    FROM
                                                      images as i,
                                                      sub_category_catalog as sct,
                                                      catalog_master as ct
                                                    WHERE
                                                      sct.sub_category_id = ? AND
                                                      sct.catalog_id = i.catalog_id AND
                                                      sct.catalog_id = ct.id AND
                                                      sct.is_active = ? AND
                                                      ct.is_featured = ? AND
                                                      i.is_featured = ?', [$this->sub_category_id, 1, 1, 1]);

                    $total_row = $total_cards[0]->total;

                    $sample_cards = DB::select('SELECT
                                                        i.id as json_id,
                                                        IF(i.attribute1 != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",i.attribute1),"") as sample_image,
                                                        i.is_free,
                                                        i.is_featured,
                                                        i.is_portrait,
                                                        coalesce(i.height,0) AS height,
                                                        coalesce(i.width,0) AS width,
                                                        coalesce(i.original_img_height,0) AS original_img_height,
                                                        coalesce(i.original_img_width,0) AS original_img_width,
                                                        i.updated_at
                                                        FROM
                                                        images as i,
                                                        sub_category_catalog as sct,
                                                        catalog_master as ct
                                                        WHERE
                                                        sct.sub_category_id = ? AND
                                                        sct.catalog_id = i.catalog_id AND
                                                        sct.catalog_id = ct.id AND
                                                        sct.is_active = 1 AND
                                                        ct.is_featured = 1 AND
                                                        i.is_featured = 1
                                                        ORDER BY i.updated_at DESC LIMIT ?, ?', [$this->sub_category_id, $this->offset, $this->item_count]);

                } else {
                    $category_list = [];
                    $total_cards = DB::select('SELECT
                                                        COUNT(*) AS total
                                                      FROM
                                                        images
                                                      WHERE
                                                        catalog_id = ?', [$this->catalog_id]);


                    $total_row = $total_cards[0]->total;

                    $sample_cards = DB::select('SELECT
                                                        id as json_id,
                                                        IF(attribute1 != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",attribute1),"") as sample_image,
                                                        is_free,
                                                        is_featured,
                                                        is_portrait,
                                                        coalesce(height,0) AS height,
                                                        coalesce(width,0) AS width,
                                                        coalesce(original_img_height,0) AS original_img_height,
                                                        coalesce(original_img_width,0) AS original_img_width,
                                                        updated_at
                                                      FROM
                                                        images
                                                      WHERE
                                                         catalog_id = ?
                                                      ORDER BY updated_at DESC LIMIT ?, ?', [$this->catalog_id, $this->offset, $this->item_count]);
                }

                $is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;

                $redis_result = array(
                    'total_record' => $total_row,
                    'is_next_page' => $is_next_page,
                    'category_list' => $category_list,
                    'sample_cards' => $sample_cards
                );
            }

            $response = Response::json(array('code' => 200, 'message' => 'All featured cards are fetched successfully.', 'cause' => '', 'data' => $redis_result));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));


        } catch
        (Exception $e) {
            Log::error("getFeaturedSamplesWithCatalogs : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get featured cards.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    /**
     * @api {post} getTemplatesBySubCategoryTags   getTemplatesBySubCategoryTags
     * @apiName getTemplatesBySubCategoryTags
     * @apiGroup User
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "sub_category_id":66, //compulsory
     * "category_name":"Business", //optional on 1st API call for home screen
     * "page":1, //compulsory
     * "item_count":2 //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Templates fetched successfully.",
     * "cause": "",
     * "data": {
     * "total_record": 23,
     * "is_next_page": true,
     * "category_list": [
     * {
     * "sub_category_tag_id": 1,
     * "tag_name": "Business Banner"
     * }
     * ],
     * "template_list": [
     * {
     * "json_id": 10669,
     * "sample_image": "http:\/\/192.168.0.113\/photo_editor_lab_backend\/image_bucket\/webp_original\/5d11bf76d377d_json_image_1561444214.webp",
     * "is_free": 1,
     * "is_featured": 0,
     * "is_portrait": 1,
     * "height": 400,
     * "width": 325,
     * "search_category": "flyers,symbol,vector,illustration,label,design,desktop,discount,image,card,wholesale,vectors,christmas,sale,celebration,price,sign,decoration,banner,business,stock",
     * "original_img_height": 800,
     * "original_img_width": 650,
     * "updated_at": "2019-06-25 06:40:08",
     * "search_text": 5.565430641174316
     * },
     * {
     * "json_id": 10663,
     * "sample_image": "http:\/\/192.168.0.113\/photo_editor_lab_backend\/image_bucket\/webp_original\/5d11bebf04cca_json_image_1561444031.webp",
     * "is_free": 1,
     * "is_featured": 0,
     * "is_portrait": 0,
     * "height": 400,
     * "width": 325,
     * "search_category": "flyers,no person,karaoke,retro,graphic design,music,isolated,classic,microphone,conceptual,bright,business,equipment,creativity,electronics,invention,achievement,illuminated,contemporary,glazed,rock",
     * "original_img_height": 800,
     * "original_img_width": 650,
     * "updated_at": "2019-06-25 06:36:12",
     * "search_text": 1.8949640989303589
     * }
     * ]
     * }
     * }
     */
    public function getTemplatesBySubCategoryTags(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('sub_category_id', 'page', 'item_count'), $request)) != '')
                return $response;

            $this->sub_category_id = $request->sub_category_id;
            $this->category_name = isset($request->category_name) ? strtolower(trim($request->category_name)) : '';
            $this->page = $request->page;
            $this->item_count = $request->item_count;
            $this->offset = ($this->page - 1) * $this->item_count;
            $this->is_cache_enable = isset($request->is_cache_enable) ? $request->is_cache_enable : 1;

            if ($this->is_cache_enable) {

                $redis_result = Cache::remember("getTemplatesBySubCategoryTags:$this->sub_category_id:$this->category_name:$this->page:$this->item_count", Config::get("constant.CACHE_TIME_6_HOUR"), function () {

                    $tag_name = $this->category_name;

                    if ($this->page == 1 && $tag_name == "") {
                        $category_list = DB::select('SELECT
                                                          id AS sub_category_tag_id,
                                                          tag_name
                                                    FROM
                                                          sub_category_tag_master
                                                    WHERE 
                                                          sub_category_id = ? AND 
                                                          is_active = ? AND
                                                          is_template = ?
                                                    ORDER BY update_time DESC', [$this->sub_category_id, 1, 1]);

                        $tag_name = (count($category_list) > 0) ? $category_list[0]->tag_name : 'Test';

                    } else {
                        $category_list = [];
                    }
                    $final_tag_list = array();
                    foreach ($category_list as $key) {

                        $total_row_result = DB::select('SELECT
                                                              count(*) as total
                                                            FROM
                                                              images as im
                                                              JOIN sub_category_catalog AS scc ON im.catalog_id = scc.catalog_id AND scc.sub_category_id = ?
                                                              JOIN catalog_master AS ctm ON ctm.id = scc.catalog_id AND ctm.is_featured = 1
                                                            WHERE
                                                              im.is_active = 1 AND
                                                              isnull(im.original_img) AND
                                                              isnull(im.display_img) AND
                                                              (MATCH(im.search_category) AGAINST("' . $key->tag_name . '") OR
                                                                MATCH(im.search_category) AGAINST(REPLACE(concat("' . $key->tag_name . '"," ")," ","* ")  IN BOOLEAN MODE))', [$this->sub_category_id]);

                        $total_row = $total_row_result[0]->total;

                        if ($total_row > 0) {
                            $final_tag_list[] = $key;
                            $tag_name = $final_tag_list[0]->tag_name;
                        }

                    }

                    $total_row_result = DB::select('SELECT
                                                        count(*) AS total
                                                      FROM
                                                        images as im
                                                        JOIN sub_category_catalog AS scc ON im.catalog_id = scc.catalog_id AND scc.sub_category_id = ?
                                                        JOIN catalog_master AS ctm ON ctm.id = scc.catalog_id AND ctm.is_featured = 1
                                                      WHERE
                                                        im.is_active = 1 AND
                                                        isnull(im.original_img) AND
                                                        isnull(im.display_img) AND
                                                        (MATCH(im.search_category) AGAINST("' . $tag_name . '") OR
                                                          MATCH(im.search_category) AGAINST(REPLACE(concat("' . $tag_name . '"," ")," ","* ")  IN BOOLEAN MODE))
                                                        ', [$this->sub_category_id]);

                    $total_row = $total_row_result[0]->total;

                    $search_result = DB::select('SELECT
                                                  im.id as json_id,
                                                  IF(im.attribute1 != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",im.attribute1),"") as sample_image,
                                                  im.is_free,
                                                  im.is_featured,
                                                  im.is_portrait,
                                                  coalesce(im.height,0) AS height,
                                                  coalesce(im.width,0) AS width,
                                                  coalesce(im.search_category,"") AS search_category,
                                                  coalesce(im.original_img_height) AS original_img_height,
                                                  coalesce(im.original_img_width) AS original_img_width,
                                                  COALESCE(multiple_images,"") AS multiple_images,
                                                  COALESCE(json_pages_sequence,"") AS pages_sequence,
                                                  COALESCE(LENGTH(json_pages_sequence) - LENGTH(REPLACE(json_pages_sequence, ",","")) + 1,1) AS total_pages,
                                                  im.updated_at,
                                                  MATCH(im.search_category) AGAINST("' . $tag_name . '") +
                                                  MATCH(im.search_category) AGAINST(REPLACE(concat("' . $tag_name . '"," ")," ","* ")  IN BOOLEAN MODE) AS search_text
                                                FROM
                                                  images as im
                                                  JOIN sub_category_catalog AS scc ON im.catalog_id = scc.catalog_id AND scc.sub_category_id = ?
                                                  JOIN catalog_master AS ctm ON ctm.id = scc.catalog_id AND ctm.is_featured = ?
                                                WHERE
                                                  im.is_active = ? AND
                                                  isnull(im.original_img) AND
                                                  isnull(im.display_img) AND
                                                  (MATCH(im.search_category) AGAINST("' . $tag_name . '") OR
                                                    MATCH(im.search_category) AGAINST(REPLACE(concat("' . $tag_name . '"," ")," ","* ")  IN BOOLEAN MODE)) 
                                                ORDER BY search_text DESC,im.updated_at DESC LIMIT ?, ?', [$this->sub_category_id, 1, 1, $this->offset, $this->item_count]);

                    $code = 200;
                    $message = "Templates fetched successfully.";

                    $is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;
                    $search_result = array(
                        'total_record' => $total_row,
                        'is_next_page' => $is_next_page,
                        'category_list' => $final_tag_list,
                        'template_list' => $search_result
                    );

                    $result = array('result' => $search_result, 'code' => $code, 'message' => $message);
                    return $result;

                });

            } else {

                $tag_name = $this->category_name;

                if ($this->page == 1 && $tag_name == "") {
                    $category_list = DB::select('SELECT
                                                          id AS sub_category_tag_id,
                                                          tag_name
                                                    FROM
                                                          sub_category_tag_master
                                                    WHERE 
                                                          sub_category_id = ? AND 
                                                          is_active = ? AND
                                                          is_template = ?
                                                    ORDER BY update_time DESC', [$this->sub_category_id, 1, 1]);

                    $tag_name = (count($category_list) > 0) ? $category_list[0]->tag_name : 'Test';

                } else {
                    $category_list = [];
                }
                $final_tag_list = array();
                foreach ($category_list as $key) {

                    $total_row_result = DB::select('SELECT
                                                              count(*) as total
                                                            FROM
                                                              images as im
                                                              JOIN sub_category_catalog AS scc ON im.catalog_id = scc.catalog_id AND scc.sub_category_id = ?
                                                              JOIN catalog_master AS ctm ON ctm.id = scc.catalog_id AND ctm.is_featured = 1
                                                            WHERE
                                                              im.is_active = 1 AND
                                                              isnull(im.original_img) AND
                                                              isnull(im.display_img) AND
                                                              (MATCH(im.search_category) AGAINST("' . $key->tag_name . '") OR
                                                                MATCH(im.search_category) AGAINST(REPLACE(concat("' . $key->tag_name . '"," ")," ","* ")  IN BOOLEAN MODE))', [$this->sub_category_id]);

                    $total_row = $total_row_result[0]->total;

                    if ($total_row > 0) {
                        $final_tag_list[] = $key;
                        $tag_name = $final_tag_list[0]->tag_name;
                    }

                }

                $total_row_result = DB::select('SELECT
                                                        count(*) AS total
                                                      FROM
                                                        images as im
                                                        JOIN sub_category_catalog AS scc ON im.catalog_id = scc.catalog_id AND scc.sub_category_id = ?
                                                        JOIN catalog_master AS ctm ON ctm.id = scc.catalog_id AND ctm.is_featured = 1
                                                      WHERE
                                                        im.is_active = 1 AND
                                                        isnull(im.original_img) AND
                                                        isnull(im.display_img) AND
                                                        (MATCH(im.search_category) AGAINST("' . $tag_name . '") OR
                                                          MATCH(im.search_category) AGAINST(REPLACE(concat("' . $tag_name . '"," ")," ","* ")  IN BOOLEAN MODE))
                                                        ', [$this->sub_category_id]);

                $total_row = $total_row_result[0]->total;

                $search_result = DB::select('SELECT
                                                  im.id as json_id,
                                                  IF(im.attribute1 != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",im.attribute1),"") as sample_image,
                                                  im.is_free,
                                                  im.is_featured,
                                                  im.is_portrait,
                                                  coalesce(im.height,0) AS height,
                                                  coalesce(im.width,0) AS width,
                                                  coalesce(im.search_category,"") AS search_category,
                                                  coalesce(im.original_img_height) AS original_img_height,
                                                  coalesce(im.original_img_width) AS original_img_width,
                                                  COALESCE(multiple_images,"") AS multiple_images,
                                                  COALESCE(json_pages_sequence,"") AS pages_sequence,
                                                  COALESCE(LENGTH(json_pages_sequence) - LENGTH(REPLACE(json_pages_sequence, ",","")) + 1,1) AS total_pages,
                                                  im.updated_at,
                                                  MATCH(im.search_category) AGAINST("' . $tag_name . '") +
                                                  MATCH(im.search_category) AGAINST(REPLACE(concat("' . $tag_name . '"," ")," ","* ")  IN BOOLEAN MODE) AS search_text
                                                FROM
                                                  images as im
                                                  JOIN sub_category_catalog AS scc ON im.catalog_id = scc.catalog_id AND scc.sub_category_id = ?
                                                  JOIN catalog_master AS ctm ON ctm.id = scc.catalog_id AND ctm.is_featured = ?
                                                WHERE
                                                  im.is_active = ? AND
                                                  isnull(im.original_img) AND
                                                  isnull(im.display_img) AND
                                                  (MATCH(im.search_category) AGAINST("' . $tag_name . '") OR
                                                    MATCH(im.search_category) AGAINST(REPLACE(concat("' . $tag_name . '"," ")," ","* ")  IN BOOLEAN MODE)) 
                                                ORDER BY search_text DESC,im.updated_at DESC LIMIT ?, ?', [$this->sub_category_id, 1, 1, $this->offset, $this->item_count]);

                $code = 200;
                $message = "Templates fetched successfully.";

                $is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;
                $search_result = array(
                    'total_record' => $total_row,
                    'is_next_page' => $is_next_page,
                    'category_list' => $final_tag_list,
                    'template_list' => $search_result
                );

                $redis_result = array('result' => $search_result, 'code' => $code, 'message' => $message);

            }

            $response = Response::json(array('code' => $redis_result['code'], 'message' => $redis_result['message'], 'cause' => '', 'data' => $redis_result['result']));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            Log::error("getTemplatesBySubCategoryTags : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get templates.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    public function getTemplatesBySubCategoryTags_v2(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('sub_category_id', 'page', 'item_count'), $request)) != '')
                return $response;

            $this->sub_category_id = $request->sub_category_id;
            $this->category_name = isset($request->category_name) ? strtolower(trim($request->category_name)) : '';
            $this->page = $request->page;
            $this->item_count = $request->item_count;
            $this->offset = ($this->page - 1) * $this->item_count;
            $is_cache_enable = isset($request->is_cache_enable) ? $request->is_cache_enable : 1;

            if($is_cache_enable){
                $redis_result = Cache::remember("getTemplatesBySubCategoryTags_v2:$this->sub_category_id:$this->category_name:$this->page:$this->item_count", Config::get("constant.CACHE_TIME_6_HOUR"), function () {

                    $this->tag_name = $this->category_name;

                    if ($this->page == 1 && $this->tag_name == "") {
                        $category_list = DB::select('SELECT
                                                      id AS sub_category_tag_id,
                                                      tag_name
                                                FROM
                                                      sub_category_tag_master
                                                WHERE 
                                                      sub_category_id = ? AND 
                                                      is_active = ? AND
                                                      is_template = ?
                                                ORDER BY update_time DESC', [$this->sub_category_id, 1, 1]);

                        $this->tag_name = (count($category_list) > 0) ? $category_list[0]->tag_name : 'Test';

                    } else {
                        $category_list = [];
                    }

                    $final_tag_list = array();
                    foreach ($category_list as $key) {

                        $total_row_result = DB::select('SELECT
                                                          COUNT(*) AS total
                                                    FROM
                                                          images AS im
                                                          JOIN sub_category_catalog AS scc ON im.catalog_id = scc.catalog_id AND scc.sub_category_id = ?
                                                          JOIN catalog_master AS ctm ON ctm.id = scc.catalog_id AND ctm.is_featured = ?
                                                    WHERE
                                                          im.is_active = 1 AND
                                                          isnull(im.original_img) AND
                                                          isnull(im.display_img) AND
                                                          (MATCH(im.search_category) AGAINST ("' . $key->tag_name . '") OR
                                                          MATCH(im.search_category) AGAINST (REPLACE(CONCAT("' . $key->tag_name . '"," ")," ","* ") IN BOOLEAN MODE))', [$this->sub_category_id, 1]);

                        $total_row = $total_row_result[0]->total;

                        if ($total_row > 0) {
                            $final_tag_list[] = $key;
                            $this->tag_name = $final_tag_list[0]->tag_name;
                        }

                    }

                    $total_row = Cache::remember("getTemplatesBySubCategoryTags_v2:$this->sub_category_id:$this->tag_name", Config::get("constant.CACHE_TIME_6_HOUR"), function () {
                        $total_row_result = DB::select('SELECT
                                                        count(*) AS total
                                                     FROM
                                                        images as im
                                                        JOIN sub_category_catalog AS scc ON im.catalog_id = scc.catalog_id AND scc.sub_category_id = ?
                                                        JOIN catalog_master AS ctm ON ctm.id = scc.catalog_id AND ctm.is_featured = ?
                                                     WHERE
                                                        im.is_active = 1 AND
                                                        isnull(im.original_img) AND
                                                        isnull(im.display_img) AND
                                                        (MATCH(im.search_category) AGAINST("' . $this->tag_name . '") OR
                                                          MATCH(im.search_category) AGAINST(REPLACE(concat("' . $this->tag_name . '"," ")," ","* ")  IN BOOLEAN MODE))
                                                        ', [$this->sub_category_id, 1]);
                        return $total_row_result[0]->total;
                    });

                    $search_result = DB::select('SELECT
                                                im.id AS json_id,
                                                IF(im.attribute1 != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",im.attribute1),"") AS sample_image,
                                                im.is_free,
                                                im.is_featured,
                                                im.is_portrait,
                                                COALESCE(im.height,0) AS height,
                                                COALESCE(im.width,0) AS width,
                                                COALESCE(im.search_category,"") AS search_category,
                                                COALESCE(im.original_img_height) AS original_img_height,
                                                COALESCE(im.original_img_width) AS original_img_width,
                                                COALESCE(im.multiple_images,"") AS multiple_images,
                                                COALESCE(im.json_pages_sequence,"") AS pages_sequence,
                                                COALESCE(LENGTH(im.json_pages_sequence) - LENGTH(REPLACE(json_pages_sequence, ",","")) + 1,1) AS total_pages,
                                                im.updated_at,
                                                MATCH(im.search_category) AGAINST("' . $this->tag_name . '") +
                                                MATCH(im.search_category) AGAINST(REPLACE(concat("' . $this->tag_name . '"," ")," ","* ")  IN BOOLEAN MODE) AS search_text
                                            FROM
                                                  images AS im
                                                  JOIN sub_category_catalog AS scc ON im.catalog_id = scc.catalog_id AND scc.sub_category_id = ?
                                                  JOIN catalog_master AS ctm ON ctm.id = scc.catalog_id AND ctm.is_featured = ?
                                            WHERE
                                                  im.is_active = ? AND
                                                  ISNULL(im.original_img) AND
                                                  ISNULL(im.display_img) AND
                                                  (MATCH(im.search_category) AGAINST("' . $this->tag_name . '") OR
                                                  MATCH(im.search_category) AGAINST(REPLACE(concat("' . $this->tag_name . '"," ")," ","* ")  IN BOOLEAN MODE)) 
                                            ORDER BY search_text DESC,im.updated_at DESC LIMIT ?, ?', [$this->sub_category_id, 1, 1, $this->offset, $this->item_count]);

                    $code = 200;
                    $message = "Templates fetched successfully.";

                    $is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;
                    $search_result = array(
                        'total_record' => $total_row,
                        'is_next_page' => $is_next_page,
                        'category_list' => $final_tag_list,
                        'template_list' => $search_result
                    );

                    $result = array('result' => $search_result, 'code' => $code, 'message' => $message);
                    return $result;

                });

            }else{
                    $this->tag_name = $this->category_name;

                    if ($this->page == 1 && $this->tag_name == "") {
                        $category_list = DB::select('SELECT
                                                      id AS sub_category_tag_id,
                                                      tag_name
                                                FROM
                                                      sub_category_tag_master
                                                WHERE 
                                                      sub_category_id = ? AND 
                                                      is_active = ? AND
                                                      is_template = ?
                                                ORDER BY update_time DESC', [$this->sub_category_id, 1, 1]);

                        $this->tag_name = (count($category_list) > 0) ? $category_list[0]->tag_name : 'Test';

                    } else {
                        $category_list = [];
                    }

                    $final_tag_list = array();
                    foreach ($category_list as $key) {

                        $total_row_result = DB::select('SELECT
                                                          COUNT(*) AS total
                                                    FROM
                                                          images AS im
                                                          JOIN sub_category_catalog AS scc ON im.catalog_id = scc.catalog_id AND scc.sub_category_id = ?
                                                          JOIN catalog_master AS ctm ON ctm.id = scc.catalog_id AND ctm.is_featured = ?
                                                    WHERE
                                                          im.is_active = 1 AND
                                                          isnull(im.original_img) AND
                                                          isnull(im.display_img) AND
                                                          (MATCH(im.search_category) AGAINST ("' . $key->tag_name . '") OR
                                                          MATCH(im.search_category) AGAINST (REPLACE(CONCAT("' . $key->tag_name . '"," ")," ","* ") IN BOOLEAN MODE))', [$this->sub_category_id, 1]);

                        $total_row = $total_row_result[0]->total;

                        if ($total_row > 0) {
                            $final_tag_list[] = $key;
                            $this->tag_name = $final_tag_list[0]->tag_name;
                        }

                    }

                        $total_row_result = DB::select('SELECT
                                                        count(*) AS total
                                                     FROM
                                                        images as im
                                                        JOIN sub_category_catalog AS scc ON im.catalog_id = scc.catalog_id AND scc.sub_category_id = ?
                                                        JOIN catalog_master AS ctm ON ctm.id = scc.catalog_id AND ctm.is_featured = ?
                                                     WHERE
                                                        im.is_active = 1 AND
                                                        isnull(im.original_img) AND
                                                        isnull(im.display_img) AND
                                                        (MATCH(im.search_category) AGAINST("' . $this->tag_name . '") OR
                                                          MATCH(im.search_category) AGAINST(REPLACE(concat("' . $this->tag_name . '"," ")," ","* ")  IN BOOLEAN MODE))
                                                        ', [$this->sub_category_id, 1]);

                        $total_row = $total_row_result[0]->total;


                    $search_result = DB::select('SELECT
                                                im.id AS json_id,
                                                IF(im.attribute1 != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",im.attribute1),"") AS sample_image,
                                                im.is_free,
                                                im.is_featured,
                                                im.is_portrait,
                                                COALESCE(im.height,0) AS height,
                                                COALESCE(im.width,0) AS width,
                                                COALESCE(im.search_category,"") AS search_category,
                                                COALESCE(im.original_img_height) AS original_img_height,
                                                COALESCE(im.original_img_width) AS original_img_width,
                                                im.updated_at,
                                                COALESCE(im.multiple_images,"") AS multiple_images,
                                                COALESCE(im.json_pages_sequence,"") AS pages_sequence,
                                                COALESCE(LENGTH(im.json_pages_sequence) - LENGTH(REPLACE(json_pages_sequence, ",","")) + 1,1) AS total_pages,
                                                MATCH(im.search_category) AGAINST("' . $this->tag_name . '") +
                                                MATCH(im.search_category) AGAINST(REPLACE(concat("' . $this->tag_name . '"," ")," ","* ")  IN BOOLEAN MODE) AS search_text
                                            FROM
                                                  images AS im
                                                  JOIN sub_category_catalog AS scc ON im.catalog_id = scc.catalog_id AND scc.sub_category_id = ?
                                                  JOIN catalog_master AS ctm ON ctm.id = scc.catalog_id AND ctm.is_featured = ?
                                            WHERE
                                                  im.is_active = ? AND
                                                  ISNULL(im.original_img) AND
                                                  ISNULL(im.display_img) AND
                                                  (MATCH(im.search_category) AGAINST("' . $this->tag_name . '") OR
                                                  MATCH(im.search_category) AGAINST(REPLACE(concat("' . $this->tag_name . '"," ")," ","* ")  IN BOOLEAN MODE)) 
                                            ORDER BY search_text DESC,im.updated_at DESC LIMIT ?, ?', [$this->sub_category_id, 1, 1, $this->offset, $this->item_count]);

                    $code = 200;
                    $message = "Templates fetched successfully.";

                    $is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;
                    $search_result = array(
                        'total_record' => $total_row_result[0]->total,
                        'is_next_page' => $is_next_page,
                        'category_list' => $final_tag_list,
                        'template_list' => $search_result
                    );

                 $redis_result = array('result' => $search_result, 'code' => $code, 'message' => $message);

            }

            $response = Response::json(array('code' => $redis_result['code'], 'message' => $redis_result['message'], 'cause' => '', 'data' => $redis_result['result']));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            Log::error("getTemplatesBySubCategoryTags_v2 : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get templates.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    public function getTemplatesBySubCategoryTagsV3(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('sub_category_id', 'page', 'item_count'), $request)) != '')
                return $response;

            $this->sub_category_id = $request->sub_category_id;
            $this->category_name = isset($request->category_name) ? strtolower(trim($request->category_name)) : '';
            $this->page = $request->page;
            $this->item_count = $request->item_count;
            $this->offset = ($this->page - 1) * $this->item_count;

            $redis_result = Cache::rememberforever("getTemplatesBySubCategoryTagsV3:$this->sub_category_id:$this->category_name:$this->page:$this->item_count", function () {

                $this->tag_name = $this->category_name;

                if ($this->page == 1 && $this->tag_name == "") {
                    DB::statement("SET sql_mode = '' ");
                    $category_list = DB::select('SELECT
                                                      id AS sub_category_tag_id,
                                                      tag_name
                                                FROM
                                                      sub_category_tag_master
                                                WHERE 
                                                      sub_category_id IN (' . $this->sub_category_id . ') AND 
                                                      is_active = ? AND
                                                      is_template = ?
                                                GROUP BY tag_name
                                                ORDER BY update_time DESC', [1, 1]);

                    $this->tag_name = (count($category_list) > 0) ? $category_list[0]->tag_name : 'Test';

                } else {
                    $category_list = [];
                }

                $final_tag_list = array();
                foreach ($category_list as $key) {

                    $total_row_result = DB::select('SELECT
                                                          COUNT(*) AS total
                                                    FROM
                                                          images AS im
                                                          JOIN sub_category_catalog AS scc ON im.catalog_id = scc.catalog_id AND scc.sub_category_id IN (' . $this->sub_category_id . ')
                                                          JOIN catalog_master AS ctm ON ctm.id = scc.catalog_id AND ctm.is_featured = ?
                                                    WHERE
                                                          im.is_active = 1 AND
                                                          ISNULL(im.original_img) AND
                                                          ISNULL(im.display_img) AND
                                                          (MATCH(im.search_category) AGAINST ("' . $key->tag_name . '") OR
                                                          MATCH(im.search_category) AGAINST (REPLACE(CONCAT("' . $key->tag_name . '"," ")," ","* ") IN BOOLEAN MODE))', [1]);

                    $total_row = $total_row_result[0]->total;

                    if ($total_row > 0) {
                        $final_tag_list[] = $key;
                        $this->tag_name = $final_tag_list[0]->tag_name;
                    }

                }

                $total_row = Cache::rememberforever("getTemplatesBySubCategoryTagsV3:$this->sub_category_id:$this->tag_name", function () {
                    $total_row_result = DB::select('SELECT
                                                        COUNT(*) AS total
                                                     FROM
                                                        images AS im
                                                        JOIN sub_category_catalog AS scc ON im.catalog_id = scc.catalog_id AND scc.sub_category_id IN (' . $this->sub_category_id . ')
                                                        JOIN catalog_master AS ctm ON ctm.id = scc.catalog_id AND ctm.is_featured = ?
                                                     WHERE
                                                        im.is_active = 1 AND
                                                        ISNULL(im.original_img) AND
                                                        ISNULL(im.display_img) AND
                                                        (MATCH(im.search_category) AGAINST ("' . $this->tag_name . '") OR
                                                          MATCH(im.search_category) AGAINST (REPLACE(CONCAT("' . $this->tag_name . '"," ")," ","* ")  IN BOOLEAN MODE))
                                                        ', [1]);
                    return $total_row_result[0]->total;
                });

                $search_result = DB::select('SELECT
                                                im.id AS json_id,
                                                IF(im.attribute1 != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",im.attribute1),"") AS sample_image,
                                                im.is_free,
                                                im.is_featured,
                                                im.is_portrait,
                                                COALESCE(im.height,0) AS height,
                                                COALESCE(im.width,0) AS width,
                                                COALESCE(im.search_category,"") AS search_category,
                                                COALESCE(im.original_img_height) AS original_img_height,
                                                COALESCE(im.original_img_width) AS original_img_width,
                                                im.updated_at,
                                                MATCH(im.search_category) AGAINST ("' . $this->tag_name . '") +
                                                MATCH(im.search_category) AGAINST (REPLACE(CONCAT("' . $this->tag_name . '"," ")," ","* ")  IN BOOLEAN MODE) AS search_text
                                            FROM
                                                  images AS im
                                                  JOIN sub_category_catalog AS scc ON im.catalog_id = scc.catalog_id AND scc.sub_category_id IN (' . $this->sub_category_id . ')
                                                  JOIN catalog_master AS ctm ON ctm.id = scc.catalog_id AND ctm.is_featured = ?
                                            WHERE
                                                  im.is_active = ? AND
                                                  ISNULL(im.original_img) AND
                                                  ISNULL(im.display_img) AND
                                                  (MATCH(im.search_category) AGAINST ("' . $this->tag_name . '") OR
                                                  MATCH(im.search_category) AGAINST (REPLACE(CONCAT("' . $this->tag_name . '"," ")," ","* ")  IN BOOLEAN MODE)) 
                                            ORDER BY search_text DESC, im.updated_at DESC LIMIT ?, ?', [1, 1, $this->offset, $this->item_count]);

                $code = 200;
                $message = "Templates fetched successfully.";

                $is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;
                $search_result = array(
                    'total_record' => $total_row,
                    'is_next_page' => $is_next_page,
                    'category_list' => $final_tag_list,
                    'template_list' => $search_result
                );

                $result = array('result' => $search_result, 'code' => $code, 'message' => $message);
                return $result;

            });

            $response = Response::json(array('code' => $redis_result['code'], 'message' => $redis_result['message'], 'cause' => '', 'data' => $redis_result['result']));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            Log::error("getTemplatesBySubCategoryTagsV3 : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get templates.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    //This API is used to get featured cards when developer has not contain catalog_id) or samples by catalog_id

    /**
     * @api {post} getTemplateWithCatalogs   getTemplateWithCatalogs
     * @apiName getTemplateWithCatalogs
     * @apiGroup User
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "sub_category_id":1, //compulsory
     * "catalog_id":1, //compulsory, pass 0 if you don't have catalog_id(in this case you get all featured cards) otherwise you have to pass specific catalog_id
     * "is_get_data_for_1st_catalog":1,//optional,pass 1 if you want sample images of 1st catalog
     * "page":1, //compulsory
     * "item_count":2 //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "All templates are fetched successfully.",
     * "cause": "",
     * "data": {
     * "total_record": 128,
     * "is_next_page": true,
     * "category_list": [
     * {
     * "catalog_id": 168,
     * "name": "Business Card Catalog2",
     * "thumbnail_img": "http://192.168.0.113/ob_photolab_backend/image_bucket/thumbnail/5a1d0851d6d32_catalog_img_1511852113.png",
     * "compressed_img": "http://192.168.0.113/ob_photolab_backend/image_bucket/compressed/5a1d0851d6d32_catalog_img_1511852113.png",
     * "original_img": "http://192.168.0.113/ob_photolab_backend/image_bucket/original/5a1d0851d6d32_catalog_img_1511852113.png",
     * "is_free": 1,
     * "is_featured": 1,
     * "updated_at": "2018-06-21 12:04:36"
     * },
     * {
     * "catalog_id": 259,
     * "name": "pavan",
     * "thumbnail_img": "http://192.168.0.113/videoflyer_backend/image_bucket/thumbnail/5ce7a0ab5fbdc_catalog_img_1558683819.jpg",
     * "compressed_img": "http://192.168.0.113/videoflyer_backend/image_bucket/compressed/5ce7a0ab5fbdc_catalog_img_1558683819.jpg",
     * "original_img": "http://192.168.0.113/videoflyer_backend/image_bucket/original/5ce7a0ab5fbdc_catalog_img_1558683819.jpg",
     * "is_free": 1,
     * "is_featured": 1,
     * "updated_at": "2019-06-17 03:33:07"
     * }
     * ],
     * "sample_cards": [
     * {
     * "json_id": 3326,
     * "sample_image": "http://192.168.0.113/photo_editor_lab_backend/image_bucket/webp_original/5c6d3523a43b5_json_image_1550660899.webp",
     * "is_free": 1,
     * "is_featured": 0,
     * "is_portrait": 0,
     * "height": 256,
     * "width": 512,
     * "search_category": "Twitter",
     * "original_img_height": 512,
     * "original_img_width": 1024,
     * "updated_at": "2019-02-20 11:08:20"
     * }
     * ]
     * }
     * }
     */
    public function getTemplateWithCatalogs(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('sub_category_id', 'catalog_id', 'page', 'item_count'), $request)) != '')
                return $response;

            $this->sub_category_id = $request->sub_category_id;
            $this->catalog_id = $request->catalog_id;
            $this->is_get_data_for_1st_catalog = isset($request->is_get_data_for_1st_catalog) ? $request->is_get_data_for_1st_catalog : 0;
            $this->page = $request->page;
            $this->item_count = $request->item_count;
            $this->offset = ($this->page - 1) * $this->item_count;
            $is_cache_enable = isset($request->is_cache_enable) ? $request->is_cache_enable : 1;

            if ($is_cache_enable) {

                $redis_result = Cache::remember("getTemplateWithCatalogs:$this->sub_category_id:$this->catalog_id:$this->page:$this->item_count:$this->is_get_data_for_1st_catalog", Config::get('constant.CACHE_TIME_6_HOUR'), function () {

                    $host_name = request()->getHttpHost(); // With port if there is. Eg: mydomain.com:81
                    $certificate_maker_host_name = Config::get('constant.HOST_NAME_OF_CERTIFICATE_MAKER');

                    $image_url = ($host_name == $certificate_maker_host_name && $this->sub_category_id == 4) ? 'IF(i.image != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",i.image),"") as sample_image,' : 'IF(i.attribute1 != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",i.attribute1),"") as sample_image,';

                    if ($this->catalog_id == 0) {

                        $category_list = DB::select('SELECT
                                          ct.id AS catalog_id,
                                          ct.name,
                                          IF(ct.image != "",CONCAT("' . Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.image),"") AS thumbnail_img,
                                          IF(ct.image != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.image),"") AS compressed_img,
                                          IF(ct.image != "",CONCAT("' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.image),"") AS original_img,
                                          IF(ct.icon != "",CONCAT("' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.icon),"") AS icon,
                                          ct.is_free,
                                          ct.is_featured,
                                          ct.updated_at
                                        FROM
                                          catalog_master AS ct,
                                          sub_category_catalog AS sct
                                        WHERE
                                          sct.sub_category_id = ? AND
                                          sct.catalog_id = ct.id AND
                                          sct.is_active = 1 AND
                                          ct.is_featured = 1
                                        ORDER BY ct.updated_at DESC', [$this->sub_category_id]);

                        if ($this->is_get_data_for_1st_catalog == 1) {
                            $total_cards = DB::select('SELECT COUNT(*) as total FROM images WHERE catalog_id = ? AND is_active = 1', [$category_list[0]->catalog_id]);
                            $total_row = $total_cards[0]->total;

                            $sample_cards = DB::select('SELECT
                                                  i.id AS json_id,
                                                  ' . $image_url . '
                                                  i.is_free,
                                                  i.is_featured,
                                                  i.is_portrait,
                                                  IF(i.cover_webp_img != "", CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '", i.cover_webp_img), "") AS cover_webp_img,
                                                  COALESCE(i.cover_img_height,0) AS cover_img_height,
                                                  COALESCE(i.cover_img_width,0) AS cover_img_width,
                                                  COALESCE(i.original_cover_img_height,0) AS original_cover_img_height,
                                                  COALESCE(i.original_cover_img_width,0) AS original_cover_img_width,
                                                  i.search_category,
                                                  i.template_name,
                                                  COALESCE(i.height,0) AS height,
                                                  COALESCE(i.width,0) AS width,
                                                  COALESCE(i.original_img_height,0) AS original_img_height,
                                                  COALESCE(i.original_img_width,0) AS original_img_width,
                                                  COALESCE(i.search_category,"") AS search_category,
                                                  COALESCE(i.multiple_images,"") AS multiple_images,
                                                  COALESCE(i.json_pages_sequence,"") AS pages_sequence,
                                                  COALESCE(LENGTH(i.json_pages_sequence) - LENGTH(REPLACE(i.json_pages_sequence, ",","")) + 1,1) AS total_pages,
                                                  i.updated_at
                                                  FROM
                                                    images as i
                                                  WHERE
                                                    i.catalog_id = ? AND
                                                    i.is_active = 1
                                                  ORDER BY updated_at DESC LIMIT ?, ?', [$category_list[0]->catalog_id, $this->offset, $this->item_count]);

                            $is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;

                            $result_array = array(
                                'total_record' => $total_row,
                                'is_next_page' => $is_next_page,
                                'category_list' => $category_list,
                                'sample_cards' => $sample_cards
                            );

                        } else {

                            $total_cards = DB::select('SELECT COUNT(*) AS total
                                                            FROM images
                                                            WHERE catalog_id IN (SELECT catalog_id
                                                                                 FROM sub_category_catalog
                                                                                 WHERE sub_category_id = ?) AND is_featured = 1 AND is_active = 1', [$this->sub_category_id]);


                            $total_row = $total_cards[0]->total;
                            $sample_cards = DB::select('SELECT
                                                            i.id as json_id,
                                                             ' . $image_url . '
                                                            i.is_free,
                                                            i.is_featured,
                                                            i.is_portrait,
                                                            IF(i.cover_webp_img != "", CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '", i.cover_webp_img), "") AS cover_webp_img,
                                                            COALESCE(i.cover_img_height,0) AS cover_img_height,
                                                            COALESCE(i.cover_img_width,0) AS cover_img_width,
                                                            COALESCE(i.original_cover_img_height,0) AS original_cover_img_height,
                                                            COALESCE(i.original_cover_img_width,0) AS original_cover_img_width,
                                                            i.search_category,
                                                            i.template_name,
                                                            COALESCE(i.height,0) AS height,
                                                            COALESCE(i.width,0) AS width,
                                                            COALESCE(i.original_img_height,0) AS original_img_height,
                                                            COALESCE(i.original_img_width,0) AS original_img_width,
                                                            COALESCE(i.search_category,"") AS search_category,
                                                            COALESCE(i.multiple_images,"") AS multiple_images,
                                                            COALESCE(i.json_pages_sequence,"") AS pages_sequence,
                                                            COALESCE(LENGTH(i.json_pages_sequence) - LENGTH(REPLACE(i.json_pages_sequence, ",","")) + 1,1) AS total_pages,
                                                            i.updated_at
                                                            FROM
                                                            images as i,
                                                            sub_category_catalog as sct,
                                                            catalog_master as ct
                                                            WHERE
                                                            sct.sub_category_id = ? AND
                                                            sct.catalog_id = i.catalog_id AND
                                                            sct.catalog_id = ct.id AND
                                                            sct.is_active = 1 AND
                                                            ct.is_featured = 1 AND
                                                            i.is_featured = 1 AND
                                                            i.is_active = 1
                                                            ORDER BY updated_at DESC LIMIT ?, ?', [$this->sub_category_id, $this->offset, $this->item_count]);

                            $is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;

                            $result_array = array(
                                'total_record' => $total_row,
                                'is_next_page' => $is_next_page,
                                'category_list' => $category_list,
                                'sample_cards' => $sample_cards
                            );
                        }

                    } else {
                        $total_cards = DB::select('SELECT COUNT(*) as total FROM images WHERE catalog_id = ? AND is_active = 1', [$this->catalog_id]);
                        $total_row = $total_cards[0]->total;

                        $sample_cards = DB::select('SELECT
                                                  i.id AS json_id,
                                                  ' . $image_url . '
                                                  i.is_free,
                                                  i.is_featured,
                                                  i.is_portrait,
                                                  IF(i.cover_webp_img != "", CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '", i.cover_webp_img), "") AS cover_webp_img,
                                                  COALESCE(i.cover_img_height,0) AS cover_img_height,
                                                  COALESCE(i.cover_img_width,0) AS cover_img_width,
                                                  COALESCE(i.original_cover_img_height,0) AS original_cover_img_height,
                                                  COALESCE(i.original_cover_img_width,0) AS original_cover_img_width,
                                                  i.search_category,
                                                  i.template_name,
                                                  COALESCE(i.height,0) AS height,
                                                  COALESCE(i.width,0) AS width,
                                                  COALESCE(i.original_img_height,0) AS original_img_height,
                                                  COALESCE(i.original_img_width,0) AS original_img_width,
                                                  COALESCE(i.search_category,"") AS search_category,
                                                  COALESCE(i.multiple_images,"") AS multiple_images,
                                                  COALESCE(i.json_pages_sequence,"") AS pages_sequence,
                                                  COALESCE(LENGTH(i.json_pages_sequence) - LENGTH(REPLACE(i.json_pages_sequence, ",","")) + 1,1) AS total_pages,
                                                  i.updated_at
                                                  FROM
                                                    images AS i
                                                  WHERE
                                                    i.catalog_id = ? AND
                                                    i.is_active = 1
                                                  ORDER BY updated_at DESC LIMIT ?, ?', [$this->catalog_id, $this->offset, $this->item_count]);

                        $is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;

                        $result_array = array(
                            'total_record' => $total_row,
                            'is_next_page' => $is_next_page,
                            'category_list' => [],
                            'sample_cards' => $sample_cards
                        );
                    }
                    $result_array['prefix_url'] = Config::get('constant.AWS_BUCKET_PATH_PHOTO_EDITOR_LAB') . '/';

                    return $result_array;
                });

            } else {

                $host_name = request()->getHttpHost(); // With port if there is. Eg: mydomain.com:81
                $certificate_maker_host_name = Config::get('constant.HOST_NAME_OF_CERTIFICATE_MAKER');

                $image_url = ($host_name == $certificate_maker_host_name && $this->sub_category_id == 4) ? 'IF(i.image != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",i.image),"") as sample_image,' : 'IF(i.attribute1 != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",i.attribute1),"") as sample_image,';

                if ($this->catalog_id == 0) {

                    $category_list = DB::select('SELECT
                                          ct.id AS catalog_id,
                                          ct.name,
                                          IF(ct.image != "",CONCAT("' . Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.image),"") AS thumbnail_img,
                                          IF(ct.image != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.image),"") AS compressed_img,
                                          IF(ct.image != "",CONCAT("' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.image),"") AS original_img,
                                          IF(ct.icon != "",CONCAT("' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.icon),"") AS icon,
                                          ct.is_free,
                                          ct.is_featured,
                                          ct.updated_at
                                        FROM
                                          catalog_master AS ct,
                                          sub_category_catalog AS sct
                                        WHERE
                                          sct.sub_category_id = ? AND
                                          sct.catalog_id = ct.id AND
                                          sct.is_active = 1 AND
                                          ct.is_featured = 1
                                        ORDER BY ct.updated_at DESC', [$this->sub_category_id]);

                    if ($this->is_get_data_for_1st_catalog == 1) {
                        $total_cards = DB::select('SELECT COUNT(*) as total FROM images WHERE catalog_id = ?', [$category_list[0]->catalog_id]);
                        $total_row = $total_cards[0]->total;

                        $sample_cards = DB::select('SELECT
                                                  i.id AS json_id,
                                                  ' . $image_url . '
                                                  i.is_free,
                                                  i.is_featured,
                                                  i.is_portrait,
                                                  IF(i.cover_webp_img != "", CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '", i.cover_webp_img), "") AS cover_webp_img,
                                                  COALESCE(i.cover_img_height,0) AS cover_img_height,
                                                  COALESCE(i.cover_img_width,0) AS cover_img_width,
                                                  COALESCE(i.original_cover_img_height,0) AS original_cover_img_height,
                                                  COALESCE(i.original_cover_img_width,0) AS original_cover_img_width,
                                                  i.search_category,
                                                  i.template_name,                                                  
                                                  COALESCE(i.height,0) AS height,
                                                  COALESCE(i.width,0) AS width,
                                                  COALESCE(i.original_img_height,0) AS original_img_height,
                                                  COALESCE(i.original_img_width,0) AS original_img_width,
                                                  COALESCE(i.search_category,"") AS search_category,
                                                  COALESCE(i.multiple_images,"") AS multiple_images,
                                                  COALESCE(i.json_pages_sequence,"") AS pages_sequence,
                                                  COALESCE(LENGTH(i.json_pages_sequence) - LENGTH(REPLACE(i.json_pages_sequence, ",","")) + 1,1) AS total_pages,
                                                  i.updated_at
                                                  FROM
                                                    images as i
                                                  WHERE
                                                    i.catalog_id = ?
                                                  ORDER BY updated_at DESC LIMIT ?, ?', [$category_list[0]->catalog_id, $this->offset, $this->item_count]);

                        $is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;

                        $result_array = array(
                            'total_record' => $total_row,
                            'is_next_page' => $is_next_page,
                            'category_list' => $category_list,
                            'sample_cards' => $sample_cards
                        );

                    } else {

                        $total_cards = DB::select('SELECT COUNT(*) AS total
                                                            FROM images
                                                            WHERE catalog_id IN (SELECT catalog_id
                                                                                 FROM sub_category_catalog
                                                                                 WHERE sub_category_id = ?) AND is_featured = 1', [$this->sub_category_id]);


                        $total_row = $total_cards[0]->total;
                        $sample_cards = DB::select('SELECT
                                                            i.id as json_id,
                                                             ' . $image_url . '
                                                            i.is_free,
                                                            i.is_featured,
                                                            i.is_portrait,
                                                            IF(i.cover_webp_img != "", CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '", i.cover_webp_img), "") AS cover_webp_img,
                                                            COALESCE(i.cover_img_height,0) AS cover_img_height,
                                                            COALESCE(i.cover_img_width,0) AS cover_img_width,
                                                            COALESCE(i.original_cover_img_height,0) AS original_cover_img_height,
                                                            COALESCE(i.original_cover_img_width,0) AS original_cover_img_width,
                                                            i.search_category,
                                                            i.template_name,                                                            
                                                            COALESCE(i.height,0) AS height,
                                                            COALESCE(i.width,0) AS width,
                                                            COALESCE(i.original_img_height,0) AS original_img_height,
                                                            COALESCE(i.original_img_width,0) AS original_img_width,
                                                            COALESCE(i.search_category,"") AS search_category,
                                                            COALESCE(i.multiple_images,"") AS multiple_images,
                                                            COALESCE(i.json_pages_sequence,"") AS pages_sequence,
                                                            COALESCE(LENGTH(i.json_pages_sequence) - LENGTH(REPLACE(i.json_pages_sequence, ",","")) + 1,1) AS total_pages,
                                                            i.updated_at
                                                            FROM
                                                            images as i,
                                                            sub_category_catalog as sct,
                                                            catalog_master as ct
                                                            WHERE
                                                            sct.sub_category_id = ? AND
                                                            sct.catalog_id = i.catalog_id AND
                                                            sct.catalog_id = ct.id AND
                                                            sct.is_active = 1 AND
                                                            ct.is_featured = 1 AND
                                                            i.is_featured = 1
                                                            ORDER BY updated_at DESC LIMIT ?, ?', [$this->sub_category_id, $this->offset, $this->item_count]);

                        $is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;

                        $result_array = array(
                            'total_record' => $total_row,
                            'is_next_page' => $is_next_page,
                            'category_list' => $category_list,
                            'sample_cards' => $sample_cards
                        );
                    }

                } else {
                    $total_cards = DB::select('SELECT COUNT(*) as total FROM images WHERE catalog_id = ?', [$this->catalog_id]);
                    $total_row = $total_cards[0]->total;

                    $sample_cards = DB::select('SELECT
                                                  i.id AS json_id,
                                                  ' . $image_url . '
                                                  i.is_free,
                                                  i.is_featured,
                                                  i.is_portrait,
                                                  IF(i.cover_webp_img != "", CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '", i.cover_webp_img), "") AS cover_webp_img,
                                                  COALESCE(i.cover_img_height,0) AS cover_img_height,
                                                  COALESCE(i.cover_img_width,0) AS cover_img_width,
                                                  COALESCE(i.original_cover_img_height,0) AS original_cover_img_height,
                                                  COALESCE(i.original_cover_img_width,0) AS original_cover_img_width,
                                                  i.search_category,
                                                  i.template_name,                                                  
                                                  COALESCE(i.height,0) AS height,
                                                  COALESCE(i.width,0) AS width,
                                                  COALESCE(i.original_img_height,0) AS original_img_height,
                                                  COALESCE(i.original_img_width,0) AS original_img_width,
                                                  COALESCE(i.search_category,"") AS search_category,
                                                  COALESCE(i.multiple_images,"") AS multiple_images,
                                                  COALESCE(i.json_pages_sequence,"") AS pages_sequence,
                                                  COALESCE(LENGTH(i.json_pages_sequence) - LENGTH(REPLACE(i.json_pages_sequence, ",","")) + 1,1) AS total_pages,
                                                  i.updated_at
                                                  FROM
                                                    images AS i
                                                  WHERE
                                                    i.catalog_id = ?
                                                  ORDER BY updated_at DESC LIMIT ?, ?', [$this->catalog_id, $this->offset, $this->item_count]);

                    $is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;

                    $result_array = array(
                        'total_record' => $total_row,
                        'is_next_page' => $is_next_page,
                        'category_list' => [],
                        'sample_cards' => $sample_cards
                    );
                }
                $result_array['prefix_url'] = Config::get('constant.AWS_BUCKET_PATH_PHOTO_EDITOR_LAB') . '/';

                $redis_result = $result_array;

            }

            $response = Response::json(array('code' => 200, 'message' => 'All templates are fetched successfully.', 'cause' => '', 'data' => $redis_result));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));


        } catch (Exception $e) {
            Log::error("getTemplateWithCatalogs : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get templates.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
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
            Log::error("saveUserFeeds : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
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
            $this->base_url = (new Utils())->getBaseUrl();

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
            Log::error("getUserFeedsBySubCategoryId : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
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

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            //Log::info("request data :", [$request]);
            if (($response = (new VerificationController())->validateRequiredParameter(array('user_feeds_id'), $request)) != '')
                return $response;

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
            Log::error("deleteUserFeeds : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
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

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            //Log::info("request data :", [$request]);
            if (($response = (new VerificationController())->validateRequiredParameter(array('sub_category_id'), $request)) != '')
                return $response;

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
            DB::delete('DELETE FROM user_feeds_master WHERE sub_category_id = ? ', [$sub_category_id]);
            DB::commit();

            $response = Response::json(array('code' => 200, 'message' => 'All images deleted successfully.', 'cause' => '', 'data' => json_decode('{}')));
        } catch (Exception $e) {
            Log::error("deleteAllUserFeeds : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
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

            } else {
                $response = Response::json(array('code' => 201, 'message' => 'Invalid request parameter.', 'cause' => '', 'data' => json_decode("{}")));

            }
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch
        (Exception $e) {
            Log::error("getDeletedCatalogId : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
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

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('sub_category_id', 'platform'), $request)) != '')
                return $response;

            $this->sub_category_id = $request->sub_category_id;
            $this->platform = $request->platform;

            if (!Cache::has("pel:getLink$this->platform:$this->sub_category_id")) {
                $result = Cache::rememberforever("getLink$this->platform:$this->sub_category_id", function () {

                    $total_row_result = DB::select('SELECT COUNT(*) as total FROM  advertise_links as adl, sub_category_advertise_links as sadl WHERE adl.platform = ? AND sadl.advertise_link_id=adl.id AND sadl.sub_category_id = ? AND sadl.is_active = ?', [$this->platform, $this->sub_category_id, 1]);
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

                    return array('total_record' => $total_row, 'link_list' => $result);
                });
            }

            $redis_result = Cache::get("getLink$this->platform:$this->sub_category_id");

            if (!$redis_result) {
                $redis_result = [];
            }

            $response = Response::json(array('code' => 200, 'message' => 'Advertise link fetched successfully.', 'cause' => '', 'data' => $redis_result));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            Log::error("getLink : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get Link.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
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
            Log::error("verifyPromoCode : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
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

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('sub_category_id', 'device_platform'), $request)) != '')
                return $response;

            $this->sub_category_id = $request->sub_category_id;
            $this->device_platform = $request->device_platform;

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
            Log::error("getAdvertiseServerIdForUser : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get advertise server id.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
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
            if (($response = (new VerificationController())->validateRequiredParameter(array('sub_category_id', 'platform'), $request)) != '')
                return $response;

            $this->sub_category_id = $request->sub_category_id;
            $this->platform = $request->platform;

            if (!Cache::has("pel:getLinkWithoutToken$this->platform->$this->sub_category_id")) {
                $result = Cache::rememberforever("getLinkWithoutToken$this->platform->$this->sub_category_id", function () {

                    $total_row_result = DB::select('SELECT COUNT(*) as total FROM  advertise_links as adl, sub_category_advertise_links as sadl WHERE adl.platform = ? AND sadl.advertise_link_id=adl.id AND sadl.sub_category_id = ? AND sadl.is_active = ?', [$this->platform, $this->sub_category_id, 1]);
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

                    return array('total_record' => $total_row, 'link_list' => $result);
                });
            }

            $redis_result = Cache::get("getLinkWithoutToken$this->platform->$this->sub_category_id");

            if (!$redis_result) {
                $redis_result = [];
            }

            $response = Response::json(array('code' => 200, 'message' => 'Advertise links fetched successfully.', 'cause' => '', 'data' => $redis_result));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            Log::error("getLink : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get Link.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    /*=========================================| Multi provider to job |=========================================*/

    /**
     * @api {post} jobMultiSearchByUser   jobMultiSearchByUser
     * @apiName jobMultiSearchByUser
     * @apiGroup Resume User
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "page":1, //compulsory
     * "description":"Engineering", //compulsory
     * "location":"chicago, il"
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Job fetched successfully.",
     * "cause": "",
     * "data": {
     * "total_record": 104,
     * "total_pages": 3,
     * "is_next_page": true,
     * "result": [
     * {
     * "sourceId": "110671270",
     * "company": "ExxonMobil",
     * "company_logo": "",
     * "company_url": "",
     * "employmentType": "",
     * "location": "",
     * "source": "Careercast",
     * "query": "engineering",
     * "title": "Electrical Engineer",
     * "job_name": "Electrical Engineer",
     * "url": "http://jobs.blackenterprise.com/jobs/electrical-engineer-singapore-01-238510-110671270-d?rsite=careercast&rgroup=1&clientid=blackent&widget=1&type=job&",
     * "created_at": "2018-12-24 09:00:00.000000"
     * },
     * {
     * "sourceId": "109573702",
     * "company": "Georgia Tech Research Institute (GTRI)",
     * "company_logo": "https://secure.adicio.com/squisher.php?u=https%3A%2F%2Fslb.adicio.com%2Ffiles%2Fys-c-01%2F2017-07%2F27%2F13%2F37%2Fweb_597a4f033826597a4f039f910.jpg",
     * "company_url": "/jobs/georgia-tech-research-institute-gtri-1096434-cd",
     * "employmentType": "",
     * "location": "Atlanta, GA",
     * "source": "Ieee",
     * "query": "engineering",
     * "title": "Electronic Warfare and Avionics Software Engineer - ELSYS",
     * "job_name": "Electronic Warfare and Avionics Software Engineer - ELSYS",
     * "url": "https://jobs.ieee.org/jobs/electronic-warfare-and-avionics-software-engineer-elsys-atlanta-ga-109573702-d?widget=1&type=job&",
     * "created_at": "2018-12-24 06:00:00.000000"
     * },
     * {
     * "sourceId": "110122113",
     * "company": "Georgia Tech Research Institute (GTRI)",
     * "company_logo": "https://secure.adicio.com/squisher.php?u=https%3A%2F%2Fslb.adicio.com%2Ffiles%2Fys-c-01%2F2017-07%2F27%2F13%2F37%2Fweb_597a4f033826597a4f039f910.jpg",
     * "company_url": "/jobs/georgia-tech-research-institute-gtri-1096434-cd",
     * "employmentType": "",
     * "location": "Huntsville, AL",
     * "source": "Ieee",
     * "query": "engineering",
     * "title": "Radar Systems Engineer - Huntsville, AL - SEAL",
     * "job_name": "Radar Systems Engineer - Huntsville, AL - SEAL",
     * "url": "https://jobs.ieee.org/jobs/radar-systems-engineer-huntsville-al-seal-huntsville-al-110122113-d?widget=1&type=job&",
     * "created_at": "2018-12-24 06:00:00.000000"
     * },
     * {
     * "sourceId": "110529248",
     * "company": "Georgia Tech Research Institute (GTRI)",
     * "company_logo": "https://secure.adicio.com/squisher.php?u=https%3A%2F%2Fslb.adicio.com%2Ffiles%2Fys-c-01%2F2017-07%2F27%2F13%2F37%2Fweb_597a4f033826597a4f039f910.jpg",
     * "company_url": "/jobs/georgia-tech-research-institute-gtri-1096434-cd",
     * "employmentType": "",
     * "location": "Smyrna, GA",
     * "source": "Ieee",
     * "query": "engineering",
     * "title": "Algorithm Developer - SEAL",
     * "job_name": "Algorithm Developer - SEAL",
     * "url": "https://jobs.ieee.org/jobs/algorithm-developer-seal-smyrna-ga-110529248-d?widget=1&type=job&",
     * "created_at": "2018-12-24 06:00:00.000000"
     * }
     * ],
     * "is_cache": 0
     * }
     * }
     */
    public function jobMultiSearchByUser(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());

            if (($response = (new VerificationController())->validateRequiredParameter(array('description', 'page'), $request)) != '')
                return $response;
            if (($response = (new VerificationController())->validateIssetRequiredParameter(array('location', 'lat', 'long', 'full_time'), $request)) != '')
                return $response;

            $this->description = strtolower($request->description);
            $this->page = $request->page;
            $this->location = isset($request->location) ? $request->location : '';
            $this->is_cache = 1;
            $this->SET_ITEM_COUNT_INTO_PROVIDER = Config::get('constant.SET_ITEM_COUNT_INTO_PROVIDER');

            if (isset($request->location)) {
                $location = explode(',', $this->location);
                $loc_count = count($location);
                if ($loc_count < 2 || $loc_count > 2) {
                    return Response::json(array('code' => 201, 'message' => "Location format should be 'City, State'.", 'cause' => '', 'data' => json_decode("{}")));
                }
            }

            if (!Cache::has("pel:jobMultiSearchByUser:$this->description:$this->location:$this->page")) {
                $result = Cache::remember("jobMultiSearchByUser:$this->description:$this->location:$this->page", 1440, function () {
                    $this->is_cache = 0;
                    $providers = [
                        'Github' => [],
                        'Ieee' => [],
                        'Careercast' => [],
                        'Govt' => [],
                        //'Stackoverflow' => [],
                        //'Dice' => [],
                        //'Jobinventory' => [],
                        //'Monster' => [],
                    ];
//                    Log::info('description', ['description' => $this->description]);
                    $client = new \JobApis\Jobs\Client\JobsMulti($providers);
                    $client->setKeyword($this->description);

                    if ($this->location != '')
                        $client->setLocation($this->location);

                    $options = [
                        'orderBy' => 'datePosted',
                        'order' => 'desc',
                    ];
                    // Get all the jobs
                    $jobs = $client->getAllJobs($options);
                    //$jobs = $client->getJobsByProvider('Github', $options);
                    // Output a summary of jobs as arrays

                    $client_result = [];
                    foreach ($jobs->all() as $job) {
                        $client_result[] = $job->toArray();
                    }
                    $client_result = json_decode(json_encode($client_result));

                    //Log::info(['client_result' => $client_result]);
                    $ob_result = [];
                    $c = 0;
                    foreach ($client_result as $result) {

                        $ob_result[$c]['sourceId'] = $result->sourceId;
                        $ob_result[$c]['company'] = $result->company;
                        $ob_result[$c]['company_logo'] = isset($result->hiringOrganization->logo) ? $result->hiringOrganization->logo : '';
                        $ob_result[$c]['company_url'] = isset($result->hiringOrganization->url) ? $result->hiringOrganization->url : '';
                        $ob_result[$c]['employmentType'] = isset($result->employmentType) ? $result->employmentType : '';
                        $ob_result[$c]['location'] = $result->location;
                        $ob_result[$c]['source'] = $result->source;
                        $ob_result[$c]['query'] = $result->query;
                        $ob_result[$c]['title'] = $result->title;
                        $ob_result[$c]['job_name'] = $result->name;
                        $ob_result[$c]['url'] = $result->url;
                        $ob_result[$c]['created_at'] = $result->datePosted->date;
                        $c = $c + 1;

                    }

                    $item_count = Config::get('constant.PAGINATION_ITEM_COUNT');
                    $offset = ($this->page - 1) * $item_count;
                    $total_job_list = count($ob_result);

                    $this->job_list = array_splice($ob_result, $offset, $item_count);

                    $total_pages = ceil($total_job_list / $item_count);
                    $is_next_page = ($total_job_list > ($offset + $item_count)) ? true : false;

                    return ['total_record' => $total_job_list, 'total_pages' => $total_pages, 'is_next_page' => $is_next_page, 'result' => $this->job_list];
                });
            }
            $redis_result = Cache::get("jobMultiSearchByUser:$this->description:$this->location:$this->page");

            Redis::expire("jobMultiSearchByUser:$this->description:$this->location:$this->page", 1);

            if (!$redis_result['result']) {
                return Response::json(array('code' => 427, 'message' => "Sorry, We couldn't find any job for " . $request->description, 'cause' => '', 'data' => json_decode('{}')));
            }

            $this->is_cache == 0 ? $is_cache = 0 : $is_cache = 1;
            $redis_result['is_cache'] = $is_cache;

            $response = Response::json(array('code' => 200, 'message' => 'Job fetched successfully.', 'cause' => '', 'data' => $redis_result));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch
        (Exception $e) {
            Log::error("jobMultiSearchByUser : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'job search by user.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    /*=========================================| Individually provider to job |=========================================*/

    /**
     * @api {post} jobMultiSearchByUserIndividually jobMultiSearchByUserIndividually
     * @apiName jobMultiSearchByUserIndividually
     * @apiGroup Resume User
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "page":1, //compulsory
     * "description":"Engineering", //compulsory
     * "provider": "Github", //?areercast??ice??ithub??ovt??eee??obinventory??onster??tackoverflow?     * "location":"chicago, il"
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Job fetched successfully.",
     * "cause": "",
     * "data": {
     * "total_record": 1,
     * "result": [
     * {
     * "sourceId": "c307e4ca-d6a6-11e8-8f6e-f00ef74f7cb0",
     * "company": "Squirro",
     * "company_logo": "https://jobs.github.com/rails/active_storage/blobs/eyJfcmFpbHMiOnsibWVzc2FnZSI6IkJBaHBBdFJYIiwiZXhwIjpudWxsLCJwdXIiOiJibG9iX2lkIn19--f57cd599eb0f28cd5bf62d1214102e55ef446841/728d2587-0eff-4cf3-bfc5-26581e0d58c2",
     * "company_url": "https://www.squirro.com",
     * "employmentType": "",
     * "location": "Zurich",
     * "source": "Github",
     * "query": "Engineering",
     * "title": "Senior Python Engineer",
     * "job_name": "Senior Python Engineer",
     * "url": "https://jobs.github.com/positions/c307e4ca-d6a6-11e8-8f6e-f00ef74f7cb0",
     * "created_at": "2018-10-23 09:36:02.000000"
     * }
     * ]
     * }
     * }
     */
    public function jobMultiSearchByUserIndividually(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());

            if (($response = (new VerificationController())->validateRequiredParameter(array('description', 'page', 'provider'), $request)) != '')
                return $response;
            if (($response = (new VerificationController())->validateIssetRequiredParameter(array('location', 'lat', 'long', 'full_time'), $request)) != '')
                return $response;

            $this->description = $request->description;
            $this->page = $request->page;
            $this->provider = $request->provider;
            $this->location = isset($request->location) ? $request->location : '';

            $providers = [
                'Careercast' => [],
                'Dice' => [],
                'Github' => [],
                'Govt' => [],
                'Ieee' => [],
                'Jobinventory' => [],
                //'Monster' => [],
                'Stackoverflow' => [],
            ];
            //Log::info('description', ['description' => $this->description]);
            $client = new \JobApis\Jobs\Client\JobsMulti($providers);
            $client->setKeyword($this->description)
                ->setPage($this->page, 30);

            if ($this->location != '')
                $client->setLocation($this->location);

            $options = [
                'maxResults' => 10,
            ];
            // Get all the jobs
//            $jobs = $client->getAllJobs($options);
            $jobs = $client->getJobsByProvider($this->provider, $options);
            // Output a summary of jobs as arrays
//            return $jobs->getStatusCode();
            $client_result = [];
            foreach ($jobs->all() as $job) {
                $client_result[] = $job->toArray();
            }
            $client_result = json_decode(json_encode($client_result));

            //Log::info(['client_result' => $client_result]);
            $ob_result = [];
            $c = 0;
            foreach ($client_result as $result) {
//                $r[]= $result->company;
                $ob_result[$c]['sourceId'] = $result->sourceId;
                $ob_result[$c]['company'] = $result->company;
                $ob_result[$c]['company_logo'] = isset($result->hiringOrganization->logo) ? $result->hiringOrganization->logo : '';
                $ob_result[$c]['company_url'] = isset($result->hiringOrganization->url) ? $result->hiringOrganization->url : '';
                $ob_result[$c]['employmentType'] = isset($result->employmentType) ? $result->employmentType : '';
                $ob_result[$c]['location'] = $result->location;
                $ob_result[$c]['source'] = $result->source;
                $ob_result[$c]['query'] = $result->query;
                $ob_result[$c]['title'] = $result->title;
                $ob_result[$c]['job_name'] = $result->name;
                $ob_result[$c]['url'] = $result->url;
                $ob_result[$c]['created_at'] = $result->datePosted->date;
//                    $rw[] = $r;
                $c = $c + 1;
            }
            $result = $ob_result;

            $total_row = count($result);
            if ($total_row == 0) {
                return Response::json(array('code' => 427, 'message' => "Sorry, We couldn't find any job for " . $request->description, 'cause' => '', 'data' => json_decode('{}')));
            }
            $response = Response::json(array('code' => 200, 'message' => 'Job fetched successfully.', 'cause' => '', 'data' => ['total_record' => $total_row, 'result' => $result]));
        } catch (Exception $e) {
            Log::error("jobMultiSearchByUserIndividually : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'job search by user.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    /*=========================================| Home page API |=========================================*/

    /**
     * @api {post} getHomePageDetail getHomePageDetail
     * @apiName getHomePageDetail
     * @apiGroup Resume User
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "sub_category_id":1, //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Home page detail fetched successfully.",
     * "cause": "",
     * "data": {
     * "template": [
     * {
     * "json_id": 2488,
     * "sample_image": "http://192.168.0.114/photo_editor_lab_backend/image_bucket/webp_original/5b81064f6a5d3_json_image_1535182415.webp",
     * "is_free": 1,
     * "is_featured": 1,
     * "is_portrait": 0,
     * "height": 300,
     * "width": 525,
     * "updated_at": "2018-10-02 11:05:56"
     * },
     * {
     * "json_id": 711,
     * "sample_image": "http://192.168.0.114/photo_editor_lab_backend/image_bucket/webp_original/5a953eafef82b_json_image_1519730351.png",
     * "is_free": 1,
     * "is_featured": 1,
     * "is_portrait": 0,
     * "height": 300,
     * "width": 525,
     * "updated_at": "2018-10-02 10:48:43"
     * },
     * {
     * "json_id": 3105,
     * "sample_image": "http://192.168.0.114/photo_editor_lab_backend/image_bucket/webp_original/5b9787d634af2_json_image_1536657366.webp",
     * "is_free": 1,
     * "is_featured": 1,
     * "is_portrait": 1,
     * "height": 600,
     * "width": 400,
     * "updated_at": "2018-09-11 09:16:07"
     * },
     * {
     * "json_id": 732,
     * "sample_image": "http://192.168.0.114/photo_editor_lab_backend/image_bucket/webp_original/5a9650b07f43d_json_image_1519800496.webp",
     * "is_free": 1,
     * "is_featured": 1,
     * "is_portrait": 0,
     * "height": 300,
     * "width": 525,
     * "updated_at": "2018-09-05 05:20:37"
     * },
     * {
     * "json_id": 731,
     * "sample_image": "http://192.168.0.114/photo_editor_lab_backend/image_bucket/webp_original/5a9650930c5a0_json_image_1519800467.webp",
     * "is_free": 1,
     * "is_featured": 1,
     * "is_portrait": 0,
     * "height": 300,
     * "width": 525,
     * "updated_at": "2018-09-05 05:20:33"
     * },
     * {
     * "json_id": 728,
     * "sample_image": "http://192.168.0.114/photo_editor_lab_backend/image_bucket/webp_original/5a965023e6c56_json_image_1519800355.webp",
     * "is_free": 1,
     * "is_featured": 1,
     * "is_portrait": 0,
     * "height": 300,
     * "width": 525,
     * "updated_at": "2018-09-05 05:20:24"
     * }
     * ],
     * "video": [
     * {
     * "video_id": 20,
     * "youtube_video_id": "d6uzZqkcsa8",
     * "title": "How To Interview Candidates For A Job",
     * "channel_name": "Videojug",
     * "url": "https://www.youtube.com/watch?v=d6uzZqkcsa8",
     * "thumbnail_url": "https://i.ytimg.com/vi/d6uzZqkcsa8/hqdefault.jpg",
     * "thumbnail_width": 480,
     * "thumbnail_height": 360,
     * "published_at": "2011-04-12 13:51:31"
     * },
     * {
     * "video_id": 19,
     * "youtube_video_id": "htBDNsunGCY",
     * "title": "How to Conduct an Interview",
     * "channel_name": "HR360Inc",
     * "url": "https://www.youtube.com/watch?v=htBDNsunGCY",
     * "thumbnail_url": "https://i.ytimg.com/vi/htBDNsunGCY/hqdefault.jpg",
     * "thumbnail_width": 480,
     * "thumbnail_height": 360,
     * "published_at": "2014-06-09 15:08:05"
     * },
     * {
     * "video_id": 18,
     * "youtube_video_id": "2kNIlIrocrU",
     * "title": "Hiring tutorial: Writing effective behavioral interview questions | lynda.com",
     * "channel_name": "LinkedIn Learning",
     * "url": "https://www.youtube.com/watch?v=2kNIlIrocrU",
     * "thumbnail_url": "https://i.ytimg.com/vi/2kNIlIrocrU/hqdefault.jpg",
     * "thumbnail_width": 480,
     * "thumbnail_height": 360,
     * "published_at": "2013-06-18 21:54:24"
     * },
     * {
     * "video_id": 17,
     * "youtube_video_id": "5NVYg2HNAdA",
     * "title": "\"Why Should We Hire You?\" How to Answer this Interview Question",
     * "channel_name": "Fisher College of Business",
     * "url": "https://www.youtube.com/watch?v=5NVYg2HNAdA",
     * "thumbnail_url": "https://i.ytimg.com/vi/5NVYg2HNAdA/hqdefault.jpg",
     * "thumbnail_width": 480,
     * "thumbnail_height": 360,
     * "published_at": "2012-03-06 13:50:47"
     * },
     * {
     * "video_id": 16,
     * "youtube_video_id": "VFTNOF77bMs",
     * "title": "Interview questions and answers",
     * "channel_name": "JobTestPrep",
     * "url": "https://www.youtube.com/watch?v=VFTNOF77bMs",
     * "thumbnail_url": "https://i.ytimg.com/vi/VFTNOF77bMs/hqdefault.jpg",
     * "thumbnail_width": 480,
     * "thumbnail_height": 360,
     * "published_at": "2011-12-05 09:03:39"
     * },
     * {
     * "video_id": 15,
     * "youtube_video_id": "PCWVi5pAa30",
     * "title": "7 body language tips to impress at your next job interview",
     * "channel_name": "Cognitive Group Microsoft Talent Solutions",
     * "url": "https://www.youtube.com/watch?v=PCWVi5pAa30",
     * "thumbnail_url": "https://i.ytimg.com/vi/PCWVi5pAa30/hqdefault.jpg",
     * "thumbnail_width": 480,
     * "thumbnail_height": 360,
     * "published_at": "2016-03-16 07:56:32"
     * }
     * ],
     * "job_news": [
     * {
     * "id": 1072922200905150500,
     * "created_at": "2018-12-12 18:32:41",
     * "text": "RT <a class=\"tweet-author\" href=\"https://twitter.com/TwitterAPI\" target=\"_blank\">@TwitterAPI</a>: All app management is unifying on <a href=\"https://t.co/EfJLLFaLkk!\" target=\"_blank\">https://t.co/EfJLLFaLkk!</a> Beginning today: \n\n<a href=\"https://t.co/PCwEGWityX\" target=\"_blank\">https://t.co/PCwEGWityX</a>\n&gt; will be redirected?,
     * "favorite_count": 0,
     * "profile_image_url": "http://pbs.twimg.com/profile_images/880136122604507136/xHrnqf1T_400x400.jpg",
     * "account_url": "https://twitter.com/TwitterDev",
     * "media_url_https": "",
     * "post_type": 1,
     * "video_url": ""
     * },
     * {
     * "id": 1070059276213702700,
     * "created_at": "2018-12-04 20:56:26",
     * "text": "Celebrating the developer success story of <a class=\"tweet-author\" href=\"https://twitter.com/UnionMetrics\" target=\"_blank\">@UnionMetrics</a> platform whose underlying technology is built upon Twitter�<a href=\"https://t.co/lxA6ePkTMj\" target=\"_blank\">https://t.co/lxA6ePkTMj</a>",
     * "favorite_count": 48,
     * "profile_image_url": "http://pbs.twimg.com/profile_images/880136122604507136/xHrnqf1T_400x400.jpg",
     * "account_url": "https://twitter.com/TwitterDev",
     * "media_url_https": "",
     * "post_type": 1,
     * "video_url": ""
     * },
     * {
     * "id": 1067094924124872700,
     * "created_at": "2018-11-26 16:37:10",
     * "text": "Just getting started with Twitter APIs? Find out what you need in order to build an app. Watch this video! <a href=\"https://t.co/Hg8nkfoizN\" target=\"_blank\">https://t.co/Hg8nkfoizN</a>",
     * "favorite_count": 490,
     * "profile_image_url": "http://pbs.twimg.com/profile_images/880136122604507136/xHrnqf1T_400x400.jpg",
     * "account_url": "https://twitter.com/TwitterDev",
     * "media_url_https": "https://pbs.twimg.com/media/DsZp7igVYAAyDHB.jpg",
     * "post_type": 3,
     * "video_url": "https://video.twimg.com/amplify_video/1064638969197977600/vid/1280x720/C1utUYBYhJ_4lwaq.mp4?tag=8"
     * },
     * {
     * "id": 1058408022936977400,
     * "created_at": "2018-11-02 17:18:31",
     * "text": "RT <a class=\"tweet-author\" href=\"https://twitter.com/harmophone\" target=\"_blank\">@harmophone</a>: \"The innovative crowdsourcing that the Tagboard, Twitter and TEGNA collaboration enables is surfacing locally relevant conv?,
     * "favorite_count": 0,
     * "profile_image_url": "http://pbs.twimg.com/profile_images/880136122604507136/xHrnqf1T_400x400.jpg",
     * "account_url": "https://twitter.com/TwitterDev",
     * "media_url_https": "",
     * "post_type": 1,
     * "video_url": ""
     * },
     * {
     * "id": 1054884245578035200,
     * "created_at": "2018-10-23 23:56:17",
     * "text": "RT <a class=\"tweet-author\" href=\"https://twitter.com/andypiper\" target=\"_blank\">@andypiper</a>: My coworker <a class=\"tweet-author\" href=\"https://twitter.com/jessicagarson\" target=\"_blank\">@jessicagarson</a> rocking Jupyter and Postman in her #TapIntoTwitterNYC demo! <a href=\"https://t.co/yuF4q2Czed\" target=\"_blank\">https://t.co/yuF4q2Czed</a>",
     * "favorite_count": 0,
     * "profile_image_url": "http://pbs.twimg.com/profile_images/880136122604507136/xHrnqf1T_400x400.jpg",
     * "account_url": "https://twitter.com/TwitterDev",
     * "media_url_https": "https://pbs.twimg.com/media/DqOrzHJWsAAWDGE.jpg",
     * "post_type": 2,
     * "video_url": ""
     * },
     * {
     * "id": 1054884091227594800,
     * "created_at": "2018-10-23 23:55:40",
     * "text": "RT <a class=\"tweet-author\" href=\"https://twitter.com/dgrreen\" target=\"_blank\">@dgrreen</a>: #TapintoTwitterNYC First up all the way from <a class=\"tweet-author\" href=\"https://twitter.com/TwitterBoulder\" target=\"_blank\">@TwitterBoulder</a>  <a class=\"tweet-author\" href=\"https://twitter.com/AdventureSteady\" target=\"_blank\">@AdventureSteady</a> with updates on how <a class=\"tweet-author\" href=\"https://twitter.com/TwitterDev\" target=\"_blank\">@TwitterDev</a> is protecting u?,
     * "favorite_count": 0,
     * "profile_image_url": "http://pbs.twimg.com/profile_images/880136122604507136/xHrnqf1T_400x400.jpg",
     * "account_url": "https://twitter.com/TwitterDev",
     * "media_url_https": "",
     * "post_type": 1,
     * "video_url": ""
     * }
     * ],
     * "interview_que_ans": [
     * {
     * "question_type_id": 1,
     * "question_type": "Interview Prep Plan",
     * "question_type_image": "http://192.168.0.114/photo_editor_lab_backend/image_bucket/compressed/5c2056193c6c1_question_type_1545623065.png",
     * "create_time": "2018-11-28 10:38:41",
     * "update_time": "2018-12-24 03:44:26"
     * },
     * {
     * "question_type_id": 2,
     * "question_type": "Most Common",
     * "question_type_image": "http://192.168.0.114/photo_editor_lab_backend/image_bucket/compressed/5c2055a09edd4_question_type_1545622944.png",
     * "create_time": "2018-11-28 10:38:47",
     * "update_time": "2018-12-24 03:42:25"
     * },
     * {
     * "question_type_id": 3,
     * "question_type": "Behavioural",
     * "question_type_image": "http://192.168.0.114/photo_editor_lab_backend/image_bucket/compressed/5c205563bc996_question_type_1545622883.png",
     * "create_time": "2018-11-28 10:38:51",
     * "update_time": "2018-12-24 03:41:24"
     * },
     * {
     * "question_type_id": 4,
     * "question_type": "Resume writing",
     * "question_type_image": "http://192.168.0.114/photo_editor_lab_backend/image_bucket/compressed/5c20554a6c98c_question_type_1545622858.png",
     * "create_time": "2018-11-28 10:38:59",
     * "update_time": "2018-12-24 03:40:59"
     * },
     * {
     * "question_type_id": 5,
     * "question_type": "Technical questions",
     * "question_type_image": "http://192.168.0.114/photo_editor_lab_backend/image_bucket/compressed/5c205529e152d_question_type_1545622825.png",
     * "create_time": "2018-11-28 10:39:04",
     * "update_time": "2018-12-24 03:40:26"
     * },
     * {
     * "question_type_id": 6,
     * "question_type": "Personality",
     * "question_type_image": "http://192.168.0.114/photo_editor_lab_backend/image_bucket/compressed/5c205504ef2a1_question_type_1545622788.png",
     * "create_time": "2018-11-28 10:39:07",
     * "update_time": "2018-12-24 03:39:49"
     * }
     * ]
     * }
     * }
     */
    public function getHomePageDetail(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());

            if (($response = (new VerificationController())->validateRequiredParameter(array('sub_category_id'), $request)) != '')
                return $response;

            $this->sub_category_id = $request->sub_category_id;
            $this->cover_catalog_id = 924;
            $this->country_code = isset($request->country_code) && isset(Config::get('constant.TWITTER_USER_LIST_FOR_TWITTER_TIMELINE')[mb_strtolower($request->country_code)]) ? mb_strtolower($request->country_code) : 'us';
            $this->template_item = Config::get('constant.TEMPLATE_COUNT_FOR_HOME_PAGE');
            $this->cover_template_item = Config::get('constant.COVER_TEMPLATE_COUNT_FOR_HOME_PAGE');
            $this->video_item = Config::get('constant.VIDEO_COUNT_FOR_HOME_PAGE');
            $this->job_news_item = Config::get('constant.JOB_NEWS_COUNT_FOR_HOME_PAGE');
            $this->question_type_item = Config::get('constant.QUESTION_TYPE_COUNT_FOR_HOME_PAGE');

            if (!Cache::has("pel:getHomePageDetail:$this->sub_category_id:$this->country_code")) {
                $result = Cache::remember("getHomePageDetail:$this->sub_category_id:$this->country_code", 1440, function () {

                    $template = DB::select('SELECT
                                    id as json_id,
                                    IF(attribute1 != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",attribute1),"") as sample_image,
                                    is_free,
                                    is_featured,
                                    is_portrait,
                                    coalesce(height,0) AS height,
                                    coalesce(width,0) AS width,
                                    COALESCE(multiple_images,"") AS multiple_images,
                                    COALESCE(json_pages_sequence,"") AS pages_sequence,
                                    COALESCE(LENGTH(json_pages_sequence) - LENGTH(REPLACE(json_pages_sequence, ",","")) + 1,1) AS total_pages,
                                    updated_at
                                    FROM
                                    images
                                    WHERE
                                    catalog_id in(select catalog_id FROM sub_category_catalog WHERE sub_category_id = ? AND is_active = 1) and
                                    is_featured = 1
                                    order by updated_at DESC LIMIT ?, ?', [$this->sub_category_id, 0, $this->template_item]);

                    $cover_template = DB::select('SELECT
                                                        id AS json_id,
                                                        IF(attribute1 != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",attribute1),"") AS sample_image,
                                                        is_free,
                                                        is_featured,
                                                        is_portrait,
                                                        coalesce(height,0) AS height,
                                                        coalesce(width,0) AS width,
                                                        COALESCE(multiple_images,"") AS multiple_images,
                                                        COALESCE(json_pages_sequence,"") AS pages_sequence,
                                                        COALESCE(LENGTH(json_pages_sequence) - LENGTH(REPLACE(json_pages_sequence, ",","")) + 1,1) AS total_pages,
                                                        updated_at
                                                    FROM
                                                        images
                                                    WHERE
                                                        catalog_id = '. $this->cover_catalog_id .'
                                                        order by updated_at DESC LIMIT ?, ?', [0, $this->template_item]);

                    $video = DB::select('SELECT id video_id,
                                         youtube_video_id,
                                         title,
                                         channel_name,
                                         url,
                                         country_code,
                                         thumbnail_url,
                                         thumbnail_width,
                                         thumbnail_height,
                                         published_at
                                         FROM youtube_video_master
                                         WHERE country_code = ?
                                         ORDER BY update_time DESC LIMIT ?, ?', [$this->country_code, 0, $this->video_item]);


                    $consumerKey = Config::get('constant.twitter_consumer_Key');
                    $consumerSecret = Config::get('constant.twitter_consumer_Secret');
                    $accessToken = Config::get('constant.twitter_access_Token');
                    $accessTokenSecret = Config::get('constant.twitter_access_Token_Secret');
                    $post_list = [];
                    $twitter_list = Config::get('constant.TWITTER_USER_LIST_FOR_TWITTER_TIMELINE.'.$this->country_code);
                    $twitter = explode(',', $twitter_list);
                    $twit_user_count = count($twitter);
                    $i = 0;
                    foreach ($twitter as $twit) {
                        $t[$i] = $twit;
                        $i++;
                    }

                    for ($loop = 0; $loop < $twit_user_count; $loop++) {
                        $this->twitter_list = $twitter;

                        // Twitter account username
                        $twitterID = $t[$loop];

                        // Number of tweets
                        $tweetNum = round($this->job_news_item / $i);

                        if ($twit_user_count < $this->job_news_item || $tweetNum == 0) {
                            //Log::info('$twit_user_count');
                            $tweetNum = $this->job_news_item;
                        }

                        // Authenticate with twitter
                        $twitterConnection = new TwitterOAuth(
                            $consumerKey,
                            $consumerSecret,
                            $accessToken,
                            $accessTokenSecret
                        );

                        // Get the user timeline feeds
                        /*$list = (new NewsController())->twitterPostByTwitterId($twitterConnection, $twitterID, $tweetNum);
                        if (is_array($list)) {
                            $post_list = array_merge($post_list, $list);
                        } else {
                            Log::error("getHomePageDetail : unable to get data for twitter channel", ["twitterID" => $twitterID]);
                        }*/

                    }

                    usort($post_list, function ($a, $b) { //Sort the array using a user defined function
                        return strtotime($a['created_at']) > strtotime($b['created_at']) ? -1 : 1;
                    });

                    $this->job_news = array_splice($post_list, 0, $this->job_news_item);

                    $this->is_active = 1;

                    $interview_que_ans = DB::select('SELECT
                                    qm.id as question_type_id,
                                    qm.question_type,
                                    IF(qm.image != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",qm.image),"") as question_type_image,
                                    qm.create_time,
                                    qm.update_time
                                  FROM
                                  question_type_master as qm
                                  where qm.is_active = ?
                                  ORDER BY update_time DESC LIMIT ?, ?', [$this->is_active, 0, $this->question_type_item]);

                    return ['template' => $template, 'cover_template' => $cover_template, 'video' => $video, 'job_news' => $this->job_news, 'interview_que_ans' => $interview_que_ans, 'prefix_url' => Config::get('constant.AWS_BUCKET_PATH_PHOTO_EDITOR_LAB').'/'];

                });
            }
            $redis_result = Cache::get("getHomePageDetail:$this->sub_category_id:$this->country_code");

            Redis::expire("getHomePageDetail:$this->sub_category_id:$this->country_code", 1);

            $response = Response::json(array('code' => 200, 'message' => 'Home page detail fetched successfully.', 'cause' => '', 'data' => $redis_result));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            Log::error("getHomePageDetail : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get home detail.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    /*=========================================| Stickers, Backgrounds, Shapes, Frames |=========================================*/

    /**
     * @api {post} getContentByCatalogId   getContentByCatalogId
     * @apiName getContentByCatalogId
     * @apiGroup User
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "catalog_id":397, //compulsory
     * "page":1, //compulsory
     * "item_count":2 //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Content fetched successfully.",
     * "cause": "",
     * "data": {
     * "total_record": 135,
     * "is_next_page": true,
     * "result": [
     * {
     * "img_id": 3303,
     * "thumbnail_img": "http://192.168.0.113/photo_editor_lab_backend/image_bucket/thumbnail/5c6bb53019f81_normal_image_1550562608.jpg",
     * "compressed_img": "http://192.168.0.113/photo_editor_lab_backend/image_bucket/compressed/5c6bb53019f81_normal_image_1550562608.jpg",
     * "original_img": "http://192.168.0.113/photo_editor_lab_backend/image_bucket/original/5c6bb53019f81_normal_image_1550562608.jpg",
     * "is_featured": "",
     * "is_free": 1,
     * "is_portrait": 0,
     * "search_category": ""
     * },
     * {
     * "img_id": 3304,
     * "thumbnail_img": "http://192.168.0.113/photo_editor_lab_backend/image_bucket/thumbnail/5c6bb530289e5_normal_image_1550562608.jpg",
     * "compressed_img": "http://192.168.0.113/photo_editor_lab_backend/image_bucket/compressed/5c6bb530289e5_normal_image_1550562608.jpg",
     * "original_img": "http://192.168.0.113/photo_editor_lab_backend/image_bucket/original/5c6bb530289e5_normal_image_1550562608.jpg",
     * "is_featured": "",
     * "is_free": 1,
     * "is_portrait": 0,
     * "search_category": ""
     * }
     * ]
     * }
     * }
     */
    public function getContentByCatalogId(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('catalog_id', 'page', 'item_count'), $request)) != '')
                return $response;

            $this->catalog_id = $request->catalog_id;
            $this->page = $request->page;
            $this->item_count = $request->item_count;
            $this->offset = ($this->page - 1) * $this->item_count;
            $is_cache_enable = isset($request->is_cache_enable) ? $request->is_cache_enable : 1;

            if ($is_cache_enable) {
                $redis_result = Cache::remember("getContentByCatalogId:$this->catalog_id:$this->page:$this->item_count", Config::get('constant.CACHE_TIME_6_HOUR'), function () {


                    $free_content_count = Config::get('constant.FREE_CONTENT_COUNT');

                    $total_row_result = DB::select('SELECT
                                                      count(im.id) as total
                                                    FROM
                                                      images as im
                                                    WHERE
                                                      im.is_active = 1 AND
                                                      im.catalog_id = ? AND
                                                      isnull(im.original_img) AND
                                                      isnull(im.display_img)', [$this->catalog_id]);

                    $total_row = $total_row_result[0]->total;

                    $content_list = DB::select('SELECT
                                                  im.id as img_id,
                                                  IF(im.image != "",CONCAT("' . Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",im.image),"") as thumbnail_img,
                                                  IF(im.image != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",im.image),"") as compressed_img,
                                                  IF(im.image != "",CONCAT("' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",im.image),"") as original_img,
                                                  coalesce(im.is_featured,"") as is_featured,
                                                  coalesce(im.is_free,0) as is_free,
                                                  coalesce(im.is_ios_free,0) as is_ios_free,
                                                  coalesce(im.is_portrait,0) as is_portrait,
                                                  COALESCE(im.multiple_images,"") AS multiple_images,
                                                  COALESCE(im.json_pages_sequence,"") AS pages_sequence,
                                                  COALESCE(LENGTH(im.json_pages_sequence) - LENGTH(REPLACE(im.json_pages_sequence, ",","")) + 1,1) AS total_pages,
                                                  coalesce(im.search_category,"") as search_category
                                                FROM
                                                  images as im
                                                WHERE
                                                  im.is_active = 1 AND
                                                  im.catalog_id = ? AND
                                                  isnull(im.original_img) AND
                                                  isnull(im.display_img)
                                                ORDER BY im.updated_at DESC LIMIT ?,?', [$this->catalog_id, $this->offset, $this->item_count]);
                    $c = 0;
                    foreach ($content_list as $key) {

                        if ($c <= $free_content_count && $this->page == 1) {
                            $key->is_free = 1;
                            $key->is_ios_free = 1;
                        }
                        $c++;
                    }

                    $is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;
                    return array('prefix_url' => Config::get('constant.AWS_BUCKET_PATH_PHOTO_EDITOR_LAB').'/', 'total_record' => $total_row, 'is_next_page' => $is_next_page, 'result' => $content_list);

                });

            } else {

                $free_content_count = Config::get('constant.FREE_CONTENT_COUNT');

                $total_row_result = DB::select('SELECT
                                                      count(im.id) as total
                                                    FROM
                                                      images as im
                                                    WHERE
                                                      im.catalog_id = ? AND
                                                      isnull(im.original_img) AND
                                                      isnull(im.display_img)', [$this->catalog_id]);

                $total_row = $total_row_result[0]->total;

                $content_list = DB::select('SELECT
                                                  im.id as img_id,
                                                  IF(im.image != "",CONCAT("' . Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",im.image),"") as thumbnail_img,
                                                  IF(im.image != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",im.image),"") as compressed_img,
                                                  IF(im.image != "",CONCAT("' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",im.image),"") as original_img,
                                                  coalesce(im.is_featured,"") as is_featured,
                                                  coalesce(im.is_free,0) as is_free,
                                                  coalesce(im.is_ios_free,0) as is_ios_free,
                                                  coalesce(im.is_portrait,0) as is_portrait,
                                                  COALESCE(im.multiple_images,"") AS multiple_images,
                                                  COALESCE(im.json_pages_sequence,"") AS pages_sequence,
                                                  COALESCE(LENGTH(im.json_pages_sequence) - LENGTH(REPLACE(im.json_pages_sequence, ",","")) + 1,1) AS total_pages,
                                                  coalesce(im.search_category,"") as search_category
                                                FROM
                                                  images as im
                                                WHERE
                                                  im.catalog_id = ? AND
                                                  isnull(im.original_img) AND
                                                  isnull(im.display_img)
                                                ORDER BY im.updated_at DESC LIMIT ?,?', [$this->catalog_id, $this->offset, $this->item_count]);
                $c = 0;
                foreach ($content_list as $key) {

                    if ($c <= $free_content_count && $this->page == 1) {
                        $key->is_free = 1;
                        $key->is_ios_free = 1;
                    }
                    $c++;
                }

                $is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;
                $redis_result = array('prefix_url' => Config::get('constant.AWS_BUCKET_PATH_PHOTO_EDITOR_LAB').'/', 'total_record' => $total_row, 'is_next_page' => $is_next_page, 'result' => $content_list);

            }

            $response = Response::json(array('code' => 200, 'message' => 'Content fetched successfully.', 'cause' => '', 'data' => $redis_result));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            Log::error("getContentByCatalogId : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get content.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    public function getContentByCatalogId_v2(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('catalog_id', 'page', 'item_count'), $request)) != '')
                return $response;

            $this->catalog_id = $request->catalog_id;
            $this->page = $request->page;
            $this->item_count = $request->item_count;
            $this->offset = ($this->page - 1) * $this->item_count;
            $is_cache_enable = isset($request->is_cache_enable) ? $request->is_cache_enable : 1;

            if($is_cache_enable){
                $redis_result = Cache::remember("getContentByCatalogId_v2:$this->catalog_id:$this->page:$this->item_count",config('constant,CACHE_TIME_6_HOUR'), function () {

                        $free_content_count = Config::get('constant.FREE_CONTENT_COUNT');

                        $total_row = Cache::remember("getContentByCatalogId_v2:$this->catalog_id",config('constant,CACHE_TIME_6_HOUR'), function () {
                                $total_row_result = DB::select('SELECT
                                                      count(im.id) as total
                                                    FROM
                                                      images as im
                                                    WHERE
                                                      im.is_active = 1 AND
                                                      im.catalog_id = ? AND
                                                      isnull(im.original_img) AND
                                                      isnull(im.display_img)', [$this->catalog_id]);
                                return $total_row = $total_row_result[0]->total;
                        });

                        $content_list = DB::select('SELECT
                                                  im.id as img_id,
                                                  IF(im.image != "",CONCAT("' . Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",im.image),"") as thumbnail_img,
                                                  IF(im.image != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",im.image),"") as compressed_img,
                                                  IF(im.image != "",CONCAT("' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",im.image),"") as original_img,
                                                  coalesce(im.is_featured,"") as is_featured,
                                                  coalesce(im.is_free,0) as is_free,
                                                  coalesce(im.is_portrait,0) as is_portrait,
                                                  coalesce(im.search_category,"") as search_category
                                                FROM
                                                  images as im
                                                WHERE
                                                  im.is_active = 1 AND
                                                  im.catalog_id = ? AND
                                                  isnull(im.original_img) AND
                                                  isnull(im.display_img)
                                                ORDER BY im.updated_at DESC LIMIT ?,?', [$this->catalog_id, $this->offset, $this->item_count]);
                        $c = 0;
                        foreach ($content_list as $key) {
                            if ($c <= $free_content_count && $this->page == 1) {
                                $key->is_free = 1;
                            }
                            $c++;
                        }

                        $is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;
                        return array('total_record' => $total_row, 'is_next_page' => $is_next_page, 'result' => $content_list);

                    });
            }else{
                $free_content_count = Config::get('constant.FREE_CONTENT_COUNT');

                    $total_row_result = DB::select('SELECT
                                                      count(im.id) as total
                                                    FROM
                                                      images as im
                                                    WHERE
                                                      im.is_active = 1 AND
                                                      im.catalog_id = ? AND
                                                      isnull(im.original_img) AND
                                                      isnull(im.display_img)', [$this->catalog_id]);
                    $total_row = $total_row_result[0]->total;

                    $content_list = DB::select('SELECT
                                                  im.id as img_id,
                                                  IF(im.image != "",CONCAT("' . Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",im.image),"") as thumbnail_img,
                                                  IF(im.image != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",im.image),"") as compressed_img,
                                                  IF(im.image != "",CONCAT("' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",im.image),"") as original_img,
                                                  coalesce(im.is_featured,"") as is_featured,
                                                  coalesce(im.is_free,0) as is_free,
                                                  coalesce(im.is_portrait,0) as is_portrait,
                                                  coalesce(im.search_category,"") as search_category
                                                FROM
                                                  images as im
                                                WHERE
                                                  im.is_active = 1 AND
                                                  im.catalog_id = ? AND
                                                  isnull(im.original_img) AND
                                                  isnull(im.display_img)
                                                ORDER BY im.updated_at DESC LIMIT ?,?', [$this->catalog_id, $this->offset, $this->item_count]);
                $c = 0;
                foreach ($content_list as $key) {
                    if ($c <= $free_content_count && $this->page == 1) {
                        $key->is_free = 1;
                    }
                    $c++;
                }

                $is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;
                $redis_result = array('total_record' => $total_row, 'is_next_page' => $is_next_page, 'result' => $content_list);
            }

            if (!$redis_result) {
                $redis_result = [];
            }


            $response = Response::json(array('code' => 200, 'message' => 'Content fetched successfully.', 'cause' => '', 'data' => $redis_result));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            Log::error("getContentByCatalogId_v2 : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get content.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
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
            $is_cache_enable = isset($request->is_cache_enable) ? $request->is_cache_enable : 1;

            if ($is_cache_enable) {
                $redis_result = Cache::remember("getImagesByCatalogId:$this->catalog_id", Config::get('constant.CACHE_TIME_6_HOUR'), function () {
                    $result = DB::select('SELECT
                                          im.id as img_id,
                                          @svg := IF(im.content_type = ' . Config::get('constant.CONTENT_TYPE_FOR_SVG_RESOURCE') . ' AND im.image != "", CONCAT("' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '", im.image), "") AS svg_image,
                                          IF(@svg != "", @svg, IF(im.image != "", CONCAT("' . Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '", im.image), "")) AS thumbnail_img,
                                          IF(@svg != "", @svg, IF(im.image != "", CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '", im.image), "")) AS compressed_img,
                                          IF(@svg != "", @svg, IF(im.image != "", CONCAT("' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '", im.image), "")) AS original_img,
                                          im.search_category,
                                          im.template_name,
                                          coalesce(im.height,0) AS height,
                                          coalesce(im.width,0) AS width,
                                          coalesce(im.original_img_height,0) AS original_img_height,
                                          coalesce(im.original_img_width,0) AS original_img_width,
                                          IF(im.json_data IS NOT NULL,1,0) as is_json_data,
                                          coalesce(im.json_data,"") as json_data,
                                          coalesce(im.is_featured,"") as is_featured,
                                          coalesce(im.is_free,0) as is_free,
                                          coalesce(im.is_ios_free,0) as is_ios_free,
                                          coalesce(im.is_portrait,0) as is_portrait,
                                          COALESCE(im.multiple_images,"") AS multiple_images,
                                          COALESCE(im.json_pages_sequence,"") AS pages_sequence,
                                          COALESCE(LENGTH(im.json_pages_sequence) - LENGTH(REPLACE(im.json_pages_sequence, ",","")) + 1,1) AS total_pages,
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

            } else {

                $redis_result = DB::select('SELECT
                                          im.id as img_id,
                                          @svg := IF(im.content_type = ' . Config::get('constant.CONTENT_TYPE_FOR_SVG_RESOURCE') . ' AND im.image != "", CONCAT("' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '", im.image), "") AS svg_image,
                                          IF(@svg != "", @svg, IF(im.image != "", CONCAT("' . Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '", im.image), "")) AS thumbnail_img,
                                          IF(@svg != "", @svg, IF(im.image != "", CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '", im.image), "")) AS compressed_img,
                                          IF(@svg != "", @svg, IF(im.image != "", CONCAT("' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '", im.image), "")) AS original_img,
                                          im.search_category,
                                          im.template_name,                                          
                                          coalesce(im.height,0) AS height,
                                          coalesce(im.width,0) AS width,
                                          coalesce(im.original_img_height,0) AS original_img_height,
                                          coalesce(im.original_img_width,0) AS original_img_width,
                                          IF(im.json_data IS NOT NULL,1,0) as is_json_data,
                                          coalesce(im.json_data,"") as json_data,
                                          coalesce(im.is_featured,"") as is_featured,
                                          coalesce(im.is_free,0) as is_free,
                                          coalesce(im.is_ios_free,0) as is_ios_free,
                                          coalesce(im.is_portrait,0) as is_portrait,
                                          COALESCE(im.multiple_images,"") AS multiple_images,
                                          COALESCE(im.json_pages_sequence,"") AS pages_sequence,
                                          COALESCE(LENGTH(im.json_pages_sequence) - LENGTH(REPLACE(im.json_pages_sequence, ",","")) + 1,1) AS total_pages,
                                          coalesce(im.search_category,"") as search_category
                                        FROM
                                          images as im
                                        where
                                          im.catalog_id = ? AND
                                          isnull(im.original_img) AND
                                          isnull(im.display_img)
                                        order by im.updated_at DESC', [$this->catalog_id]);

                /*foreach ($result as $key) {
                     if ($key->json_data != "") {
                         $key->json_data = json_decode($key->json_data);
                     }
                 }*/

            }

            $response = Response::json(array('code' => 200, 'message' => 'Images fetched successfully.', 'cause' => '', 'data' => ['image_list' => $redis_result, 'prefix_url' => Config::get('constant.AWS_BUCKET_PATH_PHOTO_EDITOR_LAB').'/']));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            Log::error("getImagesByCatalogId : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get image.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    /*=========================================| Font Module |=========================================*/

    /**
     * @api {post} getAllFontsByCatalogId   getAllFontsByCatalogId
     * @apiName getAllFontsByCatalogId
     * @apiGroup User
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "catalog_id":328 //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Fonts fetched successfully.",
     * "cause": "",
     * "data": {
     * "result": [
     * {
     * "font_id": 80,
     * "catalog_id": 333,
     * "font_name": "Shonar Bangla Bold",
     * "font_file": "Shonar-Bold.ttf",
     * "font_url": "http://192.168.0.113/photo_editor_lab_backend/image_bucket/fonts/Shonar-Bold.ttf",
     * "ios_font_name": "ShonarBangla-Bold"
     * },
     * {
     * "font_id": 79,
     * "catalog_id": 333,
     * "font_name": "Shonar Bangla",
     * "font_file": "Shonar.ttf",
     * "font_url": "http://192.168.0.113/photo_editor_lab_backend/image_bucket/fonts/Shonar.ttf",
     * "ios_font_name": "ShonarBangla"
     * }
     * ]
     * }
     * }
     */
    public function getAllFontsByCatalogId(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('catalog_id'), $request)) != '')
                return $response;

            $this->catalog_id = $request->catalog_id;
            $is_cache_enable = isset($request->is_cache_enable) ? $request->is_cache_enable : 1;

            if ($is_cache_enable) {
                $redis_result = Cache::remember("getAllFontsByCatalogId:$this->catalog_id", Config::get('constant.CACHE_TIME_24_HOUR'), function () {

                    return DB::select('SELECT
                                              fm.id as font_id,
                                              fm.catalog_id,
                                              fm.font_name,
                                              fm.font_file,
                                              IF(fm.font_file != "",CONCAT("' . Config::get('constant.FONT_FILE_DIRECTORY_OF_DIGITAL_OCEAN') . '",fm.font_file),"") as font_url,
                                              fm.ios_font_name
                                            FROM
                                              font_master as fm
                                            where
                                              fm.is_active = 1 AND
                                              fm.catalog_id = ?
                                            order by fm.update_time DESC', [$this->catalog_id]);

                });
            } else {
                $redis_result = DB::select('SELECT
                                              fm.id as font_id,
                                              fm.catalog_id,
                                              fm.font_name,
                                              fm.font_file,
                                              IF(fm.font_file != "",CONCAT("' . Config::get('constant.FONT_FILE_DIRECTORY_OF_DIGITAL_OCEAN') . '",fm.font_file),"") as font_url,
                                              fm.ios_font_name
                                            FROM
                                              font_master as fm
                                            where
                                              fm.is_active = 1 AND
                                              fm.catalog_id = ?
                                            order by fm.update_time DESC', [$this->catalog_id]);
            }

            $response = Response::json(array('code' => 200, 'message' => 'Fonts fetched successfully.', 'cause' => '', 'data' => ['result' => $redis_result, 'prefix_url' => Config::get('constant.AWS_BUCKET_PATH_PHOTO_EDITOR_LAB') . '/']));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            Log::error("getAllFontsByCatalogId : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get fonts.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    /**
     * @api {post} getCorruptedFontList   getCorruptedFontList
     * @apiName getCorruptedFontList
     * @apiGroup User
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "last_sync_time":"0" //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Fonts details fetched successfully.",
     * "cause": "",
     * "data": {
     * "result": [
     *   {
     *   "catalog_id": 228,
     *   "name": "Roboto",
     *   "is_removed": 0,
     *   "is_free": 0,
     *   "is_featured": 0,
     *   "font_list": [
     *   {
     *   "font_id": 33,
     *   "catalog_id": 228,
     *   "font_name": "Roboto Thin",
     *   "font_file": "Roboto-Thin.ttf",
     *   "font_url": "http://192.168.0.115/videoflyer_backend/image_bucket/fonts/Roboto-Thin.ttf",
     *   "ios_font_name": "Roboto-Thin",
     *   6800"android_font_name": "fonts/nexa_rustsans_black.otf"
     *   }
     *  ]
     *   }
     * ],
     * "last_sync_time": "2019-11-25 04:05:54"
     * }
     * }
     */
    public function getCorruptedFontListBackup(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('last_sync_time'), $request)) != '')
                return $response;

            $this->last_sync_time = $request->last_sync_time;

            if (!Cache::has("pel:getCorruptedFontList$this->last_sync_time")) {
                $result = Cache::rememberforever("getCorruptedFontList$this->last_sync_time", function () {

                    $catalog_list = DB::select('SELECT cfc.catalog_id,
                                                      cfc.name,
                                                      cfc.is_removed,
                                                      cfc.is_free,
                                                      cfc.is_featured
                                                    FROM
                                                      corrupt_font_catalog_master as cfc
                                                    where
                                                      cfc.is_active = 1 AND
                                                      cfc.create_time >= ?
                                                    order by cfc.update_time DESC', [$this->last_sync_time]);

                    $result = array();
                    $int = 0;
                    Foreach($catalog_list AS $catalog){

                        $catalog_id = $catalog->catalog_id;
                        $catalog_name = $catalog->name;
                        $is_removed = $catalog->is_removed;
                        $is_free = $catalog->is_free;
                        $is_featured = $catalog->is_featured;
                        $font_result =  DB::select('SELECT
                                              fm.font_id as font_id,
                                              fm.catalog_id,
                                              fm.font_name,
                                              fm.font_file,
                                              IF(fm.font_file != "",CONCAT("' . Config::get('constant.FONT_FILE_DIRECTORY_OF_DIGITAL_OCEAN') . '",fm.font_file),"") as font_url,
                                              fm.ios_font_name,
                                              fm.android_font_name
                                            FROM
                                              corrupt_font_detail_master as fm
                                            where
                                              fm.is_active = 1 AND
                                              fm.catalog_id = ? AND 
                                              fm.create_time >= ?
                                            order by fm.update_time DESC', [$catalog_id,$this->last_sync_time]);
                        $result_array = array(
                            'catalog_id' => $catalog_id,
                            'name' => $catalog_name,
                            'is_removed' => $is_removed,
                            'is_free' => $is_free,
                            'is_featured' => $is_featured,
                            'font_list' => $font_result
                        );
                        $delete_font_array = array($int => $result_array);
                        $result = array_merge($result, $delete_font_array);
                        $int++;
                    }
                    return $result;
                });
            }
            $redis_result = Cache::get("getCorruptedFontList$this->last_sync_time");

            if (!$redis_result) {
                $redis_result = [];
            }

            $last_sync_time = date("Y-m-d H:i:s");

            $response = Response::json(array('code' => 200, 'message' => 'Fonts details fetched successfully.', 'cause' => '', 'data' => ['result' => $redis_result,'last_sync_time' => $last_sync_time]));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            Log::error("getCorruptedFontList : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get fonts details.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    public function getCorruptedFontList(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('last_sync_time'), $request)) != '')
                return $response;

            $this->last_sync_time = $request->last_sync_time;
            $redis_result = array();

            $db_last_sync_time = Cache::rememberforever("getCorruptedFontList:last_sync_time", function () {
                return DB::select('SELECT MAX(update_time) AS last_sync_time FROM corrupt_font_detail_master')[0]->last_sync_time;
            });

            if ($db_last_sync_time >= $this->last_sync_time) {

                if (!$this->last_sync_time) {
                    $redis_result = Cache::rememberforever("getCorruptedFontList:default_redis_result", function () {
                        return $this->getCorruptedFontListData('', '');
                    });

                } else {
                    $redis_result = $this->getCorruptedFontListData(' AND cfc.create_time >= "' . $this->last_sync_time . '" ', ' AND fm.create_time >= "' . $this->last_sync_time . '" ');
                }
            }

            $response = Response::json(array('code' => 200, 'message' => 'Fonts details fetched successfully.', 'cause' => '', 'data' => ['result' => $redis_result, 'last_sync_time' => date("Y-m-d H:i:s")]));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            Log::error("getCorruptedFontList : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get fonts details.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    public function getCorruptedFontListData($parent_where_condition, $child_where_condition)
    {
        $result = [];
        try {
            $catalog_list = DB::select('SELECT 
                                                cfc.catalog_id,
                                                cfc.name,
                                                cfc.is_removed,
                                                cfc.is_free,
                                                cfc.is_featured
                                            FROM
                                                corrupt_font_catalog_master AS cfc
                                            WHERE
                                                cfc.is_active = 1 
                                                ' . $parent_where_condition . '
                                            ORDER BY cfc.update_time DESC');

            foreach ($catalog_list as $catalog) {

                $catalog_id = $catalog->catalog_id;
                $font_result = DB::select('SELECT
                                                  fm.font_id as font_id,
                                                  fm.catalog_id,
                                                  fm.font_name,
                                                  fm.font_file,
                                                  IF(fm.font_file != "",CONCAT("' . Config::get('constant.FONT_FILE_DIRECTORY_OF_DIGITAL_OCEAN') . '",fm.font_file),"") AS font_url,
                                                  fm.ios_font_name,
                                                  fm.android_font_name
                                            FROM
                                                  corrupt_font_detail_master AS fm
                                            WHERE
                                                  fm.is_active = 1 AND
                                                  fm.catalog_id = ? 
                                                  ' . $child_where_condition . '
                                            ORDER BY fm.update_time DESC', [$catalog_id]);
                $result_array = array(
                    'catalog_id' => $catalog_id,
                    'name' => $catalog->name,
                    'is_removed' => $catalog->is_removed,
                    'is_free' => $catalog->is_free,
                    'is_featured' => $catalog->is_featured,
                    'font_list' => $font_result
                );
                $result[] = $result_array;
            }

        } catch (Exception $e) {
            Log::error("getCorruptedFontListData : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
        }
        return $result;
    }

    /*================| This API only used for Brand Maker |===============*/

    /**
     * @api {post} getJsonSampleDataFilterBySearchTag   getJsonSampleDataFilterBySearchTag
     * @apiName getJsonSampleDataFilterBySearchTag
     * @apiGroup User
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "sub_category_id":97, //compulsory
     * "search_category":"Leaderboard Ad", //optional for templates screen
     * "page":1, //optional for templates screen
     * "item_count":20 //optional for templates screen
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Templates fetched successfully.",
     * "cause": "",
     * "data": {
     * "total_record": 11,
     * "is_next_page": false,
     * "templates_with_categories": [
     * {
     * "category_name": "Logos",
     * "content_list": []
     * },
     * {
     * "category_name": "Business Cards",
     * "content_list": [
     * {
     * "json_id": 4768,
     * "sample_image": "http://192.168.0.113/photo_editor_lab_backend/image_bucket/webp_original/5cac6c693d405_json_image_1554803817.webp",
     * "is_free": 1,
     * "is_featured": 0,
     * "is_portrait": 0,
     * "height": 300,
     * "width": 525,
     * "updated_at": "2019-04-10 08:09:41"
     * }
     * ]
     * },
     * {
     * "category_name": "Flyers",
     * "content_list": [
     * {
     * "json_id": 3390,
     * "sample_image": "http://192.168.0.113/photo_editor_lab_backend/image_bucket/webp_original/5c6f7f3e037d9_json_image_1550810942.webp",
     * "is_free": 1,
     * "is_featured": 1,
     * "is_portrait": 1,
     * "height": 400,
     * "width": 325,
     * "updated_at": "2019-03-30 06:01:38"
     * }
     * ]
     * }
     * ],
     * "template_list": []
     * }
     * }
     */
    public function getJsonSampleDataFilterBySearchTag(Request $request_body)
    {

        try {

            /*================| This API only used for Brand Maker |===============*/

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('sub_category_id'), $request)) != '')
                return $response;

            $this->sub_category_id = $request->sub_category_id;
            $this->search_category = isset($request->search_category) ? strtolower(trim($request->search_category)) : "";
            $this->page = isset($request->page) ? $request->page : 1;
            $this->item_count = isset($request->item_count) ? $request->item_count : 20;
            $this->offset = ($this->page - 1) * $this->item_count;
            $is_cache_enable = isset($request->is_cache_enable) ? $request->is_cache_enable : 1;


            if ($is_cache_enable) {
                $redis_result = Cache::remember("getJsonSampleDataFilterBySearchTag:$this->sub_category_id:$this->search_category:$this->page:$this->item_count", Config::get('constant.CACHE_TIME_6_HOUR'), function () {


                    if ($this->search_category == "") {
                        //$category_list = array("Flyers", "Business Card", "Brochures", "Banners", "Social Media Post");
                        $category_list = array(
                            "Logos",
                            "Posters",
                            "Brochures",
                            "Infographic",
                            "Flyers",
                            "Instagram Posts",
                            "Instagram Story",
                            "Facebook Posts",
                            "Twitter Post",
                            "LinkedIn Post",
                            "Pinterest Pins",
                            "Snapchat Geofilter",
                            "Business Cards",
                            "Invitation",
                            "Presentation",
                            "Storyboard",
                            "Photo Book",
                            "Photo Collage",
                            "Festivals",
                            "Blog Graphics",
                            "Blog Images",
                            "Quotes",
                            "Gift Cards",
                            "Hiring",
                            "A4 Letterhead",
                            "Bio Data",
                            "Menu",
                            "Proposal",
                            "Invoice",
                            "Planner",
                            "Twitter Header",
                            "Email Header",
                            "Page Cover",
                            "Facebook Event Cover",
                            "Facebook Group Cover",
                            "Youtube Channel Arts",
                            "Youtube Thumbnails",
                            "Announcements",
                            "Book Cover",
                            "Black And White",
                            "T Shirt",
                            "Comics",
                            "Coupon",
                            "Billboard",
                            "Roll Up Banner",
                            "Interstitial",
                            "Large Rectangle",
                            "Watercolor Logo",
                            "Medium Rectangle",
                            "Leaderboard Ad",
                            "Skyscraper Ad",
                            "Miscellaneous"
                        );
                        $item_count_of_templates = Config::get('constant.ITEM_COUNT_OF_TEMPLATES');

                        $categories_data = array();
                        foreach ($category_list as $key) {

                            $search_text = "%#$key%";
                            $content_list = DB::select('SELECT
                                                          im.id as json_id,
                                                          IF(im.attribute1 != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",im.attribute1),"") as sample_image,
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
                                                          isnull(im.display_img) AND
                                                          im.search_category LIKE ?
                                                        ORDER BY im.updated_at DESC LIMIT ?, ?', [$this->sub_category_id, $search_text, 0, $item_count_of_templates]);

                            $categories_data[] = array('category_name' => $key, 'content_list' => $content_list);

                        }
                        $total_row = count($category_list);
                        $search_result = [];
                    } else {
                        $search_text = "%#$this->search_category%";
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
                                                  im.search_category LIKE ?
                                                ORDER BY im.updated_at DESC', [$this->sub_category_id, $search_text]);

                        $total_row = $total_row_result[0]->total;

                        $search_result = DB::select('SELECT
                                                  im.id as json_id,
                                                  IF(im.attribute1 != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",im.attribute1),"") as sample_image,
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
                                                  isnull(im.display_img) AND
                                                  im.search_category LIKE ?
                                                ORDER BY im.updated_at DESC LIMIT ?, ?', [$this->sub_category_id, $search_text, $this->offset, $this->item_count]);
                        $categories_data = [];

                    }

                    $is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;
                    $result = array('total_record' => $total_row, 'is_next_page' => $is_next_page, 'templates_with_categories' => $categories_data, 'template_list' => $search_result);

                    return $result;

                });

            } else {

                if ($this->search_category == "") {
                    //$category_list = array("Flyers", "Business Card", "Brochures", "Banners", "Social Media Post");
                    $category_list = array(
                        "Logos",
                        "Posters",
                        "Brochures",
                        "Infographic",
                        "Flyers",
                        "Instagram Posts",
                        "Instagram Story",
                        "Facebook Posts",
                        "Twitter Post",
                        "LinkedIn Post",
                        "Pinterest Pins",
                        "Snapchat Geofilter",
                        "Business Cards",
                        "Invitation",
                        "Presentation",
                        "Storyboard",
                        "Photo Book",
                        "Photo Collage",
                        "Festivals",
                        "Blog Graphics",
                        "Blog Images",
                        "Quotes",
                        "Gift Cards",
                        "Hiring",
                        "A4 Letterhead",
                        "Bio Data",
                        "Menu",
                        "Proposal",
                        "Invoice",
                        "Planner",
                        "Twitter Header",
                        "Email Header",
                        "Page Cover",
                        "Facebook Event Cover",
                        "Facebook Group Cover",
                        "Youtube Channel Arts",
                        "Youtube Thumbnails",
                        "Announcements",
                        "Book Cover",
                        "Black And White",
                        "T Shirt",
                        "Comics",
                        "Coupon",
                        "Billboard",
                        "Roll Up Banner",
                        "Interstitial",
                        "Large Rectangle",
                        "Watercolor Logo",
                        "Medium Rectangle",
                        "Leaderboard Ad",
                        "Skyscraper Ad",
                        "Miscellaneous"
                    );
                    $item_count_of_templates = Config::get('constant.ITEM_COUNT_OF_TEMPLATES');

                    $categories_data = array();
                    foreach ($category_list as $key) {

                        $search_text = "%#$key%";
                        $content_list = DB::select('SELECT
                                                          im.id as json_id,
                                                          IF(im.attribute1 != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",im.attribute1),"") as sample_image,
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
                                                          im.catalog_id = scc.catalog_id AND
                                                          cm.id = scc.catalog_id AND
                                                          cm.is_featured = 1 AND
                                                          scc.sub_category_id = ? AND
                                                          isnull(im.original_img) AND
                                                          isnull(im.display_img) AND
                                                          im.search_category LIKE ?
                                                        ORDER BY im.updated_at DESC LIMIT ?, ?', [$this->sub_category_id, $search_text, 0, $item_count_of_templates]);

                        $categories_data[] = array('category_name' => $key, 'content_list' => $content_list);

                    }
                    $total_row = count($category_list);
                    $search_result = [];
                } else {
                    $search_text = "%#$this->search_category%";
                    $total_row_result = DB::select('SELECT count(*) as total
                                                FROM
                                                  images as im,
                                                  catalog_master AS cm,
                                                  sub_category_catalog AS scc
                                                WHERE
                                                  im.catalog_id = scc.catalog_id AND
                                                  cm.id = scc.catalog_id AND
                                                  cm.is_featured = 1 AND
                                                  scc.sub_category_id = ? AND
                                                  isnull(im.original_img) AND
                                                  isnull(im.display_img) AND
                                                  im.search_category LIKE ?
                                                ORDER BY im.updated_at DESC', [$this->sub_category_id, $search_text]);

                    $total_row = $total_row_result[0]->total;

                    $search_result = DB::select('SELECT
                                                  im.id as json_id,
                                                  IF(im.attribute1 != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",im.attribute1),"") as sample_image,
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
                                                  im.catalog_id = scc.catalog_id AND
                                                  cm.id = scc.catalog_id AND
                                                  cm.is_featured = 1 AND
                                                  scc.sub_category_id = ? AND
                                                  isnull(im.original_img) AND
                                                  isnull(im.display_img) AND
                                                  im.search_category LIKE ?
                                                ORDER BY im.updated_at DESC LIMIT ?, ?', [$this->sub_category_id, $search_text, $this->offset, $this->item_count]);
                    $categories_data = [];

                }

                $is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;
                $redis_result = array('total_record' => $total_row, 'is_next_page' => $is_next_page, 'templates_with_categories' => $categories_data, 'template_list' => $search_result);

            }

            $response = Response::json(array('code' => 200, 'message' => 'Templates fetched successfully.', 'cause' => '', 'data' => $redis_result));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch
        (Exception $e) {
            Log::error("getJsonSampleDataFilterBySearchTag : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get templates.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    /*=============================| To get featured backgrounds |=============================*/

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

    /*=============================| To popular and event catalog for poster maker App |=============================*/
    /**
     * @api {post} getPopularAndEventCatalogs  getPopularAndEventCatalogs
     * @apiName getPopularAndEventCatalogs
     * @apiGroup User
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "sub_category_id":182
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Categories fetched successfully.",
     * "cause": "",
     * "data": {
     *"popular_category": [
     *{
     *"catalog_id": 909,
     *"name": "catalog3",
     *"compressed_img": "http://192.168.0.116/photo_editor_lab_backend/image_bucket/compressed/5fa11c8d37cc2_catalog_img_1604394125.jpg",
     *"icon": "http://192.168.0.116/photo_editor_lab_backend/image_bucket/compressed/5fa11c8d37cc2_catalog_img_1604394125.png",
     *"compressed_landscape_img": "http://192.168.0.116/photo_editor_lab_backend/image_bucket/compressed/5fa11c765dcf0_catalog_img_1604394102.jpg",
     *"compressed_portrait_img": "http://192.168.0.116/photo_editor_lab_backend/image_bucket/compressed/5fa11c765dcf0_catalog_img_1604394102.jpg",
     *"webp_original_img": "http://192.168.0.116/photo_editor_lab_backend/image_bucket/compressed/5fa11c8d37cc2_catalog_img_1604394125.webp",
     *"webp_original_landscape_img": "http://192.168.0.116/photo_editor_lab_backend/image_bucket/compressed/5fa11c8d37cc2_catalog_img_1604394125.webp",
     *"webp_original_portrait_img": "http://192.168.0.116/photo_editor_lab_backend/image_bucket/compressed/5fa11c8d37cc2_catalog_img_1604394125.webp",
     *"is_free": 1,
     *"is_featured": 1,
     *"updated_at": "2020-11-03 09:02:05"
     *},
     *{
     *"catalog_id": 908,
     *"name": "catalog2",
     *"compressed_img": "http://192.168.0.116/photo_editor_lab_backend/image_bucket/compressed/5fa11c8d37cc2_catalog_img_1604394125.jpg",
     *"icon": "http://192.168.0.116/photo_editor_lab_backend/image_bucket/compressed/5fa11c8d37cc2_catalog_img_1604394125.png",
     *"compressed_landscape_img": "http://192.168.0.116/photo_editor_lab_backend/image_bucket/compressed/5fa11c765dcf0_catalog_img_1604394102.jpg",
     *"compressed_portrait_img": "http://192.168.0.116/photo_editor_lab_backend/image_bucket/compressed/5fa11c765dcf0_catalog_img_1604394102.jpg",
     *"webp_original_img": "http://192.168.0.116/photo_editor_lab_backend/image_bucket/compressed/5fa11c8d37cc2_catalog_img_1604394125.webp",
     *"webp_original_landscape_img": "http://192.168.0.116/photo_editor_lab_backend/image_bucket/compressed/5fa11c8d37cc2_catalog_img_1604394125.webp",
     *"webp_original_portrait_img": "http://192.168.0.116/photo_editor_lab_backend/image_bucket/compressed/5fa11c8d37cc2_catalog_img_1604394125.webp",
     *"is_free": 1,
     * "is_featured": 1,
     * "updated_at": "2020-11-03 09:01:43"
     * }
     * ],
     *"event_category": [
     *{
     *"catalog_id": 909,
     *"name": "catalog3",
     *"compressed_img": "http://192.168.0.116/photo_editor_lab_backend/image_bucket/compressed/5fa11c8d37cc2_catalog_img_1604394125.jpg",
     *"icon": "http://192.168.0.116/photo_editor_lab_backend/image_bucket/compressed/5fa11c8d37cc2_catalog_img_1604394125.png",
     *"compressed_landscape_img": "http://192.168.0.116/photo_editor_lab_backend/image_bucket/compressed/5fa11c765dcf0_catalog_img_1604394102.jpg",
     *"compressed_portrait_img": "http://192.168.0.116/photo_editor_lab_backend/image_bucket/compressed/5fa11c765dcf0_catalog_img_1604394102.jpg",
     *"webp_original_img": "http://192.168.0.116/photo_editor_lab_backend/image_bucket/compressed/5fa11c8d37cc2_catalog_img_1604394125.webp",
     *"webp_original_landscape_img": "http://192.168.0.116/photo_editor_lab_backend/image_bucket/compressed/5fa11c8d37cc2_catalog_img_1604394125.webp",
     *"webp_original_portrait_img": "http://192.168.0.116/photo_editor_lab_backend/image_bucket/compressed/5fa11c8d37cc2_catalog_img_1604394125.webp",
     *"is_free": 1,
     *"is_featured": 1,
     *"updated_at": "2020-11-03 09:02:05"
     *},
     *{
     *"catalog_id": 908,
     *"name": "catalog2",
     *"compressed_img": "http://192.168.0.116/photo_editor_lab_backend/image_bucket/compressed/5fa11c8d37cc2_catalog_img_1604394125.jpg",
     *"icon": "http://192.168.0.116/photo_editor_lab_backend/image_bucket/compressed/5fa11c8d37cc2_catalog_img_1604394125.png",
     *"compressed_landscape_img": "http://192.168.0.116/photo_editor_lab_backend/image_bucket/compressed/5fa11c765dcf0_catalog_img_1604394102.jpg",
     *"compressed_portrait_img": "http://192.168.0.116/photo_editor_lab_backend/image_bucket/compressed/5fa11c765dcf0_catalog_img_1604394102.jpg",
     *"webp_original_img": "http://192.168.0.116/photo_editor_lab_backend/image_bucket/compressed/5fa11c8d37cc2_catalog_img_1604394125.webp",
     *"webp_original_landscape_img": "http://192.168.0.116/photo_editor_lab_backend/image_bucket/compressed/5fa11c8d37cc2_catalog_img_1604394125.webp",
     *"webp_original_portrait_img": "http://192.168.0.116/photo_editor_lab_backend/image_bucket/compressed/5fa11c8d37cc2_catalog_img_1604394125.webp",
     *"is_free": 1,
     * "is_featured": 1,
     * "updated_at": "2020-11-03 09:01:43"
     * }
     * ],
     *"all_category": [
     *{
     *"catalog_id": 909,
     *"name": "catalog3",
     *"compressed_img": "http://192.168.0.116/photo_editor_lab_backend/image_bucket/compressed/5fa11c8d37cc2_catalog_img_1604394125.jpg",
     *"icon": "http://192.168.0.116/photo_editor_lab_backend/image_bucket/compressed/5fa11c8d37cc2_catalog_img_1604394125.png",
     *"compressed_landscape_img": "http://192.168.0.116/photo_editor_lab_backend/image_bucket/compressed/5fa11c765dcf0_catalog_img_1604394102.jpg",
     *"compressed_portrait_img": "http://192.168.0.116/photo_editor_lab_backend/image_bucket/compressed/5fa11c765dcf0_catalog_img_1604394102.jpg",
     *"webp_original_img": "http://192.168.0.116/photo_editor_lab_backend/image_bucket/compressed/5fa11c8d37cc2_catalog_img_1604394125.webp",
     *"webp_original_landscape_img": "http://192.168.0.116/photo_editor_lab_backend/image_bucket/compressed/5fa11c8d37cc2_catalog_img_1604394125.webp",
     *"webp_original_portrait_img": "http://192.168.0.116/photo_editor_lab_backend/image_bucket/compressed/5fa11c8d37cc2_catalog_img_1604394125.webp",
     *"is_free": 1,
     *"is_featured": 1,
     *"updated_at": "2020-11-03 09:02:05"
     *},
     *{
     *"catalog_id": 908,
     *"name": "catalog2",
     *"compressed_img": "http://192.168.0.116/photo_editor_lab_backend/image_bucket/compressed/5fa11c765dcf0_catalog_img_1604394102.jpg",
     *"icon": "http://192.168.0.116/photo_editor_lab_backend/image_bucket/compressed/5fa11c8d37cc2_catalog_img_1604394125.png",
     *"compressed_landscape_img": "http://192.168.0.116/photo_editor_lab_backend/image_bucket/compressed/5fa11c765dcf0_catalog_img_1604394102.jpg",
     *"compressed_portrait_img": "http://192.168.0.116/photo_editor_lab_backend/image_bucket/compressed/5fa11c765dcf0_catalog_img_1604394102.jpg",
     *"webp_original_img": "http://192.168.0.116/photo_editor_lab_backend/image_bucket/compressed/5fa11c8d37cc2_catalog_img_1604394125.webp",
     *"webp_original_landscape_img": "http://192.168.0.116/photo_editor_lab_backend/image_bucket/compressed/5fa11c8d37cc2_catalog_img_1604394125.webp",
     *"webp_original_portrait_img": "http://192.168.0.116/photo_editor_lab_backend/image_bucket/compressed/5fa11c8d37cc2_catalog_img_1604394125.webp",
     *"is_free": 1,
     * "is_featured": 1,
     * "updated_at": "2020-11-03 09:01:43"
     * }
     * ]
     * }
     * }
     */
    public function getPopularAndEventCatalogs(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('sub_category_id'), $request)) != '')
                return $response;

            $this->sub_category_id = $request->sub_category_id;
            $is_cache_enable = isset($request->is_cache_enable) ? $request->is_cache_enable : 1;
            //$this->item_count = Config::get('constant.ITEM_COUNT_OF_FEATURED_CATALOGS_AND_TEMPLATES');
            //$this->offset = 0;

            $this->time_of_expired_redis_key = Config::get('constant.EXPIRATION_TIME_OF_REDIS_KEY_TO_GET_ALL_FEATURED_TEMPLATES');

            if ($is_cache_enable) {

                $redis_result = Cache::remember("getPopularAndEventCatalogs:$this->sub_category_id", $this->time_of_expired_redis_key, function () {

                    $popular_catalogs = DB::select('SELECT
                                                  ct.id as catalog_id,
                                                  ct.name,
                                                  IF(ct.image != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.image),"") as compressed_img,
                                                  IF(ct.icon != "",CONCAT("' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.icon),"") as icon,
                                                  IF(ct.attribute1 != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.attribute1),"") as webp_original_img,
                                                  IF(ct.landscape_image != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.landscape_image),"") as compressed_landscape_img,
                                                  IF(ct.portrait_image != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.portrait_image),"") as compressed_portrait_img,
                                                  IF(ct.landscape_webp != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.landscape_webp),"") as webp_original_landscape_img,
                                                  IF(ct.portrait_webp != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.portrait_webp),"") as webp_original_portrait_img,
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
                                                  ct.catalog_type = ? AND
                                                  ct.popularity_rate = ? AND
                                                  ct.is_featured = 1
                                                order by ct.updated_at DESC LIMIT ?, ?', [$this->sub_category_id, 1, 5, 0, 5]);

                    //query to get random records by 3:6 ratio on value of "is_free" column within current_date
                    $event_catalogs = DB::select('SELECT
                                                      id as catalog_id,
                                                      name,
                                                      IF(image != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",image),"") as compressed_img,
                                                      IF(icon != "",CONCAT("' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",icon),"") as icon,
                                                      IF(attribute1 != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",attribute1),"") as webp_original_img,
                                                      IF(landscape_image != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",landscape_image),"") as compressed_landscape_img,
                                                      IF(portrait_image != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",portrait_image),"") as compressed_portrait_img,
                                                      IF(landscape_webp != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",landscape_webp),"") as webp_original_landscape_img,
                                                      IF(portrait_webp != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",portrait_webp),"") as webp_original_portrait_img,
                                                      is_free,
                                                      event_date,
                                                      is_featured,
                                                      updated_at
                                                    FROM (
                                                              (SELECT
                                                                   ct.*
                                                                FROM
                                                                   catalog_master as ct,
                                                                   sub_category_catalog as sct
                                                                WHERE
                                                                   sct.sub_category_id = ? AND
                                                                   sct.catalog_id=ct.id AND
                                                                   sct.is_active=1 AND
                                                                   ct.catalog_type = ? AND
                                                                   ct.is_featured = 1 AND
                                                                   ct.event_date >= NOW() AND 
                                                                   ct.event_date <=  NOW() + INTERVAL 45 DAY
                                                                   #DATE_FORMAT(ct.event_date, "%m-%d") >= DATE_FORMAT(NOW(),"%m-%d") AND
                                                                   #DATE_FORMAT(ct.event_date, "%m-%d") <= DATE_FORMAT(DATE_ADD(NOW(), INTERVAL +45 DAY),"%m-%d")
                                                                ORDER BY ct.updated_at DESC)
                                                           UNION
                                                              (SELECT
                                                                    ct.*
                                                                    FROM
                                                                      catalog_master as ct,
                                                                      sub_category_catalog as sct
                                                                    WHERE
                                                                      sct.sub_category_id = ? AND
                                                                      sct.catalog_id=ct.id AND
                                                                      sct.is_active=1 AND
                                                                      ct.catalog_type = ? AND
                                                                      ct.event_date >= NOW() AND 
                                                                      ct.event_date <=  NOW() + INTERVAL 45 DAY AND
                                                                      ct.is_featured = 1
                                                                    ORDER BY ct.updated_at DESC)
                                                                ) derived_table
                                                    ORDER BY catalog_type,DATE_FORMAT(event_date, "%m-%d"),updated_at DESC', [$this->sub_category_id, 2, $this->sub_category_id, 3]);

                    $non_date_category = DB::select('SELECT
                                                  ct.id as catalog_id,
                                                  ct.name,
                                                  IF(ct.image != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.image),"") as compressed_img,
                                                  IF(ct.icon != "",CONCAT("' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.icon),"") as icon,
                                                  IF(ct.attribute1 != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.attribute1),"") as webp_original_img,
                                                  IF(ct.landscape_image != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.landscape_image),"") as compressed_landscape_img,
                                                  IF(ct.portrait_image != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.portrait_image),"") as compressed_portrait_img,
                                                  IF(ct.landscape_webp != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.landscape_webp),"") as webp_original_landscape_img,
                                                  IF(ct.portrait_webp != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.portrait_webp),"") as webp_original_portrait_img,
                                                  ct.is_free,
                                                  ct.is_featured,
                                                  ct.updated_at
                                                FROM
                                                  catalog_master as ct,
                                                  sub_category_catalog as sct
                                                WHERE
                                                  sct.sub_category_id = ? AND
                                                  sct.catalog_id=ct.id AND
                                                  sct.is_active = 1 AND
                                                  ct.catalog_type = ? AND                                                  
                                                  ct.is_featured = 1
                                               ORDER BY ct.popularity_rate ASC,ct.updated_at DESC', [$this->sub_category_id, 4]);

                    $event_catalogs = array_merge($event_catalogs, $non_date_category);

                    $all_category = DB::select('SELECT
                                                  ct.id as catalog_id,
                                                  ct.name,
                                                  IF(ct.image != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.image),"") as compressed_img,
                                                  IF(ct.icon != "",CONCAT("' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.icon),"") as icon,
                                                  IF(ct.landscape_image != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.landscape_image),"") as compressed_landscape_img,
                                                  IF(ct.portrait_image != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.portrait_image),"") as compressed_portrait_img,
                                                  IF(ct.attribute1 != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.attribute1),"") as webp_original_img,
                                                  IF(ct.landscape_webp != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.landscape_webp),"") as webp_original_landscape_img,
                                                  IF(ct.portrait_webp != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.portrait_webp),"") as webp_original_portrait_img,
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

                    return array("popular_category" => $popular_catalogs, 'event_category' => $event_catalogs, 'all_category' => $all_category);
                });

            } else {

                $popular_catalogs = DB::select('SELECT
                                                  ct.id as catalog_id,
                                                  ct.name,
                                                  IF(ct.image != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.image),"") as compressed_img,
                                                  IF(ct.icon != "",CONCAT("' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.icon),"") as icon,
                                                  IF(ct.attribute1 != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.attribute1),"") as webp_original_img,
                                                  IF(ct.landscape_image != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.landscape_image),"") as compressed_landscape_img,
                                                  IF(ct.portrait_image != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.portrait_image),"") as compressed_portrait_img,
                                                  IF(ct.landscape_webp != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.landscape_webp),"") as webp_original_landscape_img,
                                                  IF(ct.portrait_webp != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.portrait_webp),"") as webp_original_portrait_img,
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
                                                  ct.catalog_type = ? AND
                                                  ct.popularity_rate = ? AND
                                                  ct.is_featured = 1
                                                order by ct.updated_at DESC LIMIT ?, ?', [$this->sub_category_id, 1, 5, 0, 5]);

                //query to get random records by 3:6 ratio on value of "is_free" column within current_date
                $event_catalogs = DB::select('SELECT
                                                      id as catalog_id,
                                                      name,
                                                      IF(image != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",image),"") as compressed_img,
                                                      IF(icon != "",CONCAT("' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",icon),"") as icon,
                                                      IF(attribute1 != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",attribute1),"") as webp_original_img,
                                                      IF(landscape_image != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",landscape_image),"") as compressed_landscape_img,
                                                      IF(portrait_image != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",portrait_image),"") as compressed_portrait_img,
                                                      IF(landscape_webp != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",landscape_webp),"") as webp_original_landscape_img,
                                                      IF(portrait_webp != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",portrait_webp),"") as webp_original_portrait_img,
                                                      is_free,
                                                      event_date,
                                                      is_featured,
                                                      updated_at
                                                    FROM (
                                                              (SELECT
                                                                   ct.*
                                                                FROM
                                                                   catalog_master as ct,
                                                                   sub_category_catalog as sct
                                                                WHERE
                                                                   sct.sub_category_id = ? AND
                                                                   sct.catalog_id=ct.id AND
                                                                   sct.is_active=1 AND
                                                                   ct.catalog_type = ? AND
                                                                   ct.is_featured = 1 AND
                                                                   ct.event_date >= NOW() AND 
                                                                   ct.event_date <=  NOW() + INTERVAL 45 DAY
                                                                   #DATE_FORMAT(ct.event_date, "%m-%d") >= DATE_FORMAT(NOW(),"%m-%d") AND
                                                                   #DATE_FORMAT(ct.event_date, "%m-%d") <= DATE_FORMAT(DATE_ADD(NOW(), INTERVAL +45 DAY),"%m-%d")
                                                                ORDER BY ct.updated_at DESC)
                                                           UNION
                                                              (SELECT
                                                                    ct.*
                                                                    FROM
                                                                      catalog_master as ct,
                                                                      sub_category_catalog as sct
                                                                    WHERE
                                                                      sct.sub_category_id = ? AND
                                                                      sct.catalog_id=ct.id AND
                                                                      sct.is_active=1 AND
                                                                      ct.catalog_type = ? AND
                                                                      ct.event_date >= NOW() AND 
                                                                      ct.event_date <=  NOW() + INTERVAL 45 DAY AND
                                                                      ct.is_featured = 1
                                                                    ORDER BY ct.updated_at DESC)
                                                                ) derived_table
                                                    ORDER BY catalog_type,DATE_FORMAT(event_date, "%m-%d"),updated_at DESC', [$this->sub_category_id, 2, $this->sub_category_id, 3]);

                $non_date_category = DB::select('SELECT
                                                  ct.id as catalog_id,
                                                  ct.name,
                                                  IF(ct.image != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.image),"") as compressed_img,
                                                  IF(ct.icon != "",CONCAT("' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.icon),"") as icon,
                                                  IF(ct.attribute1 != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.attribute1),"") as webp_original_img,
                                                  IF(ct.landscape_image != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.landscape_image),"") as compressed_landscape_img,
                                                  IF(ct.portrait_image != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.portrait_image),"") as compressed_portrait_img,
                                                  IF(ct.landscape_webp != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.landscape_webp),"") as webp_original_landscape_img,
                                                  IF(ct.portrait_webp != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.portrait_webp),"") as webp_original_portrait_img,
                                                  ct.is_free,
                                                  ct.is_featured,
                                                  ct.updated_at
                                                FROM
                                                  catalog_master as ct,
                                                  sub_category_catalog as sct
                                                WHERE
                                                  sct.sub_category_id = ? AND
                                                  sct.catalog_id=ct.id AND
                                                  sct.is_active = 1 AND
                                                  ct.catalog_type = ? AND                                                  
                                                  ct.is_featured = 1
                                               ORDER BY ct.popularity_rate ASC,ct.updated_at DESC', [$this->sub_category_id, 4]);

                $event_catalogs = array_merge($event_catalogs, $non_date_category);

                $all_category = DB::select('SELECT
                                                  ct.id as catalog_id,
                                                  ct.name,
                                                  IF(ct.image != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.image),"") as compressed_img,
                                                  IF(ct.icon != "",CONCAT("' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.icon),"") as icon,
                                                  IF(ct.landscape_image != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.landscape_image),"") as compressed_landscape_img,
                                                  IF(ct.portrait_image != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.portrait_image),"") as compressed_portrait_img,
                                                  IF(ct.attribute1 != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.attribute1),"") as webp_original_img,
                                                  IF(ct.landscape_webp != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.landscape_webp),"") as webp_original_landscape_img,
                                                  IF(ct.portrait_webp != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",ct.portrait_webp),"") as webp_original_portrait_img,
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

                $redis_result = array("popular_category" => $popular_catalogs, 'event_category' => $event_catalogs, 'all_category' => $all_category);

            }

            $response = Response::json(array('code' => 200, 'message' => 'Categories fetched successfully.', 'cause' => '', 'data' => $redis_result));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            Log::error("getPopularAndEventCatalogs : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get categories.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    /*=============================| Sub functions |=============================*/
    public function deleteAllRedisKeys($key_name)
    {
        try {
            $is_success = Redis::del(array_merge(Redis::keys("pel:$key_name*"), ['']));
            return $is_success;

        } catch (Exception $e) {
            Log::error("deleteAllRedisKeys : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            return 0;
        }
    }

    public function deleteMultipleRedisKeys($key_name)
    {
        try {
            foreach ($key_name as $i => $name){
                Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":$name*"), ['']));
            }
            return 1;

        } catch (Exception $e) {
            Log::error("deleteMultipleRedisKeys : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            return 0;
        }
    }

    public function getAllRedisKeys($key_name)
    {
        try {
            $redis_keys = Redis::keys("pel:$key_name*");
            return $redis_keys;

        } catch (Exception $e) {
            Log::error("getAllRedisKeys : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            return json_decode('{}');
        }
    }

    public function getRedisKeyValue($key_name)
    {
        try {
            $redis_value = Cache::get($key_name);
            return $redis_value;

        } catch (Exception $e) {
            Log::error("getRedisKeyValue : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            return json_decode('{}');
        }
    }

    /*
    Purpose : To get disable cache template by user searches (Get all template by requested tags).
    Description : This method compulsory take 4 argument as parameter.(if any argument is optional then define it here).
    Return : return template detail if success otherwise error with specific status code
    */
    public function searchTemplatesByDisableCache($search_category, $sub_category_id, $offset, $item_count, $is_featured, $catalog_id = '', $fail_over_sub_category_id = '')
    {
        try{
            $this->db_sub_category_id = $this->sub_category_id = $sub_category_id;
            $this->fail_over_sub_category_id = $fail_over_sub_category_id;
            $this->db_search_category = $this->search_category = $search_category;
            $this->offset = $offset;
            $this->item_count = $item_count;
            $this->is_search_category_changed = 0;
            $this->is_featured = $is_featured;
            $this->catalog_id = $catalog_id;

            if ($this->catalog_id) {
                $this->order_by = ' ORDER BY search_text DESC, FIELD(im.catalog_id, "'. $this->catalog_id .'") DESC, im.updated_at DESC';
                //$this->order_by = ' ORDER BY FIELD(im.catalog_id, "'. $this->catalog_id .'") DESC, search_text DESC, im.updated_at DESC';
            } else {
                $this->order_by = ' ORDER BY search_text DESC, im.updated_at DESC';
            }

            run_same_query:
            $code = 200;
            $message = "Templates fetched successfully.";

            $total_row_result = DB::select('SELECT 
                                                COUNT(DISTINCT(im.id)) AS total
                                            FROM
                                                images AS im,
                                                catalog_master AS cm,
                                                sub_category_catalog AS scc
                                            WHERE
                                                im.is_active = 1 AND
                                                im.catalog_id = scc.catalog_id AND
                                                cm.is_featured = ? AND
                                                cm.id = scc.catalog_id AND
                                                scc.sub_category_id IN (' . $this->sub_category_id . ') AND
                                                ISNULL(im.original_img) AND
                                                ISNULL(im.display_img) AND
                                                (MATCH(im.search_category) AGAINST("' . $this->search_category . '") OR 
                                                MATCH(im.search_category) AGAINST(REPLACE(concat("' . $this->search_category . '"," ")," ","* ") IN BOOLEAN MODE)) ', [$this->is_featured]);
            $total_row = $total_row_result[0]->total;

            if($total_row) {
                $search_result = DB::select('SELECT
                                            DISTINCT im.id AS json_id,
                                            im.catalog_id,
                                            IF(im.image != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",im.image),"") AS compressed_img,
                                            IF(im.attribute1 != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",im.attribute1),"") AS sample_image,
                                            IF(im.attribute1 != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",im.attribute1),"") AS webp_original_img,
                                            IF(im.content_type = ' . Config::get('constant.CONTENT_TYPE_FOR_BEFORE_AFTER_IMAGE') . ' AND im.attribute1 != "",CONCAT("' . Config::get('constant.WEBP_THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '","after_image_",im.attribute1),"") AS after_image,
                                            IF(im.content_type = ' . Config::get('constant.CONTENT_TYPE_FOR_BEFORE_AFTER_IMAGE') . ' AND im.attribute1 != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '","after_image_",im.attribute1),"") AS webp_original_after_img,
                                            IF(im.content_type = ' . Config::get('constant.CONTENT_TYPE_FOR_SAMPLE_IMAGE_GIF') . ' AND im.image != "",CONCAT("' . Config::get('constant.ORIGINAL_VIDEO_DIRECTORY_OF_DIGITAL_OCEAN') . '",SUBSTRING_INDEX(im.image,".",1),".gif"),"") AS sample_gif,
                                            IF(im.cover_webp_img != "", CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '", im.cover_webp_img), "") AS cover_webp_img,
                                            COALESCE(im.cover_img_height,0) AS cover_img_height,
                                            COALESCE(im.cover_img_width,0) AS cover_img_width,
                                            im.is_free,
                                            im.is_ios_free,
                                            im.is_featured,
                                            im.is_portrait,
                                            im.search_category,
                                            im.template_name,
                                            COALESCE(im.height,0) AS height,
                                            COALESCE(im.width,0) AS width,
                                            COALESCE(original_img_height,0) AS original_img_height,
                                            COALESCE(original_img_width,0) AS original_img_width,
                                            COALESCE(im.multiple_images,"") AS multiple_images,
                                            COALESCE(im.json_pages_sequence,"") AS pages_sequence,
                                            COALESCE(LENGTH(im.json_pages_sequence) - LENGTH(REPLACE(im.json_pages_sequence, ",","")) + 1,1) AS total_pages,
                                            im.content_type,
                                            im.updated_at,
                                            MATCH(im.search_category) AGAINST("' . $this->search_category . '") +
                                            MATCH(im.search_category) AGAINST(REPLACE(concat("' . $this->search_category . '"," ")," ","* ") IN BOOLEAN MODE) AS search_text 
                                        FROM
                                            images AS im,
                                            catalog_master AS cm,
                                            sub_category_catalog AS scc
                                        WHERE
                                            im.is_active = 1 AND
                                            im.catalog_id = scc.catalog_id AND
                                            cm.id = scc.catalog_id AND
                                            cm.is_featured = ? AND
                                            scc.sub_category_id IN (' . $this->sub_category_id . ') AND
                                            ISNULL(im.original_img) AND
                                            ISNULL(im.display_img) AND
                                            (MATCH(im.search_category) AGAINST("' . $this->search_category . '") OR 
                                            MATCH(im.search_category) AGAINST(REPLACE(concat("' . $this->search_category . '"," ")," ","* ") IN BOOLEAN MODE))
                                        '. $this->order_by .' LIMIT ?, ?', [$this->is_featured, $this->offset, $this->item_count]);

                $is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;
                $search_result = array('total_record' => $total_row, 'is_next_page' => $is_next_page, 'result' => $search_result);

                return array('code' => $code, 'message' => $message, 'cause' => '', 'data' => $search_result);
            }


            if (!$total_row && !$this->is_search_category_changed) {

                $this->is_search_category_changed = 1;

                $translate_data = $this->translateLanguage($this->search_category, "en");

                if(isset($translate_data['data']['text']) && $translate_data['data']['text'] && $translate_data['data']['text'] != $this->search_category) {
                    $this->search_category = $translate_data['data']['text'];
                    goto run_same_query;
                }
            }

            if(!$total_row && $this->is_search_category_changed && $this->fail_over_sub_category_id) {
                $this->sub_category_id = $this->fail_over_sub_category_id;
                $this->search_category = $this->search_category . " " . $this->db_search_category;
                $this->fail_over_sub_category_id = NULL;
                goto run_same_query;
            }

            if (!$total_row) {

                $code = 427;
                $message = "Sorry, we couldn't find any templates for '$this->db_search_category', but we found some other templates you might like:";

                $total_row_result = DB::select('SELECT
                                                    COUNT(DISTINCT(im.id)) AS total
                                                FROM
                                                    images AS im,
                                                    catalog_master AS cm,
                                                    sub_category_catalog AS scc
                                                WHERE
                                                    im.is_active = 1 AND
                                                    im.catalog_id = scc.catalog_id AND
                                                    cm.id = scc.catalog_id AND
                                                    scc.sub_category_id IN (' . $this->sub_category_id . ') AND
                                                    cm.is_featured = ? AND
                                                    ISNULL(im.original_img) AND
                                                    ISNULL(im.display_img) ', [$this->is_featured]);
                $total_row = $total_row_result[0]->total;

                $search_result = DB::select('SELECT
                                                    DISTINCT im.id AS json_id,
                                                    IF(im.image != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",im.image),"") AS compressed_img,
                                                    IF(im.attribute1 != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",im.attribute1),"") AS sample_image,
                                                    IF(im.attribute1 != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",im.attribute1),"") AS webp_original_img,
                                                    IF(im.content_type = ' . Config::get('constant.CONTENT_TYPE_FOR_BEFORE_AFTER_IMAGE') . ' AND im.attribute1 != "",CONCAT("' . Config::get('constant.WEBP_THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '","after_image_",im.attribute1),"") AS after_image,
                                                    IF(im.content_type = ' . Config::get('constant.CONTENT_TYPE_FOR_BEFORE_AFTER_IMAGE') . ' AND im.attribute1 != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '","after_image_",im.attribute1),"") AS webp_original_after_img,
                                                    IF(im.content_type = ' . Config::get('constant.CONTENT_TYPE_FOR_SAMPLE_IMAGE_GIF') . ' AND im.image != "",CONCAT("' . Config::get('constant.ORIGINAL_VIDEO_DIRECTORY_OF_DIGITAL_OCEAN') . '",SUBSTRING_INDEX(im.image,".",1),".gif"),"") AS sample_gif,
                                                    IF(im.cover_webp_img != "", CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '", im.cover_webp_img), "") AS cover_webp_img,
                                                    COALESCE(im.cover_img_height,0) AS cover_img_height,
                                                    COALESCE(im.cover_img_width,0) AS cover_img_width,
                                                    im.is_free,
                                                    im.is_ios_free,
                                                    im.is_featured,
                                                    im.is_portrait,
                                                    im.search_category,
                                                    im.template_name,
                                                    COALESCE(im.height,0) AS height,
                                                    COALESCE(im.width,0) AS width,
                                                    COALESCE(original_img_height,0) AS original_img_height,
                                                    COALESCE(original_img_width,0) AS original_img_width,
                                                    COALESCE(im.multiple_images,"") AS multiple_images,
                                                    COALESCE(im.json_pages_sequence,"") AS pages_sequence,
                                                    COALESCE(LENGTH(im.json_pages_sequence) - LENGTH(REPLACE(im.json_pages_sequence, ",","")) + 1,1) AS total_pages,
                                                    im.content_type,
                                                    im.updated_at
                                                FROM
                                                    images AS im,
                                                    catalog_master AS cm,
                                                    sub_category_catalog AS scc
                                                WHERE
                                                    im.is_active = 1 AND
                                                    im.catalog_id = scc.catalog_id AND
                                                    cm.id = scc.catalog_id AND
                                                    cm.is_featured = ? AND
                                                    scc.sub_category_id IN (' . $this->sub_category_id . ') AND
                                                    ISNULL(im.original_img) AND
                                                    ISNULL(im.display_img)
                                                ORDER BY im.updated_at DESC LIMIT ?, ?', [$this->is_featured, $this->offset, $this->item_count]);

                $is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;
                $search_result = array('total_record' => $total_row, 'is_next_page' => $is_next_page, 'result' => $search_result);
                return array('code' => $code, 'message' => $message, 'cause' => '', 'data' => $search_result);
            }

        } catch (Exception $e) {
            Log::error("searchTemplatesByDisableCache : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            return array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get template.', 'cause' => $e->getMessage(), 'data' => json_decode("{}"));
        }
    }

    /*
    Purpose : To get enable cache template by user searches (Get all template by requested tags).
    Description : This method compulsory take 4 argument as parameter.(if any argument is optional then define it here).
    Return : return template detail if success otherwise error with specific status code
    */
    public function searchTemplatesBySearchCategory($search_category, $sub_category_id, $offset, $item_count, $is_featured, $catalog_id = '', $fail_over_sub_category_id = '')
    {
        try{
            $this->db_sub_category_id = $this->sub_category_id = $sub_category_id;
            $this->fail_over_sub_category_id = $fail_over_sub_category_id;
            $this->db_search_category = $this->search_category = $search_category;
            $this->offset = $offset;
            $this->item_count = $item_count;
            $this->is_search_category_changed = 0;
            $this->is_featured = $is_featured;
            $this->catalog_id = $catalog_id;

            run_same_query:
            $redis_result = Cache::remember("key:searchCardsBySubCategoryId:$this->sub_category_id:$this->search_category:$this->is_featured:$this->offset:$this->item_count:$this->catalog_id", config('constant.CACHE_TIME_6_HOUR'), function () {

                $code = 200;
                $message = "Templates fetched successfully.";
                $search_result = [];

                if ($this->catalog_id) {
                    $this->order_by = ' ORDER BY search_text DESC, FIELD(im.catalog_id, "'. $this->catalog_id .'") DESC, im.updated_at DESC';
                    //$this->order_by = ' ORDER BY FIELD(im.catalog_id, "'. $this->catalog_id .'") DESC, search_text DESC, im.updated_at DESC';
                } else {
                    $this->order_by = ' ORDER BY search_text DESC, im.updated_at DESC';
                }

                $total_row_result = DB::select('SELECT 
                                                    COUNT(DISTINCT(im.id)) AS total
                                                FROM
                                                    images AS im,
                                                    catalog_master AS cm,
                                                    sub_category_catalog AS scc
                                                WHERE
                                                    im.is_active = 1 AND
                                                    im.catalog_id = scc.catalog_id AND
                                                    cm.is_featured = ? AND
                                                    cm.id = scc.catalog_id AND
                                                    scc.sub_category_id IN (' . $this->sub_category_id . ') AND
                                                    ISNULL(im.original_img) AND
                                                    ISNULL(im.display_img) AND
                                                    (MATCH(im.search_category) AGAINST ("' . $this->search_category . '") OR 
                                                    MATCH(im.search_category) AGAINST (REPLACE(CONCAT("' . $this->search_category . '"," ")," ","* ") IN BOOLEAN MODE)) ', [$this->is_featured]);
                $total_row = $total_row_result[0]->total;

                if($total_row) {
                    $search_result = DB::select('SELECT
                                                DISTINCT im.id AS json_id,
                                                im.catalog_id,
                                                IF(im.image != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",im.image),"") AS compressed_img,
                                                IF(im.attribute1 != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",im.attribute1),"") AS sample_image,
                                                IF(im.attribute1 != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",im.attribute1),"") AS webp_original_img,
                                                IF(im.content_type = ' . Config::get('constant.CONTENT_TYPE_FOR_BEFORE_AFTER_IMAGE') . ' AND im.attribute1 != "",CONCAT("' . Config::get('constant.WEBP_THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '","after_image_",im.attribute1),"") AS after_image,
                                                IF(im.content_type = ' . Config::get('constant.CONTENT_TYPE_FOR_BEFORE_AFTER_IMAGE') . ' AND im.attribute1 != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '","after_image_",im.attribute1),"") AS webp_original_after_img,
                                                IF(im.content_type = ' . Config::get('constant.CONTENT_TYPE_FOR_SAMPLE_IMAGE_GIF') . ' AND im.image != "",CONCAT("' . Config::get('constant.ORIGINAL_VIDEO_DIRECTORY_OF_DIGITAL_OCEAN') . '",SUBSTRING_INDEX(im.image,".",1),".gif"),"") AS sample_gif,
                                                IF(im.cover_webp_img != "", CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '", im.cover_webp_img), "") AS cover_webp_img,
                                                COALESCE(im.cover_img_height,0) AS cover_img_height,
                                                COALESCE(im.cover_img_width,0) AS cover_img_width,
                                                im.is_free,
                                                im.is_ios_free,
                                                im.is_featured,
                                                im.is_portrait,
                                                im.search_category,
                                                im.template_name,
                                                COALESCE(im.height,0) AS height,
                                                COALESCE(im.width,0) AS width,
                                                COALESCE(original_img_height,0) AS original_img_height,
                                                COALESCE(original_img_width,0) AS original_img_width,
                                                COALESCE(im.multiple_images,"") AS multiple_images,
                                                COALESCE(im.json_pages_sequence,"") AS pages_sequence,
                                                COALESCE(LENGTH(im.json_pages_sequence) - LENGTH(REPLACE(im.json_pages_sequence, ",","")) + 1,1) AS total_pages,
                                                im.content_type,
                                                im.updated_at,
                                                MATCH(im.search_category) AGAINST("' . $this->search_category . '") +
                                                MATCH(im.search_category) AGAINST(REPLACE(concat("' . $this->search_category . '"," ")," ","* ") IN BOOLEAN MODE) AS search_text 
                                            FROM
                                                images AS im,
                                                catalog_master AS cm,
                                                sub_category_catalog AS scc
                                            WHERE
                                                im.is_active = 1 AND
                                                im.catalog_id = scc.catalog_id AND
                                                cm.id = scc.catalog_id AND
                                                cm.is_featured = ? AND
                                                scc.sub_category_id IN (' . $this->sub_category_id . ') AND
                                                ISNULL(im.original_img) AND
                                                ISNULL(im.display_img) AND
                                                (MATCH(im.search_category) AGAINST("' . $this->search_category . '") OR 
                                                MATCH(im.search_category) AGAINST(REPLACE(concat("' . $this->search_category . '"," ")," ","* ") IN BOOLEAN MODE))
                                            '. $this->order_by .' LIMIT ?, ?', [$this->is_featured, $this->offset, $this->item_count]);

                }

                $is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;
                $search_result = array('total_record' => $total_row, 'is_next_page' => $is_next_page, 'result' => $search_result);
                return array('code' => $code, 'message' => $message, 'cause' => '', 'data' => $search_result);

            });

            if (!$redis_result['data']['total_record'] && !$this->is_search_category_changed) {

                Redis::del("pel:searchCardsBySubCategoryId:$this->sub_category_id:$this->search_category:$this->is_featured:$this->offset:$this->item_count:$this->catalog_id");

                $this->is_search_category_changed = 1;

                $translate_data = $this->translateLanguage($this->search_category, "en");

                if(isset($translate_data['data']['text']) && $translate_data['data']['text'] && $translate_data['data']['text'] != $this->search_category) {
                    $this->search_category = $translate_data['data']['text'];
                    goto run_same_query;
                }
            }

            if($this->is_search_category_changed && !$redis_result['data']['total_record'] && $this->fail_over_sub_category_id){

                $this->sub_category_id = $this->fail_over_sub_category_id;
                $this->search_category = $this->search_category . " " . $this->db_search_category;
                $this->fail_over_sub_category_id = NULL;
                goto run_same_query;
            }

            if (!$redis_result['data']['total_record']) {

                Redis::del("pel:searchCardsBySubCategoryId:$this->sub_category_id:$this->search_category:$this->is_featured:$this->offset:$this->item_count:$this->catalog_id");
                $redis_result = Cache::remember("default:searchCardsBySubCategoryId:$this->db_sub_category_id:$this->is_featured:$this->offset:$this->item_count", config('constant.CACHE_TIME_7_DAYS'), function () {

                    $code = 427;
                    $message = "Sorry, we couldn't find any templates for '$this->db_search_category', but we found some other templates you might like:";
                    $search_result = [];

                    $total_row_result = DB::select('SELECT 
                                                        COUNT(DISTINCT(im.id)) AS total
                                                    FROM
                                                        images AS im,
                                                        catalog_master AS cm,
                                                        sub_category_catalog AS scc
                                                    WHERE
                                                        im.is_active = 1 AND
                                                        im.catalog_id = scc.catalog_id AND
                                                        cm.id = scc.catalog_id AND
                                                        scc.sub_category_id IN (' . $this->db_sub_category_id . ') AND
                                                        cm.is_featured = ? AND
                                                        ISNULL(im.original_img) AND
                                                        ISNULL(im.display_img) ', [$this->is_featured]);
                    $total_row = $total_row_result[0]->total;

                    if($total_row){
                        $search_result = DB::select('SELECT
                                                        DISTINCT im.id AS json_id,
                                                        IF(im.image != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",im.image),"") AS compressed_img,
                                                        IF(im.attribute1 != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",im.attribute1),"") AS sample_image,
                                                        IF(im.attribute1 != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",im.attribute1),"") AS webp_original_img,
                                                        IF(im.content_type = ' . Config::get('constant.CONTENT_TYPE_FOR_BEFORE_AFTER_IMAGE') . ' AND im.attribute1 != "",CONCAT("' . Config::get('constant.WEBP_THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '","after_image_",im.attribute1),"") AS after_image,
                                                        IF(im.content_type = ' . Config::get('constant.CONTENT_TYPE_FOR_BEFORE_AFTER_IMAGE') . ' AND im.attribute1 != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '","after_image_",im.attribute1),"") AS webp_original_after_img,
                                                        IF(im.content_type = ' . Config::get('constant.CONTENT_TYPE_FOR_SAMPLE_IMAGE_GIF') . ' AND im.image != "",CONCAT("' . Config::get('constant.ORIGINAL_VIDEO_DIRECTORY_OF_DIGITAL_OCEAN') . '",SUBSTRING_INDEX(im.image,".",1),".gif"),"") AS sample_gif,
                                                        IF(im.cover_webp_img != "", CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '", im.cover_webp_img), "") AS cover_webp_img,
                                                        COALESCE(im.cover_img_height,0) AS cover_img_height,
                                                        COALESCE(im.cover_img_width,0) AS cover_img_width,
                                                        im.is_free,
                                                        im.is_ios_free,
                                                        im.is_featured,
                                                        im.is_portrait,
                                                        im.search_category,
                                                        im.template_name,
                                                        COALESCE(im.height,0) AS height,
                                                        COALESCE(im.width,0) AS width,
                                                        COALESCE(original_img_height,0) AS original_img_height,
                                                        COALESCE(original_img_width,0) AS original_img_width,
                                                        COALESCE(im.multiple_images,"") AS multiple_images,
                                                        COALESCE(im.json_pages_sequence,"") AS pages_sequence,
                                                        COALESCE(LENGTH(im.json_pages_sequence) - LENGTH(REPLACE(im.json_pages_sequence, ",","")) + 1,1) AS total_pages,
                                                        im.content_type,
                                                        im.updated_at
                                                    FROM
                                                        images AS im,
                                                        catalog_master AS cm,
                                                        sub_category_catalog AS scc
                                                    WHERE
                                                        im.is_active = 1 AND
                                                        im.catalog_id = scc.catalog_id AND
                                                        cm.id = scc.catalog_id AND
                                                        cm.is_featured = ? AND
                                                        scc.sub_category_id IN (' . $this->db_sub_category_id . ') AND
                                                        ISNULL(im.original_img) AND
                                                        ISNULL(im.display_img)
                                                    ORDER BY im.updated_at DESC LIMIT ?, ?', [$this->is_featured, $this->offset, $this->item_count]);

                    }

                    $is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;
                    $search_result = array('total_record' => $total_row, 'is_next_page' => $is_next_page, 'result' => $search_result);

                    return array('code' => $code, 'message' => $message, 'cause' => '', 'data' => $search_result);

                });
                $redis_result['message'] = "Sorry, we couldn't find any templates for '$this->db_search_category', but we found some other templates you might like:";
            }

            return $redis_result;

        } catch (Exception $e) {
            Log::error("searchTemplatesBySearchCategory : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            return array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get template.', 'cause' => $e->getMessage(), 'data' => json_decode("{}"));
        }
    }

    public function searchTemplatesBySearchCategory_V2($search_category, $sub_category_id, $offset, $page, $item_count, $is_featured, $fail_over_sub_category_id, $bcp_language_code, $is_cache_enable)
    {
        try {
            $this->db_sub_category_id = $this->sub_category_id = $sub_category_id;
            $this->bcp_language_code = $bcp_language_code;
            $this->fail_over_sub_category_id = $fail_over_sub_category_id;
            $this->search_category = $search_category; // ફૂડ
            $this->offset = $offset;
            $this->page = $page;
            $this->item_count = $item_count;
            $this->is_featured = $is_featured;

            $this->translated_search_category = "";
            $this->is_search_keyword_already_translated = 0;
            $this->current_search_keyword = $this->search_category;
            $this->is_cache_enable = $is_cache_enable;
            $this->cache_key_name = "direct_search:searchCardsBySubCategoryIdWithTranslate";
            $this->cache_key_time = Config::get('constant.CACHE_TIME_24_HOUR');


//            Log::info('PHASE_1: Direct Search Keyword');
            run_same_query:
//            Log::info('CURRENT SEARCH TAG : ', ['CURRENT SEARCH TAG' => $this->current_search_keyword]);

            if ($this->is_cache_enable == 1) {
//                Log::info('IS CACHE ENABLE : TRUE');
                $this->cache_key_name = "direct_search:searchCardsBySubCategoryIdWithTranslate";
                $this->cache_key_time = Config::get('constant.CACHE_TIME_24_HOUR');
            } else {
//                Log::info('IS CACHE ENABLE : FALSE');
                $this->cache_key_name = "direct_search:searchCardsBySubCategoryIdWithTranslate_without_cache";
                $this->cache_key_time = 0;
            }

            $redis_result = Cache::remember("$this->cache_key_name:$this->sub_category_id:$this->current_search_keyword:$this->is_featured:$this->offset:$this->item_count", $this->cache_key_time, function () {

                $code = 200;
                $message = "Templates fetched successfully.";
                $search_result = [];

//                Log::info('DB hit For direct search');

                $total_row_result = DB::select('SELECT 
                                                    COUNT(DISTINCT(im.id)) AS total
                                                FROM
                                                    images AS im,
                                                    catalog_master AS cm,
                                                    sub_category_catalog AS scc
                                                WHERE
                                                    im.is_active = 1 AND
                                                    im.catalog_id = scc.catalog_id AND
                                                    cm.is_featured = ? AND
                                                    cm.id = scc.catalog_id AND
                                                    scc.sub_category_id IN (' . $this->sub_category_id . ') AND
                                                    ISNULL(im.original_img) AND
                                                    ISNULL(im.display_img) AND
                                                    (MATCH(im.search_category) AGAINST("' . $this->current_search_keyword . '") OR 
                                                    MATCH(im.search_category) AGAINST(REPLACE(concat("' . $this->current_search_keyword . '"," ")," ","* ") IN BOOLEAN MODE)) ', [$this->is_featured]);
                $total_row = $total_row_result[0]->total;

                //if search count is available, then go to search image using search_category
                if ($total_row) {

                    $search_result = DB::select('SELECT
                                                DISTINCT im.id AS json_id,
                                                IF(im.attribute1 != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",im.attribute1),"") AS sample_image,
                                                IF(im.attribute1 != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",im.attribute1),"") AS webp_original_img,
                                                IF(im.content_type = ' . Config::get('constant.CONTENT_TYPE_FOR_BEFORE_AFTER_IMAGE') . ' AND im.attribute1 != "",CONCAT("' . Config::get('constant.WEBP_THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '","after_image_",im.attribute1),"") AS after_image,
                                                IF(im.content_type = ' . Config::get('constant.CONTENT_TYPE_FOR_BEFORE_AFTER_IMAGE') . ' AND im.attribute1 != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '","after_image_",im.attribute1),"") AS webp_original_after_img,
                                                IF(im.content_type = ' . Config::get('constant.CONTENT_TYPE_FOR_SAMPLE_IMAGE_GIF') . ' AND im.image != "",CONCAT("' . Config::get('constant.ORIGINAL_VIDEO_DIRECTORY_OF_DIGITAL_OCEAN') . '",SUBSTRING_INDEX(im.image,".",1),".gif"),"") AS sample_gif,
                                                im.is_free,
                                                im.is_featured,
                                                im.is_portrait,
                                                COALESCE(im.height,0) AS height,
                                                COALESCE(im.width,0) AS width,
                                                COALESCE(im.multiple_images,"") AS multiple_images,
                                                COALESCE(im.json_pages_sequence,"") AS pages_sequence,
                                                COALESCE(LENGTH(im.json_pages_sequence) - LENGTH(REPLACE(im.json_pages_sequence, ",","")) + 1,1) AS total_pages,
                                                im.content_type,
                                                im.updated_at,
                                                MATCH(im.search_category) AGAINST("' . $this->current_search_keyword . '") +
                                                MATCH(im.search_category) AGAINST(REPLACE(concat("' . $this->current_search_keyword . '"," ")," ","* ") IN BOOLEAN MODE) AS search_text 
                                            FROM
                                                images AS im,
                                                catalog_master AS cm,
                                                sub_category_catalog AS scc
                                            WHERE
                                                im.is_active = 1 AND
                                                im.catalog_id = scc.catalog_id AND
                                                cm.id = scc.catalog_id AND
                                                cm.is_featured = ? AND
                                                scc.sub_category_id IN (' . $this->sub_category_id . ') AND
                                                ISNULL(im.original_img) AND
                                                ISNULL(im.display_img) AND
                                                (MATCH(im.search_category) AGAINST("' . $this->current_search_keyword . '") OR 
                                                MATCH(im.search_category) AGAINST(REPLACE(concat("' . $this->current_search_keyword . '"," ")," ","* ") IN BOOLEAN MODE))
                                            ORDER BY search_text DESC,im.updated_at DESC LIMIT ?, ?', [$this->is_featured, $this->offset, $this->item_count]);
                }

                $is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;
                $search_result = array('total_record' => $total_row, 'is_next_page' => $is_next_page, 'result' => $search_result);

                return array('code' => $code, 'message' => $message, 'cause' => '', 'data' => $search_result);
            });

//            Log::info('Redis result', $redis_result);



//            if (!$redis_result['data']['result']) {
//                Log::info('SEARCH RESULT NOT FOUND');
//            } else {
//                Log::info('SEARCH RESULT FOUND');
//            }

            if ($redis_result['data']['result']) {
//                Log::info('RESULT FOUND!');
                if ($this->is_search_keyword_already_translated == 1) {
                    $this->reportSuccessSearchKeywordResult($this->search_category, $this->translated_search_category, $this->sub_category_id);
                }

                return $redis_result;

            } elseif ($this->page > 1 && !$redis_result['data']['is_next_page']) {
//                Log::info('NEXT PAGE RESULT NOT FOUND!');
                return $redis_result;
            } elseif ($this->is_search_keyword_already_translated == 1) {
//                Log::info('RESULT NOT FOUND! & is_search_keyword_already_translated = 1');
                // If Result not get after detect & translate then report keyword & goto fail_over_sub_category_id
                $this->reportFailSearchKeywordResult($this->search_category, $this->translated_search_category, $this->sub_category_id);

                if ($this->fail_over_sub_category_id) {

//                    Log::info('FAIL_OVER : RESULT NOT FOUND! & is_search_keyword_already_translated = 1');

                    $this->sub_category_id = $this->fail_over_sub_category_id;
                    $this->current_search_keyword = $this->translated_search_category . " " . $this->search_category ;
                    $this->fail_over_sub_category_id = NULL;
                    goto run_same_query;
                } else {
                    // Give 427 result
//                    Log::info('RETURN 427 : RESULT NOT FOUND! & is_search_keyword_already_translated = 1');
                    $redis_result = $this->giveDefaultSearchResult($this->sub_category_id, $this->search_category, $this->is_featured, $this->offset, $this->item_count, $this->db_sub_category_id,$this->is_cache_enable);
                    return $redis_result;
                }

            }


            // Verify is Language code available
            if ($bcp_language_code) {

//                Log::info('LANGUAGE CODE AVAILABLE: ' . $bcp_language_code);

                // Verify is Language code "en"
                if ($bcp_language_code == Config::get('constant.DEFAULT_LANGUAGE_CODE')) {

                    if ($this->fail_over_sub_category_id) {

//                        Log::info('FAIL_OVER : LANGUAGE CODE AVAILABLE & DEFAULT_LANGUAGE_CODE = en');

                        $this->sub_category_id = $this->fail_over_sub_category_id;
                        $this->current_search_keyword = $this->search_category;
                        $this->fail_over_sub_category_id = NULL;
                        goto run_same_query;
                    } else {
                        // Give 427 result
//                        Log::info('RETURN 427 : LANGUAGE CODE AVAILABLE & DEFAULT_LANGUAGE_CODE = en');
                        $redis_result = $this->giveDefaultSearchResult($this->sub_category_id, $this->search_category, $this->is_featured, $this->offset, $this->item_count, $this->db_sub_category_id,$this->is_cache_enable);
                        return $redis_result;
                    }

                } else {

                    if ($this->is_search_keyword_already_translated == 0) {
//                        Log::info('PHASE_2: Detect & Translate Search Keyword');
                        // Detect & Translate Search Keyword
                        $this->is_search_keyword_already_translated = 1;
                        $this->translated_search_category = $this->detectAndTranslateSearchKeyword($this->search_category);
//                        Log::info('PHASE_2 : TRANSLATED RESULT : ' . $this->translated_search_category);

                        if ($this->translated_search_category) {
                            $this->current_search_keyword = $this->translated_search_category;
                            goto run_same_query;
                        } else {

                            if ($this->fail_over_sub_category_id) {
//                                Log::info('FAIL_OVER : PHASE_2 & TRANSLATE_SEARCH_KEYWORD = NULL');

                                $this->sub_category_id = $this->fail_over_sub_category_id;
                                $this->current_search_keyword = $this->search_category;
                                $this->fail_over_sub_category_id = NULL;
                                goto run_same_query;
                            } else {
                                // Give 427 result
//                                Log::info('RETURN 427 : PHASE_2 & TRANSLATE_SEARCH_KEYWORD = NULL');
                                $redis_result = $this->giveDefaultSearchResult($this->sub_category_id, $this->search_category, $this->is_featured, $this->offset, $this->item_count, $this->db_sub_category_id,$this->is_cache_enable);
                                return $redis_result;
                            }
                        }

                    } else {

                        // If Result not get after detect & translate then report keyword & goto fail_over_sub_category_id
                        $this->reportFailSearchKeywordResult($this->search_category, $this->translated_search_category, $this->sub_category_id);

                        if ($this->fail_over_sub_category_id) {

//                            Log::info('FAIL_OVER : PHASE_2 & Detect & Translate Search Keyword Already DONE');

                            $this->sub_category_id = $this->fail_over_sub_category_id;
                            $this->search_category = $this->search_category . " " . $this->translated_search_category;
                            $this->fail_over_sub_category_id = NULL;
                            goto run_same_query;
                        } else {
                            // Give 427 result
//                            Log::info('RETURN 427 : PHASE_2 & Detect & Translate Search Keyword Already DONE');
                            $redis_result = $this->giveDefaultSearchResult($this->sub_category_id, $this->search_category, $this->is_featured, $this->offset, $this->item_count, $this->db_sub_category_id,$this->is_cache_enable);
                            return $redis_result;
                        }

                    }
                }

            } else {

//                Log::info('LANGUAGE CODE NOT AVAILABLE:');

                if ($this->is_search_keyword_already_translated == 0) {

//                    Log::info('PHASE_3: Detect & Translate Search Keyword');

                    // Detect & Translate Search Keyword
                    $this->is_search_keyword_already_translated = 1;
                    $this->translated_search_category = $this->detectAndTranslateSearchKeyword($this->search_category);
//                    Log::info('PHASE_3 : TRANSLATED RESULT : ' . $this->translated_search_category);

                    if ($this->translated_search_category) {
                        $this->current_search_keyword = $this->translated_search_category;
                        goto run_same_query;
                    } else {

                        if ($this->fail_over_sub_category_id) {
//                            Log::info('FAIL_OVER : PHASE_3 & TRANSLATE_SEARCH_KEYWORD = NULL');

                            $this->sub_category_id = $this->fail_over_sub_category_id;
                            $this->current_search_keyword = $this->search_category;
                            $this->fail_over_sub_category_id = NULL;
                            goto run_same_query;
                        } else {
                            // Give 427 result
//                            Log::info('RETURN 427 : PHASE_3 & TRANSLATE_SEARCH_KEYWORD = NULL');
                            $redis_result = $this->giveDefaultSearchResult($this->sub_category_id, $this->search_category, $this->is_featured, $this->offset, $this->item_count, $this->db_sub_category_id, $this->is_cache_enable);
                            return $redis_result;
                        }
                    }
                }
            }

            return $redis_result;

        } catch (Exception $e) {
            Log::error("searchTemplatesBySearchCategory_V2 : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            return array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get template.', 'cause' => $e->getMessage(), 'data' => json_decode("{}"));
        }
    }

    private function giveDefaultSearchResult($sub_category_id, $search_category, $is_featured, $offset, $item_count, $db_sub_category_id, $is_cache_enable)
    {
        $this->sub_category_id = $sub_category_id;
        $this->search_category = $search_category;
        $this->is_featured = $is_featured;
        $this->offset = $offset;
        $this->item_count = $item_count;
        $this->db_sub_category_id = $db_sub_category_id;
        $this->is_cache_enable = $is_cache_enable;
        $this->cache_key_name = "default_result:searchCardsBySubCategoryIdWithTranslate";
        $this->cache_key_time = Config::get('constant.CACHE_TIME_7_DAYS');

        if ($this->is_cache_enable == 1) {
            $this->cache_key_name = "default_result:searchCardsBySubCategoryIdWithTranslate";
            $this->cache_key_time = Config::get('constant.CACHE_TIME_7_DAYS');
        } else {
            $this->cache_key_name = "default_result:searchCardsBySubCategoryIdWithTranslate_without_cache";
            $this->cache_key_time = 0;
        }

        $redis_result = Cache::remember("$this->cache_key_name:$this->db_sub_category_id:$this->is_featured:$this->offset:$this->item_count", $this->cache_key_time, function () {

            $code = 427;
            $message = "Sorry, we couldn't find any templates for '$this->search_category', but we found some other templates you might like:";
            $search_result = [];

            //Log::info('DB hit For 427');

            $total_row_result = DB::select('SELECT 
                                                        COUNT(DISTINCT(im.id)) AS total
                                                    FROM
                                                        images AS im,
                                                        catalog_master AS cm,
                                                        sub_category_catalog AS scc
                                                    WHERE
                                                        im.is_active = 1 AND
                                                        im.catalog_id = scc.catalog_id AND
                                                        cm.id = scc.catalog_id AND
                                                        scc.sub_category_id IN (' . $this->db_sub_category_id . ') AND
                                                        cm.is_featured = ? AND
                                                        ISNULL(im.original_img) AND
                                                        ISNULL(im.display_img) ', [$this->is_featured]);
            $total_row = $total_row_result[0]->total;

            if ($total_row) {
                $search_result = DB::select('SELECT
                                                        DISTINCT im.id AS json_id,
                                                        IF(im.attribute1 != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",im.attribute1),"") AS sample_image,
                                                        IF(im.attribute1 != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",im.attribute1),"") AS webp_original_img,
                                                        IF(im.content_type = ' . Config::get('constant.CONTENT_TYPE_FOR_BEFORE_AFTER_IMAGE') . ' AND im.attribute1 != "",CONCAT("' . Config::get('constant.WEBP_THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '","after_image_",im.attribute1),"") AS after_image,
                                                        IF(im.content_type = ' . Config::get('constant.CONTENT_TYPE_FOR_BEFORE_AFTER_IMAGE') . ' AND im.attribute1 != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '","after_image_",im.attribute1),"") AS webp_original_after_img,
                                                        IF(im.content_type = ' . Config::get('constant.CONTENT_TYPE_FOR_SAMPLE_IMAGE_GIF') . ' AND im.image != "",CONCAT("' . Config::get('constant.ORIGINAL_VIDEO_DIRECTORY_OF_DIGITAL_OCEAN') . '",SUBSTRING_INDEX(im.image,".",1),".gif"),"") AS sample_gif,
                                                        im.is_free,
                                                        im.is_featured,
                                                        im.is_portrait,
                                                        COALESCE(im.height,0) AS height,
                                                        COALESCE(im.width,0) AS width,
                                                        COALESCE(im.multiple_images,"") AS multiple_images,
                                                        COALESCE(im.json_pages_sequence,"") AS pages_sequence,
                                                        COALESCE(LENGTH(im.json_pages_sequence) - LENGTH(REPLACE(im.json_pages_sequence, ",","")) + 1,1) AS total_pages,
                                                        im.content_type,
                                                        im.updated_at
                                                    FROM
                                                        images AS im,
                                                        catalog_master AS cm,
                                                        sub_category_catalog AS scc
                                                    WHERE
                                                        im.is_active = 1 AND
                                                        im.catalog_id = scc.catalog_id AND
                                                        cm.id = scc.catalog_id AND
                                                        cm.is_featured = ? AND
                                                        scc.sub_category_id IN (' . $this->db_sub_category_id . ') AND
                                                        ISNULL(im.original_img) AND
                                                        ISNULL(im.display_img)
                                                    ORDER BY im.updated_at DESC LIMIT ?, ?', [$this->is_featured, $this->offset, $this->item_count]);
            }

            $is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;
            $search_result = array('total_record' => $total_row, 'is_next_page' => $is_next_page, 'result' => $search_result);

            return array('code' => $code, 'message' => $message, 'cause' => '', 'data' => $search_result);

        });
        $redis_result['message'] = "Sorry, we couldn't find any templates for '$this->search_category', but we found some other templates you might like:";

        return $redis_result;
    }

    private function detectAndTranslateSearchKeyword($search_category)
    {

        $this->search_category = $search_category;

//        Log::info('PHASE : 2 (Detect & Translate)');

        $translate_data = array('data' => array("source" => "", "input" => $this->search_category, "text" => "", "model" => ""));

        $translate_data = $this->translateLanguage($this->search_category, "en");


        if (Config::get('constant.ACTIVATION_LINK_PATH') != 'https://flyerbuilder.app' && Config::get('constant.APP_ENV') != 'local' && $translate_data['code'] == 200 && $translate_data['data']['source'] == Config::get('constant.DEFAULT_LANGUAGE_CODE')) {

//            Log::info('PHASE : 3 (Start Spell Correction)');

            $suggestions = $this->spellCorrection($translate_data['data']['source'], $this->search_category);
//            Log::info("Spell Correction :",$suggestions);

        } else {
            $suggestions['data'] = array();
        }

        if ($translate_data['data']['text'] == $this->search_category || $translate_data['data']['source'] == 'en' || $translate_data['code'] != 200) {
            $translate_data['data']['text'] = NULL;
//            Log::info('PHASE : 4 (search keyword & translate word are same || language code detected en || $translate_data[code] != 200)');

            if ($suggestions['data']){
                    $search_category = implode(',', array_slice($suggestions['data'], 0, 5));
                return $search_category;
            }else{
//                Log::info('PHASE : Suggestion result NOT found!');
                return NULL;
            }

        }

        if ($translate_data['data']['text']) {

            $search_category = $translate_data['data']['text'];
//            Log::info('PHASE : 5 Return translate result');
        }

        return $search_category;

    }

    private function reportSuccessSearchKeywordResult($search_category, $current_search_keyword, $sub_category_id)
    {
        $this->search_category = $search_category;
        $this->current_search_keyword = $current_search_keyword;
        $this->sub_category_id = $sub_category_id;

        // if result found after detect & translate keyword then call

        $old_data = Cache::get('translationReport');
        $old_data[] = array("user_tag" => $this->search_category, "translate_tag" => $this->current_search_keyword, "is_success" => 1, "sub_category_id" => $this->sub_category_id);
        Cache::forever('translationReport', array_map("unserialize", array_unique(array_map("serialize", $old_data))));
    }

    private function reportFailSearchKeywordResult($search_category, $current_search_keyword, $sub_category_id)
    {
        $this->search_category = $search_category;
        $this->current_search_keyword = $current_search_keyword;
        $this->sub_category_id = $sub_category_id;

        // if result not found after detect & translate keyword then call

        $old_data = Cache::get('translationReport');
        $old_data[] = array("user_tag" => $this->search_category, "translate_tag" => $this->current_search_keyword, "is_success" => 0, "sub_category_id" => $this->sub_category_id);
        Cache::forever('translationReport', array_map("unserialize", array_unique(array_map("serialize", $old_data))));
    }

    public function searchCatalogsBySearchCategory($search_category, $sub_category_id, $offset, $item_count, $is_featured)
    {
        try {
            $total_row_result = DB::select('SELECT
                                                  COUNT(cm.id) AS total
                                                FROM
                                                  catalog_master AS cm,
                                                  sub_category_catalog AS sct
                                                WHERE
                                                  sct.sub_category_id = ? AND
                                                  sct.catalog_id = cm.id AND
                                                  cm.is_featured = ? AND
                                                  sct.is_active = 1 AND
                                                  (MATCH(cm.search_category) AGAINST ("' . $search_category . '") OR 
                                                    MATCH(cm.search_category) AGAINST (REPLACE(CONCAT("' . $search_category . '"," ")," ","* ")  IN BOOLEAN MODE))', [$sub_category_id, $is_featured]);

            $total_row = $total_row_result[0]->total;

            if ($total_row) {

                $search_result = DB::select('SELECT
                                                    cm.id AS catalog_id,
                                                    cm.name,
                                                    IF(cm.image != "",CONCAT("' . Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",cm.image),"") as thumbnail_img,
                                                    IF(cm.image != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",cm.image),"") as compressed_img,
                                                    IF(cm.image != "",CONCAT("' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",cm.image),"") as original_img,
                                                    IF(cm.attribute1 != "",CONCAT("' . Config::get('constant.WEBP_THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",cm.attribute1),"") as webp_thumbnail_img,
                                                    cm.is_free,
                                                    cm.is_featured,
                                                    MATCH(cm.search_category) AGAINST ("' . $search_category . '") +
                                                    MATCH(cm.search_category) AGAINST (REPLACE(CONCAT("' . $search_category . '"," ")," ","* ")  IN BOOLEAN MODE) AS search_text,
                                                    cm.updated_at
                                                FROM
                                                   catalog_master AS cm,
                                                   sub_category_catalog as sct
                                                WHERE
                                                    sct.sub_category_id = ? AND
                                                    sct.catalog_id = cm.id AND
                                                    cm.is_featured = ? AND
                                                    sct.is_active = 1 AND
                                                    (MATCH(cm.search_category) AGAINST("' . $search_category . '") OR 
                                                    MATCH(cm.search_category) AGAINST(REPLACE(concat("' . $search_category . '"," ")," ","* ")  IN BOOLEAN MODE))
                                                ORDER BY search_text DESC,cm.updated_at DESC LIMIT ?, ?', [$sub_category_id, $is_featured, $offset, $item_count]);

                $code = 200;
                $message = "Catalog fetched successfully.";

            } else {
                $search_result = [];
                $code = 427;
                $message = "Sorry, we couldn't find any templates for '$search_category'";
            }

            $is_next_page = ($total_row > ($offset + $item_count)) ? true : false;
            $data = array('total_record' => $total_row, 'is_next_page' => $is_next_page, 'result' => $search_result);
            return array('code' => $code, 'message' => $message, 'cause' => '', 'data' => $data);

        } catch (Exception $e) {
            Log::error("searchCatalogBySubCategoryId : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'search catalog.', 'cause' => $e->getMessage(), 'data' => json_decode("{}"));
        }
        return $response;
    }

    /*
    Purpose : To translate language from any to english with google API.
    Description : This method compulsory take 2 argument as parameter.(if any argument is optional then define it here).
    Return : return translated language detected detail if success otherwise error with specific status code
    */
    public function translateLanguage($text, $language = "en")
    {
        try {
            $this->text = $text;
            $this->language = $language;

            $redis_result = Cache::rememberforever("translateLanguage:$this->text", function () {

                $translate = new TranslateClient([
                    'key' => Config::get('constant.GOOGLE_API_KEY')
                ]);

                // Translate text from Any to English.
                $result = $translate->translate($this->text, [
                    'target' => $this->language
                ]);

                //Log::info('translateLanguage : ', ['user_tag' => $this->text, 'result' => $result, 'target_language' => $this->language]);
                return $result;
            });

            $response = array('code' => 200, 'message' => 'Language translation successfully.', 'cause' => '', 'data' => $redis_result);

        } catch (Exception $e) {
            Log::error("translateLanguage : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get template detail.', 'cause' => $e->getMessage(), 'data' => array("source" => "", "input" => $text, "text" => "", "model" => ""));
        }
        return $response;
    }

    /*
    Purpose : To correct spelling of text with p-spell php extension.
    Description : This method compulsory take 2 argument as parameter.(if any argument is optional then define it here).
    Return : return spell corrected word in array if success otherwise error with specific status code
    */
    public function spellCorrection($language_dictionary, $text)
    {
        try {
            $suggestions = array();
            //$spellLink = pspell_new($language_dictionary);
            $spellLink = pspell_new("en");
            if (!pspell_check($spellLink, $text)) {
                $suggestions = pspell_suggest($spellLink, $text);
                Log::info('spellCorrection : Spell suggestion.', ['user_tag' => $text, 'suggestion' => $suggestions, 'language_dictionary' => $language_dictionary]);
            }

            $response = array('code' => 200, 'message' => 'Spell correction successfully.', 'cause' => '', 'data' => $suggestions);

        } catch (Exception $e) {
            Log::error("spellCorrection : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get template detail.', 'cause' => $e->getMessage(), 'data' => array());
        }
        return $response;
    }

    /*
    Purpose : To detect language code with google API (Unused function)
    Description : This method compulsory take 1 argument as parameter.(if any argument is optional then define it here).
    Return : return language detected detail if success otherwise error with specific status code
    */
    public function detectLanguageCode($text)
    {
        try {
            $this->text = $text;

            $redis_result = Cache::rememberforever("detectLanguageCode:$this->text", function () {

                $translate = new TranslateClient([
                    'key' => Config::get('constant.GOOGLE_API_KEY')
                ]);

                // Detect the language of a string.
                $result = $translate->detectLanguage($this->text);

                return $result;
            });

            $response = array('code' => 200, 'message' => 'Language code detect successfully.', 'cause' => '', 'data' => $redis_result);

        } catch (Exception $e) {
            Log::error("detectLanguageCode : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get template detail.', 'cause' => $e->getMessage(), 'data' => array());
        }
        return $response;
    }

    /*
    Purpose : To detect language code with composer pear library API (Unused function : if we want to used in feature then run composer require command)
    Description : This method compulsory take 1 argument as parameter.(if any argument is optional then define it here).
    Return : return language detected detail if success otherwise error with specific status code
    */
    public function detectLanguageWithComposer($text)
    {
        try {
            $this->text = $text;
            $redis_result = NULL;

            //composer require pear/text_languagedetect
            //composer library: "pear/text_languagedetect": "^1.0",

            //$lib = new Text_LanguageDetect();

            //$redis_result = $lib->detect($text);

            $response = array('code' => 200, 'message' => 'Language code detect successfully.', 'cause' => '', 'data' => $redis_result);

        } catch (Exception $e) {
            Log::error("detectLanguageWithComposer : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get template detail.', 'cause' => $e->getMessage(), 'data' => array());
        }
        return $response;
    }

    /*
    Purpose : To detect emoji is exist or not in text. (Unused function)
    Description : This method compulsory take 1 argument as parameter.(if any argument is optional then define it here).
    Return : return true if emoji was detected otherwise false
    */
    public function checkIsEmojiExist($text)
    {
        try {
            preg_match( '/[\x{1F600}-\x{1F64F}]/u', $text, $emoji);
            if($emoji)
                $response = true;
            else
                $response = false;

        } catch (Exception $e) {
            Log::error("checkIsEmojiExist : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = false;
        }
        return $response;
    }

    /*
    Purpose : To translate emoji into text. (Unused function : if we want to used in feature then run composer require command)
    Description : This method compulsory take 1 argument as parameter.(if any argument is optional then define it here).
    Return : return translated text from emoji if success otherwise error with specific status code
    */
    public function translateEmoji($text)
    {
        try {
            $result = $text;

            //"elvanto/litemoji": "^4.1",
            //composer require elvanto/litemoji

            //$converted_string = LitEmoji::encodeShortcode($text);
            //$remove_special_char = preg_replace('/[^a-zA-Z0-9]/', ",", $converted_string);
            //$remove_same_char = trim(preg_replace('/(\,+)/', ',', $remove_special_char), ',');
            //$char_array = explode(',', $remove_same_char);
            //$unique_char = array_unique(array_filter($char_array));
            //$result = implode(',', $unique_char);

            $response = array('code' => 200, 'message' => 'Emoji translation successfully.', 'cause' => '', 'data' => $result);

        } catch (Exception $e) {
            Log::error("translateEmoji : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'translate emoji.', 'cause' => $e->getMessage(), 'data' => "");
        }
        return $response;
    }

    //Get all feature template with shuffle
    public function getAllFeaturedTemplatesWithShuffling($sub_category_id)
    {
        try{
            $this->sub_category_id = $sub_category_id;

            //get random featured templates from featured catalogs
            if (!Cache::has("pel:getAllFeaturedTemplatesWithShuffling$this->sub_category_id")) {
                $result = Cache::remember("getAllFeaturedTemplatesWithShuffling$this->sub_category_id", $this->time_of_expired_redis_key, function () {

                    //query to get random records by 3:6 ratio on value of "is_free" column within current_date
                    $records_of_current_date = DB::select('SELECT
                                                                  id AS json_id,
                                                                  IF(attribute1 != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",attribute1),"") AS sample_image,
                                                                  is_free,
                                                                  is_featured,
                                                                  is_portrait,
                                                                  coalesce(height, 0) AS height,
                                                                  coalesce(width, 0) AS width,
                                                                  coalesce(search_category,"") AS search_category,
                                                                  coalesce(original_img_height,0) AS original_img_height,
                                                                  coalesce(original_img_width,0) AS original_img_width,
                                                                  COALESCE(multiple_images,"") AS multiple_images,
                                                                  COALESCE(json_pages_sequence,"") AS pages_sequence,
                                                                  COALESCE(LENGTH(json_pages_sequence) - LENGTH(REPLACE(json_pages_sequence, ",","")) + 1,1) AS total_pages,
                                                                  updated_at
                                                                FROM (
                                                                       (SELECT
                                                                          @a := @a + 1,
                                                                          ceil(@a / 3) AS rank,
                                                                          im.*
                                                                        FROM images AS im, (SELECT @a := 0) a
                                                                        WHERE
                                                                          catalog_id IN (SELECT catalog_id
                                                                                         FROM sub_category_catalog
                                                                                         WHERE sub_category_id = ? AND is_active = 1) AND
                                                                          is_featured = 1 AND
                                                                          is_free = 1 AND
                                                                          curdate() = DATE(updated_at)
                                                                        ORDER BY updated_at DESC)
                                                                       UNION
                                                                       (SELECT
                                                                          @b := @b + 1,
                                                                          ceil(@b / 6) AS rank,
                                                                          im.*
                                                                        FROM images AS im, (SELECT @b := 0) b
                                                                        WHERE
                                                                          catalog_id IN (SELECT catalog_id
                                                                                         FROM sub_category_catalog
                                                                                         WHERE sub_category_id = ? AND is_active = 1) AND
                                                                          is_featured = 1 AND
                                                                          is_free = 0 AND
                                                                          curdate() = DATE(updated_at)
                                                                        ORDER BY updated_at DESC)
                                                                     ) derived_table
                                                                ORDER BY rank, is_free DESC, rand()', [$this->sub_category_id, $this->sub_category_id]);

                   //call procedure for get data with shuffle(pass sub category id)
//                   $remaining_records_excepting_current_date = DB::select('call getTemplatesWithShuffle('.$this->sub_category_id.')');
//                      foreach ($remaining_records_excepting_current_date as $key) {
//                         if ($key->sample_image != "") {
//                             $key->sample_image = Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').$key->sample_image;
//                         }
//                     }

                    //query to get remaining records randomly by 3:6 ratio on value of "is_free" column (Direct query)
                    $remaining_records_excepting_current_date = DB::select('
                                                                              SELECT id AS json_id,
                                                                              IF(attribute1 != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",attribute1),"") AS sample_image,
                                                                              is_free,
                                                                              is_featured,
                                                                              is_portrait,
                                                                              coalesce(height, 0) AS height,
                                                                              coalesce(width, 0) AS width,
                                                                              coalesce(search_category,"") AS search_category,
                                                                              coalesce(original_img_height,0) AS original_img_height,
                                                                              coalesce(original_img_width,0) AS original_img_width,
                                                                              COALESCE(multiple_images,"") AS multiple_images,
                                                                              COALESCE(json_pages_sequence,"") AS pages_sequence,
                                                                              COALESCE(LENGTH(json_pages_sequence) - LENGTH(REPLACE(json_pages_sequence, ",","")) + 1,1) AS total_pages,
                                                                              updated_at
                                                                              FROM (
                                                                                      (SELECT
                                                                                          @a := @a + 1,ceil(@a / 3) AS rank,
                                                                                          im.*
                                                                                          FROM (SELECT *
                                                                                                  FROM images
                                                                                                  WHERE catalog_id IN (SELECT catalog_id FROM sub_category_catalog WHERE sub_category_id = ? AND is_active = 1) AND
                                                                                                        is_featured = 1 AND
                                                                                                        is_free = 1 AND
                                                                                                        curdate() !=DATE(updated_at) ORDER By rand()) AS im,
                                                                                                        (SELECT @a := 0) a)
                                                                                       UNION
                                                                                       (SELECT
                                                                                           @b := @b + 1,ceil(@b / 6) AS rank,
                                                                                           im.*
                                                                                           FROM (SELECT *
                                                                                                   FROM images
                                                                                                   WHERE catalog_id IN (SELECT catalog_id FROM sub_category_catalog WHERE sub_category_id = ? AND is_active = 1) AND
                                                                                                   is_featured = 1 AND
                                                                                                   is_free = 0 AND
                                                                                                   curdate() !=DATE(updated_at) ORDER By rand()) AS im,
                                                                                                   (SELECT @b := 0) b)
                                                                                     ) derived_table ORDER BY rank, is_free DESC', [$this->sub_category_id, $this->sub_category_id]);

                    //query to get remaining records randomly by 3:6 ratio on value of "is_free" column
//                    $remaining_records_excepting_current_date = DB::select('SELECT
//                                                                                  id AS json_id,
//                                                                                  IF(attribute1 != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",attribute1),"") AS sample_image,
//                                                                                  is_free,
//                                                                                  is_featured,
//                                                                                  is_portrait,
//                                                                                  coalesce(height, 0) AS height,
//                                                                                  coalesce(width, 0) AS width,
//                                                                                  coalesce(search_category,"") AS search_category,
//                                                                                  coalesce(original_img_height,0) AS original_img_height,
//                                                                                  coalesce(original_img_width,0) AS original_img_width,
//                                                                                  updated_at
//                                                                                FROM (
//                                                                                       (SELECT
//                                                                                          @a := @a + 1,
//                                                                                          ceil(@a / 3) AS rank,
//                                                                                          im.*
//                                                                                        FROM images AS im, (SELECT @a := 0) a
//                                                                                        WHERE
//                                                                                          catalog_id IN (SELECT catalog_id
//                                                                                                         FROM sub_category_catalog
//                                                                                                         WHERE sub_category_id = ? AND is_active = 1) AND
//                                                                                          is_featured = 1 AND
//                                                                                          is_free = 1 AND
//                                                                                          curdate() != DATE(updated_at)
//                                                                                        ORDER BY updated_at DESC)
//                                                                                       UNION
//                                                                                       (SELECT
//                                                                                          @b := @b + 1,
//                                                                                          ceil(@b / 6) AS rank,
//                                                                                          im.*
//                                                                                        FROM images AS im, (SELECT @b := 0) b
//                                                                                        WHERE
//                                                                                          catalog_id IN (SELECT catalog_id
//                                                                                                         FROM sub_category_catalog
//                                                                                                         WHERE sub_category_id = ? AND is_active = 1) AND
//                                                                                          is_featured = 1 AND
//                                                                                          is_free = 0 AND
//                                                                                          curdate() != DATE(updated_at)
//                                                                                        ORDER BY updated_at DESC)
//                                                                                     ) derived_table
//                                                                                ORDER BY rank, is_free DESC, rand()', [$this->sub_category_id, $this->sub_category_id]);
//                    $data = DB::select('call getAllCard('.$this->sub_category_id.')');
//                    dd($data);
//                   $is_free =array();
//                   $is_pro = array();
//                   foreach ($remaining_records_excepting_current_date as $row){
//                       if($row->is_free){
//                           array_push($is_free,$row);
//                       }else{
//                           array_push($is_pro,$row);
//                       }
//                   }
//                    shuffle($is_free);
//                    shuffle($is_pro);
//                    $free_slice = array_chunk($is_free,3);
//                    $pro_slice = array_chunk($is_pro,6);
//                    $max_count = max(count($free_slice),count($pro_slice));
//
//                    $final_array=array();
//                    for($i=0;$i<=$max_count;$i++){
//                        if(isset($free_slice[$i]) && is_array($free_slice[$i])){
//                            array_push($final_array,$free_slice[$i]);
//                        }
//                        if(isset($pro_slice[$i]) && is_array($pro_slice[$i])){
//                            array_push($final_array,$pro_slice[$i]);
//                        }
//                    }
//
//                    $result = array();
//                    foreach ($final_array as $key => $value) {
//                        if (is_array($value)) {
//                            $result = array_merge($result,$value);
//                        } else {
//                            $result[$key] = $value;
//                        }
//                    }
//                    $featured_templates = array_merge($records_of_current_date,$result);
                    $featured_templates = array_merge($records_of_current_date,$remaining_records_excepting_current_date);
                    $total_row = count($featured_templates);
                    $result = array('total_row' => $total_row, 'featured_templates' => $featured_templates);
                    return $result;

                });
            }

            $featured_templates = Cache::get("getAllFeaturedTemplatesWithShuffling$this->sub_category_id");
            return $featured_templates;

        }
        catch (Exception $e)
        {
            Log::error("getAllFeaturedTemplatesWithShuffling : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            return $response = array('total_row' => 0, 'featured_templates' => [], 'prefix_url' => Config::get('constant.AWS_BUCKET_PATH_PHOTO_EDITOR_LAB').'/');
        }
    }

    public function calculateHeightWidth($image_array)
    {
        try {

//            [$width, $height] = getimagesize($image_url);
//            $img_h_w = Image::make($image_url);
//            $height = $img_h_w->height();
//            $width = $img_h_w->width();

            $image_size = getimagesize($image_array);
            $org_img_height = $image_size[1];
            $org_img_width = $image_size[0];
            $height = ($image_size[1] * 50) / 100;
            $width = ($image_size[0] * 50) / 100;

            if ($width < 200 or $height < 200) {

                $width_orig = $image_size[0];
                $height_orig = $image_size[1];
                $ratio_orig = $width_orig / $height_orig;

                $width = $width_orig < Config::get('constant.THUMBNAIL_WIDTH') ? $width_orig : Config::get('constant.THUMBNAIL_WIDTH');
                $height = $height_orig < Config::get('constant.THUMBNAIL_HEIGHT') ? $height_orig : Config::get('constant.THUMBNAIL_HEIGHT');

                if ($width / $height > $ratio_orig)
                    $width = $height * $ratio_orig;
                else
                    $height = $width / $ratio_orig;
            }

            $response = array('height' => $height, 'width' => $width, 'org_img_height' => $org_img_height, 'org_img_width' => $org_img_width);

        } catch (Exception $e) {
            Log::error("calculateHeightWidth : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = array('height' => 0, 'width' => 0, 'org_img_height' => 0, 'org_img_width' => 0);
            DB::rollBack();
        }
        return $response;
    }

    /**
     * @api {post} addCategoryNameAsTag addCategoryNameAsTag
     * @apiName addCategoryNameAsTag
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "catalog_id":12,
     * "page":1,
     * "item_count":10
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Tag added successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function addCategoryNameAsTag(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('catalog_id','page','item_count'), $request)) != '')
                return $response;

            $catalog_id = $request->catalog_id;
            $page = $request->page;
            $item_count = $request->item_count;
            $offset = ($page - 1) * $item_count;

            $total_row_result = DB::select('SELECT 
                                               count(id) as total
                                            FROM
                                               images
                                            WHERE
                                               catalog_id = ?', [$catalog_id]);

            $total_row = $total_row_result[0]->total;
            $is_next_page = ($total_row > ($offset + $item_count)) ? true : false;

            $templates = DB::select('SELECT 
                                      i.id,
                                      cm.name,
                                      i.search_category,
                                      i.updated_at 
                                  FROM
                                      images as i,
                                      catalog_master as cm
                                  WHERE
                                      i.catalog_id = ? AND
                                      i.catalog_id = cm.id 
                                  ORDER BY i.updated_at ASC LIMIT ?,? ', [$catalog_id,$offset,$item_count]);
            if (count($templates) > 0) {
                $tag = str_replace(' ', ',',strtolower(preg_replace('/[^A-Za-z ]/', '', $templates[0]->name)));

                foreach ($templates as $row) {
                    if ($row->search_category != NULL || $row->search_category != "") {
                        $row->search_category .=  ','.$tag;
                    }else{
                        $row->search_category = $tag;
                    }
                    $row->search_category = implode(',', array_unique(array_filter(explode(',', $row->search_category))));
                    DB::beginTransaction();
                    DB::update('UPDATE images SET search_category =?,updated_at =? WHERE id=?', [$row->search_category, $row->updated_at,$row->id]);
                    DB::commit();
                }
            }

            $response = Response::json(array('code' => 200, 'message' => 'Tag added successfully.', 'cause' => '', 'data' =>array('total_record' => $total_row, 'is_next_page' => $is_next_page)));
        } catch (Exception $e) {
            Log::error("addCategoryNameAsTag : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'add tag.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    public function addCategoryNameAsTagV2(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('catalog_ids'), $request)) != '')
                return $response;

            $catalog_ids = $request->catalog_ids;
            $all_query = NULL;
            $count = 1;

            $templates = DB::select('SELECT 
                                          i.id,
                                          cm.name,
                                          i.search_category,
                                          i.updated_at 
                                      FROM
                                          images AS i,
                                          catalog_master AS cm
                                      WHERE
                                          i.catalog_id IN ('.$catalog_ids.') AND
                                          i.catalog_id = cm.id');

            foreach ($templates as $i => $row) {

                $tag = str_replace(' ', ',',strtolower(preg_replace('/[^A-Za-z ]/', '', $row->name)));
                if ($row->search_category != NULL || $row->search_category != "") {
                    $row->search_category .=  ','.$tag;
                }else{
                    $row->search_category = $tag;
                }

                $row->search_category = implode(',', array_unique(array_filter(explode(',', $row->search_category))));

                $all_query .= "UPDATE images SET search_category = '".$row->search_category."', updated_at = updated_at WHERE id = '".$row->id."'; ";
                $count++;
            }

            DB::beginTransaction();
            if($all_query)
                DB::unprepared("$all_query");

            DB::commit();

            $this->deleteAllRedisKeys("getDataByCatalogIdForAdmin");

            $response = Response::json(array('code' => 200, 'message' => 'Tag added successfully.', 'cause' => '', 'data' => $count));

        } catch (Exception $e) {
            Log::error("addCategoryNameAsTagV2 : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'add tag.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /*
    Purpose : For add font name as tag in catalog.
    Description : This method compulsory take 1 argument as parameter.(if any argument is optional then define it here).
    Return : return Tag added successfully if success otherwise error with specific status code
    */
    public function addFontNameAsTag(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredArrayParameter(array('catalog_ids'), $request)) != '')
                return $response;

            $catalog_ids = $request->catalog_ids;
            $search_category = [];

            foreach ($catalog_ids AS $i => $catalog_id){

                $fonts_name = DB::select('SELECT 
                                           catalog_id,
                                           font_name
                                        FROM
                                           font_master
                                        WHERE
                                           catalog_id = ?
                                        ORDER BY update_time DESC', [$catalog_id]);

                foreach ($fonts_name AS $j => $font_name){

                    $search_category[] = strtolower(preg_replace('/[^A-Za-z0-9]/', '', $font_name->font_name));
                    $search_category[] = strtolower(preg_replace('/[^A-Za-z0-9]/', ',', $font_name->font_name));

                }

                $db_data = DB::select('SELECT search_category FROM catalog_master WHERE id = ?', [$catalog_id]);
                if(count($db_data) > 0 && $db_data[0]->search_category)
                    $search_category[] = strtolower($db_data[0]->search_category);

                $search_category = implode(',',array_filter(array_unique(explode(',',implode(',',$search_category)))));

                DB::beginTransaction();
                DB::update('UPDATE catalog_master SET search_category = ? , updated_at = updated_at WHERE id = ? ', [$search_category, $catalog_id]);
                DB::commit();
                $search_category = [];
            }

            $response = Response::json(array('code' => 200, 'message' => 'Tag added successfully.', 'cause' => '', 'data' => $search_category));

        } catch (Exception $e) {
            Log::error("addFontNameAsTag : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'add tag.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /*
    Purpose : Used to link multiple catalog at once.
    Description : This method compulsory take 2 argument as parameter.(if any argument is optional then define it here).
    Return : return Multiple catalog linked successfully if success otherwise error with specific status code
    */
    public function linkMultiPleCatalog(Request $request)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request->getContent());
            //Log::info("linkCatalog Request :", [$request]);

            if (($response = (new VerificationController())->validateRequiredParameter(array('sub_category_id'), $request)) != '')
                return $response;

            if (($response = (new VerificationController())->validateRequiredArrayParameter(array('catalog_ids'), $request)) != '')
                return $response;

            $catalog_ids = $request->catalog_ids;
            $sub_category_id = $request->sub_category_id;
            $create_at = date('Y-m-d H:i:s');
            $data = array();

            foreach ($catalog_ids AS $i => $catalog_id) {

                $catalog_name = DB::select('SELECT name FROM catalog_master WHERE id = ?', [$catalog_id]);

                if (($response = (new VerificationController())->checkIsCatalogExist($sub_category_id, $catalog_name[0]->name, $catalog_id)) != '') {
                    $sub_category_name = DB::select('SELECT name FROM sub_category WHERE id = ?', [$sub_category_id]);
                    return $response = Response::json(array('code' => 201, 'message' => '"' . $catalog_name[0]->name . '" already exist in "' . $sub_category_name[0]->name . '" category.', 'cause' => '', 'data' => json_decode("{}")));
                }

                $data[] = ['sub_category_id' => $sub_category_id, 'catalog_id' => $catalog_id, 'created_at' => $create_at];

            }

            DB::beginTransaction();
            DB::table('sub_category_catalog')->insert($data);
            DB::commit();

            $this->deleteAllRedisKeys("getCatalogBySubCategoryIdForAdmin:$sub_category_id");

            $response = Response::json(array('code' => 200, 'message' => 'Multiple catalog linked successfully.', 'cause' => '', 'data' => json_decode('{}')));

        } catch (Exception $e) {
            Log::error("linkMultiPleCatalog : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'link catalog.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /*
    Purpose : Used to copy catalog from old sub_category to new sub_category.
    Description : This method compulsory take 2 argument as parameter.(if any argument is optional then define it here).
    Return : return Catalog copied successfully if success otherwise error with specific status code
    */
    public function copyCatalogBySubCategoryId(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('old_sub_category_id', 'new_sub_category_id'), $request)) != '')
                return $response;

            $old_sub_category_id = $request->old_sub_category_id;
            $new_sub_category_id = $request->new_sub_category_id;
            $create_at = date('Y-m-d H:i:s');

            DB::beginTransaction();
            $old_all_catalog_id = DB::select('SELECT 
                                            id,
                                            name,
                                            image
                                        FROM 
                                            catalog_master 
                                        WHERE 
                                            is_featured = 1 AND
                                            is_active = 1 AND 
                                            id IN (SELECT catalog_id FROM sub_category_catalog WHERE sub_category_id = ?)
                                        ORDER by updated_at DESC', [$old_sub_category_id]);

            $old_all_catalog_id_array = array_column($old_all_catalog_id, 'id');
            $old_all_catalog_id_str = implode(',',$old_all_catalog_id_array);

            $old_all_img_array = array_column($old_all_catalog_id, 'image');
            $old_all_img_str = '"' . implode('","',$old_all_img_array) . '"';

            Log::info('copyCatalogFromOldToNew : 1.old all catalog detail',["old_id_array" => $old_all_catalog_id_array, "old_id_str" => $old_all_catalog_id_str, "old_img_array" => $old_all_img_array, "old_img_str" => $old_all_img_str]);

            DB::insert('INSERT INTO catalog_master
                              (name, catalog_type, image, icon, landscape_image, portrait_image, landscape_webp, portrait_webp, is_free, is_ios_free, is_featured, is_active, event_date, popularity_rate, search_category, created_at, updated_at, attribute1, attribute2, attribute3, attribute4, attribute5)
                        SELECT name, catalog_type, image, icon, landscape_image, portrait_image, landscape_webp, portrait_webp, is_free, is_ios_free, is_featured, is_active, event_date, popularity_rate, search_category, created_at, updated_at, attribute1, attribute2, attribute3, attribute4, id
                              FROM catalog_master
                        WHERE id IN ('.$old_all_catalog_id_str.')  ');


            $new_all_catalog_db = DB::select('SELECT 
                                                    id AS catalog_id, 
                                                    ? AS sub_category_id,
                                                    ? AS created_at
                                              FROM 
                                                    catalog_master 
                                              WHERE attribute5 IN ('.$old_all_catalog_id_str.') ', [$new_sub_category_id, $create_at]);

            $new_all_catalog_id_array = array_column($new_all_catalog_db, 'catalog_id');
            $new_all_catalog_id_str = implode(',', $new_all_catalog_id_array);

            Log::info('copyCatalogFromOldToNew : 2.new all catalog detail',["new_id_array" => $new_all_catalog_id_array, "new_id_str" => $new_all_catalog_id_str]);

            $sub_category_catalog_data = json_decode(json_encode($new_all_catalog_db), true);
            DB::table('sub_category_catalog')->insert($sub_category_catalog_data);
            DB::commit();

            $this->deleteAllRedisKeys("getCatalogBySubCategoryIdForAdmin");
            $this->deleteAllRedisKeys("getDataByCatalogIdForAdmin");

            $response = Response::json(array('code' => 200, 'message' => 'catalog copied successfully.', 'cause' => '', 'data' => $sub_category_catalog_data));

        } catch (Exception $e) {
            Log::error("copyCatalogFromOldToNew : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'add tag.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /*
    Purpose : Used to copy catalog from old sub_category to new sub_category.
    Description : This method compulsory take 2 argument as parameter.(if any argument is optional then define it here).
    Return : return Catalog copied successfully if success otherwise error with specific status code
    */
    public function copyTemplateByCatalogIds(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('catalog_ids'), $request)) != '')
                return $response;

            $catalog_ids = $request->catalog_ids;
            $except_image_ids = isset($request->image_ids) ? $request->image_ids : "";
            $where_condition = "";

            if($except_image_ids){
                $where_condition .= " AND id NOT IN ($except_image_ids) ";
            }

            DB::beginTransaction();

            $old_catalog_list = DB::select('SELECT id,attribute5 FROM catalog_master WHERE attribute5 IN ('.$catalog_ids.') ');

            $old_new_catalog_list = array_column($old_catalog_list, 'id', 'attribute5');

            $old_json_details = DB::select('SELECT * FROM images WHERE catalog_id IN ('.$catalog_ids.') '.$where_condition.' ');

            $old_json_array = json_decode(json_encode($old_json_details), true);

            foreach ($old_json_array AS $i => $old_json){
                //$old_json['old_catalog_id'] = $old_json['catalog_id'];
                $old_json_array[$i]['catalog_id'] = $old_new_catalog_list[$old_json['catalog_id']];
                $old_json_array[$i]["attribute4"] = $old_json_array[$i]["id"];
                unset($old_json_array[$i]["id"]);
            }

            $result = DB::table('images')->insert($old_json_array);

            DB::commit();

            $this->deleteAllRedisKeys("getCatalogBySubCategoryId");
            $this->deleteAllRedisKeys("getDataByCatalogIdForAdmin");

            $response = Response::json(array('code' => 200, 'message' => 'template copied successfully.', 'cause' => '', 'data' => count($old_json_array)));

        } catch (Exception $e) {
            Log::error("copyTemplateByCatalogIds : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'add tag.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /*
    Purpose : Used to convert template from single-page format to multi-page format.
    Description : This method compulsory take 1 argument as parameter.(image_ids argument is optional).
    Return : return Json converted successfully if success otherwise error with specific status code
    */
    public function updateCardToMultiFormatByCatalogIds(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('catalog_ids'), $request)) != '')
                return $response;

            $catalog_ids = $request->catalog_ids;
            $except_image_ids = isset($request->image_ids) ? $request->image_ids : "";
            $where_condition = "";
            $count = 0;

            if($except_image_ids){
                $where_condition .= " AND id NOT IN ($except_image_ids) ";
            }

            DB::beginTransaction();

            $image_details = DB::select('SELECT * FROM images WHERE catalog_id IN ('.$catalog_ids.') '.$where_condition.' ');

            foreach ($image_details AS $j => $image_detail){

                if(!$image_detail->is_multipage){

                    $count++;
                    $rand_no = rand(100001,999999);
                    $multiple_images[$rand_no] = array("name" => $image_detail->image, "webp_name" => $image_detail->attribute1, "width" => $image_detail->width, "height" => $image_detail->height, "org_img_width" => $image_detail->original_img_width, "org_img_height" => $image_detail->original_img_height, "page_id" => $rand_no);
                    $json_data = json_decode($image_detail->json_data);
                    $json_data->page_id = $rand_no;
                    $new_json_data = json_decode('{}');
                    $new_json_data->{$rand_no} = $json_data;

                    DB::update('UPDATE 
                                images 
                            SET 
                                multiple_images = ?,
                                json_data = ?,
                                json_pages_sequence = ?,
                                is_multipage = ?,
                                updated_at = updated_at
                            WHERE id = ?',[json_encode($multiple_images), json_encode($new_json_data), $rand_no, 1, $image_detail->id]);

                }
                $multiple_images = NULL;
            }

            DB::commit();

            $this->deleteAllRedisKeys("getDataByCatalogIdForAdmin");

            $response = Response::json(array('code' => 200, 'message' => 'Json converted successfully.', 'cause' => '', 'data' => $count));

        } catch (Exception $e) {
            Log::error("updateCardToMultiFormatByCatalogIds : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'change page.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /*
    Purpose : Used to mix first page template & second page template & convert it into multi-page format.
    Description : This method compulsory take 2 argument as parameter.(if any argument is optional then define it here).
    Return : return Template mixed successfully if success otherwise error with specific status code
    */
    public function mixSinglePageWithMultiPage(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredArrayParameter(array('first_page_json_ids', 'second_page_json_ids'), $request)) != '')
                return $response;

            $first_page_json_ids = $request->first_page_json_ids;
            $second_page_json_ids = $request->second_page_json_ids;
            $new_data_ids = [];

            DB::beginTransaction();
            foreach ($first_page_json_ids AS $i => $first_page_json_id){

                $multiple_images = [];

                $first_page_json_data = DB::select('SELECT * FROM images WHERE id = ?', [$first_page_json_id]);
                $second_page_json_data = DB::select('SELECT * FROM images WHERE id = ?', [$second_page_json_ids[$i]]);
                $new_catalog_detail = DB::select('SELECT id FROM catalog_master WHERE attribute5 = ?', [$first_page_json_data[0]->catalog_id]);

                if($new_catalog_detail){
                    $new_catalog_id = $new_catalog_detail[0]->id;
                }else{
                    Log::error("mixSinglePageWithMultiPage : new catalog_id not found :", ["first_page_json_id" => $first_page_json_id, "second_page_json_id" => $second_page_json_ids[$i], "new_catalog_detail" => $new_catalog_detail]);
                    continue;
                }

                $first_page_rand_no = rand(100001, 999999);
                $second_page_rand_no = rand(100001, 999999);

                if($first_page_rand_no == $second_page_rand_no){
                    Log::info('mixSinglePageWithMultiPage : random number :',["first_page_rand_no" => $first_page_rand_no, "second_page_rand_no" => $second_page_rand_no]);
                    continue;
                }

                $multiple_images[$first_page_rand_no] = array("name" => $first_page_json_data[0]->image, "webp_name" => $first_page_json_data[0]->attribute1, "width" => $first_page_json_data[0]->width, "height" => $first_page_json_data[0]->height, "org_img_width" => $first_page_json_data[0]->original_img_width, "org_img_height" => $first_page_json_data[0]->original_img_height, "page_id" => $first_page_rand_no);
                $multiple_images[$second_page_rand_no] = array("name" => $second_page_json_data[0]->image, "webp_name" => $second_page_json_data[0]->attribute1, "width" => $second_page_json_data[0]->width, "height" => $second_page_json_data[0]->height, "org_img_width" => $second_page_json_data[0]->original_img_width, "org_img_height" => $second_page_json_data[0]->original_img_height, "page_id" => $second_page_rand_no);

                $first_page_json = json_decode($first_page_json_data[0]->json_data);
                $first_page_json->page_id = $first_page_rand_no;

                $second_page_json = json_decode($second_page_json_data[0]->json_data);
                $second_page_json->page_id = $second_page_rand_no;

                $all_page_new_json_data = json_decode('{}');
                $all_page_new_json_data->{$first_page_rand_no} = $first_page_json;
                $all_page_new_json_data->{$second_page_rand_no} = $second_page_json;

                $json_pages_sequence = $first_page_rand_no . "," . $second_page_rand_no;
                $search_category = implode(',', array_filter(array_unique(explode(',', $first_page_json_data[0]->search_category . "," . $second_page_json_data[0]->search_category))));

                $new_data = NULL;
                $new_data = json_decode(json_encode($first_page_json_data[0]), true);
                $new_data['id'] = NULL;
                $new_data['catalog_id'] = $new_catalog_id;
                $new_data['multiple_images'] = json_encode($multiple_images);
                $new_data['json_data'] = json_encode($all_page_new_json_data);
                $new_data['json_pages_sequence'] = $json_pages_sequence;
                $new_data['is_multipage'] = 1;
                $new_data['search_category'] = $search_category;

                $new_data_ids[] = DB::table('images')->insertGetId($new_data);
            }
            DB::commit();

            $this->deleteAllRedisKeys("getCatalogBySubCategoryId");
            $this->deleteAllRedisKeys("getDataByCatalogIdForAdmin");

            $response = Response::json(array('code' => 200, 'message' => 'Template mixed successfully.', 'cause' => '', 'data' => $new_data_ids));

        } catch (Exception $e) {
            Log::error("mixSinglePageWithMultiPage : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'add tag.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    public function autoGenerateTemplateName(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('catalog_ids', 'sub_category_id', 'app_name'), $request)) != '')
                return $response;

            $catalog_ids = $request->catalog_ids;
            $sub_category_id = $request->sub_category_id;
            $app_name = $request->app_name;
            $i = 0;
            $all_query = NULL;

            $results = DB::select('SELECT 
                                        DISTINCT im.id AS json_id,
                                        IF(im.image != "",CONCAT("' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",im.image),"") AS sample_image,
                                        cm.name AS catalog_name,
                                        im.content_type,
                                        im.updated_at
                                    FROM
                                        images AS im,
                                        catalog_master AS cm,
                                        sub_category_catalog AS scc
                                    WHERE
                                        #im.is_active = 1 AND
                                        im.catalog_id = scc.catalog_id AND
                                        cm.id = scc.catalog_id AND
                                        scc.sub_category_id = ? AND
                                        cm.id IN (' . $catalog_ids . ') AND
                                        cm.is_featured = 1 AND
                                        ISNULL(im.template_name) AND
                                        ISNULL(im.original_img) AND
                                        ISNULL(im.display_img) ', [$sub_category_id]);

            foreach ($results as $i => $result) {

                $catalog_name = $result->catalog_name;
                $position = stripos($catalog_name, $app_name);

                if ($position) {
                    $db_template_name = $template_name = trim(substr($catalog_name, 0, $position)) . " " . $app_name;
                } else {
                    $db_template_name = $template_name = $catalog_name . " " . $app_name;
                }

                $colors = array();
                $file_path = $result->sample_image;
                $handle = @fopen($file_path, 'r');

                if ($handle) {
                    //$extension = pathinfo($file_path, PATHINFO_EXTENSION);
                    $path_info = getimagesize($file_path);
                    $extension = $path_info['mime'];

                    $color_codes = $this->getColorPallet($file_path, $extension);

                    foreach ($color_codes as $color) {
                        if (strlen($color) > 3 && strlen($color) <= 7)
                            $colors[] = (new ColorInterpreterController())->name($color);
                    }

                    if (count($colors) > 1 && $colors[0]['name'] != $colors[1]['name']) {
                        $db_template_name = $colors[0]['name'] . ' and ' . $colors[1]['name'] . ' ' . $template_name;
                    } else if (count($colors) > 0) {
                        $db_template_name = $colors[0]['name'] . ' ' . $template_name;
                    }
                }

                if (strpos($db_template_name, 'Invalid Color') !== false) {
                    $db_template_name = $template_name;
                }

                $db_template_name = str_replace("'", "", $db_template_name);

                $all_query .= "UPDATE images SET template_name = '" . $db_template_name . "', updated_at = updated_at WHERE id = '" . $result->json_id . "'; ";
            }

            if ($all_query) {
                DB::beginTransaction();
                DB::unprepared("$all_query");
                DB::commit();
            }

            $response = Response::json(array('code' => 200, 'message' => 'Template name added successfully.', 'cause' => '', 'data' => $i));

        } catch (Exception $e) {
            Log::error("autoGenerateTemplateName : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'add template name.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    /* =================================|Sub Function|=============================*/
    public function getColorPallet($imageURL, $extension, $palletSize = [1, 2])
    {
        // SIMPLE CHECK INPUT VALUES
        if (!$imageURL) return false;

        if ($extension == 'image/png') {
            // IN THIS EXEMPLE WE CREATE PALLET FROM PNG IMAGE
            $img = imagecreatefrompng($imageURL);
        } else {
            // IN THIS EXEMPLE WE CREATE PALLET FROM JPG IMAGE
            $img = imagecreatefromjpeg($imageURL);
        }

        // SCALE DOWN IMAGE
        $imgSizes = getimagesize($imageURL);

        $resizedImg = imagecreatetruecolor($palletSize[0], $palletSize[1]);

        imagecopyresized($resizedImg, $img, 0, 0, 0, 0, $palletSize[0], $palletSize[1], $imgSizes[0], $imgSizes[1]);

        imagedestroy($img);

        //CHECK IMAGE
        /*header("Content-type: image/png");
        imagepng($resizedImg);
        die();*/

        //GET COLORS IN ARRAY
        $colors = [];

        for ($i = 0; $i < $palletSize[1]; $i++)
            for ($j = 0; $j < $palletSize[0]; $j++)
                $colors[] = dechex(imagecolorat($resizedImg, $j, $i));

        imagedestroy($resizedImg);

        //REMOVE DUPLICATES
        $colors = array_unique($colors);

        return $colors;

    }

    /*
    Purpose : For add H&W in existing BackGround, TextArt, Shape, Graphics.
    Description : This method compulsory take 1 argument as parameter.(if any argument is optional then define it here).
    Return : return H&W added successfully if success otherwise error with specific status code
    */
    public function addHeightWidthInSticker(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredArrayParameter(array('catalog_ids'), $request)) != '')
                return $response;

            $catalog_ids = $request->catalog_ids;
            $all_query = NULL;
            $count1 = $count2 = 0;

            foreach ($catalog_ids AS $i => $catalog_id){

                $count1++;
                $image_details = DB::select('SELECT 
                                               id,
                                               image
                                            FROM
                                               images
                                            WHERE
                                               catalog_id = ?
                                            ORDER BY id ASC', [$catalog_id]);

                foreach ($image_details AS $j => $image_detail){
                    $count2++;
                    $image_url = Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . $image_detail->image;
                    $image_details = $this->calculateHeightWidth($image_url);
                    $all_query .= "UPDATE images SET height = '".$image_details['height']."', width = '".$image_details['width']."', original_img_height = '".$image_details['org_img_height']."', original_img_width = '".$image_details['org_img_width']."', updated_at = updated_at WHERE id = '".$image_detail->id."'; ";
                }
            }

            DB::beginTransaction();
            if($all_query)
                DB::unprepared("$all_query");

            DB::commit();
            $response = Response::json(array('code' => 200, 'message' => 'H&W added successfully.', 'cause' => '', 'data' => $count1." : ".$count2));

        } catch (Exception $e) {
            Log::error("addHeightWidthInSticker : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'change page.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    public function removeDuplicateTagInUserSearchTag(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $count = 0;
            $all_query = NULL;
            DB::statement("SET sql_mode = '' ");
            $tag_details_count = DB::select('SELECT id, SUM(search_count) AS sum, COUNT(id) AS count FROM tag_analysis_master GROUP BY sub_category_id,tag,is_success HAVING COUNT(id) > 1');

            foreach ($tag_details_count AS $i => $tag_detail_count){
                $count++;
                $all_query .= "UPDATE tag_analysis_master SET search_count = '".$tag_detail_count->sum."', update_time = update_time WHERE id = '".$tag_detail_count->id."'; ";
            }

            DB::beginTransaction();
            if($all_query)
                DB::unprepared("$all_query");

            DB::delete('DELETE FROM tag_analysis_master WHERE id NOT IN (SELECT * FROM (SELECT MIN(n.id) FROM tag_analysis_master AS n GROUP BY n.sub_category_id,n.tag,n.is_success) x)');
            DB::commit();

            $this->deleteAllRedisKeys("getAllSearchingDetailsForAdmin");

            $response = Response::json(array('code' => 200, 'message' => 'Tag Deleted successfully.', 'cause' => '', 'data' => $count));

        } catch (Exception $e) {
            Log::error("removeDuplicateTagInUserSearchTag : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'change page.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    //Get Searching tag by subcategory id

    /**
     * @api {post} getSearchTagBySubCategoryId   getSearchTagBySubCategoryId
     * @apiName getSearchTagBySubCategoryId
     * @apiGroup User
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     *  "sub_category_id" : 66,     //compulsory
     *  "is_template" : 1,     //Optional pass 0 when get sticker tag,1 when get template tag
     *  "item_count" : 5,      //compulsory
     *  "page" : 1      //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "search tags fetched successfully.",
     * "cause": "",
     * "data": {
     *      "total_record": 11,
     *      "is_next_page": true,
     *      "search_tag_list": [
     *      {
     *          "id": 12,
     *          "tag_name": "Rangoli",
     *          "total": 0
     *      },
     *      {
     *          "id": 11,
     *          "tag_name": "Dipawali",
     *          "total": 0
     *      }
     *     ]
     *  }
     * }
     */
    public function getSearchTagBySubCategoryId(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('sub_category_id', 'item_count', 'page'), $request)) != '')
                return $response;

            $this->sub_category_id = $request->sub_category_id;
            $is_cache_enable = isset($request->is_cache_enable) ? $request->is_cache_enable : 1;
            $this->is_template = isset($request->is_template) ? $request->is_template : 1;
            $this->item_count_of_search_tag = Config::get("constant.ITEM_COUNT_OF_GET_DYNAMIC_SEARCH_TAG");
            $this->page = $request->page;
            $this->offset = ($this->page - 1) * $this->item_count_of_search_tag;

            if ($is_cache_enable) {

                $redis_result = Cache::remember("getSearchTagBySubCategoryId:$this->sub_category_id:$this->is_template:$this->item_count_of_search_tag:$this->page", Config::get("constant.CACHE_TIME_24_HOUR"), function () {

                    $search_tag_list = DB::select('SELECT
                                                      id,
                                                      tag_name
                                                   FROM
                                                       sub_category_tag_master
                                                   WHERE sub_category_id = ? AND
                                                         is_template=? AND 
                                                         is_active = ? 
                                                   ORDER BY update_time DESC LIMIT ?,?', [$this->sub_category_id, $this->is_template, 1, $this->offset, $this->item_count_of_search_tag]);

                    $total_row_result = DB::select('SELECT COUNT(*) as total FROM sub_category_tag_master WHERE is_active=? AND sub_category_id = ? AND is_template=?', [1, $this->sub_category_id, $this->is_template]);
                    $total_row = $total_row_result[0]->total;

                    $is_next_page = ($total_row > ($this->offset + $this->item_count_of_search_tag)) ? true : false;

                    return array('total_record' => $total_row, 'is_next_page' => $is_next_page, 'tag_list' => $search_tag_list);
                });

            } else {

                $search_tag_list = DB::select('SELECT
                                                      id,
                                                      tag_name
                                                   FROM
                                                       sub_category_tag_master
                                                   WHERE sub_category_id = ? AND
                                                         is_template=? AND 
                                                         is_active = ?
                                                   ORDER BY update_time DESC LIMIT ?,?', [$this->sub_category_id, $this->is_template, 1, $this->offset, $this->item_count_of_search_tag]);

                $total_row_result = DB::select('SELECT COUNT(*) as total FROM sub_category_tag_master WHERE is_active=? AND sub_category_id = ? AND is_template=?', [1, $this->sub_category_id, $this->is_template]);
                $total_row = $total_row_result[0]->total;

                $is_next_page = ($total_row > ($this->offset + $this->item_count_of_search_tag)) ? true : false;

                $redis_result = array('total_record' => $total_row, 'is_next_page' => $is_next_page, 'tag_list' => $search_tag_list);

            }

            $response = Response::json(array('code' => 200, 'message' => 'search tags fetched successfully.', 'cause' => '', 'data' => $redis_result));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            Log::error("getSearchTagBySubCategoryId : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get search tags.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    /**
     * @api {post} searchCatalogBySubCategoryId   searchCatalogBySubCategoryId
     * @apiName searchCatalogBySubCategoryId
     * @apiGroup User
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     *  "sub_category_id":94,
     *  "search_category":"Fog"
     *  "is_featured":1 //1=featured catalog,0= normal catalog
     *  "page":1
     *  "item_count":10
     * }
     * @apiSuccessExample Success-Response:
     * {"code":200,"message":"Catalog fetched successfully.","cause":"","data":{"total_record":1,"is_next_page":false,"result":[{"catalog_id":451,"name":"Fog Sans","thumbnail_img":"http:\/\/192.168.0.105\/photo_editor_lab_backend\/image_bucket\/thumbnail\/5c74d4b47ff01_catalog_img_1551160500.jpg","compressed_img":"http:\/\/192.168.0.105\/photo_editor_lab_backend\/image_bucket\/compressed\/5c74d4b47ff01_catalog_img_1551160500.jpg","original_img":"http:\/\/192.168.0.105\/photo_editor_lab_backend\/image_bucket\/original\/5c74d4b47ff01_catalog_img_1551160500.jpg","webp_thumbnail_img":"http:\/\/192.168.0.105\/photo_editor_lab_backend\/image_bucket\/webp_thumbnail\/5c74d4b47ff01_catalog_img_1551160500.webp","is_free":0,"is_featured":0,"search_text":16.369916915893555,"updated_at":"2021-08-25 09:54:34"}]}}
     */
    public function searchCatalogBySubCategoryId(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('search_category', 'sub_category_id', 'page', 'item_count', 'is_featured'), $request)) != '')
                return $response;

            $this->search_category = mb_strtolower(trim($request->search_category));
            $this->sub_category_id = $request->sub_category_id;
            $this->page = $request->page;
            $this->item_count = $request->item_count;
            $this->offset = ($this->page - 1) * $this->item_count;
            $this->is_featured = $request->is_featured;
            $category_id = isset($request->category_id) ? $request->category_id : Config::get('constant.CATEGORY_ID_OF_FONTS');
            $is_cache_enable = isset($request->is_cache_enable) ? $request->is_cache_enable : 1;

            if ($is_cache_enable) {

                $redis_result = Cache::remember("searchCatalogBySubCategoryId:$this->search_category:$this->sub_category_id:$this->offset:$this->item_count:$this->is_featured", Config::get('constant.CACHE_TIME_6_HOUR'), function () {
                    return $this->searchCatalogsBySearchCategory($this->search_category, $this->sub_category_id, $this->offset, $this->item_count, $this->is_featured);
                });

            } else {
                return $this->searchCatalogsBySearchCategory($this->search_category, $this->sub_category_id, $this->offset, $this->item_count, $this->is_featured);
            }

            if($this->page == 1) {
                if($redis_result['code'] != 200){
                    SaveSearchTagJob::dispatch(0, $this->search_category, $this->sub_category_id, 0, $this->is_featured, $category_id);
                }else{
                    SaveSearchTagJob::dispatch($redis_result['data']['total_record'], $this->search_category, $this->sub_category_id, 1, $this->is_featured, $category_id);
                }
            }

            $response = Response::json(array('code' => $redis_result['code'], 'message' => $redis_result['message'], 'cause' => '', 'data' => $redis_result['data']));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            Log::error("searchCatalogBySubCategoryId : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'search catalog.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }


    /**
     *
     * - Users ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/getTemplateDetail",
     *        tags={"Users"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="getTemplateDetail",
     *        summary="getTemplateDetail",
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
     *          required={"json_id"},
     *          @SWG\Property(property="json_id",  type="integer", example=1, description=""),
     *        ),
     *      ),
     * 		@SWG\Response(
     *            response=200,
     *            description="Success",
     *        @SWG\Schema(
     *          @SWG\Property(property="Sample Response",  type="string", example={"code":200,"message":"Template details fetched successfully.","cause":"","data":{}}, description=""),),
     *        ),
     * 		@SWG\Response(
     *            response=201,
     *            description="error",
     *        ),
     *    )
     *
     */
    /**
     * @api {post} getTemplateDetail   getTemplateDetail
     * @apiName getTemplateDetail
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
     * "message": "Template details fetch successfully.",
     * "cause": "",
     * "data": {
     * "json_id": 10248,
     * "sample_image": "http://192.168.0.113/videoflyer_backend/image_bucket/webp_original/5d314a352e9d3_json_image_1563511349.webp",
     * "is_free": 1,
     * "is_featured": 0,
     * "is_portrait": 1,
     * "height": 400,
     * "width": 325,
     * "json_data": {
     * "text_json": [
     * {
     * "xPos": 169,
     * "yPos": 588,
     * "color": "#000000",
     * "text": "Happy",
     * "size": 48,
     * "fontName": "ScriptMTBold",
     * "fontPath": "fonts/SCRIPTBL.ttf",
     * "alignment": 1,
     * "bg_image": "",
     * "texture_image": "",
     * "opacity": 100,
     * "angle": 0,
     * "shadowColor": "#000000",
     * "shadowRadius": 0,
     * "shadowDistance": 0
     * }
     * ],
     * "sticker_json": [
     * {
     * "xPos": 0,
     * "yPos": 560,
     * "width": 650,
     * "height": 240,
     * "sticker_image": "video_flyer_fathersday_box_kgj4_e_1.png",
     * "angle": 0,
     * "is_round": 0
     * },
     * {
     * "xPos": 333,
     * "yPos": 614,
     * "width": 230,
     * "height": 10,
     * "sticker_image": "video_flyer_fathersday_line_kgj4_e_1.png",
     * "angle": 0,
     * "is_round": 0
     * }
     * ],
     * "image_sticker_json": [],
     * "frame_json": {
     * "frame_image": "",
     * "frame_color": ""
     * },
     * "background_json": {
     * "background_image": "video_flyer_fathersday_video_kgj4_e_1.mp4",
     * "background_color": ""
     * },
     * "sample_image": "video_flyer_fathersday_sample_kgj4_e_1.jpg",
     * "height": 800,
     * "width": 650,
     * "is_portrait": 1,
     * "is_featured": 0
     * }
     * }
     * }
     * @apiSuccessExample Error-Response:
     * {
     * "code": 201,
     * "message": "Template does not exist.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function getTemplateDetail(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('json_id'), $request)) != '')
                return $response;

            $this->json_id = $request->json_id;

            if (!Cache::has("pel:getTemplateDetail$this->json_id")) {
                $result = Cache::rememberforever("getTemplateDetail$this->json_id", function () {

                    $template_details = DB::select('SELECT
                                            id as json_id,
                                            IF(attribute1 != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",attribute1),"") as sample_image,
                                            is_free,
                                            is_featured,
                                            is_portrait,
                                            coalesce(height,0) AS height,
                                            coalesce(width,0) AS width,
                                            json_data
                                           FROM
                                            images
                                           WHERE
                                            id= ?', [$this->json_id]);

                    if (count($template_details) > 0) {
                        $template_details[0]->json_data = json_decode($template_details[0]->json_data);
                        $template_details[0]->prefix_url = Config::get('constant.AWS_BUCKET_PATH_PHOTO_EDITOR_LAB') . '/';
                        $result = array('code' => 200, 'message' => 'Template details fetch successfully.', 'result' => $template_details[0]);
                    } else {
                        $result = array('code' => 201, 'message' => 'Template does not exist.', 'result' => json_decode("{}"));
                    }
                    return $result;

                });
            }

            $redis_result = Cache::get("getTemplateDetail$this->json_id");

            if (!$redis_result) {
                $redis_result = [];
            }

            $response = Response::json(array('code' => $redis_result['code'], 'message' => $redis_result['message'], 'cause' => '', 'data' => $redis_result['result']));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            Log::error("getTemplateDetail : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get template detail.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    public function spellCorrectionApi(Request $request_body)
    {
        try {
            $response = $this->spellCorrection("en", $request_body->text);

        } catch (Exception $e) {
            Log::error("spellCorrectionApi : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get template detail.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    public function oldToNewCatalogId($old_catalog_id)
    {
        try {
            $new_catalog_id = DB::select('SELECT
                                              id AS catalog_id
                                           FROM
                                              catalog_master
                                           WHERE
                                              attribute5 = ?',[$old_catalog_id]);

            if ($new_catalog_id){
                return $new_catalog_id[0]->catalog_id;
            }else{
                return '';
            }

        } catch (Exception $e) {
            Log::error("oldToNewContentId : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get new catalog id.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    public function oldToNewContentId($old_content_id)
    {
        try {
            $new_content_id = DB::select('SELECT
                                              id AS content_id
                                           FROM
                                              images
                                           WHERE
                                              attribute4 = ?',[$old_content_id]);

            if ($new_content_id){
                return $new_content_id[0]->content_id;
            }else{
                return '';
            }

        } catch (Exception $e) {
            Log::error("oldToNewContentId : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get new catalog id.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    public function collectUserGeneratedLogoData(Request $request_body)
    {
        try {
            /*$request = json_decode($request_body->input('request_data'));
            if (($response = (new VerificationController())->validateRequiredParameter(array('svg_url'), $request)) != '')
                return $response;

            $svg_url = $request->svg_url;
            $folder_name = uniqid();
            $disk = Storage::disk('s3');
            $aws_bucket = config('constant.AWS_BUCKET');
            //$svg_path = $pdf_path = '';

            if (!$request_body->hasFile('file')) {
                Log::error('collectUserGeneratedLogoData : ', ['message' => 'required field file is missing or empty.']);
            } else {
                $image_array = Input::file('file');
                if (($response = (new ImageController())->verifyPDF($image_array)) != ''){
                    Log::error('collectUserGeneratedLogoData : ', ['verifyPDF' => $response]);
                }else{
                    $pdf_name = (new ImageController())->generateNewFileName('user_logo_pdf', $image_array);
                    $original_path = '../..' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY');
                    Input::file('file')->move($original_path, $pdf_name);

                    if (config('constant.STORAGE') === 'S3_BUCKET') {
                        $original_sourceFile = '../..' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY') . $pdf_name;

                        if (($is_exist = ((new ImageController())->checkFileExist($original_sourceFile)) != 0)) {
                            $original_targetFile = "imageflyer/temp_data/$folder_name/" . $pdf_name;
                            $disk->put($original_targetFile, file_get_contents($original_sourceFile), 'public');
                            unlink($original_sourceFile);
                            //return "https://obinternalrd.s3.amazonaws.com/obinternalrd/logo_maker/$folder_name/" . $pdf_name;
                            //$pdf_path = "https://obinternalrd.s3.amazonaws.com/obinternalrd/logo_maker/$folder_name/" . $pdf_name;
                        }
                    }
                }
            }

            if(isset($request->svg_url) && $request->svg_url != ''){
                $svg_file_content = file_get_contents($svg_url);
                $extension = pathinfo($svg_url, PATHINFO_EXTENSION);
                $svg_file_name = uniqid() . '_logo_svg_' . time() . '.' . $extension;
                if(config('constant.STORAGE') === 'S3_BUCKET'){
                    $destination_path = "imageflyer/temp_data/$folder_name/" . $svg_file_name;
                    $disk->put($destination_path, $svg_file_content, 'public');
                    //return "https://obinternalrd.s3.amazonaws.com/obinternalrd/logo_maker/$folder_name/" . $svg_file_name;
                    //$svg_path = "https://obinternalrd.s3.amazonaws.com/obinternalrd/logo_maker/$folder_name/" . $svg_file_name;
                }
            }else{
                Log::error('collectUserGeneratedLogoData : ', ['message' => 'required field SVG url is missing or empty.']);
            }*/

            return Response::json(array('code' => 200, 'message' => 'Images uploaded successfully.', 'cause' => '', 'data' => json_decode('{}')));

        } catch (Exception $e) {
            Log::error("collectUserGeneratedLogoData : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            return Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'upload files.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
    }

    public function getIndustry($sub_category_id, $offset, $item_count)
    {
        $total_row_result = DB::select('SELECT COUNT(*) as total FROM post_industry WHERE sub_category_id = ? AND is_active = 1', [$sub_category_id]);
        $total_row = $total_row_result[0]->total;

        $result = DB::select('SELECT  
                                    id, 
                                    sub_category_id,
                                    IF(icon != "", CONCAT("' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '", icon),"") as icon,
                                    IF(icon_webp != "", CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '", icon_webp),"") as icon_webp,
                                    industry_name,
                                    is_active,
                                    created_at,
                                    updated_at
                               FROM 
                                    post_industry 
                               WHERE 
                                    sub_category_id = ? AND is_active = 1
                               ORDER BY updated_at DESC 
                               LIMIT ?,?', [$sub_category_id, $offset, $item_count]);

        $is_next_page = ($total_row > ($offset + $item_count)) ? true : false;

        return array('total_record' => $total_row, 'is_next_page' => $is_next_page, 'industry_list' => $result);
    }
    /**
     * @api {post} getIndustryBySubCategoryId getIndustryBySubCategoryId
     * @apiName getIndustryBySubCategoryId
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "sub_category_id":66,
     * "page":1,
     * "item_count":10,
     * "is_cache_enable":1
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Industry fetched successfully.",
     * "cause": "",
     * "data": {
     * "total_record": 1,
     * "is_next_page": false,
     * "industry_list": [
     * {
     * "id": 155,
     * "sub_category_id": 66,
     * "icon": "http://192.168.0.109/photo_editor_lab_backend/image_bucket/original/64b6353d2ec77_industry_icon_1689662781.png",
     * "icon_webp": "http://192.168.0.109/photo_editor_lab_backend/image_bucket/webp_original/64b6353d2ec77_industry_icon_1689662781.webp",
     * "industry_name": "test_industry",
     * "is_active": 1,
     * "created_at": "2023-07-18 06:46:21",
     * "updated_at": "2023-07-18 06:46:21"
     * }
     * ]
     * }
     * }
     */
    public function getIndustryBySubCategoryId(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('sub_category_id', 'page', 'item_count'), $request)) != '')
                return $response;

            $this->sub_category_id = $request->sub_category_id;
            $page = $request->page;
            $this->item_count = $request->item_count;
            $this->offset = ($page - 1) * $this->item_count;
            $is_cache_enable = isset($request->is_cache_enable) ? $request->is_cache_enable : 1;
            if ($is_cache_enable) {
                $redis_result = Cache::remember("getIndustryBySubCategoryId:$this->sub_category_id:$page:$this->item_count", Config::get('constant.CACHE_TIME_6_HOUR'), function () {
                    return $this->getIndustry($this->sub_category_id, $this->offset, $this->item_count);
                });
            } else {
                $redis_result = $this->getIndustry($this->sub_category_id, $this->offset, $this->item_count);
            }

            $response = Response::json(array('code' => 200, 'message' => 'Industry fetched successfully.', 'cause' => '', 'data' => $redis_result));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));
        } catch (Exception $e) {
            Log::error("getIndustrySubCategoryId : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get industry by sub category id.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    public function getTemplateList($sub_category_id, $template_ids, $offset, $item_count, $search_tag)
    {

        $search_tag = str_replace(',', ' ', $search_tag);
        $this->is_featured = 1;

        $total_row_result = DB::select('SELECT  
                                         COUNT(im.id) AS total
                                      FROM 
                                            images AS im,
                                            catalog_master AS cm,
                                            sub_category_catalog AS scc
                                       WHERE
                                            im.is_active = 1 AND
                                            im.catalog_id = scc.catalog_id AND
                                            cm.id = scc.catalog_id AND
                                            cm.is_featured = ? AND
                                            (im.is_multipage = 0 OR (im.is_multipage = 1 AND COALESCE(LENGTH(im.json_pages_sequence) - LENGTH(REPLACE(im.json_pages_sequence, ",","")) + 1,1) = 1)) AND
                                            scc.sub_category_id = ? AND
                                            im.original_img_height = im.original_img_width AND
                                            ISNULL(im.original_img) AND
                                            ISNULL(im.display_img) AND
                                            (im.id IN(' . $template_ids . ') OR
                                            (MATCH(im.search_category) AGAINST("' . $search_tag . '") OR 
                                            MATCH(im.search_category) AGAINST(REPLACE(concat("' . $search_tag . '"," ")," ","* ") IN BOOLEAN MODE)))
                                       ',
            [$this->is_featured, $sub_category_id]);

        $total_row = $total_row_result[0]->total;

        $host_name = request()->getHttpHost(); // With port if there is. Eg: mydomain.com:81
        $certificate_maker_host_name = Config::get('constant.HOST_NAME_OF_CERTIFICATE_MAKER');

        //to pass compress image(jpg/png) only for certificate_maker app because webp is not supported there into iOS
        $image_url = ($host_name == $certificate_maker_host_name && $sub_category_id == 4) ? 'IF(im.image != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",im.image),"") as sample_image,' : 'IF(im.attribute1 != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",im.attribute1),"") as sample_image,';
        $template_list = DB::select('SELECT
                                          im.id AS json_id,
                                          ' . $image_url . '
                                          im.is_free,
                                          im.is_featured,
                                          im.is_portrait,
                                          coalesce(im.height,0) AS height,
                                          coalesce(im.width,0) AS width,
                                          coalesce(im.original_img_height,0) AS original_img_height,
                                          coalesce(im.original_img_width,0) AS original_img_width,
                                          COALESCE(im.multiple_images,"") AS multiple_images,
                                          COALESCE(im.json_pages_sequence,"") AS pages_sequence,
                                          COALESCE(LENGTH(im.json_pages_sequence) - LENGTH(REPLACE(json_pages_sequence, ",","")) + 1,1) AS total_pages,
                                          MATCH(im.search_category) AGAINST("' . $search_tag . '") +
                                                MATCH(im.search_category) AGAINST(REPLACE(concat("' . $search_tag . '"," ")," ","* ") IN BOOLEAN MODE) AS search_text ,
                                          im.updated_at
                                        FROM
                                            images AS im,
                                            catalog_master AS cm,
                                            sub_category_catalog AS scc
                                        WHERE
                                            im.is_active = 1 AND
                                            im.catalog_id = scc.catalog_id AND
                                            cm.id = scc.catalog_id AND
                                            cm.is_featured = ? AND
                                            (im.is_multipage = 0 OR (im.is_multipage = 1 AND COALESCE(LENGTH(im.json_pages_sequence) - LENGTH(REPLACE(im.json_pages_sequence, ",","")) + 1,1) = 1)) AND
                                            scc.sub_category_id  = ? AND
                                            im.original_img_height = im.original_img_width AND
                                            ISNULL(im.original_img) AND
                                            ISNULL(im.display_img) AND
                                            (im.id IN(' . $template_ids . ') OR
                                            (MATCH(im.search_category) AGAINST("' . $search_tag . '") OR 
                                            MATCH(im.search_category) AGAINST(REPLACE(concat("' . $search_tag . '"," ")," ","* ") IN BOOLEAN MODE)))
                                         ORDER BY CASE
                                                WHEN im.id IN(' . $template_ids . ') THEN FIND_IN_SET(im.id, "' . $template_ids . '")
                                                ELSE ' . count(explode(',', $template_ids)) . ' + 1
                                            END,
                                            search_text DESC, im.updated_at DESC
                                        LIMIT ?,?',

            [$this->is_featured, $sub_category_id, $offset, $item_count]);

        $result = [
            'template_list' => $template_list,
            'total_row' => $total_row
        ];
        return $result;
    }

    public function getPosts($start_date, $industry_id, $sub_category_id, $offset, $item_count, $is_cache_enable)
    {
        $this->start_date = $start_date;
        $this->industry_id = $industry_id;
        $this->sub_category_id = $sub_category_id;

        if ($is_cache_enable) {
            $result = Cache::rememberforever("getPostByIndustryId:$this->sub_category_id:$this->industry_id:$this->start_date", function () {
                return DB::select('SELECT 
                                        psm.schedule_date, 
                                        DATE_FORMAT (psm.schedule_date, "%a %d") as display_date , 
                                        psm.template_ids, pt.id AS theme_id, 
                                        pt.theme_name, 
                                        pt.short_description AS theme_short_description, 
                                        psm.tags
                                    FROM 
                                        post_schedule_master AS psm, 
                                        post_theme AS pt
                                    WHERE 
                                        pt.id = psm.post_theme_id AND 
                                        psm.schedule_date  >= ? AND 
                                        psm.post_industry_id =? AND 
                                        psm.sub_category_id = ?
                                    ORDER BY schedule_date
                                    LIMIT 7', [$this->start_date, $this->industry_id, $this->sub_category_id]);
            });
        } else {
            $result = DB::select('SELECT 
                                        psm.schedule_date, 
                                        DATE_FORMAT (psm.schedule_date, "%a %d") as display_date , 
                                        psm.template_ids, pt.id AS theme_id, 
                                        pt.theme_name, 
                                        pt.short_description AS theme_short_description, 
                                        psm.tags
                                    FROM 
                                        post_schedule_master AS psm, 
                                        post_theme AS pt
                                    WHERE 
                                        pt.id = psm.post_theme_id AND 
                                        psm.schedule_date  >= ? AND 
                                        psm.post_industry_id =? AND 
                                        psm.sub_category_id = ?
                                    ORDER BY schedule_date
                                    LIMIT 7', [$this->start_date, $this->industry_id, $this->sub_category_id]);
        }

        $schedule_date = date('Y-m-d');
        $template_ids = [];

        foreach ($result as $item) {
            if ($item->schedule_date == $schedule_date) {
                $template_ids = $item->template_ids;
                $search_tag = $item->tags;
                break;
            } elseif ($item->schedule_date > $schedule_date) {
                $schedule_date = $item->schedule_date;
                $template_ids = $item->template_ids;
                $search_tag = $item->tags;
                break;
            }
        }

        if (isset($result) && count($result) > 1) {
            $templates = $this->getTemplateList($this->sub_category_id, $template_ids, $offset, $item_count, $search_tag);
            $sample_cards = $templates['template_list'];
            $total_row = $templates['total_row'];
        } elseif (isset($result) && count($result) == 1) {
            $schedule_date = $result[0]->schedule_date;
            $template_ids = $result[0]->template_ids;
            $search_tag = $result[0]->tags;
            $templates = $this->getTemplateList($this->sub_category_id, $template_ids, $offset, $item_count, $search_tag);
            $sample_cards = $templates['template_list'];
            $total_row = $templates['total_row'];
        } else {
            $sample_cards = [];
            $total_row = 0;
        }

        $is_next_page = ($total_row > ($offset + $item_count)) ? true : false;

        if ($this->page == 1)
            return array('total_records' => $total_row, 'is_next_page' => $is_next_page, 'schedule_date' => $schedule_date, 'schedule_theme_list' => $result, 'sample_cards' => $sample_cards);
        else
            return array('total_records' => $total_row, 'is_next_page' => $is_next_page, 'schedule_date' => $schedule_date, 'sample_cards' => $sample_cards);
    }

    public function getScheduledDatePost($schedule_date, $industry_id, $sub_category_id, $offset, $item_count)
    {
        $result = DB::select('SELECT 
                                    psm.schedule_date ,
                                    DATE_FORMAT (psm.schedule_date, "%a %d") as display_date, 
                                    psm.template_ids, pt.id AS theme_id, 
                                    pt.theme_name, 
                                    pt.short_description AS theme_short_description,
                                    psm.tags
                               FROM 
                                    post_schedule_master AS psm, 
                                    post_theme AS pt
                               WHERE 
                                    pt.id = psm.post_theme_id AND 
                                    psm.schedule_date = ? AND 
                                    psm.post_industry_id =? AND 
                                    psm.sub_category_id = ?
                               ORDER BY
                                    schedule_date',
            [$schedule_date, $industry_id, $sub_category_id]);

        if (count($result) > 0) {
            $search_tag = $result[0]->tags;
            $template_ids = $result[0]->template_ids;
            $templates = $this->getTemplateList($sub_category_id, $template_ids, $offset, $item_count, $search_tag);
            $sample_cards = $templates['template_list'];
            $total_row = $templates['total_row'];
        } else {
            $sample_cards = [];
            $total_row = 0;
        }

        $is_next_page = ($total_row > ($offset + $item_count)) ? true : false;
        return array('total_records' => $total_row, 'is_next_page' => $is_next_page, 'schedule_date' => $this->schedule_date, 'sample_cards' => $sample_cards);
    }


    /**
     * @api {post} getPostByIndustryId getPostByIndustryId
     * @apiName getPostByIndustryId
     * @apiGroup User
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     *  Key: Authorization
     *  Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     *  "sub_category_id" :66,
     *  "industry_id":100,
     *  "schedule_date":"",
     *  "page": "1",
     *  "item_count":"1",
     *  "is_cache_enable":1
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Post details fetched successfully.",
     * "cause": "",
     * "data": {
     * "total_records": 5,
     * "is_next_page": true,
     * "schedule_date": "2023-07-11",
     * "schedule_theme_list": [
     * {
     * "schedule_date": "2023-07-10",
     * "display_date": "Mon 10",
     * "template_ids": "10667,3612,3613",
     * "theme_id": 40,
     * "theme_name": "women's day",
     * "theme_short_description": "day to empower women worldwide."
     * },
     * {
     * "schedule_date": "2023-07-11",
     * "display_date": "Tue 11",
     * "template_ids": "18420,9914,9919,10597,10657",
     * "theme_id": 44,
     * "theme_name": "my theme",
     * "theme_short_description": "this theme for demo"
     * },
     * {
     * "schedule_date": "2023-07-12",
     * "display_date": "Wed 12",
     * "template_ids": "4069,3521,3522,3523",
     * "theme_id": 29,
     * "theme_name": "Management Post",
     * "theme_short_description": "For increase social traffics on your post"
     * },
     * {
     * "schedule_date": "2023-07-13",
     * "display_date": "Thu 13",
     * "template_ids": "3806,4072,3807",
     * "theme_id": 31,
     * "theme_name": "Marketing Post",
     * "theme_short_description": "To increase audience engagement"
     * }
     * ],
     * "sample_cards": [
     * {
     * "json_id": 18420,
     * "sample_image": "http://192.168.0.109/photo_editor_lab_backend/image_bucket/webp_original/61557acd10f02_json_image_1632991949.webp",
     * "is_free": 1,
     * "is_featured": 1,
     * "is_portrait": 1,
     * "height": 650,
     * "width": 550,
     * "original_img_height": 1300,
     * "original_img_width": 1100,
     * "multiple_images": "",
     * "pages_sequence": "",
     * "total_pages": 1,
     * "updated_at": "2023-03-09 09:30:47"
     * },
     * {
     * "json_id": 9914,
     * "sample_image": "http://192.168.0.109/photo_editor_lab_backend/image_bucket/webp_original/5cfe2ddeaad29_json_image_1560161758.webp",
     * "is_free": 1,
     * "is_featured": 0,
     * "is_portrait": 1,
     * "height": 540,
     * "width": 540,
     * "original_img_height": 1080,
     * "original_img_width": 1080,
     * "multiple_images": "",
     * "pages_sequence": "",
     * "total_pages": 1,
     * "updated_at": "2019-12-24 11:57:22"
     * }
     * ]
     * }
     * }
     */
    public function getPostByIndustryId(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('sub_category_id', 'industry_id', 'page', 'item_count'), $request)) != '')
                return $response;

            $this->current_date = date('Y-m-d');
            $this->start_date = date('Y-m-d', strtotime('-1 day', strtotime($this->current_date)));
            $this->industry_id = $request->industry_id;
            $this->sub_category_id = $request->sub_category_id;
            $this->schedule_date = isset($request->schedule_date) ? $request->schedule_date : NULL;
            $this->page = $request->page;
            $this->item_count = $request->item_count;
            $this->offset = ($this->page - 1) * $this->item_count;
            $is_cache_enable = isset($request->is_cache_enable) ? $request->is_cache_enable : 1;

            if ($this->schedule_date) {
                if ($is_cache_enable) {
                    $redis_result = Cache::rememberforever("getPostByIndustryId:$this->sub_category_id:$this->industry_id:schedule_date:$this->schedule_date:$this->page:$this->item_count", function () {
                        return $this->getScheduledDatePost($this->schedule_date, $this->industry_id, $this->sub_category_id, $this->offset, $this->item_count);
                    });
                } else {
                    $redis_result = $this->getScheduledDatePost($this->schedule_date, $this->industry_id, $this->sub_category_id, $this->offset, $this->item_count);
                }

            } else {
                if ($is_cache_enable) {
                    $redis_result = Cache::rememberforever("getPostByIndustryId:$this->sub_category_id:$this->industry_id:fresh_response:$this->current_date:$this->page:$this->item_count", function () {
                        return $this->getPosts($this->start_date, $this->industry_id, $this->sub_category_id, $this->offset, $this->item_count, 1);
                    });
                } else {
                    $redis_result = $this->getPosts($this->start_date, $this->industry_id, $this->sub_category_id, $this->offset, $this->item_count, 0);
                }
            }

            $response = Response::json(array('code' => 200, 'message' => 'Post details fetched successfully.', 'cause' => '', 'data' => $redis_result));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));
        } catch (Exception $e) {
            Log::error("getPostByIndustryId : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'getPostByIndustryId', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    public function checkHeightWidthOfTemplateInCatalog(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('catalog_ids'), $request)) != '')
                return $response;

            $catalog_ids = $request->catalog_ids;
            $response = DB::select('SELECT  
                                         im.id AS template_id,
                                         IF(im.image != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",im.image),"") AS sample_img,
                                         im.catalog_id,
                                         im.height,
                                         im.width,
                                         im.original_img_height,
                                         im.original_img_width,
                                         im.is_active
                                   FROM
                                        images AS im
                                   WHERE
                                        ((im.height = 0 OR im.height IS NULL OR im.height = "" ) OR (im.width = 0 OR im.width IS NULL OR im.width = "") OR (im.original_img_height = 0 OR im.original_img_height IS NULL OR im.original_img_height = "") OR (im.original_img_width = 0 OR im.original_img_width IS NULL OR im.original_img_width = "")) AND
                                        im.catalog_id IN (' . $catalog_ids . ') AND
                                        im.is_featured = 1
                                   ORDER BY im.updated_at DESC');

            $response = Response::json(array('code' => 200, 'message' => 'Template fetched successfully.', 'cause' => '', 'data' => $response));
        } catch (Exception $e) {
            Log::error("checkHeightWidthOfTemplateInCatalog : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'checkHeightWidthOfTemplateInCatalog', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;

    }

}

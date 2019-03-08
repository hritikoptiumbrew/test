<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Redis;
use App\Http\Requests;
use App\Jobs\EmailJob;
use JWTAuth;
use JWTFactory;
use Response;
use DB;
use Config;
use Exception;
use Log;
use Cache;
use HttpHeaderException;
use GuzzleHttp\Client;

class PixabayController extends Controller
{
    /* =====================================| Fetch Images |==============================================*/

    /**
     *
     * - Users ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/getImagesFromPixabay",
     *        tags={"Users"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="getImagesFromPixabay",
     *        summary="Get images from pixabay",
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
     *          required={"search_query","page","item_count"},
     *          @SWG\Property(property="search_query",  type="string", example="Forest", description=""),
     *          @SWG\Property(property="page",  type="integer", example=1, description=""),
     *        ),
     *      ),
     * 		@SWG\Response(
     *            response=200,
     *            description="success",
     *             @SWG\Schema(
     *              @SWG\Property(property="Sample_Response",  type="string", example={"code":200,"message":"Images fetched successfully.","cause":"","data":{"user_profile_url":"https://pixabay.com/users/","is_next_page":true,"is_cache":0,"result":{"totalHits":500,"hits":{{"largeImageURL":"https://pixabay.com/get/e835b60d20f6023ed1584d05fb1d4797e372e1d611b40c4090f4c97aa7eab0bbd9_1280.jpg","webformatHeight":373,"webformatWidth":640,"likes":1813,"imageWidth":3160,"id":1072823,"user_id":1720744,"views":491468,"comments":205,"pageURL":"https://pixabay.com/en/road-forest-season-autumn-fall-1072823/","imageHeight":1846,"webformatURL":"https://pixabay.com/get/e835b60d20f6023ed1584d05fb1d4797e372e1d611b40c4090f4c97aa7eab0bbd9_640.jpg","type":"photo","previewHeight":87,"tags":"road, forest, season","downloads":195129,"user":"valiunic","favorites":1559,"imageSize":3819762,"previewWidth":150,"userImageURL":"https://cdn.pixabay.com/user/2015/12/01/20-20-44-483_250x250.jpg","previewURL":"https://cdn.pixabay.com/photo/2015/12/01/20/28/road-1072823_150.jpg"}}}}}, description=""),
     *            ),
     *        ),
     * 		@SWG\Response(
     *            response=201,
     *            description="error",
     *        ),
     *    )
     *
     */

    /**
     * @api {post} getImagesFromPixabay   getImagesFromPixabay
     * @apiName getImagesFromPixabay
     * @apiGroup User
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "page":1, //compulsory
     * "search_query":"nature"
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Images fetched successfully.",
     * "cause": "",
     * "data": {
     * "user_profile_url": "https://pixabay.com/users/",
     * "is_next_page": true,
     * "is_cache": 0,
     * "result": {
     * "totalHits": 500,
     * "hits": [
     * {
     * "largeImageURL": "https://pixabay.com/get/e835b60d20f6023ed1584d05fb1d4797e47ee0dc1fb70c4090f5c071a1edb3bcdd_1280.jpg",
     * "webformatHeight": 373,
     * "webformatWidth": 640,
     * "likes": 1918,
     * "imageWidth": 3160,
     * "id": 1072823,
     * "user_id": 1720744,
     * "views": 546053,
     * "comments": 218,
     * "pageURL": "https://pixabay.com/photos/road-forest-season-autumn-fall-1072823/",
     * "imageHeight": 1846,
     * "webformatURL": "https://pixabay.com/get/e835b60d20f6023ed1584d05fb1d4797e47ee0dc1fb70c4090f5c071a1edb3bcdd_640.jpg",
     * "type": "photo",
     * "previewHeight": 87,
     * "tags": "road, forest, season",
     * "downloads": 219192,
     * "user": "valiunic",
     * "favorites": 1634,
     * "imageSize": 3819762,
     * "previewWidth": 150,
     * "userImageURL": "https://cdn.pixabay.com/user/2015/12/01/20-20-44-483_250x250.jpg",
     * "previewURL": "https://cdn.pixabay.com/photo/2015/12/01/20/28/road-1072823_150.jpg"
     * }
     * ],
     * "total": 248144
     * }
     * }
     * }
     */
    public function getImagesFromPixabay(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $keys = Config::get('constant.PIXABAY_API_KEY');
            $request = json_decode($request_body->getContent());

            if (($response = (new VerificationController())->validateRequiredParameter(array('page'), $request)) != '')
                return $response;

            $search_query = isset($request->search_query) ? strtolower(trim($request->search_query)) : "";
            $page = $request->page;
            $per_page = Config::get('constant.PIXABAY_IMAGES_ITEM_COUNT');

            if (($response = (new VerificationController())->validateItemCount($per_page)) != '')
                return $response;

            $key = explode(',', $keys);
            $i = 0;
            foreach ($key as $k) {
                $kt[$i] = $k;
                $i++;
            }

            $redis_keys = Redis::keys('pel:currentKey:*');

            count($redis_keys) > 0 ? $this->currentKey = substr($redis_keys[0], -1) : $this->currentKey = 0;

            $result = $this->getPixabayImageForUser($search_query, $page, $per_page, $kt[$this->currentKey]);

            if ($result === 429) {
                $redis_keys = Redis::keys('pel:currentKey:*');
                count($redis_keys) > 0 ? $this->currentKey = substr($redis_keys[0], -1) : $this->currentKey = 0;
                foreach ($redis_keys as $key) {
                    //Log::info($key);
                    $this->deleteRedisKey($key);
                }

                $getKey = $this->increaseCurrentKey($this->currentKey);

                $currentKey = $getKey + 1;
                $template = 'stock_photos';
                $host_name = request()->getHttpHost();
                $subject = "Photo Editor Lab: Pixabay rate limit exceeded (host: $host_name).";
                $message_body = array(
                    'message' => "The Rate limit for Pixabay is exceeded now.<br> The updated key is <b>$currentKey</b>.",
                    'user_name' => 'Admin'
                );
                $api_name = 'getImagesFromPixabay';
                $api_description = 'Get images from pixabay for user.';
                $email_id = 'pooja.optimumbrew@gmail.com';
                $this->dispatch(new EmailJob(1, $email_id, $subject, $message_body, $template, $api_name, $api_description));

                return Response::json(array('code' => 201, 'message' => 'The server is unable to load images. Please try again after 10-20 minutes.', 'cause' => '', 'data' => json_decode('{}')));

            }

            if ($result === 201) {
                return Response::json(array('code' => 201, 'message' => "Sorry, we couldn't find icon for " . $search_query, 'cause' => '', 'data' => json_decode('{}')));
            }

            $response = Response::json(array('code' => 200, 'message' => 'Images fetched successfully.', 'cause' => '', 'data' => $result));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));
            /*return  $getKey = $this->increaseCurrentKey($this->currentKey);*/

        } catch (Exception $e) {
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get images from pixabay.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            Log::error("getImagesFromPixabay : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            DB::rollBack();
        }
        return $response;
    }

    /* =====================================| Function |==============================================*/

    public function getCurrentKey()
    {
        try {

            $redis_keys = Redis::keys('pel:currentKey*');
            $result = isset($redis_keys) ? $redis_keys : '{}';

            $response = Response::json(array('code' => 200, 'message' => 'Current key fetched successfully.', 'cause' => '', 'data' => $result));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get current key.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            Log::error("getCurrentKey : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            DB::rollBack();
        }
        return $response;
    }

    public function increaseCurrentKey($currentKey)
    {
        try {
            $keys = Config::get('constant.PIXABAY_API_KEY');
            $key = explode(',', $keys);
            $i = 0;
            foreach ($key as $k) {
                $kt[$i] = $k;
                $i++;
            }
            $countKey = $i - 1;
            $this->currentKey = $currentKey;
            if ($this->currentKey == $countKey) {
                $this->currentKey = 0;
            } else {
                $this->currentKey = $this->currentKey + 1;
            }

            if (!Cache::has("pel:currentKey:$this->currentKey")) {
                $result = Cache::remember("currentKey:$this->currentKey", 29, function () {
                    Log::info('Current Key :'.$this->currentKey);
                    return $this->currentKey;
                });
            }
            $redis_result = Cache::get("currentKey:$this->currentKey");
            Redis::expire("currentKey:$this->currentKey", 1);
            $response = $redis_result;
        } catch (Exception $e) {
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'change API key of pixabay.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            Log::error("increaseCurrentKey : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            DB::rollBack();
        }
        return $response;
    }

    public function getPixabayImageForUser($category, $page, $per_page, $key)
    {
        try {
            $this->category = $category;
            $this->page = $page;
            $this->per_page = $per_page;
            $this->is_cache = 1;

            $this->url = Config::get('constant.PIXABAY_API_URL') . '?key=' . $key . '&q=' . $this->category . '&page=' . $this->page . '&per_page=' . $this->per_page;

            Log::info('url : ',['url' => $this->url]);

            if (!Cache::has("pel:getPixabayImageForUser:$this->category:$this->page:$this->per_page")) {
                $result = Cache::remember("getPixabayImageForUser:$this->category:$this->page:$this->per_page", 1440, function () {

                    $this->is_cache = 0;
                    $client = new Client();
                    try {
                        $response = $client->request('get', $this->url);
                        $this->getRateRemaining = $response->getHeader('X-RateLimit-Remaining');

                        if (count($this->getRateRemaining) > 0) {
                            $rate_remaining = intval($this->getRateRemaining[0]);
                        } else {
                            $rate_remaining = 0;
                        }
                        Log::info('Rate remaining : ',['rate_limit' => $rate_remaining]);
                        if ($rate_remaining <= 100) {

                            $redis_keys = Redis::keys('pel:currentKey:*');
                            count($redis_keys) > 0 ? $this->currentKey = substr($redis_keys[0], -1) : $this->currentKey = 0;

                            foreach ($redis_keys as $key) {
                                $this->deleteRedisKey($key);
                            }

                            $getKey = $this->increaseCurrentKey($this->currentKey);
                            $host_name = request()->getHttpHost();
                            $currentKey = $getKey + 1;
                            $template = 'stock_photos';
                            $subject = "Photo Editor Lab: Pixabay rate limit <= 100 (host: $host_name).";
                            $message_body = array(
                                'message' => "100 request is remaining from key <b>$getKey</b>.<br>Your currently used key is <b>$currentKey</b>.",
                                'user_name' => 'Admin'
                            );
                            $api_name = 'getPixabayImageForUser';
                            $api_description = 'Get pixabay image for user.';
                            $email_id = 'pooja.optimumbrew@gmail.com';
                            $this->dispatch(new EmailJob(1, $email_id, $subject, $message_body, $template, $api_name, $api_description));

                        }
                        $http_status = $response->getStatusCode();
                        $result = json_decode($response->getBody()->getContents(), true);
                    } catch (Exception $e) {
                        $http_status = $e->getResponse()->getStatusCode();
                        $result = $e->getResponse()->getBody()->getContents();
                    }
                    return ['http_status' => $http_status, 'result' => $result];
                });
            }

            $this->is_cache == 0 ? $is_cache = 0 : $is_cache = 1;

            $redis_result = Cache::get("getPixabayImageForUser:$this->category:$this->page:$this->per_page");

            Redis::expire("getPixabayImageForUser:$this->category:$this->page:$this->per_page", 1);

            if ($redis_result['http_status'] === 429) {
                $this->deleteRedisKey("pel:getPixabayImageForUser:$this->category:$this->page:$this->per_page");
                return 429;
                //$response = Response::json(array('code' => 201, 'message' => 'The server is unable to load images. Please try again after 10-20 minutes.', 'cause' => '', 'data' => json_decode('{}')));
            }
            if ($redis_result['http_status'] != 200) {
                Log::error("getPixabayImageForUser failed to fetch stock images : \n", ["http_status_code" => $redis_result['http_status'], "\nresult" => $redis_result['result']]);
                return 201;
            } else {
                $total_row = isset($redis_result['result']['totalHits']) ? $redis_result['result']['totalHits'] : 0;
            }

            if($total_row <= 0)
            {
                return 201;
            }

            $this->offset = ($page - 1) * $this->per_page;

            $is_next_page = ($total_row > ($this->offset + $this->per_page)) ? true : false;
            return ['user_profile_url' => 'https://pixabay.com/users/', 'is_next_page' => $is_next_page, 'is_cache' => $is_cache, 'result' => $redis_result['result']];


        } catch (Exception $e) {
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get images from pixabay.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            Log::error("getPixabayImageForUser : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            DB::rollBack();
        }
        return $response;

    }

    public function deleteRedisKey($keys)
    {
        try {

            Redis::del($keys);
            $redis_keys = Redis::keys($keys);

            $response = $redis_keys ? 0 : 1;
        } catch (Exception $e) {
            Log::error("deleteRedisKey : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'delete redis keys.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

}

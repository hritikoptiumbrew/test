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
     *          @SWG\Property(property="item_count",  type="integer", example=5, description="Item count must be >= 3 and <= 200"),
     *        ),
     *      ),
     * 		@SWG\Response(
     *            response=200,
     *            description="success",
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
     * "item_count":4, //compulsory and must be >=3 && <=200
     * "search_query":"nature"
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Images fetched successfully.",
     * "cause": "",
     * "data": {
     * "is_next_page": true,
     * "is_cache": 0,
     * "result": {
     * "totalHits": 500,
     * "hits": [
     * {
     * "largeImageURL": "https://pixabay.com/get/e835b60d20f6023ed1584d05fb1d4f95e571e5d21eac104491f5c678a4edbcbe_1280.jpg",
     * "webformatHeight": 373,
     * "webformatWidth": 640,
     * "likes": 1736,
     * "imageWidth": 3160,
     * "id": 1072823,
     * "user_id": 1720744,
     * "views": 455334,
     * "comments": 199,
     * "pageURL": "https://pixabay.com/en/road-forest-season-autumn-fall-1072823/",
     * "imageHeight": 1846,
     * "webformatURL": "https://pixabay.com/get/e835b60d20f6023ed1584d05fb1d4f95e571e5d21eac104491f5c678a4edbcbe_640.jpg",
     * "type": "photo",
     * "previewHeight": 87,
     * "tags": "road, forest, season",
     * "downloads": 180992,
     * "user": "valiunic",
     * "favorites": 1503,
     * "imageSize": 3819762,
     * "previewWidth": 150,
     * "userImageURL": "https://cdn.pixabay.com/user/2015/12/01/20-20-44-483_250x250.jpg",
     * "previewURL": "https://cdn.pixabay.com/photo/2015/12/01/20/28/road-1072823_150.jpg"
     * }
     * ],
     * "total": 238950
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

            if (($response = (new VerificationController())->validateRequiredParameter(array('page', 'item_count'), $request)) != '')
                return $response;

            $search_query = isset($request->search_query) ? $request->search_query : "";
            $page = $request->page;
            $per_page = $request->item_count;

            if (($response = (new VerificationController())->validateItemCount($per_page)) != '')
                return $response;

            $key = explode(',', $keys);
            $i = 0;
            foreach ($key as $k) {
                $kt[$i] = $k;
                $i++;
            }

            if ($search_query == "" or $search_query == NULL) {
                $search_query = 'All';
            }
            $search_query = strtolower($search_query);

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
                $subject = 'Pixabay rate limit exceeded.';
                //$message_body = "Now, The current key is $currentKey.";
                $message_body = array(
                    'message' => "The Rate limit for Pixabay is exceeded now.<br> The updated key is $currentKey.",
                    'user_name' => 'Admin'
                );
                $api_name = 'getImagesFromPixabay';
                $api_description = 'Get images from pixabay for user.';
                $email_id = 'pooja.optimumbrew@gmail.com';
                $this->dispatch(new EmailJob(1, $email_id, $subject, $message_body, $template, $api_name, $api_description));

                return Response::json(array('code' => 201, 'message' => 'The server is unable to load images. Please try again after 10-20 minutes.', 'cause' => '', 'data' => json_decode('{}')));

            }

            $response = Response::json(array('code' => 200, 'message' => 'Images fetched successfully.', 'cause' => '', 'data' => $result));;
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));
            /*return  $getKey = $this->increaseCurrentKey($this->currentKey);*/

        } catch (Exception $e) {
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get images from pixabay.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            Log::error("getImagesFromPixabay : ", ['Exception' => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            DB::rollBack();
        }
        return $response;
    }

    /* =====================================| Fetch Videos |==============================================*/

    /**
     *
     * - Users ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/getVideosFromPixabay",
     *        tags={"Users"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="getVideosFromPixabay",
     *        summary="Get videos from pixabay",
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
     *          @SWG\Property(property="item_count",  type="integer", example=5, description="Item count must be >= 3 and <= 200"),
     *        ),
     *      ),
     * 		@SWG\Response(
     *            response=200,
     *            description="success",
     *        ),
     * 		@SWG\Response(
     *            response=201,
     *            description="error",
     *        ),
     *    )
     *
     */
    public function getVideosFromPixabay(Request $request_body)
    {
        try {
            $keys = Config::get('constant.PIXABAY_API_KEY');
            $request = json_decode($request_body->getContent());

            if (($response = (new VerificationController())->validateRequiredParameter(array('search_query', 'page', 'item_count'), $request)) != '')
                return $response;

            $search_query = $request->search_query;
            $page = $request->page;
            $per_page = $request->item_count;

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


            $result = $this->getPixabayVideoForUser($search_query, $page, $per_page, $kt[$this->currentKey]);

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
                $subject = 'Pixabay rate limit exceeded.';
                //$message_body = "Now, The current key is $currentKey.";
                $message_body = array(
                    'message' => "The Rate limit for Pixabay is exceeded now.<br> The updated key is $currentKey.",
                    'user_name' => 'Admin'
                );
                $api_name = 'getVideosFromPixabay';
                $api_description = 'Get videos from pixabay for user.';
                $email_id = 'pooja.optimumbrew@gmail.com';
                $this->dispatch(new EmailJob(1, $email_id, $subject, $message_body, $template, $api_name, $api_description));

                return Response::json(array('code' => 201, 'message' => 'The server is unable to load videos. Please try again after 10-20 minutes.', 'cause' => '', 'data' => json_decode('{}')));
            }

            $response = Response::json(array('code' => 200, 'message' => 'Videos fetched successfully.', 'cause' => '', 'data' => $result));;
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));
            /*return  $getKey = $this->increaseCurrentKey($this->currentKey);*/

        } catch (Exception $e) {
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get videos from pixabay.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            Log::error("getVideosFromPixabay : ", ['Exception' => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            DB::rollBack();
        }
        return $response;
    }

    /* =====================================| Function |==============================================*/

    public function getCurrentKey()
    {
        try {

            $redis_keys = Redis::keys('pel:currentKey*');
            $key = explode(',', $redis_keys);

            $result = isset($redis_keys) ? $redis_keys : '{}';

            $response = Response::json(array('code' => 200, 'message' => 'Current key fetched successfully.', 'cause' => '', 'data' => $result));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get current key.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            Log::error("getCurrentKey : ", ['Exception' => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
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
                    //Log::info('Current Key :'.$this->currentKey);
                    return $this->currentKey;
                });
            }
            $redis_result = Cache::get("currentKey:$this->currentKey");
            Redis::expire("currentKey:$this->currentKey", 1);
            $response = $redis_result;
        } catch (Exception $e) {
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'change API key of pixabay.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            Log::error("increaseCurrentKey : ", ['Exception' => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
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

            //Log::info('url : ', ['url' => $this->url]);

            if (!Cache::has("pel:getPixabayImageForUser:$this->category:$this->page:$this->per_page")) {
                $result = Cache::remember("getPixabayImageForUser:$this->category:$this->page:$this->per_page", 1440, function () {

                    $this->is_cache = 0;
                    $client = new Client();
                    try {
                        $response = $client->request('get', $this->url);
                        $this->getRateRemaining = $response->getHeader('X-RateLimit-Remaining');
                        //Log::info('rate limit : ', ['rate_limit' => $this->getRateRemaining]);
                        if (count($this->getRateRemaining) > 0) {
                            $rate_remaining = intval($this->getRateRemaining[0]);
                        } else {
                            $rate_remaining = 0;
                        }

                        if ($rate_remaining <= 100) {

                            $redis_keys = Redis::keys('pel:currentKey:*');
                            count($redis_keys) > 0 ? $this->currentKey = substr($redis_keys[0], -1) : $this->currentKey = 0;

                            foreach ($redis_keys as $key) {
                                $this->deleteRedisKey($key);
                            }

                            $getKey = $this->increaseCurrentKey($this->currentKey);

                            $currentKey = $getKey + 1;
                            $template = 'stock_photos';
                            $subject = 'Pixabay rate limit <= 100.';
                            $message_body = array(
                                'message' => "100 request is remaining from key $getKey.<br>Your currently used key is $currentKey.",
                                'user_name' => 'Admin'
                            );
                            $api_name = 'getPixabayImageForUser';
                            $api_description = 'Get pixabay image for user.';
                            $email_id = 'pooja.optimumbrew@gmail.com';
                            $this->dispatch(new EmailJob(1, $email_id, $subject, $message_body, $template, $api_name, $api_description));

                        }
                        return $redis_result = json_decode($response->getBody()->getContents(), true);
                    } catch (Exception $e) {
                        return $e->getResponse()->getStatusCode();
                    }
                });
            }

            $this->is_cache == 0 ? $is_cache = 0 : $is_cache = 1;

            $redis_result = Cache::get("getPixabayImageForUser:$this->category:$this->page:$this->per_page");

            Redis::expire("getPixabayImageForUser:$this->category:$this->page:$this->per_page", 1);

            if ($redis_result === 429) {
                $this->deleteRedisKey("pel:getPixabayImageForUser:$this->category:$this->page:$this->per_page");
                return 429;
                //$response = Response::json(array('code' => 201, 'message' => 'The server is unable to load images. Please try again after 10-20 minutes.', 'cause' => '', 'data' => json_decode('{}')));
            }

            if (!$redis_result) {
                $redis_result = [];
                $total_row = 0;
            } else {
                $total_row = isset($redis_result['totalHits']) ? $redis_result['totalHits'] : 0;
            }

            $this->offset = ($page - 1) * $this->per_page;

            $is_next_page = ($total_row > ($this->offset + $this->per_page)) ? true : false;
            return ['is_next_page' => $is_next_page, 'is_cache' => $is_cache, 'result' => $redis_result];


        } catch (Exception $e) {
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get images from pixabay.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            Log::error("getPixabayImageForUser : ", ['Exception' => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            DB::rollBack();
        }
        return $response;

    }

    public function getPixabayVideoForUser($category, $page, $per_page, $key)
    {
        try {
            $this->category = $category;
            $this->page = $page;
            $this->per_page = $per_page;
            $this->is_cache = 1;


            $this->url = Config::get('constant.PIXABAY_API_URL') . 'videos/?key=' . $key . '&q=' . $this->category . '&page=' . $this->page . '&per_page=' . $this->per_page;

            if (!Cache::has("pel:getPixabayVideoForUser:$this->category:$this->page:$this->per_page")) {
                $result = Cache::remember("getPixabayVideoForUser:$this->category:$this->page:$this->per_page", 1440, function () {

                    $this->is_cache = 0;
                    $client = new Client();
                    try {
                        $response = $client->request('get', $this->url);
                        $this->getRateRemaining = $response->getHeader('X-RateLimit-Remaining');
                        if ($this->getRateRemaining <= 100) {
                            //log::info('Rate Remaining :' . $this->getRateRemaining);
                            $redis_keys = Redis::keys('pel:currentKey:*');
                            count($redis_keys) > 0 ? $this->currentKey = substr($redis_keys[0], -1) : $this->currentKey = 0;
                            foreach ($redis_keys as $key) {
                                //Log::info($key);
                                $this->deleteRedisKey($key);
                            }

                            $getKey = $this->increaseCurrentKey($this->currentKey);

                            $currentKey = $getKey + 1;
                            $template = 'stock_photos';
                            $subject = 'Pixabay rate limit <= 100.';
                            //$message_body = "100 request is remaining $getKey. Your currently used key is $currentKey.";
                            $message_body = array(
                                'message' => "100 request is remaining from key $getKey.<br>Your currently used key is $currentKey.",
                                'user_name' => 'Admin'
                            );
                            $api_name = 'getPixabayVideoForUser';
                            $api_description = 'Get videos from pixabay for user.';
                            $email_id = 'pooja.optimumbrew@gmail.com';
                            $this->dispatch(new EmailJob(1, $email_id, $subject, $message_body, $template, $api_name, $api_description));
                        }
                        return json_decode($response->getBody()->getContents());
                    } catch (Exception $e) {
                        return $e->getResponse()->getStatusCode();
                    }
                });
            }

            $this->is_cache == 0 ? $is_cache = 0 : $is_cache = 1;

            $redis_result = Cache::get("getPixabayVideoForUser:$this->category:$this->page:$this->per_page");

            Redis::expire("getPixabayVideoForUser:$this->category:$this->page:$this->per_page", 1);

            if ($redis_result === 429) {
                $this->deleteRedisKey("pel:getPixabayVideoForUser:$this->category:$this->page:$this->per_page");
                return 429;
                /*$response = Response::json(array('code' => 201, 'message' => 'The server is unable to load images. Please try again after 10-20 minutes.', 'cause' => '', 'data' => json_decode('{}')));*/
            }

            if (!$redis_result) {
                $redis_result = [];
            }

            $this->offset = ($page - 1) * $this->per_page;
            $total_row = $redis_result['totalHits'];
            $is_next_page = ($total_row > ($this->offset + $this->per_page)) ? true : false;
            return ['is_next_page' => $is_next_page, 'is_cache' => $is_cache, 'result' => $redis_result];

            //return ['is_cache' => $is_cache, 'result' => $redis_result];

        } catch (Exception $e) {
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get videos from pixabay.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            Log::error("getPixabayVideoForUser : ", ['Exception' => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
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
            Log::error("deleteRedisKey : ", ['Exception' => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'Delete Redis Keys.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

}

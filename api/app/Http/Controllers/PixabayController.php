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
     * "category":"flower",
     * "page":1,
     * "item_count":3
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Images are fatched successfully.",
     * "cause": "",
     * "data": {
     * "is_cache": 0,
     * "result": {
     * "totalHits": 500,
     * "hits": [
     * {
     * "largeImageURL": "https://pixabay.com/get/e831b30f2afc1c22d2524518b7454590e570e5d204b014439cf9c87caee8b6_1280.jpg",
     * "webformatHeight": 640,
     * "webformatWidth": 425,
     * "likes": 182,
     * "imageWidth": 2136,
     * "id": 142028,
     * "user_id": 41549,
     * "views": 45888,
     * "comments": 15,
     * "pageURL": "https://pixabay.com/en/lotus-pink-lotus-flower-plant-flowers-lo-142028/",
     * "imageHeight": 3216,
     * "webformatURL": "https://pixabay.com/get/e831b30f2afc1c22d2524518b7454590e570e5d204b014439cf9c87caee8b6_640.jpg",
     * "type": "photo",
     * "previewHeight": 150,
     * "tags": "lotus pink lotus flower plant flowers lotus flower flower flower flowers flowers flowers flowers flowers",
     * "downloads": 12277,
     * "user": "artzhangqingfeng",
     * "favorites": 173,
     * "imageSize": 978322,
     * "previewWidth": 100,
     * "userImageURL": "https://cdn.pixabay.com/user/2017/08/17/18-37-52-458_250x250.jpg",
     * "previewURL": "https://cdn.pixabay.com/photo/2013/06/29/06/24/lotus-142028_150.jpg"
     * },
     * {
     * "largeImageURL": "https://pixabay.com/get/ef30b9082df51c22d2524518b7454590e570e5d204b014439cf9c87caee8b6_1280.jpg",
     * "webformatHeight": 640,
     * "webformatWidth": 612,
     * "likes": 164,
     * "imageWidth": 2200,
     * "id": 658751,
     * "user_id": 784204,
     * "views": 37932,
     * "comments": 20,
     * "pageURL": "https://pixabay.com/en/bells-flower-flowers-blue-flower-black-n-658751/",
     * "imageHeight": 2300,
     * "webformatURL": "https://pixabay.com/get/ef30b9082df51c22d2524518b7454590e570e5d204b014439cf9c87caee8b6_640.jpg",
     * "type": "photo",
     * "previewHeight": 150,
     * "tags": "bells flower flowers blue flower black nature spring blue purple flower blue flower background flowers flowers flowers flowers flowers blue flower flower",
     * "downloads": 18587,
     * "user": "Catharina77",
     * "favorites": 197,
     * "imageSize": 294501,
     * "previewWidth": 144,
     * "userImageURL": "https://cdn.pixabay.com/user/2015/11/27/12-31-26-612_250x250.jpg",
     * "previewURL": "https://cdn.pixabay.com/photo/2015/03/04/12/59/bells-flower-658751_150.jpg"
     * },
     * {
     * "largeImageURL": "https://pixabay.com/get/ea37b1072ff01c22d2524518b7454590e570e5d204b014439cf9c87caee8b6_1280.jpg",
     * "webformatHeight": 640,
     * "webformatWidth": 437,
     * "likes": 159,
     * "imageWidth": 1365,
     * "id": 320874,
     * "user_id": 217857,
     * "views": 38553,
     * "comments": 19,
     * "pageURL": "https://pixabay.com/en/tulip-flower-bloom-pink-flowers-spring-n-320874/",
     * "imageHeight": 2000,
     * "webformatURL": "https://pixabay.com/get/ea37b1072ff01c22d2524518b7454590e570e5d204b014439cf9c87caee8b6_640.jpg",
     * "type": "photo",
     * "previewHeight": 150,
     * "tags": "tulip flower bloom pink flowers spring nature tulip tulip tulip flower flower flower flowers flowers flowers flowers flowers spring spring nature",
     * "downloads": 13673,
     * "user": "Anelka",
     * "favorites": 159,
     * "imageSize": 420525,
     * "previewWidth": 102,
     * "userImageURL": "https://cdn.pixabay.com/user/2014/04/10/14-20-41-498_250x250.jpg",
     * "previewURL": "https://cdn.pixabay.com/photo/2014/04/10/11/27/tulip-320874_150.jpg"
     * }
     * ],
     * "total": 16885
     * }
     * }
     * }
     */
    public function getImagesFromPixabay(Request $request_body){
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $keys = Config::get('constant.PIXABAY_API_KEY');
            $request = json_decode($request_body->getContent());

            if (($response = (new VerificationController())->validateRequiredParameter(array('category', 'page', 'item_count'), $request)) != '')
                return $response;

            $category = $request->category;
            $page = $request->page;
            $per_page = $request->item_count;

            if (($response = (new VerificationController())->validateItemCount($per_page)) != '')
                return $response;

            $key = explode(',', $keys);
            $i =0 ;
            foreach($key as $k){
                $kt[$i] = $k;
                $i++;
            }

            $redis_keys = Redis::keys('pel:currentKey:*');

            count($redis_keys) > 0 ? $this->currentKey = substr($redis_keys[0], -1) : $this->currentKey = 0;

            $result = $this->getPixabayImageForUser($category,$page,$per_page,$kt[$this->currentKey]);

            if($result === 429 ){
                $redis_keys = Redis::keys('pel:currentKey:*');
                count($redis_keys) > 0 ? $this->currentKey = substr($redis_keys[0], -1) : $this->currentKey = 0;
                $this->deleteRedisKey($redis_keys);
                $getKey = $this->increaseCurrentKey($this->currentKey);

                $currentKey = $getKey +1;
                $template = 'simple';
                $subject = 'Pixabay rate limit exceeded.';
                $message_body = "Now, The current key is $currentKey.";
                $api_name = 'getImagesFromPixabay';
                $api_description = 'Get images from pixabay for user.';
                $email_id = 'pooja.optimumbrew@gmail.com';
                $this->dispatch(new EmailJob(1, $email_id, $subject, $message_body, $template, $api_name, $api_description));

                return Response::json(array('code' => 201, 'message' => 'The server is unable to load images. Please try again after 10-20 minutes.', 'cause' => '', 'data' => json_decode('{}')));

            }

            $response = Response::json(array('code' => 200, 'message' => 'Images fetched successfully.', 'cause' => '', 'data' => $result));;
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));
            /*return  $getKey = $this->increaseCurrentKey($this->currentKey);*/

        }catch (Exception $e) {
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get images from pixabay.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            Log::error("getImagesFromPixabay :", ['Exception' => $e->getMessage(), "\nTraceAsString :" => $e->getTraceAsString()]);
            DB::rollBack();
        }
        return $response;
    }

    /* =====================================| Fetch Videos |==============================================*/

    /**
     * @api {post} getVideosFromPixabay   getVideosFromPixabay
     * @apiName getVideosFromPixabay
     * @apiGroup User
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "category":"water",
     * "page":1,
     * "item_count":3
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "videos are fatched successfully.",
     * "cause": "",
     * "data": {
     * "is_cache": 0,
     * "result": {
     * "totalHits": 500,
     * "hits": [
     * {
     * "picture_id": "529921736",
     * "videos": {
     * "large": {
     * "url": "",
     * "width": 0,
     * "size": 0,
     * "height": 0
     * },
     * "small": {
     * "url": "https://player.vimeo.com/external/135733055.sd.mp4?s=4755d2adc868862c3e5bb601a4841c32debe22c8&profile_id=112",
     * "width": 640,
     * "size": 3709798,
     * "height": 310
     * },
     * "medium": {
     * "url": "https://player.vimeo.com/external/135733055.hd.mp4?s=d8ad3de28c7bda1746059d926a2cde7d4198348c&profile_id=113",
     * "width": 1280,
     * "size": 11624131,
     * "height": 620
     * },
     * "tiny": {
     * "url": "https://player.vimeo.com/external/135733055.mobile.mp4?s=326829edd29f45b58f45cfaaa1107d13ecb17eb6&profile_id=116",
     * "width": 480,
     * "size": 1747635,
     * "height": 232
     * }
     * },
     * "tags": "rain, thunder, water",
     * "downloads": 34362,
     * "likes": 256,
     * "favorites": 204,
     * "duration": 36,
     * "id": 78,
     * "user_id": 1280814,
     * "views": 92826,
     * "comments": 41,
     * "userImageURL": "https://cdn.pixabay.com/user/2015/08/07/22-32-32-276_250x250.jpg",
     * "pageURL": "https://pixabay.com/videos/id-78/",
     * "type": "film",
     * "user": "DistillVideos"
     * },
     * {
     * "picture_id": "583481279",
     * "videos": {
     * "large": {
     * "url": "https://player.vimeo.com/external/176282263.hd.mp4?s=5ae9c441e89ee36646286c22fddc6c8781946c7d&profile_id=169",
     * "width": 1920,
     * "size": 32514692,
     * "height": 1080
     * },
     * "small": {
     * "url": "https://player.vimeo.com/external/176282263.sd.mp4?s=eae20877d2f66cd5b7481c8e9ac2b4b10fd92bef&profile_id=165",
     * "width": 960,
     * "size": 7392540,
     * "height": 540
     * },
     * "medium": {
     * "url": "https://player.vimeo.com/external/176282263.hd.mp4?s=5ae9c441e89ee36646286c22fddc6c8781946c7d&profile_id=174",
     * "width": 1280,
     * "size": 12518329,
     * "height": 720
     * },
     * "tiny": {
     * "url": "https://player.vimeo.com/external/176282263.sd.mp4?s=eae20877d2f66cd5b7481c8e9ac2b4b10fd92bef&profile_id=164",
     * "width": 640,
     * "size": 2470905,
     * "height": 360
     * }
     * },
     * "tags": "sea, wave, golden",
     * "downloads": 54973,
     * "likes": 341,
     * "favorites": 291,
     * "duration": 40,
     * "id": 4006,
     * "user_id": 1024927,
     * "views": 108996,
     * "comments": 64,
     * "userImageURL": "https://cdn.pixabay.com/user/2017/10/03/16-01-13-529_250x250.png",
     * "pageURL": "https://pixabay.com/videos/id-4006/",
     * "type": "film",
     * "user": "outlinez"
     * },
     * {
     * "picture_id": "540200545",
     * "videos": {
     * "large": {
     * "url": "https://player.vimeo.com/external/142801793.hd.mp4?s=7fb230aa374b14694792fc7ab23e31ca40cd4117&profile_id=119",
     * "width": 1920,
     * "size": 8542568,
     * "height": 1080
     * },
     * "small": {
     * "url": "https://player.vimeo.com/external/142801793.sd.mp4?s=452bc0b5bb4ddc978189a41e1eacdc3409edb7e8&profile_id=112",
     * "width": 640,
     * "size": 1448790,
     * "height": 360
     * },
     * "medium": {
     * "url": "https://player.vimeo.com/external/142801793.hd.mp4?s=7fb230aa374b14694792fc7ab23e31ca40cd4117&profile_id=113",
     * "width": 1280,
     * "size": 4826988,
     * "height": 720
     * },
     * "tiny": {
     * "url": "https://player.vimeo.com/external/142801793.mobile.mp4?s=b7f949c7dab73b2c48da20ee73d10c48a6f68a18&profile_id=116",
     * "width": 480,
     * "size": 579502,
     * "height": 270
     * }
     * },
     * "tags": "bubbles, air, underwater",
     * "downloads": 33843,
     * "likes": 219,
     * "favorites": 231,
     * "duration": 15,
     * "id": 1085,
     * "user_id": 1283884,
     * "views": 64453,
     * "comments": 29,
     * "userImageURL": "https://cdn.pixabay.com/user/2015/08/09/12-33-44-788_250x250.png",
     * "pageURL": "https://pixabay.com/videos/id-1085/",
     * "type": "film",
     * "user": "Vimeo-Free-Videos"
     * }
     * ],
     * "total": 1159
     * }
     * }
     * }
     */
    public function getVideosFromPixabay(Request $request_body)
    {
        try {
            $keys = Config::get('constant.PIXABAY_API_KEY');
            $request = json_decode($request_body->getContent());

            if (($response = (new VerificationController())->validateRequiredParameter(array('category', 'page', 'item_count'), $request)) != '')
                return $response;

            $category = $request->category;
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


            $result = $this->getPixabayVideoForUser($category, $page, $per_page, $kt[$this->currentKey]);

            if ($result === 429) {
                $redis_keys = Redis::keys('pel:currentKey:*');
                count($redis_keys) > 0 ? $this->currentKey = substr($redis_keys[0], -1) : $this->currentKey = 0;
                $this->deleteRedisKey($redis_keys);
                $getKey = $this->increaseCurrentKey($this->currentKey);

                $currentKey = $getKey + 1;
                $template = 'simple';
                $subject = 'Pixabay rate limit exceeded.';
                $message_body = "Now, The current key is $currentKey.";
                $api_name = 'getVideosFromPixabay';
                $api_description = 'Get videos from pixabay for user.';
                $email_id = 'pooja.optimumbrew@gmail.com';
                $this->dispatch(new EmailJob(1, $email_id, $subject, $message_body, $template, $api_name, $api_description));

                return Response::json(array('code' => 201, 'message' => 'The server is unable to load videos. Please try again after 10-20 minutes.', 'cause' => '', 'data' => json_decode('{}')));
            }

            $response = Response::json(array('code' => 200, 'message' => 'videos fetched successfully.', 'cause' => '', 'data' => $result));;
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));
            /*return  $getKey = $this->increaseCurrentKey($this->currentKey);*/

        } catch (Exception $e) {
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get videos from pixabay.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            Log::error("getVideosFromPixabay :", ['Exception' => $e->getMessage(), "\nTraceAsString :" => $e->getTraceAsString()]);
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

            $result = isset($redis_keys)?$redis_keys:'{}';

            $response = Response::json(array('code' => 200, 'message' => 'Current key fetched successfully.', 'cause' => '', 'data' => $result));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get current key.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            Log::error("getCurrentKey :", ['Exception' => $e->getMessage(), "\nTraceAsString :" => $e->getTraceAsString()]);
            DB::rollBack();
        }
        return $response;
    }

    public function increaseCurrentKey($currentKey){
        try{
            $keys = Config::get('constant.PIXABAY_API_KEY');
            $key = explode(',', $keys);
            $i =0 ;
            foreach($key as $k){
                $kt[$i] = $k;
                $i++;
            }
            $countKey = $i-1;
            $this->currentKey=$currentKey;
            if($this->currentKey == $countKey){
                $this->currentKey = 0;
            }else{
                $this->currentKey = $this->currentKey + 1;
            }

            if (!Cache::has("pel:currentKey:$this->currentKey")) {
                $result = Cache::remember("currentKey:$this->currentKey",29, function () {
                    //Log::info('Current Key :'.$this->currentKey);
                    return $this->currentKey;
                });
            }
            $redis_result = Cache::get("currentKey:$this->currentKey");
            Redis::expire("currentKey:$this->currentKey", 1);
            $response = $redis_result;
        }catch (Exception $e) {
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'change API key of pixabay.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            Log::error("increaseCurrentKey :", ['Exception' => $e->getMessage(), "\nTraceAsString :" => $e->getTraceAsString()]);
            DB::rollBack();
        }
        return $response;
    }

    public function getPixabayImageForUser($category,$page,$per_page,$key){
        try{
            $this->category = $category;
            $this->page = $page;
            $this->per_page = $per_page;
            $this->is_cache = 1;

            $this->url = Config::get('constant.PIXABAY_API_URL').'?key='.$key.'&q='.$this->category.'&orientation=vertical&page='.$this->page.'&per_page='.$this->per_page;

            if (!Cache::has("pel:getPixabayImageForUser:$this->category:$this->page:$this->per_page")) {
                $result = Cache::remember("getPixabayImageForUser:$this->category:$this->page:$this->per_page", 1440, function () {

                    $this->is_cache = 0;
                    $client = new Client();
                    try{
                        $response = $client->request('get',$this->url);
                        $this->getRateRemaining = $response->getHeader('X-RateLimit-Remaining');
                        //Log::info('rate limit : ',['rate_limit' => $this->getRateRemaining]);

                        if($this->getRateRemaining <= 100){
                            $redis_keys = Redis::keys('pel:currentKey:*');
                            count($redis_keys) > 0 ? $this->currentKey = substr($redis_keys[0], -1) : $this->currentKey = 0;
                            $this->deleteRedisKey($redis_keys);
                            $getKey = $this->increaseCurrentKey($this->currentKey);

                            $currentKey = $getKey +1;
                            $template = 'simple';
                            $subject = 'Pixabay rate limit <= 100.';
                            $message_body = "100 request is remaining $getKey. Your currently used key is $currentKey.";
                            $api_name = 'getPixabayImageForUser';
                            $api_description = 'Get pixabay image for user.';
                            $email_id = 'pooja.optimumbrew@gmail.com';
                            $this->dispatch(new EmailJob(1, $email_id, $subject, $message_body, $template, $api_name, $api_description));

                        }
                        return $redis_result = json_decode($response->getBody()->getContents(), true);
                    }catch (Exception $e){
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
            }

            return  ['is_cache' => $is_cache, 'result' => $redis_result];


        } catch (Exception $e) {
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get images from pixabay.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            Log::error("getPixabayImageForUser :", ['Exception' => $e->getMessage(), "\nTraceAsString :" => $e->getTraceAsString()]);
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

            /*$is_cache = 0;
            $client = new Client();
            try {
                $response = $client->request('get', $this->url);
                $this->getRateRemaining = $response->getHeader('X-RateLimit-Remaining');
                if ($this->getRateRemaining <= 100) {
                    log::info('Rate Remaining :' . $this->getRateRemaining);
                    $redis_keys = Redis::keys('pel:currentKey:*');
                    count($redis_keys) > 0 ? $this->currentKey = substr($redis_keys[0], -1) : $this->currentKey = 0;
                    $this->deleteRedisKey($redis_keys);
                    $getKey = $this->increaseCurrentKey($this->currentKey);

                    $currentKey = $getKey + 1;
                    $template = 'simple';
                    $subject = 'Pixabay rate limit <= 100.';
                    $message_body = "100 request is remaining $getKey. Your currently used key is $currentKey.";
                    $api_name = 'getPixabayVideoForUser';
                    $api_description = 'Get videos from pixabay for user.';
                    $email_id = 'pooja.optimumbrew@gmail.com';
                    $this->dispatch(new EmailJob(1, $email_id, $subject, $message_body, $template, $api_name, $api_description));
                }
                $redis_result =  json_decode($response->getBody()->getContents());
            } catch (Exception $e) {
                $redis_result =  $e->getResponse()->getStatusCode();
            }*/


            if (!Cache::has("pel:getPixabayVideoForUser:$this->category:$this->page:$this->per_page")) {
                $result = Cache::remember("getPixabayVideoForUser:$this->category:$this->page:$this->per_page", 1440, function () {

                    $this->is_cache = 0;
                    $client = new Client();
                    try {
                        $response = $client->request('get', $this->url);
                        $this->getRateRemaining = $response->getHeader('X-RateLimit-Remaining');
                        if ($this->getRateRemaining <= 100) {
                            //Log::info('Rate Remaining :' . $this->getRateRemaining);
                            $redis_keys = Redis::keys('pel:currentKey:*');
                            count($redis_keys) > 0 ? $this->currentKey = substr($redis_keys[0], -1) : $this->currentKey = 0;
                            $this->deleteRedisKey($redis_keys);
                            $getKey = $this->increaseCurrentKey($this->currentKey);

                            $currentKey = $getKey + 1;
                            $template = 'simple';
                            $subject = 'Pixabay rate limit <= 100.';
                            $message_body = "100 request is remaining $getKey. Your currently used key is $currentKey.";
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

            return ['is_cache' => $is_cache, 'result' => $redis_result];

        } catch (Exception $e) {
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get videos from pixabay.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            Log::error("getPixabayVideoForUser :", ['Exception' => $e->getMessage(), "\nTraceAsString :" => $e->getTraceAsString()]);
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
            Log::error("deleteRedisKey Error :", ['Error : ' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'Delete Redis Keys.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

}

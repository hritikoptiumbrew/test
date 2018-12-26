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

class UnsplashController extends Controller
{
    /* =====================================| Fetch Images |==============================================*/

    /**
     * @api {post} getImageByUnsplash getImageByUnsplash
     * @apiName getImageByUnsplash
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
     * "search_query":"car"
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Images fetched successfully.",
     * "cause": "",
     * "data": {
     * "total": 96294,
     * "total_pages": 3210,
     * "results": [
     * {
     * "id": "8qEuawM_txg",
     * "created_at": "2018-10-22T06:00:30-04:00",
     * "updated_at": "2018-12-23T11:00:34-05:00",
     * "width": 3000,
     * "height": 4000,
     * "color": "#061A1A",
     * "description": null,
     * "urls": {
     * "raw": "https://images.unsplash.com/photo-1540202403-b7abd6747a18?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjQ3NDQ5fQ",
     * "full": "https://images.unsplash.com/photo-1540202403-b7abd6747a18?ixlib=rb-1.2.1&q=85&fm=jpg&crop=entropy&cs=srgb&ixid=eyJhcHBfaWQiOjQ3NDQ5fQ",
     * "regular": "https://images.unsplash.com/photo-1540202403-b7abd6747a18?ixlib=rb-1.2.1&q=80&fm=jpg&crop=entropy&cs=tinysrgb&w=1080&fit=max&ixid=eyJhcHBfaWQiOjQ3NDQ5fQ",
     * "small": "https://images.unsplash.com/photo-1540202403-b7abd6747a18?ixlib=rb-1.2.1&q=80&fm=jpg&crop=entropy&cs=tinysrgb&w=400&fit=max&ixid=eyJhcHBfaWQiOjQ3NDQ5fQ",
     * "thumb": "https://images.unsplash.com/photo-1540202403-b7abd6747a18?ixlib=rb-1.2.1&q=80&fm=jpg&crop=entropy&cs=tinysrgb&w=200&fit=max&ixid=eyJhcHBfaWQiOjQ3NDQ5fQ"
     * },
     * "links": {
     * "self": "https://api.unsplash.com/photos/8qEuawM_txg",
     * "html": "https://unsplash.com/photos/8qEuawM_txg",
     * "download": "https://unsplash.com/photos/8qEuawM_txg/download",
     * "download_location": "https://api.unsplash.com/photos/8qEuawM_txg/download"
     * },
     * "categories": [],
     * "sponsored": true,
     * "sponsored_by": {
     * "id": "MEbW0Mv5SHk",
     * "updated_at": "2018-10-22T14:47:15-04:00",
     * "username": "maldives",
     * "name": "Maldives Tourism",
     * "first_name": "Maldives Tourism",
     * "last_name": null,
     * "twitter_username": null,
     * "portfolio_url": null,
     * "bio": "The sunny side of life.",
     * "location": "Malé, Maldives",
     * "links": {
     * "self": "https://api.unsplash.com/users/maldives",
     * "html": "https://unsplash.com/@maldives",
     * "photos": "https://api.unsplash.com/users/maldives/photos",
     * "likes": "https://api.unsplash.com/users/maldives/likes",
     * "portfolio": "https://api.unsplash.com/users/maldives/portfolio",
     * "following": "https://api.unsplash.com/users/maldives/following",
     * "followers": "https://api.unsplash.com/users/maldives/followers"
     * },
     * "profile_image": {
     * "small": "https://images.unsplash.com/profile-1540233904172-590b0facb2d0?ixlib=rb-1.2.1&q=80&fm=jpg&crop=faces&cs=tinysrgb&fit=crop&h=32&w=32",
     * "medium": "https://images.unsplash.com/profile-1540233904172-590b0facb2d0?ixlib=rb-1.2.1&q=80&fm=jpg&crop=faces&cs=tinysrgb&fit=crop&h=64&w=64",
     * "large": "https://images.unsplash.com/profile-1540233904172-590b0facb2d0?ixlib=rb-1.2.1&q=80&fm=jpg&crop=faces&cs=tinysrgb&fit=crop&h=128&w=128"
     * },
     * "instagram_username": null,
     * "total_collections": 0,
     * "total_likes": 0,
     * "total_photos": 0,
     * "accepted_tos": false
     * },
     * "sponsored_impressions_id": "3282145",
     * "likes": 501,
     * "liked_by_user": false,
     * "current_user_collections": [],
     * "slug": null,
     * "user": {
     * "id": "cYNNst8ZosY",
     * "updated_at": "2018-12-24T17:54:48-05:00",
     * "username": "seefromthesky",
     * "name": "Ishan @seefromthesky",
     * "first_name": "Ishan",
     * "last_name": "@seefromthesky",
     * "twitter_username": "SeefromtheSky",
     * "portfolio_url": "http://www.instagram.com/seefromthesky",
     * "bio": "??? ?????? ????? ?????? ????????? ??????? ??????\r\n ••• \r\nPeace and love. ?? #seefromthesky\r\n? ishan@seefromthesky.com\r\n",
     * "location": "maldives",
     * "links": {
     * "self": "https://api.unsplash.com/users/seefromthesky",
     * "html": "https://unsplash.com/@seefromthesky",
     * "photos": "https://api.unsplash.com/users/seefromthesky/photos",
     * "likes": "https://api.unsplash.com/users/seefromthesky/likes",
     * "portfolio": "https://api.unsplash.com/users/seefromthesky/portfolio",
     * "following": "https://api.unsplash.com/users/seefromthesky/following",
     * "followers": "https://api.unsplash.com/users/seefromthesky/followers"
     * },
     * "profile_image": {
     * "small": "https://images.unsplash.com/profile-1470411901970-0f48a5d5e958?ixlib=rb-1.2.1&q=80&fm=jpg&crop=faces&cs=tinysrgb&fit=crop&h=32&w=32",
     * "medium": "https://images.unsplash.com/profile-1470411901970-0f48a5d5e958?ixlib=rb-1.2.1&q=80&fm=jpg&crop=faces&cs=tinysrgb&fit=crop&h=64&w=64",
     * "large": "https://images.unsplash.com/profile-1470411901970-0f48a5d5e958?ixlib=rb-1.2.1&q=80&fm=jpg&crop=faces&cs=tinysrgb&fit=crop&h=128&w=128"
     * },
     * "instagram_username": "seefromthesky",
     * "total_collections": 0,
     * "total_likes": 64,
     * "total_photos": 91,
     * "accepted_tos": false
     * },
     * "tags": [
     * {
     * "title": "underwater"
     * },
     * {
     * "title": "reef"
     * },
     * {
     * "title": "coral"
     * },
     * {
     * "title": "water"
     * },
     * {
     * "title": "maldives"
     * }
     * ],
     * "photo_tags": [
     * {
     * "title": "underwater"
     * },
     * {
     * "title": "reef"
     * },
     * {
     * "title": "coral"
     * },
     * {
     * "title": "water"
     * },
     * {
     * "title": "maldives"
     * }
     * ]
     * }
     * ],
     * "is_next_page": true,
     * "is_cache": 0
     * }
     * }
     */
    /**
     *
     * - Users ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/getImageByUnsplash",
     *        tags={"Users"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="getImageByUnsplash",
     *        summary="Get images from Unsplash",
     *        produces={"application/json"},
     *      @SWG\Parameter(
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
     *        ),
     * 		@SWG\Response(
     *            response=201,
     *            description="error",
     *        ),
     *    )
     *
     */
    public function getImageByUnsplash(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $keys = Config::get('constant.UNSPLASH_API_KEY');
            $request = json_decode($request_body->getContent());

            if (($response = (new VerificationController())->validateRequiredParameter(array('page'), $request)) != '')
                return $response;

            $search_query = isset($request->search_query) ? $request->search_query : "";
            $page = $request->page;
            $per_page = Config::get('constant.UNSPLASH_ITEM_COUNT');

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

            $redis_keys = Redis::keys('pel:currentKeyOfUnsplash:*');

            count($redis_keys) > 0 ? $this->currentKey = substr($redis_keys[0], -1) : $this->currentKey = 0;
            //log::info($kt[$this->currentKey]);

            $result = $this->getUnsplashImageForUser($search_query, $page, $per_page, $kt[$this->currentKey]);

            if ($result === 403) {
                $redis_keys = Redis::keys('pel:currentKeyOfUnsplash:*');
                count($redis_keys) > 0 ? $this->currentKey = substr($redis_keys[0], -1) : $this->currentKey = 0;
                foreach ($redis_keys as $key) {
                    $this->deleteRedisKey($key);
                }

                $getKey = $this->increaseCurrentKeyOfUnsplash($this->currentKey);

                $currentKey = $getKey + 1;
                $template = 'stock_photos';
                $subject = 'Unsplash rate limit exceeded.';
                //$message_body = "Now, The current key is $currentKey.";
                $message_body = array(
                    'message' => "The Rate limit for unsplash is exceeded now.<br> The updated key is $currentKey.",
                    'user_name' => 'Admin'
                );
                $api_name = 'getImageByUnsplash';
                $api_description = 'Get images from unsplash for user.';
                $email_id = 'pooja.optimumbrew@gmail.com';
                $this->dispatch(new EmailJob(1, $email_id, $subject, $message_body, $template, $api_name, $api_description));

                return Response::json(array('code' => 201, 'message' => 'The server is unable to load images. Please try again after 10-20 minutes.', 'cause' => '', 'data' => json_decode('{}')));

            }

            $response = Response::json(array('code' => 200, 'message' => 'Images fetched successfully.', 'cause' => '', 'data' => $result));;
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));
            /*return  $getKey = $this->increaseCurrentKeyOfUnsplash($this->currentKey);*/

        } catch (Exception $e) {
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get images from unsplash.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            Log::error("getImageByUnsplash :", ['Exception' => $e->getMessage(), "\nTraceAsString :" => $e->getTraceAsString()]);
            DB::rollBack();
        }
        return $response;
    }

    /* =====================================| Function |==============================================*/

    public function currentKeyOfUnsplash()
    {
        try {

            $redis_keys = Redis::keys('pel:currentKeyOfUnsplash*');
            $key = explode(',', $redis_keys);

            $result = isset($redis_keys) ? $redis_keys : '{}';

            $response = Response::json(array('code' => 200, 'message' => 'Current key fetched successfully.', 'cause' => '', 'data' => $result));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get current key.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            Log::error("currentKeyOfUnsplash :", ['Exception' => $e->getMessage(), "\nTraceAsString :" => $e->getTraceAsString()]);
            DB::rollBack();
        }
        return $response;
    }

    public function increaseCurrentKeyOfUnsplash($currentKey)
    {
        try {
            $keys = Config::get('constant.UNSPLASH_API_KEY');
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

            if (!Cache::has("pel:currentKeyOfUnsplash:$this->currentKey")) {
                $result = Cache::rememberforever("currentKeyOfUnsplash:$this->currentKey", function () {
                    return $this->currentKey;
                });
            }
            $redis_result = Cache::get("currentKeyOfUnsplash:$this->currentKey");
            Redis::expire("currentKeyOfUnsplash:$this->currentKey", 1);
            $response = $redis_result;
        } catch (Exception $e) {
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'change API key of unsplash.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            Log::error("increaseCurrentKeyOfUnsplash :", ['Exception' => $e->getMessage(), "\nTraceAsString :" => $e->getTraceAsString()]);
            DB::rollBack();
        }
        return $response;
    }

    public function getUnsplashImageForUser($category, $page, $per_page, $key)
    {
        try {
            $this->category = $category;
            $this->page = $page;
            $this->per_page = $per_page;
            $this->is_cache = 1;

            $this->url = Config::get('constant.UNSPLASH_API_URL') . '?client_id=' . $key . '&query=' . $this->category . '&page=' . $this->page . '&per_page=' . $this->per_page;

            //Log::info('url : ',['url' => $this->url]);

            if (!Cache::has("pel:getUnsplashImageForUser:$this->category:$this->page:$this->per_page")) {
                $result = Cache::remember("getUnsplashImageForUser:$this->category:$this->page:$this->per_page", 1440, function () {

                    $this->is_cache = 0;
                    $client = new Client();
                    try {
                        $response = $client->request('get', $this->url);
                        $this->getRateRemaining = $response->getHeader('X-RateLimit-Remaining');
                        //Log::info('rate limit : ',['rate_limit' => $this->getRateRemaining]);
                        if (count($this->getRateRemaining) > 0) {
                            $rate_remaining = intval($this->getRateRemaining[0]);
                        } else {
                            $rate_remaining = 0;
                        }

                        if ($rate_remaining <= 48) {

                            $redis_keys = Redis::keys('pel:currentKeyOfUnsplash:*');
                            count($redis_keys) > 0 ? $this->currentKey = substr($redis_keys[0], -1) : $this->currentKey = 0;

                            foreach ($redis_keys as $key) {
                                $this->deleteRedisKey($key);
                            }

                            //return $this->currentKey;

                            $getKey = $this->increaseCurrentKeyOfUnsplash($this->currentKey);

                            $currentKey = $getKey + 1;
                            $template = 'stock_photos';
                            $subject = 'Unsplash rate limit <= 100.';
                            //$message_body = "100 request is remaining $getKey. Your currently used key is $currentKey.";
                            $message_body = array(
                                'message' => "100 request is remaining $getKey.<br>Your currently used key is $currentKey.",
                                'user_name' => 'Admin'
                            );
                            $api_name = 'getUnsplashImageForUser';
                            $api_description = 'Get Unsplash image for user.';
                            $email_id = 'pooja.optimumbrew@gmail.com';
                            $this->dispatch(new EmailJob(1, $email_id, $subject, $message_body, $template, $api_name, $api_description));

                        }
                        return $redis_result = json_decode($response->getBody()->getContents(), true);
                    } catch (Exception $e) {
                        return $redis_result = $e->getResponse()->getStatusCode();
                    }
                });
            }

            $this->is_cache == 0 ? $is_cache = 0 : $is_cache = 1;

            $redis_result = Cache::get("getUnsplashImageForUser:$this->category:$this->page:$this->per_page");

            Redis::expire("getUnsplashImageForUser:$this->category:$this->page:$this->per_page", 1);


            if ($redis_result == 403) {

                $this->deleteRedisKey("pel:getUnsplashImageForUser:$this->category:$this->page:$this->per_page");
                return 403;
                //$response = Response::json(array('code' => 201, 'message' => 'The server is unable to load images. Please try again after 10-20 minutes.', 'cause' => '', 'data' => json_decode('{}')));
            }

            if (!$redis_result) {
                $redis_result = [];
            }

            $total_pages = $redis_result['total_pages'];
            $is_next_page = $total_pages <= $page ? false : true;
            $redis_result['is_next_page'] = $is_next_page;
            $redis_result['is_cache'] = $this->is_cache;

            return $redis_result;

        } catch (Exception $e) {
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get images from unsplash.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            Log::error("getUnsplashImageForUser :", ['Exception' => $e->getMessage(), "\nTraceAsString :" => $e->getTraceAsString()]);
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
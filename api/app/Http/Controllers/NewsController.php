<?php

namespace App\Http\Controllers;

use Abraham\TwitterOAuth\TwitterOAuth;
use App\Permission;
use App\Role;
use Illuminate\Support\Facades\Mail;
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
use GuzzleHttp\Client;

class NewsController extends Controller
{
    /**
     * @api {post} getFeedFromTwitter getFeedFromTwitter
     * @apiName getFeedFromTwitter
     * @apiGroup Resume User
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "page":1 //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Twitter post fatched successfully.",
     * "cause": "",
     * "data": {
     * "total_record": 1198,
     * "total_pages": 60,
     * "is_next_page": true,
     * "result": [
     * {
     * "id": 1077628187000094700,
     * "created_at": "2018-12-25 18:12:35",
     * "text": "Tech startups, ecommerce companies to step up hiring in 2019 #Jobs <a href=\"https://t.co/nJQY9q3krl\" target=\"_blank\">https://t.co/nJQY9q3krl</a>",
     * "favorite_count": 0,
     * "profile_image_url": "http://pbs.twimg.com/profile_images/795539118172160001/IbZPUHK9_400x400.jpg",
     * "account_url": "https://twitter.com/ETJobNews",
     * "media_url_https": "",
     * "post_type": 1,
     * "video_url": ""
     * },
     * {
     * "id": 1077625329257201700,
     * "created_at": "2018-12-25 18:01:14",
     * "text": "Are you a talented Housekeeper in #Winchester? We want you on our team! #jobs <a href=\"https://t.co/TVZ2u7wjYi\" target=\"_blank\">https://t.co/TVZ2u7wjYi</a> <a href=\"https://t.co/2f2DEskXmy\" target=\"_blank\">https://t.co/2f2DEskXmy</a>",
     * "favorite_count": 1,
     * "profile_image_url": "http://pbs.twimg.com/profile_images/1056994307658432512/aGmzhHz4_400x400.jpg",
     * "account_url": "https://twitter.com/MonsterJobs",
     * "media_url_https": "https://pbs.twimg.com/media/DvR-hoaWsAErDTT.jpg",
     * "post_type": 2,
     * "video_url": ""
     * }
     * ],
     * "is_cache": 1
     * }
     * }
     */
    public function getFeedFromTwitter(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());

            $response = (new VerificationController())->validateRequiredParameter(array('page'), $request);
            if ($response != '') {
                return $response;
            }
            $this->page = $request->page;
            $this->is_cache = 1;

            if (!Cache::has("pel:getFeedFromTwitter:$this->page")) {
                $result = Cache::remember("getFeedFromTwitter:$this->page", 1440, function () {
                    /*
                    * Twitter App Settings
                    * Set access tokens and API keys
                    */
                    $this->is_cache = 0;
                    $consumerKey = Config::get('constant.twitter_consumer_Key');
                    $consumerSecret = Config::get('constant.twitter_consumer_Secret');
                    $accessToken = Config::get('constant.twitter_access_Token');
                    $accessTokenSecret = Config::get('constant.twitter_access_Token_Secret');
                    $post_list = [];
                    $twitter_list = Config::get('constant.TWITTER_USER_LIST_FOR_TWITTER_TIMELINE');
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
                        $tweetNum = Config::get('constant.TWITTER_POST_ITEM_COUNT_FOR_TWITTER_TIMELINE');

                        // Authenticate with twitter
                        $twitterConnection = new TwitterOAuth(
                            $consumerKey,
                            $consumerSecret,
                            $accessToken,
                            $accessTokenSecret
                        );

                        // Get the user timeline feeds
                        $list = $this->twitterPostByTwitterId($twitterConnection, $twitterID, $tweetNum);
                        $post_list = array_merge($post_list, $list);

                    }

                    usort($post_list, function ($a, $b) { //Sort the array using a user defined function
                        return strtotime($a['created_at']) > strtotime($b['created_at']) ? -1 : 1;
                    });

                    $item_count = Config::get('constant.TWITTER_TIMELINE_ITEM_COUNT_FOR_PAGINATION');
                    $offset = ($this->page - 1) * $item_count;

                    $total_post_list = count($post_list);
                    $this->post_list = array_splice($post_list, $offset, $item_count);
                    $total_pages = ceil($total_post_list / $item_count);
                    $is_next_page = ($total_post_list > ($offset + $item_count)) ? true : false;

                    return ['total_record' => $total_post_list, 'total_pages' => $total_pages, 'is_next_page' => $is_next_page, 'result' => $this->post_list];
                });
            }


            $redis_result = Cache::get("getFeedFromTwitter:$this->page");

            Redis::expire("getFeedFromTwitter", 1);

            if (!$redis_result['result']) {
                return Response::json(array('code' => 427, 'message' => "Sorry, We couldn't find any twitter post.", 'cause' => '', 'data' => json_decode('{}')));
            }

            $this->is_cache == 0 ? $is_cache = 0 : $is_cache = 1;
            $redis_result['is_cache'] = $is_cache;

            $response = Response::json(array('code' => 200, 'message' => 'Twitter post fatched successfully.', 'cause' => '', 'data' => $redis_result));

        } catch (Exception $e) {
            Log::error("getFeedFromTwitter : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get page of twitter.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    public function twitterPostByTwitterId($twitterConnection, $twitterID, $tweetNum)
    {
        try {
            $feedData = $twitterConnection->get(
                'statuses/user_timeline',
                array(
                    'screen_name' => $twitterID,
                    'count' => $tweetNum,
                    'exclude_replies' => false,
                    'include_rts' => 1,
                )
            );

            $r = [];
            $c = 0;

            foreach ($feedData as $result) {
                $latestTweet = $result->text;
                $latestTweet = preg_replace('/https:\/\/([a-z0-9_\.\-\+\&\!\#\~\/\,]+)/i', '<a href="https://$1" target="_blank">https://$1</a>', $latestTweet);
                $latestTweet = preg_replace('/@([a-z0-9_]+)/i', '<a class="tweet-author" href="https://twitter.com/$1" target="_blank">@$1</a>', $latestTweet);

                /* Only get post and video
                 * if (isset($result->entities->media)) {
                    $r[$c]['is_post'] = 1;
                    $r[$c]['id'] = $result->id;
                    $r[$c]['created_at'] = $result->created_at;
                    $r[$c]['text'] = $result->text;
                    $r[$c]['media_url_https'] = $result->entities->media[0]->media_url_https;
                    $r[$c]['is_video_post'] = isset($result->extended_entities->media[0]->video_info) ? 1 : 0;
                    if (isset($result->extended_entities->media[0]->video_info)) {
                        $video_detail = $result->extended_entities->media[0]->video_info->variants;
                        $i = 0;
                        foreach ($video_detail as $video) {
                            if ($video->content_type == 'video/mp4') {
                                $r[$c]['video_url'] = $video->url;
                                break;
                            }
                            $i = $i + 1;
                        }
                    } else {
                        $r[$c]['video_url'] = '';
                    }
//                    $r[$c]['video_content'] = isset($result->extended_entities->media[0]->video_info) ? $result->extended_entities->media[0]->video_info->variants : '';

                    $c = $c + 1;
                }*/
                if (isset($result->entities->media)) {
                    $r[$c]['id'] = $result->id;
                    $r[$c]['created_at'] = date("Y-m-d H:i:s", strtotime($result->created_at));
                    $r[$c]['text'] = $latestTweet;//$result->text;
                    $r[$c]['favorite_count'] = $result->favorite_count;
                    //$r[$c]['profile_image_url'] = $result->user->profile_image_url;
                    $r[$c]['profile_image_url'] = str_replace("normal", "400x400", $result->user->profile_image_url);
                    $r[$c]['account_url'] = "https://twitter.com/" . $result->user->screen_name;
                    $r[$c]['media_url_https'] = $result->entities->media[0]->media_url_https;
                    $r[$c]['post_type'] = isset($result->extended_entities->media[0]->video_info) ? 3 : 2;
                    if (isset($result->extended_entities->media[0]->video_info)) {
                        $video_detail = $result->extended_entities->media[0]->video_info->variants;
                        $i = 0;
                        foreach ($video_detail as $video) {
                            if ($video->content_type == 'video/mp4') {
                                $r[$c]['video_url'] = $video->url;
                                break;
                            }
                            $i = $i + 1;
                        }
                    } else {
                        $r[$c]['video_url'] = '';
                    }

//                    $r[$c]['video_content'] = isset($result->extended_entities->media[0]->video_info) ? $result->extended_entities->media[0]->video_info->variants : '';

                    $c = $c + 1;
                } else {
                    $r[$c]['id'] = $result->id;
                    $r[$c]['created_at'] = date("Y-m-d H:i:s", strtotime($result->created_at));
                    $r[$c]['text'] = $latestTweet;//$result->text;
                    $r[$c]['favorite_count'] = $result->favorite_count;
                    //$r[$c]['profile_image_url'] = $result->user->profile_image_url;
                    $r[$c]['profile_image_url'] = str_replace("normal", "400x400", $result->user->profile_image_url);
                    $r[$c]['account_url'] = "https://twitter.com/" . $result->user->screen_name;
                    $r[$c]['media_url_https'] = '';
                    $r[$c]['post_type'] = 1;
                    $r[$c]['video_url'] = '';
                    $c = $c + 1;
                }
            }
//            log::info('feedData', ['feedData' => $r]);
            return $r;

        } catch (Exception $e) {
            Log::error("twitterPostByTwitterId : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get post of twitter.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;

    }

    /*=============================================================================*/

    public function getFeedFromTwitterByRequest(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());

            $response = (new VerificationController())->validateRequiredArrayParameter(array('twitter_list'), $request);
            if ($response != '') {
                return $response;
            }

            $this->twitter_list = $request->twitter_list;
            /*
            * Twitter App Settings
            * Set access tokens and API keys
            */
            $consumerKey = "nFkqK8lBcfHsPQSZgkWh6Ky4T";
            $consumerSecret = "Koh4KvkkXlYrzVE61q7yanZrUeuXvCFovObD0k53RF0NYMGksK";
            $accessToken = "4746441684-gfXiRVLR9q7IOTz6Us03PyH621lu7L9p2mtF0SU";
            $accessTokenSecret = "lHh4WBilZYzdW7b2amVBEguoLA1JaiQP2qtUAOyTopp4h";
            $this->post_list = [];
            foreach ($this->twitter_list as $twitter) {
                $response = (new VerificationController())->validateRequiredParameter(array('twitterID', 'tweetNum'), $twitter);
                if ($response != '') {
                    return $response;
                }

                // Twitter account username
                $twitterID = $twitter->twitterID;

                // Number of tweets
                $tweetNum = $twitter->tweetNum;

                // Authenticate with twitter
                $twitterConnection = new TwitterOAuth(
                    $consumerKey,
                    $consumerSecret,
                    $accessToken,
                    $accessTokenSecret
                );
                // Get the user timeline feeds

                $list = $this->twitterPostByTwitterId($twitterConnection, $twitterID, $tweetNum);

                $this->post_list = array_merge($this->post_list, $list);

            }

            usort($this->post_list, function ($a, $b) { //Sort the array using a user defined function
                return strtotime($a['created_at']) > strtotime($b['created_at']) ? -1 : 1;
            });
            $redis_result = $this->post_list;
            return $response = Response::json(array('code' => 200, 'message' => 'Twitter post fatched successfully.', 'cause' => '', 'data' => ['result' => $redis_result]));

            if (!Cache::has("pel:getFeedFromTwitterByRequest")) {
                $result = Cache::remember("getFeedFromTwitterByRequest", 1440, function () {
                    return $this->post_list;

                });
            }
            $redis_result = Cache::get("getFeedFromTwitterByRequest");

            Redis::expire("getFeedFromTwitter", 1);

            if (!$redis_result) {
                $redis_result = [];
            }
            $response = Response::json(array('code' => 200, 'message' => 'Twitter post fatched successfully.', 'cause' => '', 'data' => ['result' => $redis_result]));

        } catch (Exception $e) {
            Log::error("getFeedFromTwitter : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get page of twitter.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    public function getFeedFromTwitter_test(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());

            $response = (new VerificationController())->validateRequiredParameter(array('twitterID', 'tweetNum'), $request);
            if ($response != '') {
                return $response;
            }
            $twitterID = $request->twitterID;

            // Number of tweets
            $tweetNum = $request->tweetNum;

            /*
            * Twitter App Settings
            * Set access tokens and API keys
            */
            $consumerKey = "nFkqK8lBcfHsPQSZgkWh6Ky4T";
            $consumerSecret = "Koh4KvkkXlYrzVE61q7yanZrUeuXvCFovObD0k53RF0NYMGksK";
            $accessToken = "4746441684-gfXiRVLR9q7IOTz6Us03PyH621lu7L9p2mtF0SU";
            $accessTokenSecret = "lHh4WBilZYzdW7b2amVBEguoLA1JaiQP2qtUAOyTopp4h";
            $post_list = [];

            $twitterConnection = new TwitterOAuth(
                $consumerKey,
                $consumerSecret,
                $accessToken,
                $accessTokenSecret
            );
            // Get the user timeline feeds

            $feedData = $twitterConnection->get(
                'statuses/user_timeline',
                array(
                    'screen_name' => $twitterID,
                    'count' => $tweetNum,
                    'exclude_replies' => false,
                    'include_rts' => 1,

                )
            );


            $r = [];
            $c = 0;

            foreach ($feedData as $result) {

                $latestTweet = $result->text;
                $latestTweet = preg_replace('/https:\/\/([a-z0-9_\.\-\+\&\!\#\~\/\,]+)/i', '<a href="https://$1" target="_blank">https://$1</a>', $latestTweet);
                $latestTweet = preg_replace('/@([a-z0-9_]+)/i', '<a class="tweet-author" href="https://twitter.com/$1" target="_blank">@$1</a>', $latestTweet);

                if (isset($result->entities->media)) {
                    $r[$c]['id'] = $result->id;
                    $r[$c]['created_at'] = date("Y-m-d H:i:s", strtotime($result->created_at));
                    $r[$c]['text'] = $latestTweet;//$result->text;
                    $r[$c]['favorite_count'] = $result->favorite_count;
                    //$r[$c]['profile_image_url'] = $result->user->profile_image_url;
                    $r[$c]['profile_image_url'] = str_replace("normal", "400x400", $result->user->profile_image_url);
                    $r[$c]['account_url'] = "https://twitter.com/" . $result->user->screen_name;
                    $r[$c]['media_url_https'] = $result->entities->media[0]->media_url_https;
                    $r[$c]['post_type'] = isset($result->extended_entities->media[0]->video_info) ? 3 : 2;
                    if (isset($result->extended_entities->media[0]->video_info)) {
                        $video_detail = $result->extended_entities->media[0]->video_info->variants;
                        $i = 0;
                        foreach ($video_detail as $video) {
                            if ($video->content_type == 'video/mp4') {
                                $r[$c]['video_url'] = $video->url;
                                break;
                            }
                            $i = $i + 1;
                        }
                    } else {
                        $r[$c]['video_url'] = '';
                    }

//                    $r[$c]['video_content'] = isset($result->extended_entities->media[0]->video_info) ? $result->extended_entities->media[0]->video_info->variants : '';

                    $c = $c + 1;
                } else {
                    $r[$c]['id'] = $result->id;
                    $r[$c]['created_at'] = date("Y-m-d H:i:s", strtotime($result->created_at));
                    $r[$c]['text'] = $latestTweet;//$result->text;
                    $r[$c]['favorite_count'] = $result->favorite_count;
                    //$r[$c]['profile_image_url'] = $result->user->profile_image_url;
                    $r[$c]['profile_image_url'] = str_replace("normal", "400x400", $result->user->profile_image_url);
                    $r[$c]['account_url'] = "https://twitter.com/" . $result->user->screen_name;
                    $r[$c]['media_url_https'] = '';
                    $r[$c]['post_type'] = 1;
                    $r[$c]['video_url'] = '';
                    $c = $c + 1;
                }
            }
//            log::info('feedData', ['feedData' => $r]);
            return $r;

            $list = $this->twitterPostByTwitterId($twitterConnection, $twitterID, $tweetNum);

            $post_list = array_merge($post_list, $list);


            return $post_list;
        } catch (Exception $e) {
            Log::error("getFeedFromTwitter : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get page of twitter.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }

        return $response;
    }

}
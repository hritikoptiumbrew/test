<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Response;
use Config;
use DB;
use Log;
use File;
use Cache;
use GuzzleHttp\Client;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class VideoController extends Controller
{
    /**
     * @api {post} addYouTubeVideoURL addYouTubeVideoURL
     * @apiName addYouTubeVideoURL
     * @apiGroup Resume Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "url":"https://www.youtube.com/watch?v=E78k_XDjFLA" //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Video url added successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function addYouTubeVideoURL(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());

            if (($response = (new VerificationController())->validateRequiredParameter(array('url'), $request)) != '')
                return $response;

            $video_url = $request->url;
            $create_at = date('Y-m-d H:i:s');
            $API_key = 'AIzaSyB_MvXChn1z1PG_WihzIJ-s-iJQcsGH9KM';

            if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $video_url, $match)) {
                $video_id = $match[1];

                $existed = DB::select('SELECT youtube_video_id FROM youtube_video_master WHERE youtube_video_id=?', [$video_id]);
                if (count($existed) > 0) {
                    return Response::json(array('code' => 201, 'message' => 'Video already exist.', 'cause' => '', 'data' => json_decode('{}')));
                }

                $url = "https://www.googleapis.com/youtube/v3/videos/?part=snippet%2CcontentDetails%2Cstatistics&id=" . $video_id . "&key=" . $API_key;

                $client = new Client();
                $client_result = $client->request('get', $url);

                $jd_client_result = json_decode($client_result->getBody()->getContents(), true);

                $youtube_video_id = $jd_client_result['items'][0]['id'];
                $publishedAt = isset($jd_client_result['items'][0]['snippet']['publishedAt']) ? date("Y-m-d H:i:s", strtotime($jd_client_result['items'][0]['snippet']['publishedAt'])) : '';
                $title = $jd_client_result['items'][0]['snippet']['title'];
                $channelTitle = $jd_client_result['items'][0]['snippet']['channelTitle'];
                $thumbnail_url = $jd_client_result['items'][0]['snippet']['thumbnails']['high']['url'];
                $thumbnail_width = $jd_client_result['items'][0]['snippet']['thumbnails']['high']['width'];
                $thumbnail_height = $jd_client_result['items'][0]['snippet']['thumbnails']['high']['height'];

                DB::beginTransaction();

                DB::insert('insert into youtube_video_master (youtube_video_id,title,channel_name,url,thumbnail_url,thumbnail_width,thumbnail_height,published_at,create_time)
                            VALUES(?,?,?,?,?,?,?,?,?)',
                    [$youtube_video_id,
                        $title,
                        $channelTitle,
                        $video_url,
                        $thumbnail_url,
                        $thumbnail_width,
                        $thumbnail_height,
                        $publishedAt,
                        $create_at]);

                DB::commit();
                $response = Response::json(array('code' => 200, 'message' => 'Video url added successfully.', 'cause' => '', 'data' => json_decode('{}')));

            } else {
                $response = Response::json(array('code' => 427, 'message' => "Sorry, We couldn't find video.", 'cause' => '', 'data' => json_decode('{}')));

            }

        } catch (Exception $e) {
            Log::error("addYouTubeVideoURL Error :", ['Error : ' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'add youtube video.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    /**
     * @api {post} updateYouTubeVideoURL updateYouTubeVideoURL
     * @apiName updateYouTubeVideoURL
     * @apiGroup Resume Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "video_id":1, //compulsory
     * "title":"How to Interview for a Job in American English, part 1/5 Test",
     * "url":"https://www.youtube.com/watch?v=yBtMwyQFXwA test"
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Video updated successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function updateYouTubeVideoURL(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());

            if (($response = (new VerificationController())->validateRequiredParameter(array('video_id'), $request)) != '')
                return $response;

            $id = $request->video_id;
            $title = isset($request->title) ? $request->title : '';
            $video_url = isset($request->url) ? $request->url : '';
            $API_key = 'AIzaSyB_MvXChn1z1PG_WihzIJ-s-iJQcsGH9KM';

            if ($video_url) {

                if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $video_url, $match)) {
                    $video_id = $match[1];

                    $existed = DB::select('SELECT youtube_video_id FROM youtube_video_master WHERE youtube_video_id=? AND id != ?', [$video_id, $id]);
                    if (count($existed) > 0) {
                        return Response::json(array('code' => 201, 'message' => '', 'cause' => 'Video already exist.', 'data' => json_decode('{}')));
                    }

                    $url = "https://www.googleapis.com/youtube/v3/videos/?part=snippet%2CcontentDetails%2Cstatistics&id=" . $video_id . "&key=" . $API_key;

                    $client = new Client();
                    $client_result = $client->request('get', $url);

                    $jd_client_result = json_decode($client_result->getBody()->getContents(), true);

                    $youtube_video_id = $jd_client_result['items'][0]['id'];
                    $publishedAt = isset($jd_client_result['items'][0]['snippet']['publishedAt']) ? date("Y-m-d H:i:s", strtotime($jd_client_result['items'][0]['snippet']['publishedAt'])) : '';
                    $Video_title = $jd_client_result['items'][0]['snippet']['title'];
                    $channelTitle = $jd_client_result['items'][0]['snippet']['channelTitle'];
                    $thumbnail_url = $jd_client_result['items'][0]['snippet']['thumbnails']['high']['url'];
                    $thumbnail_width = $jd_client_result['items'][0]['snippet']['thumbnails']['high']['width'];
                    $thumbnail_height = $jd_client_result['items'][0]['snippet']['thumbnails']['high']['height'];
                    DB::beginTransaction();

                    DB::update('UPDATE youtube_video_master
                                SET youtube_video_id = ?,
                                    title = ?,
                                    channel_name = ?,
                                    url = ?,
                                    thumbnail_url = ?,
                                    thumbnail_width = ?,
                                    thumbnail_height = ?,
                                    published_at = ?
                                    WHERE id = ?
                                ', [
                        $youtube_video_id,
                        $Video_title,
                        $channelTitle,
                        $video_url,
                        $thumbnail_url,
                        $thumbnail_width,
                        $thumbnail_height,
                        $publishedAt,
                        $id
                    ]);
                    DB::commit();

                } else {
                    return Response::json(array('code' => 427, 'message' => "Sorry, We couldn't find video.", 'cause' => '', 'data' => json_decode('{}')));

                }
            }

            if ($title) {

                DB::beginTransaction();

                DB::update('UPDATE youtube_video_master
                                SET title = ?
                                    WHERE id = ?
                                ', [
                    $title,
                    $id
                ]);
                DB::commit();
                $response = Response::json(array('code' => 200, 'message' => 'Video updated successfully.', 'cause' => '', 'data' => json_decode('{}')));
            } else {
                $response = Response::json(array('code' => 200, 'message' => 'Video updated successfully.', 'cause' => '', 'data' => json_decode('{}')));
            }
        } catch (Exception $e) {
            Log::error("updateYouTubeVideoURL Error :", ['Error : ' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'update youtube video.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    /**
     * @api {post} deleteYouTubeVideoURL deleteYouTubeVideoURL
     * @apiName deleteYouTubeVideoURL
     * @apiGroup Resume Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     *{
     * "video_id":9 //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Video deleted successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function deleteYouTubeVideoURL(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('video_id'), $request)) != '')
                return $response;

            $video_id = $request->video_id;

            DB::beginTransaction();

            DB::delete('DELETE
                        FROM
                          youtube_video_master
                        WHERE
                          id = ?', [$video_id]);

            DB::commit();

            $response = Response::json(array('code' => 200, 'message' => 'Video deleted successfully.', 'cause' => '', 'data' => json_decode('{}')));
        } catch (Exception $e) {
            Log::error("deleteYouTubeVideoURL Error :", ['Error : ' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'delete video.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /**
     * @api {post} getYouTubeVideoForInterview getYouTubeVideoForInterview
     * @apiName getYouTubeVideoForInterview
     * @apiGroup Resume User
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
     * "message": "Video fatched successfully.",
     * "cause": "",
     * "data": {
     * "result": [
     * {
     * "video_id": 9,
     * "youtube_video_id": "E78k_XDjFLA",
     * "title": "How to act in an interview",
     * "channel_name": "LoquaCommunications",
     * "url": "https://www.youtube.com/watch?v=E78k_XDjFLA",
     * "thumbnail_url": "https://i.ytimg.com/vi/E78k_XDjFLA/hqdefault.jpg",
     * "thumbnail_width": 480,
     * "thumbnail_height": 360,
     * "published_at": "2009-10-07 19:40:34"
     * },
     * {
     * "video_id": 8,
     * "youtube_video_id": "kayOhGRcNt4",
     * "title": "Tell Me About Yourself - A Good Answer to This Interview Question",
     * "channel_name": "Linda Raynier",
     * "url": "https://www.youtube.com/watch?v=kayOhGRcNt4",
     * "thumbnail_url": "https://i.ytimg.com/vi/kayOhGRcNt4/hqdefault.jpg",
     * "thumbnail_width": 480,
     * "thumbnail_height": 360,
     * "published_at": "2016-12-14 15:12:37"
     * },
     * {
     * "video_id": 7,
     * "youtube_video_id": "BkL98JHAO_w",
     * "title": "Mock Job Interview Questions and Tips for a Successful Interview",
     * "channel_name": "Virginia Western Community College",
     * "url": "https://www.youtube.com/watch?v=BkL98JHAO_w",
     * "thumbnail_url": "https://i.ytimg.com/vi/BkL98JHAO_w/hqdefault.jpg",
     * "thumbnail_width": 480,
     * "thumbnail_height": 360,
     * "published_at": "2009-09-25 20:36:08"
     * }
     * ]
     * }
     * }
     */
    public function getYouTubeVideoForInterview()
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            if (!Cache::has("pel:getYouTubeVideoForInterview")) {
                $result = Cache::rememberforever("getYouTubeVideoForInterview", function () {
                    return DB::select('SELECT id video_id,
                                         youtube_video_id,
                                         title,
                                         channel_name,
                                         url,
                                         thumbnail_url,
                                         thumbnail_width,
                                         thumbnail_height,
                                         published_at
                                         FROM youtube_video_master
                                         ORDER BY update_time DESC');
                });
            }
            $redis_result = Cache::get("getYouTubeVideoForInterview");

            if (!$redis_result) {
                return Response::json(array('code' => 427, 'message' => "Sorry, We couldn't find any video", 'cause' => '', 'data' => json_decode('{}')));
            }
            $response = Response::json(array('code' => 200, 'message' => 'Video fatched successfully.', 'cause' => '', 'data' => ['result' => $redis_result]));

        } catch (Exception $e) {
            Log::error("getYouTubeVideoForInterview Error :", ['Error : ' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get video for interview.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    /**
     * @api {post} getYouTubeVideoForInterviewForAdmin getYouTubeVideoForInterviewForAdmin
     * @apiName getYouTubeVideoForInterviewForAdmin
     * @apiGroup Resume Admin
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
     * "message": "Video fatched successfully.",
     * "cause": "",
     * "data": {
     * "result": [
     * {
     * "video_id": 9,
     * "youtube_video_id": "E78k_XDjFLA",
     * "title": "How to act in an interview",
     * "channel_name": "LoquaCommunications",
     * "url": "https://www.youtube.com/watch?v=E78k_XDjFLA",
     * "thumbnail_url": "https://i.ytimg.com/vi/E78k_XDjFLA/hqdefault.jpg",
     * "thumbnail_width": 480,
     * "thumbnail_height": 360,
     * "published_at": "2009-10-07 19:40:34"
     * },
     * {
     * "video_id": 8,
     * "youtube_video_id": "kayOhGRcNt4",
     * "title": "Tell Me About Yourself - A Good Answer to This Interview Question",
     * "channel_name": "Linda Raynier",
     * "url": "https://www.youtube.com/watch?v=kayOhGRcNt4",
     * "thumbnail_url": "https://i.ytimg.com/vi/kayOhGRcNt4/hqdefault.jpg",
     * "thumbnail_width": 480,
     * "thumbnail_height": 360,
     * "published_at": "2016-12-14 15:12:37"
     * },
     * {
     * "video_id": 7,
     * "youtube_video_id": "BkL98JHAO_w",
     * "title": "Mock Job Interview Questions and Tips for a Successful Interview",
     * "channel_name": "Virginia Western Community College",
     * "url": "https://www.youtube.com/watch?v=BkL98JHAO_w",
     * "thumbnail_url": "https://i.ytimg.com/vi/BkL98JHAO_w/hqdefault.jpg",
     * "thumbnail_width": 480,
     * "thumbnail_height": 360,
     * "published_at": "2009-09-25 20:36:08"
     * }
     * ]
     * }
     * }
     */
    public function getYouTubeVideoForInterviewForAdmin()
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            if (!Cache::has("pel:getYouTubeVideoForInterviewForAdmin")) {
                $result = Cache::rememberforever("getYouTubeVideoForInterviewForAdmin", function () {
                    return DB::select('SELECT id video_id,
                                         youtube_video_id,
                                         title,
                                         channel_name,
                                         url,
                                         thumbnail_url,
                                         thumbnail_width,
                                         thumbnail_height,
                                         published_at
                                         FROM youtube_video_master
                                         ORDER BY update_time DESC');
                });
            }
            $redis_result = Cache::get("getYouTubeVideoForInterviewForAdmin");

            if (!$redis_result) {
                return Response::json(array('code' => 427, 'message' => "Sorry, We couldn't find any video", 'cause' => '', 'data' => json_decode('{}')));
            }
            $response = Response::json(array('code' => 200, 'message' => 'Video fatched successfully.', 'cause' => '', 'data' => ['result' => $redis_result]));

        } catch (Exception $e) {
            Log::error("getYouTubeVideoForInterviewForAdmin Error :", ['Error : ' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get video for interview.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    /**
     * @api {post} getVideoIdByURL getVideoIdByURL
     * @apiName getVideoIdByURL
     * @apiGroup Resume Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "url":"https://www.youtube.com/watch?v=yBtMwyQFXwA" //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Video url added successfully.",
     * "cause": "",
     * "data": "yBtMwyQFXwA"
     * }
     */
    public function getVideoIdByURL(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());

            if (($response = (new VerificationController())->validateRequiredParameter(array('url'), $request)) != '')
                return $response;

            $url = $request->url;

            if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $url, $match)) {
                $video_id = $match[1];
            } else {
                $video_id = 1;
            }
            //return $video_id;

            $response = Response::json(array('code' => 200, 'message' => 'Video url added successfully.', 'cause' => '', 'data' => $video_id));

        } catch (Exception $e) {
            Log::error("getVideoIdByURL Error :", ['Error : ' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get video id.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }
}

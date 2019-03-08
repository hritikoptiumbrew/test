<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Input;
use Response;
use DB;
use Config;
use Exception;
use Log;
use Auth;
use Cache;
use File;
use Image;
use Mail;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class QnAController extends Controller
{
    /*===================================== Question type =====================================*/

    /**
     * @api {post} addQuestionType addQuestionType
     * @apiName addQuestionType
     * @apiGroup Resume Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "request_data":{
     * "question_type":1 //compulsory
     * },
     * "file":"1.jpg" //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Question type added successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function addQuestionType(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->input('request_data'));
            if (!$request_body->has('request_data'))
                return Response::json(array('code' => 201, 'message' => 'Required field request_data is missing or empty.', 'cause' => '', 'data' => json_decode("{}")));

            if (($response = (new VerificationController())->validateRequiredParameter(array('question_type'), $request)) != '')
                return $response;

            if (!$request_body->hasFile('file')) {
                return Response::json(array('code' => 201, 'message' => 'Required field file is missing or empty.', 'cause' => '', 'data' => json_decode("{}")));
            }

            $question_type = $request->question_type;
            $create_at = date('Y-m-d H:i:s');
            $image_array = Input::file('file');

            $get_category = DB::select('select * from question_type_master WHERE TRIM(question_type) = ? AND is_active = 1', [TRIM($question_type)]);

            if (count($get_category) > 0) {
                return Response::json(array('code' => 201, 'message' => 'Question type already exist.', 'cause' => '', 'data' => json_decode("{}")));
            }

            if (($response = (new ImageController())->verifyImage($image_array)) != '')
                return $response;

            $image = (new ImageController())->generateNewFileName('question_type', $image_array);

            (new ImageController())->saveOriginalImage($image);
            (new ImageController())->saveCompressedImage($image);
            (new ImageController())->saveThumbnailImage($image);

            if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
                (new ImageController())->saveImageInToS3($image);
            }

            DB::beginTransaction();

            DB::insert('insert into question_type_master (question_type,image,create_time) VALUES(?,?,?)', [$question_type, $image, $create_at]);

            DB::commit();

            $response = Response::json(array('code' => 200, 'message' => 'Question type added successfully.', 'cause' => '', 'data' => json_decode('{}')));

        } catch (Exception $e) {
            Log::error("addQuestionType Error :", ['Error : ' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'add question type.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;

    }

    /**
     * @api {post} updateQuestionType updateQuestionType
     * @apiName updateQuestionType
     * @apiGroup Resume Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "request_data":{
     * "question_type_id":1, //compulsory
     * "question_type":1 //compulsory
     * },
     * "file":"1.jpg"
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Question type updated successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function updateQuestionType(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->input('request_data'));
            if (!$request_body->has('request_data'))
                return Response::json(array('code' => 201, 'message' => 'Required field request_data is missing or empty', 'cause' => '', 'data' => json_decode("{}")));

            if (($response = (new VerificationController())->validateRequiredParameter(array('question_type_id', 'question_type'), $request)) != '')
                return $response;

            $question_type_id = $request->question_type_id;
            $question_type = $request->question_type;

            $get_category = DB::select('select * from question_type_master WHERE TRIM(question_type) = ? AND is_active = 1 AND id != ?', [TRIM($question_type), $question_type_id]);

            if (count($get_category) > 0) {
                return Response::json(array('code' => 201, 'message' => 'Question type already exist.', 'cause' => '', 'data' => json_decode("{}")));
            }
            $category_detail = DB::select('select * from question_type_master WHERE  is_active = 1 AND id = ?', [$question_type_id]);

            $old_img = $category_detail[0]->image;

            if ($request_body->hasFile('file')) {
                $image_array = Input::file('file');
                if (($response = (new ImageController())->verifyImage($image_array)) != '')
                    return $response;

                $image = (new ImageController())->generateNewFileName('question_type', $image_array);

                (new ImageController())->saveOriginalImage($image);
                (new ImageController())->saveCompressedImage($image);
                (new ImageController())->saveThumbnailImage($image);

                if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
                    (new ImageController())->saveImageInToS3($image);
                }

                DB::beginTransaction();

                DB::update('UPDATE
                              question_type_master
                            SET
                              image = ?
                            WHERE
                              id = ? ',
                    [$image, $question_type_id]);
                DB::commit();

            }

            DB::beginTransaction();

            DB::update('UPDATE
                              question_type_master
                            SET
                              question_type = ?
                            WHERE
                              id = ? ',
                [$question_type, $question_type_id]);

            DB::commit();

            if ($old_img) {
                //Image Delete in image_bucket
                (new ImageController())->deleteImage($old_img);
            }

            $response = Response::json(array('code' => 200, 'message' => 'Question type updated successfully.', 'cause' => '', 'data' => json_decode('{}')));

        } catch (Exception $e) {
            Log::error("updateQuestionType Error :", ['Error : ' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'update question type.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;

    }

    /**
     * @api {post} deleteQuestionType deleteQuestionType
     * @apiName deleteQuestionType
     * @apiGroup Resume Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "question_type_id":7
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Question type deleted successfully",
     * "cause": "",
     * "data": {}
     * }
     */
    public function deleteQuestionType(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());

            if (($response = (new VerificationController())->validateRequiredParameter(array('question_type_id'), $request)) != '')
                return $response;

            $question_type_id = $request->question_type_id;

            $category_detail = DB::select('select * from question_type_master WHERE is_active = 1 AND id = ?', [$question_type_id]);

            if (count($category_detail) > 0) {
                $old_img = $category_detail[0]->image;
            }

            DB::beginTransaction();

            DB::delete('DELETE
                        FROM question_type_master
                        WHERE
                        id = ? ',
                [$question_type_id]);

            DB::commit();

            if ($old_img) {
                //Image Delete in image_bucket
                (new ImageController())->deleteImage($old_img);
            }

            $response = Response::json(array('code' => 200, 'message' => 'Question type deleted successfully.', 'cause' => '', 'data' => json_decode('{}')));

        } catch (Exception $e) {
            Log::error("deleteQuestionType Error :", ['Error : ' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'delete question type.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;

    }

    /**
     * @api {post} getAllQuestionType getAllQuestionType
     * @apiName getAllQuestionType
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
     * "message": "All question and answer fetched successfully.",
     * "cause": "",
     * "data": {
     * "result": [
     * {
     * "question_type_id": 1,
     * "question_type": "Interview Prep Plan",
     * "question_type_image": "http://192.168.0.113/photo_editor_lab_backend/image_bucket/compressed/5c22f822b2c0d_question_type_1545795618.png",
     * "create_time": "2018-11-28 10:38:41",
     * "update_time": "2018-12-26 03:40:19"
     * },
     * {
     * "question_type_id": 2,
     * "question_type": "Most Common",
     * "question_type_image": "",
     * "create_time": "2018-11-28 10:38:47",
     * "update_time": "2018-11-28 10:38:47"
     * },
     * {
     * "question_type_id": 3,
     * "question_type": "Behavioural",
     * "question_type_image": "",
     * "create_time": "2018-11-28 10:38:51",
     * "update_time": "2018-11-28 10:38:51"
     * }
     * ]
     * }
     * }
     */
    public function getAllQuestionType()
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $this->is_active = 1;

            if (!Cache::has("pel:getAllQuestionType")) {
                $result = Cache::rememberforever("getAllQuestionType", function () {
                    return DB::select('SELECT
                                    qm.id as question_type_id,
                                    qm.question_type,
                                    IF(qm.image != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",qm.image),"") as question_type_image,
                                    qm.create_time,
                                    qm.update_time
                                  FROM
                                  question_type_master as qm
                                  where qm.is_active = ?
                                  ORDER BY update_time DESC', [$this->is_active]);
                });
            }

            $redis_result = Cache::get("getAllQuestionType");

            if (!$redis_result) {
                $redis_result = [];
            }

            $response = Response::json(array('code' => 200, 'message' => 'All question and answer fetched successfully.', 'cause' => '', 'data' => ['result' => $redis_result]));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            Log::error("getAllQuestionType Error :", ['Error : ' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get question type.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;

    }

    /**
     * @api {post} getAllQuestionTypeForAdmin getAllQuestionTypeForAdmin
     * @apiName getAllQuestionTypeForAdmin
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
     * "message": "All question and answer fetched successfully.",
     * "cause": "",
     * "data": {
     * "result": [
     * {
     * "question_type_id": 1,
     * "question_type": "Interview Prep Plan",
     * "question_type_image": "http://192.168.0.113/photo_editor_lab_backend/image_bucket/compressed/5c22f822b2c0d_question_type_1545795618.png",
     * "create_time": "2018-11-28 10:38:41",
     * "update_time": "2018-12-26 03:40:19"
     * },
     * {
     * "question_type_id": 2,
     * "question_type": "Most Common",
     * "question_type_image": "",
     * "create_time": "2018-11-28 10:38:47",
     * "update_time": "2018-11-28 10:38:47"
     * },
     * {
     * "question_type_id": 3,
     * "question_type": "Behavioural",
     * "question_type_image": "",
     * "create_time": "2018-11-28 10:38:51",
     * "update_time": "2018-11-28 10:38:51"
     * }
     * ]
     * }
     * }
     */
    public function getAllQuestionTypeForAdmin()
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $this->is_active = 1;

            if (!Cache::has("pel:getAllQuestionTypeForAdmin")) {
                $result = Cache::rememberforever("getAllQuestionTypeForAdmin", function () {
                    return DB::select('SELECT
                                    qm.id as question_type_id,
                                    qm.question_type,
                                    IF(qm.image != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",qm.image),"") as question_type_image,
                                    qm.create_time,
                                    qm.update_time
                                  FROM
                                  question_type_master as qm
                                  where qm.is_active = ?
                                  ORDER BY update_time DESC', [$this->is_active]);
                });
            }

            $redis_result = Cache::get("getAllQuestionTypeForAdmin");

            if (!$redis_result) {
                $redis_result = [];
            }

            $response = Response::json(array('code' => 200, 'message' => 'All question and answer fetched successfully.', 'cause' => '', 'data' => ['result' => $redis_result]));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            Log::error("getAllQuestionTypeForAdmin Error :", ['Error : ' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get question type.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;

    }

    /*===================================== Question Answer =====================================*/

    /**
     * @api {post} addQuestionAnswer addQuestionAnswer
     * @apiName addQuestionAnswer
     * @apiGroup Resume Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "question":"Test", //compulsory
     * "answer":"<p>Test</p>", //compulsory
     * "question_type":1 //compulsory
     * }
     * @apiSuccessExample Success-Response:
     *{
     * "code": 200,
     * "message": "Question and Answer added successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function addQuestionAnswer(Request $request_body)
    {
        try {

            /*$token = JWTAuth::getToken();
            JWTAuth::toUser($token);*/

            $request = json_decode($request_body->getContent());

            if (($response = (new VerificationController())->validateRequiredParameter(array('question', 'answer', 'question_type'), $request)) != '')
                return $response;

            $question = $request->question;
            $answer = $request->answer;
            $question_type = isset($request->question_type) ? $request->question_type : 1;
            $create_at = date('Y-m-d H:i:s');


            DB::beginTransaction();

            DB::insert('insert into question_master (question_type,question,answer,create_time) VALUES(?,?,?,?)', [$question_type, $question, $answer, $create_at]);

            DB::commit();

            $response = Response::json(array('code' => 200, 'message' => 'Question and Answer added successfully.', 'cause' => '', 'data' => json_decode('{}')));

        } catch (Exception $e) {
            Log::error("addQuestionAnswer Error :", ['Error : ' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'add question and answer.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;

    }

    /**
     * @api {post} updateQuestionAnswer updateQuestionAnswer
     * @apiName updateQuestionAnswer
     * @apiGroup Resume Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "question_id":1, //compulsory
     * "question":"Test", //compulsory
     * "answer":"<p>Test</p>", //compulsory
     * "question_type":1 //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Question and Answer updated successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function updateQuestionAnswer(Request $request_body)
    {
        try {

            /*$token = JWTAuth::getToken();
            JWTAuth::toUser($token);*/

            $request = json_decode($request_body->getContent());

            if (($response = (new VerificationController())->validateRequiredParameter(array('question_id', 'question', 'answer', 'question_type'), $request)) != '')
                return $response;

            $question_id = $request->question_id;
            $question = $request->question;
            $question_type = isset($request->question_type) ? $request->question_type : 1;
            $answer = $request->answer;

            DB::beginTransaction();

            DB::update('UPDATE
                              question_master
                            SET
                              question_type = ?,
                              question = ?,
                              answer = ?
                            WHERE
                              id = ? ',
                [$question_type, $question, $answer, $question_id]);

            DB::commit();

            $response = Response::json(array('code' => 200, 'message' => 'Question and Answer updated successfully.', 'cause' => '', 'data' => json_decode('{}')));

        } catch (Exception $e) {
            Log::error("updateQuestionAnswer Error :", ['Error : ' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'update question and answer.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;

    }

    /**
     * @api {post} deleteQuestionAnswer deleteQuestionAnswer
     * @apiName deleteQuestionAnswer
     * @apiGroup Resume Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "question_id":1 //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Question and Answer deleted successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function deleteQuestionAnswer(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());

            if (($response = (new VerificationController())->validateRequiredParameter(array('question_id'), $request)) != '')
                return $response;

            $question_id = $request->question_id;

            DB::beginTransaction();

            DB::delete('delete from question_master WHERE id = ? ', [$question_id]);

            DB::commit();

            $response = Response::json(array('code' => 200, 'message' => 'Question and Answer deleted successfully.', 'cause' => '', 'data' => json_decode('{}')));

        } catch (Exception $e) {
            Log::error("deleteQuestionAnswer Error :", ['Error : ' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'delete question and answer.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;

    }

    /**
     * @api {post} getAllQuestionAnswerByType getAllQuestionAnswerByType
     * @apiName getAllQuestionAnswerByType
     * @apiGroup Resume User
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "question_type_id":1 //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "All question and answer fetched successfully.",
     * "cause": "",
     * "data": {
     * "result": [
     * {
     * "question_id": 5,
     * "question_type": 1,
     * "question": "test",
     * "answer": "<p style=\"margin: 0cm 0cm 15pt; line-height: 19.2pt; background-image: initial; background-position: initial; background-size: initial; background-repeat: initial; background-attachment: initial; background-origin: initial; background-clip: initial;\"><font color=\"#333333\" face=\"Georgia, serif\"><span style=\"font-size: 17.3333px;\">test</span></font></p>",
     * "create_time": "2018-12-26 04:58:34",
     * "update_time": "2018-12-26 04:58:34"
     * },
     * {
     * "question_id": 2,
     * "question_type": 1,
     * "question": "Research the organization",
     * "answer": "<p>Test</p>",
     * "create_time": "2018-12-26 04:25:03",
     * "update_time": "2018-12-26 04:25:03"
     * }
     * ]
     * }
     * }
     */
    public function getAllQuestionAnswerByType(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());

            if (($response = (new VerificationController())->validateRequiredParameter(array('question_type_id'), $request)) != '')
                return $response;

            $this->is_active = 1;
            $this->question_type_id = $request->question_type_id;

            if (!Cache::has("pel:getAllQuestionAnswerByType:$this->question_type_id")) {
                $result = Cache::rememberforever("getAllQuestionAnswerByType:$this->question_type_id", function () {
                    return DB::select('SELECT
                                    qm.id as question_id,
                                    qm.question_type,
                                    qm.question,
                                    qm.answer,
                                    qm.create_time,
                                    qm.update_time
                                  FROM
                                  question_master as qm
                                  where qm.is_active=? AND qm.question_type = ?
                                  ORDER BY update_time DESC', [$this->is_active, $this->question_type_id]);
                });
            }

            $redis_result = Cache::get("getAllQuestionAnswerByType:$this->question_type_id");

            if (!$redis_result) {
                $redis_result = [];
            }
            if (count($redis_result) == 0) {
                return Response::json(array('code' => 427, 'message' => "Sorry, We couldn't find any question and answer.", 'cause' => '', 'data' => json_decode('{}')));
            }

            $response = Response::json(array('code' => 200, 'message' => 'All question and answer fetched successfully.', 'cause' => '', 'data' => ['result' => $redis_result]));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            Log::error("getAllQuestionAnswerByType Error :", ['Error : ' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get question and answer by type.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;

    }

    /**
     * @api {post} getAllQuestionAnswerByTypeForAdmin getAllQuestionAnswerByTypeForAdmin
     * @apiName getAllQuestionAnswerByTypeForAdmin
     * @apiGroup Resume Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "question_type_id":1 //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "All question and answer fetched successfully.",
     * "cause": "",
     * "data": {
     * "result": [
     * {
     * "question_id": 5,
     * "question_type": 1,
     * "question": "test",
     * "answer": "<p style=\"margin: 0cm 0cm 15pt; line-height: 19.2pt; background-image: initial; background-position: initial; background-size: initial; background-repeat: initial; background-attachment: initial; background-origin: initial; background-clip: initial;\"><font color=\"#333333\" face=\"Georgia, serif\"><span style=\"font-size: 17.3333px;\">test</span></font></p>",
     * "create_time": "2018-12-26 04:58:34",
     * "update_time": "2018-12-26 04:58:34"
     * },
     * {
     * "question_id": 2,
     * "question_type": 1,
     * "question": "Research the organization",
     * "answer": "<p>Test</p>",
     * "create_time": "2018-12-26 04:25:03",
     * "update_time": "2018-12-26 04:25:03"
     * }
     * ]
     * }
     * }
     */
    public function getAllQuestionAnswerByTypeForAdmin(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());

            if (($response = (new VerificationController())->validateRequiredParameter(array('question_type_id'), $request)) != '')
                return $response;

            $this->is_active = 1;
            $this->question_type_id = $request->question_type_id;

            if (!Cache::has("pel:getAllQuestionAnswerByTypeForAdmin:$this->question_type_id")) {
                $result = Cache::rememberforever("getAllQuestionAnswerByTypeForAdmin:$this->question_type_id", function () {
                    return DB::select('SELECT
                                    qm.id as question_id,
                                    qm.question_type,
                                    qm.question,
                                    qm.answer,
                                    qm.create_time,
                                    qm.update_time
                                  FROM
                                  question_master as qm
                                  where qm.is_active=? AND qm.question_type = ?
                                  ORDER BY update_time DESC', [$this->is_active, $this->question_type_id]);
                });
            }

            $redis_result = Cache::get("getAllQuestionAnswerByTypeForAdmin:$this->question_type_id");

            if (!$redis_result) {
                $redis_result = [];
            }
            if (count($redis_result) == 0) {
                return Response::json(array('code' => 427, 'message' => "Sorry, We couldn't find any question and answer.", 'cause' => '', 'data' => json_decode('{}')));
            }

            $response = Response::json(array('code' => 200, 'message' => 'All question and answer fetched successfully.', 'cause' => '', 'data' => ['result' => $redis_result]));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            Log::error("getAllQuestionAnswerByTypeForAdmin Error :", ['Error : ' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get question and answer by type.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;

    }

    /**
     * @api {post} searchQuestionAnswer searchQuestionAnswer
     * @apiName searchQuestionAnswer
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
     * "question_type":1,
     * "search_query":"Test"
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Question and answer fetched successfully.",
     * "cause": "",
     * "response": {
     * "total_record": 1,
     * "is_next_page": false,
     * "result": [
     * {
     * "question_id": 5,
     * "question_type": 1,
     * "question": "test",
     * "answer": "<p style=\"margin: 0cm 0cm 15pt; line-height: 19.2pt; background-image: initial; background-position: initial; background-size: initial; background-repeat: initial; background-attachment: initial; background-origin: initial; background-clip: initial;\"><font color=\"#333333\" face=\"Georgia, serif\"><span style=\"font-size: 17.3333px;\">test</span></font></p>",
     * "create_time": "2018-12-26 04:58:34",
     * "update_time": "2018-12-26 04:58:34",
     * "search_text": 0.36247622966766
     * }
     * ]
     * }
     * }
     */
    public function searchQuestionAnswer(Request $request)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request->getContent());

            if (($response = (new VerificationController())->validateRequiredParameter(array('page'), $request)) != '')
                return $response;

            $page = $request->page;
            $item_count = Config::get('constant.ITEM_COUNT_TO_GET_QUESTION');
            $offset = ($page - 1) * $item_count;
            $question_type = isset($request->question_type) ? $request->question_type : '';
            $search_query = isset($request->search_query) ? $request->search_query : '';


            if ($question_type == '' && $search_query == '') {

                $total_row_result = DB::select('SELECT COUNT(*) as total FROM question_master');
                $total_row = $total_row_result[0]->total;

                $result = DB::select('SELECT
                                    qm.id as question_id,
                                    qm.question_type,
                                    qm.question,
                                    qm.answer,
                                    qm.create_time,
                                    qm.update_time
                                  FROM
                                  question_master as qm
                                  where qm.is_active=1
                                  ORDER BY update_time DESC LIMIT ?, ?', [$offset, $item_count]);

            } elseif ($question_type == '' && $search_query != '') {

                $total_row_result = DB::select('SELECT COUNT(*) as total FROM question_master WHERE MATCH(question) AGAINST("' . $search_query . '") AND is_active = 1');
                $total_row = $total_row_result[0]->total;

                $result = DB::select('SELECT
                                        qm.id as question_id,
                                        qm.question_type,
                                        qm.question,
                                        qm.answer,
                                        qm.create_time,
                                        qm.update_time,
                                        MATCH(qm.question) AGAINST("' . $search_query . '") AS search_text
                                         FROM question_master as qm
                                        WHERE qm.is_active=1 AND
                                        MATCH(qm.question) AGAINST("' . $search_query . '")
                                        ORDER BY search_text DESC LIMIT ?, ?', [$offset, $item_count]);


            } elseif ($question_type != '' && $search_query == '') {

                $total_row_result = DB::select('SELECT COUNT(*) as total FROM question_master WHERE question_type = ? AND is_active = 1', [$question_type]);
                $total_row = $total_row_result[0]->total;

                $result = DB::select('SELECT
                                        qm.id as question_id,
                                        qm.question_type,
                                        qm.question,
                                        qm.answer,
                                        qm.create_time,
                                        qm.update_time
                                         FROM
                                        question_master as qm
                                        WHERE qm.is_active=1 AND question_type = ? LIMIT ?, ?', [$question_type, $offset, $item_count]);


            } else {

                $total_row_result = DB::select('SELECT COUNT(*) as total FROM question_master WHERE question_type = ? AND is_active = 1 AND MATCH(question) AGAINST("' . $search_query . '")', [$question_type]);
                $total_row = $total_row_result[0]->total;

                $result = DB::select('SELECT
                                        qm.id as question_id,
                                        qm.question_type,
                                        qm.question,
                                        qm.answer,
                                        qm.create_time,
                                        qm.update_time,
                                        MATCH(qm.question) AGAINST("' . $search_query . '") AS search_text
                                         FROM
                                        question_master as qm
                                        WHERE qm.is_active=1 AND
                                              qm.question_type = ? AND
                                              MATCH(qm.question) AGAINST("' . $search_query . '")
                                        ORDER BY search_text DESC LIMIT ?, ?', [$question_type, $offset, $item_count]);


            }

            // $result_array = array('result' => $result);
            //
            // $result = json_decode(json_encode($result_array), true);
            if (count($result) == 0) {
                return Response::json(array('code' => 427, 'message' => "Sorry, We couldn't find any question and answer.", 'cause' => '', 'data' => json_decode('{}')));
            }

            $is_next_page = ($total_row > ($offset + $item_count)) ? true : false;

            $response = Response::json(array('code' => 200, 'message' => 'Question and answer fetched successfully.', 'cause' => '', 'data' => ['total_record' => $total_row, 'is_next_page' => $is_next_page, 'result' => $result]));


            //$response = Response::json(array('code' => 200, 'message' => 'Dealers fetched successfully.', 'cause' => '', 'data' => $result));

            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get all question answer.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            Log::error("searchQuestionAnswer Exception :", ['Exception' => $e->getMessage(), "\nTraceAsString :" => $e->getTraceAsString()]);
            DB::rollBack();
        }
        return $response;

    }

    /**
     * @api {post} searchQuestionAnswer searchQuestionAnswer
     * @apiName searchQuestionAnswer
     * @apiGroup Resume Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "page":1, //compulsory
     * "question_type":1,
     * "search_query":"Test"
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Question and answer fetched successfully.",
     * "cause": "",
     * "response": {
     * "total_record": 1,
     * "is_next_page": false,
     * "result": [
     * {
     * "question_id": 5,
     * "question_type": 1,
     * "question": "test",
     * "answer": "<p style=\"margin: 0cm 0cm 15pt; line-height: 19.2pt; background-image: initial; background-position: initial; background-size: initial; background-repeat: initial; background-attachment: initial; background-origin: initial; background-clip: initial;\"><font color=\"#333333\" face=\"Georgia, serif\"><span style=\"font-size: 17.3333px;\">test</span></font></p>",
     * "create_time": "2018-12-26 04:58:34",
     * "update_time": "2018-12-26 04:58:34",
     * "search_text": 0.36247622966766
     * }
     * ]
     * }
     * }
     */
    public function searchQuestionAnswerForAdmin(Request $request)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request->getContent());

            if (($response = (new VerificationController())->validateRequiredParameter(array('page'), $request)) != '')
                return $response;

            $page = $request->page;
            $item_count = Config::get('constant.ITEM_COUNT_TO_GET_QUESTION');
            $offset = ($page - 1) * $item_count;
            $question_type = isset($request->question_type) ? $request->question_type : '';
            $search_query = isset($request->search_query) ? $request->search_query : '';


            if ($question_type == '' && $search_query == '') {

                $total_row_result = DB::select('SELECT COUNT(*) as total FROM question_master');
                $total_row = $total_row_result[0]->total;

                $result = DB::select('SELECT
                                    qm.id as question_id,
                                    qm.question_type,
                                    qm.question,
                                    qm.answer,
                                    qm.create_time,
                                    qm.update_time
                                  FROM
                                  question_master as qm
                                  where qm.is_active=1
                                  ORDER BY update_time DESC LIMIT ?, ?', [$offset, $item_count]);

            } elseif ($question_type == '' && $search_query != '') {

                $total_row_result = DB::select('SELECT COUNT(*) as total FROM question_master WHERE MATCH(question) AGAINST("' . $search_query . '") AND is_active = 1');
                $total_row = $total_row_result[0]->total;

                $result = DB::select('SELECT
                                        qm.id as question_id,
                                        qm.question_type,
                                        qm.question,
                                        qm.answer,
                                        qm.create_time,
                                        qm.update_time,
                                        MATCH(qm.question) AGAINST("' . $search_query . '") AS search_text
                                         FROM question_master as qm
                                        WHERE qm.is_active=1 AND
                                        MATCH(qm.question) AGAINST("' . $search_query . '")
                                        ORDER BY search_text DESC LIMIT ?, ?', [$offset, $item_count]);


            } elseif ($question_type != '' && $search_query == '') {

                $total_row_result = DB::select('SELECT COUNT(*) as total FROM question_master WHERE question_type = ? AND is_active = 1', [$question_type]);
                $total_row = $total_row_result[0]->total;

                $result = DB::select('SELECT
                                        qm.id as question_id,
                                        qm.question_type,
                                        qm.question,
                                        qm.answer,
                                        qm.create_time,
                                        qm.update_time
                                         FROM
                                        question_master as qm
                                        WHERE qm.is_active=1 AND question_type = ? LIMIT ?, ?', [$question_type, $offset, $item_count]);


            } else {

                $total_row_result = DB::select('SELECT COUNT(*) as total FROM question_master WHERE question_type = ? AND is_active = 1 AND MATCH(question) AGAINST("' . $search_query . '")', [$question_type]);
                $total_row = $total_row_result[0]->total;

                $result = DB::select('SELECT
                                        qm.id as question_id,
                                        qm.question_type,
                                        qm.question,
                                        qm.answer,
                                        qm.create_time,
                                        qm.update_time,
                                        MATCH(qm.question) AGAINST("' . $search_query . '") AS search_text
                                         FROM
                                        question_master as qm
                                        WHERE qm.is_active=1 AND
                                              qm.question_type = ? AND
                                              MATCH(qm.question) AGAINST("' . $search_query . '")
                                        ORDER BY search_text DESC LIMIT ?, ?', [$question_type, $offset, $item_count]);


            }

            // $result_array = array('result' => $result);
            //
            // $result = json_decode(json_encode($result_array), true);
            if (count($result) == 0) {
                return Response::json(array('code' => 427, 'message' => "Sorry, We couldn't find any question and answer.", 'cause' => '', 'data' => json_decode('{}')));
            }

            $is_next_page = ($total_row > ($offset + $item_count)) ? true : false;

            $response = Response::json(array('code' => 200, 'message' => 'Question and answer fetched successfully.', 'cause' => '', 'data' => ['total_record' => $total_row, 'is_next_page' => $is_next_page, 'result' => $result]));


            //$response = Response::json(array('code' => 200, 'message' => 'Dealers fetched successfully.', 'cause' => '', 'data' => $result));

            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get all question answer.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            Log::error("searchQuestionAnswerForAdmin Exception :", ['Exception' => $e->getMessage(), "\nTraceAsString :" => $e->getTraceAsString()]);
            DB::rollBack();
        }
        return $response;

    }

    /**
     * @api {post} getAllQuestionAnswer getAllQuestionAnswer
     * @apiName getAllQuestionAnswer
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
     * "message": "All question and answer fetched successfully.",
     * "cause": "",
     * "data": {
     * "result": [
     * {
     * "question_id": 5,
     * "question_type": 1,
     * "question": "test",
     * "answer": "<p style=\"margin: 0cm 0cm 15pt; line-height: 19.2pt; background-image: initial; background-position: initial; background-size: initial; background-repeat: initial; background-attachment: initial; background-origin: initial; background-clip: initial;\"><font color=\"#333333\" face=\"Georgia, serif\"><span style=\"font-size: 17.3333px;\">test</span></font></p>",
     * "create_time": "2018-12-26 04:58:34",
     * "update_time": "2018-12-26 04:58:34"
     * },
     * {
     * "question_id": 3,
     * "question_type": 2,
     * "question": "Research the organization",
     * "answer": "<p>test1</p>",
     * "create_time": "2018-12-26 04:25:34",
     * "update_time": "2018-12-26 04:30:49"
     * }
     * ]
     * }
     * }
     */
    public function getAllQuestionAnswer()
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $this->is_active = 1;

            if (!Cache::has("pel:getAllQuestionAnswer")) {
                $result = Cache::rememberforever("getAllQuestionAnswer", function () {
                    return DB::select('SELECT
                                    qm.id as question_id,
                                    qm.question_type,
                                    qm.question,
                                    qm.answer,
                                    qm.create_time,
                                    qm.update_time
                                  FROM
                                  question_master as qm
                                  where qm.is_active=?
                                  ORDER BY update_time DESC', [$this->is_active]);
                });
            }

            $redis_result = Cache::get("getAllQuestionAnswer");

            if (!$redis_result) {
                $redis_result = [];
            }

            $response = Response::json(array('code' => 200, 'message' => 'All question and answer fetched successfully.', 'cause' => '', 'data' => ['result' => $redis_result]));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            Log::error("getAllQuestionAnswer Error :", ['Error : ' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get question and answer.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;

    }

}

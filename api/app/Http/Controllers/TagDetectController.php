<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Clarifai\API\ClarifaiClient;
use Clarifai\DTOs\Inputs\ClarifaiFileImage;
use Clarifai\DTOs\Outputs\ClarifaiOutput;
use Clarifai\DTOs\Predictions\Concept;
use Clarifai\DTOs\Inputs\ClarifaiURLImage;
use Cache;
use Illuminate\Support\Facades\Input;
use Config;
use function PHPSTORM_META\elementType;
use Response;
use App\Jobs\EmailJob;
use Log;
use Illuminate\Support\Facades\Redis;

class TagDetectController extends Controller
{

    public function getTagInImageByViaURL($file_name, $file_path)
    {
        try {

            $keys = Config::get('constant.CLARIFAI_API_KEY');

            $key = explode(',', $keys);
            $i = 0;
            foreach ($key as $k) {
                $kt[$i] = $k;
                $i++;
            }
            $redis_keys = Redis::keys('pel:currentKeyForGetTag:*');

            count($redis_keys) > 0 ? $this->currentKey = substr($redis_keys[0], -1) : $this->currentKey = 0;

            /*return $kt[$this->currentKey];*/
            //Log::info('Current Key :', ['API Key' => $kt[$this->currentKey]]);
            $image_url = $file_path.$file_name;
            //Log::info('img_url',[$image_url]);
            $result = $this->getTagByURL($image_url, $kt[$this->currentKey]);
            //Log::info('is_success',['is_success' => $result['is_success'], 'result' => $result]);
            if ($result['is_success'] == 0) {

                $redis_keys = Redis::keys('pel:currentKeyForGetTag*');
                count($redis_keys) > 0 ? $this->currentKey = substr($redis_keys[0], -1) : $this->currentKey = 0;
                //Log::info('redis_keys :', ['redis_keys' => $this->currentKey]);
                if ($this->currentKey != 0) {
                    //Log::info('deleteRedisKey');
                    $this->deleteRedisKey($redis_keys[0]);
                }
                $this->currentKey;
                //$this->deleteRedisKey($redis_keys);
                $getKey = $this->increaseCurrentKey($this->currentKey);

                $currentKey = $getKey + 1;
                $template = 'simple';
                $host_name = request()->getHttpHost();
                $subject = "PhotoEditorLab: Clarifai account limit exceeded (host: $host_name)";
                //$message_body = "Error Code : " . $result['statusCode'] . "<br>" . $result['description'] . "<br>Now, Current key is $currentKey";
                $message_body = array(
                    'message' => "Clarifai $result[description] .<br>Stauts code is $result[statusCode].<br>Now, The updated key is $currentKey",
                    'user_name' => 'Admin'
                );
                $api_name = 'getTagInImageByViaURL';
                $api_description = $result['description'];
                $email_id = 'pooja.optimumbrew@gmail.com';
                $this->dispatch(new EmailJob(1, $email_id, $subject, $message_body, $template, $api_name, $api_description));

                //return Response::json(array('code' => 201, 'message' => 'The server is unable to load images. Please try again after 10-20 minutes.', 'cause' => '', 'data' => json_decode('{}')));
                return $tag = "";
            } else {
                return $tag = $result['tag'];
            }

        } catch (Exception $e) {
            //$response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'detect lable from image by amazon.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            Log::error("getTagInImageByViaURL : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            return $tag = "";
        }

    }

    public function getTagInImageByBytes($photo)
    {
        try {

            $keys = Config::get('constant.CLARIFAI_API_KEY');

            if($keys != "")
            {
                $key = explode(',', $keys);
                $i = 0;
                foreach ($key as $k) {
                    $kt[$i] = $k;
                    $i++;
                }
                $redis_keys = Redis::keys('pel:currentKeyForGetTag:*');

                count($redis_keys) > 0 ? $this->currentKey = substr($redis_keys[0], -1) : $this->currentKey = 0;

                /*return $kt[$this->currentKey];*/
                //Log::info('Current Key :', ['API Key' => $kt[$this->currentKey]]);
                $result = $this->getTagByImage($photo, $kt[$this->currentKey]);

                if ($result['is_success'] == 0) {
                    $redis_keys = Redis::keys('pel:currentKeyForGetTag*');
                    count($redis_keys) > 0 ? $this->currentKey = substr($redis_keys[0], -1) : $this->currentKey = 0;
                    //Log::info('redis_keys :', ['redis_keys' => $this->currentKey]);
                    if ($this->currentKey != 0) {
                        //Log::info('deleteRedisKey');
                        $this->deleteRedisKey($redis_keys[0]);
                    }
                    $this->currentKey;
                    //$this->deleteRedisKey($redis_keys);
                    $getKey = $this->increaseCurrentKey($this->currentKey);



                    $currentKey = $getKey + 1;
                    $template = 'simple';
                    $host_name = request()->getHttpHost();
                    $subject = "PhotoEditorLab: Clarifai account limit exceeded (host: $host_name)";
                    //$message_body = "Error Code : " . $result['statusCode'] . "<br>" . $result['description'] . "<br>Now, Current key is $currentKey";
                    $message_body = array(
                        'message' => "Clarifai $result[description] .<br>Stauts code is $result[statusCode].<br>Now, The updated key is $currentKey",
                        'user_name' => 'Admin'
                    );
                    $api_name = 'getTagInImageByViaBytes';
                    $api_description = $result['description'];
                    $email_id = 'pooja.optimumbrew@gmail.com';
                    $this->dispatch(new EmailJob(1, $email_id, $subject, $message_body, $template, $api_name, $api_description));

                    //return Response::json(array('code' => 201, 'message' => 'The server is unable to load images. Please try again after 10-20 minutes.', 'cause' => '', 'data' => json_decode('{}')));
                    return $tag = "";
                } else {
                    return $tag = $result['tag'];
                }
            }
            else
            {
                return $tag = "";
            }



        } catch (Exception $e) {
            //$response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'detect lable from image by amazon.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            Log::error("getTagInImageByBytes : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            return $tag = "";
        }

    }

    /* =====================================| Function |==============================================*/

    public function getTagByURL($image_url, $currentKey)
    {
        try {
            //$client = new ClarifaiClient('bee6bec4edc24f9192feee0db7131ffc');
            $client = new ClarifaiClient($currentKey);

            $response = $client->publicModels()->generalModel()->predict(
                new ClarifaiURLImage($image_url))
                ->executeSync();

            //Log::info('',[$response->status()->statusCode()]);
            if ($response->isSuccessful()) {
                /** @var ClarifaiOutput $output */
                $output = $response->get();

                //echo "Predicted concepts:\n";
                /** @var Concept $concept */
                $tag = [];
                foreach ($output->data() as $concept) {
                    $tag[] = $concept->name();
                }
                $tag = implode(",", $tag);
                $result = ['is_success' => 1, 'tag' => $tag];
                //Log::info('Success',['is_success' => 1, 'tag' => $tag]);

            } else {
                Log::info('Clarifai API Failure : ',['is_success' => 0, 'description' => $response->status()->description(), 'statusCode' => $response->status()->statusCode()]);
                $result = ['is_success' => 0, 'description' => $response->status()->description(), 'statusCode' => $response->status()->statusCode()];

            }

        } catch (Exception $e) {
            $result = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'detect label from image by amazon.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            Log::error("getTagByURL : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
        }
        return $result;
    }

    public function getTagByImage($photo, $currentKey)
    {
        try {
            $client = new ClarifaiClient($currentKey);

            try{
                $response = $client->publicModels()->generalModel()->predict(
                    new ClarifaiFileImage(file_get_contents($photo)))
                    ->executeSync();
            }catch (Exception $e) {
                Log::debug("getTagByImage CURL API call : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
                $response = '';
            }

            /*return $response->status()->statusCode();*/

            if ($response->isSuccessful()) {
                /** @var ClarifaiOutput $output */
                $output = $response->get();


                //echo "Predicted concepts:\n";
                /** @var Concept $concept */
                $tag = [];
                foreach ($output->data() as $concept) {
                    $tag[] = $concept->name();
                }
                $tag = implode(",", $tag);
                $result = ['is_success' => 1, 'tag' => $tag];
                //Log::debug('getTagByImage success : ', ['is_success' => 1, 'tag' => $tag]);
            } else {
                Log::error('getTagByImage failed : ',['error_details' => $response->status()->errorDetails(), 'error_description' => $response->status()->description(), 'statusCode' => $response->status()->statusCode()]);
                $result = ['is_success' => 0, 'description' => $response->status()->description(), 'statusCode' => $response->status()->statusCode()];

            }

        } catch (Exception $e) {
            $result = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'detect label from image by amazon.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            Log::error("getTagByImage : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
        }
        return $result;
    }

    public function getCurrentKey()
    {
        try {

            $redis_keys = Redis::keys('pel:currentKeyForGetTag:*');
            $key = explode(',', $redis_keys);

            $result = isset($redis_keys) ? $redis_keys : '{}';

            $response = Response::json(array('code' => 200, 'message' => 'Current key fetched successfully.', 'cause' => '', 'data' => $result));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get current key.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            Log::error("getCurrentKey : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
        }
        return $response;
    }

    public function increaseCurrentKey($currentKey)
    {
        try {
            $keys = Config::get('constant.CLARIFAI_API_KEY');
            $key = explode(',', $keys);
            $i = 0;
            foreach ($key as $k) {
                $kt[$i] = $k;
                $i++;
            }
            $countKey = $i - 1;
            $this->currentKey = $currentKey;
            if ($this->currentKey == $countKey) {
                //Log::info('$this->currentKey = 0');
                $this->currentKey = 0;
            } else {
                //Log::info('$this->currentKey = $this->currentKey + 1');
                $this->currentKey = $this->currentKey + 1;
            }

            /*if (!Cache::has("aud:currentKeyForGetTag:$this->currentKey")) {
                $result = Cache::rememberforever("currentKeyForGetTag:$this->currentKey", function () {
                    Log::info('Current Key :' . $this->currentKey);
                    return $this->currentKey;
                });
            }
            $redis_result = Cache::get("currentKeyForGetTag:$this->currentKey");*/

            if (!Cache::has("pel:currentKeyForGetTag:$this->currentKey")) {
                $result = Cache::rememberforever("currentKeyForGetTag:$this->currentKey", function () {
                    //Log::info('Current Key :' . $this->currentKey);
                    return $this->currentKey;
                });
            }
            $redis_result = Cache::get("currentKeyForGetTag:$this->currentKey");


            /*Redis::expire("currentKeyForGetTag:$this->currentKey", 1);*/
            $response = $redis_result;
        } catch (Exception $e) {
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'change API key of clarifai.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            Log::error("increaseCurrentKey : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
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

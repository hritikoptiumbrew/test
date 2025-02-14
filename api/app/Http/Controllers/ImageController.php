<?php


namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Support\Facades\Storage;
use Image;
use Exception;
use Response;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Cache;
use File;
use Log;
use DB;
use Aws\CloudFront\CloudFrontClient;
use Aws\Exception\AwsException;

class ImageController extends Controller
{
    //verify image
    public function verifyImage($image_array, $category_id, $is_featured, $is_catalog)
    {

        $image_type = $image_array->getMimeType();
        $image_size = $image_array->getSize();
        //Log::info("verifyImage image details : ",['size' => $image_size, 'mime_type' => $image_type]);

        /*
         * check size into kb
         * here 100 is kb & 1024 is bytes
         * 1kb = 1024 bytes
         * */

        $validations = $this->getValidationFromCache($category_id, $is_featured, $is_catalog);
        //Log::info('verifyImage : ', ['validations' => $validations]);

        $MAXIMUM_FILESIZE = $validations * 1024;
        //$MAXIMUM_FILESIZE = 100 * 1024;

        if (!($image_type == 'image/png' || $image_type == 'image/jpeg'))
            $response = Response::json(array('code' => 201, 'message' => 'Please select PNG or JPEG file.', 'cause' => '', 'data' => json_decode("{}")));
        elseif ($image_size > $MAXIMUM_FILESIZE)
            $response = Response::json(array('code' => 201, 'message' => 'File Size is greater then '.$validations.'KB.', 'cause' => '', 'data' => json_decode("{}")));
        else
            $response = '';
        return $response;
    }

    public function verifyPDF($image_array)
    {

        $image_type = $image_array->getMimeType();
        $image_size = $image_array->getSize();
        //Log::info("verifyImage image details : ",['size' => $image_size, 'mime_type' => $image_type]);

        /*
         * check size into kb
         * here 100 is kb & 1024 is bytes
         * 1kb = 1024 bytes
         * */

        //$validations = $this->getValidationFromCache($category_id, $is_featured, $is_catalog);
        //Log::info('verifyImage : ', ['validations' => $validations]);

        //$MAXIMUM_FILESIZE = $validations * 1024;
        //$MAXIMUM_FILESIZE = 10000 * 1024;
        $MAXIMUM_FILESIZE = 10 * 1024 * 1024;

        if (!($image_type == 'application/pdf'))
            $response = Response::json(array('code' => 201, 'message' => 'Please select PDF file.', 'cause' => '', 'data' => json_decode("{}")));
        elseif ($image_size > $MAXIMUM_FILESIZE)
            $response = Response::json(array('code' => 201, 'message' => 'File Size is greater then 10MB.', 'cause' => '', 'data' => json_decode("{}")));
        else
            $response = '';
        return $response;
    }

    public function verifyImageWithSvg($image_array, $category_id, $is_featured, $is_catalog)
    {

        $image_type = $image_array->getMimeType();
        $image_size = $image_array->getSize();
        //Log::info("verifyImage image details : ",['size' => $image_size, 'mime_type' => $image_type]);

        /*
         * check size into kb
         * here 100 is kb & 1024 is bytes
         * 1kb = 1024 bytes
         * */

        $validations = $this->getValidationFromCache($category_id, $is_featured, $is_catalog);
        //Log::info('verifyImage : ', ['validations' => $validations]);

        $MAXIMUM_FILESIZE = $validations * 1024;
        //$MAXIMUM_FILESIZE = 100 * 1024;

        if (!($image_type == 'image/png' || $image_type == 'image/jpeg' || $image_type == 'image/svg+xml' || $image_type == 'image/svg'))
            $response = Response::json(array('code' => 201, 'message' => 'Please select PNG or JPEG or SVG file.', 'cause' => '', 'data' => json_decode("{}")));
        elseif ($image_size > $MAXIMUM_FILESIZE)
            $response = Response::json(array('code' => 201, 'message' => 'File Size is greater then ' . $validations . 'KB.', 'cause' => '', 'data' => json_decode("{}")));
        else
            $response = '';
        return $response;
    }

    public function verifyIcon($image_array)
    {
        $image_type = $image_array->getMimeType();
        $image_size = $image_array->getSize();

        $MAXIMUM_FILESIZE = 50 * 1024; //50kb

        if (!($image_type == 'image/png'))
            $response = Response::json(array('code' => 201, 'message' => 'Please select PNG icon file.', 'cause' => '', 'data' => json_decode("{}")));
        elseif ($image_size > $MAXIMUM_FILESIZE)
            $response = Response::json(array('code' => 201, 'message' => 'Icon file Size is greater then 50KB.', 'cause' => '', 'data' => json_decode("{}")));
        else
            $response = '';
        return $response;
    }

    //verify sample image of cards
    public function verifySampleImage($image_array, $category_id, $is_featured, $is_catalog)
    {

        $image_type = $image_array->getMimeType();
        $image_size = $image_array->getSize();

        /*
         * check size into kb
         * here 200 is kb & 1024 is bytes
         * 1kb = 1024 bytes
         * */

        $validations = $this->getValidationFromCache($category_id, $is_featured, $is_catalog);
        //Log::info('verifyImage : ', ['validations' => $validations]);

        $MAXIMUM_FILESIZE = $validations * 1024;
        //$MAXIMUM_FILESIZE = 200 * 1024;

        if (!($image_type == 'image/png' || $image_type == 'image/jpeg'))
            $response = Response::json(array('code' => 201, 'message' => 'Please select PNG or JPEG file', 'cause' => '', 'data' => json_decode("{}")));
        elseif ($image_size > $MAXIMUM_FILESIZE)
            $response = Response::json(array('code' => 201, 'message' => 'File size is greater then '.$validations.'', 'cause' => '', 'data' => json_decode("{}")));
        else
            $response = '';
        return $response;
    }

    //verify sample gif of cards
    public function verifySampleGif($image_array, $category_id, $is_featured, $is_catalog)
    {

        $image_type = $image_array->getMimeType();
        $image_size = $image_array->getSize();

        /*
         * check size into kb
         * here 200 is kb & 1024 is bytes
         * 1kb = 1024 bytes
         * */

        $validations = $this->getValidationFromCache($category_id, $is_featured, $is_catalog);
        //Log::info('verifyImage : ', ['validations' => $validations]);

        $MAXIMUM_FILESIZE = $validations * 1024;
        //$MAXIMUM_FILESIZE = 200 * 1024;

        if (!($image_type == 'image/gif'))
            $response = Response::json(array('code' => 201, 'message' => 'Please select GIF file', 'cause' => '', 'data' => json_decode("{}")));
        elseif ($image_size > $MAXIMUM_FILESIZE)
            $response = Response::json(array('code' => 201, 'message' => 'File size is greater then '.$validations.'', 'cause' => '', 'data' => json_decode("{}")));
        else
            $response = '';
        return $response;
    }

    //verify images array
    public function verifyImagesArray($images_array, $is_resource_images, $category_id, $is_featured, $is_catalog)
    {
        $files_array = array();
        if ($is_resource_images == 1) {
            foreach ($images_array as $key) {

                if (($response = $this->verifySampleImage($key, $category_id, $is_featured, $is_catalog)) != '') {
                    $file_name = $key->getClientOriginalName();
                    $data = (json_decode(json_encode($response), true));
                    $message = $data['original']['message'];
                    $files_array[] = array('file_name' => $file_name, 'error_message' => $message);
                }
            }
        } elseif ($is_resource_images == 2) {
            foreach ($images_array as $key) {

                if (($response = $this->verifyImageWithSvg($key, $category_id, $is_featured, $is_catalog)) != '') {
                    $file_name = $key->getClientOriginalName();
                    $data = (json_decode(json_encode($response), true));
                    $message = $data['original']['message'];
                    $files_array[] = array('file_name' => $file_name, 'error_message' => $message);
                }
            }
        } else {
            foreach ($images_array as $key) {

                if (($response = $this->verifyImage($key, $category_id, $is_featured, $is_catalog)) != '') {
                    $file_name = $key->getClientOriginalName();
                    $data = (json_decode(json_encode($response), true));
                    $message = $data['original']['message'];
                    $files_array[] = array('file_name' => $file_name, 'error_message' => $message);
                }
            }
        }


        if (sizeof($files_array) > 0) {
            $array = array('error_list' => $files_array);
            $result = json_decode(json_encode($array), true);
            return $response = Response::json(array('code' => 432, 'message' => 'File is not verified by size or format. Please check error list.', 'cause' => '', 'data' => $result));
        } else {
            return $response = '';
        }
    }

    //verify video
    public function verifyVideo($video_array)
    {

        $video_type = $video_array->getMimeType();
        $video_size = $video_array->getSize();

        $MAXIMUM_FILESIZE = 10 * 1024 * 1024;

        //x-ms-asf ==>.asf or .asx
        //ap4 ==>.mp4
        //webm ==>.webm (upload mkv file)
        //quicktime ==>.mov

        if (!($video_type == 'video/x-ms-asf' || $video_type == 'video/mp4' || $video_type == 'video/webm' || $video_type == 'video/quicktime')) {
            return $response = Response::json(array('code' => 201, 'message' => 'Please select asf or mp4 or webm(mkv) or mov file', 'cause' => '', 'data' => json_decode("{}")));

        } elseif ($video_size > $MAXIMUM_FILESIZE) {
            return $response = Response::json(array('code' => 201, 'message' => 'File Size is greater then 10MB', 'cause' => '', 'data' => json_decode("{}")));
        } else
            $response = '';
        return $response;
    }

    //verify audio
    public function verifyAudio($audio_array)
    {

        $audio_type = $audio_array->getMimeType();
        $audio_size = $audio_array->getSize();

        $MAXIMUM_FILESIZE = 10 * 1024 * 1024;

        //octet-stream ==>.3gp
        //quicktime ==>.mov

        if (!($audio_type == 'audio/mpeg' || $audio_type == 'application/octet-stream')) {
            return $response = Response::json(array('code' => 201, 'message' => 'Please select 3gp or mp4 audio file', 'cause' => '', 'data' => json_decode("{}")));
        } elseif ($audio_size > $MAXIMUM_FILESIZE) {
            return $response = Response::json(array('code' => 201, 'message' => 'File Size is greater then 10MB', 'cause' => '', 'data' => json_decode("{}")));
        } else
            $response = '';
        return $response;
    }

    //verify font file
    public function verifyFontFile($file_array, $category_id, $is_featured, $is_catalog)
    {

        $file_type = $file_array->getMimeType();
        $file_size = $file_array->getSize();
        //Log::info("Font file : ", ['type' => $file_type, 'size' => $file_size]);

        $validations = $this->getValidationFromCache($category_id, $is_featured, $is_catalog);
        //Log::info('verifyFontFile : ', ['validations' => $validations]);

        $MAXIMUM_FILESIZE = $validations * 1024;
        //$MAXIMUM_FILESIZE = 1 * 1024 * 1024;

        /*Here special characters are restricted & only allow underscore & alphabetic values*/
        $fileData = pathinfo(basename($file_array->getClientOriginalName()));
        $file_name = str_replace(" ", "", strtolower($fileData['filename']));
        $string_array = str_split($file_name);
        foreach ($string_array as $key)
        {
            $is_valid = preg_match ('/[[:alpha:]_]+/', $key);
            if($is_valid == 0)
            {
                return Response::json(array('code' => 201, 'message' => 'Special characters (except underscore) & numeric value are not allowed into the file name.', 'cause' => '', 'data' => json_decode("{}")));
            }
        }

        /* there is no specific mimetype for otf & ttf so here we used 2 popular type */

        //if (!($file_type == 'application/x-font-ttf' || $file_type == 'application/vnd.ms-opentype'))
        //if (!($file_type == 'application/x-font-ttf' || $file_type == 'application/font-sfnt' || $file_type == 'application/vnd.ms-opentype' || $file_type == 'application/x-font-opentype'))
        //Galada_Regular.ttf file extension is font/sfnt mime in php 7.4 & application/font-sfnt in php <= 7.3 that's why we add one more condition.
        if (!($file_type == 'application/x-font-ttf' || $file_type == 'application/font-sfnt' || $file_type == 'font/sfnt' || $file_type == 'application/vnd.ms-opentype' || $file_type == 'application/x-font-opentype'))
            $response = Response::json(array('code' => 201, 'message' => 'Please select TTF or OTF file.', 'cause' => '', 'data' => json_decode("{}")));
        elseif ($file_size > $MAXIMUM_FILESIZE)
            $response = Response::json(array('code' => 201, 'message' => 'File size is greater then '.$validations.'KB.', 'cause' => '', 'data' => json_decode("{}")));
        else
            $response = '';
        return $response;
    }

    //verify image
    public function validateHeightWidthOfSampleImage($image_array, $json_data)
    {
        // Open image as a string
        $data = file_get_contents($image_array);

        // getimagesizefromstring function accepts image data as string & return file info
        $file_info = getimagesizefromstring($data);

        // Display the image content
        $width = $file_info[0];
        $height = $file_info[1];

        //Log::info('validateHeightWidthOfSampleImage height & width : ',['height_from_img' => $height, 'width_from_img' => $width, 'height_from_json' => $json_data->height, 'width_from_json' => $json_data->width]);

        if ($json_data->height == $height && $json_data->width == $width) {
            $response = '';
        } else {
            return $response = Response::json(array('code' => 201, 'message' => 'Height & width of the sample image doesn\'t match with height & width given in json.', 'cause' => '', 'data' => json_decode("{}")));
        }

        return $response;
    }

    public function validateAspectRatioOfSampleImage($image_array, $json_data)
    {
        // Open image as a string
        $data = file_get_contents($image_array);

        // getimagesizefromstring function accepts image data as string & return file info
        $file_info = getimagesizefromstring($data);

        // Display the image content
        $width = $file_info[0];
        $height = $file_info[1];
        $file_ratio = round($width / $height, 2);
        $json_data_ratio = round($json_data->width / $json_data->height, 2);

        //Log::info('validateHeightWidthOfSampleImage height & width : ',['height_from_img' => $height, 'width_from_img' => $width, 'height_from_json' => $json_data->height, 'width_from_json' => $json_data->width]);

        if ($file_ratio == $json_data_ratio) {
            $response = '';
        } else {
            return $response = Response::json(array('code' => 201, 'message' => 'Height & width of the sample image doesn\'t match with height & width given in json.', 'cause' => '', 'data' => json_decode("{}")));
        }

        return $response;
    }

    //validate fonts
    public function validateFonts($json_data)
    {
        $text_json = $json_data->text_json;
        $exist_count = 0;
        $mismatch_fonts = array();
        $incorrect_fonts = array();

        foreach ($text_json as $key) {
            $ios_font_name = $key->fontName;
            $android_font_name = $key->fontPath;

            $is_exist = DB::select('SELECT id FROM font_master WHERE BINARY ios_font_name = ? AND BINARY android_font_name = ?', [$ios_font_name, $android_font_name]);

            if (count($is_exist) == 0) {
                Log::info('validateFonts font not exist : ', ['query_result' => $is_exist, 'ios_font_name' => $ios_font_name, 'android_font_name' => $android_font_name]);

                $is_ios_font_name_exist = DB::select('SELECT id, ios_font_name, android_font_name FROM font_master WHERE ios_font_name = ?', [$ios_font_name]);
                $is_android_font_name_exist = DB::select('SELECT id, ios_font_name, android_font_name FROM font_master WHERE android_font_name = ?', [$android_font_name]);

                $is_correct_name = 0;
                $is_correct_path = 0;

                if (count($is_ios_font_name_exist) > 0) {
                    $is_correct_name = (strcmp($ios_font_name, $is_ios_font_name_exist[0]->ios_font_name) != 0) ? 0 : 1;
                }

                if (count($is_android_font_name_exist) > 0) {
                    $is_correct_path = (strcmp($android_font_name, $is_android_font_name_exist[0]->android_font_name) != 0) ? 0 : 1;

                }


                if (count($is_android_font_name_exist) > 0 && count($is_ios_font_name_exist) > 0 && $is_correct_name == 1 && $is_correct_path == 1) {


                    $mismatch_fonts[] = array(
                        'font_name' => $ios_font_name,
                        'font_path' => $android_font_name,
                        'correct_font_path' => $is_ios_font_name_exist[0]->android_font_name,
                        'correct_font_name' => $is_android_font_name_exist[0]->ios_font_name
                    );

                } elseif (count($is_android_font_name_exist) == 0 && count($is_ios_font_name_exist) == 0) {
                    $incorrect_fonts[] = array(
                        'font_name' => $ios_font_name,
                        'font_path' => $android_font_name,
                        'correct_font_path' => 'Font not available',
                        'correct_font_name' => 'Font not available',
                        'is_correct_path' => 0,
                        'is_correct_name' => 0
                    );
                } elseif (count($is_android_font_name_exist) > 0) {

                    $incorrect_fonts[] = array(
                        'font_name' => $ios_font_name,
                        'font_path' => $android_font_name,
                        'correct_font_path' => $is_android_font_name_exist[0]->android_font_name,
                        'correct_font_name' => $is_android_font_name_exist[0]->ios_font_name,
                        'is_correct_path' => (strcmp($android_font_name, $is_android_font_name_exist[0]->android_font_name) != 0) ? 0 : 1,
                        'is_correct_name' => (strcmp($ios_font_name, $is_android_font_name_exist[0]->ios_font_name) != 0) ? 0 : 1
                    );
                } elseif (count($is_ios_font_name_exist) > 0) {
                    $incorrect_fonts[] = array(
                        'font_name' => $ios_font_name,
                        'font_path' => $android_font_name,
                        'correct_font_path' => $is_ios_font_name_exist[0]->android_font_name,
                        'correct_font_name' => $is_ios_font_name_exist[0]->ios_font_name,
                        'is_correct_path' => (strcmp($android_font_name, $is_ios_font_name_exist[0]->android_font_name) != 0) ? 0 : 1,
                        'is_correct_name' => (strcmp($ios_font_name, $is_ios_font_name_exist[0]->ios_font_name) != 0) ? 0 : 1
                    );
                }

                $exist_count = $exist_count + 1;
            }

        }

        if ($exist_count > 0) {
            //$response = Response::json(array('code' => 201, 'message' => 'Fonts used by json does not exist in the server.', 'cause' => '', 'data' => json_decode("{}")));
            $response = Response::json(array('code' => 433, 'message' => 'Fonts used by json does not exist in the server.', 'cause' => '', 'data' => ['mismatch_fonts' => $mismatch_fonts, 'incorrect_fonts' => $incorrect_fonts]));
        } else {
            $response = '';
        }

        return $response;
    }

    //generate new name of image
    public function generateNewFileName($image_type, $image_array)
    {

        $fileData = pathinfo(basename($image_array->getClientOriginalName()));
        $new_file_name = uniqid() . '_' . $image_type . '_' . time() . '.' . $fileData['extension'];
        $path = '../..' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY') . $new_file_name;
        if (File::exists($path))
            $new_file_name = uniqid() . '_' . $image_type . '_' . time() . '.' . $fileData['extension'];
        return $new_file_name;
    }

    public function saveFileByPath($image_array, $img, $file_path, $dir_name)
    {
        try {
            $original_path = '../..' . $file_path;
            $image_array->move($original_path, $img);
            //$path = $original_path . $img;
            //$this->saveImageDetails($path, $dir_name);
        } catch (Exception $e) {
            Log::error("saveFileByPath : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
        }
    }

    //save original image
    public function saveOriginalImage($img)
    {
        try {
            $original_path = '../..' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY');
            Input::file('file')->move($original_path, $img);
            $path = $original_path . $img;
            $this->saveImageDetails($path, 'original');
        } catch (Exception $e) {
            Log::error("saveOriginalImage : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
        }
    }

    //save original webp image
    public function saveWebpOriginalImage($img)
    {
        try {
            $original_path = '../..' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY');
            $path = $original_path . $img;

            //convert image into .webp format
            $file_data = pathinfo(basename($path));
            $webp_name = $file_data['filename'];

            /*
                 *  -q Set image quality
                 *  -o Output file name
             */

            $webp_path = Config::get('constant.IMAGE_BUCKET_WEBP_ORIGINAL_IMG_PATH') . $webp_name . '.webp';
            $org_path = Config::get('constant.IMAGE_BUCKET_ORIGINAL_IMG_PATH') . $img;
            $quality = Config::get('constant.QUALITY');
            $libwebp = Config::get('constant.PATH_OF_CWEBP');

            $cmd = "$libwebp -q $quality $org_path -o $webp_path";

            if (Config::get('constant.APP_ENV') != 'local') {
                $result = (!shell_exec($cmd));
            } else {
                $result = (!exec($cmd));
            }

            return $webp_name . '.webp';
        } catch (Exception $e) {
            Log::error("saveWebpOriginalImage : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
        }
    }

    //save original image from array
    public function saveOriginalImageFromArray($image_array, $img)
    {
        $original_path = '../..' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY');
        $image_array->move($original_path, $img);
        $path = $original_path . $img;
        $this->saveImageDetails($path, 'original');
    }

    //save encoded image (not in use)
    public function saveEncodedImage($image_array, $professional_img)
    {
        $path = '../..' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY') . $professional_img;
        file_put_contents($path, $image_array);
    }

    //save compressed image
    public function saveCompressedImage($cover_img)
    {
        try {
            $original_path = '../..' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY') . $cover_img;
            $compressed_path = '../..' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY') . $cover_img;
            $img = Image::make($original_path);
            $img->save($compressed_path, 75);

            $original_img_size = filesize($original_path);
            $compress_img_size = filesize($compressed_path);


            //Log::info(["Original Img Size :"=>$original_img_size,"Compress Img Size :"=>$compress_img_size]);


            if ($compress_img_size >= $original_img_size) {
                //save original image in Compress image
                //Log::info("Compress Image Deleted.!");
                File::delete($compressed_path);
                File::copy($original_path, $compressed_path);
            }
            //use for Image Details
            $this->saveImageDetails($compressed_path, 'compress');


        } catch (Exception $e) {
            $dest1 = '../..' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY') . $cover_img;
            $dest2 = '../..' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY') . $cover_img;
            foreach ($_FILES['file'] as $check) {
                chmod($dest1, 0777);
                copy($dest1, $dest2);
                Log::error("saveCompressedImage : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);

            }

        }
    }

    //save template resources
    public function saveResourceImage($image_array)
    {
        $image = $image_array->getClientOriginalName();
        $resource_path = Config::get('constant.RESOURCE_IMAGES_DIRECTORY');
        $this->unlinkFileFromLocalStorage($image, $resource_path);
        $original_path = '../..' . $resource_path;
        $image_array->move($original_path, $image);

    }

    //save font file
    public function saveFontFile($file_name, $is_replace)
    {
        try {

            if ($is_replace == 0) {

                $source_file_path = '../..' . Config::get('constant.TEMP_FILE_DIRECTORY') . $file_name;
                $destination_file_path = '../..' . Config::get('constant.FONT_FILE_DIRECTORY') . $file_name;

                //move file from temp to fonts directory
                rename($source_file_path, $destination_file_path);
            } else {

                $destination_path = '../..' . Config::get('constant.FONT_FILE_DIRECTORY');
                Input::file('file')->move($destination_path, $file_name);

            }

            $font_name = (new VerificationController())->getFontName($file_name);
            return $font_name;

        } catch (Exception $e) {
            Log::error("saveFontFile : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);

        }

    }

    //get height-width of thumbnail image
    public function getThumbnailWidthHeight($file_name)
    {

        $original_path = '../..' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY') . $file_name;
        $image_size = getimagesize($original_path);
        $width_orig = $image_size[0];
        $height_orig = $image_size[1];
        $ratio_orig = $width_orig / $height_orig;

        $width = $width_orig < Config::get('constant.THUMBNAIL_WIDTH') ? $width_orig : Config::get('constant.THUMBNAIL_WIDTH');
        $height = $height_orig < Config::get('constant.THUMBNAIL_HEIGHT') ? $height_orig : Config::get('constant.THUMBNAIL_HEIGHT');

        if ($width / $height > $ratio_orig)
            $width = $height * $ratio_orig;
        else
            $height = $width / $ratio_orig;

        $array = array('width' => $width, 'height' => $height);
        return $array;
    }

    //get thumbnail height-width from s3
    public function getThumbnailWidthHeightForWebp($img)
    {

        $original_path = Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . $img;
        $image_size = getimagesize($original_path);
        $width_orig = $image_size[0];
        $height_orig = $image_size[1];
        $ratio_orig = $width_orig / $height_orig;

        $width = $width_orig < Config::get('constant.THUMBNAIL_WIDTH') ? $width_orig : Config::get('constant.THUMBNAIL_WIDTH');
        $height = $height_orig < Config::get('constant.THUMBNAIL_HEIGHT') ? $height_orig : Config::get('constant.THUMBNAIL_HEIGHT');

        if ($width / $height > $ratio_orig)
            $width = $height * $ratio_orig;
        else
            $height = $width / $ratio_orig;

        $array = array('width' => $width, 'height' => $height);
        return $array;
    }

    //save thumbnail image
    public function saveThumbnailImage($file_name)
    {
        try {
            $array = $this->getThumbnailWidthHeight($file_name);
            $width = $array['width'];
            $height = $array['height'];
            $original_path = '../..' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY') . $file_name;
            $thumbnail_path = '../..' . Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY') . $file_name;
            $img = Image::make($original_path)->resize($width, $height);
            $img->save($thumbnail_path);

            //use for Image Details
            $this->saveImageDetails($thumbnail_path, 'thumbnail');

        } catch (Exception $e) {
            Log::error("saveThumbnailImage : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $dest1 = '../..' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY') . $file_name;
            $dest2 = '../..' . Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY') . $file_name;
            foreach ($_FILES['file'] as $check) {
                chmod($dest1, 0777);
                copy($dest1, $dest2);
            }
            return "";
        }
    }

    //save webp thumbnail image
    public function saveWebpThumbnailImage($file_name)
    {
        try {
            $array = $this->getThumbnailWidthHeight($file_name);
            $width = $array['width'];
            $height = $array['height'];
            $original_path = '../..' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY') . $file_name;
            $thumbnail_path = '../..' . Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY') . $file_name;

            $file_data = pathinfo(basename($thumbnail_path));
            //convert image into .webp format
            $webp_name = $file_data['filename'];
            $image_size = getimagesize($original_path);
            $org_img_height = $image_size[1];
            $org_img_width = $image_size[0];
            $width_orig = ($image_size[0] * 50) / 100;
            $height_orig = ($image_size[1] * 50) / 100;


            /*
             *  -q Set image quality
             *  -o Output file name
             *  -resize  Resize the image
             */

            $webp_path = Config::get('constant.IMAGE_BUCKET_WEBP_THUMBNAIL_IMG_PATH') . $webp_name . '.webp';
            $org_path = Config::get('constant.IMAGE_BUCKET_ORIGINAL_IMG_PATH') . $file_name;
            $quality = Config::get('constant.QUALITY');
            $libwebp = Config::get('constant.PATH_OF_CWEBP');


            if ($width_orig < 200 or $height_orig < 200) {

                $cmd = "$libwebp -q $quality $org_path -resize $width $height -o $webp_path";
                if (Config::get('constant.APP_ENV') != 'local') {
                    //For Linux
                    $result = (!shell_exec($cmd));
                } else {
                    // For windows
                    $result = (!exec($cmd));
                }
                return array('height' => $height, 'width' => $width, 'org_img_height' => $org_img_height, 'org_img_width' => $org_img_width);
            } else {

                $cmd = "$libwebp -q $quality $org_path -resize $width_orig $height_orig -o $webp_path";
                if (Config::get('constant.APP_ENV') != 'local') {
                    //For Linux
                    $result = (!shell_exec($cmd));
                } else {
                    // For windows
                    $result = (!exec($cmd));
                }
                return array('height' => $height_orig, 'width' => $width_orig, 'org_img_height' => $org_img_height, 'org_img_width' => $org_img_width);
            }

        } catch (Exception $e) {
            Log::error("saveWebpThumbnailImage : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $dest1 = '../..' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY') . $file_name;
            $dest2 = '../..' . Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY') . $file_name;
            foreach ($_FILES['file'] as $check) {
                chmod($dest1, 0777);
                copy($dest1, $dest2);
            }
            return "";
        }
    }

    //save compressed and thumbnail image (not in use)
    public function saveCompressedThumbnailImage($source_url, $destination_url, $thumbnail_path)
    {

        $info = getimagesize($source_url);
        $width_orig = $info[0];
        $height_orig = $info[1];
        $ratio_orig = $width_orig / $height_orig;

        $width = $width_orig < Config::get('constant.THUMBNAIL_WIDTH') ? $width_orig : Config::get('constant.THUMBNAIL_WIDTH');
        $height = $height_orig < Config::get('constant.THUMBNAIL_HEIGHT') ? $height_orig : Config::get('constant.THUMBNAIL_HEIGHT');

        if ($width / $height > $ratio_orig)
            $width = $height * $ratio_orig;
        else
            $height = $width / $ratio_orig;

        if ($info['mime'] == 'image/jpeg') {
            // save compress image
            $image = imagecreatefromjpeg($source_url);
            imagejpeg($image, $destination_url, 75);

            // save thumbnail image
            $tmp_img = imagecreatetruecolor($width, $height);
            imagecopyresized($tmp_img, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);
            imagejpeg($tmp_img, $thumbnail_path);

        } elseif ($info['mime'] == 'image/png') {
            // save compress image
            $image = imagecreatefrompng($source_url);
            imagepng($image, $destination_url, 9);

            // save thumbnail image
            $tmp_img = imagecreatetruecolor($width, $height);
            imagealphablending($tmp_img, false);
            imagesavealpha($tmp_img, true);
            $transparent = imagecolorallocatealpha($tmp_img, 255, 255, 255, 127);
            imagefilledrectangle($tmp_img, 0, 0, $width_orig, $height_orig, $transparent);
            imagecopyresized($tmp_img, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);
            imagepng($tmp_img, $thumbnail_path);
        }
    }

    //check compress image >= original image ? save original image : compress (not in use)
    public function CompressImageCheck($img_type, $image_array)
    {
        try {
            //Create Image Name
            $img_name = $this->generateNewFileName($img_type, $image_array);

            //create Original Image Path
            $original_path = '../..' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY');

            //Create Compress Image Path
            $compress_path = '../..' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY');

            //get Original Image Size
            $original_img_size = filesize($image_array);
            //Log::info(["Original Img Size"=>$original_img_size]);

            //Move Image in User decide Folder(Save)
            Input::file('file')->move($original_path, $img_name);

            //Compressed Image
            $img = Image::make($original_path . $img_name);
            $img->save($compress_path . $img_name, 75);
            $compress_img_size = filesize($compress_path . $img_name);
            //Log::info(["Original Img Size :"=>$original_img_size,"Compress Img Size :"=>$compress_img_size]);
            //compress image size >= original image size
            if ($compress_img_size >= $original_img_size) {
                //save original image in Compress image
                //Log::info("Compress Image Deleted.!");
                File::delete($compress_path . $img_name);
                File::copy($original_path . $img_name, $compress_path . $img_name);
            }
        } catch (Exception $e) {
            Log::error("CompressImageCheck : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
        }

    }

    // Delete json data From S3
    public function deleteCDNCache($files_array)
    {
        try {

            //verify credentials
            $cloudFrontClient = CloudFrontClient::factory(array(
                'version'=> 'latest',
                'region' => 'us-east-1',
                'credentials' => array(
                    'key' => Config::get('constant.AWS_KEY'),
                    'secret'  => Config::get('constant.AWS_SECRET'),
                )
            ));

           //create new invalidation for delete cache
            $result = $cloudFrontClient->createInvalidation([
                'DistributionId' => Config::get('constant.CDN_DISTRIBUTION_ID'),
                'InvalidationBatch' => [
                    'CallerReference' => time(),
                    'Paths' => [
                        'Items' => $files_array,
                        'Quantity' => count($files_array),
                    ],
                ]
            ]);

        } catch (Exception $e) {
            Log::error("deleteCDNCache Exception: ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $result = "";
        } catch (AwsException $e) {
            Log::error("deleteCDNCache AwsException: ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $result = "";
        }
        return $result;
    }

    // Delete json data From S3
    public function deleteResourceImages($file_name)
    {
        try {
            if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
                $this->deleteObjectFromS3($file_name, 'resource');
            } else {
                $this->unlinkFileFromLocalStorage($file_name, Config::get('constant.RESOURCE_IMAGES_DIRECTORY'));
            }
        } catch (Exception $e) {
            Log::error("deleteResourceImages : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
        }
    }

    //delete images from directory
    public function deleteImage($image_name)
    {
        try {

            if (Config::get('constant.STORAGE') === 'S3_BUCKET') {

                $this->deleteObjectFromS3($image_name, 'original');
                $this->deleteObjectFromS3($image_name, 'compressed');
                $this->deleteObjectFromS3($image_name, 'thumbnail');

            } else {

                $this->unlinkFileFromLocalStorage($image_name, Config::get('constant.ORIGINAL_IMAGES_DIRECTORY'));
                $this->unlinkFileFromLocalStorage($image_name, Config::get('constant.COMPRESSED_IMAGES_DIRECTORY'));
                $this->unlinkFileFromLocalStorage($image_name, Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY'));

            }

            /* Image Details delete */
            DB::beginTransaction();
            DB::delete('DELETE
                        FROM
                          image_details
                        WHERE
                          name = ?', [$image_name]);
            DB::commit();

        } catch (Exception $e) {
            Log::error("deleteImage : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            DB::rollBack();
            return Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . ' delete image.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
    }

    public function deleteFileByPath($image_name, $local_path, $s3_path)
    {
        try {

            if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
                $this->deleteObjectFromS3($image_name, $s3_path);

            } else {
                $this->unlinkFileFromLocalStorage($image_name, $local_path);

            }

            /* Image Details delete */
            DB::beginTransaction();
            DB::delete('DELETE
                        FROM
                          image_details
                        WHERE
                          name = ?', [$image_name]);
            DB::commit();

        } catch (Exception $e) {
            Log::error("deleteFileByPath : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            DB::rollBack();
            return Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . ' delete image.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
    }

    //Delete Webp Images In Directory
    public function deleteWebpImage($image_name)
    {
        try {

            if (Config::get('constant.STORAGE') === 'S3_BUCKET') {

                $this->deleteObjectFromS3($image_name, 'webp_original');
                $this->deleteObjectFromS3($image_name, 'webp_thumbnail');

            } else {

                $this->unlinkFileFromLocalStorage($image_name, Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY'));
                $this->unlinkFileFromLocalStorage($image_name, Config::get('constant.WEBP_THUMBNAIL_IMAGES_DIRECTORY'));

            }


        } catch (Exception $e) {
            Log::error("deleteWebpImage : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            return Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'delete webp image.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
    }

    //image analysis
    public function saveImageDetails($image_path, $image_directory)
    {
        try {
            if (!Config::get('constant.IS_IMAGE_DETAILS_ANALYTICS_ENABLE')) {
                return 0;
            }

            //Log::info('file details:',pathinfo($image_path));
            $file_info = pathinfo($image_path);
            $name = $file_info['basename'];
            $type = $file_info['extension'];
            $width = Image::make($image_path)->width();
            $height = Image::make($image_path)->height();
            $size = filesize($image_path);
            //$size = $bytes/(1024 * 1024);
            //Log::info('file details:',[$name,$type,$height,$width,$size]);
            $pixel = 0;
            $create_at = date('Y-m-d H:i:s');
            DB::beginTransaction();

            DB::insert('INSERT INTO image_details
                            (name,directory_name,type,size,height,width,pixel,created_at)
                            values (?,?,?,?,?,?,?,?)',
                [$name, $image_directory, $type, $size, $height, $width, $pixel, $create_at]);

            DB::commit();
        } catch (Exception $e) {
            Log::error("saveImageDetails : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            DB::rollBack();
            return Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'save image details.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
    }

    //to save original image using dynamic request parameter
    public function saveMultipleOriginalImage($img, $file_name)
    {

        $original_path = '../..' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY');
        Input::file($file_name)->move($original_path, $img);

        //use for Image Details
        $path = $original_path . $img;
        $this->saveImageDetails($path, 'original');
    }

    //to save compressed image using dynamic request parameter
    public function saveMultipleCompressedImage($img, $file_name)
    {
        try {
            $original_path = '../..' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY') . $img;
            $compressed_path = '../..' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY') . $img;
            $image = Image::make($original_path);
            $image->save($compressed_path, 75);

            $original_img_size = filesize($original_path);
            $compress_img_size = filesize($compressed_path);
            //Log::info(["Original Img Size :"=>$original_img_size,"Compress Img Size :"=>$compress_img_size]);
            if ($compress_img_size >= $original_img_size) {
                //save original image in Compress image
                //Log::info("Compress Image Deleted.!");
                File::delete($compressed_path);
                File::copy($original_path, $compressed_path);
            }
            //use for Image Details
            $this->saveImageDetails($compressed_path, 'compress');

        } catch (Exception $e) {
            $dest1 = '../..' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY') . $img;
            $dest2 = '../..' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY') . $img;
            foreach ($_FILES[$file_name] as $check) {
                chmod($dest1, 0777);
                copy($dest1, $dest2);
                Log::error("saveMultipleCompressedImage : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            }
        }

    }

    //to save thumbnail image using dynamic request parameter
    public function saveMultipleThumbnailImage($img, $file_name)
    {
        try {
            $array = $this->getThumbnailWidthHeight($img);
            $width = $array['width'];
            $height = $array['height'];
            $original_path = '../..' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY') . $img;
            $thumbnail_path = '../..' . Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY') . $img;
            $image = Image::make($original_path)->resize($width, $height);
            $image->save($thumbnail_path);

            //use for Image Details
            $this->saveImageDetails($thumbnail_path, 'thumbnail');

        } catch (Exception $e) {
            $dest1 = '../..' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY') . $img;
            $dest2 = '../..' . Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY') . $img;
            foreach ($_FILES[$file_name] as $check) {
                chmod($dest1, 0777);
                copy($dest1, $dest2);
            }
        }
    }

    //unlink images from local server(image_bucket)
    public function unlinkFile($image)
    {
        try {

            $original_image_path = '../..' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY') . $image;

            if (($is_exist = ($this->checkFileExist($original_image_path)) != 0)) {
                unlink($original_image_path);
            }

            $compressed_image_path = '../..' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY') . $image;

            if (($is_exist = ($this->checkFileExist($compressed_image_path)) != 0)) {
                unlink($compressed_image_path);
            }

            $thumbnail_image_path = '../..' . Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY') . $image;

            if (($is_exist = ($this->checkFileExist($thumbnail_image_path)) != 0)) {
                unlink($thumbnail_image_path);
            }


        } catch (Exception $e) {
            Log::error("unlinkFile : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);

        }


    }

    public function saveFileInToS3ByPath($image_name, $image_path, $s3_path)
    {
        try {
            $original_sourceFile = '../..' . $image_path . $image_name;

            $disk = Storage::disk('s3');
            if (($is_exist = ($this->checkFileExist($original_sourceFile)) != 0)) {
                $original_targetFile = "imageflyer/$s3_path/$image_name";
                $disk->put($original_targetFile, file_get_contents($original_sourceFile), 'public');

                //unlink file from local storage
                unlink($original_sourceFile);
            }

        } catch (Exception $e) {
            Log::error("saveFileInToS3ByPath : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
        }
    }

    //save image into S3
    public function saveImageInToS3($image)
    {
        try {

            $original_sourceFile = '../..' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY') . $image;
            $compressed_sourceFile = '../..' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY') . $image;
            $thumbnail_sourceFile = '../..' . Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY') . $image;

            $disk = Storage::disk('s3');
            if (($is_exist = ($this->checkFileExist($original_sourceFile)) != 0)) {
                $original_targetFile = "imageflyer/original/" . $image;
                $disk->put($original_targetFile, file_get_contents($original_sourceFile), 'public');

                //unlink file from local storage
                unlink($original_sourceFile);
            }

            if (($is_exist = ($this->checkFileExist($compressed_sourceFile)) != 0)) {
                $compressed_targetFile = "imageflyer/compressed/" . $image;
                $disk->put($compressed_targetFile, file_get_contents($compressed_sourceFile), 'public');

                //unlink file from local storage
                unlink($compressed_sourceFile);
            }

            if (($is_exist = ($this->checkFileExist($thumbnail_sourceFile)) != 0)) {
                $thumbnail_targetFile = "imageflyer/thumbnail/" . $image;
                $disk->put($thumbnail_targetFile, file_get_contents($thumbnail_sourceFile), 'public');

                //unlink file from local storage
                unlink($thumbnail_sourceFile);
            }

            /*$this->unlinkFileFromLocalStorage($image, $original_directory);
            $this->unlinkFileFromLocalStorage($image, $compressed_directory);
            $this->unlinkFileFromLocalStorage($image, $thumbnail_directory);*/


        } catch (Exception $e) {
            Log::error("saveImageInToS3 : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);

        }


    }

    //save font into S3
    public function saveFontInToS3($file)
    {
        try {
            $original_sourceFile = '../..' . Config::get('constant.FONT_FILE_DIRECTORY') . $file;
            $disk = Storage::disk('s3');
            $this->deleteObjectFromS3($file, 'fonts');

            if (($is_exist = ($this->checkFileExist($original_sourceFile)) != 0)) {

                $original_targetFile = "imageflyer/fonts/" . $file;
                $disk->put($original_targetFile, file_get_contents($original_sourceFile), 'public');

                unlink($original_sourceFile);

            }

        } catch (Exception $e) {
            Log::error("saveFontInToS3 : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);

        }


    }

    //save resource image into S3
    public function saveResourceImageInToS3($image)
    {
        try {

            $resource_dir = Config::get('constant.RESOURCE_IMAGES_DIRECTORY');
            $resource_sourceFile = '../..' . $resource_dir . $image;

            $disk = Storage::disk('s3');
            $this->deleteObjectFromS3($image, 'resource');

            if (($is_exist = ($this->checkFileExist($resource_sourceFile)) != 0)) {
                $resource_targetFile = "imageflyer/resource/" . $image;
                $disk->put($resource_targetFile, file_get_contents($resource_sourceFile), 'public');

                //delete file from local storage
                unlink($resource_sourceFile);
            }

        } catch (Exception $e) {
            Log::error("saveResourceImageInToS3 : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
        }
    }

    //save image into S3 for migration
    public function saveImageInToS3ForMigration($image)
    {
        try {
            //$base_url = (new ImageController())->getBaseUrl();
            $base_url = 'http://138.197.11.186/ob_photolab_backend';


            $original_sourceFile = $base_url . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY') . $image;
            $compressed_sourceFile = $base_url . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY') . $image;
            $thumbnail_sourceFile = $base_url . Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY') . $image;


            //return array($original_sourceFile,$compressed_sourceFile, $thumbnail_sourceFile);

            $disk = Storage::disk('spaces');
            if (fopen($original_sourceFile, "r")) {

                $original_targetFile = "photoeditorlab/original/" . $image;
                $disk->put($original_targetFile, file_get_contents($original_sourceFile), 'public');

            }

            if (fopen($compressed_sourceFile, "r")) {

                $compressed_targetFile = "photoeditorlab/compressed/" . $image;
                $disk->put($compressed_targetFile, file_get_contents($compressed_sourceFile), 'public');

            }

            if (fopen($thumbnail_sourceFile, "r")) {

                $thumbnail_targetFile = "photoeditorlab/thumbnail/" . $image;
                $disk->put($thumbnail_targetFile, file_get_contents($thumbnail_sourceFile), 'public');

            }

            (new ImageController())->unlinkFile($image);
        } catch (Exception $e) {
            Log::error("saveImageInToS3ForMigration : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);

        }


    }

    //save webp image to generate webp on call of update API
    public function saveWebpImage($img)
    {

        $original_path = Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN');
        $path = $original_path . $img;
        //$original_img_size = filesize($path);
        //$headers = get_headers($path, true);
        //$original_img_size = $headers['Content-Length'];
        $array = $this->getThumbnailWidthHeightForWebp($img);
        $width = $array['width'];
        $height = $array['height'];

        //convert image into .webp format
        $file_data = pathinfo(basename($path));
        $webp_name = $file_data['filename'];
        $image_size = getimagesize($path);
        $width_orig = round(($image_size[0] * 50) / 100);
        $height_orig = round(($image_size[1] * 50) / 100);

        /*
        *  -q Set image quality
        *  -o Output file name
        *  -resize  Resize the image
        */

        $webp_path = Config::get('constant.IMAGE_BUCKET_WEBP_ORIGINAL_IMG_PATH') . $webp_name . '.webp';
        $org_path = Config::get('constant.IMAGE_BUCKET_ORIGINAL_IMG_PATH') . $img;
        //$org_path = $path;
        $quality = Config::get('constant.QUALITY');
        $libwebp = Config::get('constant.PATH_OF_CWEBP');
        $cmd = "$libwebp -q $quality $org_path -o $webp_path";

        //Log::info($cmd);
        if (Config::get('constant.APP_ENV') != 'local') {
            $result = (!shell_exec($cmd));
        } else {
            $result = (!exec($cmd));
        }

        //$base_url = (new ImageController())->getBaseUrl();
        //$webp_org_path = $base_url . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY') . $webp_name . '.webp';
        //$headers = get_headers($webp_org_path, true);
        //$webp_img_size = $headers['Content-Length'];


        /* save webp_thumbnail */
        $webp_thumb_path = Config::get('constant.IMAGE_BUCKET_WEBP_THUMBNAIL_IMG_PATH') . $webp_name . '.webp';
        $org_path = Config::get('constant.IMAGE_BUCKET_ORIGINAL_IMG_PATH') . $img;//$path;
        $quality = Config::get('constant.QUALITY');
        $libwebp = Config::get('constant.PATH_OF_CWEBP');


        if ($width_orig < 200 or $height_orig < 200) {

            $cmd = "$libwebp -q $quality $org_path -resize $width $height -o $webp_thumb_path";
            if (Config::get('constant.APP_ENV') != 'local') {
                //For Linux
                $result = (!shell_exec($cmd));
            } else {
                // For windows
                $result = (!exec($cmd));
            }

            /*if ($webp_img_size > $original_img_size) {

                $webp_org_path = '../..' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY') . $webp_name . '.webp';
                unlink($webp_org_path);
                $webp_org_path = '../..' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY') . $img;
                File::copy($path, $webp_org_path);
                return array('height' => $height, 'width' => $width, 'filename' => $img);
            } else {
                return array('height' => $height, 'width' => $width, 'filename' => $webp_name . '.webp');

            }*/
            return array('height' => $height, 'width' => $width, 'filename' => $webp_name . '.webp');

        } else {

            $cmd = "$libwebp -q $quality $org_path -resize $width_orig $height_orig -o $webp_thumb_path";

            if (Config::get('constant.APP_ENV') != 'local') {
                //For Linux
                $result = (!shell_exec($cmd));
            } else {
                // For windows
                $result = (!exec($cmd));
            }

            /*if ($webp_img_size > $original_img_size) {

                $webp_org_path = '../..' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY') . $webp_name . '.webp';
                unlink($webp_org_path);
                $webp_org_path = '../..' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY') . $img;
                File::copy($path, $webp_org_path);
                return array('height' => $height_orig, 'width' => $width_orig, 'filename' => $img);
            } else {
                return array('height' => $height_orig, 'width' => $width_orig, 'filename' => $webp_name . '.webp');

            }*/
            return array('height' => $height_orig, 'width' => $width_orig, 'filename' => $webp_name . '.webp');
        }
    }

    //save webp_thumbnail image from S3
    public function saveWebpThumbnailImageFromS3($img)
    {
        try {
            $original_path = Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN');
            $path = $original_path . $img;
            //$original_img_size = filesize($path);

            //convert image into .webp format
            $file_data = pathinfo(basename($path));
            $webp_name = $file_data['filename'];
            $webp_thumbnail_dir = Config::get('constant.WEBP_THUMBNAIL_IMAGES_DIRECTORY');

            $this->unlinkFileFromLocalStorage($webp_name, $webp_thumbnail_dir);

            $image_size = getimagesize($path);
            $width_orig = round(($image_size[0] * 50) / 100);
            $height_orig = round(($image_size[1] * 50) / 100);

            $array = $this->getThumbnailWidthHeightForWebp($img);
            $width = $array['width'];
            $height = $array['height'];


            /*
           *  -q Set image quality
           *  -o Output file name
           *  -resize  Resize the image
           */

            $webp_path = Config::get('constant.IMAGE_BUCKET_WEBP_THUMBNAIL_IMG_PATH') . $webp_name . '.webp';
            $org_path = Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . $img;
            $quality = Config::get('constant.QUALITY');
            $libwebp = Config::get('constant.PATH_OF_CWEBP');

            if ($width_orig < 200 or $height_orig < 200) {

                $cmd = "$libwebp -q $quality $org_path -resize $width $height -o $webp_path";
                //Log::info('cmd 1: ',['cmd' => $cmd]);
                if (Config::get('constant.APP_ENV') != 'local') {
                    //For Linux
                    $result = (!shell_exec($cmd));
                } else {
                    // For windows
                    $result = (!exec($cmd));
                }
                return array('height' => $height, 'width' => $width);


            } else {

                $cmd = "$libwebp -q $quality $org_path -resize $width_orig $height_orig -o $webp_path";
                //Log::info('cmd 2: ',['cmd' => $cmd]);
                if (Config::get('constant.APP_ENV') != 'local') {
                    //For Linux
                    $result = (!shell_exec($cmd));
                } else {
                    // For windows
                    $result = (!exec($cmd));
                }
                return array('height' => $height_orig, 'width' => $width_orig);
            }

        } catch (Exception $e) {

            Log::error("saveWebpThumbnailImageFromS3 : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $image_size = getimagesize($path);
            $width_orig = round(($image_size[0] * 50) / 100);
            $height_orig = round(($image_size[1] * 50) / 100);

            $array = $this->getThumbnailWidthHeightForWebp($img);
            $width = $array['width'];
            $height = $array['height'];


            /*
          *  -q Set image quality
          *  -o Output file name
          *  -resize  Resize the image
          */

            $webp_path = Config::get('constant.IMAGE_BUCKET_WEBP_THUMBNAIL_IMG_PATH') . $webp_name . '.webp';
            $org_path = Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . $img;
            $quality = Config::get('constant.QUALITY');
            $libwebp = Config::get('constant.PATH_OF_CWEBP');


            if ($width_orig < 200 or $height_orig < 200) {

                $cmd = "$libwebp -q $quality $org_path -resize $width $height -o $webp_path";
                if (Config::get('constant.APP_ENV') != 'local') {
                    //For Linux
                    $result = (!shell_exec($cmd));
                } else {
                    // For windows
                    $result = (!exec($cmd));
                }
                return array('height' => $height, 'width' => $width);
            } else {

                $cmd = "$libwebp -q $quality $org_path -resize $width_orig $height_orig -o $webp_path";
                if (Config::get('constant.APP_ENV') != 'local') {
                    //For Linux
                    $result = (!shell_exec($cmd));
                } else {
                    // For windows
                    $result = (!exec($cmd));
                }
                return array('height' => $height_orig, 'width' => $width_orig);
            }

        }
    }

    //save webp image into S3
    public function saveWebpImageInToS3($image)
    {
        try {

            /*$webp_original_dir = Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY');
            $webp_thumbnail_dir = Config::get('constant.WEBP_THUMBNAIL_IMAGES_DIRECTORY');*/

            $original_sourceFile = '../..' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY') . $image;
            $thumbnail_sourceFile = '../..' . Config::get('constant.WEBP_THUMBNAIL_IMAGES_DIRECTORY') . $image;

            $disk = Storage::disk('s3');
            if (($is_exist = ($this->checkFileExist($original_sourceFile)) != 0)) {

                $original_targetFile = "imageflyer/webp_original/" . $image;
                $disk->put($original_targetFile, file_get_contents($original_sourceFile), 'public');

                //delete file from local storage
                unlink($original_sourceFile);

            }
            if (($is_exist = ($this->checkFileExist($thumbnail_sourceFile)) != 0)) {

                $thumbnail_targetFile = "imageflyer/webp_thumbnail/" . $image;
                $disk->put($thumbnail_targetFile, file_get_contents($thumbnail_sourceFile), 'public');

                //delete file from local storage
                unlink($thumbnail_sourceFile);

            }


        } catch (Exception $e) {
            Log::error("saveWebpImageInToS3 : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);

        }
    }

    //save webp_thumbnail image into S3
    public function saveWebpThumbnailImageInToS3($image)
    {
        try {

            $base_url = (new Utils())->getBaseUrl();
            $webp_thumbnail_dir = Config::get('constant.WEBP_THUMBNAIL_IMAGES_DIRECTORY');

            $thumbnail_sourceFile = $base_url . $webp_thumbnail_dir . $image;

            $disk = Storage::disk('s3');
            if (($is_exist = ($this->checkFileExist($thumbnail_sourceFile)) != 0)) {

                $thumbnail_targetFile = "imageflyer/webp_thumbnail/" . $image;
                $disk->put($thumbnail_targetFile, file_get_contents($thumbnail_sourceFile), 'public');

                //delete file from local storage
                unlink($thumbnail_sourceFile);
            }


        } catch (Exception $e) {
            Log::error("saveWebpThumbnailImageInToS3 : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);

        }
    }

    //save original image from S3
    public function saveOriginalImageFromToS3($image)
    {
        try {

            $original_img_of_s3 = Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . $image;

            $original_path = '../..' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY');
            $path = $original_path . $image;
            try {

                if (($is_exist = ($this->checkFileExist($path)) != 0)) {
                    if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
                        unlink($path);
                    }

                }

            } catch (Exception $e) {
                Log::Debug("saveOriginalImageFromToS3 is_exist? : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            }

            if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
                copy($original_img_of_s3, $path);
            }


            $this->saveImageDetails($path, 'original');
            //$original_img_size = filesize($path);

            //convert image into .webp format
            $file_data = pathinfo(basename($path));
            $webp_name = $file_data['filename'];

            /*
                 *  -q Set image quality
                 *  -o Output file name
             */

            $webp_path = Config::get('constant.IMAGE_BUCKET_WEBP_ORIGINAL_NEW_IMG_PATH') . $webp_name . '.webp';
            $org_path = Config::get('constant.IMAGE_BUCKET_ORIGINAL_IMG_PATH') . $image;
            $quality = Config::get('constant.QUALITY');
            $libwebp = Config::get('constant.PATH_OF_CWEBP');

            $cmd = "$libwebp -q $quality $org_path -o $webp_path";
            //Log::info('webp command : ',['command' => $cmd]);

            if (Config::get('constant.APP_ENV') != 'local') {
                $result = (!shell_exec($cmd));
            } else {
                $result = (!exec($cmd));
            }

            //Log::info('webp result : ',['return' => $result]);

            /*$base_url = (new ImageController())->getBaseUrl();

            $webp_org_path = $base_url . Config::get('constant.WEBP_ORIGINAL_NEW_IMAGES_DIRECTORY') . $webp_name . '.webp';

            $headers = get_headers($webp_org_path, true);
            $webp_img_size = $headers['Content-Length'];

            if ($webp_img_size > $original_img_size) {

                $webp_org_path = '../..' . Config::get('constant.WEBP_ORIGINAL_NEW_IMAGES_DIRECTORY'). $webp_name . '.webp';
                unlink($webp_org_path);
                $webp_org_path = '../..' . Config::get('constant.WEBP_ORIGINAL_NEW_IMAGES_DIRECTORY') . $image;
                File::copy($path, $webp_org_path);
                return $image;

            } else {
                return $webp_name . '.webp';

            }*/
            return $webp_name . '.webp';

        } catch (Exception $e) {

            Log::error("saveOriginalImageFromToS3 : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            return "";

        }


    }

    //check image is exist into resource directory
    public function checkIsImageExist($image_array, $is_name)
    {
        try {

            $exist_files_array = array();
            foreach ($image_array as $key) {

                if ($is_name) {
                    $image = $key;
                } else {
                    $image = $key->getClientOriginalName();
                }
                $image_url = Config::get('constant.RESOURCE_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . $image;

                if (Config::get('constant.STORAGE') === 'S3_BUCKET') {

                    $disk = Storage::disk('s3');
                    $value = "imageflyer/resource/" . $image;
                    if ($disk->exists($value)) {

                        $exist_files_array[] = array('url' => $image_url, 'name' => $image);
                    }
                } else {
                    $image_path = '../..' . Config::get('constant.RESOURCE_IMAGES_DIRECTORY') . $image;
                    if (($is_exist = ($this->checkFileExist($image_path)) != 0)) {
                        $exist_files_array[] = array('url' => $image_url, 'name' => $image);
                    }
                }
            }
            if (sizeof($exist_files_array) > 0) {
                $array = array('existing_files' => $exist_files_array);

                $result = json_decode(json_encode($array), true);
                return $response = Response::json(array('code' => 420, 'message' => 'File already exists.', 'cause' => '', 'data' => $result));
            } else {
                return $response = '';
            }
        } catch (Exception $e) {
            Log::error("checkIsImageExist : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            return $response = '';
        }
    }

    // Check font is exist into font directory
    public function removeFontIfIsExist($file_name)
    {
        try {
            $file_url = Config::get('constant.FONT_FILE_DIRECTORY_OF_DIGITAL_OCEAN') . $file_name;
            if (Config::get('constant.APP_ENV') != 'local') {

                $aws_bucket = Config::get('constant.AWS_BUCKET');
                $disk = Storage::disk('s3');
                $value = "$aws_bucket/fonts/" . $file_name;
                if ($disk->exists($value)) {
                    $this->deleteObjectFromS3($file_name , 'fonts');
                }
            } else {
                $file_path = '../..' . Config::get('constant.FONT_FILE_DIRECTORY') . $file_name;
                if (($is_exist = ($this->checkFileExist($file_path)) == 1)) {
                    $this->unlinkFileFromLocalStorage($file_name, Config::get('constant.FONT_FILE_DIRECTORY'));
                }
            }
            return $response = 1;
        } catch (Exception $e) {
            Log::error("removeFontIfIsExist : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            return $response = 0;
        }
    }

    //save thumbnail image into webp_thumbnail_new
    public function saveThumbnailImageFromS3($file_name)
    {
        try {
            $array = $this->getThumbnailWidthHeight($file_name);
            $width = $array['width'];
            $height = $array['height'];
            $original_path = '../..' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY') . $file_name;
            $thumbnail_path = '../..' . Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY') . $file_name;

            if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
                $img = Image::make($original_path)->resize($width, $height);
                $img->save($thumbnail_path);

                //use for Image Details
                $this->saveImageDetails($thumbnail_path, 'thumbnail');
            }

            $file_data = pathinfo(basename($thumbnail_path));
            //convert image into .webp format
            $webp_name = $file_data['filename'];
            $image_size = getimagesize($original_path);
            $width_orig = ($image_size[0] * 50) / 100;
            $height_orig = ($image_size[1] * 50) / 100;


            /*
             *  -q Set image quality
             *  -o Output file name
             *  -resize  Resize the image
             */

            $webp_path = Config::get('constant.IMAGE_BUCKET_WEBP_THUMBNAIL_NEW_IMG_PATH') . $webp_name . '.webp';
            $org_path = Config::get('constant.IMAGE_BUCKET_ORIGINAL_IMG_PATH') . $file_name;
            $quality = Config::get('constant.QUALITY');
            $libwebp = Config::get('constant.PATH_OF_CWEBP');


            if ($width_orig < 200 or $height_orig < 200) {

                $cmd = "$libwebp -q $quality $org_path -resize $width $height -o $webp_path";
                //Log::info('webp thumbnail command : ',['command' => $cmd]);
                if (Config::get('constant.APP_ENV') != 'local') {
                    //For Linux
                    $result = (!shell_exec($cmd));
                } else {
                    // For windows
                    $result = (!exec($cmd));
                }
                //Log::info('webp thumbnail command result : ',['return' => $result]);
                return array('height' => $height, 'width' => $width);
            } else {

                $cmd = "$libwebp -q $quality $org_path -resize $width_orig $height_orig -o $webp_path";
                //Log::info('webp thumbnail command (aspect ratio) : ',['command' => $cmd]);
                if (Config::get('constant.APP_ENV') != 'local') {
                    //For Linux
                    $result = (!shell_exec($cmd));
                } else {
                    // For windows
                    $result = (!exec($cmd));
                }
                //Log::info('webp thumbnail command result (aspect ratio) : ',['return' => $result]);
                return array('height' => $height_orig, 'width' => $width_orig);
            }

        } catch (Exception $e) {

            Log::error("saveThumbnailImageFromS3 : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            return "";
        }
    }

    //save new_webp image into S3
    public function saveNewWebpImageInToS3($image)
    {
        try {
            $base_url = (new Utils())->getBaseUrl();

            $original_sourceFile = $base_url . Config::get('constant.WEBP_ORIGINAL_NEW_IMAGES_DIRECTORY') . $image;
            $thumbnail_sourceFile = $base_url . Config::get('constant.WEBP_THUMBNAIL_NEW_IMAGES_DIRECTORY') . $image;
            $original_image_path = '../..' . Config::get('constant.WEBP_ORIGINAL_NEW_IMAGES_DIRECTORY') . $image;
            $thumbnail_image_path = '../..' . Config::get('constant.WEBP_THUMBNAIL_NEW_IMAGES_DIRECTORY') . $image;
            //return array($original_sourceFile,$compressed_sourceFile, $thumbnail_sourceFile);

            $disk = Storage::disk('s3');

            if (fopen($original_sourceFile, "r")) {

                $original_targetFile = "imageflyer/webp_original_new/" . $image;
                $disk->put($original_targetFile, file_get_contents($original_sourceFile), 'public');


                if (($is_exist = ($this->checkFileExist($original_image_path)) != 0)) {
                    //File::delete($image_path);
                    //Log::info('s3');
                    unlink($original_image_path);
                }


            }

            if (fopen($thumbnail_sourceFile, "r")) {

                $thumbnail_targetFile = "imageflyer/webp_thumbnail_new/" . $image;
                $disk->put($thumbnail_targetFile, file_get_contents($thumbnail_sourceFile), 'public');

                if (($is_exist = ($this->checkFileExist($thumbnail_image_path)) != 0)) {
                    //File::delete($image_path);
                    unlink($thumbnail_image_path);
                }

            }

            //(new ImageController())->unlinkfile($image);

        } catch (Exception $e) {
            Log::error("saveNewWebpImageInToS3 : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);

        }
    }

    //unlink file from local storage
    public function unlinkFileFromLocalStorage($file, $path)
    {
        try {

            $original_image_path = '../..' . $path . $file;

            if (($is_exist = ($this->checkFileExist($original_image_path)) != 0)) {
                unlink($original_image_path);
            }

        } catch (Exception $e) {
            Log::debug("unlinkFileFromLocalStorage : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);

        }
    }

    //For check file exist in s3 bucket
    public function checkFileExistInS3($folder_name,$file_name)
    {
        try {
            $aws_bucket = Config::get('constant.AWS_BUCKET');
            $disk = Storage::disk('s3');
            $value = "$aws_bucket/$folder_name/$file_name";
            if ($disk->exists($value)) {
                $response = 1;
            }else {
                $response = 0;
            }
        } catch (Exception $e) {
            $response = 0;
            Log::debug("checkFileExistInS3 : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
        }
        return $response;
    }

    //check file is exist
    public function checkFileExist($file_path)
    {
        try {
            //if (fopen($original_sourceFile, "r")) {
            if (File::exists($file_path)) {
                //Log::info('file exist : ',['path' => $file_path]);
                $response = 1;
            } else {
                $response = 0;
                //Log::info('file does not exist : ', ['path' => $file_path]);
            }

        } catch (Exception $e) {
            $response = 0;
            Log::debug("checkFileExist : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
        }
        return $response;
    }

    //delete object from S3
    public function deleteObjectFromS3($file, $directory)
    {
        try {

            $disk = Storage::disk('s3');
            $original = "imageflyer/$directory/" . $file;
            if ($disk->exists($original)) {
                $disk->delete($original);
            }

        } catch (Exception $e) {
            Log::error("deleteObjectFromS3 : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);

        }
    }

    //get validations from settings_master (from database)
    public function getValidationFromCache($category_id, $is_featured, $is_catalog)
    {
        try {
            $this->category_id = $category_id;
            $this->is_featured = $is_featured;
            $this->is_catalog = $is_catalog;

            if (!Cache::has("pel:getValidationFromCache$this->category_id:$this->is_featured:$this->is_catalog" && Cache::get("getValidationFromCache") == [])) {
                $result = Cache::rememberforever("getValidationFromCache$this->category_id:$this->is_featured:$this->is_catalog", function () {

                    $validation_info = DB::select('SELECT
                                          id AS setting_id,
                                          category_id,
                                          validation_name,
                                          max_value_of_validation,
                                          is_featured,
                                          is_catalog,
                                          update_time
                                        FROM
                                          settings_master
                                        WHERE 
                                          is_active = ? AND 
                                          category_id = ? AND 
                                          is_featured = ? AND 
                                          is_catalog = ?', [1, $this->category_id, $this->is_featured, $this->is_catalog]);

                    if (count($validation_info) == 0) {
                        $validation_info = DB::select('SELECT
                                          id AS setting_id,
                                          category_id,
                                          validation_name,
                                          max_value_of_validation,
                                          is_featured,
                                          is_catalog,
                                          update_time
                                        FROM
                                          settings_master
                                        WHERE 
                                          category_id = ? AND 
                                          is_active = ?', [0, 1]);
                    }

                    return $validation_info[0]->max_value_of_validation;

                });
            }

            $redis_result = Cache::get("getValidationFromCache$this->category_id:$this->is_featured:$this->is_catalog");

            if (!$redis_result) {
                $redis_result = [];
            }

            return $redis_result;

        } catch (Exception $e) {
            Log::error("getValidationFromCache : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            return Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get validations from cache.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
    }

    /*-----------------------------| Functions for ZIP |-----------------------------*/

    //save all template resources(image) uploaded by Zip
    public function saveResourceImageByZip($image_array, $image)
    {
        $resource_path = Config::get('constant.RESOURCE_IMAGES_DIRECTORY');
        $this->unlinkFileFromLocalStorage($image, $resource_path);
        $original_path = '../..' . $resource_path;
        copy($image_array, $original_path . $image);
    }

    // Save original Image
    public function saveOriginalImageByZip($image_array, $image)
    {
        $original_path = '../..' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY');
        copy($image_array, $original_path . $image);
        $path = $original_path . $image;
        $this->saveImageDetails($path, 'original');
    }

    //generate file name for zip
    public function generateNewFileNameByZip($image_type, $fileData)
    {
        $fileData = pathinfo(basename($fileData));
        $new_file_name = uniqid() . '_' . $image_type . '_' . time() . '.' . strtolower($fileData['extension']);
        $path = '../..' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY') . $new_file_name;
        if (File::exists($path))
            $new_file_name = uniqid() . '_' . $image_type . '_' . time() . '.' . $fileData['extension'];
        return $new_file_name;
    }

    //verify zip file
    public function verifyZipFile($image_array)
    {
        $file_type = $image_array->getMimeType();
        $file_size = $image_array->getSize();
        //Log::info('extension : ', ['extension' => $file_type]);
        //Log::info("Image Size",[$image_size]);
        $MAXIMUM_FILESIZE = 10 * 1024 * 1024;

        if (!($file_type == 'application/zip'))
            $response = Response::json(array('code' => 201, 'message' => 'Please select zip file.', 'cause' => '', 'data' => json_decode("{}")));
        elseif ($file_size > $MAXIMUM_FILESIZE)
            $response = Response::json(array('code' => 201, 'message' => 'File size is greater then 10MB.', 'cause' => '', 'data' => json_decode("{}")));
        else
            $response = '';
        return $response;
    }

    public function rrmdir($dir)
    {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (is_dir($dir . "/" . $object))
                        $this->rrmdir($dir . "/" . $object);
                    else
                        unlink($dir . "/" . $object);
                }
            }
            rmdir($dir);
        }
    }

    // Check height-width of sample image
    public function verifyHeightWidthOfSampleImage($image_array, $type = 0)
    {
        /** Type
         *0 = Square
         *1 = landscape
         *2 = portrait
         */
        // Open image as a string
        $data = file_get_contents($image_array);

        // getimagesizefromstring function accepts image data as string & return file info
        $file_info = getimagesizefromstring($data);

        // Display the image content
        $width = $file_info[0];
        $height = $file_info[1];

        if ($type == 1) {
            if ($height < $width) {
                $response = '';
            } else {
                return $response = Response::json(array('code' => 201, 'message' => 'Please select landscape image.', 'cause' => '', 'data' => json_decode("{}")));
            }
        } elseif ($type == 2) {
            if ($height > $width) {
                $response = '';
            } else {
                return $response = Response::json(array('code' => 201, 'message' => 'Please select portrait image.', 'cause' => '', 'data' => json_decode("{}")));
            }
        } else {
            if ($height == $width) {
                $response = '';
            } else {
                return $response = Response::json(array('code' => 201, 'message' => 'Please select square image.', 'cause' => '', 'data' => json_decode("{}")));
            }
        }
        return $response;
    }

    public function verifyIndustryIcon($image_array)
    {
        $image_type = $image_array->getMimeType();
        $image_size = $image_array->getSize();

        $MAXIMUM_FILESIZE = 100 * 1024; //100kb

        if (!($image_type == 'image/png'))
            $response = Response::json(array('code' => 201, 'message' => 'Please select PNG icon file.', 'cause' => '', 'data' => json_decode("{}")));
        elseif ($image_size > $MAXIMUM_FILESIZE)
            $response = Response::json(array('code' => 201, 'message' => 'Icon file Size is greater then 100KB.', 'cause' => '', 'data' => json_decode("{}")));
        else
            $response = '';
        return $response;
    }

}

<?php


namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Support\Facades\Storage;
use Image;
use Exception;
use Response;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Input;
use File;
use Log;
use DB;

class ImageController extends Controller
{
    // get base url
    public function getBaseUrl(){

        // get base url in local/live server
        return Config::get('constant.ACTIVATION_LINK_PATH');
        //return 'http://'.$_SERVER['HTTP_HOST'].'/'.Config::get('constant.PROJECT_NAME');
        //$a='http://'.$_SERVER['HTTP_HOST'].'/'.Config::get('constant.PROJECT_NAME');
        //var_dump($a);
    }


    //verify image
    public function verifyImage($image_array){

        $image_type =$image_array->getMimeType();
        $image_size = $image_array->getSize();
        //Log::info("Image Size",[$image_size]);
        $MAXIMUM_FILESIZE = 10 * 1024 * 1024;

        if(!($image_type == 'image/png' || $image_type == 'image/jpeg'))
            $response =  Response::json(array('code' => '201', 'message' => 'Please select PNG or JPEG file', 'cause'=>'','response'=>json_decode("{}")));
        elseif($image_size > $MAXIMUM_FILESIZE)
            $response =  Response::json(array('code' => '201', 'message' => 'File Size is greater then 5MB', 'cause'=>'','response'=>json_decode("{}")));
        else
            $response = '';
        return $response;
    }

    public function verifyVideo($video_array){

        $video_type =$video_array->getMimeType();
        $video_size = $video_array->getSize();

        $MAXIMUM_FILESIZE = 10 * 1024 * 1024;

        //x-ms-asf ==>.asf or .asx
        //ap4 ==>.mp4
        //webm ==>.webm (upload mkv file)
        //quicktime ==>.mov

        if(!($video_type == 'video/x-ms-asf' || $video_type == 'video/mp4'|| $video_type == 'video/webm'|| $video_type == 'video/quicktime')){
            return $response =  Response::json(array('code' => '201', 'message' => 'Please select asf or mp4 or webm(mkv) or mov file', 'cause'=>'','data'=>json_decode("{}")));

        }
        elseif($video_size > $MAXIMUM_FILESIZE){
            return $response =  Response::json(array('code' => '201', 'message' => 'File Size is greater then 10MB', 'cause'=>'','data'=>json_decode("{}")));
        }

        else
            $response = '';
        return $response;
    }

    // verify audio
    public function verifyAudio($audio_array){

        $audio_type =$audio_array->getMimeType();
        $audio_size = $audio_array->getSize();

        $MAXIMUM_FILESIZE = 10 * 1024 * 1024;

        //octet-stream ==>.3gp
        //quicktime ==>.mov

        if(!($audio_type == 'audio/mpeg'||$audio_type == 'application/octet-stream')){
            return $response =  Response::json(array('code' => '201', 'message' => 'Please select 3gp or mp4 audio file', 'cause'=>'','data'=>json_decode("{}")));
        }

        elseif($audio_size > $MAXIMUM_FILESIZE){
            return $response =  Response::json(array('code' => '201', 'message' => 'File Size is greater then 10MB', 'cause'=>'','data'=>json_decode("{}")));
        }

        else
            $response = '';
        return $response;
    }

    //generate image new name
    public function generateNewFileName($image_type,$image_array){

        $fileData = pathinfo(basename($image_array->getClientOriginalName()));
        $new_file_name =  uniqid().'_'.$image_type.'_'.time().'.'.$fileData['extension'];
        $path = '../..'.Config::get('constant.ORIGINAL_IMAGES_DIRECTORY').$new_file_name;
        if(File::exists($path))
            $new_file_name =  uniqid().'_'.$image_type.'_'.time().'.'.$fileData['extension'];
        return $new_file_name;
    }

    // Save original Image
    public function saveOriginalImage($img){
        $original_path = '../..'.Config::get('constant.ORIGINAL_IMAGES_DIRECTORY');
        Input::file('file')->move($original_path, $img);
        $path = $original_path.$img;
        $this->saveImageDetails($path,'original');


    }
// Save encoded Image
    public function saveEncodedImage($image_array,$professional_img){
        $path = '../..'.Config::get('constant.ORIGINAL_IMAGES_DIRECTORY').$professional_img;
        file_put_contents($path, $image_array);
    }

    // Save Compressed Image
    public function saveCompressedImage($cover_img){
        try {
            $original_path = '../..'.Config::get('constant.ORIGINAL_IMAGES_DIRECTORY').$cover_img;
            $compressed_path = '../..'.Config::get('constant.COMPRESSED_IMAGES_DIRECTORY').$cover_img;
            $img = Image::make($original_path);
            $img->save($compressed_path, 75);

            $original_img_size = filesize($original_path);
            $compress_img_size = filesize($compressed_path);


            //Log::info(["Original Img Size :"=>$original_img_size,"Compress Img Size :"=>$compress_img_size]);


            if($compress_img_size >= $original_img_size){
                //save original image in Compress image
                //Log::info("Compress Image Deleted.!");
                File::delete($compressed_path);
                File::copy($original_path,$compressed_path);
            }
            //use for Image Details
            $this->saveImageDetails($compressed_path,'compress');


        }catch (Exception $e){
            $dest1 = '../..'.Config::get('constant.ORIGINAL_IMAGES_DIRECTORY').$cover_img;
            $dest2 = '../..'.Config::get('constant.COMPRESSED_IMAGES_DIRECTORY').$cover_img;
            foreach($_FILES['file'] as $check){
                chmod($dest1,0777);
                copy($dest1, $dest2);
                Log::error("Exception :",[$e->getMessage()]);
            }

        }
    }

    // Get Thumbnail Width Height
    public function getThumbnailWidthHeight($professional_img){

        $original_path = '../..'.Config::get('constant.ORIGINAL_IMAGES_DIRECTORY').$professional_img;
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

        $array = array('width'=>$width,'height'=>$height);
        return $array;
    }

    // Save Thumbnail Image
    public function saveThumbnailImage($professional_img){
        try {
            $array = $this->getThumbnailWidthHeight($professional_img);
            $width = $array['width'];
            $height = $array['height'];
            $original_path = '../..'.Config::get('constant.ORIGINAL_IMAGES_DIRECTORY').$professional_img;
            $thumbnail_path = '../..'.Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY').$professional_img;
            $img = Image::make($original_path)->resize($width, $height);
            $img->save($thumbnail_path);

            //use for Image Details
            $this->saveImageDetails($thumbnail_path,'thumbnail');

        }catch (Exception $e){
            $dest1 = '../..'.Config::get('constant.ORIGINAL_IMAGES_DIRECTORY').$professional_img;
            $dest2 = '../..'.Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY').$professional_img;
            foreach($_FILES['file'] as $check){
                chmod($dest1,0777);
                copy($dest1, $dest2);
            }
        }
    }

    // save Compressed and Thumbnail Image
    public function saveCompressedThumbnailImage($source_url, $destination_url,$thumbnail_path) {

        $info = getimagesize($source_url);
        $width_orig = $info[0];
        $height_orig = $info[1];
        $ratio_orig = $width_orig/$height_orig;

        $width = $width_orig < Config::get('constant.THUMBNAIL_WIDTH') ? $width_orig : Config::get('constant.THUMBNAIL_WIDTH');
        $height = $height_orig < Config::get('constant.THUMBNAIL_HEIGHT') ? $height_orig : Config::get('constant.THUMBNAIL_HEIGHT');

        if ($width/$height > $ratio_orig)
            $width = $height*$ratio_orig;
        else
            $height = $width/$ratio_orig;

        if ($info['mime'] == 'image/jpeg'){
            // save compress image
            $image = imagecreatefromjpeg($source_url);
            imagejpeg($image, $destination_url, 75);

            // save thumbnail image
            $tmp_img = imagecreatetruecolor( $width, $height );
            imagecopyresized( $tmp_img, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig );
            imagejpeg( $tmp_img,$thumbnail_path);

        }elseif ($info['mime'] == 'image/png'){
            // save compress image
            $image = imagecreatefrompng($source_url);
            imagepng($image, $destination_url, 9);

            // save thumbnail image
            $tmp_img = imagecreatetruecolor($width, $height);
            imagealphablending($tmp_img, false);
            imagesavealpha($tmp_img, true);
            $transparent = imagecolorallocatealpha($tmp_img, 255, 255, 255, 127);
            imagefilledrectangle($tmp_img, 0, 0, $width_orig, $height_orig, $transparent);
            imagecopyresized( $tmp_img, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig );
            imagepng( $tmp_img,$thumbnail_path);
        }
    }

    //Check Compress image >= original image ? save original image : compress
    public function CompressImageCheck($img_type,$image_array){
        try{
            //Create Image Name
            $img_name = $this->generateNewFileName($img_type,$image_array);

            //create Original Image Path
            $original_path = '../..'.Config::get('constant.ORIGINAL_IMAGES_DIRECTORY');

            //Create Compress Image Path
            $compress_path = '../..'.Config::get('constant.COMPRESSED_IMAGES_DIRECTORY');

            //get Original Image Size
            $original_img_size = filesize($image_array);
            //Log::info(["Original Img Size"=>$original_img_size]);

            //Move Image in User decide Folder(Save)
            Input::file('file')->move($original_path,$img_name);

            //Compressed Image
            $img = Image::make($original_path.$img_name);
            $img->save($compress_path.$img_name, 75);
            $compress_img_size = filesize($compress_path.$img_name);
            //Log::info(["Original Img Size :"=>$original_img_size,"Compress Img Size :"=>$compress_img_size]);
            //compress image size >= original image size
            if($compress_img_size >= $original_img_size){
                //save original image in Compress image
                //Log::info("Compress Image Deleted.!");
                File::delete($compress_path.$img_name);
                File::copy($original_path.$img_name,$compress_path.$img_name);
            }
        }catch (Exception $e){
            Log::error(["Exception :",$e->getMessage(),"TraceAsString :",$e->getTraceAsString()]);
        }

    }




    //Delete Images In Directory
    public function deleteImage($image_name){
        try{

            $original_path = '../..'.Config::get('constant.ORIGINAL_IMAGES_DIRECTORY').$image_name;
            $compressed_path = '../..'.Config::get('constant.COMPRESSED_IMAGES_DIRECTORY').$image_name;
            $thumbnail_path = '../..'.Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY').$image_name;

            File::delete($original_path);
            File::delete($compressed_path);
            File::delete($thumbnail_path);
            DB::beginTransaction();
            /* Image Details delete */
            DB::delete('DELETE
                        FROM
                          image_details
                        WHERE
                          name = ?',[$image_name]);
            DB::commit();

        }catch (Exception $e){
            Log::error("Exception :",["Error :"=>$e->getMessage(),"TraceAsString :"=>$e->getTraceAsString()]);
            DB::rollBack();
            return  Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . ' delete image.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
    }

    //image Analysis
    public function  saveImageDetails($image_path,$image_directory){
        try{
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
                [$name,$image_directory,$type,$size,$height,$width,$pixel,$create_at]);

            DB::commit();
        }catch (Exception $e){
            Log::error("Exception :",["Error :"=>$e->getMessage(),"TraceAsString :"=>$e->getTraceAsString()]);
            DB::rollBack();
            return  Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . ' save image details.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
    }
    // Save original Image
    public function saveMultipleOriginalImage($img,$file_name){

        $original_path = '../..'.Config::get('constant.ORIGINAL_IMAGES_DIRECTORY');
        Input::file($file_name)->move($original_path, $img);
//
//        $original_path = Config::get('constant.ORIGINAL_IMAGES_DIRECTORY');
//        Input::file($file_name)->move($original_path, $img);
//
//        //use for Image Details
        $path = $original_path.$img;
        $this->saveImageDetails($path,'original');
    }

    public function saveMultipleCompressedImage($cover_img,$file_name)
    {
        try {
            $original_path = '../..'.Config::get('constant.ORIGINAL_IMAGES_DIRECTORY').$cover_img;
            $compressed_path = '../..'.Config::get('constant.COMPRESSED_IMAGES_DIRECTORY').$cover_img;
            $img = Image::make($original_path);
            $img->save($compressed_path, 75);

            $original_img_size = filesize($original_path);
            $compress_img_size = filesize($compressed_path);
            //Log::info(["Original Img Size :"=>$original_img_size,"Compress Img Size :"=>$compress_img_size]);
            if($compress_img_size >= $original_img_size){
                //save original image in Compress image
                //Log::info("Compress Image Deleted.!");
                File::delete($compressed_path);
                File::copy($original_path,$compressed_path);
            }
            //use for Image Details
            $this->saveImageDetails($compressed_path,'compress');

        }catch (Exception $e){
            $dest1 = '../..'.Config::get('constant.ORIGINAL_IMAGES_DIRECTORY').$cover_img;
            $dest2 = '../..'.Config::get('constant.COMPRESSED_IMAGES_DIRECTORY').$cover_img;
            foreach($_FILES[$file_name] as $check){
                chmod($dest1,0777);
                copy($dest1, $dest2);
                Log::error("Exception :",[$e->getMessage()]);
            }
        }

    }

    public function saveMultipleThumbnailImage($professional_img,$file_name)
    {
        try {
            $array = $this->getThumbnailWidthHeight($professional_img);
            $width = $array['width'];
            $height = $array['height'];
            $original_path = '../..'.Config::get('constant.ORIGINAL_IMAGES_DIRECTORY').$professional_img;
            $thumbnail_path = '../..'.Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY').$professional_img;
            $img = Image::make($original_path)->resize($width, $height);
            $img->save($thumbnail_path);

            //use for Image Details
            $this->saveImageDetails($thumbnail_path,'thumbnail');

        }catch (Exception $e){
            $dest1 = '../..'.Config::get('constant.ORIGINAL_IMAGES_DIRECTORY').$professional_img;
            $dest2 = '../..'.Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY').$professional_img;
            foreach($_FILES[$file_name] as $check){
                chmod($dest1,0777);
                copy($dest1, $dest2);
            }
        }
    }

    public function saveResourceImage($image_array)
    {
        $bg_image = $image_array->getClientOriginalName();
        $original_path = '../..' . Config::get('constant.RESOURCE_IMAGES_DIRECTORY');
        $image_array->move($original_path, $bg_image);

    }

    public function unlinkImage($image_array)
    {
        $bg_image = $image_array->getClientOriginalName();

        $image_path = '../..'.Config::get('constant.RESOURCE_IMAGES_DIRECTORY').$bg_image;

        if (File::exists($image_path)) {
            //File::delete($image_path);
            unlink($image_path);
        }
//                   File::delete($filename);

    }

    //unlinkImage from image_bucket
    public function unlinkfile($image_array)
    {
        try{

            //$bg_image = $image_array->getClientOriginalName();

            $original_image_path = '../..'.Config::get('constant.ORIGINAL_IMAGES_DIRECTORY').$image_array;

            //Log::info('path : ',['path' => $image_array]);
            //if (File::exists($original_image_path)) {
            if (fopen($original_image_path, "r")) {
                //File::delete($image_path);
                unlink($original_image_path);
            }
            else
            {
                return 1;
            }

            $compressed_image_path = '../..'.Config::get('constant.COMPRESSED_IMAGES_DIRECTORY').$image_array;

            //if (File::exists($compressed_image_path)) {
            if (fopen($compressed_image_path, "r")) {
                //File::delete($image_path);
                unlink($compressed_image_path);
            }

            $thumbnail_image_path = '../..'.Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY').$image_array;

            //if (File::exists($thumbnail_image_path)) {
            if (fopen($thumbnail_image_path, "r")) {
                //File::delete($image_path);
                unlink($thumbnail_image_path);
            }

        }
        catch(Exception $e){

        }



    }

    //unlinkImage from image_bucket
    public function saveImageInToSpaces($image)
    {
        try
        {
            $base_url = (new ImageController())->getBaseUrl();



            $original_sourceFile = $base_url . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY') . $image;
            $compressed_sourceFile = $base_url . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY') . $image;
            $thumbnail_sourceFile = $base_url . Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY') . $image;

            //return array($original_sourceFile,$compressed_sourceFile, $thumbnail_sourceFile);

            $disk = Storage::disk('spaces');
            if (fopen($original_sourceFile, "r")){

                $original_targetFile = "photoeditorlab/original/" . $image;
                $disk->put($original_targetFile, file_get_contents($original_sourceFile),'public');

            }

            if (fopen($compressed_sourceFile, "r")){

                $compressed_targetFile = "photoeditorlab/compressed/" . $image;
                $disk->put($compressed_targetFile, file_get_contents($compressed_sourceFile),'public');

            }

            if (fopen($thumbnail_sourceFile, "r")){

                $thumbnail_targetFile = "photoeditorlab/thumbnail/" . $image;
                $disk->put($thumbnail_targetFile, file_get_contents($thumbnail_sourceFile),'public');

            }

            (new ImageController())->unlinkfile($image);
        }
        catch(Exception $e)
        {
            Log::error("saveImageInToSpaces Exception :", ['Error : ' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);

        }


    }

    //unlinkImage from image_bucket
    public function saveImageInToSpacesForMigration($image)
    {
        try
        {
            //$base_url = (new ImageController())->getBaseUrl();
            $base_url = 'http://138.197.11.186/ob_photolab_backend';



            $original_sourceFile = $base_url . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY') . $image;
            $compressed_sourceFile = $base_url . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY') . $image;
            $thumbnail_sourceFile = $base_url . Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY') . $image;



            //return array($original_sourceFile,$compressed_sourceFile, $thumbnail_sourceFile);

            $disk = Storage::disk('spaces');
            if (fopen($original_sourceFile, "r")){

                $original_targetFile = "photoeditorlab/original/" . $image;
                $disk->put($original_targetFile, file_get_contents($original_sourceFile),'public');

            }

            if (fopen($compressed_sourceFile, "r")){

                $compressed_targetFile = "photoeditorlab/compressed/" . $image;
                $disk->put($compressed_targetFile, file_get_contents($compressed_sourceFile),'public');

            }

            if (fopen($thumbnail_sourceFile, "r")){

                $thumbnail_targetFile = "photoeditorlab/thumbnail/" . $image;
                $disk->put($thumbnail_targetFile, file_get_contents($thumbnail_sourceFile),'public');

            }

            (new ImageController())->unlinkfile($image);
        }
        catch(Exception $e)
        {
            Log::error("saveImageInToSpaces Exception :", ['Error : ' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);

        }


    }



}
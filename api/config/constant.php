<?php
/**
 * Created by Optimumbrew.
 * User: admin & user
 * Date: 05/07/2017
 * Time: 10:00 AM
 */
return [

    //////////////////////////////Change Server Configuration////////////////////////////////////////////////////////

    'ACTIVATION_LINK_PATH' => env('ACTIVATION_LINK_PATH'),     //live & local

    'XMPP_HOST' => 'photoeditorlab.co.in', //live
    //'XMPP_HOST' => '192.168.0.113', //local


    'EXCEPTION_ERROR' => 'PhotoEditorLab is unable to ',
    'DATE_FORMAT' => 'Y-m-d H:i:s',

    /* For live server */
    'SUPER_ADMIN_EMAIL_ID' => env('SUPER_ADMIN_EMAIL_ID'),
    'ADMIN_EMAIL_ID' => env('ADMIN_EMAIL_ID'),
    'SUB_ADMIN_EMAIL_ID' => env('SUB_ADMIN_EMAIL_ID'),
    'SUPPORTER_EMAIL_ID' => env('SUPPORTER_EMAIL_ID'),

    'RESPONSE_HEADER_CACHE' => 'max-age=2592000',    // 30 days caching response data
    'ROLE_FOR_ADMIN' => 'admin',
    'ROLE_FOR_USER' => 'user',

    'GUEST_USER_UD' => 'guest@gmail.com',
    'GUEST_PASSWORD' => 'demo@123',
    'PROJECT_NAME' => 'photo_editor_lab_backend',

    'COMPRESSED_IMAGES_DIRECTORY' => '/image_bucket/compressed/',
    'ORIGINAL_IMAGES_DIRECTORY' => '/image_bucket/original/',
    'THUMBNAIL_IMAGES_DIRECTORY' => '/image_bucket/thumbnail/',
    'RESOURCE_IMAGES_DIRECTORY' => '/image_bucket/resource/',
    'ORIGINAL_VIDEO_DIRECTORY' => '/image_bucket/video/',
    'WEBP_ORIGINAL_IMAGES_DIRECTORY' => '/image_bucket/webp_original/',
    'WEBP_THUMBNAIL_IMAGES_DIRECTORY' => '/image_bucket/webp_thumbnail/',
    'WEBP_ORIGINAL_NEW_IMAGES_DIRECTORY' => '/image_bucket/webp_original_new/',
    'WEBP_THUMBNAIL_NEW_IMAGES_DIRECTORY' => '/image_bucket/webp_thumbnail_new/',
    'FONT_FILE_DIRECTORY' => '/image_bucket/fonts/',
    'TEMP_FILE_DIRECTORY' => '/image_bucket/temp/',
    //'SVG_IMAGES_DIRECTORY' => '/image_bucket/svg/',

    /*'COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN' => 'https://photoeditorlab.nyc3.digitaloceanspaces.com/photoeditorlab/compressed/',
    'ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN' => 'https://photoeditorlab.nyc3.digitaloceanspaces.com/photoeditorlab/original/',
    'THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN' => 'https://photoeditorlab.nyc3.digitaloceanspaces.com/photoeditorlab/thumbnail/',
    'RESOURCE_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN' => 'https://photoeditorlab.nyc3.digitaloceanspaces.com/photoeditorlab/resource/',*/

    /* static path of live server*/
    /*'COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN' => 'https://photoeditorlab.co.in/photo_editor_lab_backend/image_bucket/compressed/',
    'ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN' => 'https://photoeditorlab.co.in/photo_editor_lab_backend/image_bucket/original/',
    'THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN' => 'https://photoeditorlab.co.in/photo_editor_lab_backend/image_bucket/thumbnail/',
    'RESOURCE_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN' => 'https://photoeditorlab.co.in/photo_editor_lab_backend/image_bucket/resource/',*/

    /*live server*/
    'AWS_BUCKET_PATH_PHOTO_EDITOR_LAB' => env('AWS_BUCKET_PATH_PHOTO_EDITOR_LAB'),
    'COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN' => env('COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN'),
    'ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN' => env('ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN'),
    'THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN' => env('THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN'),
    'RESOURCE_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN' => env('RESOURCE_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN'),
    'WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN' => env('WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN'),
    'WEBP_THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN' => env('WEBP_THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN'),
    'WEBP_ORIGINAL_NEW_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN' => env('WEBP_ORIGINAL_NEW_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN'),
    'WEBP_THUMBNAIL_NEW_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN' => env('WEBP_THUMBNAIL_NEW_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN'),
    'ORIGINAL_VIDEO_DIRECTORY_OF_DIGITAL_OCEAN' => env('ORIGINAL_VIDEO_DIRECTORY_OF_DIGITAL_OCEAN'),
    'FONT_FILE_DIRECTORY_OF_DIGITAL_OCEAN' => env('FONT_FILE_DIRECTORY_OF_DIGITAL_OCEAN'),
    'TEMP_FILE_DIRECTORY_OF_DIGITAL_OCEAN' => env('TEMP_FILE_DIRECTORY_OF_DIGITAL_OCEAN'),
    //'SVG_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN' => env('SVG_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN'),

    'TOTAL_ORDER_AMOUNT' => 10,
    'THUMBNAIL_HEIGHT' => 240,
    'THUMBNAIL_WIDTH' => 320,

    'PAGINATION_ITEM_LIMIT' => '15',
    'ITEM_COUNT_OF_FEATURED_JSON' => '5',
    'ITEM_COUNT_OF_TEMPLATES' => '5', //It's used for only Brand Maker in API to get templates in templates screen
    'ITEM_COUNT_OF_FEATURED_CATALOGS_AND_TEMPLATES' => 10, //It's used for only in Flyer Maker's API to get feature templates and catalog for new layout module.
    'ITEM_COUNT_OF_GET_DYNAMIC_SEARCH_TAG' => 100,

    'GCM_NOTIFICATION_URL' => 'https://fcm.googleapis.com/fcm/send',

    /* host name of certificate maker to display sample image in jpg/png */
    'HOST_NAME_OF_CERTIFICATE_MAKER' => 'www.graphicdesigns.co.in',

    /*for testing key and sender id*/
    /*'GCM_SERVER_KEY' => 'AIzaSyDRwopI1tXBr2OJL8AwaQgkGoKAvYq4dmg',
    'GCM_SENDER_ID' => '129236435167',*/

    /* For Live server key and sender id */
    'GCM_SERVER_KEY' => 'AAAA20RbdKs:APA91bHGLlVsyfcqNMNHPQBYt-hyjY8NzuaxOUOlSOx3bIiZVnCL8kL2pTWsFchw0jbx0kfO_BVmcjmZlgJWNojXpyF0D0FegVzgMarzu5S9SnSchoyYyCMVUgli8QE4QFXhmWSRhqJE',
    'GCM_SENDER_ID' => '941744682155',
    'GCM_TITLE_FOR_CATALOG' => '',

    /* for local server */
    //'receipt_validator_endpoint' => 'https://sandbox.itunes.apple.com/verifyReceipt',

    /* for live server */
    'receipt_validator_endpoint' => 'https://buy.itunes.apple.com/verifyReceipt',

    /*api key of pixabay*/
    'PIXABAY_API_KEY' => env('PIXABAY_API_KEY'),
    'PIXABAY_API_URL' => 'https://pixabay.com/api/',
    'PIXABAY_IMAGES_ITEM_COUNT' => 50,

    /* quality of image compression */
    'QUALITY' => '75',

    'IMAGE_BUCKET_ORIGINAL_IMG_PATH' => env('IMAGE_BUCKET_ORIGINAL_IMG_PATH'),
    'IMAGE_BUCKET_WEBP_ORIGINAL_IMG_PATH' => env('IMAGE_BUCKET_WEBP_ORIGINAL_IMG_PATH'),
    'IMAGE_BUCKET_WEBP_THUMBNAIL_IMG_PATH' => env('IMAGE_BUCKET_WEBP_THUMBNAIL_IMG_PATH'),
    'IMAGE_BUCKET_WEBP_ORIGINAL_NEW_IMG_PATH' => env('IMAGE_BUCKET_WEBP_ORIGINAL_NEW_IMG_PATH'),
    'IMAGE_BUCKET_WEBP_THUMBNAIL_NEW_IMG_PATH' => env('IMAGE_BUCKET_WEBP_THUMBNAIL_NEW_IMG_PATH'),
    'PATH_OF_CWEBP' => env('PATH_OF_CWEBP'),
    'FFMPEG_PATH' => env('FFMPEG_PATH'),
    'FFPROBE_PATH' => env('FFPROBE_PATH'),


    /*=======================| API key of Unsplash |=======================*/

    'UNSPLASH_API_KEY' => env('UNSPLASH_API_KEY'),
    'UNSPLASH_API_URL' => 'https://api.unsplash.com/search/photos',
    'UNSPLASH_ITEM_COUNT' => 30, //Maximum 30 item

    /*============================| resume_maker_job_search_module |============================*/

    'PAGINATION_ITEM_COUNT' => 50,
    'ITEM_COUNT_TO_GET_QUESTION' => 30,
    'SET_ITEM_COUNT_INTO_PROVIDER' => 15,

    /*============================== Twitter ==============================*/

    /* local server */
    /*'twitter_consumer_Key' => 'nFkqK8lBcfHsPQSZgkWh6Ky4T',
    'twitter_consumer_Secret' => 'Koh4KvkkXlYrzVE61q7yanZrUeuXvCFovObD0k53RF0NYMGksK',
    'twitter_access_Token' => '4746441684-gfXiRVLR9q7IOTz6Us03PyH621lu7L9p2mtF0SU',
    'twitter_access_Token_Secret' => 'lHh4WBilZYzdW7b2amVBEguoLA1JaiQP2qtUAOyTopp4h',*/

    /* live server */
    'twitter_consumer_Key' => 'KK45r5cgIWC7ATtWXsNIFo5vA',
    'twitter_consumer_Secret' => '433nrWNI1qaNt6damLjhmS13eIjsGEVqRUKypgmQomkKmyWifU',
    'twitter_access_Token' => '878515855679668225-rwtI5bQP1uIjudoBncbaVCDMclkMgf3',
    'twitter_access_Token_Secret' => 'BZz1uRMwrrwZET1CoKEipo6CJHzO5r2FL2LPfcCkuUlAv',
    'TWITTER_USER_LIST_FOR_TWITTER_TIMELINE' => [
        'us' => 'Jobs_in_USA_,jobsxlusa,zobjobsUS,tmj_usa_itqa,tmj_usa_art,intellegojobs,cybersecjobsusa,CGJob,ETJobNews,ALA_JobLIST,scrum_jobs,targetjobsUK,RandstadUSJobs,CareerBuilder,jtodonnell,Absolutely_Abby,AlisonDoyle,CreativeGroup,MonsterJobs,careersherpa',//'careerealism,CareerBliss,JobSearchAmanda,lindseypollak,'GrJobInterview',
        'my' => 'krojamy,kerja_kosongMY,twtkerjakosongg,KerjaOh,Kerjacarirezeki,JobVacanciesMY,jomkerjamy,Oh_KerjaKosong,infokerjaya,adnexioJobs,JobStreetMY,ManpowerMY',
        'id' => 'JobsIndonesia_,snapcareers_id,MaranathaCareer,ITBCareerCenter,CDCUIcareer,700juta,TelUCareer',
        'in' => 'Freshersworld,Jobs_in_India_,PlacementIndia,timesjobsdotcom,careerage,worksforindia,Jobscareers4u,JIT_official_,GovtJobOfficial,latestgovtnakur,Jobsportalforu,SarkariJob,iJobsUpdates,jobsinindia1978,TheIndiaJobs',
        //'th' => 'JobsinThailand_,UNESCAP_Jobs'
    ],
    'TWITTER_POST_ITEM_COUNT_FOR_TWITTER_TIMELINE' => 80, //80 items per account
    'TWITTER_TIMELINE_ITEM_COUNT_FOR_PAGINATION' => 50, //50 items per page
    'TWITTER_TIMELINE_IS_TEXT' => 1,
    'TWITTER_TIMELINE_IS_POST' => 2,
    'TWITTER_TIMELINE_IS_VIDEO' => 3,

    /*=============================| Home page |===============================*/

    'TEMPLATE_COUNT_FOR_HOME_PAGE' => 10,
    'COVER_TEMPLATE_COUNT_FOR_HOME_PAGE' => 10,
    'VIDEO_COUNT_FOR_HOME_PAGE' => 10,
    'JOB_NEWS_COUNT_FOR_HOME_PAGE' => 10,
    'QUESTION_TYPE_COUNT_FOR_HOME_PAGE' => 10,

    'APP_ENV' => env('APP_ENV'), //get app environment to store files into s3_bucket
    'AWS_BUCKET' => env('AWS_BUCKET'), //s3_bucket name from env to store/retrieve file from s3
    'AWS_KEY' => env('AWS_KEY'),
    'AWS_SECRET' => env('AWS_SECRET'),
    'STORAGE' => env('STORAGE'), //s3_bucket name from env to store/retrieve file from s3
    'CDN_DISTRIBUTION_ID' => env('CDN_DISTRIBUTION_ID'),

    /* Non-commercial fonts */
    'OFFLINE_CATALOG_IDS_OF_FONT' => env('OFFLINE_CATALOG_IDS_OF_FONT'), //Misc catalog Id for old fonts(non-commercial)

    'LOGO_MAKER_AI_CATALOG_ID' => env('LOGO_MAKER_AI_CATALOG_ID'),

    'FREE_CONTENT_COUNT' => 9, //count to provide free images for bkg, stickers, shapes, textArt & frames

    /* api key to detect tag from image */
    'CLARIFAI_API_KEY' => env('CLARIFAI_API_KEY'),

    /* api key to run google API like: translate,detect language */
    'GOOGLE_API_KEY' => env('GOOGLE_API_KEY'),

    /* host name to identify server for 2FA */
    'APP_HOST_NAME' => env('APP_HOST_NAME'),

    'EXPIRATION_TIME_OF_REDIS_KEY_TO_GET_ALL_FEATURED_TEMPLATES' => 1440, //time to expire key of caching in minutes (1440 = 24 hours)
    'ITEM_COUNT_PER_PAGE_FOR_FEATURED_TEMPLATES'=>20, //set item count per page for feature template(with  shuffle )

    'SUB_CATEGORY_ID_OF_TEXT_ART' => env('SUB_CATEGORY_ID_OF_TEXT_ART'),
    'SUB_CATEGORY_ID_OF_SHAPES' => env('SUB_CATEGORY_ID_OF_SHAPES'),
    'SUB_CATEGORY_ID_OF_GRAPHICS' => env('SUB_CATEGORY_ID_OF_GRAPHICS'),
    'SUB_CATEGORY_ID_OF_MOCK_UP' => env('SUB_CATEGORY_ID_OF_MOCK_UP'),

    /* BackEnd Logs credential */
    'LOG_USERNAME' => env('LOG_USERNAME'),
    'LOG_PASSWORD' => env('LOG_PASSWORD'),

    /* all image card content type */
    'CONTENT_TYPE_FOR_SAMPLE_IMAGE' => '1',
    'CONTENT_TYPE_FOR_SAMPLE_IMAGE_GIF' => '2',
    'CONTENT_TYPE_FOR_BEFORE_AFTER_IMAGE' => '3',
    'CONTENT_TYPE_FOR_NORMAL_RESOURCE' => '4',
    'CONTENT_TYPE_FOR_SVG_RESOURCE' => '5',

    /* Category Id value */
    'CATEGORY_ID_OF_STICKER' => '2',
    'CATEGORY_ID_OF_BACKGROUND' => '3',
    'CATEGORY_ID_OF_FONTS' => '4',

    /* For Language Translation */
    'DEFAULT_LANGUAGE_CODE' => 'en',

    /* For API Caching */
    'CACHE_TIME_6_HOUR' => '360',
    'CACHE_TIME_24_HOUR' => '1440',
    'CACHE_TIME_48_HOUR' => '2880',
    'CACHE_TIME_7_DAYS' => '10080',

    //Expiration time of 2fa cookie for 30 days
    'EXPIRATION_TIME_OF_2FA_COOKIE' => env('EXPIRATION_TIME_OF_2FA_COOKIE'),
    'API_KEY' => '6a2b65c9-d0ca-40c2-99ae-817c53f1496f',
    'DAYS_TO_KEEP_SEARCH_TAG' => env('DAYS_TO_KEEP_SEARCH_TAG'),
    'IS_IMAGE_DETAILS_ANALYTICS_ENABLE' => env('IS_IMAGE_DETAILS_ANALYTICS_ENABLE'),

    /* single page to multi page data transfer */
    'SINGLE_PAGE_SUB_CATEGORY_ID' => '2',
    'MULTI_PAGE_SUB_CATEGORY_ID' => '19',
    'SINGLE_PAGE_SUB_CATEGORY_ID_FOR_LAYOUT' => '15',
    'MULTI_PAGE_SUB_CATEGORY_ID_FOR_LAYOUT' => '18',

    'OPENAI_API_KEY' => env('OPENAI_API_KEY'),

    'OPENAI_API_KEY_FOR_POSTER' => env('OPENAI_API_KEY_FOR_POSTER'),

//    'POSTER_API_KEY' => env('POSTER_API_KEY'),
//    'POSTER_API_KEY_ARRAY' => [env('POSTER_API_KEY'), env('POSTER_API_KEY_SECOND')],

    'PEXELS_API_KEY' => env('PEXELS_API_KEY'),
    'PEXELS_API_KEY_ARRAY' => [env('PEXELS_API_KEY'), env('PEXELS_API_KEY_SECOND'), env('PEXELS_API_KEY_THRID')],

    'IS_AI_COLOR' => false,
    'POSTER_API_KEY_RESET_MINUTES' => 60,

//    'poster_json_with_image' => array(46940, 46941, 46942, 46943, 46944, 46950,46951, 46952, 46953, 46954, 46955, 46956, 46957, 46958, 46959),
//    'poster_json_without_image' => array(46945, 46946, 46947,46948,46949,46960, 46961, 46962, 46963, 46964),

    //-- For Live
    'poster_json_with_image' => array(76249, 76250, 76251, 76252, 76253, 76254, 76255, 76256, 76257, 76258, 76259, 76260, 76261, 76262, 76263),
    'poster_json_without_image' => array(76264, 76265, 76266, 76267, 76268, 76269, 76270, 76271, 76272, 76273),

    'ai_text_prompts' => [

//====================================================headline PROMPT=====================================================//
        'headline' => 'You are a helpful assistant whose job is to provide or create text content for poster. The text content should be concise and not too detailed to fit on the poster.
{DynamicValue1}
Purpose of the poster: {DynamicValue2}

give a Headline based on this but give the exact result that the user wants. Do not give the other description.

provide a result text without adding any extra words so that it can be used directly in the poster.

add this text in the \'data\' key in the JSON provided below.

give the result in the below JSON format.

{
  "data": ""
}

If the result cannot be found then only give the below response in JSON format

{
  "error": "result not found"
}',
//====================================================promotion PROMPT=====================================================//
        'promotion' => 'You are a helpful assistant whose job is to give text content for posters. The text content should be concise and not too detailed to fit on the poster.
{DynamicValue1}
Objective of this poster: {DynamicValue2}

Provide a suitable promotion for this poster. Generate a result text without any extra words for direct use in the poster. Ensure that the promotion or promotions type keywords are not included in the result, as this content is intended for promotional use in the poster.

add this text in the \'data\' key in the JSON provided below.

give the result in the below JSON format.

{
  "data": ""
}

If the result cannot be found then only give the below response in JSON format

{
  "error": "result not found"
}',


//====================================================special offers PROMPT=====================================================//

        'special offers' => 'You are a helpful assistant whose job is to give text content for posters. The text content should be concise and not too detailed to fit on the poster.
{DynamicValue1}
Objective of this poster: {DynamicValue2}

Provide appropriate Special Offers for this poster. give special offers that are attractive. Generate a result text without any extra words for direct use in the poster.

add this text in the \'data\' key in the JSON provided below.

give the result in the below JSON format.

{
"data": ""
}

If the result cannot be found then only give the below response in JSON format

{
"error": "result not found"
}
',

        //====================================================subheadline PROMPT=====================================================//

        'subheadline' => 'You are a helpful assistant whose job is to provide or create text content for poster. The text content should be concise and not too detailed to fit on the poster.
{DynamicValue1}
Purpose of the poster: {DynamicValue2}

give a Subheadline based on this but give the exact result that the user wants. Do not give the other description.

provide a result text without adding any extra words so that it can be used directly in the poster.

add this text in the \'data\' key in the JSON provided below.

give the result in the below JSON format.

{
  "data": ""
}

If the result cannot be found then only give the below response in JSON format

{
  "error": "result not found"
}',

        //====================================================Description PROMPT=====================================================//

        'description' => 'You are a helpful assistant whose job is to provide or create text content for poster. The text content should be concise and not too detailed to fit on the poster.
{DynamicValue1}
Purpose of the poster: {DynamicValue2}

give a description based on this but give the exact result that the user wants. Do not give the other description.

provide a result text without adding any extra words so that it can be used directly in the poster.

add this text in the \'data\' key in the JSON provided below.

give the result in the below JSON format.

{
  "data": ""
}

If the result cannot be found then only give the below response in JSON format

{
  "error": "result not found"
}',


        //====================================================Branding Description PROMPT=====================================================//

        'branding description' => 'You are a helpful assistant whose job is to give text content for posters. The text content should be concise and not too detailed to fit on the poster.
{DynamicValue1}
Objective of this poster: {DynamicValue2}

provide a suitable branding description text for this poster. Generate a text without any extra words for direct use in the poster. Here are some general examples of branding descriptions, unrelated to this specific poster, provided only for your understanding: \'Innovative technology, timeless elegance\', \'Empowering lifestyles through sustainable fashion, \'Where comfort meets cutting-edge fashion\', \'Designing tomorrow with eco-conscious solutions\', and \'Join us in creating positive change by supporting our mission\', and \'Designing tomorrow with eco-conscious solutions\'.

add this text in the \'data\' key in the JSON provided below.

give the result in the below JSON format.

{
  "data": ""
}

If the result cannot be found then only give the below response in JSON format

{
  "error": "result not found"
}',


        //====================================================Service Description PROMPT=====================================================//

        'service description' => 'You are a helpful assistant whose job is to provide or create text content for poster. The text content should be concise and not too detailed to fit on the poster.
{DynamicValue1}
Purpose of the poster: {DynamicValue2}

give a Service Description based on this but give the exact result that the user wants. Do not give the other description.

provide a result text without adding any extra words so that it can be used directly in the poster.

add this text in the \'data\' key in the JSON provided below.

give the result in the below JSON format.

{
  "data": ""
}

If the result cannot be found then only give the below response in JSON format

{
  "error": "result not found"
}',



        //====================================================call to action PROMPT=====================================================//

        'call to action' => 'You are a helpful assistant whose job is to provide or create text content for poster. The text content should be concise and not too detailed to fit on the poster.
{DynamicValue1}
Purpose of the poster: {DynamicValue2}

give a CTA based on this but give the exact result that the user wants. Do not give the other description.

give CTA text in 1 to 5 words.

provide a result text without adding any extra words so that it can be used directly in the poster.

add this text in the \'data\' key in the JSON provided below.

give the result in the below JSON format.

{
  "data": ""
}

If the result cannot be found then only give the below response in JSON format

{
  "error": "result not found"
}',




        //====================================================Product list PROMPT=====================================================//

        'product list' => 'You are a helpful assistant whose job is to provide or create text content for poster. The text content should be concise and not too detailed to fit on the poster.
{DynamicValue1}
Purpose of the poster: {DynamicValue2}

give a Product list based on this but give the exact result that the user wants. Do not give the other description.

Ensure that the user receives the actual product list in the result, avoiding the use of dummy data.

provide a result text without adding any extra words so that it can be used directly in the poster.

add this text in the \'data\' key in the JSON provided below.

give the result in the below JSON format.

{
  "data": ""
}

If the result cannot be found then only give the below response in JSON format

{
  "error": "result not found"
}',



        //====================================================Product Description PROMPT=====================================================//

        'product description' => 'You are a helpful assistant whose job is to provide or create text content for poster. The text content should be concise and not too detailed to fit on the poster.
{DynamicValue1}
Purpose of the poster: {DynamicValue2}

give a Product Description based on this but give the exact result that the user wants. Do not give the other description.

provide a result text without adding any extra words so that it can be used directly in the poster.

add this text in the \'data\' key in the JSON provided below.

give the result in the below JSON format.

{
  "data": ""
}

If the result cannot be found then only give the below response in JSON format

{
  "error": "result not found"
}',



        //====================================================Service list PROMPT=====================================================//

        'service list' => 'You are a helpful assistant whose job is to give text content for posters. The text content should be concise and not too detailed to fit on the poster.
{DynamicValue1}
Objective of this poster: {DynamicValue2}

Provide a service list that precisely matches the user\'s request. Avoid including any extraneous information and ensure the user receives the accurate service list, without the inclusion of placeholder data.

add this text in the \'data\' key in the JSON provided below.

give the result in the below JSON format.

{
"data": ""
}

If the result cannot be found then only give the below response in JSON format

{
"error": "result not found"
}
',



        //====================================================Others PROMPT=====================================================//

        'others' => 'You are a helpful assistant whose job is to provide or create text content for poster. The text content should be concise and not too detailed to fit on the poster.
{DynamicValue1}
Purpose of the poster: {DynamicValue2}
User needs text for: {DynamicValue3}

give the text base on this data that user provide. Make sure to avoid repeating the exact line provided as the poster details.

provide a result text without adding any extra words so that it can be used directly in the poster.

add this text in the \'data\' key in the JSON provided below.

give the result in the below JSON format.

{
  "data": ""
}

If the result cannot be found then only give the below response in JSON format

{
  "error": "result not found"
}',
    ],

];

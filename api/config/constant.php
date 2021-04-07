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
    'ADMIN_EMAIL_ID' => env('ADMIN_EMAIL_ID'),
    'SUB_ADMIN_EMAIL_ID' => env('SUB_ADMIN_EMAIL_ID'),

    'RESPONSE_HEADER_CACHE' => 'max-age=2592000',
    'ROLE_FOR_ADMIN' => 'admin',
    'ROLE_FOR_USER' => 'user',

    'GUEST_USER_UD' => 'guest@gmail.com',
    'GUEST_PASSWORD' => 'demo@123',
    'PROJECT_NAME' => 'photo_editor_lab_backend',

    'COMPRESSED_IMAGES_DIRECTORY' => '/image_bucket/compressed/',
    'ORIGINAL_IMAGES_DIRECTORY' => '/image_bucket/original/',
    'THUMBNAIL_IMAGES_DIRECTORY' => '/image_bucket/thumbnail/',
    'RESOURCE_IMAGES_DIRECTORY' => '/image_bucket/resource/',
    'WEBP_ORIGINAL_IMAGES_DIRECTORY' => '/image_bucket/webp_original/',
    'WEBP_THUMBNAIL_IMAGES_DIRECTORY' => '/image_bucket/webp_thumbnail/',
    'WEBP_ORIGINAL_NEW_IMAGES_DIRECTORY' => '/image_bucket/webp_original_new/',
    'WEBP_THUMBNAIL_NEW_IMAGES_DIRECTORY' => '/image_bucket/webp_thumbnail_new/',
    'FONT_FILE_DIRECTORY' => '/image_bucket/fonts/',
    'TEMP_FILE_DIRECTORY' => '/image_bucket/temp/',

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
    'FONT_FILE_DIRECTORY_OF_DIGITAL_OCEAN' => env('FONT_FILE_DIRECTORY_OF_DIGITAL_OCEAN'),
    'TEMP_FILE_DIRECTORY_OF_DIGITAL_OCEAN' => env('TEMP_FILE_DIRECTORY_OF_DIGITAL_OCEAN'),

    /* static path of local server*/
    /*'COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN' => 'http://192.168.0.113/photo_editor_lab_backend/image_bucket/compressed/',
    'ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN' => 'http://192.168.0.113/photo_editor_lab_backend/image_bucket/original/',
    'THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN' => 'http://192.168.0.113/photo_editor_lab_backend/image_bucket/thumbnail/',
    'RESOURCE_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN' => 'http://192.168.0.113/photo_editor_lab_backend/image_bucket/resource/',*/

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
    'TWITTER_USER_LIST_FOR_TWITTER_TIMELINE' => 'CGJob,ETJobNews,ALA_JobLIST,scrum_jobs,targetjobsUK,RandstadUSJobs,CareerBuilder,jtodonnell,Absolutely_Abby,AlisonDoyle,CreativeGroup,MonsterJobs,careersherpa',//'careerealism,CareerBliss,JobSearchAmanda,lindseypollak,'GrJobInterview',
    'TWITTER_POST_ITEM_COUNT_FOR_TWITTER_TIMELINE' => 80, //80 items per account
    'TWITTER_TIMELINE_ITEM_COUNT_FOR_PAGINATION' => 50, //50 items per page
    'TWITTER_TIMELINE_IS_TEXT' => 1,
    'TWITTER_TIMELINE_IS_POST' => 2,
    'TWITTER_TIMELINE_IS_VIDEO' => 3,

    /*=============================| Home page |===============================*/

    'TEMPLATE_COUNT_FOR_HOME_PAGE' => 6,
    'VIDEO_COUNT_FOR_HOME_PAGE' => 6,
    'JOB_NEWS_COUNT_FOR_HOME_PAGE' => 6,
    'QUESTION_TYPE_COUNT_FOR_HOME_PAGE' => 6,

    'APP_ENV' => env('APP_ENV'), //get app environment to store files into s3_bucket
    'AWS_BUCKET' => env('AWS_BUCKET'), //s3_bucket name from env to store/retrieve file from s3
    'STORAGE' => env('STORAGE'), //s3_bucket name from env to store/retrieve file from s3

    /* Non-commercial fonts */
    'OFFLINE_CATALOG_IDS_OF_FONT' => env('OFFLINE_CATALOG_IDS_OF_FONT'), //Misc catalog Id for old fonts(non-commercial)

    'FREE_CONTENT_COUNT' => 9, //count to provide free images for bkg, stickers, shapes, textArt & frames

    /* api key to detect tag from image */
    'CLARIFAI_API_KEY' => env('CLARIFAI_API_KEY'),

    /* host name to identify server for 2FA */
    'APP_HOST_NAME' => env('APP_HOST_NAME'),

    'EXPIRATION_TIME_OF_REDIS_KEY_TO_GET_ALL_FEATURED_TEMPLATES' => 1440, //time to expire key of caching in minutes (1440 = 24 hours)
    'ITEM_COUNT_PER_PAGE_FOR_FEATURED_TEMPLATES'=>20, //set item count per page for feature template(with  shuffle )

    'SUB_CATEGORY_ID_OF_TEXT_ART' => env('SUB_CATEGORY_ID_OF_TEXT_ART'),
    'SUB_CATEGORY_ID_OF_SHAPES' => env('SUB_CATEGORY_ID_OF_SHAPES'),
    'SUB_CATEGORY_ID_OF_GRAPHICS' => env('SUB_CATEGORY_ID_OF_GRAPHICS')


];

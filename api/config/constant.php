<?php
/**
 * Created by Optimumbrew.
 * User: admin-2
 * Date: 05/07/2017
 * Time: 10:00 AM
 */
return [

    //////////////////////////////Change Server Configuration////////////////////////////////////////////////////////

    //'ACTIVATION_LINK_PATH'=>'https://'.$_SERVER['HTTP_HOST'],     //live

    'ACTIVATION_LINK_PATH'=>'http://'.$_SERVER['HTTP_HOST'].'/'.basename(dirname(dirname(__DIR__))),     //local

    'FORGOT_PASSWORD_SVN_PATH' => 'http://'.$_SERVER['HTTP_HOST'].'/'.basename(dirname(dirname(__DIR__))).'/api/public/activeProfile/',   //local

    'XMPP_HOST' => 'photoeditorlab.co.in', //live
    //'XMPP_HOST' => '192.168.0.113', //local


    'EXCEPTION_ERROR' => 'Photo Editor Lab is unable to ',
    'DATE_FORMAT' => 'Y-m-d H:i:s',

    /* For local server */
    //'ADMIN_EMAIL_ID' => 'patelpinal1415160@gmail.com',

    /* For live server */
    'ADMIN_EMAIL_ID' => 'alagiyanirav@gmail.com',

    'SYSADMIN_EMAIL_ID' => 'alagiyanirav@gmail.com',


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
    'COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN' => env('COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN'),
    'ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN' => env('ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN'),
    'THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN' => env('THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN'),
    'RESOURCE_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN' => env('RESOURCE_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN'),
    'WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN' => env('WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN'),
    'WEBP_THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN' => env('WEBP_THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN'),

    /* static path of local server*/
    /*'COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN' => 'http://192.168.0.113/photo_editor_lab_backend/image_bucket/compressed/',
    'ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN' => 'http://192.168.0.113/photo_editor_lab_backend/image_bucket/original/',
    'THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN' => 'http://192.168.0.113/photo_editor_lab_backend/image_bucket/thumbnail/',
    'RESOURCE_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN' => 'http://192.168.0.113/photo_editor_lab_backend/image_bucket/resource/',*/

    'TOTAL_ORDER_AMOUNT' => 10,
    'THUMBNAIL_HEIGHT' => 240,
    'THUMBNAIL_WIDTH' => 320,

    'PAGINATION_ITEM_LIMIT' => '15',

    'GCM_NOTIFICATION_URL' => 'https://fcm.googleapis.com/fcm/send',

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
    //'PIXABAY_API_KEY' => '9366777-8d4a537113175da05770fd05e,9420810-0fe767598d152bba00a385ade,9420912-5544c439271da9cb408f85869',
    'PIXABAY_API_KEY' => env('PIXABAY_API_KEY'),
    'PIXABAY_API_URL'=> 'https://pixabay.com/api/',

];
<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

//Logs Viewer
Route::get('logs/{user_name}/{password}', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');

//Login route
Route::post('doLoginForGuest', 'LoginController@doLoginForGuest');
Route::post('doLogin', 'LoginController@doLogin');
Route::post('doLogout', 'LoginController@doLogout');
Route::post('registerUserDeviceByDeviceUdid', 'RegisterController@registerUserDeviceByDeviceUdid');
Route::post('storeFileIntoS3Bucket', 'AdminController@storeFileIntoS3Bucket');

//get advertisement without token
Route::post('getLinkWithoutToken', 'UserController@getLinkWithoutToken');




Route::get('/clear-cache', function() {
    $exitCode = Artisan::call('cache:clear');
    return $exitCode;
});

Route::get('getRedisInfo','AdminController@getRedisInfo');

Route::get('/info',function(){
    return \Illuminate\Support\Facades\Redis::info();
});


Route::get('/monitoring',function(){
    return \Illuminate\Support\Facades\Redis::monitor();
});

Route::group(['prefix' => '', 'middleware' => ['ability:admin,admin_permission']], function() {

    Route::post('changePassword', 'LoginController@changePassword');

    Route::post('addAppContentViaMigration', 'AdminController@addAppContentViaMigration');

    //Promo code
    Route::post('addPromoCode', 'AdminController@addPromoCode');
    Route::post('getAllPromoCode', 'AdminController@getAllPromoCode');
    Route::post('searchPromoCode', 'AdminController@searchPromoCode');

    //category
    Route::post('addCategory', 'AdminController@addCategory');
    Route::post('updateCategory', 'AdminController@updateCategory');
    Route::post('deleteCategory', 'AdminController@deleteCategory');
    Route::post('searchCategoryByName', 'AdminController@searchCategoryByName');
    //Route::post('getAllCategory', 'AdminController@getAllCategory');

    //sub-category
    Route::post('addSubCategory', 'AdminController@addSubCategory');
    Route::post('updateSubCategory', 'AdminController@updateSubCategory');
    Route::post('deleteSubCategory', 'AdminController@deleteSubCategory');
    Route::post('searchSubCategoryByName', 'AdminController@searchSubCategoryByName');

    //catalog
    Route::post('addCatalog', 'AdminController@addCatalog');
    Route::post('updateCatalog', 'AdminController@updateCatalog');
    Route::post('deleteCatalog', 'AdminController@deleteCatalog');
    Route::post('searchCatalogByName', 'AdminController@searchCatalogByName');

    //catalog category Images
    Route::post('addCatalogImages', 'AdminController@addCatalogImages');
    Route::post('updateCatalogImage', 'AdminController@updateCatalogImage');
    Route::post('deleteCatalogImage', 'AdminController@deleteCatalogImage');
    Route::post('getImagesByCatalogIdForAdmin', 'AdminController@getImagesByCatalogIdForAdmin');

    /* Common API */

    Route::post('getAllSubCategory', 'AdminController@getAllSubCategory');
    Route::post('getAllUser', 'AdminController@getAllUser');
    Route::post('getImageDetails', 'AdminController@getImageDetails');
    Route::post('getPurchaseUser', 'AdminController@getPurchaseUser');
    Route::post('getAllRestoreDevice', 'AdminController@getAllRestoreDevice');
    Route::post('searchRestoreDevice', 'AdminController@searchRestoreDevice');
    Route::post('searchUser', 'AdminController@searchUser');
    Route::post('searchPurchaseUser', 'AdminController@searchPurchaseUser');

    Route::post('sendPushNotification', 'NotificationController@sendPushNotification');
    Route::post('getUserProfile', 'LoginController@getUserProfile');
    Route::post('updateUserProfile', 'AdminController@updateUserProfile');

    //link catalog
    //Route::post('getAllCatalog', 'AdminController@getAllCatalog');
    Route::post('linkCatalog', 'AdminController@linkCatalog');
    Route::post('getAllSubCategoryForLinkCatalog', 'AdminController@getAllSubCategoryForLinkCatalog');

    //advertise link
    Route::post('addLink', 'AdminController@addLink');


    Route::post('updateLink', 'AdminController@updateLink');
    Route::post('deleteLink', 'AdminController@deleteLink');
    Route::post('getAllLink', 'AdminController@getAllLink');
    Route::post('deleteLinkedCatalog', 'AdminController@deleteLinkedCatalog');
    Route::post('getAdvertiseLink', 'AdminController@getAdvertiseLink');
    Route::post('addAdvertiseLink', 'AdminController@addAdvertiseLink');
    Route::post('unlinkAdvertise', 'AdminController@unlinkAdvertise');


    //featured images for Background
    Route::post('addFeaturedBackgroundCatalogImage', 'AdminController@addFeaturedBackgroundCatalogImage');
    Route::post('updateFeaturedBackgroundCatalogImage', 'AdminController@updateFeaturedBackgroundCatalogImage');
    Route::post('getSampleImagesForAdmin', 'AdminController@getSampleImagesForAdmin');


    //featured images for Sticker and Frame (This api is not used in this project)
    Route::post('addFeaturedImage', 'AdminController@addFeaturedImage');
    Route::post('updateFeaturedImage', 'AdminController@updateFeaturedImage');



    //get featured catalog

    Route::post('getFrameCatalogBySubCategoryId', 'AdminController@getBackgroundCatalogBySubCategoryId');


    Route::post('getRedisKeys', 'AdminController@getRedisKeys');
    Route::post('deleteRedisKeys', 'AdminController@deleteRedisKeys');
    Route::post('getRedisKeyDetail', 'AdminController@getRedisKeyDetail');
    Route::post('clearRedisCache', 'AdminController@clearRedisCache');


    Route::post('sendPushNotification', 'NotificationController@sendPushNotification');

    // Images for greetings card
    Route::post('addCatalogImagesForJson', 'AdminController@addCatalogImagesForJson');
    Route::post('addJson', 'AdminController@addJson');
    Route::post('editJsonData', 'AdminController@editJsonData');


    // Link Advertisement with another sub_category
    Route::post('getAllAdvertisementForLinkAdvertisement', 'AdminController@getAllAdvertisementForLinkAdvertisement');
    Route::post('linkAdvertisementWithSubCategory', 'AdminController@linkAdvertisementWithSubCategory');
    Route::post('deleteLinkedAdvertisement', 'AdminController@deleteLinkedAdvertisement');


    Route::post('getAllAdvertisements', 'AdminController@getAllAdvertisements');
    Route::post('getAllAdvertisementToLinkAdvertisement', 'AdminController@getAllAdvertisementToLinkAdvertisement');

    //getUserFeedsBySubCategoryId
    Route::post('getUserFeedsBySubCategoryId', 'UserController@getUserFeedsBySubCategoryId');
    Route::post('deleteUserFeeds', 'UserController@deleteUserFeeds');
    Route::post('deleteAllUserFeeds', 'UserController@deleteAllUserFeeds');

    //Advertise Server Id
    Route::post('addAdvertisementCategory', 'AdminController@addAdvertisementCategory');
    Route::post('editAdvertisementCategory', 'AdminController@editAdvertisementCategory');
    Route::post('deleteAdvertisementCategory', 'AdminController@deleteAdvertisementCategory');
    Route::post('getAllAdvertiseCategory', 'AdminController@getAllAdvertiseCategory');
    Route::post('addAdvertiseServerId', 'AdminController@addAdvertiseServerId');
    Route::post('updateAdvertiseServerId', 'AdminController@updateAdvertiseServerId');
    Route::post('deleteAdvertiseServerId', 'AdminController@deleteAdvertiseServerId');
    Route::post('getAdvertiseServerIdForAdmin', 'AdminController@getAdvertiseServerIdForAdmin');

    //update all sample images using libwebp
    Route::post('updateAllSampleImages', 'AdminController@updateAllSampleImages');

    //Search Tag
    Route::post('addTag', 'AdminController@addTag');
    Route::post('updateTag', 'AdminController@updateTag');
    Route::post('deleteTag', 'AdminController@deleteTag');
    Route::post('getAllTags', 'AdminController@getAllTags');


});

Route::post('addLinkWithAppLogo', 'AdminController@addLinkWithAppLogo');
Route::post('updateLinkWithAppLogo', 'AdminController@updateLinkWithAppLogo');
Route::group(['prefix' => '', 'middleware' => ['ability:admin|user,user_permission|user_permission']], function() {

    Route::post('user', 'UserController@index');

    Route::post('getAllCategory', 'AdminController@getAllCategory');
    Route::post('getSubCategoryByCategoryId', 'AdminController@getSubCategoryByCategoryId');
    Route::post('getCatalogBySubCategoryId', 'AdminController@getCatalogBySubCategoryId');
    Route::post('getImagesByCatalogId', 'AdminController@getImagesByCatalogId');
    Route::post('getFeaturedBackgroundCatalog', 'AdminController@getFeaturedBackgroundCatalog');
    Route::post('getFeaturedStickerCatalog', 'AdminController@getFeaturedStickerCatalog');
    Route::post('getFeaturedCatalogBySubCategoryId', 'AdminController@getFeaturedCatalogBySubCategoryId');
    Route::post('getSampleImagesForMobile', 'AdminController@getSampleImagesForMobile');
    Route::post('getBackgroundCatalogBySubCategoryId', 'AdminController@getBackgroundCatalogBySubCategoryId');
    Route::post('getLink', 'UserController@getLink');
    Route::post('getJsonData', 'UserController@getJsonData');
    Route::post('getJsonSampleData', 'UserController@getJsonSampleData');

    // Get Catalog with last_sync_date
    Route::post('getCatalogBySubCategoryIdWithLastSyncTime', 'UserController@getCatalogBySubCategoryIdWithLastSyncTime');
    Route::post('getJsonSampleDataWithLastSyncTime', 'UserController@getJsonSampleDataWithLastSyncTime');
    Route::post('getFeaturedJsonImages', 'UserController@getFeaturedJsonImages'); // get all featured images of catalog for json
    Route::post('getDeletedJsonId', 'UserController@getDeletedJsonId');
    Route::post('saveUserFeeds', 'UserController@saveUserFeeds');
    Route::post('getDeletedCatalogId', 'UserController@getDeletedCatalogId');

    Route::post('appPurchasePayment', 'SubscriptionPaymentController@appPurchasePayment');
    Route::post('appPurchasePaymentForIos', 'SubscriptionPaymentController@appPurchasePaymentForIos');

    Route::post('clearBadgeCountData', 'NotificationController@clearBadgeCountData');
    Route::post('verifyPromoCode', 'UserController@verifyPromoCode');

    //webp
    Route::post('getJsonSampleDataWithLastSyncTime_webp', 'UserController@getJsonSampleDataWithLastSyncTime_webp');
    Route::post('getJsonSampleDataWithLastSyncTime_webpIos', 'UserController@getJsonSampleDataWithLastSyncTime_webpIos');
    Route::post('getFeaturedJsonSampleData_webp', 'UserController@getFeaturedJsonSampleData_webp');

    //Fetch images from Pixabay
    Route::post('getImagesFromPixabay', 'PixabayController@getImagesFromPixabay');

    //Fetch videos from Pixabay
    Route::post('getVideosFromPixabay', 'PixabayController@getVideosFromPixabay');

    //Advertise Server Id
    Route::post('getAdvertiseServerIdForUser', 'UserController@getAdvertiseServerIdForUser');

    //Advertisements with last_sync_time
    Route::post('getLinkWithLastSyncTime', 'UserController@getLinkWithLastSyncTime');

    Route::post('addZipFile', 'UserController@addZipFile');

    //Search cards by sub_category_id
    Route::post('searchCardsBySubCategoryId', 'UserController@searchCardsBySubCategoryId');

    //Get host name
    Route::post('getHostName', 'AdminController@getHostName');

});

Route::post('getZipFile', 'UserController@getZipFile');

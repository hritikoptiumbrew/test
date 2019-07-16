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

/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/

//Logs Viewer
Route::get('logs/{user_name}/{password}', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');

//User login
Route::post('doLoginForGuest', 'LoginController@doLoginForGuest');

//Register user device
Route::post('registerUserDeviceByDeviceUdid', 'RegisterController@registerUserDeviceByDeviceUdid');

//Admin login
Route::post('doLogin', 'LoginController@doLogin');
Route::post('verify2faOPT', 'Google2faController@verify2faOPT');

//Get advertisement without token
Route::post('getLinkWithoutToken', 'UserController@getLinkWithoutToken');

//Statistics of current server
Route::post('getSummaryByAdmin', 'AdminController@getSummaryByAdmin');
Route::post('getSummaryByDateRange', 'AdminController@getSummaryByDateRange');
Route::post('getSummaryOfCatalogsByDateRange', 'AdminController@getSummaryOfCatalogsByDateRange');

//API of resume maker
Route::post('addQuestionAnswer','QnAController@addQuestionAnswer');
Route::post('updateQuestionAnswer','QnAController@updateQuestionAnswer');

//Add template by zip
Route::post('addTemplateByZip', 'ZipController@addTemplateByZip');
Route::post('getCatalogBySubCategoryList', 'ZipController@getCatalogBySubCategoryList'); //catalog list by subcategory

//API of Admin
Route::middleware(['ability:admin,admin_permission'])->group(function () {

    //Change password
    Route::post('changePassword', 'LoginController@changePassword');

    //Google 2fa API route
    Route::post('enable2faByAdmin', 'Google2faController@enable2faByAdmin');
    Route::post('disable2faByAdmin', 'Google2faController@disable2faByAdmin');

    //Promo code
    Route::post('addPromoCode', 'AdminController@addPromoCode');
    Route::post('getAllPromoCode', 'AdminController@getAllPromoCode');
    Route::post('searchPromoCode', 'AdminController@searchPromoCode');

    //Category
    Route::post('addCategory', 'AdminController@addCategory');
    Route::post('updateCategory', 'AdminController@updateCategory');
    Route::post('deleteCategory', 'AdminController@deleteCategory');
    Route::post('searchCategoryByName', 'AdminController@searchCategoryByName');
    Route::post('getAllCategory', 'AdminController@getAllCategory');

    //Sub-category
    Route::post('addSubCategory', 'AdminController@addSubCategory');
    Route::post('updateSubCategory', 'AdminController@updateSubCategory');
    Route::post('deleteSubCategory', 'AdminController@deleteSubCategory');
    Route::post('getAllSubCategory', 'AdminController@getAllSubCategory');
    Route::post('searchSubCategoryByName', 'AdminController@searchSubCategoryByName');

    //Catalog
    Route::post('addCatalog', 'AdminController@addCatalog');
    Route::post('updateCatalog', 'AdminController@updateCatalog');
    Route::post('deleteCatalog', 'AdminController@deleteCatalog');
    Route::post('searchCatalogByName', 'AdminController@searchCatalogByName');

    //Normal images
    Route::post('addCatalogImages', 'AdminController@addCatalogImages');
    Route::post('updateCatalogImage', 'AdminController@updateCatalogImage');

    //Featured images for background
    Route::post('addFeaturedBackgroundCatalogImage', 'AdminController@addFeaturedBackgroundCatalogImage');
    Route::post('updateFeaturedBackgroundCatalogImage', 'AdminController@updateFeaturedBackgroundCatalogImage');
    Route::post('getSampleImagesForAdmin', 'AdminController@getSampleImagesForAdmin');

    //Common for all content within a catalog
    Route::post('deleteCatalogImage', 'AdminController@deleteCatalogImage');
    Route::post('getDataByCatalogIdForAdmin', 'AdminController@getDataByCatalogIdForAdmin');

    //Link catalog into sub_category
    //Route::post('getAllCatalog', 'AdminController@getAllCatalog'); //Unused APIs
    Route::post('linkCatalog', 'AdminController@linkCatalog');
    Route::post('deleteLinkedCatalog', 'AdminController@deleteLinkedCatalog');
    Route::post('getAllSubCategoryForLinkCatalog', 'AdminController@getAllSubCategoryForLinkCatalog');

    //Move template
    Route::post('moveTemplate', 'AdminController@moveTemplate');
    Route::post('getAllSubCategoryToMoveTemplate', 'AdminController@getAllSubCategoryToMoveTemplate');

    //Advertise
    Route::post('addLink', 'AdminController@addLink');
    Route::post('updateLink', 'AdminController@updateLink');
    Route::post('deleteLink', 'AdminController@deleteLink');
    Route::post('getAllAdvertisements', 'AdminController@getAllAdvertisements');
    Route::post('getAllLink', 'AdminController@getAllLink');
    Route::post('getAdvertiseLink', 'AdminController@getAdvertiseLink');
    Route::post('addAdvertiseLink', 'AdminController@addAdvertiseLink');
    Route::post('unlinkAdvertise', 'AdminController@unlinkAdvertise');

    //User APIs
    Route::post('getUserProfile', 'LoginController@getUserProfile');
    Route::post('getAllUser', 'AdminController@getAllUser');
    Route::post('updateUserProfile', 'AdminController@updateUserProfile');
    Route::post('getPurchaseUser', 'AdminController@getPurchaseUser');
    Route::post('getAllRestoreDevice', 'AdminController@getAllRestoreDevice');
    Route::post('searchRestoreDevice', 'AdminController@searchRestoreDevice');
    Route::post('searchUser', 'AdminController@searchUser');
    Route::post('searchPurchaseUser', 'AdminController@searchPurchaseUser');

    //Image details
    Route::post('getImageDetails', 'AdminController@getImageDetails');

    //Template APIs
    Route::post('addCatalogImagesForJson', 'AdminController@addCatalogImagesForJson');
    Route::post('addJson', 'AdminController@addJson');
    Route::post('editJsonData', 'AdminController@editJsonData');

    //Link advertisement with another sub_category
    Route::post('linkAdvertisementWithSubCategory', 'AdminController@linkAdvertisementWithSubCategory');
    Route::post('deleteLinkedAdvertisement', 'AdminController@deleteLinkedAdvertisement');
    Route::post('getAllAdvertisementToLinkAdvertisement', 'AdminController@getAllAdvertisementToLinkAdvertisement');

    //API to add old application content via migration
    Route::post('addAppContentViaMigration', 'AdminController@addAppContentViaMigration');

    //Advertise Server_Id
    Route::post('addAdvertisementCategory', 'AdminController@addAdvertisementCategory'); //To add main 3 category of add like Banner/interstitial
    Route::post('editAdvertisementCategory', 'AdminController@editAdvertisementCategory');
    Route::post('deleteAdvertisementCategory', 'AdminController@deleteAdvertisementCategory');
    Route::post('getAllAdvertiseCategory', 'AdminController@getAllAdvertiseCategory');
    Route::post('addAdvertiseServerId', 'AdminController@addAdvertiseServerId');
    Route::post('updateAdvertiseServerId', 'AdminController@updateAdvertiseServerId');
    Route::post('deleteAdvertiseServerId', 'AdminController@deleteAdvertiseServerId');
    Route::post('getAdvertiseServerIdForAdmin', 'AdminController@getAdvertiseServerIdForAdmin');

    //Update all sample images using libwebp (to generate webp of all templates)
    Route::post('updateAllSampleImages', 'AdminController@updateAllSampleImages');

    //Search Tag
    Route::post('addTag', 'AdminController@addTag');
    Route::post('updateTag', 'AdminController@updateTag');
    Route::post('deleteTag', 'AdminController@deleteTag');
    Route::post('getAllTags', 'AdminController@getAllTags');

    //Font
    Route::post('getSamplesOfNonCommercialFont', 'AdminController@getSamplesOfNonCommercialFont'); //To get template by fontNAme/fontPath
    Route::post('addFont', 'AdminController@addFont');
    Route::post('editFont', 'AdminController@editFont');
    Route::post('deleteFont', 'AdminController@deleteFont');
    Route::post('getAllFontsByCatalogIdForAdmin', 'AdminController@getAllFontsByCatalogIdForAdmin');
    Route::post('addInvalidFont', 'AdminController@addInvalidFont'); //API without font file validation

    //Statistics of All servers
    Route::post('addServerUrl', 'AdminController@addServerUrl');
    Route::post('updateServerUrl', 'AdminController@updateServerUrl');
    Route::post('deleteServerUrl', 'AdminController@deleteServerUrl');
    Route::post('getAllServerUrls', 'AdminController@getAllServerUrls');
    Route::post('getSummaryDetailFromDiffServer', 'AdminController@getSummaryDetailFromDiffServer');//Statistics from other servers
    Route::post('getSummaryOfCatalogsFromDiffServer', 'AdminController@getSummaryOfCatalogsFromDiffServer');//Statistics from other servers by catalogs
    Route::post('getSummaryOfIndividualServerByAdmin', 'AdminController@getSummaryOfIndividualServerByAdmin');//Statistics from individual server

    //Set rank of catalogs & templates
    Route::post('setCatalogRankOnTheTopByAdmin', 'AdminController@setCatalogRankOnTheTopByAdmin');
    Route::post('setContentRankOnTheTopByAdmin', 'AdminController@setContentRankOnTheTopByAdmin');

    /*API to add search tags via image_url by sub_category_id with pagination
    Note: Don't use this API in live server because it will must effect on live cards*/
    Route::post('getSearchTagsForAllSampleImages', 'AdminController@getSearchTagsForAllSampleImages');
    Route::post('getSearchTagsForAllNormalImages', 'AdminController@getSearchTagsForAllNormalImages');

    //File validation
    Route::post('addValidation', 'AdminController@addValidation');
    Route::post('editValidation', 'AdminController@editValidation');
    Route::post('deleteValidation', 'AdminController@deleteValidation');
    Route::post('getAllValidationsForAdmin', 'AdminController@getAllValidationsForAdmin');

    //Send push notification
    Route::post('sendPushNotification', 'NotificationController@sendPushNotification');

    //Redis-cache
    Route::post('getRedisKeys', 'AdminController@getRedisKeys');
    Route::post('deleteRedisKeys', 'AdminController@deleteRedisKeys');
    Route::post('getRedisKeyDetail', 'AdminController@getRedisKeyDetail');
    Route::post('clearRedisCache', 'AdminController@clearRedisCache');

    //Show templates created by user from admin portal
    Route::post('getUserFeedsBySubCategoryId', 'UserController@getUserFeedsBySubCategoryId');
    Route::post('deleteUserFeeds', 'UserController@deleteUserFeeds');
    Route::post('deleteAllUserFeeds', 'UserController@deleteAllUserFeeds');
    Route::post('getHostName', 'AdminController@getHostName'); //Get host name

    /* ===================| APIs of resume maker (created by Pooja Jadav) |=================== */

    //Get youtube videos related to interview
    Route::post('addYouTubeVideoURL','VideoController@addYouTubeVideoURL');
    Route::post('updateYouTubeVideoURL','VideoController@updateYouTubeVideoURL');
    Route::post('deleteYouTubeVideoURL','VideoController@deleteYouTubeVideoURL');
    Route::post('getYouTubeVideoForInterviewForAdmin','VideoController@getYouTubeVideoForInterviewForAdmin');
    Route::post('getVideoIdByURL','VideoController@getVideoIdByURL');

    //Interview questions & answers
    Route::post('addQuestionType','QnAController@addQuestionType');
    Route::post('updateQuestionType','QnAController@updateQuestionType');
    Route::post('deleteQuestionType','QnAController@deleteQuestionType');
    Route::post('getAllQuestionTypeForAdmin','QnAController@getAllQuestionTypeForAdmin');
    Route::post('deleteQuestionAnswer','QnAController@deleteQuestionAnswer');
    Route::post('getAllQuestionAnswer','QnAController@getAllQuestionAnswer');
    Route::post('getAllQuestionAnswerByTypeForAdmin','QnAController@getAllQuestionAnswerByTypeForAdmin');
    Route::post('searchQuestionAnswerForAdmin','QnAController@searchQuestionAnswerForAdmin');

});

//Common api
Route::middleware(['ability:user|admin,user_permission|admin_permission'])->group(function () {

    //doLogout
    Route::post('doLogout', 'LoginController@doLogout');

    //Sub_category & catalogs
    Route::post('getSubCategoryByCategoryId', 'AdminController@getSubCategoryByCategoryId');
    Route::post('getCatalogBySubCategoryId', 'AdminController@getCatalogBySubCategoryId');

});

//API of User
Route::middleware(['ability:user,user_permission'])->group(function () {

    Route::post('user', 'UserController@index');

    //Template APIs
    Route::post('getJsonData', 'UserController@getJsonData');
    Route::post('getJsonSampleData', 'UserController@getJsonSampleData');
    Route::post('getContentByCatalogId', 'UserController@getContentByCatalogId');
    Route::post('getFeaturedJsonImages', 'UserController@getFeaturedJsonImages'); // get all featured images of catalog for json
    Route::post('getDeletedJsonId', 'UserController@getDeletedJsonId');
    Route::post('searchCardsBySubCategoryId', 'UserController@searchCardsBySubCategoryId'); //Search cards by sub_category_id

    //Normal images
    Route::post('getImagesByCatalogId', 'UserController@getImagesByCatalogId');

    //Get data with last_sync_date
    Route::post('getCatalogBySubCategoryIdWithLastSyncTime', 'UserController@getCatalogBySubCategoryIdWithLastSyncTime');
    Route::post('getCatalogBySubCategoryIdWithWebp', 'UserController@getCatalogBySubCategoryIdWithWebp'); //get list of catalogs with pagination
    Route::post('getJsonSampleDataWithLastSyncTime', 'UserController@getJsonSampleDataWithLastSyncTime');
    Route::post('getLinkWithLastSyncTime', 'UserController@getLinkWithLastSyncTime'); //Advertisements with last_sync_time

    //Catalogs
    Route::post('getBackgroundCatalogBySubCategoryId', 'UserController@getBackgroundCatalogBySubCategoryId');
    Route::post('getFeaturedCatalogBySubCategoryId', 'UserController@getFeaturedCatalogBySubCategoryId');
    Route::post('getDeletedCatalogId', 'UserController@getDeletedCatalogId');
    Route::post('getCatalogsByType', 'UserController@getCatalogsByType'); //To get jpg/png catalog images
    Route::post('getCatalogsByTypeInWebp', 'UserController@getCatalogsByTypeInWebp'); //To get webp catalog image

    //Featured images for background (Background changer)
    Route::post('getSampleImagesForMobile', 'UserController@getSampleImagesForMobile');

    //API with webp images
    Route::post('getJsonSampleDataWithLastSyncTime_webp', 'UserController@getJsonSampleDataWithLastSyncTime_webp');
    Route::post('getJsonSampleDataWithLastSyncTime_webpIos', 'UserController@getJsonSampleDataWithLastSyncTime_webpIos');
    Route::post('getFeaturedJsonSampleData_webp', 'UserController@getFeaturedJsonSampleData_webp');
    Route::post('getAllSamplesWithWebp', 'UserController@getAllSamplesWithWebp');

    //Fonts
    Route::post('getAllFontsByCatalogId', 'UserController@getAllFontsByCatalogId');

    //Get advertise link
    Route::post('getLink', 'UserController@getLink');

    //Get templates for Brand Maker
    Route::post('getJsonSampleDataFilterBySearchTag', 'UserController@getJsonSampleDataFilterBySearchTag'); //To get templates divide by categories

    //Get templates for Brochure Maker with catalogs
    Route::post('getFeaturedSamplesWithCatalogs', 'UserController@getFeaturedSamplesWithCatalogs'); //To get featured templates with catalogs
    Route::post('getTemplateWithCatalogs', 'UserController@getTemplateWithCatalogs'); //To get all templates with catalogs

    //Fetch images from Pixabay
    Route::post('getImagesFromPixabay', 'PixabayController@getImagesFromPixabay');
    Route::post('getImageByUnsplash', 'UnsplashController@getImageByUnsplash');

    //Save templates created by user
    Route::post('saveUserFeeds', 'UserController@saveUserFeeds');

    //Promo code
    Route::post('verifyPromoCode', 'UserController@verifyPromoCode');

    //Advertise Server Id
    Route::post('getAdvertiseServerIdForUser', 'UserController@getAdvertiseServerIdForUser');

    //Payment (unused APIs)
    Route::post('appPurchasePayment', 'SubscriptionPaymentController@appPurchasePayment');
    Route::post('appPurchasePaymentForIos', 'SubscriptionPaymentController@appPurchasePaymentForIos');

    //To clear badge count of push notification
    Route::post('clearBadgeCountData', 'NotificationController@clearBadgeCountData');


    /* ===================| APIs of resume maker (created by Pooja Jadav) |=================== */

    //Resume maker job search
    Route::post('getHomePageDetail','UserController@getHomePageDetail');
    Route::post('jobMultiSearchByUser','UserController@jobMultiSearchByUser');
    Route::post('jobMultiSearchByUserIndividually','UserController@jobMultiSearchByUserIndividually');

    Route::post('getFeedFromTwitter','NewsController@getFeedFromTwitter');
    Route::post('getFeedFromTwitter_test','NewsController@getFeedFromTwitter_test');

    Route::post('getAllQuestionAnswerByType','QnAController@getAllQuestionAnswerByType');
    Route::post('searchQuestionAnswer','QnAController@searchQuestionAnswer');
    Route::post('getYouTubeVideoForInterview','VideoController@getYouTubeVideoForInterview');

    Route::post('getAllQuestionType','QnAController@getAllQuestionType');


});


//APIs of debug purpose
Route::post('getDatabaseInfo', 'AdminController@getDatabaseInfo');
Route::post('getConstants', 'AdminController@getConstants');
Route::post('runArtisanCommands', 'AdminController@runArtisanCommands');
Route::post('storeFileIntoS3Bucket', 'AdminController@storeFileIntoS3Bucket');
Route::post('testMail', 'AdminController@testMail');
Route::post('getPhpInfo', 'AdminController@getPhpInfo');

//API to get fonts into font_collection (for designer use)
Route::post('getAllFonts', 'AdminController@getAllFonts');
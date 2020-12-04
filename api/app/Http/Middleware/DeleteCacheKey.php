<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Redis;
use Log;
use Illuminate\Http\Request;
use Mockery\CountValidator\Exception;


class DeleteCacheKey
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        /*$api = $request->getPathInfo();
        if ($api != "/api/logs/admin@gmail.com/demo@123") {
            Log::info("apicall :",[$api]);
        }*/
        return $next($request);
    }

    public function terminate(Request $request)
    {
        try {
            $api = $request->getPathInfo();

            //Category
            if ($api == '/api/addCategory' or $api == '/api/updateCategory' or $api == '/api/deleteCategory') {

                //All Category Key
                $keys = Redis::keys('pel:getAllCategory*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }
                /*if (count($keys) === 0) {
                    //Log::info("pel:getAllCategory Key Deleted");
                }*/

                //Delete Image details View Key
                $keys = Redis::keys('pel:getImageDetails*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }
                /*if (count($keys) === 0) {
                    //Log::info("pel:getImageDetails Key Deleted");
                }*/

                //getAllValidationsForAdmin
                $keys = Redis::keys('pel:getAllValidationsForAdmin*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }

            }

            //Sub-Category
            if ($api == '/api/addSubCategory' or $api == '/api/updateSubCategory' or $api == '/api/deleteSubCategory') {

                //All Sub Category Key
                $keys = Redis::keys('pel:getAllSubCategory*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }

                //getSubCategoryByCategoryId Key
                $keys = Redis::keys('pel:getSubCategoryByCategoryId*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }

                //Delete Image details View Key
                $keys = Redis::keys('pel:getImageDetails*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }

                //getAllSubCategoryToMoveTemplate
                $keys = Redis::keys('pel:getAllSubCategoryToMoveTemplate*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }

                //getCatalogBySubCategoryList
                $keys = Redis::keys('pel:getCatalogBySubCategoryList*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }

            }

            //Catalog-Category
            if ($api == '/api/addCatalog' or $api == '/api/updateCatalog' or $api == '/api/deleteCatalog' or $api == '/api/linkCatalog' or $api == '/api/deleteLinkedCatalog' or $api == '/api/setCatalogRankOnTheTopByAdmin') {


                //getFeaturedCatalogBySubCategoryId
                $keys = Redis::keys('pel:getFeaturedCatalogBySubCategoryId*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }


                //getCatalogBySubCategoryId
                $keys = Redis::keys('pel:getCatalogBySubCategoryId*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }

                //getCatalogBySubCategoryIdForAutoUpload
                $keys = Redis::keys('vf:getCatalogBySubCategoryIdForAutoUpload*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }

                //getCatalogBySubCategoryId_v2
                $keys = Redis::keys('pel:getCatalogBySubCategoryId_v2*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }

                //getOfflineFontCatalogs
                $keys = Redis::keys('pel:getOfflineFontCatalogs*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }

                //remove keys of getCatalogsByType & getCatalogsByTypeInWebp api
                $keys = Redis::keys('pel:getCatalogsByType*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }

                //getFeaturedJsonSampleData_webp
                $keys = Redis::keys('pel:getFeaturedJsonSampleData_webp*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }

                //getAllSubCategoryForLinkCatalog
                $keys = Redis::keys('pel:getAllSubCategoryForLinkCatalog*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }

                //getBackgroundCatalogBySubCategoryId
                $keys = Redis::keys('pel:getBackgroundCatalogBySubCategoryId*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }

                //Delete Image details View Key
                $keys = Redis::keys('pel:getImageDetails*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }

                //getFeaturedSamplesWithCatalogs
                $keys = Redis::keys('pel:getFeaturedSamplesWithCatalogs*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }

                //getAllSubCategoryToMoveTemplate
                $keys = Redis::keys('pel:getAllSubCategoryToMoveTemplate*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }

                //getCatalogBySubCategoryList
                $keys = Redis::keys('pel:getCatalogBySubCategoryList*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }

                //getTemplateWithCatalogs
                $keys = Redis::keys('pel:getTemplateWithCatalogs*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }

                //searchNormalImagesBySubCategoryId
                $keys = Redis::keys('pel:searchNormalImagesBySubCategoryId*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }

                //searchNormalImagesBySubCategoryId_v2
                $keys = Redis::keys('pel:searchNormalImagesBySubCategoryId_v2*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }

               //searchNormalImagesBySubCategoryIdForFlyer
                $keys = Redis::keys('pel:searchNormalImagesBySubCategoryIdForFlyer*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }

                //searchCardsBySubCategoryId
                $keys = Redis::keys('pel:searchCardsBySubCategoryId*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }

                //searchCardsBySubCategoryId_v2
                $keys = Redis::keys('pel:searchCardsBySubCategoryId_v2*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }

                //getFeaturedTemplatesWithWebp
                $keys = Redis::keys('pel:getFeaturedTemplatesWithWebp*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }

                //getAllFeaturedTemplatesWithShuffling
                $keys = Redis::keys('pel:getAllFeaturedTemplatesWithShuffling*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }

                //searchCatalogByUser
                $keys = Redis::keys('pel:searchCatalogByUser*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }

            }

            //Sub Category Images
            if ($api == '/api/addCatalogImages' or $api == '/api/updateCatalogImage' or $api == '/api/deleteCatalogImage' or $api == '/api/addFeaturedBackgroundCatalogImage' or $api == '/api/updateFeaturedBackgroundCatalogImage' or $api == '/api/addJson' or $api == '/api/addCatalogImagesForJson' or $api == '/api/editJsonData' or $api == '/api/updateAllSampleImages' or $api == '/api/setContentRankOnTheTopByAdmin' or $api == '/api/getSearchTagsForAllSampleImages' or $api == '/api/getSearchTagsForAllNormalImages' or $api == '/api/moveTemplate' or $api == '/api/addTemplateByZip' or $api == '/api/addCategoryNameAsTag'  or $api == '/api/autoUploadTemplate') {

                //Category Wise Images Key
                $keys = Redis::keys('pel:getImagesByCatalogId*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }

                //getContentByCatalogId
                $keys = Redis::keys('pel:getContentByCatalogId*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }

                //getContentByCatalogId_v2
                $keys = Redis::keys('pel:getContentByCatalogId_v2*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }

                //getJsonSampleDataFilterBySearchTag
                $keys = Redis::keys('pel:getJsonSampleDataFilterBySearchTag*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }

                //searchCardsBySubCategoryId
                $keys = Redis::keys('pel:searchCardsBySubCategoryId*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }

                //searchCardsBySubCategoryId_v2
                $keys = Redis::keys('pel:searchCardsBySubCategoryId_v2*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }

                //getDataByCatalogIdForAdmin
                $keys = Redis::keys('pel:getDataByCatalogIdForAdmin*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }

                //getJsonData
                $keys = Redis::keys('pel:getJsonData*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }

                //getJsonSampleData
                $keys = Redis::keys('pel:getJsonSampleData*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }

                //getJsonSampleDataWithLastSyncTime & getJsonSampleDataWithLastSyncTime_webp
                $keys = Redis::keys('pel:getJsonSampleDataWithLastSyncTime*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }

               //getJsonSampleDataWithLastSyncTime_webp_v2
                $keys = Redis::keys('pel:getJsonSampleDataWithLastSyncTime_webp_v2*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }

                //getAllSamplesWithWebp
                $keys = Redis::keys('pel:getAllSamplesWithWebp*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }

                //getHomePageDetail
                $keys = Redis::keys('pel:getHomePageDetail*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }

                //getFeaturedJsonSampleData_webp
                $keys = Redis::keys('pel:getFeaturedJsonSampleData_webp*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }

                //getFeaturedJsonImages
                $keys = Redis::keys('pel:getFeaturedJsonImages*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }

                //getSampleImagesForAdmin
                $keys = Redis::keys('pel:getSampleImagesForAdmin*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }

                //getSampleImagesForMobile
                $keys = Redis::keys('pel:getSampleImagesForMobile*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }

                //Delete Image details View Key
                $keys = Redis::keys('pel:getImageDetails*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }

                //getFeaturedSamplesWithCatalogs
                $keys = Redis::keys('pel:getFeaturedSamplesWithCatalogs*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }

                //getFeaturedSampleAndCatalogWithWebp
                $keys = Redis::keys('pel:getFeaturedSampleAndCatalogWithWebp*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }

                //getAllSubCategoryToMoveTemplate
                $keys = Redis::keys('pel:getAllSubCategoryToMoveTemplate*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }

                //getTemplateWithCatalogs
                $keys = Redis::keys('pel:getTemplateWithCatalogs*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }

                //getTemplatesBySubCategoryTags
                $keys = Redis::keys('pel:getTemplatesBySubCategoryTags*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }

                //getTemplatesBySubCategoryTags_v2
                $keys = Redis::keys('pel:getTemplatesBySubCategoryTags_v2*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }

                //getCategoryTagBySubCategoryId
                $keys = Redis::keys('pel:getCategoryTagBySubCategoryId*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }

                //searchNormalImagesBySubCategoryId
                $keys = Redis::keys('pel:searchNormalImagesBySubCategoryId*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }

                //searchNormalImagesBySubCategoryId_v2
                $keys = Redis::keys('pel:searchNormalImagesBySubCategoryId_v2*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }

                //searchNormalImagesBySubCategoryIdForFlyer
                $keys = Redis::keys('pel:searchNormalImagesBySubCategoryIdForFlyer*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }

                //getFeaturedTemplatesWithWebp
                $keys = Redis::keys('pel:getFeaturedTemplatesWithWebp*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }
                //getAllFeaturedTemplatesWithShuffling
                $keys = Redis::keys('pel:getAllFeaturedTemplatesWithShuffling*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }

            }

            //Other
            if ($api == '/api/appPurchasePayment' or $api == '/api/appPurchasePaymentForIos' or $api == '/api/registerUserDeviceByDeviceUdid') {

                //All user Key
                $keys = Redis::keys('pel:getAllUser*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }

                //Purchase User Key
                $keys = Redis::keys('pel:getPurchaseUser*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }

                //All Restore Device Key
                $keys = Redis::keys('pel:getAllRestoreDevice*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }

            }

            //saveUserFeeds
            if ($api == '/api/saveUserFeeds' or $api == '/api/deleteUserFeeds' or $api == '/api/deleteAllUserFeeds') {

                //getUserFeedsBySubCategoryId
                $keys = Redis::keys('pel:getUserFeedsBySubCategoryId*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }
            }

            //getAdvertiseServerIdForAdmin
            if ($api == '/api/addAdvertiseServerId' or $api == '/api/updateAdvertiseServerId' or $api == '/api/deleteAdvertiseServerId' or $api == '/api/addAdvertisementCategory' or $api == '/api/editAdvertisementCategory' or $api == '/api/deleteAdvertisementCategory') {

                //getAllAdvertiseCategory
                $keys = Redis::keys('pel:getAllAdvertiseCategory*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }

                //getAdvertiseServerIdForAdmin
                $keys = Redis::keys('pel:getAdvertiseServerIdForAdmin*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }

            }

            //Advertise Link
            if ($api == '/api/addLink' or $api == '/api/updateLink' or $api == '/api/deleteLink' or $api == '/api/addAdvertiseLink' or $api == '/api/unlinkAdvertise' or $api == '/api/linkAdvertisementWithSubCategory' or $api == '/api/deleteLinkedAdvertisement') {

                //getAllLink
                $keys = Redis::keys('pel:getAllLink*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }

                //getLink
                $keys = Redis::keys('pel:getLink*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }

                //getLinkWithoutToken
                $keys = Redis::keys('pel:getLinkWithoutToken*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }

                //getLinkWithLastSyncTime
                $keys = Redis::keys('pel:getLinkWithLastSyncTime*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }

                //Delete Image details View Key
                $keys = Redis::keys('pel:getImageDetails*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }

                //getAdvertiseLink
                $keys = Redis::keys('pel:getAdvertiseLink*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }

                //getAllAdvertisementForLinkAdvertisement
                $keys = Redis::keys('pel:getAllAdvertisementForLinkAdvertisement*');
                //Log::info("pel:getAllAdvertisementForLinkAdvertisement Key Deleted",['key' =>$keys]);
                foreach ($keys as $key) {
                    Redis::del($key);
                }

                //getAllAdvertisementToLinkAdvertisement
                $keys = Redis::keys('pel:getAllAdvertisementToLinkAdvertisement*');
                //Log::info("pel:getAllAdvertisementToLinkAdvertisement Key Deleted",['key' =>$keys]);
                foreach ($keys as $key) {
                    Redis::del($key);
                }

                //getAllAdvertisements
                $keys = Redis::keys('pel:getAllAdvertisements*');
                //Log::info("pel:getAllAdvertisements Key Deleted",['key' =>$keys]);
                foreach ($keys as $key) {
                    Redis::del($key);
                }

            }

            //Search Tags
            if ($api == '/api/addTag' or $api == '/api/updateTag' or $api == '/api/deleteTag') {

                //getAllTags
                $keys = Redis::keys('pel:getAllTags*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }
            }

            //ServerUrl
            if ($api == '/api/addServerUrl' or $api == '/api/updateServerUrl' or $api == '/api/deleteServerUrl') {

                //getAllServerUrls
                $keys = Redis::keys('pel:getAllServerUrls*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }
            }

            //Question type
            if ($api == '/api/addQuestionType' or $api == '/api/updateQuestionType' or $api == '/api/deleteQuestionType') {

                //getAllQuestionType
                $keys = Redis::keys('pel:getAllQuestionType*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }

                //getHomePageDetail
                $keys = Redis::keys('pel:getHomePageDetail*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }
            }

            //Question and answer
            if ($api == '/api/addQuestionAnswer' or $api == '/api/updateQuestionAnswer' or $api == '/api/deleteQuestionAnswer') {

                //get Category
                $keys = Redis::keys('pel:getAllQuestionAnswer*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }
            }

            //Youtube Video module
            if ($api == '/api/addYouTubeVideoURL' or $api == '/api/updateYouTubeVideoURL' or $api == '/api/deleteYouTubeVideoURL') {

                //get Category
                $keys = Redis::keys('pel:getYouTubeVideo*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }

                //getHomePageDetail
                $keys = Redis::keys('pel:getHomePageDetail*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }
            }

            //Font Module
            if ($api == '/api/addFont' or $api == '/api/editFont' or $api == '/api/deleteFont' or $api == '/api/addInvalidFont' or $api == '/api/removeInvalidFont') {

                /*//getAllFontsByCatalogIdForAdmin
                $keys = Redis::keys('pel:getAllFontsByCatalogIdForAdmin*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }

                //getAllFontsByCatalogId
                $keys = Redis::keys('pel:getAllFontsByCatalogId*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }*/

                //getAllFontsByCatalogId, getAllFontsByCatalogIdForAdmin & getAllFonts
                $keys = Redis::keys('pel:getAllFonts*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }

                //getOfflineFontCatalogs
                $keys = Redis::keys('pel:getOfflineFontCatalogs*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }

                //getCorruptedFontList
                $keys = Redis::keys('pel:getCorruptedFontList*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }

            }

            //Search Category Tags
            if ($api == '/api/addSearchCategoryTag' or $api == '/api/updateSearchCategoryTag' or $api == '/api/deleteSearchCategoryTag' or $api == '/api/setCategoryTagRankOnTheTopByAdmin') {

                //getCategoryTagBySubCategoryId
                $keys = Redis::keys('pel:getCategoryTagBySubCategoryId*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }

                //getTemplatesBySubCategoryTags
                $keys = Redis::keys('pel:getTemplatesBySubCategoryTags*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }

                //getTemplatesBySubCategoryTags_v2
                $keys = Redis::keys('pel:getTemplatesBySubCategoryTags_v2*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }
            }

            //Validations for file size
            if ($api == '/api/addValidation' or $api == '/api/editValidation' or $api == '/api/deleteValidation') {

                //getAllValidationsForAdmin
                $keys = Redis::keys('pel:getAllValidationsForAdmin*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }

                //getAllValidationsForAdminForAutoUpload
                $keys = Redis::keys('pel:getAllValidationsForAdminForAutoUpload*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }

                //getValidationFromCache
                $keys = Redis::keys('pel:getValidationFromCache*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }
            }

            //blog module
            if ($api == '/api/addBlogContent' or $api == '/api/updateBlogContent' or $api == '/api/deleteBlogContent' or $api == '/api/setBlogRankOnTheTopByAdmin') {

                //getBlogContent
                $keys = Redis::keys('pel:getBlogContent*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }

                //getBlogListByUser
                $keys = Redis::keys('pel:getBlogListByUser*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }

                //getBlogContentByIdForUser
                $keys = Redis::keys('pel:getBlogContentByIdForUser*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }
            }

            //only use brand-maker search module
            if($api == '/api/updateTagForBrandSearch'){

                //getAllSamplesWithWebp
                $keys = Redis::keys('pel:getAllSamplesWithWebp*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }
            }

        } catch (Exception $e) {
            Log::error("DeleteCacheKey Middleware : ", ['Error : ' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
            return Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'Delete Cache Key.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
    }
}

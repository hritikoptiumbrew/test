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
        $api = $request->getPathInfo();
        IF($api != "/api/logs/admin@gmail.com/demo@123"){
            //Log::info("apicall :",[$api]);
        }
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
                if (count($keys) === 0) {
                    //Log::info("pel:getAllCategory Key Deleted");
                }

                //Category Wise Images Key
                $keys = Redis::keys('pel:getImagesByCategoryId*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }
                if (count($keys) === 0) {
                    //Log::info("pel:getImagesByCategoryId Key Deleted");
                }

                //Delete Image details View Key
                $keys = Redis::keys('pel:getImageDetails*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }
                if (count($keys) === 0) {
                    //Log::info("pel:getImageDetails Key Deleted");
                }

            }

            //Sub-Category
            if ($api == '/api/addSubCategory' or $api == '/api/updateSubCategory' or $api == '/api/deleteSubCategory') {

                //All Sub Category Key
                $keys = Redis::keys('pel:getAllSubCategory*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }
                if (count($keys) === 0) {
                    //Log::info("pel:getAllSubCategory Key Deleted");
                }


                //getCatalogCategory Key
                $keys = Redis::keys('pel:getSubCategoryByCategoryId*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }
                if (count($keys) === 0) {
                    //Log::info("pel:getSubCategoryByCategoryId Key Deleted");
                }


                //getFeaturedCategory Key
                $keys = Redis::keys('pel:getFeaturedCategory*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }
                if (count($keys) === 0) {
                    //Log::info("pel:getFeaturedCategory Key Deleted");
                }

                //getBackgroundCategory
                $keys = Redis::keys('pel:getBackgroundCategory*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }
                if (count($keys) === 0) {
                    //Log::info("pel:getBackgroundCategory Key Deleted");
                }

                //getStickerCategory
                $keys = Redis::keys('pel:getStickerCategory*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }
                if (count($keys) === 0) {
                    //Log::info("pel:getStickerCategory Key Deleted");
                }

                //getFrameCategory
                $keys = Redis::keys('pel:getFrameCategory*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }
                if (count($keys) === 0) {
                    //Log::info("pel:getFrameCategory Key Deleted");
                }

                //Delete Image details View Key
                $keys = Redis::keys('pel:getImageDetails*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }
                if (count($keys) === 0) {
                    //Log::info("pel:getImageDetails Key Deleted");
                }


            }

            //Catalog-Category
            if ($api == '/api/addCatalog' or $api == '/api/updateCatalog' or $api == '/api/deleteCatalog' or $api == '/api/linkCatalog' or $api == '/api/deleteLinkedCatalog' or $api=='/api/addFeaturedBackgroundCatalogImage' or $api=='/api/updateFeaturedBackgroundCatalogImage') {



                //getFeaturedBackgroundCatalog
                $keys = Redis::keys('pel:getFeaturedCatalogBySubCategoryId*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }
                if (count($keys) === 0) {
                    //Log::info("pel:getFeaturedCatalogBySubCategoryId Key Deleted");
                }

                //getFeaturedBackgroundCatalog
                $keys = Redis::keys('pel:getFeaturedBackgroundCatalog*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }
                if (count($keys) === 0) {
                    //Log::info("pel:getFeaturedBackgroundCatalog Key Deleted");
                }

                //getCatalogBySubCategoryId
                $keys = Redis::keys('pel:getCatalogBySubCategoryId*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }
                if (count($keys) === 0) {
                    //Log::info("pel:getCatalogBySubCategoryId Key Deleted");
                }

                //getSampleImagesForAdmin
                $keys = Redis::keys('pel:getSampleImagesForAdmin*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }
                if (count($keys) === 0) {
                    //Log::info("pel:getSampleImagesForAdmin Key Deleted");
                }

                //getSampleImagesForMobile
                $keys = Redis::keys('pel:getSampleImagesForMobile*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }
                if (count($keys) === 0) {
                    //Log::info("pel:getSampleImagesForMobile Key Deleted");
                }


                $keys = Redis::keys('pel:getAllSubCategoryForLinkCatalog*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }
                if (count($keys) === 0) {
                   //Log::info("pel:getAllSubCategoryForLinkCatalog Key Deleted");
                }


                //getBackgroundCatalog
                $keys = Redis::keys('pel:getBackgroundCatalog*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }
                if (count($keys) === 0) {
                    //Log::info("pel:getBackgroundCatalog Key Deleted");
                }

                //getBackgroundCatalogBySubCategoryId
                $keys = Redis::keys('pel:getBackgroundCatalogBySubCategoryId*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }
                if (count($keys) === 0) {
                    //Log::info("pel:getBackgroundCatalogBySubCategoryId Key Deleted");
                }

                //getStickerCatalog
                $keys = Redis::keys('pel:getStickerCatalog*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }
                if (count($keys) === 0) {
                    //Log::info("pel:getStickerCatalog Key Deleted");
                }

                //getFrameCatalog
                $keys = Redis::keys('pel:getFrameCatalog*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }
                if (count($keys) === 0) {
                    //Log::info("pel:getFrameCatalog Key Deleted");
                }


                //All Sub Category Key
                $keys = Redis::keys('pel:getAllSubCategory*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }
                if (count($keys) === 0) {
                    //Log::info("pel:getAllSubCategory Key Deleted");
                }


                //getCatalogCategory Key
                $keys = Redis::keys('pel:getCatalogBySubCategoryId*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }
                if (count($keys) === 0) {
                    //Log::info("pel:getCatalogBySubCategoryId Key Deleted");
                }

                //Delete Image details View Key
                $keys = Redis::keys('pel:getImageDetails*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }
                if (count($keys) === 0) {
                    //Log::info("pel:getImageDetails Key Deleted");
                }

            }

            //Sub Category Images
            if ($api == '/api/addCatalogImages' or $api == '/api/updateCatalogImage' or $api == '/api/deleteCatalogImage' or $api =='/api/addFeaturedBackgroundCatalogImage' or $api =='/api/updateFeaturedBackgroundCatalogImage' or $api =='/api/addJson' or $api =='/api/addCatalogImagesForJson' or $api =='/api/editJsonData' or $api =='/api/updateAllSampleImages') {

                //Category Wise Images Key
                $keys = Redis::keys('pel:getImagesByCatalogId*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }
                if (count($keys) === 0) {
                    //Log::info("pel:getImagesByCatalogId Key Deleted");
                }

                //getImagesByCatalogIdForAdmin
                $keys = Redis::keys('pel:getImagesByCatalogIdForAdmin*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }
                if (count($keys) === 0) {
                    //Log::info("pel:getImagesByCatalogIdForAdmin Key Deleted");
                }

                //Category Wise Images Key
                $keys = Redis::keys('pel:getCatalogBySubCategoryIdWithLastSyncTime*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }
                if (count($keys) === 0) {
                    //Log::info("pel:getCatalogBySubCategoryIdWithLastSyncTime Key Deleted");
                }

                //Category Wise Images Key
                $keys = Redis::keys('pel:getJsonData*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }
                if (count($keys) === 0) {
                    //Log::info("pel:getJsonData Key Deleted");
                }

                //Category Wise Images Key
                $keys = Redis::keys('pel:getJsonSampleData*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }
                if (count($keys) === 0) {
                    //Log::info("pel:getJsonSampleData Key Deleted");
                }

                //getJsonSampleDataWithLastSyncTime
                $keys = Redis::keys('pel:getJsonSampleDataWithLastSyncTime*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }
                if (count($keys) === 0) {
                    //Log::info("pel:getJsonSampleDataWithLastSyncTime Key Deleted");
                }

                //getJsonSampleDataWithLastSyncTime_webp
                $keys = Redis::keys('pel:getJsonSampleDataWithLastSyncTime_webp*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }
                if (count($keys) === 0) {
                    //Log::info("pel:getJsonSampleDataWithLastSyncTime_webp Key Deleted");
                }

                //getJsonSampleDataWithLastSyncTime_webpIos
                $keys = Redis::keys('pel:getJsonSampleDataWithLastSyncTime_webpIos*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }
                if (count($keys) === 0) {
                    //Log::info("pel:getJsonSampleDataWithLastSyncTime_webpIos Key Deleted");
                }

                //getFeaturedJsonSampleData_webp
                $keys = Redis::keys('pel:getFeaturedJsonSampleData_webp*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }
                if (count($keys) === 0) {
                    //Log::info("pel:getFeaturedJsonSampleData_webp Key Deleted");
                }

                //Category Wise Images Key
                $keys = Redis::keys('pel:getFeaturedJsonImages*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }
                if (count($keys) === 0) {
                    //Log::info("pel:getFeaturedJsonImages Key Deleted");
                }

                //getSampleImagesForAdmin
                $keys = Redis::keys('pel:getSampleImagesForAdmin*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }
                if (count($keys) === 0) {
                    //Log::info("pel:getSampleImagesForAdmin Key Deleted");
                }

                //getSampleImagesForMobile
                $keys = Redis::keys('pel:getSampleImagesForMobile*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }
                if (count($keys) === 0) {
                    //Log::info("pel:getSampleImagesForMobile Key Deleted");
                }

                //getFeaturedCategory
                $keys = Redis::keys('pel:getFeaturedImages*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }
                if (count($keys) === 0) {
                    //Log::info("pel:getFeaturedImages Key Deleted");
                }

                //getFeaturedBackgroundCatalog
                $keys = Redis::keys('pel:getFeaturedBackgroundCatalog*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }
                if (count($keys) === 0) {
                    //Log::info("pel:getFeaturedBackgroundCatalog Key Deleted");
                }

                //getFeaturedStickerCatalog
                $keys = Redis::keys('pel:getFeaturedStickerCatalog*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }
                if (count($keys) === 0) {
                    //Log::info("pel:getFeaturedStickerCatalog Key Deleted");
                }

                //Delete Image details View Key
                $keys = Redis::keys('pel:getImageDetails*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }
                if (count($keys) === 0) {
                    //Log::info("pel:getImageDetails Key Deleted");
                }


            }

            //Other
            if ($api == '/api/appPurchasePayment' or $api == '/api/appPurchasePaymentForIos' or $api == '/api/registerUserDeviceByDeviceUdid') {

                //All user Key
                $keys = Redis::keys('pel:getAllUser*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }
                if (count($keys) === 0) {
                    //Log::info("pel:getAllUser Key Deleted");
                }

                //Purchase User Key
                $keys = Redis::keys('pel:getPurchaseUser*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }
                if (count($keys) === 0) {
                   //Log::info("pel:getPurchaseUser Key Deleted");
                }

                //All Restore Device Key
                $keys = Redis::keys('pel:getAllRestoreDevice*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }
                if (count($keys) === 0) {
                    //Log::info("pel:getAllRestoreDevice Key Deleted");
                }

            }

            //saveUserFeeds
            if ($api == '/api/saveUserFeeds' or $api == '/api/deleteUserFeeds' or $api == '/api/deleteAllUserFeeds') {

                //getAllLink
                $keys = Redis::keys('pel:getUserFeedsBySubCategoryId*');
                //Log::info("pel:getUserFeedsBySubCategoryId Key Deleted",['key' =>$keys]);
                foreach ($keys as $key) {
                    Redis::del($key);
                }
                if (count($keys) === 0) {
                    //Log::info("pel:getUserFeedsBySubCategoryId Key Deleted");
                }
            }

            //getAdvertiseServerIdForAdmin
            if ($api == '/api/addAdvertiseServerId' or $api == '/api/updateAdvertiseServerId' or $api == '/api/deleteAdvertiseServerId' or $api == '/api/addAdvertisementCategory' or $api == '/api/editAdvertisementCategory' or $api == '/api/deleteAdvertisementCategory') {

                //getAllAdvertiseCategory
                $keys = Redis::keys('pel:getAllAdvertiseCategory*');
                //Log::info("pel:getAllAdvertiseCategory Key Deleted",['key' =>$keys]);
                foreach ($keys as $key) {
                    Redis::del($key);
                }
                if (count($keys) === 0) {
                    //Log::info("pel:getAllAdvertiseCategory Key Deleted");
                }

                //getAdvertiseServerIdForAdmin
                $keys = Redis::keys('pel:getAdvertiseServerIdForAdmin*');
                //Log::info("pel:getAdvertiseServerIdForAdmin Key Deleted",['key' =>$keys]);
                foreach ($keys as $key) {
                    Redis::del($key);
                }
                if (count($keys) === 0) {
                    //Log::info("pel:getAdvertiseServerIdForAdmin Key Deleted");
                }
            }


            //Advertise Link
            if ($api == '/api/addLink' or $api == '/api/updateLink' or $api == '/api/deleteLink' or $api == '/api/addAdvertiseLink' or $api == '/api/unlinkAdvertise' or $api == '/api/linkAdvertisementWithSubCategory' or $api == '/api/deleteLinkedAdvertisement') {

                //getAllLink
                $keys = Redis::keys('pel:getAllLink*');
                //Log::info("pel:getAllLink Key Deleted",['key' =>$keys]);
                foreach ($keys as $key) {
                    Redis::del($key);
                }
                if (count($keys) === 0) {
                    //Log::info("pel:getAllLink Key Deleted");
                }

                //getLink
                $keys = Redis::keys('pel:getLink*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }
                if (count($keys) === 0) {
                    //Log::info("pel:getLink Key Deleted");
                }

                //getLinkWithoutToken
                $keys = Redis::keys('pel:getLinkWithoutToken*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }
                if (count($keys) === 0) {
                    //Log::info("pel:getLinkWithoutToken Key Deleted");
                }

                //getLinkWithLastSyncTime
                $keys = Redis::keys('pel:getLinkWithLastSyncTime*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }
                if (count($keys) === 0) {
                    //Log::info("pel:getLinkWithLastSyncTime Key Deleted");
                }


                //Delete Image details View Key
                $keys = Redis::keys('pel:getImageDetails*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }
                if (count($keys) === 0) {
                    //Log::info("pel:getImageDetails Key Deleted");
                }

                //Delete getAdvertiseLink
                $keys = Redis::keys('pel:getAdvertiseLink*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }
                if (count($keys) === 0) {
                    //Log::info("pel:getAdvertiseLink Key Deleted");
                }

                //getAllAdvertisementForLinkAdvertisement
                $keys = Redis::keys('pel:getAllAdvertisementForLinkAdvertisement*');
                //Log::info("pel:getAllAdvertisementForLinkAdvertisement Key Deleted",['key' =>$keys]);
                foreach ($keys as $key) {
                    Redis::del($key);
                }
                if (count($keys) === 0) {
                    //Log::info("pel:getAllAdvertisementForLinkAdvertisement Key Deleted");
                }

                //getAllAdvertisementToLinkAdvertisement
                $keys = Redis::keys('pel:getAllAdvertisementToLinkAdvertisement*');
                //Log::info("pel:getAllAdvertisementToLinkAdvertisement Key Deleted",['key' =>$keys]);
                foreach ($keys as $key) {
                    Redis::del($key);
                }
                if (count($keys) === 0) {
                    //Log::info("pel:getAllAdvertisementToLinkAdvertisement Key Deleted");
                }

                //getAllAdvertisements
                $keys = Redis::keys('pel:getAllAdvertisements*');
                //Log::info("pel:getAllAdvertisements Key Deleted",['key' =>$keys]);
                foreach ($keys as $key) {
                    Redis::del($key);
                }
                if (count($keys) === 0) {
                    //Log::info("pel:getAllAdvertisements Key Deleted");
                }

            }


        } catch (Exception $e) {
            Log::error("DeleteCacheKey Middleware Error :", ['Error : ' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
            return Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'Delete Cache Key.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
    }
}

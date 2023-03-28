<?php

namespace App\Jobs;

use App\Http\Controllers\UserController;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class UpdateSearchTagInMultiPageSubCategory implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var mixed|null
     */
    private $image_ids;

    /**
     * Create a new job instance.
     *
     * @param $image_ids
     */
    public function __construct($image_ids = NULL)
    {
        $this->image_ids = $image_ids;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            /* This job is run for Flyer Maker(https://flyerbuilder.app) application */
            if (config('constant.ACTIVATION_LINK_PATH') == 'https://flyerbuilder.app') {
                /*if(!$this->sub_category_id){
                    if($this->catalog_id){
                        $result = DB::select('SELECT sub_category_id FROM sub_category_catalog WHERE catalog_id IN('. $this->catalog_id .')');
                        if ($result){
                            $this->sub_category_id = $result[0]->sub_category_id;
                        } else{
                            Log::error("UpdateSearchTagInMultiPageSubCategory : sub_category_id not found for catalog.", ["catalog_id" => $this->catalog_id]);
                            return;
                        }
                    } else{
                        $result = DB::select('SELECT
                                                    scc.sub_category_id
                                                FROM
                                                    images as im,
                                                    sub_category_catalog as scc
                                                WHERE
                                                    scc.catalog_id = im.catalog_id AND
                                                    im.id IN('. $this->image_id .') AND im.id NOT IN(31884,14292,47704,21969,16369,45575,45126,44928,44926,44898,44896,44894,44890,44848,44846,44842,44840,44838,44836,44834,44832,45125, 44927, 44922, 44897, 44895, 44893, 44887, 44847, 44845, 44841, 44839, 44837, 44835, 44833, 44831,44830, 44828, 44826, 44824, 44822, 44816, 44930, 44931, 44795, 44820, 44817, 44813, 44807, 44806, 44802, 44829, 44827, 44825, 44823, 44821, 44815, 44929, 44932, 44794, 44819, 44818, 44814, 44808, 44805, 44803, 44800, 44796, 44791, 45767, 45761, 45836, 45822, 45820, 45818, 45816, 45790, 45788, 45759, 16576, 16580, 44801, 44797, 44790, 45766, 45760, 45835, 45821, 45819, 45817, 45815, 45789, 45787, 45758, 16575, 16579, 16580, 16588, 16590, 16583, 16578, 16579, 16587, 16589, 16584, 16577)');
                        if ($result){
                            $this->sub_category_id = $result[0]->sub_category_id;
                        } else{
                            Log::error("UpdateSearchTagInMultiPageSubCategory : sub_category_id not found for image.", ["image_id" => $this->image_id]);
                            return;
                        }
                    }
                }*/
                $old_sub_category_id = config('constant.SINGLE_PAGE_SUB_CATEGORY_ID');
                $old_layout_sub_category_id = config('constant.SINGLE_PAGE_SUB_CATEGORY_ID_FOR_LAYOUT');
                $update_catalog_count = $update_content_count = $new_catalog_count = $new_content_count = 0;

                $image_id_list = DB::select('SELECT
                                                  im.id as image_id
                                                FROM
                                                  images AS im,
                                                  sub_category_catalog AS scc
                                                WHERE
                                                    im.catalog_id = scc.catalog_id AND
                                                    im.id IN(' . $this->image_ids . ') AND
                                                    im.id NOT IN(45126,44928,44926,44898,44896,44894,44890,44848,44846,44842,44840,44838,44836,44834,44832,45125,44927,44922,44897,44895,44893,44887,44847,44845,44841,44839,44837,44835,44833,44831,44830,44828,44826,44824,44822,44816,44930,44931,44795,44820,44817,44813,44807,44806,44802,44829,44827,44825,44823,44821,44815,44929,44932,44794,44819,44818,44814,44808,44805,44803,44800,44796,44791,45767,45761,45836,45822,45820,45818,45816,45790,45788,45759,16576,16580,44801,44797,44790,45766,45760,45835,45821,45819,45817,45815,45789,45787,45758,16575,16579,16580,16588,16590,16583,16578,16579,16587,16589,16584,16577) AND
                                                    scc.sub_category_id IN(' . $old_sub_category_id . ', ' . $old_layout_sub_category_id . ')');
                $image_id_array = array_column($image_id_list, 'image_id');
                $image_ids = implode(',', $image_id_array);

                /*if ($this->sub_category_id == config('constant.SINGLE_PAGE_SUB_CATEGORY_ID')) {
                    $new_sub_category = config('constant.MULTI_PAGE_SUB_CATEGORY_ID');
                } elseif ($this->sub_category_id == config('constant.SINGLE_PAGE_SUB_CATEGORY_ID_FOR_LAYOUT')) {
                    $new_sub_category = config('constant.MULTI_PAGE_SUB_CATEGORY_ID_FOR_LAYOUT');
                } else {
                    Log::error("UpdateSearchTagInMultiPageSubCategory : Unidentified sub_category_id detected.", ["sub_category_id" => $this->sub_category_id]);
                    return;
                }

                $start_date = date("Y-m-d H:i:s", strtotime('-5 minutes'));
                $end_date = date('Y-m-d H:i:s');*/

                /*$updated_catalog = DB::select('SELECT
                                                  ctm.id AS catalog_id,
                                                  ctm.name,
                                                  ctm.catalog_type,
                                                  ctm.image,
                                                  ctm.icon,
                                                  ctm.landscape_image,
                                                  ctm.portrait_image,
                                                  ctm.landscape_webp,
                                                  ctm.portrait_webp,
                                                  ctm.is_free,
                                                  ctm.is_ios_free,
                                                  ctm.is_featured,
                                                  ctm.is_active,
                                                  ctm.event_date,
                                                  ctm.popularity_rate,
                                                  ctm.search_category,
                                                  ctm.created_at,
                                                  ctm.updated_at,
                                                  ctm.attribute1,
                                                  ctm.attribute2,
                                                  ctm.attribute3,
                                                  ctm.attribute4
                                              FROM
                                                  catalog_master AS ctm
                                                  JOIN sub_category_catalog AS scc ON ctm.id = scc.catalog_id AND scc.sub_category_id = ?
                                              WHERE
                                                  ctm.updated_at BETWEEN "' . $start_date . '" AND "' . $end_date . '" ', [$this->sub_category_id]);

                DB::beginTransaction();
                foreach ($updated_catalog as $catalog) {
                    $old_catalog_id = $catalog->catalog_id;
                    DB::update('UPDATE catalog_master
                                SET
                                  name = ?,
                                  catalog_type = ?,
                                  image = ?,
                                  icon = ?,
                                  landscape_image = ?,
                                  portrait_image = ?,
                                  landscape_webp = ?,
                                  portrait_webp = ?,
                                  is_free = ?,
                                  is_ios_free = ?,
                                  is_featured = ?,
                                  is_active = ?,
                                  event_date = ?,
                                  popularity_rate = ?,
                                  search_category = ?,
                                  created_at = ?,
                                  updated_at = ?,
                                  attribute1 = ?,
                                  attribute2 = ?,
                                  attribute3 = ?,
                                  attribute4 = ?
                                WHERE
                                  attribute5 = ?',
                        [$catalog->name,
                            $catalog->catalog_type,
                            $catalog->image,
                            $catalog->icon,
                            $catalog->landscape_image,
                            $catalog->portrait_image,
                            $catalog->landscape_webp,
                            $catalog->portrait_webp,
                            $catalog->is_free,
                            $catalog->is_ios_free,
                            $catalog->is_featured,
                            $catalog->is_active,
                            $catalog->event_date,
                            $catalog->popularity_rate,
                            $catalog->search_category,
                            $catalog->created_at,
                            $catalog->updated_at,
                            $catalog->attribute1,
                            $catalog->attribute2,
                            $catalog->attribute3,
                            $catalog->attribute4,
                            $old_catalog_id]);
                    $update_catalog_count++;
                }
                DB::commit();*/
                if ($image_ids) {
                    $updated_content = DB::select('SELECT
                                                      im.id AS content_id,
                                                      im.catalog_id,
                                                      im.image,
                                                      im.multiple_images,
                                                      im.original_img,
                                                      im.display_img,
                                                      im.cover_img,
                                                      im.cover_webp_img,
                                                      im.is_active,
                                                      im.content_type,
                                                      im.image_type,
                                                      im.template_name,
                                                      im.json_data,
                                                      im.json_pages_sequence,
                                                      im.is_multipage,
                                                      im.is_free,
                                                      im.is_ios_free,
                                                      im.is_featured,
                                                      im.is_portrait,
                                                      im.search_category,
                                                      im.height,
                                                      im.width,
                                                      im.original_img_height,
                                                      im.original_img_width,
                                                      im.cover_img_height,
                                                      im.cover_img_width,
                                                      im.original_cover_img_height,
                                                      im.original_cover_img_width,
                                                      im.is_auto_upload,
                                                      im.created_at,
                                                      im.updated_at,
                                                      im.attribute1,
                                                      im.attribute2,
                                                      im.attribute3,
                                                      im.attribute4
                                                   FROM
                                                      images as im
                                                      JOIN sub_category_catalog AS scc ON im.catalog_id = scc.catalog_id
                                                      JOIN catalog_master AS ctm ON ctm.id = scc.catalog_id AND ctm.is_featured = 1
                                                   WHERE
                                                      im.id IN(' . $image_ids . ')');

                    $create_at = date('Y-m-d H:i:s');
                    DB::beginTransaction();
                    foreach ($updated_content as $content) {
                        $json_data = json_decode($content->json_data);
                        $new_json_data = json_decode('{}');
                        if (isset($json_data->page_id)) {
                            $rand_no = $json_data->page_id;
                            $new_json_data->{$rand_no} = $json_data;
                        } else {
                            $rand_no = rand(100001, 999999);
                            $json_data->page_id = $rand_no;
                            $new_json_data->{$rand_no} = $json_data;
                        }
                        $content->json_data = json_encode($new_json_data);

                        $new_catalog_id = (new UserController())->oldToNewCatalogId($content->catalog_id);
                        if (!$new_catalog_id) {
                            /*$updated_catalog_data = DB::select('SELECT
                                                                      ctm.id AS catalog_id,
                                                                      ctm.name,
                                                                      ctm.catalog_type,
                                                                      ctm.image,
                                                                      ctm.icon,
                                                                      ctm.landscape_image,
                                                                      ctm.portrait_image,
                                                                      ctm.landscape_webp,
                                                                      ctm.portrait_webp,
                                                                      ctm.is_free,
                                                                      ctm.is_ios_free,
                                                                      ctm.is_featured,
                                                                      ctm.is_active,
                                                                      ctm.event_date,
                                                                      ctm.popularity_rate,
                                                                      ctm.search_category,
                                                                      ctm.created_at,
                                                                      ctm.updated_at,
                                                                      ctm.attribute1,
                                                                      ctm.attribute2,
                                                                      ctm.attribute3,
                                                                      ctm.attribute4
                                                                  FROM
                                                                      catalog_master AS ctm
                                                                      JOIN sub_category_catalog AS scc ON ctm.id = scc.catalog_id AND scc.sub_category_id = ?
                                                                  WHERE
                                                                      ctm.id = ?', [$this->sub_category_id, $content->catalog_id]);
                            if ($updated_catalog_data) {
                                $updated_catalog_data = json_decode(json_encode($updated_catalog_data[0]), true);
                                $updated_catalog_data['attribute5'] = $updated_catalog_data['catalog_id'];
                                unset($updated_catalog_data['catalog_id']);
                                $new_catalog_id = DB::table('catalog_master')->insertGetId($updated_catalog_data);
                                DB::insert('INSERT INTO sub_category_catalog(sub_category_id,catalog_id,created_at) VALUES (?, ?, ?)', [$new_sub_category, $new_catalog_id, $create_at]);
                                $new_catalog_count++;
                            }*/
                            Log::error('UpdateSearchTagInMultiPageSubCategory new catalog not found.', ['catalog_id' => $content->catalog_id]);
                            return;
                        }

                        $new_content_id = (new UserController())->oldToNewContentId($content->content_id);
                        $multiple_images[$rand_no] = array("name" => $content->image, "webp_name" => $content->attribute1, "width" => $content->width, "height" => $content->height, "org_img_width" => $content->original_img_width, "org_img_height" => $content->original_img_height, "page_id" => $rand_no);
                        if (!$new_content_id) {
                            $new_content_data = json_decode(json_encode($content), true);
                            $new_content_data['attribute4'] = $new_content_data['content_id'];
                            $new_content_data['catalog_id'] = $new_catalog_id;
                            $new_content_data['json_pages_sequence'] = $rand_no;
                            $new_content_data['multiple_images'] = json_encode($multiple_images);
                            $new_content_data['is_multipage'] = 1;
                            unset($new_content_data['content_id']);
                            DB::table('images')->insertGetId($new_content_data);
                            $new_content_count++;
                        } else {
                            DB::update('UPDATE images
                                        SET
                                            catalog_id = ?,
                                            image = ?,
                                            multiple_images = ?,
                                            original_img = ?,
                                            display_img = ?,
                                            cover_img = ?,
                                            cover_webp_img = ?,
                                            is_active = ?,
                                            content_type = ?,
                                            image_type = ?,
                                            template_name = ?,
                                            json_data = ?,
                                            json_pages_sequence = ?,
                                            is_multipage = ?,
                                            is_free = ?,
                                            is_ios_free = ?,
                                            is_featured = ?,
                                            is_portrait = ?,
                                            search_category = ?,
                                            height = ?,
                                            width = ?,
                                            original_img_height = ?,
                                            original_img_width = ?,
                                            cover_img_height = ?,
                                            cover_img_width = ?,
                                            original_cover_img_height = ?,
                                            original_cover_img_width = ?,
                                            is_auto_upload = ?,
                                            created_at = ?,
                                            updated_at = ?,
                                            attribute1 = ?,
                                            attribute2 = ?,
                                            attribute3 = ?
                                        WHERE
                                            id = ?',
                                [$new_catalog_id,
                                    $content->image,
                                    json_encode($multiple_images),
                                    $content->original_img,
                                    $content->display_img,
                                    $content->cover_img,
                                    $content->cover_webp_img,
                                    $content->is_active,
                                    $content->content_type,
                                    $content->image_type,
                                    $content->template_name,
                                    $content->json_data,
                                    $rand_no,
                                    1,
                                    $content->is_free,
                                    $content->is_ios_free,
                                    $content->is_featured,
                                    $content->is_portrait,
                                    $content->search_category,
                                    $content->height,
                                    $content->width,
                                    $content->original_img_height,
                                    $content->original_img_width,
                                    $content->cover_img_height,
                                    $content->cover_img_width,
                                    $content->original_cover_img_height,
                                    $content->original_cover_img_width,
                                    $content->is_auto_upload,
                                    $content->created_at,
                                    $content->updated_at,
                                    $content->attribute1,
                                    $content->attribute2,
                                    $content->attribute3,
                                    $new_content_id]);
                            $update_content_count++;
                        }
                        $multiple_images = NULL;

                    }
                    DB::commit();

                    //Log::info('UpdateSearchTagInMultiPageSubCategory job run successfully.', ['catalog_count' => $update_catalog_count, 'content_count' => $update_content_count, 'new_catalog_count' => $new_catalog_count, 'new_content_count' => $new_content_count]);
                    (new UserController())->deleteAllRedisKeys("getCatalogBySubCategoryIdForAdmin");
                    (new UserController())->deleteAllRedisKeys("getDataByCatalogIdForAdmin");
                    (new UserController())->deleteAllRedisKeys("getCatalogBySubCategoryId");
                }
            }

        } catch (Exception $e) {
            Log::error("UpdateSearchTagInMultiPageSubCategory command handle() : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            DB::rollBack();
        }
    }
}

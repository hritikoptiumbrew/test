<?php

namespace App\Console\Commands;

use App\Http\Controllers\UserController;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SinglePageToMultiPageLayoutDataTransfer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'SinglePageToMultiPageLayoutDataTransfer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Convert single page layout to multi page layout from old sub category to new sub category.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            $old_sub_category = config('constant.SINGLE_PAGE_SUB_CATEGORY_ID_FOR_LAYOUT');
            $new_sub_category = config('constant.MULTI_PAGE_SUB_CATEGORY_ID_FOR_LAYOUT');
            $start_date = date("Y-m-d H:i:s", strtotime('-1 day'));
            $end_date = date('Y-m-d H:i:s');
            $update_catalog_count = $update_content_count = $new_catalog_count = $new_content_count = 0;

            $updated_catalog = DB::select('SELECT
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
                                              ctm.updated_at BETWEEN "' . $start_date . '" AND "' . $end_date . '" ', [$old_sub_category]);

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
            DB::commit();

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
                                              JOIN sub_category_catalog AS scc ON im.catalog_id = scc.catalog_id AND scc.sub_category_id = ?
                                              JOIN catalog_master AS ctm ON ctm.id = scc.catalog_id AND ctm.is_featured = 1
                                           WHERE
                                              im.updated_at BETWEEN "' . $start_date . '" AND "' . $end_date . '" ', [$old_sub_category]);

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
                    $updated_catalog_data = DB::select('SELECT
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
                                                          ctm.id = ?', [$old_sub_category, $content->catalog_id]);
                    if ($updated_catalog_data) {
                        $updated_catalog_data = json_decode(json_encode($updated_catalog_data[0]), true);
                        $updated_catalog_data['attribute5'] = $updated_catalog_data['catalog_id'];
                        unset($updated_catalog_data['catalog_id']);
                        $new_catalog_id = DB::table('catalog_master')->insertGetId($updated_catalog_data);
                        DB::insert('INSERT INTO sub_category_catalog(sub_category_id,catalog_id,created_at) VALUES (?, ?, ?)', [$new_sub_category, $new_catalog_id, $create_at]);
                        $new_catalog_count++;
                    }
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

            Log::info('SinglePageToMultiPageDataTransfer scheduler run successfully.', ['catalog_count' => $update_catalog_count, 'content_count' => $update_content_count, 'new_catalog_count' => $new_catalog_count, 'new_content_count' => $new_content_count]);
            (new UserController())->deleteAllRedisKeys("getCatalogBySubCategoryIdForAdmin");
            (new UserController())->deleteAllRedisKeys("getDataByCatalogIdForAdmin");
            (new UserController())->deleteAllRedisKeys("getCatalogBySubCategoryId");

        } catch (Exception $e) {
            Log::error("SinglePageToMultiPageDataTransfer command handle() : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            DB::rollBack();
        }
    }
}

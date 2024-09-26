<?php

namespace Heptaaurium\AliexpressImporter\Traits;

use Heptaaurium\AliexpressImporter\Models\Product;
use Illuminate\Support\Facades\DB;

trait ProductsTrait
{
    public function _fetch_product($id)
    {
        $url = env('PRODUCT_DETAILS_END_POINT') ?? url('/api/product');
        $url .= "?product_id={$id}";

        $details = file_get_contents($url);
        $details = json_decode($details, true);

        return $details;
    }

    public function _store_product($data)
    {
        $product = new Product;
        $product->name  = $data['ae_item_base_info_dto']['subject'];
        $product->description  = $data['ae_item_base_info_dto']['detail'];
        $product->images  = $data['ae_multimedia_info_dto']['image_urls'];
        $product->image  = explode(",", $data['ae_multimedia_info_dto']['image_urls'])[0];
        $product->aliexpress_product_id = $data['ae_item_base_info_dto']['product_id'];
        $product->imported_from_aliexpress = true;
        $product->save();
    }

    public function _sku_details($data, $product_id)
    {
        // $sets = DB::table('')
    }
}

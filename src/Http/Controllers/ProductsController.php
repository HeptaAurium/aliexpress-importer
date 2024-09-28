<?php

namespace Heptaaurium\AliexpressImporter\Traits;

use Heptaaurium\AliexpressImporter\Models\Product;
use Botble\Slug\Models\Slug;
use Illuminate\Support\Facades\DB;

trait ProductsTrait
{
    public function _fetch_product($id)
    {
        $url = env('PRODUCT_DETAILS_END_POINT') ?? url('/api/product');
        $url .= "?product_id={$id}";

        $details = file_get_contents($url);
        $details = json_decode($details, true);

        return $details['aliexpress_ds_product_get_response']['result'];
    }

    public function _store_product($data)
    {
        $count = [
            'successful' => 0,
            'failed' => 0,
            'products' => []
        ];

        $pd = $this->_sku_details($data);
        foreach ($pd as  $_pd) {
            $product = Product::updateOrCreate(
                ['aliexpress_product_id' => $_pd['aliexpress_product_id']],
                [
                    'name' => $_pd['name'],
                    'description' => $_pd['description'],
                    'price' => $_pd['price'],
                    'quantity' => $_pd['quantity'],
                    'imported_from_aliexpress' => true,
                    'images' => $_pd['images'],
                    'image' => $_pd['image'],
                    'created_by_id' => 1,
                    'with_storehouse_management' => 1,
                    'is_featured' => 1,
                    'is_variation' => 0,
                    'stock_status' => 'in_stock',
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );
            if ($product) {
                if ($_pd['attributes']) {
                    $this->_handle_attributes($_pd['attributes'], $product->id);
                    $this->_handle_files($_pd['images'], $product->id);
                    $this->_handle_tags($_pd['properties'], $product->id);
                    $this->_handle_slug($data, $product->id);
                }
                $count['successful'] += 1;
                $count['products'][] = $product->id . ": " . $_pd['name'];
            } else {
                $count['failed'] += 1;
            }
        }
        return response()->json($count);
    }

    public function _sku_details($data): array
    {
        $product = [];
        $products = [];

        $product['description'] = $data['ae_item_base_info_dto']['detail'];
        $product['images'] = $data['ae_multimedia_info_dto']['image_urls'];
        $product['image'] = explode(';', $data['ae_multimedia_info_dto']['image_urls'])[0];
        $product['imported_from_aliexpress'] = true;
        $product['aliexpress_product_id'] = $data['ae_item_base_info_dto']['product_id'];
        $product['properties'] = $data['ae_item_properties']['ae_item_property'];

        // data from sku 
        $sku_data =  $data['ae_item_sku_info_dtos']['ae_item_sku_info_d_t_o'];
        foreach ($sku_data as $sku) {
            $name = $sku['ae_sku_property_dtos']['ae_sku_property_d_t_o'][0]['sku_property_value'] . ' ' . $data['ae_item_base_info_dto']['subject'];
            $product['name'] = $name;
            $product['price'] =  $sku['sku_price'];
            $product['quantity'] =  $sku['sku_available_stock'];
            $product['attributes'] = $sku['ae_sku_property_dtos']['ae_sku_property_d_t_o'];
            array_push($products, $product);
        }
        return $products;
    }

    public function _get_name_description($sku_details)
    {
        $name = "";
        foreach ($sku_details as $sk) {
            if ($sk['sku_property_value']) {
                $name .= $sk['property_value_definition_name'] . '(' . $sk['sku_property_name'] . ') ';
            }
        }
    }

    public function _handle_files($data, $product_id)
    {
        $data = explode(";", $data);
        foreach ($data as $file) {
            DB::table('ec_product_files')->insert([
                'url' => $file,
                'product_id' => $product_id,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
    public function _handle_attributes($data, $product_id)
    {
        foreach ($data as $dt) {

            $attr = DB::table('ec_product_attribute_sets')
                ->where('title', 'LIKE', "%{$dt['sku_property_name']}%")
                ->OrWhere('slug', 'LIKE', "%{$dt['sku_property_name']}%")
                ->first();

            if ($attr && $product_id) {
                DB::table('ec_product_with_attribute_set')
                    ->insert([
                        'attribute_set_id' => $attr->id,
                        'product_id' => $product_id
                    ]);
            }
        }
    }

    public function _handle_tags($data, $product_id)
    {
        foreach ($data as $prop) {
            $tag = DB::table('ec_product_tags')
                ->where('name', 'LIKE', "%{$prop['attr_name']}%")
                ->first();

            if (!$tag) {
                $tag_id = DB::table('ec_product_tags')->insertGetId([
                    'name' => $prop['attr_name'],
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            $existingCombination = DB::table('ec_product_tag_product')
                ->where('tag_id', $tag->id ?? $tag_id)
                ->where('product_id', $product_id)
                ->first();

            if (!$existingCombination) {
                DB::table('ec_product_tag_product')->insert([
                    'tag_id' => $tag->id ?? $tag_id,
                    'product_id' => $product_id
                ]);
            }
        }
    }
    public function _handle_brands($data, $product_id)
    {

        foreach ($data as $prop) {
            if (strpos(strtolower($prop['attr_name']), 'brand') !== false) {
                $brand = DB::table('ec_brands')
                    ->where('name', 'LIKE', "%{$prop['attr_value']}%")
                    ->first();

                if ($brand) {
                    DB::table('ec_product_with_brands')->insert([
                        'brand_id' => $brand->id,
                        'product_id' => $product_id,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }
        }
    }
    public function _handle_slug($data, $product_id)
    {
        $slug = str_replace(' ', '-', strtolower($data['ae_item_base_info_dto']['subject']));
        Slug::query()->updateOrCreate(
            ["key" => $slug,],
            [
                "reference_id" => $product_id,
                "reference_type" => "Botble\Ecommerce\Models\Product",
                "prefix" => "products",
            ]
        );
    }
}

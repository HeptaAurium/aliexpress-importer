<?php


namespace Heptaaurium\AliexpressImporter\Http\Controllers;

use Heptaaurium\AliexpressImporter\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ProductsController extends Controller
{

    public function index()
    {
        return json_encode([

            'status' => 200
        ]);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {

            $product = new Product();
            $product->name = $request->input('product_name');
            $product->price = $request->input('product_price');
            $product->cost_per_item = $request->input('product_price');
            $product->stock = $request->input('stock');
            $product->description = $request->input('product_description');
            $product->category_id = $request->input('category_id');
            $product->status = $request->input('status');
            $product->sub_categories_id = $request->input('sub_categories_id');
            $product->store_id = $request->input('storeid');
            $product->inventories_id = $request->input('inventories_id');
            $product->cost = $request->input('cost');
            $product->sku = $request->input('sku');
            // $product->type = $request->input('type');
            // $product->color = $request->input('color');
            // $product->size = $request->input('size');
            $product->brand_id = $request->input('brand');
            $product->images = $request->input('product_image');

            $product->product_features = $request->input('product_features');
            $product->product_specifications = $request->input('product_specifications');
            $product->save();

            // files
            DB::table('ec_product_files')
                ->insert([
                    [
                        'product_id' => $product->id,
                        'url' => $request->input('product_image')
                    ],
                    [
                        'product_id' => $product->id,
                        'url' => $request->input('product_thumbnail')
                    ],
                    [
                        'product_id' => $product->id,
                        'url' => $request->input('product_thumbnail_two')
                    ],
                    [
                        'product_id' => $product->id,
                        'url' => $request->input('product_thumbnail_three')
                    ],
                    [
                        'product_id' => $product->id,
                        'url' => $request->input('product_thumbnail_four')
                    ],
                ]);
            DB::table('ec_product_attributes')
                ->insert([
                    'title' => $request->input('product_description'),
                    'color' => $request->input('color'),
                    'image' => $request->input('product_image'),
                ]);

            DB::commit();
            return response()->json(['status' => 200]);
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollBack();
            return response()->json(['status' => 500]);
        }
    }
}

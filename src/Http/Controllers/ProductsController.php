<?php


namespace Heptaaurium\AliexpressImporter\Http\Controllers;

use Heptaaurium\AliexpressImporter\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Heptaaurium\AliexpressImporter\Traits\AuthTrait;
use Heptaaurium\AliexpressImporter\Traits\ProductsTrait;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\Log;

class ProductsController extends Controller
{
    use AuthTrait, ProductsTrait;
    public function index()
    {
        return json_encode([

            'status' => 200
        ]);
    }

    public function store(Request $request)
    {
        $token = $request->get('api_token');
        if ($this->_verify_token($token)->status() != 200) {
            return $this->_verify_token($token);
        }
        DB::beginTransaction();

        try {
            $product_details = $this->_fetch_product($request->product_id);
            $product_id = $request->product_id;
            $product = Product::where('aliexpress_product_id', $product_id)->first();
            if ($product) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Product already exists'
                ], 400);
            }
            $stored_product = $this->_store_product($product_details);
            DB::commit();
            return response()->json([
                'status' => 'ok',
                'data' => $stored_product,
                'message' => 'Product imported successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
            return response()->json(['error' => $e], 500);
        }
    }

    public function runScript($productId)
    {

        // $process = new Process(['node', base_path('vendor/heptaaurium/aliexpress-importer/resources/js/index.mjs'), $productId]);
        $process = new Process(['node', public_path('vendor/ha-axi/js/index.mjs'), $productId]);

        try {
            $process->mustRun();
            $output = $process->getOutput();
            $response = json_decode($output);
            Log::info('Script response: ' . json_encode($response));
            return response()->json($response);
        } catch (ProcessFailedException $exception) {
            Log::error('Process failed: ' . $exception->getMessage());
            Log::error('Process error output: ' . $process->getErrorOutput());
            return response()->json(['error' => 'An error occurred while processing your request.'], 500);
        }
    }
}

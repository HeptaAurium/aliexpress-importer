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
        Log::info($request->product_id);

        DB::beginTransaction();
        $token = $request->get('api_token');
        Log::info($token);
        if ($this->_verify_token($token)->status() != 200) {
            return $this->_verify_token($token);
        }

        return json_encode([
            'status' => 'ok'
        ]);

        $product_details = $this->_fetch_product($request->product_id);
        return $product_details;
        $product = $this->_store_product($product_details);
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

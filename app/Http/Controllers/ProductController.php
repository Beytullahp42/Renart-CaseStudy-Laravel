<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function fetchProducts(): JsonResponse
    {

        $goldPrice = $this->getGoldPrice();


        // local -> storage/app/private

        if (!Storage::disk('local')->exists('products.json')) {
            return response()->json(['error' => 'File not found'], 404);
        }

        $json = Storage::disk('local')->get('products.json');

        $products = json_decode($json, true);

        foreach ($products as &$product) {
            $product['price'] = round(($product['popularityScore'] + 1) * $product['weight'] * $goldPrice, 2);
        }


        return response()->json([
            'goldPrice' => $goldPrice,
            'products' => $products
        ]);
    }

    public function getGoldPrice(): float
    {
        $goldPrice = Cache::remember('gold_price', now()->addHour(), function () {
            $response = Http::get('https://api.metalpriceapi.com/v1/latest', [
                'api_key' => env('GOLD_API_KEY'),
                'base' => 'USD',
                'currencies' => 'XAU',
            ]);

            if (!$response->ok()) {
                throw new Exception('Failed to fetch gold price');
            }

            return $response->json()['rates']['USDXAU'] / 31.1034768;
        });

        return $goldPrice;
    }

}


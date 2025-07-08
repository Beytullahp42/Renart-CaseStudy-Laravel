<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{

    public function fetchProducts(): JsonResponse
    {
        try {
            $products = $this->getPricedProducts();
            return response()->json($products);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }


    public function filterProducts(Request $request): JsonResponse
    {
        $request->validate([
            'minPrice' => 'required|numeric',
            'maxPrice' => 'required|numeric',
            'minPopularity' => 'required|numeric',
            'maxPopularity' => 'required|numeric',
        ]);

        try {
            $products = $this->getPricedProducts();

            $filtered = array_filter($products, function ($product) use ($request) {
                return $product['price'] >= $request->minPrice &&
                    $product['price'] <= $request->maxPrice &&
                    $product['popularityScore'] >= $request->minPopularity / 5 &&
                    $product['popularityScore'] <= $request->maxPopularity / 5;
            });

            return response()->json(array_values($filtered));
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }


    public function getGoldPrice(): float
    {
        return Cache::remember('gold_price', now()->addHour(), function () {
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
    }


    private function getPricedProducts(): array
    {
        if (!Storage::disk('local')->exists('products.json')) {
            throw new Exception('File not found');
        }

        $goldPrice = $this->getGoldPrice();
        $json = Storage::disk('local')->get('products.json');
        $products = json_decode($json, true);

        foreach ($products as &$product) {
            $product['price'] = round(($product['popularityScore'] + 1) * $product['weight'] * $goldPrice, 2);
        }

        return $products;
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Lunar\Models\Product;
use Lunar\Models\Url;

class ProductController extends ApiController
{
    /**
     * Get all products with pagination
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 12);
        $search = $request->get('search');
        $collection = $request->get('collection');
        $brand = $request->get('brand');
        $minPrice = $request->get('min_price');
        $maxPrice = $request->get('max_price');

        $query = Product::with([
            'variants.prices',
            'variants.images',
            'variants.values.option', // Cambiar productOptions por values.option
            'collections',
            'brand',
            'thumbnail'
        ]);

        // Search filter
        if ($search) {
            $query->where('product_type_id', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Collection filter
        if ($collection) {
            $query->whereHas('collections', function ($q) use ($collection) {
                $q->where('id', $collection);
            });
        }

        // Brand filter
        if ($brand) {
            $query->where('brand_id', $brand);
        }

        // Price filter
        if ($minPrice || $maxPrice) {
            $query->whereHas('variants.prices', function ($q) use ($minPrice, $maxPrice) {
                if ($minPrice) {
                    $q->where('price', '>=', $minPrice * 100); // Convert to cents
                }
                if ($maxPrice) {
                    $q->where('price', '<=', $maxPrice * 100);
                }
            });
        }

        $products = $query->paginate($perPage);

        return $this->successResponse($products, 'Products retrieved successfully');
    }

    /**
     * Get product by slug
     */
    public function show(string $slug): JsonResponse
    {
        $url = Url::whereElementType(Product::class)
                  ->whereSlug($slug)
                  ->first();

        if (!$url) {
            return $this->errorResponse('Product not found', 404);
        }

        $product = Product::with([
            'variants.prices',
            'variants.images',
            'variants.values.option', // Cambiar productOptions por values.option
            'collections',
            'brand',
            'thumbnail',
            'images'
        ])->find($url->element_id);

        if (!$product) {
            return $this->errorResponse('Product not found', 404);
        }

        return $this->successResponse($product, 'Product retrieved successfully');
    }

    /**
     * Get featured products
     */
    public function featured(): JsonResponse
    {
        $products = Product::with([
            'variants.prices',
            'thumbnail'
        ])
        ->inRandomOrder()
        ->limit(8)
        ->get();

        return $this->successResponse($products, 'Featured products retrieved successfully');
    }

    /**
     * Get products by collection
     */
    public function byCollection(string $collectionSlug, Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 12);

        $collection = Url::whereElementType(\Lunar\Models\Collection::class)
                        ->whereSlug($collectionSlug)
                        ->first();

        if (!$collection) {
            return $this->errorResponse('Collection not found', 404);
        }

        $products = $collection->element->products()
            ->with(['variants.prices', 'thumbnail'])
            ->paginate($perPage);

        return $this->successResponse($products, 'Collection products retrieved successfully');
    }
}

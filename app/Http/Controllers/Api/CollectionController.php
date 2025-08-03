<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\JsonResponse;
use Lunar\Models\Collection;
use Lunar\Models\Url;

class CollectionController extends ApiController
{
    /**
     * Get all collections
     */
    public function index(): JsonResponse
    {
        $collections = Collection::with(['thumbnail'])
            ->get()
            ->map(function ($collection) {
                $url = Url::whereElementType(Collection::class)
                         ->whereElementId($collection->id)
                         ->first();

                return [
                    'id' => $collection->id,
                    'name' => $collection->name,
                    'description' => $collection->description,
                    'slug' => $url ? $url->slug : null,
                    'thumbnail' => $collection->thumbnail,
                    'product_count' => $collection->products()->count()
                ];
            });

        return $this->successResponse($collections, 'Collections retrieved successfully');
    }

    /**
     * Get collection by slug
     */
    public function show(string $slug): JsonResponse
    {
        $url = Url::whereElementType(Collection::class)
                  ->whereSlug($slug)
                  ->first();

        if (!$url) {
            return $this->errorResponse('Collection not found', 404);
        }

        $collection = Collection::with(['thumbnail'])
            ->find($url->element_id);

        if (!$collection) {
            return $this->errorResponse('Collection not found', 404);
        }

        $data = [
            'id' => $collection->id,
            'name' => $collection->name,
            'description' => $collection->description,
            'slug' => $url->slug,
            'thumbnail' => $collection->thumbnail,
            'product_count' => $collection->products()->count()
        ];

        return $this->successResponse($data, 'Collection retrieved successfully');
    }
}

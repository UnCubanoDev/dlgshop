<?php

namespace App\Traits;

use Lunar\Models\Url;

trait FetchesUrls
{
    /**
     * The URL model from the slug.
     */
    public ?Url $url = null;

    /**
     * Fetch a url model.
     *
     * @param  string  $slug
     * @param  string  $type
     * @param  array  $eagerLoad
     */
    public function fetchUrl($slug, $type, $eagerLoad = []): ?Url
    {
        try {
            return Url::whereElementType($type)
                ->whereDefault(true)
                ->whereSlug($slug)
                ->with($eagerLoad)->first();
        } catch (\Exception $e) {
            \Log::error('Error fetching URL', [
                'slug' => $slug,
                'type' => $type,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
}

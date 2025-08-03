<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LunarPanelMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Disable Livewire navigation for admin panel
        if (str_contains($request->path(), 'lunar')) {
            config(['livewire.navigate.preload_on_hover' => false]);
            config(['livewire.navigate.preload_on_intersect' => false]);
            config(['livewire.navigate.preload_links' => false]);
        }

        return $next($request);
    }
}

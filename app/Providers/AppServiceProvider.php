<?php

namespace App\Providers;

use App\Modifiers\ShippingModifier;
use Illuminate\Support\ServiceProvider;
use Lunar\Admin\Support\Facades\LunarPanel;
use Lunar\Base\ShippingModifiers;
use Lunar\Shipping\ShippingPlugin;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        LunarPanel::panel(
            fn ($panel) => $panel->plugins([
                new ShippingPlugin,
            ])
        )
            ->register();

        // Configure Livewire for admin panel
        if (request()->is('lunar*')) {
            config([
                'livewire.navigate.preload_on_hover' => false,
                'livewire.navigate.preload_on_intersect' => false,
                'livewire.navigate.preload_links' => false,
                'livewire.optimize_script_loading' => true,
            ]);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(ShippingModifiers $shippingModifiers): void
    {
        $shippingModifiers->add(
            ShippingModifier::class
        );

        \Lunar\Facades\ModelManifest::replace(
            \Lunar\Models\Contracts\Product::class,
            \App\Models\Product::class,
            // \App\Models\CustomProduct::class,
        );

        // Additional configuration for admin panel
        if (request()->is('lunar*')) {
            // Disable Livewire navigation for admin panel
            config(['livewire.navigate.enabled' => false]);
        }
    }
}

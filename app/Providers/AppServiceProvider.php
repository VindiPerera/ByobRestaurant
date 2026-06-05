<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Product;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        View::composer('layouts.navbar', function ($view) {
            $lowStockCount = 0;
            if (auth()->check()) {
                $lowStockCount = Product::where('is_unlimited_stock', false)
                    ->whereNotNull('low_stock_threshold')
                    ->whereRaw('quantity <= low_stock_threshold')
                    ->count();
            }
            $view->with('lowStockCount', $lowStockCount);
        });
    }
}

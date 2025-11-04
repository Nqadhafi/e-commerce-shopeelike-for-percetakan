<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;
use App\Models\Cart;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
            Inertia::share('cart', function () {
        try {
            $sessionId = session()->getId();
            $cart = Cart::where('session_id',$sessionId)->where('status','open')->withCount('items')->first();
            return [
                'items_count' => $cart?->items_count ?? 0,
            ];
        } catch (\Throwable $e) {
            return ['items_count'=>0];
        }
    });
    }
}

<?php

namespace App\Http\Controllers;
use App\Models\Cart;
use Inertia\Inertia;
use App\Models\Category;
use Illuminate\Database\Eloquent\Builder;
use App\Models\WebBanner as Banner;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class HomeController extends Controller
{
    public function index()
    {

    $banners = Banner::where('is_active', true)
        ->orderBy('position') // atau orderBy('created_at')
        ->get(['id', 'title', 'image_url', 'link_url']);

        $categories = Category::with(['products' => function(BelongsToMany $query) {
            $query->select(['products.id', 'products.name', 'products.slug', 'products.thumbnail_url', 'product_category.category_id'])
                ->where('products.is_active', true)
                // ->orderBy('products.featured', 'desc') // Featured products first
                ->orderBy('products.created_at', 'desc'); // Then newest products
                // ->take(5); // Take exactly 5 products for the collage
        }])
        ->whereHas('products', function($query) {
            $query->where('is_active', true);
        })
        ->get();

        return Inertia::render('Home/Index', [
            'categories' => $categories,
            'banners' => $banners,
            'promoBanners' => Banner::promo2col()->take(2)->get(),
        ]);
    }

}

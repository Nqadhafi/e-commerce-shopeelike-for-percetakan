<?php

namespace App\Http\Controllers;
use App\Models\Cart;
use Inertia\Inertia;
use App\Models\Category;

class HomeController extends Controller
{


public function index()
{
    $categories = Category::with(['products' => fn($q) => $q->latest()->take(5)])
        
        ->get();

    // $cart = Cart::totals(); 

    return Inertia::render('Home/Index', [
        'categories' => $categories,
        // 'cart' => [
        //     'items_count' => $cart->items->count(),
        // ],
    ]);
}

}

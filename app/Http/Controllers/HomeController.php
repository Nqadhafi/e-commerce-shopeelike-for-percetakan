<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class HomeController extends Controller
{
    public function index()
    {
        // TODO: ambil data beneran dari DB (featured/new/bestseller, categories, dsb.)
        return Inertia::render('Home/Index', [
            'featuredProducts' => [],
            'newProducts'      => [],
            'bestSellers'      => [],
            'topCategories'    => [],
        ]);
    }
}

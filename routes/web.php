<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Admin\{CategoryController, TagController, ProductController, VariantController, VariantOptionController, AddonController, ProductAddonController};

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware(['auth', 'verified', 'role:admin,catalog'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('categories', CategoryController::class)->except(['show']);
    Route::resource('tags', TagController::class)->except(['show']);

    Route::resource('addons', AddonController::class)->except(['show']);

    Route::resource('products', ProductController::class)->except(['show']);
    Route::post('products/{product}/categories-sync', [ProductController::class,'categoriesSync'])->name('products.categories.sync');
    Route::post('products/{product}/tags-sync', [ProductController::class,'tagsSync'])->name('products.tags.sync');

    Route::post('products/{product}/variants', [VariantController::class,'store'])->name('variants.store');
    Route::put('variants/{variant}', [VariantController::class,'update'])->name('variants.update');
    Route::delete('variants/{variant}', [VariantController::class,'destroy'])->name('variants.destroy');

    Route::post('variants/{variant}/options', [VariantOptionController::class,'store'])->name('variant-options.store');
    Route::put('variant-options/{option}', [VariantOptionController::class,'update'])->name('variant-options.update');
    Route::delete('variant-options/{option}', [VariantOptionController::class,'destroy'])->name('variant-options.destroy');

    Route::post('products/{product}/addons-sync', [ProductAddonController::class,'sync'])->name('products.addons.sync');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
});

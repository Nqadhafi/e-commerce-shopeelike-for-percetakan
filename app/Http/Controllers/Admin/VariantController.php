<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Product, ProductVariant};
use Illuminate\Http\Request;

class VariantController extends Controller
{
    public function store(Request $r, Product $product) {
        $data = $r->validate([
            'name'=>['required','string','max:100'],
            'position'=>['nullable','integer','min:0'],
            'is_active'=>['boolean'],
        ]);
        $variant = $product->variants()->create($data);
        return response()->json(['ok'=>true,'variant'=>$variant]);
    }
    public function update(Request $r, ProductVariant $variant) {
        $data = $r->validate([
            'name'=>['required','string','max:100'],
            'position'=>['nullable','integer','min:0'],
            'is_active'=>['boolean'],
        ]);
        $variant->update($data);
        return response()->json(['ok'=>true]);
    }
    public function destroy(ProductVariant $variant) {
        $variant->delete();
        return response()->json(['ok'=>true]);
    }
}

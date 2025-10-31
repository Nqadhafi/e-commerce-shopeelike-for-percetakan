<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{ProductVariant, ProductVariantOption};
use Illuminate\Http\Request;

class VariantOptionController extends Controller
{
    public function store(Request $r, ProductVariant $variant) {
        $data = $r->validate([
            'label'=>['required','string','max:100'],
            'unit_price'=>['required','numeric','min:0'],
            'sku_suffix'=>['nullable','string','max:50'],
            'is_active'=>['boolean'],
        ]);
        $opt = $variant->options()->create($data);
        return response()->json(['ok'=>true,'option'=>$opt]);
    }
    public function update(Request $r, ProductVariantOption $option) {
        $data = $r->validate([
            'label'=>['required','string','max:100'],
            'unit_price'=>['required','numeric','min:0'],
            'sku_suffix'=>['nullable','string','max:50'],
            'is_active'=>['boolean'],
        ]);
        $option->update($data);
        return response()->json(['ok'=>true]);
    }
    public function destroy(ProductVariantOption $option) {
        $option->delete();
        return response()->json(['ok'=>true]);
    }
}

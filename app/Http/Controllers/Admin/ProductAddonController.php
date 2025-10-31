<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Product, Addon};
use Illuminate\Http\Request;

class ProductAddonController extends Controller
{
    public function sync(Request $r, Product $product) {
        $data = $r->validate([
            'addon_ids'=>['array'],
            'addon_ids.*'=>['integer','exists:addons_catalog,id'],
            'defaults'=>['array'],
            'requireds'=>['array'],
        ]);

        $sync = [];
        foreach ($data['addon_ids'] ?? [] as $aid) {
            $sync[$aid] = [
                'is_default' => in_array($aid, $data['defaults'] ?? []),
                'is_required'=> in_array($aid, $data['requireds'] ?? []),
                'constraints_json' => null,
            ];
        }
        $product->addons()->sync($sync);
        return back();
    }
}

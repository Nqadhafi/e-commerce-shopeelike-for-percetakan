<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Product, Addon};
use Illuminate\Http\Request;

class ProductAddonController extends Controller
{
    public function sync(Request $r, Product $product) {
            $data = $r->validate([
            'addon_ids'   => ['array'],
            'addon_ids.*' => ['integer','exists:addons_catalog,id'],
            'defaults'    => ['array'],
            'defaults.*'  => ['integer','exists:addons_catalog,id'],
            'requireds'   => ['array'],
            'requireds.*' => ['integer','exists:addons_catalog,id'],
        ]);

        $ids = collect($data['addon_ids'] ?? []);
        $def = collect($data['defaults'] ?? []);
        $req = collect($data['requireds'] ?? []);

        $sync = [];
        foreach ($ids as $id) {
            $sync[$id] = [
                'is_default'  => $def->contains($id),
                'is_required' => $req->contains($id),
                'constraints_json' => null,
            ];
        }
        $product->addons()->sync($sync);

        return back();
        }
}

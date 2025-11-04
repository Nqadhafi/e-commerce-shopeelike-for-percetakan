<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Product, Category, Tag};
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index() {
        return inertia('Admin/Products/Index', [
            'items' => Product::with(['categories','tags'])->latest()->paginate(20)
        ]);
    }

public function create() {
    return inertia('Admin/Products/Create', [
        'categories' => Category::orderBy('name')->get(['id','name','slug']),
        'tags' => Tag::orderBy('name')->get(['id','name','slug']),
        'addons' => \App\Models\Addon::where('is_active',1)->orderBy('label')->get(['id','code','label','type','billing_basis','amount']),
    ]);
}


public function store(Request $r) {
    $data = $r->validate([
        'name'=>['required','string','max:150'],
        'slug'=>['required','string','max:150','unique:products,slug'],
        'unit_label'=>['required','string','max:30'],
        'default_unit_price'=>['nullable','numeric','min:0'],
        'has_variants'=>['boolean'],
        'has_addons'=>['boolean'],
        'status'=>['required', Rule::in(['draft','published'])],
        'thumbnail_url'=>['nullable','string'],
        'images_json'=>['nullable','array'],
        'spec_json'=>['nullable','array'],
        'is_active'=>['boolean'],

        'category_ids'=>['sometimes','array'],
        'category_ids.*'=>['integer','exists:categories,id'],
        'primary_category_id'=>['nullable','integer','exists:categories,id'],
        'tag_ids'=>['sometimes','array'],
        'tag_ids.*'=>['integer','exists:tags,id'],

        // VARIANT DECLARATION (max 2 groups)
        'variants'=>['sometimes','array','max:2'],
        'variants.*.name'=>['required_with:variants','string','max:100'],
        'variants.*.position'=>['nullable','integer','min:0'],
        'variants.*.options'=>['required_with:variants','array','min:1'],
        'variants.*.options.*.label'=>['required','string','max:100'],
        'variants.*.options.*.sku_suffix'=>['nullable','string','max:50'],
        // harga opsi TIDAK dipakai (pakai matrix). tetap izinkan kosong:
        'variants.*.options.*.unit_price'=>['nullable','numeric','min:0'],

        // MATRIX PRICING + FILE UPLOAD (nullable)
        'matrix'=>['sometimes','array'],
        'matrix.*.v1_label'=>['required','string','max:100'],
        'matrix.*.v2_label'=>['nullable','string','max:100'],
        'matrix.*.unit_price'=>['required','numeric','min:0'],
        'matrix.*.image'=>['nullable','file','mimes:jpg,jpeg,png,webp','max:4096'],

        // ADDONS
        'addons'=>['sometimes','array'],
        'addons.*.id'=>['required','integer','exists:addons_catalog,id'],
        'addons.*.is_default'=>['nullable','boolean'],
        'addons.*.is_required'=>['nullable','boolean'],
    ]);

    // jika tanpa variasi → wajib price default
    if (!(bool)($data['has_variants'] ?? false) && empty($data['default_unit_price'])) {
        return back()->withErrors(['default_unit_price'=>'Wajib diisi jika tanpa variasi.'])->withInput();
    }

    // create product
    $payload = $data;
    $payload['images_json'] = $payload['images_json'] ?? null;
    $payload['spec_json']   = $payload['spec_json'] ?? null;
    $product = Product::create($payload);

    // categories
    if (!empty($data['category_ids'])) {
        $sync = [];
        foreach ($data['category_ids'] as $cid) {
            $sync[$cid] = ['is_primary' => ($data['primary_category_id'] ?? null) == $cid, 'position'=>0];
        }
        $product->categories()->sync($sync);
    }
    // tags
    if (!empty($data['tag_ids'])) $product->tags()->sync($data['tag_ids']);

    // VARIANTS (create up to 2 groups, create options)
    $variantGroups = [];
    if (($data['has_variants'] ?? false) && !empty($data['variants'])) {
        foreach ($data['variants'] as $idx => $vg) {
            $variant = $product->variants()->create([
                'name' => $vg['name'],
                'position' => $vg['position'] ?? $idx,
                'is_active' => true,
            ]);
            $optionsToCreate = [];
            foreach ($vg['options'] as $opt) {
                $optionsToCreate[] = [
                    'label' => $opt['label'],
                    'unit_price' => $opt['unit_price'] ?? 0, // ignored by matrix
                    'sku_suffix' => $opt['sku_suffix'] ?? null,
                    'is_active' => true,
                ];
            }
            $variant->options()->createMany($optionsToCreate);
            $variant->load('options:id,product_variant_id,label'); // reload IDs
            $variantGroups[] = $variant;
        }
    }

    // MATRIX: map label -> option_id per group, lalu simpan kombinasi + upload file
    if (!empty($data['matrix']) && !empty($variantGroups)) {
        // build label maps
        $mapV1 = []; $mapV2 = [];
        if (isset($variantGroups[0])) {
            foreach ($variantGroups[0]->options as $o) $mapV1[mb_strtolower($o->label)] = $o->id;
        }
        if (isset($variantGroups[1])) {
            foreach ($variantGroups[1]->options as $o) $mapV2[mb_strtolower($o->label)] = $o->id;
        }

        foreach ($data['matrix'] as $rowIdx => $row) {
            $v1Label = mb_strtolower($row['v1_label']);
            $v2Label = isset($row['v2_label']) && $row['v2_label'] !== null ? mb_strtolower($row['v2_label']) : null;

            $v1Id = $mapV1[$v1Label] ?? null;
            $v2Id = $v2Label ? ($mapV2[$v2Label] ?? null) : null;

            if (!$v1Id || (isset($variantGroups[1]) && !$v2Id)) {
                // skip jika tidak cocok
                continue;
            }

            $imagePath = null;
            if ($r->hasFile("matrix.$rowIdx.image")) {
                $imagePath = $r->file("matrix.$rowIdx.image")->store('variant-matrix','public');
            }

            $product->variantMatrix()->create([
                'variant1_option_id' => $v1Id,
                'variant2_option_id' => $v2Id,
                'unit_price' => $row['unit_price'],
                'image_path' => $imagePath,
            ]);
        }
    }

    // ADDONS
    if (($data['has_addons'] ?? false) && !empty($data['addons'])) {
        $sync = [];
        foreach ($data['addons'] as $ad) {
            $sync[$ad['id']] = [
                'is_default' => (bool)($ad['is_default'] ?? false),
                'is_required'=> (bool)($ad['is_required'] ?? false),
                'constraints_json' => null,
            ];
        }
        $product->addons()->sync($sync);
    }

    return redirect()->route('admin.products.edit', $product);
}



// UBAH edit(): kirim addons aktif juga
public function edit(Product $product) {
    $product->load(['categories','tags','variants.options','variantMatrix']);
    return inertia('Admin/Products/Edit', [
        'item' => $product,
        'categories' => Category::orderBy('name')->get(['id','name','slug']),
        'tags' => Tag::orderBy('name')->get(['id','name','slug']),
        'addons' => \App\Models\Addon::where('is_active',1)->orderBy('label')->get(['id','code','label','type','billing_basis','amount']),
    ]);
}

// NEW: sinkronisasi ulang matrix (update/hapus/tambah) dari Edit page
public function matrixSync(Request $r, Product $product) {
    $data = $r->validate([
        'rows' => ['required','array'],
        'rows.*.v1_option_id' => ['required','integer','exists:product_variant_options,id'],
        'rows.*.v2_option_id' => ['nullable','integer','exists:product_variant_options,id'],
        'rows.*.unit_price'   => ['required','numeric','min:0'],
        'rows.*.image'        => ['nullable','file','mimes:jpg,jpeg,png,webp','max:4096'],
    ]);

    // bangun key unik untuk dedup
    $key = fn($v1,$v2) => $v1.'|'.($v2 ?? 'null');

    // map existing
    $existing = $product->variantMatrix()->get()->keyBy(function($m) use($key){ return $key($m->variant1_option_id,$m->variant2_option_id); });

    foreach (($data['rows'] ?? []) as $i => $row) {
        $k = $key($row['v1_option_id'], $row['v2_option_id'] ?? null);
        $imagePath = null;
        if ($r->hasFile("rows.$i.image")) {
            $imagePath = $r->file("rows.$i.image")->store('variant-matrix','public');
        }
        if (isset($existing[$k])) {
            $payload = ['unit_price'=>$row['unit_price']];
            if ($imagePath) $payload['image_path'] = $imagePath;
            $existing[$k]->update($payload);
            unset($existing[$k]);
        } else {
            $product->variantMatrix()->create([
                'variant1_option_id' => $row['v1_option_id'],
                'variant2_option_id' => $row['v2_option_id'] ?? null,
                'unit_price' => $row['unit_price'],
                'image_path' => $imagePath,
            ]);
        }
    }
    // sisanya dihapus (opsional – comment jika tidak ingin auto-delete)
    foreach ($existing as $left) { $left->delete(); }

    return back();
}

// NEW: quick price preview (qty + addons)
public function pricePreview(Request $r, Product $product) {
    $data = $r->validate([
        'qty' => ['required','integer','min:1'],
        'v1_option_id' => ['nullable','integer','exists:product_variant_options,id'],
        'v2_option_id' => ['nullable','integer','exists:product_variant_options,id'],
        'addon_ids'    => ['sometimes','array'],
        'addon_ids.*'  => ['integer','exists:addons_catalog,id'],
    ]);

    $base = null;
    if ($product->has_variants) {
        $row = $product->variantMatrix()
            ->when($data['v1_option_id'] ?? null, fn($q,$v)=>$q->where('variant1_option_id',$v))
            ->when($data['v2_option_id'] ?? null, fn($q,$v)=>$q->where('variant2_option_id',$v))
            ->first();
        if (!$row) return response()->json(['ok'=>false,'message'=>'Kombinasi tidak ditemukan'], 422);
        $base = (float)$row->unit_price;
    } else {
        $base = (float)($product->default_unit_price ?? 0);
    }

    $addons = \App\Models\Addon::whereIn('id', $data['addon_ids'] ?? [])->get();
    $addonTotal = 0.0;
    foreach ($addons as $ad) {
        if ($ad->billing_basis === 'per_unit') $addonTotal += (float)$ad->amount * $data['qty'];
        else $addonTotal += (float)$ad->amount;
    }

    $subtotal = $base * $data['qty'] + $addonTotal;

    return response()->json([
        'ok'=>true,
        'unit_price'=>$base,
        'qty'=>$data['qty'],
        'addons_total'=>$addonTotal,
        'total'=>$subtotal,
    ]);
}


    public function update(Request $r, Product $product) {
        $data = $r->validate([
            'name'=>['required','string','max:150'],
            'slug'=>['required','string','max:150', Rule::unique('products','slug')->ignore($product->id)],
            'unit_label'=>['required','string','max:30'],
            'default_unit_price'=>['nullable','numeric','min:0'],
            'has_variants'=>['boolean'],
            'has_addons'=>['boolean'],
            'status'=>['required', Rule::in(['draft','published'])],
            'thumbnail_url'=>['nullable','string'],
            'images_json'=>['nullable','array'],
            'spec_json'=>['nullable','array'],
            'is_active'=>['boolean'],
        ]);
        if (!(bool)($data['has_variants'] ?? false) && empty($data['default_unit_price'])) {
            return back()->withErrors(['default_unit_price'=>'Wajib diisi jika tanpa variasi.']);
        }
        $data['images_json'] = $data['images_json'] ?? null;
        $data['spec_json'] = $data['spec_json'] ?? null;
        $product->update($data);
        return back();
    }

    public function destroy(Product $product) {
        $product->delete();
        return back();
    }

    public function categoriesSync(Request $r, Product $product) {
        $payload = $r->validate([
            'category_ids'=>['array'],
            'category_ids.*'=>['integer','exists:categories,id'],
            'primary_id'=>['nullable','integer','exists:categories,id'],
        ]);
        $sync = [];
        foreach ($payload['category_ids'] ?? [] as $cid) {
            $sync[$cid] = ['is_primary' => ($payload['primary_id'] ?? null) == $cid, 'position'=>0];
        }
        $product->categories()->sync($sync);
        return back();
    }

    public function tagsSync(Request $r, Product $product) {
        $payload = $r->validate([
            'tag_ids'=>['array'],
            'tag_ids.*'=>['integer','exists:tags,id'],
        ]);
        $product->tags()->sync($payload['tag_ids'] ?? []);
        return back();
    }
}

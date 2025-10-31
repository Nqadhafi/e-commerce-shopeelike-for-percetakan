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
        ]);
        if (!(bool)($data['has_variants'] ?? false) && empty($data['default_unit_price'])) {
            return back()->withErrors(['default_unit_price'=>'Wajib diisi jika tanpa variasi.']);
        }
        $data['images_json'] = $data['images_json'] ?? null;
        $data['spec_json'] = $data['spec_json'] ?? null;
        $product = Product::create($data);
        return redirect()->route('admin.products.edit', $product);
    }

    public function edit(Product $product) {
        $product->load(['categories','tags','variants.options']);
        return inertia('Admin/Products/Edit', [
            'item' => $product,
            'categories' => Category::orderBy('name')->get(['id','name','slug']),
            'tags' => Tag::orderBy('name')->get(['id','name','slug']),
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

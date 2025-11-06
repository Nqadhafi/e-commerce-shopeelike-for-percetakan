<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index(Request $r)
    {
        $q = trim((string) $r->get('q'));
        $items = Category::query()
            ->when($q, fn($x)=>$x->where(fn($w)=>$w->where('name','like',"%$q%")->orWhere('slug','like',"%$q%")))
            ->orderBy('position')->orderBy('name')
            ->get(['id','name','slug','parent_id','position','is_active','image_url','description','created_at']);

        $parents = Category::orderBy('name')->get(['id','name']);

        return inertia('Admin/Categories/Index', [
            'items' => $items,
            'parents' => $parents,
            'q' => $q,
        ]);
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'name'        => ['required','string','max:150'],
            'slug'        => ['nullable','string','max:150','unique:categories,slug'],
            'parent_id'   => ['nullable','integer','exists:categories,id'],
            'position'    => ['nullable','integer','min:0'],
            'is_active'   => ['nullable','boolean'],
            'description' => ['nullable','string'],
            'image'       => ['nullable','file','mimes:jpg,jpeg,png,webp','max:4096'],
        ]);

        $slug = $data['slug'] ?? Str::slug($data['name']);
        $payload = [
            'name' => $data['name'],
            'slug' => $slug,
            'parent_id' => $data['parent_id'] ?? null,
            'position' => $data['position'] ?? 0,
            'is_active' => (bool)($data['is_active'] ?? true),
            'description' => $data['description'] ?? null,
            'image_url' => null,
        ];

        if ($r->hasFile('image')) {
            $path = $r->file('image')->store('categories','public');
            $payload['image_url'] = '/storage/'.$path;
        }

        Category::create($payload);
        return back();
    }

    public function update(Request $r, Category $category)
    {
        $data = $r->validate([
            'name'        => ['required','string','max:150'],
            'slug'        => ['required','string','max:150', Rule::unique('categories','slug')->ignore($category->id)],
            'parent_id'   => ['nullable','integer','different:id','exists:categories,id'],
            'position'    => ['nullable','integer','min:0'],
            'is_active'   => ['nullable','boolean'],
            'description' => ['nullable','string'],
            'image'       => ['nullable','file','mimes:jpg,jpeg,png,webp','max:4096'],
        ]);

        $payload = [
            'name' => $data['name'],
            'slug' => $data['slug'],
            'parent_id' => $data['parent_id'] ?? null,
            'position' => $data['position'] ?? 0,
            'is_active' => (bool)($data['is_active'] ?? $category->is_active),
            'description' => $data['description'] ?? null,
        ];

        if ($r->hasFile('image')) {
            $path = $r->file('image')->store('categories','public');
            $payload['image_url'] = '/storage/'.$path;
        }

        $category->update($payload);
        return back();
    }

    public function toggle(Category $category)
    {
        $category->update(['is_active'=>!$category->is_active]);
        return back();
    }

    public function destroy(Category $category)
    {
        if ($category->products()->exists() || $category->children()->exists()) {
            return back()->withErrors(['category'=>'Tidak bisa hapus: masih terhubung ke produk atau punya subkategori.']);
        }
        $category->delete();
        return back();
    }
}

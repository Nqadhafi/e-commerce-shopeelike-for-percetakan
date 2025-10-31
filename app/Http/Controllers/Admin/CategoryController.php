<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function index() {
        return inertia('Admin/Categories/Index', [
            'items' => Category::orderBy('parent_id')->orderBy('position')->get()
        ]);
    }
    public function store(Request $r) {
        $data = $r->validate([
            'name'=>['required','string','max:150'],
            'slug'=>['required','string','max:150','unique:categories,slug'],
            'parent_id'=>['nullable','exists:categories,id'],
            'position'=>['nullable','integer','min:0'],
            'is_active'=>['boolean'],
            'image_url'=>['nullable','string'],
            'description'=>['nullable','string'],
        ]);
        Category::create($data);
        return back();
    }
    public function update(Request $r, Category $category) {
        $data = $r->validate([
            'name'=>['required','string','max:150'],
            'slug'=>['required','string','max:150', Rule::unique('categories','slug')->ignore($category->id)],
            'parent_id'=>['nullable','exists:categories,id'],
            'position'=>['nullable','integer','min:0'],
            'is_active'=>['boolean'],
            'image_url'=>['nullable','string'],
            'description'=>['nullable','string'],
        ]);
        $category->update($data);
        return back();
    }
    public function destroy(Category $category) {
        $category->delete();
        return back();
    }
}

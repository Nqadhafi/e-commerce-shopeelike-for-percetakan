<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TagController extends Controller
{
    public function index() {
        return inertia('Admin/Tags/Index', ['items'=>Tag::orderBy('name')->get()]);
    }
    public function store(Request $r) {
        $data = $r->validate([
            'name'=>['required','string','max:100'],
            'slug'=>['required','string','max:120','unique:tags,slug'],
            'is_active'=>['boolean'],
        ]);
        Tag::create($data);
        return back();
    }
    public function update(Request $r, Tag $tag) {
        $data = $r->validate([
            'name'=>['required','string','max:100'],
            'slug'=>['required','string','max:120', Rule::unique('tags','slug')->ignore($tag->id)],
            'is_active'=>['boolean'],
        ]);
        $tag->update($data);
        return back();
    }
    public function destroy(Tag $tag) {
        $tag->delete();
        return back();
    }
}

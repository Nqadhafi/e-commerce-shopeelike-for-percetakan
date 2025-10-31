<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Addon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AddonController extends Controller
{
    public function index() {
        return inertia('Admin/Addons/Index', ['items'=>Addon::orderBy('label')->get()]);
    }
    public function store(Request $r) {
        $data = $r->validate([
            'code'=>['required','string','max:50','unique:addons_catalog,code'],
            'label'=>['required','string','max:100'],
            'type'=>['required', Rule::in(['multiplier','overall'])],
            'billing_basis'=>['required', Rule::in(['per_unit','per_order'])],
            'amount'=>['required','numeric','min:0'],
            'is_active'=>['boolean'],
            'notes'=>['nullable','string'],
        ]);
        Addon::create($data);
        return back();
    }
    public function update(Request $r, Addon $addon) {
        $data = $r->validate([
            'code'=>['required','string','max:50', Rule::unique('addons_catalog','code')->ignore($addon->id)],
            'label'=>['required','string','max:100'],
            'type'=>['required', Rule::in(['multiplier','overall'])],
            'billing_basis'=>['required', Rule::in(['per_unit','per_order'])],
            'amount'=>['required','numeric','min:0'],
            'is_active'=>['boolean'],
            'notes'=>['nullable','string'],
        ]);
        $addon->update($data);
        return back();
    }
    public function destroy(Addon $addon) {
        $addon->delete();
        return back();
    }
}

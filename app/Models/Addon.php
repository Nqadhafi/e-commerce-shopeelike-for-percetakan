<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Addon extends Model
{
    protected $table = 'addons_catalog';
    protected $fillable = ['code','label','type','billing_basis','amount','is_active','notes'];
    protected $casts = ['is_active'=>'bool','amount'=>'decimal:2'];

    public function products(){
        return $this->belongsToMany(Product::class, 'product_addons', 'addon_id', 'product_id')
            ->withPivot(['is_default','is_required','constraints_json'])->withTimestamps();
    }
}

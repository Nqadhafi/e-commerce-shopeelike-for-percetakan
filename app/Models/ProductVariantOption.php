<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariantOption extends Model
{
    protected $fillable = ['product_variant_id','label','unit_price','sku_suffix','is_active'];
    protected $casts = ['is_active'=>'bool','unit_price'=>'decimal:2'];

    public function variant(){ return $this->belongsTo(ProductVariant::class, 'product_variant_id'); }
    public function tiers(){
        return $this->morphMany(WholesaleTier::class, 'scope');
    }
}

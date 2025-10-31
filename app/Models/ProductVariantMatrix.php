<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariantMatrix extends Model
{
    protected $fillable = [
        'product_id','variant1_option_id','variant2_option_id','unit_price','image_path'
    ];
    protected $casts = ['unit_price'=>'decimal:2'];

    public function product(){ return $this->belongsTo(Product::class); }
    public function v1(){ return $this->belongsTo(ProductVariantOption::class, 'variant1_option_id'); }
    public function v2(){ return $this->belongsTo(ProductVariantOption::class, 'variant2_option_id'); }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
protected $fillable = [
    'cart_id','product_id','v1_option_id','v2_option_id','qty',
    'unit_price','addons_total','line_total','spec_snapshot',
    'signature','customer_note'
];


    protected $casts = [
        'unit_price'   => 'decimal:2',
        'addons_total' => 'decimal:2',
        'line_total'   => 'decimal:2',
        'spec_snapshot'=> 'array',
    ];

    public function cart(){ return $this->belongsTo(Cart::class); }
    public function product(){ return $this->belongsTo(\App\Models\Product::class); }
}

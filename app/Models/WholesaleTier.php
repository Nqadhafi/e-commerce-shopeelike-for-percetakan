<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WholesaleTier extends Model
{
    protected $fillable = ['scope_type','scope_id','min_qty','unit_price'];
    protected $casts = ['unit_price'=>'decimal:2'];

    public function scope(){ return $this->morphTo(); }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Cart extends Model
{
    protected $fillable = ['user_id','session_id','status','currency'];

    public function items(): HasMany { return $this->hasMany(CartItem::class); }

    public function totals(): array
    {
        $subtotal = $this->items->sum(fn($i) => (float)$i->unit_price * $i->qty);
        $addons   = $this->items->sum('addons_total');
        $total    = $subtotal + $addons;
        return ['subtotal'=>$subtotal, 'addons'=>$addons, 'total'=>$total];
    }
}

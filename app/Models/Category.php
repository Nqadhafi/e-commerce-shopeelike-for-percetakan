<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name','slug','parent_id','position','is_active','image_url','description'];

    public function parent(){ return $this->belongsTo(Category::class, 'parent_id'); }
    public function children(){ return $this->hasMany(Category::class, 'parent_id'); }
    public function products(){
        return $this->belongsToMany(Product::class, 'product_category')
            ->withPivot(['is_primary','position'])->withTimestamps();
    }
}

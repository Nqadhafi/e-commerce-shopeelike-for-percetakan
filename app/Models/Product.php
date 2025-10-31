<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name','slug','unit_label','default_unit_price',
        'has_variants','has_addons','status','thumbnail_url','images_json','spec_json','is_active'
    ];

    protected $casts = [
        'has_variants'=>'bool','has_addons'=>'bool','is_active'=>'bool',
        'images_json'=>'array','spec_json'=>'array'
    ];

    public function categories(){
        return $this->belongsToMany(Category::class, 'product_category')
            ->withPivot(['is_primary','position'])->withTimestamps();
    }
    public function tags(){
        return $this->belongsToMany(Tag::class, 'product_tag')->withTimestamps();
    }
    public function variants(){ return $this->hasMany(ProductVariant::class); }
    public function addons(){ 
        return $this->belongsToMany(Addon::class, 'product_addons', 'product_id', 'addon_id')
            ->withPivot(['is_default','is_required','constraints_json'])->withTimestamps();
    }
    public function variantMatrix(){
    return $this->hasMany(ProductVariantMatrix::class);
}

}

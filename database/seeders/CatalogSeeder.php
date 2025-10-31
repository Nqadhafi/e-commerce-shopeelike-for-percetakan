<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\{
    Category, Tag, Product,
    ProductVariant, ProductVariantOption,
    Addon
};

class CatalogSeeder extends Seeder
{
    public function run(): void
    {
        /** === 1. CATEGORIES === */
        $categories = [
            ['name' => 'Kertas',        'slug' => 'kertas'],
            ['name' => 'Label & Stiker','slug' => 'stiker'],
            ['name' => 'Kemasan',       'slug' => 'kemasan'],
        ];
        foreach ($categories as $c) Category::create($c);

        /** === 2. TAGS === */
        $tags = [
            ['name' => 'best-seller', 'slug' => 'best-seller'],
            ['name' => 'premium',     'slug' => 'premium'],
            ['name' => 'doff-ready',  'slug' => 'doff-ready'],
            ['name' => 'eco-paper',   'slug' => 'eco-paper'],
        ];
        foreach ($tags as $t) Tag::create($t);

        /** === 3. ADD-ONS === */
        $addons = [
            ['code'=>'lamination_doff','label'=>'Laminasi Doff','type'=>'multiplier','billing_basis'=>'per_unit','amount'=>200,'is_active'=>1],
            ['code'=>'lamination_glossy','label'=>'Laminasi Glossy','type'=>'multiplier','billing_basis'=>'per_unit','amount'=>200,'is_active'=>1],
            ['code'=>'design_service','label'=>'Jasa Desain','type'=>'overall','billing_basis'=>'per_order','amount'=>15000,'is_active'=>1],
        ];
        foreach ($addons as $a) Addon::create($a);

        /** === 4. PRODUCTS === */

        // Produk 1 — Kartu Nama Premium (variasi + add-on)
        $p1 = Product::create([
            'name' => 'Kartu Nama Premium',
            'slug' => 'kartu-nama-premium',
            'unit_label' => 'pcs',
            'has_variants' => true,
            'has_addons' => true,
            'status' => 'published',
            'is_active' => true,
        ]);

        // Variasi: Bahan
        $v1 = $p1->variants()->create(['name'=>'Bahan','position'=>1,'is_active'=>true]);
        $v1->options()->createMany([
            ['label'=>'Art Carton 230gsm - 1 Sisi','unit_price'=>500],
            ['label'=>'Art Carton 230gsm - 2 Sisi','unit_price'=>900],
            ['label'=>'Art Carton 260gsm - 1 Sisi','unit_price'=>600],
            ['label'=>'Art Carton 260gsm - 2 Sisi','unit_price'=>1000],
        ]);

        // Variasi: Ukuran
        $v2 = $p1->variants()->create(['name'=>'Ukuran','position'=>2,'is_active'=>true]);
        $v2->options()->createMany([
            ['label'=>'9x5 cm','unit_price'=>0],
            ['label'=>'8x5 cm','unit_price'=>0],
        ]);

        // Sync kategori & tag
        $p1->categories()->sync([1 => ['is_primary'=>true]]);
        $p1->tags()->sync([1,2]);

        // Add-ons: Laminasi Doff + Glossy + Jasa Desain
        $p1->addons()->sync([
            1 => ['is_default'=>false,'is_required'=>false],
            2 => ['is_default'=>false,'is_required'=>false],
            3 => ['is_default'=>false,'is_required'=>false],
        ]);

        // Produk 2 — Stiker Vinyl (flat + add-on)
        $p2 = Product::create([
            'name' => 'Stiker Vinyl',
            'slug' => 'stiker-vinyl',
            'unit_label' => 'lembar',
            'default_unit_price' => 2500,
            'has_variants' => false,
            'has_addons' => true,
            'status' => 'published',
            'is_active' => true,
        ]);
        $p2->categories()->sync([2 => ['is_primary'=>true]]);
        $p2->tags()->sync([3]);
        $p2->addons()->sync([
            1 => ['is_default'=>false,'is_required'=>false],
            2 => ['is_default'=>false,'is_required'=>false],
        ]);

        // Produk 3 — Sertifikat A4 (flat, tanpa add-on)
        $p3 = Product::create([
            'name' => 'Sertifikat A4',
            'slug' => 'sertifikat-a4',
            'unit_label' => 'lembar',
            'default_unit_price' => 5000,
            'has_variants' => false,
            'has_addons' => false,
            'status' => 'published',
            'is_active' => true,
        ]);
        $p3->categories()->sync([1 => ['is_primary'=>true]]);
        $p3->tags()->sync([4]);

        echo "✅ CatalogSeeder selesai.\n";
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WebBanner;

class WebBannerSeeder extends Seeder
{
    public function run(): void
    {
        WebBanner::insert([
            [
                'title' => 'Promo Kartu Nama Premium',
                'image_url' => '/storage/banners/banner-kartu-nama.jpg',
                'link_url' => '/products/kartu-nama-premium',
                'position' => 3,
                'is_active' => true,
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'title' => 'Cetak Brosur A4 Murah!',
                'image_url' => '/storage/banners/banner-brosur.jpg',
                'link_url' => '/products/brosur-a4-full-color',
                'position' => 4,
                'is_active' => true,
                'created_at' => now(), 'updated_at' => now(),
            ],
                        [
                'title' => 'Cetak Brosur A4 Murah!',
                'image_url' => '/storage/banners/banner-brosur.jpg',
                'link_url' => '/products/brosur-a4-full-color',
                'position' => 5,
                'is_active' => true,
                'created_at' => now(), 'updated_at' => now(),
            ],
        ]);
    }
}

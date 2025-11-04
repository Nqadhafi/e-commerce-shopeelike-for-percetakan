<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('cart_items', function (Blueprint $t) {
      $t->id();
      $t->foreignId('cart_id')->constrained('carts')->cascadeOnDelete();
      $t->foreignId('product_id')->constrained('products')->cascadeOnDelete();
      $t->unsignedBigInteger('v1_option_id')->nullable(); // product_variant_options.id
      $t->unsignedBigInteger('v2_option_id')->nullable(); // product_variant_options.id
      $t->unsignedInteger('qty')->default(1);

      // snapshot harga saat add-to-cart
      $t->decimal('unit_price', 12, 2);     // dari matrix / default_unit_price
      $t->decimal('addons_total', 12, 2)->default(0); // total addon utk baris ini (per_unit*qty + per_order sekali)
      $t->decimal('line_total', 12, 2);     // (unit_price*qty) + addons_total

      // snapshot spesifikasi (nama produk, label varian, gambar combo, addon terpilih)
      $t->json('spec_snapshot')->nullable();

      // untuk deteksi item identik (product + varian + addon set)
      $t->string('signature', 191)->index();

      $t->timestamps();
    });
  }
  public function down(): void { Schema::dropIfExists('cart_items'); }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('products', function (Blueprint $t) {
      $t->id();
      $t->string('name', 150);
      $t->string('slug', 150)->unique();
      $t->string('unit_label', 30)->default('pcs');
      $t->decimal('default_unit_price', 12, 2)->nullable(); // wajib jika has_variants=false
      $t->boolean('has_variants')->default(false);
      $t->boolean('has_addons')->default(false);
      $t->enum('status', ['draft','published'])->default('draft');
      $t->string('thumbnail_url')->nullable();
      $t->json('images_json')->nullable();
      $t->json('spec_json')->nullable();
      $t->boolean('is_active')->default(true);
      $t->timestamps();
    });

    Schema::create('product_variants', function (Blueprint $t) {
      $t->id();
      $t->foreignId('product_id')->constrained()->cascadeOnDelete();
      $t->string('name', 100); // Bahan/Ukuran/Sisi
      $t->unsignedSmallInteger('position')->default(0);
      $t->boolean('is_active')->default(true);
      $t->timestamps();
    });

    Schema::create('product_variant_options', function (Blueprint $t) {
      $t->id();
      $t->foreignId('product_variant_id')->constrained()->cascadeOnDelete();
      $t->string('label', 100);
      $t->decimal('unit_price', 12, 2); // harga inti per opsi
      $t->string('sku_suffix', 50)->nullable();
      $t->boolean('is_active')->default(true);
      $t->timestamps();
      $t->index(['product_variant_id','is_active']);
    });

    Schema::create('wholesale_tiers', function (Blueprint $t) {
      $t->id();
      $t->enum('scope_type', ['product','variant_option']);
      $t->unsignedBigInteger('scope_id');
      $t->unsignedInteger('min_qty');
      $t->decimal('unit_price', 12, 2);
      $t->timestamps();
      $t->index(['scope_type','scope_id','min_qty']);
    });

    Schema::create('addons_catalog', function (Blueprint $t) {
      $t->id();
      $t->string('code', 50)->unique();
      $t->string('label', 100);
      $t->enum('type', ['multiplier','overall']);   // multiplier=per_unit, overall=per_order
      $t->enum('billing_basis', ['per_unit','per_order']);
      $t->decimal('amount', 12, 2);
      $t->boolean('is_active')->default(true);
      $t->text('notes')->nullable();
      $t->timestamps();
    });

    Schema::create('product_addons', function (Blueprint $t) {
      $t->id();
      $t->foreignId('product_id')->constrained()->cascadeOnDelete();
      $t->foreignId('addon_id')->constrained('addons_catalog')->cascadeOnDelete();
      $t->boolean('is_default')->default(false);
      $t->boolean('is_required')->default(false);
      $t->json('constraints_json')->nullable();
      $t->timestamps();
      $t->unique(['product_id','addon_id']);
    });
  }

  public function down(): void {
    Schema::dropIfExists('product_addons');
    Schema::dropIfExists('addons_catalog');
    Schema::dropIfExists('wholesale_tiers');
    Schema::dropIfExists('product_variant_options');
    Schema::dropIfExists('product_variants');
    Schema::dropIfExists('products');
  }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('product_variant_matrices', function (Blueprint $t) {
      $t->id();
      $t->foreignId('product_id')->constrained()->cascadeOnDelete();
      $t->foreignId('variant1_option_id')->constrained('product_variant_options')->cascadeOnDelete();
      $t->foreignId('variant2_option_id')->nullable()->constrained('product_variant_options')->nullOnDelete();
      $t->decimal('unit_price', 12, 2);
      $t->string('image_path')->nullable(); // upload per kombinasi (nullable)
      $t->timestamps();

      $t->unique(['product_id','variant1_option_id','variant2_option_id'], 'uniq_matrix_combo');
      $t->index(['product_id']);
    });
  }
  public function down(): void {
    Schema::dropIfExists('product_variant_matrices');
  }
};

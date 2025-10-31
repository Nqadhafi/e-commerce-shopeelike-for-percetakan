<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('categories', function (Blueprint $t) {
      $t->id();
      $t->string('name', 150);
      $t->string('slug', 150)->unique();
      $t->foreignId('parent_id')->nullable()->constrained('categories')->nullOnDelete();
      $t->unsignedSmallInteger('position')->default(0);
      $t->boolean('is_active')->default(true);
      $t->string('image_url')->nullable();
      $t->text('description')->nullable();
      $t->timestamps();
    });

    Schema::create('tags', function (Blueprint $t) {
      $t->id();
      $t->string('name', 100);
      $t->string('slug', 120)->unique();
      $t->boolean('is_active')->default(true);
      $t->timestamps();
    });

    Schema::create('product_category', function (Blueprint $t) {
      $t->id();
      $t->foreignId('product_id')->constrained()->cascadeOnDelete();
      $t->foreignId('category_id')->constrained()->cascadeOnDelete();
      $t->boolean('is_primary')->default(false);
      $t->unsignedSmallInteger('position')->default(0);
      $t->timestamps();
      $t->unique(['product_id','category_id']);
      $t->index(['category_id','product_id']);
    });

    Schema::create('product_tag', function (Blueprint $t) {
      $t->id();
      $t->foreignId('product_id')->constrained()->cascadeOnDelete();
      $t->foreignId('tag_id')->constrained()->cascadeOnDelete();
      $t->timestamps();
      $t->unique(['product_id','tag_id']);
    });
  }

  public function down(): void {
    Schema::dropIfExists('product_tag');
    Schema::dropIfExists('product_category');
    Schema::dropIfExists('tags');
    Schema::dropIfExists('categories');
  }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('carts', function (Blueprint $t) {
      $t->id();
      $t->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
      $t->string('session_id', 100)->index();
      $t->string('status', 20)->default('open'); // open, locked, ordered
      $t->string('currency', 3)->default('IDR');
      $t->timestamps();
    });
  }
  public function down(): void { Schema::dropIfExists('carts'); }
};

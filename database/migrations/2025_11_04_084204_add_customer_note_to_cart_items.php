<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::table('cart_items', function (Blueprint $t) {
      $t->text('customer_note')->nullable()->after('spec_snapshot');
    });
  }

  public function down(): void {
    Schema::table('cart_items', function (Blueprint $t) {
      $t->dropColumn('customer_note');
    });
  }
};

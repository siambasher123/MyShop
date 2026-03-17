<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('orders1', function (Blueprint $table) {
            $table->json('product_codes')->nullable()->after('products');
        });
    }

    public function down(): void
    {
        Schema::table('orders1', function (Blueprint $table) {
            $table->dropColumn('product_codes');
        });
    }
};

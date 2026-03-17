<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('orders1', function (Blueprint $table) {
            if (Schema::hasColumn('orders1', 'shipping_charge')) {
                $table->dropColumn('shipping_charge');
            }
            if (Schema::hasColumn('orders1', 'discount')) {
                $table->dropColumn('discount');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders1', function (Blueprint $table) {
            $table->decimal('shipping_charge', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
        });
    }
};

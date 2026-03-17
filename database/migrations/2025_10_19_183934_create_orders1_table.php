<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('orders1', function (Blueprint $table) {
            $table->id();

            // 🧑 User details
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('full_name');
            $table->string('email')->nullable();
            $table->string('mobile_number');
            $table->string('delivery_area');
            $table->text('address');

            // 🛍 Product + order summary
            $table->json('products'); // array of {id, name, price, qty}
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('shipping_charge', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);

            $table->string('status')->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('orders1');
    }
};

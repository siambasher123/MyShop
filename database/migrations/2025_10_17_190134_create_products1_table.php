<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('products1', function (Blueprint $table) {
            $table->id();
            $table->string('category');      // Men, Women, Kids, ...
            $table->string('subcategory');   // T-Shirts, Sarees, ...
            $table->string('code')->unique();
            $table->decimal('price', 10, 2);
            $table->unsignedInteger('stock')->default(0);
            $table->text('description')->nullable();
            $table->string('image')->nullable(); // stored path (optional to show)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products1');
    }
};

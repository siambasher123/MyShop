<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up()
{
    Schema::create('review1', function (Blueprint $table) {
        $table->id();
        $table->string('category');
        $table->string('subcategory');
        $table->string('code');
        $table->decimal('price', 10, 2);
        $table->string('username');
        $table->integer('rating');
        $table->text('review');
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('review1');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('extended_details_related_product', function (Blueprint $table) {
            $table->id();
            $table->string('product_id');
            $table->string('title')->nullable();
            $table->text('descriptions')->nullable();
            $table->string('rating')->nullable();
            $table->integer('price')->nullable();
            $table->string('image')->nullable();


            $table->foreignId('related_products_id')->constrained('extended_details_related_products')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('extended_details_related_product');
    }
};

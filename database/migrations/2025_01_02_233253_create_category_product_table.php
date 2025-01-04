<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('category_product', function (Blueprint $table) {
            $table->id();

            // Referencia a categories
            $table->unsignedBigInteger('category_id');
            $table->foreign('category_id')
                ->references('id')
                ->on('categories')
                ->onDelete('cascade');

            // Referencia a products
            $table->unsignedBigInteger('product_id');
            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->onDelete('cascade');

            // Un producto no se repita 2 veces en la misma category
            $table->unique(['category_id', 'product_id']);

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('category_product');
    }
};

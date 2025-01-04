<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            // "product_id" de la API (p. ej. "355852021513" o "B0DGHQQK9D")
            $table->string('product_id');

            // "vendor" (p. ej. "ebay", "amazon")
            $table->string('vendor');

            // Clave compuesta: (product_id, vendor)
            $table->unique(['product_id', 'vendor']);

            // Campos principales
            $table->string('image')->nullable();   // link principal
            $table->string('title')->nullable();
            $table->decimal('price', 10, 2)->default(0);

            $table->enum('type', ['simple', 'configurable'])->nullable();
            $table->boolean('has_variants')->default(false);
            $table->boolean('has_combinations')->default(false);
            $table->string('combination_separator')->default('_')->nullable();

            $table->string('brand')->nullable();
            $table->string('brand_store_link')->nullable();
            $table->decimal('shipping_price', 10, 2)->default(0);
            $table->integer('score')->nullable()->default(0);
            $table->decimal('rating', 3, 1)->nullable()->default(0);
            $table->text('description')->nullable();

            // HTML campos:
            $table->text('html_description')->nullable();
            $table->text('html_features')->nullable();
            $table->text('html_product_specfics')->nullable();
            $table->text('html_images')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('products');
    }
};

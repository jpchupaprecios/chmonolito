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

            $table->string('product_id');

            // "vendor" (p. ej. "ebay", "amazon")
            $table->string('vendor');

            // Clave compuesta: (product_id, vendor)
            $table->unique(['product_id', 'vendor']);

            // Campos principales
            $table->string('image');   // link principal
            $table->string('title');
            $table->text('thumbs')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->decimal('shipping_price', 10, 2)->default(0);
            $table->integer('score')->nullable()->default(0);
            $table->decimal('rating', 3, 1)->nullable()->default(0);
            $table->string('brand')->nullable();
            $table->string('brand_store_link')->nullable();

            $table->enum('type', ['simple', 'configurable', 'search']);
            $table->boolean('has_variants')->default(false);
            $table->boolean('has_combinations')->default(false);
            $table->string('combination_separator')->default('_')->nullable();

            $table->text('variants')->nullable();
            $table->text('description')->nullable();
            $table->text('categories')->nullable();
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

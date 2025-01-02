<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_details', function (Blueprint $table) {
            $table->id();
            $table->string('product_id');
            $table->string('parent_product_id')->nullable();
            $table->string('type');
            $table->mediumText('title');
            $table->integer('score')->nullable();
            $table->float('rating')->nullable();
            $table->float('price');
            $table->mediumText('breadcrumbs_flat');
            $table->float('shipping_price')->nullable();
            $table->string('brand')->nullable();
            $table->string('image');
            $table->boolean('has_variants');

            $table->text('description')->nullable();
            $table->text('html')->nullable();
            $table->string('vendor');

            $table->index('product_id');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_details');
    }
}

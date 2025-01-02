<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_results', function (Blueprint $table) {
            $table->id();

            $table->string("product_id");
            $table->mediumText("title");
            $table->float("price");
            $table->string("image");
            $table->integer("position");
            $table->integer("score")->nullable();
            $table->float("rating")->nullable();
            $table->text("description")->nullable();
            $table->timestamps();

            $table->foreignId('result_id')->constrained('results')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_results');
    }
}

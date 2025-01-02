<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterImageProductResultTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_results', function (Blueprint $table) {
            $table->mediumText("image")->change();
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_results', function (Blueprint $table) {
            // Revertir el cambio de columna, asumiendo que la columna original era de tipo string
            $table->string("image")->change();
        });
    }
}

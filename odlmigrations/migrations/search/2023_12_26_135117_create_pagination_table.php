<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaginationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pagination', function (Blueprint $table) {
            $table->id();
            $table->integer("current");
            $table->integer("totalPages");
            $table->binary("next")->nullable();
            $table->binary("prev")->nullable();
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
        Schema::dropIfExists('pagination');
    }
}

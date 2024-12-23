<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRefinementLinkTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('refinement_links', function (Blueprint $table) {
            $table->id();
            $table->binary("link");
            $table->string("title");
            $table->boolean("checked")->default(false);
            $table->timestamps();
            $table->foreignId('refinement_id')->constrained('refinement')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('refinement_links');
    }
}

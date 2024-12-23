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
        Schema::create('extended_details_also_bought_product', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->text('descriptions')->nullable();
            $table->text('rating')->nullable();
            $table->text('image')->nullable();

            $table->foreignId('also_bought_id')->constrained('extended_details_also_bought')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('extended_details_also_bought_product');
    }
};

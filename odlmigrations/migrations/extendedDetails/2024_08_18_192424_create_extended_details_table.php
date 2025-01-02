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
        Schema::create('extended_details', function (Blueprint $table) {
            $table->id();
            $table->string('product_id');

            $table->longText('html_description')->nullable();
            $table->text('html_features')->nullable();
            $table->text('html_images')->nullable();
            $table->text('html_product_specfics')->nullable();
            $table->text('brand')->nullable();

            $table->foreignId('product_details_id')->constrained('product_details')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('extended_details');
    }
};

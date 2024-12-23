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
        Schema::create('variant_combinations', function (Blueprint $table) {
            $table->id();
            $table->string('variant_key');
            $table->string('variant_sku');

            $table->foreignId('product_details_id')->constrained('product_details')->onDelete('cascade');


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('variant_combinations');
    }
};

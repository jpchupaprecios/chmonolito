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
        Schema::create('chunks_product_log', function (Blueprint $table) {
            $table->id();
            $table->string('sku');
            $table->string('vendor')->default('amazon');

            $table->text('chunk_html_output');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chunks_product_log');
    }
};

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
        Schema::create('category_history', function (Blueprint $table) {
            $table->id();

            $table->string('category');
            $table->string('category_url');
            $table->string('vendor');

            $table->string('user_agent')->nullable();
            $table->string('customer')->nullable();
            $table->text('customer_data')->nullable();
            $table->string('ip')->nullable();
            $table->integer('count')->default(0);

            $table->foreignId('user_cart_id')->constrained('user_cart')->onDelete('cascade');
            $table->foreignId('user_cookie_id')->constrained('user_cookie')->onDelete('cascade');


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('table_category_history');
    }
};

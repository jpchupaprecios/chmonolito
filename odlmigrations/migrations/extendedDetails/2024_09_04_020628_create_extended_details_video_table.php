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
        Schema::create('extended_details_video', function (Blueprint $table) {
            $table->id();

            $table->string('title')->nullable();
            $table->text('data')->nullable();

            $table->foreignId('extended_details_videos_id')->constrained('extended_details_videos')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('extended_details_video');
    }
};

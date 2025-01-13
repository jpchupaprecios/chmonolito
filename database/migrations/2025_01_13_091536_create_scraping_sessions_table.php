<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('scraping_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('client_session_id', 255);
            // Campos para tu scraping
            $table->text('amazon_cookie')->nullable();
            $table->string('user_agent', 255)->nullable();
            // Podrías querer más campos: ip, amazon_session_id, etc.

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('scraping_sessions');
    }
};

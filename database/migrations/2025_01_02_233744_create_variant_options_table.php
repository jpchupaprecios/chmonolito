<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('variant_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_variant_id')
                ->constrained('product_variants')
                ->onDelete('cascade');

            $table->string('title')->nullable();              // "Black"
            $table->string('option_product_id')->nullable();  // "B0DGHLFC4M" etc.
            $table->string('image')->nullable();
            $table->boolean('selected')->default(false);
            $table->boolean('available')->default(true);

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('variant_options');
    }
};

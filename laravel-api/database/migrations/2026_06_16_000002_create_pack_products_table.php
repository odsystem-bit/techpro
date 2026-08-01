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
        Schema::create('pack_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pack_id');
            $table->unsignedBigInteger('product_id');
            $table->timestamps();

            $table->foreign('pack_id')->references('id')->on('packs')->cascadeOnDelete();
            $table->foreign('product_id')->references('id')->on('products')->cascadeOnDelete();
            
            $table->unique(['pack_id', 'product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pack_products', function (Blueprint $table) {
            $table->dropForeign(['pack_id']);
            $table->dropForeign(['product_id']);
        });
        Schema::dropIfExists('pack_products');
    }
};

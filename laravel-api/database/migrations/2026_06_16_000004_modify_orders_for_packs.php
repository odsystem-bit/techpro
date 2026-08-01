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
        Schema::table('orders', function (Blueprint $table) {
            // Remplacer product_id par un système polymorphe
            $table->dropForeign(['product_id']);
            $table->dropColumn('product_id');
            
            $table->unsignedBigInteger('orderable_id')->nullable()->after('order_number');
            $table->string('orderable_type')->nullable()->after('orderable_id')->comment('App\\Models\\Product|App\\Models\\Pack');
            
            // Index pour le polymorphisme
            $table->index(['orderable_id', 'orderable_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['orderable_id', 'orderable_type']);
            $table->dropColumn(['orderable_id', 'orderable_type']);
            
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
        });
    }
};

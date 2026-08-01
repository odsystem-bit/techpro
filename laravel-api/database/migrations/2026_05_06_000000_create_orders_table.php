<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('customer_email');
            $table->string('customer_name')->nullable();
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('unit_price', 15, 2);
            $table->decimal('total_amount', 15, 2);
            $table->string('currency', 10)->default('XOF');
            $table->string('payment_gateway')->default('moneroo');
            $table->string('payment_status')->default('pending')->comment('pending|paid|failed|refunded');
            $table->string('moneroo_transaction_id')->nullable()->index();
            $table->string('download_token', 64)->unique()->nullable();
            $table->unsignedSmallInteger('download_count')->default(0);
            $table->unsignedSmallInteger('download_limit')->default(3);
            $table->timestamp('download_expires_at')->nullable();
            $table->timestamp('downloaded_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('security_logs', function (Blueprint $table) {
            $table->id();
            $table->string('event_type'); // 'failed_login', 'ip_blocked', 'suspicious_activity', etc.
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('email')->nullable();
            $table->string('url')->nullable();
            $table->text('details')->nullable();
            $table->string('country', 2)->nullable();
            $table->string('city')->nullable();
            $table->timestamp('created_at');
            
            $table->index(['event_type', 'created_at']);
            $table->index('ip_address');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('security_logs');
    }
};

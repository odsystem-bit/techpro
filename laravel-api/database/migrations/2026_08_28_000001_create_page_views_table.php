<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('page_views', function (Blueprint $table) {
            $table->id();
            $table->string('url');
            $table->string('path');
            $table->string('route_name')->nullable();
            $table->string('method', 10)->default('GET');
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->string('referer')->nullable();
            $table->string('session_id', 100)->nullable();
            $table->timestamps();

            $table->index(['path', 'created_at']);
            $table->index('created_at');
            $table->index('session_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_views');
    }
};

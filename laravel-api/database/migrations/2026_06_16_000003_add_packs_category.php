<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Insérer la catégorie "Packs" si elle n'existe pas déjà
        DB::table('categories')->insertOrIgnore([
            'name' => 'Packs',
            'slug' => 'packs',
            'description' => 'Catégorie pour les packs de produits (bundles)',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('categories')->where('slug', 'packs')->delete();
    }
};

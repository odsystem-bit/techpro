<?php

use App\Http\Controllers\Api\ApiController;
use Illuminate\Support\Facades\Route;

Route::middleware('api.token')->group(function () {
    // Catalogue complet (produits + packs) pour le bot
    Route::get('/catalog', [ApiController::class, 'catalog']);

    // Détail d'un produit par slug
    Route::get('/products/{slug}', [ApiController::class, 'product']);

    // Créer une session de paiement (checkout)
    Route::post('/checkout', [ApiController::class, 'checkout']);

    // Stats pour le propriétaire
    Route::get('/stats', [ApiController::class, 'stats']);

    // Commandes récentes
    Route::get('/orders', [ApiController::class, 'orders']);
});

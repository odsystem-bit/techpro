<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Shop\ProductController;
use App\Http\Controllers\Shop\CartController;
use App\Http\Controllers\Payment\MonerooWebhookController;
use App\Http\Controllers\Download\DownloadController;
use App\Http\Controllers\Shop\PackController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductAdminController;
use App\Http\Controllers\Admin\CategoryAdminController;
use App\Http\Controllers\Admin\OrderAdminController;
use App\Http\Controllers\Admin\HomePageController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\HeroSlideController;
use App\Http\Controllers\Admin\TestimonialController;
use App\Http\Controllers\Admin\PackAdminController;
use App\Http\Controllers\Admin\FormationAdminController;
use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Shop\OdibotController;

// ── Shop public ──────────────────────────────────────────────────────────────
Route::get('/', [ProductController::class, 'home'])->name('home');

Route::prefix('shop')->name('shop.')->group(function () {
    Route::get('/', [ProductController::class, 'index'])->name('index');
    Route::get('/product/{product:slug}', [ProductController::class, 'show'])->name('show');
    Route::get('/cart', [CartController::class, 'index'])->name('cart');
    Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/add-pack/{pack}', [CartController::class, 'addPack'])->name('cart.add.pack');
    Route::patch('/cart/update/{product}', [CartController::class, 'update'])->name('cart.update');
    Route::patch('/cart/update-pack/{pack}', [CartController::class, 'updatePack'])->name('cart.update.pack');
    Route::delete('/cart/remove/{product}', [CartController::class, 'remove'])->name('cart.remove');
    Route::delete('/cart/remove-pack/{pack}', [CartController::class, 'removePack'])->name('cart.remove.pack');
    Route::get('/checkout', [CartController::class, 'checkout'])->name('checkout');
    Route::post('/checkout', [CartController::class, 'processCheckout'])->name('checkout.process');
    Route::get('/checkout/success', [CartController::class, 'success'])->name('checkout.success');
    Route::get('/checkout/cancel', [CartController::class, 'cancel'])->name('checkout.cancel');
});

// ── Téléchargement sécurisé ───────────────────────────────────────────────
Route::get('/download/{token}', [DownloadController::class, 'handle'])->name('download');
Route::get('/download-pack/{token}/{product_id}', [DownloadController::class, 'handlePackProduct'])->name('download.pack.product');
Route::get('/download-pack-file/{token}/{file_id}', [DownloadController::class, 'handlePackFile'])->name('download.pack.file');
Route::get('/download-module/{token}/{module_id}', [DownloadController::class, 'handleFormationModule'])->name('download.module');
Route::get('/download-module-file/{token}/{file_id}', [DownloadController::class, 'handleFormationModuleFile'])->name('download.module.file');

// ── Packs (shop public) ───────────────────────────────────────────────────
Route::prefix('packs')->name('packs.')->group(function () {
    Route::get('/', [PackController::class, 'index'])->name('index');
    Route::get('/{pack:slug}', [PackController::class, 'show'])->name('show');
    Route::get('/view/{token}', [PackController::class, 'view'])->name('view');
});

// ── Formations (vue après achat) ──────────────────────────────────────────
Route::get('/formation/view/{token}', [DownloadController::class, 'viewFormation'])->name('formations.view');

// ── ODIBOT (téléchargement du bot) ────────────────────────────────────────
Route::get('/odibot/download', [OdibotController::class, 'download'])->name('odibot.download');
Route::get('/odibot/info', [OdibotController::class, 'info'])->name('odibot.info');
// ── Pages statiques ──────────────────────────────────────────────────────────────
Route::redirect('/about', '/apropos');
Route::view('/apropos', 'apropos')->name('apropos');
// ── Webhook Moneroo (pas de CSRF) ─────────────────────────────────────────
Route::post('/api/webhooks/moneroo', [MonerooWebhookController::class, 'handle'])
    ->name('api.webhooks.moneroo');

// ── Admin Panel (Route personnalisée sécurisée) ─────────────────────────────────────
Route::prefix('gestion-tpf-x9k2m')->name('admin.')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('login.post');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

    Route::middleware('admin.auth')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        Route::prefix('products')->name('products.')->group(function () {
            Route::get('/', [ProductAdminController::class, 'index'])->name('index');
            Route::get('/create', [ProductAdminController::class, 'create'])->name('create');
            Route::post('/', [ProductAdminController::class, 'store'])->name('store');
            Route::get('/{product}/edit', [ProductAdminController::class, 'edit'])->name('edit');
            Route::put('/{product}', [ProductAdminController::class, 'update'])->name('update');
            Route::delete('/{product}', [ProductAdminController::class, 'destroy'])->name('destroy');
            Route::patch('/{product}/featured', [ProductAdminController::class, 'toggleFeatured'])->name('toggle-featured');
            Route::delete('/{product}/modules/{module}', [ProductAdminController::class, 'deleteModule'])->name('modules.destroy');
            Route::delete('/{product}/modules/{module}/files/{file}', [ProductAdminController::class, 'deleteModuleFile'])->name('module-files.destroy');
            Route::delete('/{product}/gallery/{image}', [ProductAdminController::class, 'deleteGalleryImage'])->name('gallery.destroy');
        });

        Route::prefix('categories')->name('categories.')->group(function () {
            Route::get('/', [CategoryAdminController::class, 'index'])->name('index');
            Route::get('/create', [CategoryAdminController::class, 'create'])->name('create');
            Route::post('/', [CategoryAdminController::class, 'store'])->name('store');
            Route::get('/{category}/edit', [CategoryAdminController::class, 'edit'])->name('edit');
            Route::put('/{category}', [CategoryAdminController::class, 'update'])->name('update');
            Route::delete('/{category}', [CategoryAdminController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('packs')->name('packs.')->group(function () {
            Route::get('/', [PackAdminController::class, 'index'])->name('index');
            Route::get('/create', [PackAdminController::class, 'create'])->name('create');
            Route::post('/', [PackAdminController::class, 'store'])->name('store');
            Route::get('/{pack}/edit', [PackAdminController::class, 'edit'])->name('edit');
            Route::put('/{pack}', [PackAdminController::class, 'update'])->name('update');
            Route::delete('/{pack}', [PackAdminController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('formations')->name('formations.')->group(function () {
            Route::get('/', [FormationAdminController::class, 'index'])->name('index');
            Route::get('/create', [FormationAdminController::class, 'create'])->name('create');
            Route::post('/', [FormationAdminController::class, 'store'])->name('store');
            Route::get('/{product}/edit', [FormationAdminController::class, 'edit'])->name('edit');
            Route::put('/{product}', [FormationAdminController::class, 'update'])->name('update');
            Route::delete('/{product}', [FormationAdminController::class, 'destroy'])->name('destroy');
            Route::delete('/{product}/modules/{module}', [FormationAdminController::class, 'deleteModule'])->name('modules.destroy');
            Route::delete('/{product}/modules/{module}/files/{file}', [FormationAdminController::class, 'deleteModuleFile'])->name('module-files.destroy');
            Route::delete('/{product}/gallery/{imageId}', [FormationAdminController::class, 'deleteGalleryImage'])->name('gallery.destroy');
        });

        Route::prefix('orders')->name('orders.')->group(function () {
            Route::get('/', [OrderAdminController::class, 'index'])->name('index');
            Route::get('/{order}', [OrderAdminController::class, 'show'])->name('show');
        });

        Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics');

        Route::get('/home', [HomePageController::class, 'edit'])->name('home.edit');
        Route::post('/home', [HomePageController::class, 'update'])->name('home.update');

        Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
        Route::post('/settings', [SettingsController::class, 'update'])->name('settings.update');

        Route::prefix('testimonials')->name('testimonials.')->group(function () {
            Route::get('/', [TestimonialController::class, 'index'])->name('index');
            Route::get('/create', [TestimonialController::class, 'create'])->name('create');
            Route::post('/', [TestimonialController::class, 'store'])->name('store');
            Route::get('/{testimonial}/edit', [TestimonialController::class, 'edit'])->name('edit');
            Route::put('/{testimonial}', [TestimonialController::class, 'update'])->name('update');
            Route::delete('/{testimonial}', [TestimonialController::class, 'destroy'])->name('destroy');
        });
    });
});

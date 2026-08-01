<?php

namespace App\Http\Controllers\Download;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\PackFile;
use App\Models\Product;
use App\Models\FormationModule;
use App\Models\FormationModuleFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DownloadController extends Controller
{
    public function handle(Request $request, string $token)
    {
        $order = Order::where('download_token', $token)->firstOrFail();

        if (! $order->canDownload()) {
            abort(403, 'Ce lien de téléchargement est expiré ou a atteint sa limite.');
        }

        $orderable = $order->orderable;

        if (! $orderable) {
            abort(404, 'Produit ou pack introuvable.');
        }

        // Si c'est un pack, rediriger vers la page des packs
        if ($orderable instanceof \App\Models\Pack) {
            return redirect()->route('packs.view', ['token' => $token]);
        }

        // Si c'est une formation avec des modules, rediriger vers la page de formation
        if ($orderable instanceof Product && $orderable->isFormation() && $orderable->modules()->exists()) {
            return redirect()->route('formations.view', ['token' => $token]);
        }

        // Si c'est un produit simple
        if (! $orderable->file_path) {
            abort(404, 'Fichier introuvable.');
        }

        if (! Storage::disk('local')->exists($orderable->file_path)) {
            abort(404, 'Le fichier n\'est plus disponible.');
        }

        $order->increment('download_count');
        if ($order->downloaded_at === null) {
            $order->update(['downloaded_at' => now()]);
        }

        $filename = $orderable->slug . '.' . pathinfo($orderable->file_path, PATHINFO_EXTENSION);

        return Storage::disk('local')->download($orderable->file_path, $filename);
    }

    public function handlePackProduct(Request $request, string $token, int $productId)
    {
        $order = Order::where('download_token', $token)->firstOrFail();

        if (! $order->canDownload()) {
            abort(403, 'Ce lien de téléchargement est expiré ou a atteint sa limite.');
        }

        $pack = $order->pack;

        if (! $pack) {
            abort(404, 'Pack introuvable.');
        }

        // Vérifier que le produit fait partie du pack
        $product = $pack->products()->where('products.id', $productId)->first();

        if (! $product) {
            abort(404, 'Ce produit ne fait pas partie de ce pack.');
        }

        if (! $product->file_path) {
            abort(404, 'Fichier introuvable.');
        }

        if (! Storage::disk('local')->exists($product->file_path)) {
            abort(404, 'Le fichier n\'est plus disponible.');
        }

        $order->increment('download_count');
        if ($order->downloaded_at === null) {
            $order->update(['downloaded_at' => now()]);
        }

        $filename = $product->slug . '.' . pathinfo($product->file_path, PATHINFO_EXTENSION);

        return Storage::disk('local')->download($product->file_path, $filename);
    }

    public function handlePackFile(Request $request, string $token, int $fileId)
    {
        $order = Order::where('download_token', $token)->firstOrFail();

        if (! $order->canDownload()) {
            abort(403, 'Ce lien de téléchargement est expiré ou a atteint sa limite.');
        }

        $pack = $order->orderable;

        if (! $pack || ! ($pack instanceof \App\Models\Pack)) {
            abort(404, 'Pack introuvable.');
        }

        // Vérifier que le fichier fait partie du pack
        $packFile = $pack->files()->where('id', $fileId)->first();

        if (! $packFile) {
            abort(404, 'Ce fichier ne fait pas partie de ce pack.');
        }

        if (! Storage::disk('public')->exists($packFile->file_path)) {
            abort(404, 'Le fichier n\'est plus disponible.');
        }

        $order->increment('download_count');
        if ($order->downloaded_at === null) {
            $order->update(['downloaded_at' => now()]);
        }

        return Storage::disk('public')->download($packFile->file_path, $packFile->name);
    }

    public function handleFormationModule(Request $request, string $token, int $moduleId)
    {
        $order = Order::where('download_token', $token)->firstOrFail();

        if (! $order->canDownload()) {
            abort(403, 'Ce lien de téléchargement est expiré ou a atteint sa limite.');
        }

        $product = $order->orderable;

        if (! $product || ! ($product instanceof Product)) {
            abort(404, 'Produit introuvable.');
        }

        $module = FormationModule::where('product_id', $product->id)->where('id', $moduleId)->first();

        if (! $module) {
            abort(404, 'Ce module ne fait pas partie de cette formation.');
        }

        if (! $module->file_path) {
            abort(404, 'Ce module n\'a pas de fichier à télécharger.');
        }

        if (! Storage::disk('local')->exists($module->file_path)) {
            abort(404, 'Le fichier n\'est plus disponible.');
        }

        $order->increment('download_count');
        if ($order->downloaded_at === null) {
            $order->update(['downloaded_at' => now()]);
        }

        $filename = $module->title . '.' . pathinfo($module->file_path, PATHINFO_EXTENSION);

        return Storage::disk('local')->download($module->file_path, $filename);
    }

    public function handleFormationModuleFile(Request $request, string $token, int $fileId)
    {
        $order = Order::where('download_token', $token)->firstOrFail();

        if (! $order->canDownload()) {
            abort(403, 'Ce lien de téléchargement est expiré ou a atteint sa limite.');
        }

        $product = $order->orderable;

        if (! $product || ! ($product instanceof Product)) {
            abort(404, 'Produit introuvable.');
        }

        $file = FormationModuleFile::with('module')->where('id', $fileId)->first();

        if (! $file || ! $file->module || $file->module->product_id !== $product->id) {
            abort(404, 'Ce fichier ne fait pas partie de cette formation.');
        }

        if (! Storage::disk('local')->exists($file->file_path)) {
            abort(404, 'Le fichier n\'est plus disponible.');
        }

        $order->increment('download_count');
        if ($order->downloaded_at === null) {
            $order->update(['downloaded_at' => now()]);
        }

        return Storage::disk('local')->download($file->file_path, $file->original_name);
    }

    public function viewFormation(string $token)
    {
        $order = Order::where('download_token', $token)->firstOrFail();

        if (! $order->canDownload()) {
            abort(403, 'Ce lien est expiré ou a atteint sa limite de téléchargements.');
        }

        $product = $order->orderable;

        if (! $product || ! ($product instanceof Product) || ! $product->isFormation()) {
            abort(404, 'Formation introuvable.');
        }

        $product->load('modules.files');

        return view('shop.formation-view', compact('order', 'product', 'token'));
    }
}

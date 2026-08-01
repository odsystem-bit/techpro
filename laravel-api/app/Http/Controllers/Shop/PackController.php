<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Pack;
use Illuminate\Http\Request;

class PackController extends Controller
{
    public function index()
    {
        $packs = Pack::with('category', 'products')
            ->where('is_active', true)
            ->latest()
            ->paginate(12);
        
        return view('shop.packs.index', compact('packs'));
    }

    public function show(Pack $pack)
    {
        if (! $pack->is_active) {
            abort(404);
        }

        $pack->load('category', 'products');

        return view('shop.packs.show', compact('pack'));
    }

    public function view(Request $request, string $token)
    {
        $order = Order::where('download_token', $token)->firstOrFail();

        if (! $order->canDownload()) {
            abort(403, 'Ce lien de téléchargement est expiré ou a atteint sa limite.');
        }

        $pack = $order->orderable;

        if (! $pack || ! ($pack instanceof \App\Models\Pack)) {
            abort(404, 'Pack introuvable.');
        }

        $pack->load('products', 'files');

        return view('shop.packs.view', compact('order', 'pack'));
    }
}

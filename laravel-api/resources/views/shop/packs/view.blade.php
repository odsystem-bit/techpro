@extends('layouts.app')
@section('title', 'Mon Pack — Tech Pro Futur')

@section('content')
<div class="mx-auto max-w-5xl px-6 py-10 lg:px-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Votre pack : {{ $pack->name }}</h1>
        <p class="mt-1 text-sm text-gray-500">Commande n° {{ $order->order_number }}</p>
    </div>

    @if (!$order->canDownload())
        <div class="rounded-xl border border-red-200 bg-red-50 p-6 text-center">
            <svg class="mx-auto h-12 w-12 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <h3 class="mt-3 text-lg font-semibold text-red-900">Lien de téléchargement expiré</h3>
            <p class="mt-1 text-sm text-red-700">Ce lien a atteint sa limite de téléchargement ou a expiré.</p>
        </div>
    @else
        <div class="mb-6 rounded-lg bg-blue-50 p-4">
            <div class="flex items-start gap-3">
                <svg class="h-5 w-5 text-blue-600 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <div class="text-sm text-blue-800">
                    <p class="font-medium">Téléchargements restants : {{ $order->download_limit - $order->download_count }} / {{ $order->download_limit }}</p>
                    @if ($order->download_expires_at)
                        <p class="mt-1">Ce lien expire le : {{ $order->download_expires_at->format('d/m/Y à H:i') }}</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Fichiers du pack --}}
        @if ($pack->files->isNotEmpty())
        <div class="space-y-4 mb-8">
            <h2 class="text-lg font-semibold text-gray-900">Fichiers du pack ({{ $pack->files->count() }} fichier(s))</h2>

            @foreach ($pack->files as $file)
            <div class="flex items-center gap-4 rounded-xl border border-indigo-200 bg-indigo-50 p-5">
                <div class="flex h-16 w-16 items-center justify-center rounded-lg bg-indigo-100 text-indigo-600">
                    @if (in_array($file->file_type, ['zip', 'rar', '7z']))
                        <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                    @elseif ($file->file_type === 'pdf')
                        <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    @else
                        <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    @endif
                </div>

                <div class="flex-1">
                    <div class="font-semibold text-gray-900">{{ $file->name }}</div>
                    <div class="text-sm text-gray-500">{{ strtoupper($file->file_type) }} • {{ number_format($file->file_size / 1024 / 1024, 2) }} Mo</div>
                </div>

                <a href="{{ route('download.pack.file', ['token' => $order->download_token, 'file_id' => $file->id]) }}" 
                   class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-500">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Télécharger
                </a>
            </div>
            @endforeach
        </div>
        @endif

        {{-- Produits du pack --}}
        @if ($pack->products->isNotEmpty())
        <div class="space-y-4">
            <h2 class="text-lg font-semibold text-gray-900">Ebooks inclus ({{ $pack->products->count() }} produit(s))</h2>

            @foreach ($pack->products as $product)
            <div class="flex items-center gap-4 rounded-xl border border-gray-200 bg-white p-5">
                @if ($product->image)
                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="h-16 w-16 rounded-lg object-cover">
                @else
                    <div class="flex h-16 w-16 items-center justify-center rounded-lg bg-gray-100 text-gray-300">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                @endif

                <div class="flex-1">
                    <div class="font-semibold text-gray-900">{{ $product->name }}</div>
                    <div class="text-sm text-gray-500">{{ $product->category?->name ?? '' }}</div>
                    @if ($product->short_description)
                        <p class="mt-1 text-xs text-gray-400 line-clamp-1">{{ $product->short_description }}</p>
                    @endif
                </div>

                @if ($product->file_path)
                <a href="{{ route('download.pack.product', ['token' => $order->download_token, 'product_id' => $product->id]) }}" 
                   class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-500">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Télécharger
                </a>
                @else
                <span class="text-sm text-gray-400">Pas de fichier</span>
                @endif
            </div>
            @endforeach
        </div>
        @endif

        <div class="mt-8 rounded-lg bg-gray-50 p-6">
            <h3 class="mb-3 text-sm font-semibold text-gray-900">Informations de commande</h3>
            <div class="space-y-2 text-sm text-gray-600">
                <div class="flex justify-between">
                    <span>Numéro de commande</span>
                    <span class="font-medium">{{ $order->order_number }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Email</span>
                    <span class="font-medium">{{ $order->customer_email }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Montant payé</span>
                    <span class="font-medium">{{ number_format($order->total_amount, 0, ',', ' ') }} {{ $order->currency }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Date d'achat</span>
                    <span class="font-medium">{{ $order->created_at->format('d/m/Y à H:i') }}</span>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

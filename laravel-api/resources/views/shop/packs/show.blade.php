@extends('layouts.app')
@section('title', $pack->name . ' — Tech Pro Futur')

@section('content')
<div class="mx-auto max-w-5xl px-6 py-10 lg:px-8">
    <a href="{{ route('packs.index') }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-indigo-600 mb-6">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Retour aux packs
    </a>

    <div class="grid gap-8 lg:grid-cols-[1fr_350px]">
        <div>
            @if ($pack->image)
                <img src="{{ asset('storage/' . $pack->image) }}" alt="{{ $pack->name }}" class="w-full rounded-xl object-cover">
            @else
                <div class="flex h-64 w-full items-center justify-center rounded-xl bg-gray-100 text-gray-300">
                    <svg class="h-16 w-16" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </div>
            @endif

            <div class="mt-6">
                <div class="mb-2">
                    @if ($pack->category)
                        <span class="inline-block rounded-full bg-indigo-100 px-2.5 py-1 text-xs font-medium text-indigo-700">{{ $pack->category->name }}</span>
                    @endif
                    @if ($pack->is_featured)
                        <span class="ml-2 inline-block rounded-full bg-amber-100 px-2.5 py-1 text-xs font-medium text-amber-700">★ Vedette</span>
                    @endif
                </div>

                <h1 class="text-2xl font-bold text-gray-900">{{ $pack->name }}</h1>
                <p class="mt-2 text-gray-600">{{ $pack->short_description }}</p>
            </div>

            @if ($pack->description)
                <div class="mt-6">
                    <h2 class="mb-3 text-lg font-semibold text-gray-900">Description</h2>
                    <div class="prose prose-sm text-gray-600">{{ $pack->description }}</div>
                </div>
            @endif

            <div class="mt-6">
                <h2 class="mb-3 text-lg font-semibold text-gray-900">Contenu du pack ({{ $pack->products->count() }} produit(s))</h2>
                <div class="space-y-3">
                    @foreach ($pack->products as $product)
                    <div class="flex items-center gap-4 rounded-lg border border-gray-200 bg-white p-4">
                        @if ($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="h-12 w-12 rounded-lg object-cover">
                        @else
                            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-gray-100 text-gray-300">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </div>
                        @endif
                        <div class="flex-1">
                            <div class="text-sm font-medium text-gray-900">{{ $product->name }}</div>
                            <div class="text-xs text-gray-500">{{ $product->category?->name ?? '' }}</div>
                        </div>
                        <div class="text-sm font-semibold text-gray-900">
                            {{ number_format($product->price, 0, ',', ' ') }} {{ $product->currency }}
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            @if ($pack->savings > 0)
            <div class="mt-6 rounded-lg bg-green-50 p-4">
                <div class="flex items-center gap-2 text-green-700">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="font-medium">Vous économisez {{ number_format($pack->savings, 0, ',', ' ') }} {{ $pack->currency }} avec ce pack</span>
                </div>
            </div>
            @endif
        </div>

        <div class="self-start rounded-xl border border-gray-200 bg-white p-6 lg:sticky lg:top-8">
            <h2 class="mb-4 text-lg font-bold text-gray-900">Prix du pack</h2>

            @if ($pack->has_discount)
                <div class="mb-4">
                    <span class="text-lg text-gray-400 line-through">{{ number_format($pack->price, 0, ',', ' ') }} {{ $pack->currency }}</span>
                    <div class="text-3xl font-bold text-green-600">{{ number_format($pack->discount_price, 0, ',', ' ') }} {{ $pack->currency }}</div>
                    <div class="text-sm font-medium text-green-600">-{{ $pack->discount_percent }}% de réduction</div>
                </div>
            @else
                <div class="mb-4 text-3xl font-bold text-gray-900">
                    {{ number_format($pack->price, 0, ',', ' ') }} {{ $pack->currency }}
                </div>
            @endif

            <form method="POST" action="{{ route('shop.cart.add.pack', $pack) }}">
                @csrf
                <button type="submit" class="w-full rounded-lg bg-indigo-600 py-3 text-sm font-semibold text-white hover:bg-indigo-500">
                    Ajouter au panier
                </button>
            </form>

            <div class="mt-4 space-y-2 text-xs text-gray-500">
                <div class="flex items-center gap-2">
                    <svg class="h-4 w-4 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>Téléchargement illimité</span>
                </div>
                <div class="flex items-center gap-2">
                    <svg class="h-4 w-4 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>Mises à jour incluses</span>
                </div>
                <div class="flex items-center gap-2">
                    <svg class="h-4 w-4 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>Accès à vie</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

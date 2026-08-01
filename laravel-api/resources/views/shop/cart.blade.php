@extends('layouts.app')
@section('title', 'Mon panier — Tech Pro Futur')

@section('content')
<div class="mx-auto max-w-5xl px-6 py-10 lg:px-8">
    <h1 class="mb-6 text-xl font-bold text-gray-900">Mon panier</h1>

    @if ($items->isEmpty())
        <div class="rounded-xl border border-gray-200 bg-white py-16 text-center">
            <p class="text-gray-400">Votre panier est vide.</p>
            <a href="{{ route('shop.index') }}" class="mt-4 inline-block rounded-lg bg-indigo-600 px-5 py-2 text-sm font-medium text-white hover:bg-indigo-500">Voir la boutique</a>
        </div>
    @else
        <div class="grid gap-8 lg:grid-cols-[1fr_300px]">

            <div class="space-y-3">
                @foreach ($items as $item)
                @php
                    $isPack = $item['type'] === 'pack';
                    $entity = $isPack ? $item['pack'] : $item['product'];
                    $currency = $entity->currency ?? 'XOF';
                @endphp
                <div class="flex gap-4 rounded-xl border {{ $isPack ? 'border-amber-200 bg-amber-50/30' : 'border-gray-200 bg-white' }} p-4">
                    @if ($entity->image)
                        <img src="{{ asset('storage/' . $entity->image) }}" alt="{{ $entity->name }}" class="h-16 w-16 shrink-0 rounded-lg object-cover">
                    @else
                        <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-lg {{ $isPack ? 'bg-amber-100 text-amber-300' : 'bg-gray-100 text-gray-300' }}">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                    @endif

                    <div class="flex flex-1 flex-col justify-between">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                @if ($isPack)
                                    <span class="mb-1 inline-block rounded bg-amber-500 px-1.5 py-0.5 text-[10px] font-bold text-white">PACK</span>
                                    <a href="{{ route('packs.show', $entity) }}" class="text-sm font-semibold text-gray-900 hover:text-amber-600">{{ $entity->name }}</a>
                                @else
                                    <a href="{{ route('shop.show', $entity) }}" class="text-sm font-semibold text-gray-900 hover:text-indigo-600">{{ $entity->name }}</a>
                                @endif
                                <p class="text-xs text-gray-400">{{ $entity->category?->name ?? '' }}</p>
                            </div>
                            <form method="POST" action="{{ route($isPack ? 'shop.cart.remove.pack' : 'shop.cart.remove', $entity) }}">
                                @csrf @method('DELETE')
                                <button class="text-gray-300 hover:text-red-500">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </form>
                        </div>
                        <div class="mt-2 flex items-center justify-between">
                            <form method="POST" action="{{ route($isPack ? 'shop.cart.update.pack' : 'shop.cart.update', $entity) }}" class="flex items-center gap-2">
                                @csrf @method('PATCH')
                                <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="1"
                                    class="w-14 rounded-md border border-gray-200 px-2 py-1 text-center text-sm outline-none focus:border-indigo-400" />
                                <button type="submit" class="rounded-md border border-gray-200 px-2.5 py-1 text-xs text-gray-500 hover:bg-gray-50">OK</button>
                            </form>
                            <p class="font-semibold {{ $isPack ? 'text-amber-600' : 'text-gray-900' }}">{{ number_format($item['total'], 0, ',', ' ') }} {{ $currency }}</p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="self-start rounded-xl border border-gray-200 bg-white p-5">
                <h2 class="mb-3 text-sm font-bold text-gray-900">Résumé</h2>
                <div class="space-y-2 text-sm">
                    @foreach ($items as $item)
                    @php
                        $entity = $item['type'] === 'pack' ? $item['pack'] : $item['product'];
                    @endphp
                    <div class="flex justify-between text-gray-600">
                        <span class="truncate max-w-[150px]">{{ $entity->name }}</span>
                        <span>{{ number_format($item['total'], 0, ',', ' ') }}</span>
                    </div>
                    @endforeach
                    <div class="border-t border-gray-100 pt-2 flex justify-between font-bold text-gray-900">
                        <span>Total</span>
                        <span>{{ number_format($total, 0, ',', ' ') }} {{ $items->first()['type'] === 'pack' ? $items->first()['pack']->currency : ($items->first()['product']->currency ?? 'XOF') }}</span>
                    </div>
                </div>

                <a href="{{ route('shop.checkout') }}"
                   class="mt-5 block w-full rounded-lg bg-indigo-600 py-2.5 text-center text-sm font-semibold text-white hover:bg-indigo-500">
                    Passer à la caisse
                </a>
                <a href="{{ route('shop.index') }}" class="mt-2 block text-center text-xs text-gray-400 hover:text-indigo-600">
                    Continuer mes achats
                </a>
            </div>
        </div>
    @endif
</div>
@endsection

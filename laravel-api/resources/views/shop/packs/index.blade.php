@extends('layouts.app')
@section('title', 'Packs — Tech Pro Futur')

@section('content')
<div class="mx-auto max-w-6xl px-6 py-10 lg:px-8">

    <div class="mb-6">
        <h1 class="text-xl font-bold text-gray-900">Packs Exclusifs</h1>
        <p class="mt-1 text-sm text-gray-500">{{ $packs->total() }} pack(s) disponible(s)</p>
    </div>

    <div class="flex flex-col gap-8 lg:flex-row">

        <aside class="w-full shrink-0 lg:w-52">
            <form method="GET" action="{{ route('shop.index') }}" class="space-y-5">
                <input type="hidden" name="type" value="pack">
                <div>
                    <label class="mb-1 block text-xs font-medium text-gray-500">Recherche</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Nom du pack..."
                        class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm outline-none focus:border-amber-400" />
                </div>
                <div>
                    <label class="mb-1.5 block text-xs font-medium text-gray-500">Type de produit</label>
                    <div class="space-y-1">
                        <a href="{{ route('shop.index') }}"
                           class="block rounded-md px-3 py-1.5 text-sm text-gray-600 hover:bg-gray-100">
                            Tous les produits
                        </a>
                        <a href="{{ route('shop.index', ['type' => 'pack']) }}"
                           class="block rounded-md px-3 py-1.5 text-sm bg-amber-500 font-medium text-white">
                            Packs
                        </a>
                    </div>
                </div>
                <div>
                    <label class="mb-1.5 block text-xs font-medium text-gray-500">Catégories</label>
                    <div class="space-y-1">
                        <a href="{{ route('shop.index', ['type' => 'pack']) }}"
                           class="block rounded-md px-3 py-1.5 text-sm {{ !request('category') ? 'bg-gray-900 font-medium text-white' : 'text-gray-600 hover:bg-gray-100' }}">
                            Toutes
                        </a>
                        @foreach ($categories as $cat)
                        <a href="{{ route('shop.index', ['type' => 'pack', 'category' => $cat->slug]) }}"
                           class="block rounded-md px-3 py-1.5 text-sm {{ request('category') === $cat->slug ? 'bg-gray-900 font-medium text-white' : 'text-gray-600 hover:bg-gray-100' }}">
                            {{ $cat->name }}
                        </a>
                        @endforeach
                    </div>
                </div>
                <button type="submit" class="w-full rounded-lg bg-amber-500 py-2 text-sm font-medium text-white hover:bg-amber-600">
                    Filtrer
                </button>
            </form>
        </aside>

        <div class="flex-1">
            @if ($packs->isEmpty())
                <div class="rounded-xl border border-gray-200 bg-white py-16 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    <p class="mt-4 text-gray-400">Aucun pack disponible pour le moment.</p>
                    <a href="{{ route('shop.index') }}" class="mt-4 inline-block rounded-lg bg-indigo-600 px-5 py-2 text-sm font-medium text-white hover:bg-indigo-500">Voir la boutique</a>
                </div>
            @else
                <div class="grid gap-4 grid-cols-2 sm:grid-cols-3 xl:grid-cols-4">
                    @foreach ($packs as $pack)
                    <div class="group flex flex-col overflow-hidden rounded-xl border-2 border-amber-200 bg-gradient-to-b from-amber-50 to-white transition-all duration-300 hover:shadow-xl hover:-translate-y-1 hover:border-amber-400">
                        <a href="{{ route('packs.show', $pack) }}" class="block relative">
                            @if ($pack->image)
                                <div class="aspect-[3/4] overflow-hidden">
                                    <img src="{{ asset('storage/' . $pack->image) }}" alt="{{ $pack->name }}" class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105">
                                </div>
                            @else
                                <div class="flex aspect-[3/4] items-center justify-center bg-amber-100 text-amber-400">
                                    <svg class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                </div>
                            @endif
                            <div class="absolute top-2 left-2">
                                <span class="rounded-full bg-amber-500 px-2 py-1 text-[10px] font-bold text-white shadow">PACK</span>
                            </div>
                            @if ($pack->has_discount)
                            <div class="absolute top-2 right-2">
                                <span class="rounded-full bg-green-500 px-2 py-1 text-[10px] font-bold text-white shadow">-{{ $pack->discount_percent }}%</span>
                            </div>
                            @endif
                        </a>
                        <div class="flex flex-1 flex-col p-3">
                            <div class="flex items-center gap-1 mb-1">
                                <span class="text-[10px] text-amber-600 font-medium">{{ $pack->products->count() }} ebook(s)</span>
                            </div>
                            <a href="{{ route('packs.show', $pack) }}" class="text-sm font-semibold text-gray-900 line-clamp-2 group-hover:text-amber-600">{{ $pack->name }}</a>
                            <div class="mt-auto pt-2">
                                @if ($pack->has_discount)
                                    <div class="flex items-center gap-1.5 mb-1">
                                        <span class="text-xs text-gray-400 line-through">{{ number_format($pack->price, 0, ',', ' ') }}</span>
                                    </div>
                                    <span class="font-bold text-amber-600">{{ number_format($pack->discount_price, 0, ',', ' ') }} {{ $pack->currency }}</span>
                                @else
                                    <span class="font-bold text-amber-600">{{ number_format($pack->price, 0, ',', ' ') }} {{ $pack->currency }}</span>
                                @endif
                            </div>
                            <form method="POST" action="{{ route('shop.cart.add.pack', $pack) }}" class="mt-2">
                                @csrf
                                <button class="w-full rounded-lg bg-amber-500 py-2 text-xs font-medium text-white hover:bg-amber-600 transition">Ajouter au panier</button>
                            </form>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="mt-8">{{ $packs->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection

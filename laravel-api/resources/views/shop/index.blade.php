@extends('layouts.app')
@section('title', 'Boutique — Tech Pro Futur')

@section('content')
<div class="mx-auto max-w-6xl px-6 py-10 lg:px-8">

    <div class="mb-6">
        <h1 class="text-xl font-bold text-gray-900">Boutique</h1>
        <p class="mt-1 text-sm text-gray-500">{{ $products->total() }} produit(s) disponible(s)</p>
    </div>

    <div class="flex flex-col gap-8 lg:flex-row">

        <aside class="w-full shrink-0 lg:w-52">
            <div x-data="{ open: false }" class="space-y-5">
                <button @click="open = !open" class="lg:hidden flex w-full items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-3 text-sm font-medium text-gray-700">
                    <span>Filtrer les produits</span>
                    <svg class="h-4 w-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>

                <div x-show="open || window.innerWidth >= 1024" x-transition class="hidden lg:block">
                    <form method="GET" action="{{ route('shop.index') }}" class="space-y-5">
                        <div>
                            <label class="mb-1 block text-xs font-medium text-gray-500">Recherche</label>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Nom du produit..."
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm outline-none focus:border-indigo-400" />
                        </div>
                        <div>
                            <label class="mb-1.5 block text-xs font-medium text-gray-500">Type de produit</label>
                            <div class="space-y-1">
                                <a href="{{ route('shop.index', request()->except('type')) }}"
                                   class="block rounded-md px-3 py-1.5 text-sm {{ !request('type') ? 'bg-indigo-600 font-medium text-white' : 'text-gray-600 hover:bg-gray-100' }}">
                                    Tous
                                </a>
                                <a href="{{ route('shop.index', array_merge(request()->except('type'), ['type' => 'ebook'])) }}"
                                   class="block rounded-md px-3 py-1.5 text-sm {{ request('type') === 'ebook' ? 'bg-indigo-600 font-medium text-white' : 'text-gray-600 hover:bg-gray-100' }}">
                                    Ebooks
                                </a>
                                <a href="{{ route('shop.index', array_merge(request()->except('type'), ['type' => 'formation'])) }}"
                                   class="block rounded-md px-3 py-1.5 text-sm {{ request('type') === 'formation' ? 'bg-indigo-600 font-medium text-white' : 'text-gray-600 hover:bg-gray-100' }}">
                                    Formations
                                </a>
                                <a href="{{ route('shop.index', array_merge(request()->except('type'), ['type' => 'template'])) }}"
                                   class="block rounded-md px-3 py-1.5 text-sm {{ request('type') === 'template' ? 'bg-indigo-600 font-medium text-white' : 'text-gray-600 hover:bg-gray-100' }}">
                                    Templates
                                </a>
                                <a href="{{ route('shop.index', array_merge(request()->except('type'), ['type' => 'pack'])) }}"
                                   class="block rounded-md px-3 py-1.5 text-sm {{ request('type') === 'pack' ? 'bg-amber-500 font-medium text-white' : 'text-amber-600 hover:bg-amber-50 font-medium' }}">
                                    Packs
                                </a>
                            </div>
                        </div>
                        <div>
                            <label class="mb-1.5 block text-xs font-medium text-gray-500">Catégories</label>
                            <div class="space-y-1">
                                <a href="{{ route('shop.index', request()->except('category')) }}"
                                   class="block rounded-md px-3 py-1.5 text-sm {{ !request('category') ? 'bg-gray-900 font-medium text-white' : 'text-gray-600 hover:bg-gray-100' }}">
                                    Toutes
                                </a>
                                @foreach ($categories as $cat)
                                <a href="{{ route('shop.index', array_merge(request()->except('category'), ['category' => $cat->slug])) }}"
                                   class="block rounded-md px-3 py-1.5 text-sm {{ request('category') === $cat->slug ? 'bg-gray-900 font-medium text-white' : 'text-gray-600 hover:bg-gray-100' }}">
                                    {{ $cat->name }}
                                </a>
                                @endforeach
                            </div>
                        </div>
                        <button type="submit" class="w-full rounded-lg bg-gray-900 py-2 text-sm font-medium text-white hover:bg-gray-700">
                            Filtrer
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <div class="flex-1">
            @if ($products->isEmpty())
                <div class="rounded-xl border border-gray-200 bg-white py-16 text-center">
                    <p class="text-gray-400">Aucun produit trouvé.</p>
                    <a href="{{ route('shop.index') }}" class="mt-3 inline-block text-sm text-indigo-600 hover:underline">Voir tous les produits</a>
                </div>
            @else
                <div class="grid gap-4 grid-cols-2 sm:grid-cols-3 xl:grid-cols-4">
                    @foreach ($products as $product)
                    <div class="group flex flex-col overflow-hidden rounded-xl border border-gray-200 bg-white transition-all duration-300 hover:shadow-xl hover:-translate-y-1">
                        <a href="{{ route('shop.show', $product) }}" class="block">
                            @if ($product->image)
                                <div class="aspect-[3/4] overflow-hidden bg-gray-100">
                                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" loading="lazy" class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105">
                                </div>
                            @else
                                <div class="flex aspect-[3/4] items-center justify-center bg-gray-100 text-gray-300">
                                    <svg class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                </div>
                            @endif
                        </a>
                        <div class="flex flex-1 flex-col p-3">
                            <div class="flex items-center gap-1 mb-1.5">
                                @if ($product->product_type === 'formation')
                                    <span class="rounded bg-indigo-100 px-1.5 py-0.5 text-[10px] font-medium text-indigo-700">Formation</span>
                                @elseif ($product->product_type === 'template')
                                    <span class="rounded bg-purple-100 px-1.5 py-0.5 text-[10px] font-medium text-purple-700">Template</span>
                                @else
                                    <span class="rounded bg-gray-100 px-1.5 py-0.5 text-[10px] text-gray-500">Ebook</span>
                                @endif
                            </div>
                            <a href="{{ route('shop.show', $product) }}" class="text-sm font-semibold text-gray-900 line-clamp-2 group-hover:text-indigo-600">{{ $product->name }}</a>
                            <div class="mt-auto pt-2">
                                @if ($product->has_discount)
                                    <div class="flex items-center gap-1.5 mb-1">
                                        <span class="text-xs text-gray-400 line-through">{{ number_format($product->price, 0, ',', ' ') }}</span>
                                        <span class="rounded bg-green-50 px-1 py-0.5 text-[10px] font-semibold text-green-700">-{{ $product->discount_percent }}%</span>
                                    </div>
                                    <span class="font-bold text-gray-900">{{ number_format($product->discount_price, 0, ',', ' ') }} {{ $product->currency }}</span>
                                @else
                                    <span class="font-bold text-gray-900">{{ number_format($product->price, 0, ',', ' ') }} {{ $product->currency }}</span>
                                @endif
                            </div>
                            <form method="POST" action="{{ route('shop.cart.add', $product) }}" class="mt-2">
                                @csrf
                                <button class="w-full rounded-lg bg-indigo-600 py-2 text-xs font-medium text-white hover:bg-indigo-500 transition">Ajouter au panier</button>
                            </form>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="mt-8">{{ $products->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection

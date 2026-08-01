@extends('layouts.app')
@section('title', ($settings['site_name'] ?? 'Tech Pro Futur') . ' — ' . ($settings['site_tagline'] ?? 'Produits numériques premium'))

@section('content')

{{-- HERO --}}
@php
    $heroBg     = !empty($settings['hero_image']) ? asset('storage/' . $settings['hero_image']) : null;
    $heroTitle  = $settings['hero_title']       ?? ($settings['site_tagline'] ?? 'Boostez vos competences digitales');
    $heroDesc   = $settings['hero_description'] ?? ($settings['site_description'] ?? 'Ebooks, templates et formations — livres instantanement apres paiement.');
    $heroBtnLbl = $settings['hero_btn_label']   ?? 'Voir les produits';
    $heroBtnUrl = $settings['hero_btn_url']     ?? route('shop.index', ['type' => 'ebook']);
    $wa         = $settings['whatsapp_number']  ?? '';
    $heroProducts = $featured->take(8); // Plus de produits pour le carousel
@endphp

<section class="relative overflow-hidden bg-gray-900">
    @if ($heroBg)
        <img src="{{ $heroBg }}" alt="" class="absolute inset-0 h-full w-full object-cover opacity-30" />
    @else
        <div class="absolute inset-0 bg-gradient-to-br from-gray-900 via-indigo-950 to-gray-900"></div>
    @endif

    <div class="relative mx-auto grid max-w-6xl gap-10 px-6 py-16 sm:py-20 lg:grid-cols-[1fr_380px] lg:items-center lg:px-8">
        {{-- Texte gauche --}}
        <div class="animate-fade-in-left">
            <h1 class="text-3xl font-bold leading-snug text-white sm:text-4xl lg:text-5xl">
                {!! nl2br(e($heroTitle)) !!}
            </h1>
            <p class="mt-5 text-base leading-relaxed text-gray-300 sm:text-lg">{{ $heroDesc }}</p>

            <div class="mt-7 flex flex-wrap gap-3">
                <a href="{{ $heroBtnUrl }}"
                   class="rounded-lg bg-indigo-600 px-6 py-3 text-sm font-semibold text-white transition hover:bg-indigo-500">
                    {{ $heroBtnLbl }}
                </a>
                @if ($wa)
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $wa) }}" target="_blank"
                   class="rounded-lg border border-gray-600 px-6 py-3 text-sm font-semibold text-gray-200 transition hover:border-gray-400 hover:text-white">
                    Nous contacter
                </a>
                @endif
            </div>

            <div class="mt-8 flex flex-wrap gap-5 text-sm text-gray-400">
                <span class="flex items-center gap-2">
                    <svg class="h-4 w-4 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    Livraison instantanee
                </span>
                <span class="flex items-center gap-2">
                    <svg class="h-4 w-4 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    Paiement securise
                </span>
                <span class="flex items-center gap-2">
                    <svg class="h-4 w-4 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    Support WhatsApp
                </span>
            </div>
        </div>

        {{-- Slider produits a droite --}}
        @if ($heroProducts->isNotEmpty())
        <div x-data="{
                current: 0,
                total: {{ $heroProducts->count() }},
                autoplay: null,
                isPaused: false,
                init() {
                    this.startAutoplay();
                    this.$el.addEventListener('mouseenter', () => { this.isPaused = true; this.stopAutoplay(); });
                    this.$el.addEventListener('mouseleave', () => { this.isPaused = false; this.startAutoplay(); });
                },
                startAutoplay() {
                    this.autoplay = setInterval(() => { if (!this.isPaused) this.next() }, 3000);
                },
                stopAutoplay() {
                    if (this.autoplay) { clearInterval(this.autoplay); this.autoplay = null; }
                },
                next() { 
                    this.current = (this.current + 1) % this.total;
                    this.resetAutoplay();
                },
                prev() { 
                    this.current = (this.current - 1 + this.total) % this.total;
                    this.resetAutoplay();
                },
                goto(i) { 
                    this.current = i;
                    this.resetAutoplay();
                },
                resetAutoplay() {
                    this.stopAutoplay();
                    if (!this.isPaused) this.startAutoplay();
                }
             }" class="hidden lg:block animate-fade-in-up">
            <div class="relative rounded-xl border border-white/10 bg-white/5 backdrop-blur-sm overflow-hidden">

                {{-- Track qui glisse avec animation améliorée --}}
                <div class="flex transition-all duration-700 ease-out will-change-transform"
                     :style="'transform: translateX(-' + (current * 100) + '%)'">
                    @foreach ($heroProducts as $idx => $hp)
                    <a href="{{ route('shop.show', $hp) }}" class="w-full flex-shrink-0 block p-4 group">
                        @if ($hp->image)
                            <img src="{{ asset('storage/' . $hp->image) }}" alt="{{ $hp->name }}" loading="lazy"
                                 class="h-48 w-full rounded-lg object-cover transition-transform duration-500 group-hover:scale-105">
                        @else
                            <div class="flex h-48 items-center justify-center rounded-lg bg-gray-800 text-gray-500">
                                <svg class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </div>
                        @endif
                        <div class="mt-3">
                            <p class="text-xs text-indigo-300">{{ $hp->category?->name ?? 'Produit' }}</p>
                            <h3 class="mt-1 font-semibold text-white truncate">{{ $hp->name }}</h3>
                            <div class="mt-2 flex items-center justify-between">
                                @if ($hp->has_discount)
                                    <span class="font-bold text-white">{{ number_format($hp->discount_price, 0, ',', ' ') }} {{ $hp->currency }}</span>
                                    <span class="rounded bg-green-500/20 px-2 py-0.5 text-xs font-medium text-green-300">-{{ $hp->discount_percent }}%</span>
                                @else
                                    <span class="font-bold text-white">{{ number_format($hp->price, 0, ',', ' ') }} {{ $hp->currency }}</span>
                                @endif
                            </div>
                        </div>
                    </a>
                    @endforeach
                </div>

                {{-- Fleches prev/next avec animation --}}
                <button @click="prev()" class="absolute left-2 top-1/2 -translate-y-1/2 flex h-8 w-8 items-center justify-center rounded-full bg-black/40 text-white backdrop-blur hover:bg-black/60 transition-all duration-300 hover:scale-110 opacity-0 group-hover:opacity-100">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                </button>
                <button @click="next()" class="absolute right-2 top-1/2 -translate-y-1/2 flex h-8 w-8 items-center justify-center rounded-full bg-black/40 text-white backdrop-blur hover:bg-black/60 transition-all duration-300 hover:scale-110 opacity-0 group-hover:opacity-100">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </button>

                {{-- Dots --}}
                <div class="absolute bottom-3 left-1/2 -translate-x-1/2 flex items-center gap-1.5">
                    <template x-for="i in total" :key="i">
                        <button @click="goto(i - 1)"
                                :class="current === i - 1 ? 'bg-indigo-400 w-5' : 'bg-white/40 w-2'"
                                class="h-2 rounded-full transition-all duration-300"></button>
                    </template>
                </div>
            </div>
        </div>
        @endif
    </div>
</section>

{{-- Barre de confiance --}}
<section class="border-b border-gray-200 bg-white py-5">
    <div class="mx-auto flex max-w-6xl flex-wrap items-center justify-center gap-x-10 gap-y-3 px-6 text-sm text-gray-600 lg:px-8">
        <span class="flex items-center gap-2">
            <svg class="h-5 w-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            Paiement 100% securise
        </span>
        <span class="flex items-center gap-2">
            <svg class="h-5 w-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            Livraison par email instantanee
        </span>
        <span class="flex items-center gap-2">
            <svg class="h-5 w-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            3 telechargements garantis
        </span>
        <span class="flex items-center gap-2">
            <svg class="h-5 w-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            Support reactif sur WhatsApp
        </span>
    </div>
</section>

{{-- CATÉGORIES --}}
@if ($categories->isNotEmpty())
<section id="categories" class="bg-white py-14">
    <div class="mx-auto max-w-6xl px-6 lg:px-8">
        <h2 class="text-xl font-bold text-gray-900">Parcourir par catégorie</h2>
        <div class="mt-6 flex flex-wrap gap-3">
            @foreach ($categories as $cat)
            <a href="{{ route('shop.index', ['category' => $cat->slug]) }}"
               class="rounded-lg border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm font-medium text-gray-700 transition-all duration-300 hover:border-indigo-300 hover:bg-indigo-50 hover:text-indigo-700 hover:-translate-y-1 hover:shadow-md">
                {{ $cat->name }}
                <span class="ml-1 text-gray-400">({{ $cat->products_count }})</span>
            </a>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- PRODUITS EN VEDETTE --}}
@if ($featured->isNotEmpty())
<section class="bg-gray-50 py-14">
    <div class="mx-auto max-w-6xl px-6 lg:px-8">
        <div class="flex items-baseline justify-between">
            <h2 class="text-xl font-bold text-gray-900">Produits populaires</h2>
            <a href="{{ route('shop.index') }}" class="text-sm font-medium text-indigo-600 hover:underline">Tout voir</a>
        </div>
        <div class="mt-8 grid gap-5 grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
            @foreach ($featured as $product)
            <a href="{{ route('shop.show', $product) }}"
               class="group flex flex-col overflow-hidden rounded-xl border border-gray-200 bg-white transition-all duration-300 hover:shadow-xl hover:-translate-y-1">
                @if ($product->image)
                    <div class="aspect-[3/4] overflow-hidden bg-gray-100">
                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" loading="lazy"
                             class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105">
                    </div>
                @else
                    <div class="flex aspect-[3/4] items-center justify-center bg-gray-100 text-gray-300">
                        <svg class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    </div>
                @endif
                <div class="flex flex-1 flex-col p-3">
                    <span class="w-fit rounded bg-gray-100 px-1.5 py-0.5 text-[10px] text-gray-500">{{ $product->category?->name ?? 'Ebook' }}</span>
                    <h3 class="mt-1.5 text-sm font-semibold text-gray-900 line-clamp-2 group-hover:text-indigo-600">{{ $product->name }}</h3>
                    <div class="mt-auto pt-2">
                        @if ($product->has_discount)
                            <div class="flex items-center gap-1.5">
                                <span class="text-xs text-gray-400 line-through">{{ number_format($product->price, 0, ',', ' ') }}</span>
                                <span class="rounded bg-green-50 px-1 py-0.5 text-[10px] font-semibold text-green-700">-{{ $product->discount_percent }}%</span>
                            </div>
                            <span class="font-bold text-gray-900">{{ number_format($product->discount_price, 0, ',', ' ') }} {{ $product->currency }}</span>
                        @else
                            <span class="font-bold text-gray-900">{{ number_format($product->price, 0, ',', ' ') }} {{ $product->currency }}</span>
                        @endif
                    </div>
                </div>
            </a>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- EBOOKS --}}
@if ($ebooks->isNotEmpty())
<section class="bg-white py-14">
    <div class="mx-auto max-w-6xl px-6 lg:px-8">
        <div class="flex items-baseline justify-between">
            <h2 class="text-xl font-bold text-gray-900">Ebooks</h2>
            <a href="{{ route('shop.index', ['type' => 'ebook']) }}" class="text-sm font-medium text-indigo-600 hover:underline">Voir tous</a>
        </div>
        <div class="mt-8 grid gap-5 grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
            @foreach ($ebooks as $product)
            <a href="{{ route('shop.show', $product) }}"
               class="group flex flex-col overflow-hidden rounded-xl border border-gray-200 bg-white transition-all duration-300 hover:shadow-xl hover:-translate-y-1">
                @if ($product->image)
                    <div class="aspect-[3/4] overflow-hidden bg-emerald-50">
                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" loading="lazy"
                             class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105">
                    </div>
                @else
                    <div class="flex aspect-[3/4] items-center justify-center bg-emerald-50 text-emerald-300">
                        <svg class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    </div>
                @endif
                <div class="flex flex-1 flex-col p-3">
                    <span class="w-fit rounded bg-emerald-100 px-1.5 py-0.5 text-[10px] font-medium text-emerald-700">Ebook</span>
                    <h3 class="mt-1.5 text-sm font-semibold text-gray-900 line-clamp-2 group-hover:text-indigo-600">{{ $product->name }}</h3>
                    <div class="mt-auto pt-2">
                        @if ($product->has_discount)
                            <div class="flex items-center gap-1.5">
                                <span class="text-xs text-gray-400 line-through">{{ number_format($product->price, 0, ',', ' ') }}</span>
                                <span class="rounded bg-green-50 px-1 py-0.5 text-[10px] font-semibold text-green-700">-{{ $product->discount_percent }}%</span>
                            </div>
                            <span class="font-bold text-gray-900">{{ number_format($product->discount_price, 0, ',', ' ') }} {{ $product->currency }}</span>
                        @else
                            <span class="font-bold text-gray-900">{{ number_format($product->price, 0, ',', ' ') }} {{ $product->currency }}</span>
                        @endif
                    </div>
                </div>
            </a>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- PACKS EXCLUSIFS - SECTION MISE EN VALEUR --}}
@if ($packs->isNotEmpty())
<section class="relative overflow-hidden bg-gradient-to-br from-amber-500 via-orange-500 to-red-500 py-16">
    <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width=\"60\" height=\"60\" viewBox=\"0 0 60 60\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cg fill=\"none\" fill-rule=\"evenodd\"%3E%3Cg fill=\"%23ffffff\" fill-opacity=\"0.1\"%3E%3Cpath d=\"M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] opacity-30"></div>
    <div class="relative mx-auto max-w-6xl px-6 lg:px-8">
        <div class="text-center mb-10">
            <span class="inline-block rounded-full bg-white/20 px-4 py-1.5 text-sm font-bold text-white mb-3">OFFRE SPÉCIALE</span>
            <h2 class="text-3xl font-extrabold text-white drop-shadow-lg">Packs Exclusifs</h2>
            <p class="mt-2 text-lg text-white/90">Économisez jusqu'à 50% avec nos packs d'ebooks</p>
        </div>
        <div class="grid gap-5 grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
            @foreach ($packs as $pack)
            <a href="{{ route('packs.show', $pack) }}"
               class="group flex flex-col overflow-hidden rounded-xl bg-white shadow-2xl transition-all duration-300 hover:-translate-y-2 hover:shadow-amber-500/30">
                <div class="relative">
                    @if ($pack->image)
                        <div class="aspect-[3/4] overflow-hidden">
                            <img src="{{ asset('storage/' . $pack->image) }}" alt="{{ $pack->name }}" loading="lazy"
                                 class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-110">
                        </div>
                    @else
                        <div class="flex aspect-[3/4] items-center justify-center bg-gradient-to-br from-amber-100 to-orange-100 text-amber-400">
                            <svg class="h-16 w-16" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        </div>
                    @endif
                    <div class="absolute top-2 left-2 right-2 flex justify-between">
                        <span class="rounded-full bg-amber-500 px-2.5 py-1 text-[10px] font-bold text-white shadow-lg">PACK</span>
                        @if ($pack->has_discount)
                        <span class="rounded-full bg-green-500 px-2.5 py-1 text-[10px] font-bold text-white shadow-lg animate-pulse">-{{ $pack->discount_percent }}%</span>
                        @endif
                    </div>
                </div>
                <div class="flex flex-1 flex-col p-3 bg-gradient-to-b from-white to-amber-50">
                    <span class="text-[10px] text-amber-600 font-semibold">{{ $pack->products->count() }} ebook(s) inclus</span>
                    <h3 class="mt-1 text-sm font-bold text-gray-900 line-clamp-2 group-hover:text-amber-600">{{ $pack->name }}</h3>
                    <div class="mt-auto pt-2">
                        @if ($pack->has_discount)
                            <span class="text-xs text-gray-400 line-through">{{ number_format($pack->price, 0, ',', ' ') }}</span>
                            <div class="font-extrabold text-lg text-amber-600">{{ number_format($pack->discount_price, 0, ',', ' ') }} <span class="text-sm">{{ $pack->currency }}</span></div>
                        @else
                            <div class="font-extrabold text-lg text-amber-600">{{ number_format($pack->price, 0, ',', ' ') }} <span class="text-sm">{{ $pack->currency }}</span></div>
                        @endif
                    </div>
                </div>
            </a>
            @endforeach
        </div>
        <div class="mt-10 text-center">
            <a href="{{ route('shop.index', ['type' => 'pack']) }}" class="inline-flex items-center gap-2 rounded-full bg-white px-8 py-3 text-sm font-bold text-amber-600 shadow-xl transition hover:bg-amber-50 hover:scale-105">
                Voir tous les packs
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </a>
        </div>
    </div>
</section>
@endif

{{-- FORMATIONS --}}
@if ($formations->isNotEmpty())
<section class="bg-white py-14">
    <div class="mx-auto max-w-6xl px-6 lg:px-8">
        <div class="flex items-baseline justify-between">
            <h2 class="text-xl font-bold text-gray-900">Formations</h2>
            <a href="{{ route('shop.index', ['type' => 'formation']) }}" class="text-sm font-medium text-indigo-600 hover:underline">Voir toutes</a>
        </div>
        <div class="mt-8 grid gap-5 grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
            @foreach ($formations as $product)
            <a href="{{ route('shop.show', $product) }}"
               class="group flex flex-col overflow-hidden rounded-xl border border-gray-200 bg-white transition-all duration-300 hover:shadow-xl hover:-translate-y-1">
                @if ($product->image)
                    <div class="aspect-[3/4] overflow-hidden bg-indigo-50">
                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" loading="lazy"
                             class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105">
                    </div>
                @else
                    <div class="flex aspect-[3/4] items-center justify-center bg-indigo-50 text-indigo-300">
                        <svg class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    </div>
                @endif
                <div class="flex flex-1 flex-col p-3">
                    <span class="w-fit rounded bg-indigo-100 px-1.5 py-0.5 text-[10px] text-indigo-700">Formation</span>
                    <h3 class="mt-1.5 text-sm font-semibold text-gray-900 line-clamp-2 group-hover:text-indigo-600">{{ $product->name }}</h3>
                    <div class="mt-auto pt-2">
                        @if ($product->has_discount)
                            <div class="flex items-center gap-1.5">
                                <span class="text-xs text-gray-400 line-through">{{ number_format($product->price, 0, ',', ' ') }}</span>
                                <span class="rounded bg-green-50 px-1 py-0.5 text-[10px] font-semibold text-green-700">-{{ $product->discount_percent }}%</span>
                            </div>
                            <span class="font-bold text-gray-900">{{ number_format($product->discount_price, 0, ',', ' ') }} {{ $product->currency }}</span>
                        @else
                            <span class="font-bold text-gray-900">{{ number_format($product->price, 0, ',', ' ') }} {{ $product->currency }}</span>
                        @endif
                    </div>
                </div>
            </a>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- TEMPLATES --}}
@if ($templates->isNotEmpty())
<section class="bg-gray-50 py-14">
    <div class="mx-auto max-w-6xl px-6 lg:px-8">
        <div class="flex items-baseline justify-between">
            <h2 class="text-xl font-bold text-gray-900">Templates</h2>
            <a href="{{ route('shop.index', ['type' => 'template']) }}" class="text-sm font-medium text-indigo-600 hover:underline">Voir tous</a>
        </div>
        <div class="mt-8 grid gap-5 grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
            @foreach ($templates as $product)
            <a href="{{ route('shop.show', $product) }}"
               class="group flex flex-col overflow-hidden rounded-xl border border-gray-200 bg-white transition-all duration-300 hover:shadow-xl hover:-translate-y-1">
                @if ($product->image)
                    <div class="aspect-[3/4] overflow-hidden bg-purple-50">
                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" loading="lazy"
                             class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105">
                    </div>
                @else
                    <div class="flex aspect-[3/4] items-center justify-center bg-purple-50 text-purple-300">
                        <svg class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/></svg>
                    </div>
                @endif
                <div class="flex flex-1 flex-col p-3">
                    <span class="w-fit rounded bg-purple-100 px-1.5 py-0.5 text-[10px] text-purple-700">Template</span>
                    <h3 class="mt-1.5 text-sm font-semibold text-gray-900 line-clamp-2 group-hover:text-indigo-600">{{ $product->name }}</h3>
                    <div class="mt-auto pt-2">
                        @if ($product->has_discount)
                            <div class="flex items-center gap-1.5">
                                <span class="text-xs text-gray-400 line-through">{{ number_format($product->price, 0, ',', ' ') }}</span>
                                <span class="rounded bg-green-50 px-1 py-0.5 text-[10px] font-semibold text-green-700">-{{ $product->discount_percent }}%</span>
                            </div>
                            <span class="font-bold text-gray-900">{{ number_format($product->discount_price, 0, ',', ' ') }} {{ $product->currency }}</span>
                        @else
                            <span class="font-bold text-gray-900">{{ number_format($product->price, 0, ',', ' ') }} {{ $product->currency }}</span>
                        @endif
                    </div>
                </div>
            </a>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ODIBOT PROMO --}}
<section class="relative overflow-hidden bg-gradient-to-br from-indigo-600 via-purple-600 to-indigo-800 py-16">
    <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width=\"60\" height=\"60\" viewBox=\"0 0 60 60\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cg fill=\"none\" fill-rule=\"evenodd\"%3E%3Cg fill=\"%23ffffff\" fill-opacity=\"0.05\"%3E%3Cpath d=\"M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] opacity-40"></div>
    <div class="relative mx-auto max-w-6xl px-6 lg:px-8">
        <div class="grid items-center gap-10 lg:grid-cols-2">
            <div>
                <span class="inline-block rounded-full bg-white/20 px-4 py-1.5 text-sm font-bold text-white mb-4">NOUVEAU — INCLUS GRATUITEMENT</span>
                <h2 class="text-3xl font-extrabold text-white drop-shadow-lg">ODIBOT, votre assistant IA</h2>
                <p class="mt-4 text-lg text-white/90 leading-relaxed">
                    Achetez un ebook et recevez <strong>ODIBOT</strong> gratuitement. Votre assistant IA personnel pour vous aider à apprendre, poser des questions et progresser dans vos projets.
                </p>
                <ul class="mt-6 space-y-2 text-white/80 text-sm">
                    <li class="flex items-center gap-2">
                        <svg class="h-4 w-4 text-green-300 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        Posez vos questions à l'IA après la lecture
                    </li>
                    <li class="flex items-center gap-2">
                        <svg class="h-4 w-4 text-green-300 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        Application Android à télécharger
                    </li>
                    <li class="flex items-center gap-2">
                        <svg class="h-4 w-4 text-green-300 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        100% gratuit avec l'achat d'un ebook
                    </li>
                </ul>
                <a href="{{ route('shop.index', ['type' => 'ebook']) }}"
                   class="mt-8 inline-flex items-center gap-2 rounded-full bg-white px-8 py-3 text-sm font-bold text-indigo-600 shadow-xl transition hover:bg-indigo-50 hover:scale-105">
                    Découvrir les ebooks
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
            </div>
            <div class="flex justify-center lg:justify-end">
                <div class="relative">
                    <div class="flex h-48 w-48 items-center justify-center rounded-3xl bg-white/10 backdrop-blur-sm border-2 border-white/20 shadow-2xl">
                        <svg class="h-24 w-24 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                    </div>
                    <div class="absolute -top-3 -right-3 rounded-full bg-green-400 px-3 py-1 text-xs font-bold text-white shadow-lg">GRATUIT</div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- COMMENT ÇA MARCHE --}}
<section class="bg-white py-14">
    <div class="mx-auto max-w-6xl px-6 lg:px-8">
        <h2 class="text-xl font-bold text-gray-900 text-center">Comment ça marche</h2>
        <div class="mt-8 grid gap-8 sm:grid-cols-3">
            <div>
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-600 text-lg font-bold text-white shadow-lg shadow-indigo-200">1</div>
                <h3 class="mt-3 font-semibold text-gray-900">Choisissez un produit</h3>
                <p class="mt-1 text-sm leading-relaxed text-gray-500">Parcourez notre catalogue d'ebooks, templates et formations. Ajoutez au panier ce qui vous intéresse.</p>
            </div>
            <div>
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-600 text-lg font-bold text-white shadow-lg shadow-indigo-200">2</div>
                <h3 class="mt-3 font-semibold text-gray-900">Payez en toute sécurité</h3>
                <p class="mt-1 text-sm leading-relaxed text-gray-500">Mobile Money, carte bancaire ou autre moyen disponible. La transaction est protégée par Moneroo.</p>
            </div>
            <div>
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-600 text-lg font-bold text-white shadow-lg shadow-indigo-200">3</div>
                <h3 class="mt-3 font-semibold text-gray-900">Recevez votre fichier</h3>
                <p class="mt-1 text-sm leading-relaxed text-gray-500">Un email avec votre lien de téléchargement arrive en quelques secondes. Vous avez 3 téléchargements, valables 7 jours.</p>
            </div>
        </div>
    </div>
</section>

{{-- TÉMOIGNAGES --}}
@if ($testimonials->isNotEmpty())
<section class="bg-gray-50 py-14">
    <div class="mx-auto max-w-6xl px-6 lg:px-8">
        <h2 class="text-xl font-bold text-gray-900">Ce que disent nos clients</h2>
        <div class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($testimonials as $t)
            <div class="flex flex-col rounded-xl border border-gray-200 bg-white p-5">
                <div class="flex gap-0.5 mb-3">
                    @for ($i = 1; $i <= 5; $i++)
                        <svg class="h-4 w-4 {{ $i <= $t->rating ? 'text-amber-400' : 'text-gray-200' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    @endfor
                </div>
                <p class="flex-1 text-sm text-gray-600">"{{ $t->content }}"</p>
                @if ($t->screenshot)
                <div class="mt-3 overflow-hidden rounded-lg border border-gray-100">
                    <img src="{{ asset('storage/' . $t->screenshot) }}" alt="Avis de {{ $t->author_name }}" loading="lazy"
                         class="w-full object-cover" />
                </div>
                @endif
                <div class="mt-3 flex items-center gap-2.5 border-t border-gray-100 pt-3">
                    @if ($t->author_avatar)
                        <img src="{{ asset('storage/' . $t->author_avatar) }}" class="h-8 w-8 rounded-full object-cover" loading="lazy" />
                    @else
                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-gray-100 text-sm font-semibold text-gray-600">
                            {{ strtoupper(substr($t->author_name, 0, 1)) }}
                        </div>
                    @endif
                    <div>
                        <p class="text-sm font-medium text-gray-900">{{ $t->author_name }}</p>
                        @if ($t->author_title)<p class="text-xs text-gray-400">{{ $t->author_title }}</p>@endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- SYSTEME.IO EMBED --}}
@if (!empty($settings['systemeio_embed']))
<section class="bg-white py-14">
    <div class="mx-auto max-w-xl px-6 lg:px-8">
        <h2 class="text-xl font-bold text-gray-900 text-center">Restez informé</h2>
        <p class="mt-2 mb-6 text-center text-sm text-gray-500">Recevez nos nouveaux produits et offres directement dans votre boite mail.</p>
        {!! $settings['systemeio_embed'] !!}
    </div>
</section>
@endif

{{-- CTA --}}
<section class="bg-gray-900 py-14 text-center">
    <div class="mx-auto max-w-xl px-6">
        <h2 class="text-2xl font-bold text-white sm:text-3xl">Prêt à passer au niveau supérieur ?</h2>
        <p class="mt-3 text-gray-400">Des ressources concrètes pour développer vos compétences, à votre rythme.</p>
        <a href="{{ route('shop.index') }}"
           class="mt-6 inline-block rounded-lg bg-indigo-600 px-6 py-3 text-sm font-semibold text-white transition hover:bg-indigo-500">
            Découvrir les produits
        </a>
    </div>
</section>

@endsection

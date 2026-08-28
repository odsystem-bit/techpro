<!DOCTYPE html>
<html lang="fr" class="scroll-smooth">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'Dashboard') — Admin Tech Pro Futur</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50 text-gray-900 antialiased">

@php
    use App\Models\Order;
    use App\Models\Product;
    $pendingOrders = Order::where('payment_status', 'pending')->count();
@endphp

<div class="flex min-h-screen">

{{-- ════════════════════════════════════════
     SIDEBAR
════════════════════════════════════════ --}}
<aside class="flex w-64 shrink-0 flex-col border-r border-gray-200 bg-white" id="sidebar">

    {{-- Logo --}}
    <div class="flex h-16 items-center gap-3 border-b border-gray-100 px-5">
        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-indigo-600 text-sm font-extrabold text-white shadow-sm">TP</span>
        <div class="min-w-0">
            <p class="truncate text-sm font-bold text-gray-900">Tech Pro Futur</p>
            <p class="text-[11px] text-indigo-500 font-medium">Panneau Admin</p>
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 overflow-y-auto px-3 py-4 text-sm">

        {{-- Groupe : Vue d'ensemble --}}
        <p class="mb-2 px-2 text-[10px] font-bold uppercase tracking-widest text-gray-400">Vue d'ensemble</p>

        <a href="{{ route('admin.dashboard') }}"
           class="nav-link {{ request()->routeIs('admin.dashboard') ? 'nav-active' : '' }}">
            <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M4 5a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1V5zm10 0a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1v-4zm10 0a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z"/></svg>
            Tableau de bord
        </a>

        <a href="{{ route('admin.analytics') }}"
           class="nav-link {{ request()->routeIs('admin.analytics') ? 'nav-active' : '' }}">
            <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            Statistiques
        </a>

        {{-- Groupe : Boutique --}}
        <p class="mt-5 mb-2 px-2 text-[10px] font-bold uppercase tracking-widest text-gray-400">Boutique</p>

        <a href="{{ route('admin.products.index') }}"
           class="nav-link {{ request()->routeIs('admin.products.*') ? 'nav-active' : '' }}">
            <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            Produits
            @php $pc = Product::where('is_active', true)->count(); @endphp
            <span class="ml-auto rounded-full bg-gray-100 px-2 py-0.5 text-[11px] font-semibold text-gray-500">{{ $pc }}</span>
        </a>

        <a href="{{ route('admin.formations.index') }}"
           class="nav-link {{ request()->routeIs('admin.formations.*') ? 'nav-active' : '' }}">
            <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            Formations
            @php $fc = Product::where('product_type', 'formation')->count(); @endphp
            <span class="ml-auto rounded-full bg-gray-100 px-2 py-0.5 text-[11px] font-semibold text-gray-500">{{ $fc }}</span>
        </a>

        <a href="{{ route('admin.categories.index') }}"
           class="nav-link {{ request()->routeIs('admin.categories.*') ? 'nav-active' : '' }}">
            <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
            Catégories
        </a>

        <a href="{{ route('admin.packs.index') }}"
           class="nav-link {{ request()->routeIs('admin.packs.*') ? 'nav-active' : '' }}">
            <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
            Packs
        </a>

        <a href="{{ route('admin.orders.index') }}"
           class="nav-link {{ request()->routeIs('admin.orders.*') ? 'nav-active' : '' }}">
            <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
            Commandes
            @if ($pendingOrders > 0)
            <span class="ml-auto rounded-full bg-amber-100 px-2 py-0.5 text-[11px] font-bold text-amber-700">{{ $pendingOrders }}</span>
            @endif
        </a>

        {{-- Groupe : Contenu du site --}}
        <p class="mt-5 mb-2 px-2 text-[10px] font-bold uppercase tracking-widest text-gray-400">Contenu du site</p>

        <a href="{{ route('admin.home.edit') }}"
           class="nav-link {{ request()->routeIs('admin.home*') ? 'nav-active' : '' }}">
            <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            Page d'accueil
        </a>

        <a href="{{ route('admin.testimonials.index') }}"
           class="nav-link {{ request()->routeIs('admin.testimonials.*') ? 'nav-active' : '' }}">
            <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
            Témoignages
        </a>

        {{-- Groupe : Configuration --}}
        <p class="mt-5 mb-2 px-2 text-[10px] font-bold uppercase tracking-widest text-gray-400">Configuration</p>

        <a href="{{ route('admin.settings') }}"
           class="nav-link {{ request()->routeIs('admin.settings*') ? 'nav-active' : '' }}">
            <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            Paramètres
        </a>

    </nav>

    {{-- Profil admin --}}
    <div class="border-t border-gray-100 p-4">
        <div class="mb-3 flex items-center gap-3 rounded-xl bg-gray-50 px-3 py-2.5">
            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-indigo-100 text-xs font-bold text-indigo-700">
                {{ strtoupper(substr(session('admin_email', 'A'), 0, 1)) }}
            </span>
            <div class="min-w-0">
                <p class="truncate text-xs font-semibold text-gray-700">{{ session('admin_email', 'Administrateur') }}</p>
                <p class="text-[10px] text-gray-400">Super Admin</p>
            </div>
        </div>
        <div class="grid grid-cols-2 gap-2">
            <a href="{{ route('home') }}" target="_blank"
               class="flex items-center justify-center gap-1.5 rounded-lg border border-gray-200 px-2 py-1.5 text-xs text-gray-600 transition hover:border-indigo-300 hover:text-indigo-600">
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                Boutique
            </a>
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center gap-1.5 rounded-lg border border-gray-200 px-2 py-1.5 text-xs text-gray-600 transition hover:border-red-300 hover:bg-red-50 hover:text-red-600">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    Déconnexion
                </button>
            </form>
        </div>
    </div>
</aside>

{{-- ════════════════════════════════════════
     ZONE PRINCIPALE
════════════════════════════════════════ --}}
<div class="flex flex-1 flex-col overflow-hidden">

    {{-- Topbar --}}
    <header class="flex h-16 shrink-0 items-center justify-between border-b border-gray-200 bg-white px-6 shadow-sm">
        <div>
            <h1 class="text-base font-bold text-gray-900">@yield('title', 'Tableau de bord')</h1>
            @hasSection('breadcrumb')
            <p class="text-xs text-gray-400">@yield('breadcrumb')</p>
            @endif
        </div>
        <div class="flex items-center gap-3">
            @if ($pendingOrders > 0)
            <a href="{{ route('admin.orders.index') }}"
               class="flex items-center gap-1.5 rounded-lg bg-amber-50 border border-amber-200 px-3 py-1.5 text-xs font-semibold text-amber-700 transition hover:bg-amber-100">
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                {{ $pendingOrders }} commande(s) en attente
            </a>
            @endif
            <a href="{{ route('admin.products.create') }}"
               class="inline-flex items-center gap-1.5 rounded-lg bg-indigo-600 px-3 py-1.5 text-xs font-semibold text-white shadow transition hover:bg-indigo-700">
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Nouveau produit
            </a>
        </div>
    </header>

    {{-- Contenu --}}
    <main class="flex-1 overflow-y-auto p-6">
        @if (session('success'))
        <div class="mb-5 flex items-center gap-3 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
            <svg class="h-5 w-5 shrink-0 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('success') }}
        </div>
        @endif
        @if (session('error'))
        <div class="mb-5 flex items-center gap-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            <svg class="h-5 w-5 shrink-0 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('error') }}
        </div>
        @endif

        @yield('content')
    </main>
</div>

</div>

<style>
.nav-link  { display:flex; align-items:center; gap:.625rem; border-radius:.5rem; padding:.5rem .625rem; color:#4b5563; font-weight:500; transition:all .15s; }
.nav-link:hover { background:#eef2ff; color:#4338ca; }
.nav-active { background:#eef2ff !important; color:#4338ca !important; font-weight:600; }
.nav-icon  { width:1rem; height:1rem; shrink:0; }
</style>

</body>
</html>

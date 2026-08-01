@extends('admin.layouts.app')
@section('title', 'Tableau de bord')

@section('content')
<div class="space-y-6">

    {{-- Stats --}}
    <div class="grid grid-cols-2 gap-4 lg:grid-cols-3 xl:grid-cols-6">
        @php
            $cards = [
                ['label' => 'Produits actifs',  'value' => $stats['active_products'],  'color' => 'indigo'],
                ['label' => 'Total produits',   'value' => $stats['total_products'],   'color' => 'slate'],
                ['label' => 'Commandes payées', 'value' => $stats['paid_orders'],      'color' => 'green'],
                ['label' => 'Total commandes',  'value' => $stats['total_orders'],     'color' => 'slate'],
                ['label' => 'Revenus (XOF)',    'value' => number_format($stats['total_revenue'], 0, ',', ' '), 'color' => 'amber'],
                ['label' => 'Catégories',       'value' => $stats['categories'],       'color' => 'slate'],
            ];
        @endphp
        @foreach ($cards as $card)
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-medium uppercase tracking-wide text-gray-500">{{ $card['label'] }}</p>
            <p class="mt-2 text-2xl font-bold text-gray-900">{{ $card['value'] }}</p>
        </div>
        @endforeach
    </div>

    <div class="grid gap-6 lg:grid-cols-[1.5fr_1fr]">

        {{-- Dernières commandes --}}
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4">
                <h2 class="text-sm font-semibold text-gray-800">Dernières commandes</h2>
                <a href="{{ route('admin.orders.index') }}" class="text-xs text-indigo-600 hover:underline">Voir tout</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-xs font-medium uppercase text-gray-500">
                        <tr>
                            <th class="px-6 py-3 text-left">N°</th>
                            <th class="px-6 py-3 text-left">Client</th>
                            <th class="px-6 py-3 text-left">Produit</th>
                            <th class="px-6 py-3 text-right">Montant</th>
                            <th class="px-6 py-3 text-center">Statut</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($recentOrders as $order)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-3 font-mono text-xs text-gray-500">{{ $order->order_number }}</td>
                            <td class="px-6 py-3 text-gray-700">{{ $order->customer_email }}</td>
                            <td class="px-6 py-3 text-gray-700">{{ $order->product?->name ?? '—' }}</td>
                            <td class="px-6 py-3 text-right font-medium text-gray-900">{{ number_format($order->total_amount, 0, ',', ' ') }} {{ $order->currency }}</td>
                            <td class="px-6 py-3 text-center">
                                @if ($order->payment_status === 'paid')
                                    <span class="rounded-full bg-green-100 px-2.5 py-1 text-xs font-medium text-green-700">Payé</span>
                                @else
                                    <span class="rounded-full bg-yellow-100 px-2.5 py-1 text-xs font-medium text-yellow-700">{{ ucfirst($order->payment_status) }}</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="px-6 py-8 text-center text-gray-400">Aucune commande pour le moment.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Top produits --}}
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-100 px-6 py-4">
                <h2 class="text-sm font-semibold text-gray-800">Top produits vendus</h2>
            </div>
            <ul class="divide-y divide-gray-100">
                @forelse ($topProducts as $product)
                <li class="flex items-center justify-between px-6 py-4">
                    <div class="min-w-0">
                        <p class="truncate text-sm font-medium text-gray-800">{{ $product->name }}</p>
                        <p class="text-xs text-gray-400">{{ $product->category?->name ?? '—' }}</p>
                    </div>
                    <span class="ml-4 shrink-0 rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700">
                        {{ $product->orders_count }} ventes
                    </span>
                </li>
                @empty
                <li class="px-6 py-8 text-center text-sm text-gray-400">Pas encore de ventes.</li>
                @endforelse
            </ul>
        </div>
    </div>

    {{-- Actions rapides --}}
    <div class="flex flex-wrap gap-3">
        <a href="{{ route('admin.products.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow transition hover:bg-indigo-700">
            + Nouveau produit
        </a>
        <a href="{{ route('admin.categories.create') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50">
            + Nouvelle catégorie
        </a>
    </div>
</div>
@endsection

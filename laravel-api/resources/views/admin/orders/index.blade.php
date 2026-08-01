@extends('admin.layouts.app')
@section('title', 'Commandes')

@section('content')
<div class="space-y-4">
    <div class="flex items-center justify-between">
        <p class="text-sm text-gray-500">{{ $orders->total() }} commande(s) — Revenus : <strong>{{ number_format($totalRevenue, 0, ',', ' ') }} XOF</strong></p>
    </div>

    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-xs font-medium uppercase text-gray-500">
                <tr>
                    <th class="px-6 py-3 text-left">N° Commande</th>
                    <th class="px-6 py-3 text-left">Client</th>
                    <th class="px-6 py-3 text-left">Produit</th>
                    <th class="px-6 py-3 text-center">Qté</th>
                    <th class="px-6 py-3 text-right">Montant</th>
                    <th class="px-6 py-3 text-center">Statut</th>
                    <th class="px-6 py-3 text-center">Téléchargé</th>
                    <th class="px-6 py-3 text-center">Date</th>
                    <th class="px-6 py-3 text-center">Détail</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($orders as $order)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-3 font-mono text-xs text-gray-500">{{ $order->order_number }}</td>
                    <td class="px-6 py-3">
                        <p class="font-medium text-gray-800">{{ $order->customer_name ?? '—' }}</p>
                        <p class="text-xs text-gray-400">{{ $order->customer_email }}</p>
                    </td>
                    <td class="px-6 py-3 text-gray-700">{{ $order->product?->name ?? '—' }}</td>
                    <td class="px-6 py-3 text-center text-gray-600">{{ $order->quantity }}</td>
                    <td class="px-6 py-3 text-right font-semibold text-gray-900">
                        {{ number_format($order->total_amount, 0, ',', ' ') }} {{ $order->currency }}
                    </td>
                    <td class="px-6 py-3 text-center">
                        @if ($order->payment_status === 'paid')
                            <span class="rounded-full bg-green-100 px-2.5 py-1 text-xs font-medium text-green-700">Payé</span>
                        @elseif ($order->payment_status === 'pending')
                            <span class="rounded-full bg-yellow-100 px-2.5 py-1 text-xs font-medium text-yellow-700">En attente</span>
                        @else
                            <span class="rounded-full bg-red-100 px-2.5 py-1 text-xs font-medium text-red-700">{{ ucfirst($order->payment_status) }}</span>
                        @endif
                    </td>
                    <td class="px-6 py-3 text-center text-xs text-gray-500">
                        {{ $order->download_count }}/{{ $order->download_limit }}
                    </td>
                    <td class="px-6 py-3 text-center text-xs text-gray-500">
                        {{ $order->created_at->format('d/m/Y') }}
                    </td>
                    <td class="px-6 py-3 text-center">
                        <a href="{{ route('admin.orders.show', $order) }}" class="rounded-lg border border-gray-200 px-3 py-1.5 text-xs font-medium text-gray-600 transition hover:bg-gray-50">Voir</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="9" class="px-6 py-12 text-center text-gray-400">Aucune commande pour le moment.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div>{{ $orders->links() }}</div>
</div>
@endsection

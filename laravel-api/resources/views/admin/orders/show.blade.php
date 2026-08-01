@extends('admin.layouts.app')
@section('title', 'Commande ' . $order->order_number)

@section('content')
<div class="mx-auto max-w-2xl space-y-6">
    <a href="{{ route('admin.orders.index') }}" class="text-sm text-indigo-600 hover:underline">← Retour aux commandes</a>

    <div class="rounded-xl border border-gray-200 bg-white p-8 shadow-sm space-y-6">
        <div class="flex items-start justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-900">{{ $order->order_number }}</h2>
                <p class="text-sm text-gray-400">{{ $order->created_at->format('d/m/Y à H:i') }}</p>
            </div>
            @if ($order->payment_status === 'paid')
                <span class="rounded-full bg-green-100 px-3 py-1.5 text-sm font-semibold text-green-700">Payé</span>
            @else
                <span class="rounded-full bg-yellow-100 px-3 py-1.5 text-sm font-semibold text-yellow-700">{{ ucfirst($order->payment_status) }}</span>
            @endif
        </div>

        <div class="grid gap-4 sm:grid-cols-2 text-sm">
            <div class="rounded-lg bg-gray-50 p-4">
                <p class="text-xs font-medium uppercase tracking-wide text-gray-500 mb-2">Client</p>
                <p class="font-medium text-gray-800">{{ $order->customer_name ?? '—' }}</p>
                <p class="text-gray-600">{{ $order->customer_email }}</p>
            </div>
            <div class="rounded-lg bg-gray-50 p-4">
                <p class="text-xs font-medium uppercase tracking-wide text-gray-500 mb-2">Produit</p>
                <p class="font-medium text-gray-800">{{ $order->product?->name ?? '—' }}</p>
                <p class="text-gray-600">Qté : {{ $order->quantity }}</p>
            </div>
            <div class="rounded-lg bg-gray-50 p-4">
                <p class="text-xs font-medium uppercase tracking-wide text-gray-500 mb-2">Paiement</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($order->total_amount, 0, ',', ' ') }} {{ $order->currency }}</p>
                <p class="text-xs text-gray-400 mt-1">via {{ $order->payment_gateway }}</p>
                @if ($order->moneroo_transaction_id)
                    <p class="text-xs font-mono text-gray-400 mt-1">TX : {{ $order->moneroo_transaction_id }}</p>
                @endif
            </div>
            <div class="rounded-lg bg-gray-50 p-4">
                <p class="text-xs font-medium uppercase tracking-wide text-gray-500 mb-2">Téléchargement</p>
                <p class="text-gray-700">{{ $order->download_count }}/{{ $order->download_limit }} téléchargements</p>
                @if ($order->download_expires_at)
                    <p class="text-xs text-gray-400 mt-1">Expire le {{ $order->download_expires_at->format('d/m/Y') }}</p>
                @endif
                @if ($order->download_token)
                    <a href="{{ route('download', $order->download_token) }}" target="_blank"
                        class="mt-2 inline-block text-xs text-indigo-600 hover:underline">
                        Lien de téléchargement →
                    </a>
                @endif
            </div>
        </div>

        @if ($order->metadata)
        <div>
            <p class="text-xs font-medium uppercase tracking-wide text-gray-500 mb-2">Métadonnées</p>
            <pre class="overflow-auto rounded-lg bg-gray-50 p-4 text-xs text-gray-600">{{ json_encode($order->metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
        </div>
        @endif
    </div>
</div>
@endsection

@extends('layouts.app')
@section('title', 'Paiement — Tech Pro Futur')

@section('content')
<div class="mx-auto max-w-5xl px-6 py-10 lg:px-8">
    <h1 class="mb-6 text-xl font-bold text-gray-900">Finaliser ma commande</h1>

    @if (session('error'))
        <div class="mb-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ session('error') }}</div>
    @endif
    @if (session('info'))
        <div class="mb-5 rounded-lg border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-700">{{ session('info') }}</div>
    @endif
    @if ($errors->any())
        <div class="mb-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            <ul class="list-inside list-disc space-y-1">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <div class="grid gap-8 lg:grid-cols-[1fr_320px]">

        <form method="POST" action="{{ route('shop.checkout.process') }}" class="space-y-5">
            @csrf

            <div class="rounded-xl border border-gray-200 bg-white p-5">
                <h2 class="mb-4 text-sm font-bold text-gray-900">Vos coordonnées</h2>
                <div class="space-y-3">
                    <div>
                        <label class="mb-1 block text-sm text-gray-600">Nom complet</label>
                        <input type="text" name="customer_name" value="{{ old('customer_name') }}" required
                            class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm outline-none focus:border-indigo-400"
                            placeholder="Jean Dupont" />
                    </div>
                    <div>
                        <label class="mb-1 block text-sm text-gray-600">Adresse email</label>
                        <input type="email" name="customer_email" value="{{ old('customer_email') }}" required
                            class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm outline-none focus:border-indigo-400"
                            placeholder="vous@email.com" />
                        <p class="mt-1 text-xs text-gray-400">Votre lien de téléchargement sera envoyé ici.</p>
                    </div>
                </div>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-5">
                <h2 class="mb-3 text-sm font-bold text-gray-900">Paiement</h2>
                <div class="flex items-center gap-3 rounded-lg border border-gray-200 bg-gray-50 p-3">
                    <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-indigo-600">
                        <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900">Moneroo</p>
                        <p class="text-xs text-gray-500">Mobile Money, carte bancaire</p>
                    </div>
                </div>
            </div>

            <button type="submit"
                class="w-full rounded-lg bg-indigo-600 py-3 text-sm font-semibold text-white transition hover:bg-indigo-500">
                Payer maintenant
            </button>

            <p class="text-center text-xs text-gray-400">
                Vous serez redirigé vers Moneroo pour finaliser le paiement en toute sécurité.
            </p>
        </form>

        <div class="self-start rounded-xl border border-gray-200 bg-white p-5">
            <h2 class="mb-3 text-sm font-bold text-gray-900">Récapitulatif</h2>
            <div class="divide-y divide-gray-100">
                @foreach ($items as $item)
                @php
                    $entity = $item['type'] === 'pack' ? $item['pack'] : $item['product'];
                    $currency = $entity->currency ?? 'XOF';
                @endphp
                <div class="flex items-start gap-3 py-2.5">
                    <div class="flex-1 min-w-0">
                        @if ($item['type'] === 'pack')
                            <span class="inline-block rounded bg-amber-500 px-1 py-0.5 text-[9px] font-bold text-white mr-1">PACK</span>
                        @endif
                        <p class="truncate text-sm text-gray-800">{{ $entity->name }}</p>
                        <p class="text-xs text-gray-400">x{{ $item['quantity'] }}</p>
                    </div>
                    <p class="shrink-0 text-sm font-medium text-gray-900">{{ number_format($item['total'], 0, ',', ' ') }}</p>
                </div>
                @endforeach
            </div>
            <div class="mt-3 flex justify-between border-t border-gray-100 pt-3 font-bold text-gray-900">
                <span>Total</span>
                @php
                    $firstItem = $items->first();
                    $firstCurrency = $firstItem['type'] === 'pack' ? $firstItem['pack']->currency : ($firstItem['product']->currency ?? 'XOF');
                @endphp
                <span>{{ number_format($total, 0, ',', ' ') }} {{ $firstCurrency }}</span>
            </div>

            <div class="mt-4 space-y-1.5 text-xs text-gray-400">
                <p class="flex items-center gap-1.5">
                    <svg class="h-3 w-3 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    Livraison instantanée par email
                </p>
                <p class="flex items-center gap-1.5">
                    <svg class="h-3 w-3 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    3 téléchargements, valables 7 jours
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

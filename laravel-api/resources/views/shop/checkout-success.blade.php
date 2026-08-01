@extends('layouts.app')
@section('title', 'Paiement confirmé — Tech Pro Futur')

@section('content')
<div class="mx-auto max-w-lg px-6 py-16 text-center">

    <div class="inline-flex h-14 w-14 items-center justify-center rounded-full bg-green-100 text-green-600">
        <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
        </svg>
    </div>

    <h1 class="mt-5 text-2xl font-bold text-gray-900">Paiement confirmé !</h1>
    <p class="mt-3 text-gray-500">
        Merci pour votre achat. Vos produits sont prêts à être téléchargés.
    </p>

    {{-- Téléchargements directs --}}
    @isset($orders)
        @if ($orders->isNotEmpty())
            <div class="mt-6 rounded-xl border-2 border-indigo-100 bg-indigo-50 p-6 text-left animate-pop-in">
                <div class="flex items-center gap-2 mb-4">
                    <svg class="h-5 w-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    <h3 class="text-sm font-bold text-indigo-900 uppercase tracking-wide">Vos fichiers sont prêts</h3>
                </div>
                <div class="space-y-3">
                    @foreach ($orders as $order)
                        @php
                            $orderable = $order->orderable;
                            $orderName = $orderable ? $orderable->name : 'Produit';
                            $isPackOrder = $order->orderable_type === 'App\\Models\\Pack';
                            $isFormationOrder = $orderable instanceof \App\Models\Product && $orderable->isFormation();
                            $hasModules = $isFormationOrder && $orderable->modules()->exists();
                        @endphp
                        <div class="flex items-center justify-between rounded-lg bg-white border border-indigo-100 p-3">
                            <div class="text-sm">
                                @if ($isPackOrder)
                                    <span class="inline-block rounded bg-amber-500 px-1 py-0.5 text-[9px] font-bold text-white mr-1">PACK</span>
                                @elseif ($isFormationOrder)
                                    <span class="inline-block rounded bg-indigo-500 px-1 py-0.5 text-[9px] font-bold text-white mr-1">FORMATION</span>
                                @endif
                                <p class="font-semibold text-gray-900">{{ $orderName }}</p>
                                <p class="text-xs text-gray-500">Commande #{{ $order->order_number }}</p>
                            </div>
                            <a href="{{ $order->download_url }}"
                               class="download-btn inline-flex items-center gap-1.5 rounded-lg bg-indigo-600 px-3.5 py-2 text-xs font-bold text-white hover:bg-indigo-700 transition shadow-sm">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                @if ($hasModules)
                                    Accéder aux modules
                                @elseif ($isPackOrder)
                                    Voir mes fichiers
                                @else
                                    Télécharger
                                @endif
                            </a>
                        </div>
                    @endforeach
                </div>
                <p class="mt-3 text-xs text-indigo-700">
                    Les liens sont valables 7 jours et permettent 3 téléchargements maximum.
                </p>

                {{-- Section ODIBOT pour les ebooks --}}
                @php $hasEbookOrder = $orders->contains(fn ($o) => $o->product && $o->product->product_type === 'ebook'); @endphp
                @if ($hasEbookOrder)
                <div class="mt-4 rounded-lg border-2 border-indigo-300 bg-gradient-to-r from-indigo-50 to-purple-50 p-4">
                    <div class="flex items-start gap-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-indigo-600 text-white">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-bold text-indigo-900">ODIBOT — Votre assistant IA gratuit</p>
                            <p class="text-xs text-indigo-700 mt-0.5">Téléchargez ODIBOT, inclus gratuitement avec votre achat d'ebook.</p>
                            @php $ebookOrder = $orders->first(fn ($o) => $o->product && $o->product->product_type === 'ebook'); @endphp
                            <a href="{{ $ebookOrder->odibot_url }}"
                               class="download-btn mt-2 inline-flex items-center gap-1.5 rounded-lg bg-purple-600 px-3.5 py-2 text-xs font-bold text-white hover:bg-purple-700 transition shadow-sm">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                Télécharger ODIBOT
                            </a>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        @endif
    @endisset

    <div class="mt-6 rounded-xl border border-gray-200 bg-white p-5 text-left space-y-2.5 text-sm text-gray-600">
        <p class="flex items-center gap-2.5">
            <svg class="h-4 w-4 shrink-0 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            Email de confirmation envoyé avec lien sécurisé
        </p>
        <p class="flex items-center gap-2.5">
            <svg class="h-4 w-4 shrink-0 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            Lien valable 7 jours, 3 téléchargements
        </p>
        <p class="flex items-center gap-2.5">
            <svg class="h-4 w-4 shrink-0 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            Contactez-nous sur WhatsApp en cas de problème
        </p>
    </div>

    <div class="mt-6 flex flex-col items-center gap-2 sm:flex-row sm:justify-center">
        <a href="{{ route('shop.index') }}" class="rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-medium text-white hover:bg-indigo-500">
            Retour à la boutique
        </a>
        @php $waSucc = preg_replace('/[^0-9]/', '', \App\Models\SiteSetting::get('whatsapp_number', '')); @endphp
        @if ($waSucc)
        <a href="https://wa.me/{{ $waSucc }}" target="_blank" class="rounded-lg border border-gray-200 px-5 py-2.5 text-sm font-medium text-gray-700 hover:border-green-400 hover:text-green-600">
            WhatsApp Support
        </a>
        @endif
    </div>
</div>

{{-- Popup modale : télécharger avant de partir --}}
<div id="leave-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 backdrop-blur-sm">
    <div class="mx-4 w-full max-w-sm rounded-2xl bg-white p-6 shadow-2xl text-center">
        <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-amber-100 text-amber-600">
            <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        </div>
        <h3 class="text-lg font-bold text-gray-900">Vous n'avez pas téléchargé !</h3>
        <p class="mt-2 text-sm text-gray-500">
            Votre fichier est prêt. Ne partez pas sans le télécharger — le lien expire dans 7 jours.
        </p>
        <div class="mt-5 flex flex-col gap-2">
            <a id="modal-download-link" href="#" class="rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-bold text-white hover:bg-indigo-700 transition">
                Télécharger maintenant
            </a>
            <button onclick="document.getElementById('leave-modal').classList.add('hidden');document.getElementById('leave-modal').classList.remove('flex');" class="rounded-lg border border-gray-200 px-4 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-50">
                Rester sur la page
            </button>
        </div>
    </div>
</div>

<script>
(function() {
    let downloaded = false;

    // Marquer comme téléchargé si un bouton est cliqué
    document.querySelectorAll('.download-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            downloaded = true;
            btn.innerHTML = '<svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> Téléchargé';
            btn.classList.remove('bg-indigo-600', 'hover:bg-indigo-700');
            btn.classList.add('bg-green-600', 'hover:bg-green-700');
        });
    });

    // Avertissement avant fermeture
    window.addEventListener('beforeunload', function(e) {
        if (!downloaded) {
            e.preventDefault();
            e.returnValue = '';
            return '';
        }
    });

    // Afficher popup si tentative de navigation interne sans télécharger
    document.querySelectorAll('a[href]').forEach(function(link) {
        if (link.closest('#leave-modal')) return;
        link.addEventListener('click', function(e) {
            if (!downloaded && !link.href.includes('download')) {
                e.preventDefault();
                const modal = document.getElementById('leave-modal');
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                // Mettre le premier lien de téléchargement dans le bouton modal
                const firstDl = document.querySelector('.download-btn');
                if (firstDl) {
                    document.getElementById('modal-download-link').href = firstDl.href;
                }
            }
        });
    });
})();
</script>

{{-- Meta Pixel Purchase Event (client-side) --}}
@isset($orders)
    @if ($orders->isNotEmpty())
        @php
            $firstOrder = $orders->first();
            $eventId = 'order_' . $firstOrder->id . '_' . $firstOrder->order_number;
        @endphp
        <script>
            (function() {
                if (typeof window.MetaPixel !== 'undefined' && !{{ $firstOrder->pixel_purchase_sent ? 'true' : 'false' }}) {
                    window.MetaPixel.trackPurchase(
                        '{{ $firstOrder->orderable_id }}',
                        {{ $firstOrder->total_amount }},
                        '{{ $firstOrder->currency }}',
                        '{{ $eventId }}'
                    );
                }
            })();
        </script>
    @endif
@endisset
@endsection

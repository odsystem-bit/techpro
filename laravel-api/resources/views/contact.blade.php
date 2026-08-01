@extends('layouts.app')

@section('title', 'Contact - Tech Pro Futur')
@section('description', 'Contactez Tech Pro Futur pour toute question, demande de support ou partenariat.')

@section('content')
<div class="mx-auto max-w-4xl px-6 py-14 lg:px-8">

    <h1 class="text-2xl font-bold text-gray-900">Nous contacter</h1>
    <p class="mt-3 text-gray-500">
        Une question sur un produit, un souci avec votre commande ou une demande de partenariat ? Nous sommes disponibles pour vous aider.
    </p>

    <div class="mt-10 grid gap-8 sm:grid-cols-2">

        <div class="space-y-6">
            <div class="rounded-xl border border-gray-200 bg-white p-5">
                <div class="flex items-center gap-3">
                    <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-green-100 text-green-600">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.031-.967-.273-.1-.472-.149-.672.15-.198.297-.767.967-.94 1.164-.173.198-.347.223-.644.075-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.52.149-.173.198-.298.298-.497.1-.198.05-.372-.025-.521-.075-.149-.672-1.612-.92-2.206-.242-.579-.487-.5-.672-.51l-.573-.01c-.198 0-.52.075-.792.372s-1.04 1.016-1.04 2.479 1.064 2.876 1.213 3.074c.149.198 2.096 3.2 5.077 4.487.71.307 1.262.49 1.693.627.712.227 1.36.195 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.413z"/></svg>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">WhatsApp</p>
                        <p class="text-sm text-gray-500">Reponse rapide, du lundi au samedi</p>
                    </div>
                </div>
                @php $waCont = preg_replace('/[^0-9]/', '', \App\Models\SiteSetting::get('whatsapp_number', '')); @endphp
                @if ($waCont)
                <a href="https://wa.me/{{ $waCont }}" target="_blank"
                   class="mt-4 inline-block rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-500">
                    Ecrire sur WhatsApp
                </a>
                @else
                <p class="mt-3 text-sm text-gray-400">Numero non configure.</p>
                @endif
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-5">
                <div class="flex items-center gap-3">
                    <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-indigo-100 text-indigo-600">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">Email</p>
                        @php $contactEmail = \App\Models\SiteSetting::get('contact_email', ''); @endphp
                        @if ($contactEmail)
                        <a href="mailto:{{ $contactEmail }}" class="text-sm text-indigo-600 hover:underline">{{ $contactEmail }}</a>
                        @else
                        <p class="text-sm text-gray-500">Non configure</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-5">
                <div class="flex items-center gap-3">
                    <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-amber-100 text-amber-600">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">Horaires</p>
                        <p class="text-sm text-gray-500">Lundi - Samedi, 8h - 18h (GMT+1)</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-6">
            <h2 class="text-sm font-bold text-gray-900">Questions frequentes</h2>
            <div class="mt-4 space-y-4">
                <div>
                    <p class="text-sm font-medium text-gray-800">Comment recevoir mon produit ?</p>
                    <p class="mt-1 text-sm text-gray-500">Apres paiement, vous recevez un email avec un lien de telechargement securise. Verifiez vos spams si besoin.</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-800">Combien de fois puis-je telecharger ?</p>
                    <p class="mt-1 text-sm text-gray-500">Chaque lien permet 3 telechargements et reste valide pendant 7 jours.</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-800">Quels moyens de paiement acceptez-vous ?</p>
                    <p class="mt-1 text-sm text-gray-500">Mobile Money (MTN, Moov, Wave) et Cartes Bancaires via Moneroo.</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-800">Je n'ai pas recu mon email, que faire ?</p>
                    <p class="mt-1 text-sm text-gray-500">Contactez-nous sur WhatsApp avec votre email de commande. Nous renvoyons le lien sous 24h.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

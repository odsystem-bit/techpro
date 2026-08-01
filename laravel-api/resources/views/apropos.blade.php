@extends('layouts.app')

@section('title', 'À propos de Tech Pro Futur')
@section('description', "En savoir plus sur Tech Pro Futur et notre ambition d'accompagner les professionnels du digital.")

@section('content')
<div class="mx-auto max-w-4xl px-4 py-16 sm:px-6 lg:px-8">

    {{-- En-tête --}}
    <div class="mb-12 text-center">
        <p class="text-sm font-semibold uppercase tracking-[0.3em] text-indigo-600">À propos</p>
        <h1 class="mt-4 text-4xl font-bold tracking-tight text-gray-900 sm:text-5xl">Tech Pro Futur</h1>
    </div>

    {{-- Introduction --}}
    <div class="rounded-2xl border border-gray-200 bg-white p-6 sm:p-8">
        <p class="text-gray-700 leading-relaxed">
            Bienvenue sur Tech Pro Futur, une plateforme dédiée au savoir, à la transformation personnelle et au développement des compétences à travers des ebooks pratiques, modernes et accessibles.
        </p>
        <p class="mt-4 text-gray-700 leading-relaxed">
            Je suis <strong>Parfait AKOTENOU</strong>, Ingénieur en Productions Animales, Enseignant des Sciences de la Vie et de la Terre (SVT), Créateur d'entreprise et passionné par la transmission du savoir utile.
        </p>
        <p class="mt-4 text-gray-700 leading-relaxed">
            À travers Tech Pro Futur, mon objectif est simple : rendre les connaissances essentielles accessibles à tous grâce à des contenus numériques de qualité, conçus pour aider chacun à évoluer dans différents domaines de la vie.
        </p>
    </div>

    {{-- Notre Mission --}}
    <div class="mt-10 rounded-2xl border border-gray-200 bg-white p-6 sm:p-8">
        <h2 class="text-xl font-bold text-gray-900">Notre Mission</h2>
        <p class="mt-3 text-gray-700 leading-relaxed">
            Chez Tech Pro Futur, nous croyons que l'information peut transformer une vie lorsqu'elle est claire, pratique et applicable immédiatement.
        </p>
        <p class="mt-3 text-gray-700 leading-relaxed">
            Notre mission est de proposer des ebooks utiles, inspirants et orientés résultats dans plusieurs catégories :
        </p>
        <div class="mt-4 grid gap-2 sm:grid-cols-2">
            <div class="flex items-center gap-2 rounded-lg bg-gray-50 px-4 py-2.5 text-sm text-gray-700">
                <svg class="h-4 w-4 shrink-0 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                Santé & Bien-être
            </div>
            <div class="flex items-center gap-2 rounded-lg bg-gray-50 px-4 py-2.5 text-sm text-gray-700">
                <svg class="h-4 w-4 shrink-0 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                Développement personnel
            </div>
            <div class="flex items-center gap-2 rounded-lg bg-gray-50 px-4 py-2.5 text-sm text-gray-700">
                <svg class="h-4 w-4 shrink-0 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                Nutrition & alimentation saine
            </div>
            <div class="flex items-center gap-2 rounded-lg bg-gray-50 px-4 py-2.5 text-sm text-gray-700">
                <svg class="h-4 w-4 shrink-0 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                Business & entrepreneuriat
            </div>
            <div class="flex items-center gap-2 rounded-lg bg-gray-50 px-4 py-2.5 text-sm text-gray-700">
                <svg class="h-4 w-4 shrink-0 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                Motivation & mentalité
            </div>
            <div class="flex items-center gap-2 rounded-lg bg-gray-50 px-4 py-2.5 text-sm text-gray-700">
                <svg class="h-4 w-4 shrink-0 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                Éducation & apprentissage
            </div>
            <div class="flex items-center gap-2 rounded-lg bg-gray-50 px-4 py-2.5 text-sm text-gray-700">
                <svg class="h-4 w-4 shrink-0 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                Remèdes naturels & habitudes de vie
            </div>
            <div class="flex items-center gap-2 rounded-lg bg-gray-50 px-4 py-2.5 text-sm text-gray-700">
                <svg class="h-4 w-4 shrink-0 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                Guides pratiques du quotidien
            </div>
            <div class="flex items-center gap-2 rounded-lg bg-gray-50 px-4 py-2.5 text-sm text-gray-700 sm:col-span-2">
                <svg class="h-4 w-4 shrink-0 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                Opportunités digitales et revenus en ligne
            </div>
        </div>
    </div>

    {{-- Pourquoi Tech Pro Futur --}}
    <div class="mt-10 rounded-2xl border border-gray-200 bg-white p-6 sm:p-8">
        <h2 class="text-xl font-bold text-gray-900">Pourquoi Tech Pro Futur ?</h2>
        <p class="mt-3 text-gray-700 leading-relaxed">
            Nous ne proposons pas seulement des ebooks. Nous créons des contenus pensés pour apporter :
        </p>
        <ul class="mt-4 space-y-2">
            <li class="flex items-start gap-2 text-gray-700">
                <span class="mt-0.5 text-green-500">✅</span>
                Des solutions concrètes
            </li>
            <li class="flex items-start gap-2 text-gray-700">
                <span class="mt-0.5 text-green-500">✅</span>
                Des conseils simples à appliquer
            </li>
            <li class="flex items-start gap-2 text-gray-700">
                <span class="mt-0.5 text-green-500">✅</span>
                Une compréhension facile même pour les débutants
            </li>
            <li class="flex items-start gap-2 text-gray-700">
                <span class="mt-0.5 text-green-500">✅</span>
                Des stratégies modernes adaptées au monde actuel
            </li>
            <li class="flex items-start gap-2 text-gray-700">
                <span class="mt-0.5 text-green-500">✅</span>
                Une véritable valeur pratique au quotidien
            </li>
        </ul>
        <p class="mt-4 text-gray-700 leading-relaxed">
            Chaque ebook est conçu avec une approche claire, pédagogique et orientée vers l'action.
        </p>
    </div>

    {{-- Vision et Valeurs --}}
    <div class="mt-10 grid gap-6 sm:grid-cols-2">
        <div class="rounded-xl border border-gray-200 bg-white p-6">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-amber-100 text-amber-600">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
            </div>
            <h3 class="mt-4 text-lg font-bold text-gray-900">Notre Vision</h3>
            <p class="mt-2 text-sm text-gray-600 leading-relaxed">
                Construire une plateforme numérique de référence en Afrique et à l'international dans la diffusion de contenus éducatifs et pratiques, capables d'aider des milliers de personnes à améliorer leur santé, leur mentalité, leurs connaissances et leur avenir.
            </p>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-6">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-green-100 text-green-600">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            </div>
            <h3 class="mt-4 text-lg font-bold text-gray-900">Nos Valeurs</h3>
            <ul class="mt-2 space-y-1.5 text-sm text-gray-600">
                <li class="flex items-center gap-2">
                    <span class="h-1.5 w-1.5 rounded-full bg-green-500"></span>
                    Excellence
                </li>
                <li class="flex items-center gap-2">
                    <span class="h-1.5 w-1.5 rounded-full bg-green-500"></span>
                    Simplicité
                </li>
                <li class="flex items-center gap-2">
                    <span class="h-1.5 w-1.5 rounded-full bg-green-500"></span>
                    Innovation
                </li>
                <li class="flex items-center gap-2">
                    <span class="h-1.5 w-1.5 rounded-full bg-green-500"></span>
                    Transmission du savoir
                </li>
                <li class="flex items-center gap-2">
                    <span class="h-1.5 w-1.5 rounded-full bg-green-500"></span>
                    Impact positif
                </li>
                <li class="flex items-center gap-2">
                    <span class="h-1.5 w-1.5 rounded-full bg-green-500"></span>
                    Évolution continue
                </li>
            </ul>
        </div>
    </div>

    {{-- Mot du Fondateur --}}
    <div class="mt-10 rounded-xl bg-gray-900 px-6 py-8 sm:px-8 sm:py-10">
        <div class="flex items-center gap-2 text-indigo-400">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
            <span class="text-sm font-medium uppercase tracking-wide">Un Mot du Fondateur</span>
        </div>
        <blockquote class="mt-4 text-lg italic text-gray-300 leading-relaxed">
            "Le savoir est aujourd'hui l'un des outils les plus puissants pour transformer sa vie. Avec Tech Pro Futur, je souhaite créer une bibliothèque numérique moderne capable d'inspirer, d'éduquer et d'aider chacun à avancer vers une meilleure version de lui-même."
        </blockquote>
        <p class="mt-4 text-right text-white font-semibold">
            — Parfait AKOTENOU
        </p>
    </div>

    {{-- CTA --}}
    <div class="mt-10 text-center">
        <h2 class="text-xl font-bold text-gray-900">Rejoignez l'Aventure Tech Pro Futur</h2>
        <p class="mt-2 text-gray-600">Merci de faire partie de cette communauté tournée vers l'apprentissage, l'évolution et le progrès.</p>
        <p class="mt-2 text-indigo-600 font-medium">Bienvenue dans l'univers de Tech Pro Futur.</p>
        <div class="mt-6 flex flex-wrap justify-center gap-3">
            <a href="{{ route('shop.index') }}" class="rounded-lg bg-indigo-600 px-6 py-3 text-sm font-medium text-white hover:bg-indigo-500">Explorer la boutique</a>
            <a href="{{ route('home') }}#contact" class="rounded-lg border border-gray-300 px-6 py-3 text-sm font-medium text-gray-700 hover:border-gray-400 hover:text-gray-900">Nous contacter</a>
        </div>
    </div>

</div>
@endsection

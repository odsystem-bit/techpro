@extends('admin.layouts.app')
@section('title', "Page d'accueil")
@section('breadcrumb', 'Modifier le contenu de la page d\'accueil')

@section('content')
<div class="mx-auto max-w-3xl">

    {{-- Aperçu actuel --}}
    @php $heroImg = $settings['hero_image'] ?? ''; @endphp
    @if ($heroImg)
    <div class="mb-6 overflow-hidden rounded-2xl border border-gray-200 shadow-sm">
        <div class="relative">
            <img src="{{ asset('storage/' . $heroImg) }}" class="h-48 w-full object-cover" alt="Image hero actuelle" />
            <div class="absolute inset-0 bg-black/40 flex items-center justify-center">
                <span class="rounded-full bg-white/90 px-4 py-1.5 text-xs font-bold text-gray-700">Image hero actuelle</span>
            </div>
        </div>
    </div>
    @endif

    <form method="POST" action="{{ route('admin.home.update') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf

        @if ($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            <ul class="list-inside list-disc space-y-1">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
        @endif

        {{-- Section : Image de fond --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="mb-5 flex items-center gap-3">
                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-indigo-100 text-indigo-600">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <div>
                    <h2 class="font-bold text-gray-900">Image de fond (Hero)</h2>
                    <p class="text-xs text-gray-400">Recommandé : 1920 × 800 px — JPG ou PNG</p>
                </div>
            </div>

            <label class="flex cursor-pointer flex-col items-center justify-center gap-3 rounded-xl border-2 border-dashed border-gray-300 bg-gray-50 px-6 py-10 text-center transition hover:border-indigo-400 hover:bg-indigo-50" id="drop-zone">
                <svg class="h-10 w-10 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1"><path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                <div>
                    <p class="font-semibold text-gray-700">Glisser-déposer ou <span class="text-indigo-600">cliquer pour choisir</span></p>
                    <p class="mt-1 text-xs text-gray-400">JPG, PNG, WEBP — max 5 Mo</p>
                </div>
                <input type="file" name="hero_image" accept="image/*" class="hidden" id="hero-file"
                       onchange="document.getElementById('file-name').textContent = this.files[0]?.name ?? ''" />
            </label>
            <p id="file-name" class="mt-2 text-center text-xs text-indigo-600 font-medium"></p>

            @if ($heroImg)
            <label class="mt-4 flex cursor-pointer items-center gap-2 text-sm text-red-600">
                <input type="checkbox" name="hero_image_remove" value="1" class="h-4 w-4 accent-red-600" />
                Supprimer l'image actuelle (retour au fond dégradé)
            </label>
            @endif
        </div>

        {{-- Section : Textes --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="mb-5 flex items-center gap-3">
                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-purple-100 text-purple-600">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </div>
                <div>
                    <h2 class="font-bold text-gray-900">Textes du hero</h2>
                    <p class="text-xs text-gray-400">Titre principal et description affichés sur l'image</p>
                </div>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="mb-1.5 block text-sm font-semibold text-gray-700">Titre principal <span class="text-red-500">*</span></label>
                    <input type="text" name="hero_title"
                           value="{{ old('hero_title', $settings['hero_title'] ?? 'Boostez vos compétences digitales') }}"
                           required maxlength="255"
                           class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100"
                           placeholder="Ex: Accédez aux meilleurs outils digitaux" />
                    <p class="mt-1 text-xs text-gray-400">Affiché en grand au centre de l'image</p>
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-semibold text-gray-700">Description / Sous-titre</label>
                    <textarea name="hero_description" rows="3" maxlength="500"
                              class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100"
                              placeholder="Ex: Ebooks, templates, formations — livrés instantanément après paiement.">{{ old('hero_description', $settings['hero_description'] ?? '') }}</textarea>
                    <p class="mt-1 text-xs text-gray-400">Phrase courte sous le titre</p>
                </div>
            </div>
        </div>

        {{-- Section : Bouton d'appel à l'action --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="mb-5 flex items-center gap-3">
                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-green-100 text-green-600">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                </div>
                <div>
                    <h2 class="font-bold text-gray-900">Bouton d'appel à l'action (CTA)</h2>
                    <p class="text-xs text-gray-400">Le bouton blanc sous le titre</p>
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="mb-1.5 block text-sm font-semibold text-gray-700">Texte du bouton</label>
                    <input type="text" name="hero_btn_label"
                           value="{{ old('hero_btn_label', $settings['hero_btn_label'] ?? 'Explorer la boutique') }}"
                           maxlength="80"
                           class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100"
                           placeholder="Explorer la boutique" />
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-semibold text-gray-700">Lien du bouton</label>
                    <input type="text" name="hero_btn_url"
                           value="{{ old('hero_btn_url', $settings['hero_btn_url'] ?? '/shop') }}"
                           maxlength="255"
                           class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100"
                           placeholder="/shop ou https://..." />
                </div>
            </div>
        </div>

        {{-- Aperçu rapide --}}
        <div class="rounded-2xl border border-indigo-100 bg-indigo-50 p-4">
            <p class="text-xs font-semibold text-indigo-700 mb-2">💡 Comment ça fonctionne</p>
            <ul class="space-y-1 text-xs text-indigo-600">
                <li>→ Après enregistrement, l'image apparaît immédiatement sur la page d'accueil</li>
                <li>→ Si aucune image n'est choisie, un fond dégradé indigo/violet sera affiché</li>
                <li>→ Le titre et la description remplacent le texte par défaut</li>
            </ul>
        </div>

        {{-- Boutons --}}
        <div class="flex items-center justify-between rounded-2xl border border-gray-200 bg-white p-4">
            <a href="{{ route('home') }}" target="_blank"
               class="inline-flex items-center gap-2 text-sm text-indigo-600 hover:underline">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                Voir la boutique
            </a>
            <button type="submit"
                    class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-8 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-indigo-700">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                Enregistrer les modifications
            </button>
        </div>
    </form>
</div>
@endsection

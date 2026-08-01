@extends('admin.layouts.app')
@section('title', isset($slide) ? 'Modifier le slide' : 'Nouveau slide hero')

@section('content')
@php $isEdit = isset($slide); @endphp
<div class="mx-auto max-w-2xl">
    <form method="POST"
          action="{{ $isEdit ? route('admin.hero.update', $slide) : route('admin.hero.store') }}"
          enctype="multipart/form-data"
          class="space-y-6 rounded-xl border border-gray-200 bg-white p-8 shadow-sm">
        @csrf
        @if ($isEdit) @method('PUT') @endif

        @if ($errors->any())
        <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            <ul class="list-inside list-disc">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
        @endif

        <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700">Titre principal *</label>
            <input type="text" name="title" value="{{ old('title', $slide->title ?? '') }}" required
                class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200" />
        </div>

        <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700">Sous-titre</label>
            <input type="text" name="subtitle" value="{{ old('subtitle', $slide->subtitle ?? '') }}"
                class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200" />
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700">Texte du bouton</label>
                <input type="text" name="btn_label" value="{{ old('btn_label', $slide->btn_label ?? '') }}"
                    class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm outline-none focus:border-indigo-500" placeholder="Découvrir →" />
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700">URL du bouton</label>
                <input type="text" name="btn_url" value="{{ old('btn_url', $slide->btn_url ?? '') }}"
                    class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm outline-none focus:border-indigo-500" placeholder="/shop" />
            </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700">Ordre d'affichage</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', $slide->sort_order ?? 0) }}" min="0"
                    class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm outline-none focus:border-indigo-500" />
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700">Couleur d'overlay (classe Tailwind)</label>
                <select name="overlay_color" class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm outline-none focus:border-indigo-500">
                    @foreach ([
                        'from-indigo-900/70' => 'Indigo sombre',
                        'from-black/60'      => 'Noir semi-transparent',
                        'from-gray-900/70'   => 'Gris sombre',
                        'from-purple-900/70' => 'Violet sombre',
                        'from-blue-900/70'   => 'Bleu sombre',
                    ] as $val => $label)
                    <option value="{{ $val }}" {{ old('overlay_color', $slide->overlay_color ?? 'from-indigo-900/70') === $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700">Image de fond (recommandé : 1920×600px)</label>
            @if (!empty($slide->image))
                <img src="{{ asset('storage/' . $slide->image) }}" class="mb-3 h-28 w-full rounded-xl object-cover" />
            @endif
            <input type="file" name="image" accept="image/*"
                class="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm" />
        </div>

        <label class="flex cursor-pointer items-center gap-2 text-sm">
            <input type="checkbox" name="is_active" value="1" class="h-4 w-4 accent-indigo-600"
                {{ old('is_active', $slide->is_active ?? true) ? 'checked' : '' }} />
            Slide actif (visible sur le site)
        </label>

        <div class="flex gap-3 border-t border-gray-100 pt-6">
            <button type="submit" class="rounded-lg bg-indigo-600 px-6 py-2.5 text-sm font-semibold text-white shadow transition hover:bg-indigo-700">
                {{ $isEdit ? 'Enregistrer' : 'Créer le slide' }}
            </button>
            <a href="{{ route('admin.hero.index') }}" class="rounded-lg border border-gray-300 px-6 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-50">Annuler</a>
        </div>
    </form>
</div>
@endsection

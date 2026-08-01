@extends('admin.layouts.app')
@section('title', $isEdit ? 'Modifier la formation' : 'Nouvelle formation')
@section('breadcrumb', $isEdit ? 'Modifier' : 'Créer')

@php
    $p = $product ?? null;
    $modulesJson = [];
    if ($p) {
        foreach ($p->modules as $m) {
            $modulesJson[] = [
                'id' => $m->id,
                'title' => $m->title,
                'description' => $m->description ?? '',
                'external_url' => $m->external_url ?? '',
                'file_path' => $m->file_path,
                'files' => $m->files->map(fn($f) => ['id' => $f->id, 'original_name' => $f->original_name])->toArray(),
            ];
        }
    }
@endphp

@section('content')

<script>
window.__formationModules = @json($modulesJson);
let __moduleUid = 0;

function formationForm() {
    return {
        modules: (window.__formationModules || []).map(m => ({ ...m, uid: ++__moduleUid })),

        addModule() {
            this.modules.push({
                uid: ++__moduleUid,
                id: null,
                title: '',
                description: '',
                external_url: '',
                file_path: null,
                files: []
            });
        },

        removeModule(index) {
            if (confirm('Supprimer ce module ?')) {
                this.modules.splice(index, 1);
            }
        }
    };
}

// Spinner on submit - works even without Alpine
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('formationForm');
    if (!form) return;
    form.addEventListener('submit', function() {
        const btn = document.getElementById('submitBtn');
        const spinner = document.getElementById('submitSpinner');
        if (btn) { btn.disabled = true; btn.classList.add('opacity-50', 'cursor-not-allowed'); }
        if (spinner) { spinner.style.display = 'inline-block'; }
    });
});
</script>

@if ($errors->any())
    <div class="mb-4 rounded-lg border-2 border-red-300 bg-red-50 p-4">
        <p class="text-sm font-bold text-red-800">Erreurs de validation ({{ $errors->count() }}) :</p>
        <ul class="mt-2 list-disc list-inside text-sm text-red-700">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if (session('success'))
    <div class="mb-4 rounded-lg border-2 border-green-300 bg-green-50 p-4">
        <p class="text-sm font-bold text-green-800">{{ session('success') }}</p>
    </div>
@endif

@if (session('error'))
    <div class="mb-4 rounded-lg border-2 border-red-300 bg-red-50 p-4">
        <p class="text-sm font-bold text-red-800">Erreur : {{ session('error') }}</p>
    </div>
@endif

@php
    // Detect if POST was too large (post_max_size exceeded = empty $_POST)
    if (request()->isMethod('POST') && empty($_POST) && empty($_FILES)):
@endphp
    <div class="mb-4 rounded-lg border-2 border-red-300 bg-red-50 p-4">
        <p class="text-sm font-bold text-red-800">Erreur : Le formulaire est trop volumineux.</p>
        <p class="mt-1 text-sm text-red-700">La taille totale des fichiers dépasse la limite serveur (5 GB). Réduisez la taille des fichiers ou ajoutez les modules en plusieurs éditions.</p>
    </div>
@php endif; @endphp

<div class="max-w-4xl">
    <form id="formationForm" method="POST" action="{{ $isEdit ? route('admin.formations.update', $p) : route('admin.formations.store') }}" enctype="multipart/form-data" x-data="formationForm()">
        @csrf
        @if ($isEdit) @method('PUT') @endif

        {{-- Informations générales --}}
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <h2 class="mb-4 text-sm font-bold uppercase tracking-wider text-gray-500">Informations générales</h2>

            <div class="grid gap-5 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label class="mb-1 block text-sm font-medium text-gray-700">Titre de la formation *</label>
                    <input type="text" name="name" value="{{ $p?->name ?? old('name') }}" required
                           class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200" />
                </div>

                <div class="sm:col-span-2">
                    <label class="mb-1 block text-sm font-medium text-gray-700">Description courte</label>
                    <input type="text" name="short_description" value="{{ $p?->short_description ?? old('short_description') }}"
                           class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200" />
                </div>

                <div class="sm:col-span-2">
                    <label class="mb-1 block text-sm font-medium text-gray-700">Description complète</label>
                    <textarea name="description" rows="5">{{ $p?->description ?? old('description') }}</textarea>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Prix *</label>
                    <input type="number" name="price" value="{{ $p?->price ?? old('price', 0) }}" required step="0.01" min="0"
                           class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200" />
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Prix promo (optionnel)</label>
                    <input type="number" name="discount_price" value="{{ $p?->discount_price ?? old('discount_price') }}" step="0.01" min="0"
                           class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200" />
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Devise</label>
                    <select name="currency" class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm">
                        <option value="XOF" {{ ($p?->currency ?? old('currency')) === 'XOF' ? 'selected' : '' }}>FCFA (XOF)</option>
                        <option value="EUR" {{ ($p?->currency ?? old('currency')) === 'EUR' ? 'selected' : '' }}>EUR</option>
                        <option value="USD" {{ ($p?->currency ?? old('currency')) === 'USD' ? 'selected' : '' }}>USD</option>
                    </select>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Catégorie</label>
                    <select name="category_id" class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm">
                        <option value="">— Aucune —</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" {{ ($p?->category_id ?? old('category_id')) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <label class="mb-1 block text-sm font-medium text-gray-700">Image de couverture</label>
                    @if ($p?->image)
                        <div class="mb-2 flex items-center gap-3">
                            <img src="{{ asset('storage/' . $p->image) }}" class="h-16 w-16 rounded-lg object-cover" alt="">
                            <span class="text-xs text-gray-400">Image actuelle (laisser vide pour conserver)</span>
                        </div>
                    @endif
                    <input type="file" name="image" accept="image/*" class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm" />
                </div>

                <div class="sm:col-span-2">
                    <label class="mb-1 block text-sm font-medium text-gray-700">Fonctionnalités (une par ligne)</label>
                    <textarea name="features_raw" rows="4" class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm">{{ is_array($p?->features) ? implode("\n", $p->features) : ($p?->features ?? old('features_raw')) }}</textarea>
                </div>

                <div class="sm:col-span-2">
                    <label class="mb-1 block text-sm font-medium text-gray-700">URL d'aperçu (vidéo externe)</label>
                    <input type="text" name="preview_url" value="{{ $p?->preview_url ?? old('preview_url') }}" placeholder="https://..."
                           class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm" />
                </div>

                <div class="flex gap-6">
                    <label class="flex items-center gap-2 text-sm">
                        <input type="checkbox" name="is_active" value="1" {{ ($p?->is_active ?? true) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600" />
                        Actif
                    </label>
                    <label class="flex items-center gap-2 text-sm">
                        <input type="checkbox" name="is_featured" value="1" {{ ($p?->is_featured ?? false) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600" />
                        En vedette
                    </label>
                </div>
            </div>
        </div>

        {{-- Modules de formation --}}
        <div class="mt-6 rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="mb-4 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold uppercase tracking-wider text-gray-500">Modules de la formation</h2>
                    <p class="mt-1 text-xs text-gray-400">Ajoutez les modules (cours) de votre formation. Chaque module peut contenir des fichiers téléchargeables.</p>
                </div>
                <button type="button" @click="addModule()"
                        class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow transition hover:bg-indigo-700">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    Ajouter un module
                </button>
            </div>

            <template x-for="(module, index) in modules" :key="module.uid">
                <div class="mb-4 rounded-lg border-2 border-gray-200 bg-gray-50 p-4">
                    <div class="mb-3 flex items-center justify-between">
                        <span class="text-sm font-bold text-gray-700">Module <span x-text="index + 1"></span></span>
                        <button type="button" @click="removeModule(index)"
                                class="text-xs font-medium text-red-600 hover:text-red-700">
                            Supprimer
                        </button>
                    </div>

                    <input type="hidden" :name="`modules[${index}][id]`" :value="module.id" />
                    <input type="hidden" :name="`modules[${index}][existing_path]`" :value="module.file_path" />

                    <div class="grid gap-4">
                        <div>
                            <label class="mb-1 block text-xs font-medium text-gray-600">Titre du module</label>
                            <input type="text" x-model="module.title" :name="`modules[${index}][title]`"
                                   class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="Ex: Module 1 - Introduction" />
                        </div>

                        <div>
                            <label class="mb-1 block text-xs font-medium text-gray-600">Description</label>
                            <textarea x-model="module.description" :name="`modules[${index}][description]`" rows="2"
                                      class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="Description du module..."></textarea>
                        </div>

                        <div>
                            <label class="mb-1 block text-xs font-medium text-gray-600">URL externe (optionnel)</label>
                            <input type="text" x-model="module.external_url" :name="`modules[${index}][external_url]`"
                                   class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="https://..." />
                        </div>

                        <div>
                            <label class="mb-1 block text-xs font-medium text-gray-600">Fichier principal du module (optionnel)</label>
                            <input type="file" :name="`modules[${index}][file]`"
                                   class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm" />
                            <template x-if="module.file_path">
                                <p class="mt-1 text-xs text-gray-500">Fichier actuel: <span x-text="module.file_path"></span></p>
                            </template>
                        </div>

                        <div>
                            <label class="mb-1 block text-xs font-medium text-gray-600">Fichiers supplémentaires (multiples)</label>
                            <input type="file" :name="`modules[${index}][files][]`" multiple
                                   class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm" />
                            <template x-if="module.files && module.files.length > 0">
                                <div class="mt-2 space-y-1">
                                    <template x-for="file in module.files" :key="file.id">
                                        <p class="text-xs text-gray-500 flex items-center gap-1">
                                            <svg class="h-3 w-3 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                            <span x-text="file.original_name"></span>
                                        </p>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </template>

            <div x-show="modules.length === 0" class="rounded-lg border-2 border-dashed border-gray-300 p-8 text-center">
                <svg class="mx-auto h-10 w-10 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                <p class="mt-2 text-sm text-gray-400">Aucun module. Cliquez sur "Ajouter un module" pour commencer.</p>
            </div>
        </div>

        {{-- Galerie d'images --}}
        <div class="mt-6 rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <h2 class="mb-4 text-sm font-bold uppercase tracking-wider text-gray-500">Galerie d'images</h2>
            @if ($p && $p->galleryImages->isNotEmpty())
                <div class="mb-3 flex flex-wrap gap-3">
                    @foreach ($p->galleryImages as $image)
                        <div class="relative">
                            <img src="{{ asset('storage/' . $image->image_path) }}" class="h-20 w-20 rounded-lg object-cover" alt="">
                            <a href="{{ route('admin.formations.gallery.destroy', [$p, $image->id]) }}"
                               class="absolute -right-2 -top-2 flex h-6 w-6 items-center justify-center rounded-full bg-red-500 text-xs text-white shadow"
                               onclick="return confirm('Supprimer cette image ?')">×</a>
                        </div>
                    @endforeach
                </div>
            @endif
            <input type="file" name="gallery[]" multiple accept="image/*" class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm" />
        </div>

        {{-- Boutons --}}
        <div class="mt-6 flex gap-3">
            <button type="submit" id="submitBtn"
                    class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-6 py-2.5 text-sm font-semibold text-white shadow transition hover:bg-indigo-700">
                <svg id="submitSpinner" class="hidden h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24" style="display:none;">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                {{ $isEdit ? 'Enregistrer la formation' : 'Créer la formation' }}
            </button>
            <a href="{{ route('admin.formations.index') }}" class="rounded-lg border border-gray-300 px-6 py-2.5 text-sm font-medium text-gray-600 transition hover:bg-gray-50">
                Annuler
            </a>
        </div>
    </form>
</div>
@endsection

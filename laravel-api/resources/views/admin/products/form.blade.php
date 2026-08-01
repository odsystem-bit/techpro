@extends('admin.layouts.app')
@section('title', isset($product) ? 'Modifier le produit' : 'Nouveau produit')

@section('content')
@php $isEdit = isset($product); @endphp

<div class="mx-auto max-w-3xl" x-data="productForm({
    productType: '{{ old('product_type', $product->product_type ?? 'ebook') }}',
    isEdit: {{ $isEdit ? 'true' : 'false' }},
    existingModules: @json($isEdit ? $product->modules->load('files') : []),
    galleryImages: @json($isEdit ? $product->galleryImages : [])
})">
    <form method="POST"
          action="{{ $isEdit ? route('admin.products.update', $product) : route('admin.products.store') }}"
          enctype="multipart/form-data"
          class="space-y-6 rounded-xl border border-gray-200 bg-white p-8 shadow-sm">
        @csrf
        @if ($isEdit) @method('PUT') @endif

        @if ($errors->any())
        <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            <ul class="list-inside list-disc space-y-1">
                @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
        @endif

        <div class="grid gap-5 sm:grid-cols-2">
            <div class="sm:col-span-2">
                <label class="mb-1.5 block text-sm font-medium text-gray-700">Nom du produit *</label>
                <input type="text" name="name" value="{{ old('name', $product->name ?? '') }}" required
                    class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200" />
            </div>

            <div class="sm:col-span-2">
                <label class="mb-1.5 block text-sm font-medium text-gray-700">Description courte</label>
                <input type="text" name="short_description" value="{{ old('short_description', $product->short_description ?? '') }}" maxlength="500"
                    class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200" />
            </div>

            <div class="sm:col-span-2">
                <label class="mb-1.5 block text-sm font-medium text-gray-700">Description longue</label>
                <textarea name="description" rows="5"
                    class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200">{{ old('description', $product->description ?? '') }}</textarea>
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700">Prix *</label>
                <input type="number" name="price" value="{{ old('price', $product->price ?? '') }}" min="0" step="1" required
                    class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200" />
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700">Prix promo (optionnel)</label>
                <input type="number" name="discount_price" value="{{ old('discount_price', $product->discount_price ?? '') }}" min="0" step="1"
                    class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200" />
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700">Devise</label>
                <select name="currency" class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm outline-none focus:border-indigo-500">
                    @foreach (['XOF','EUR','USD'] as $cur)
                    <option value="{{ $cur }}" {{ old('currency', $product->currency ?? 'XOF') === $cur ? 'selected' : '' }}>{{ $cur }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700">Type de produit *</label>
                <select name="product_type" x-model="productType"
                    class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm outline-none focus:border-indigo-500">
                    <option value="ebook">Ebook</option>
                    <option value="formation">Formation</option>
                    <option value="template">Template</option>
                </select>
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700">Stock (-1 = illimité)</label>
                <input type="number" name="stock" value="{{ old('stock', $product->stock ?? -1) }}" min="-1"
                    class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200" />
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700">Catégorie</label>
                <select name="category_id" class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm outline-none focus:border-indigo-500">
                    <option value="">— Aucune —</option>
                    @foreach ($categories as $cat)
                    <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id ?? '') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700">URL de prévisualisation</label>
                <input type="url" name="preview_url" value="{{ old('preview_url', $product->preview_url ?? '') }}"
                    class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200" />
            </div>

            <div class="sm:col-span-2">
                <label class="mb-1.5 block text-sm font-medium text-gray-700">Caractéristiques (une par ligne)</label>
                <textarea name="features_raw" rows="4" placeholder="120 pages&#10;Accès à vie&#10;Mises à jour gratuites"
                    class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200">{{ old('features_raw', implode("\n", $product->features ?? [])) }}</textarea>
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700">Image (couverture)</label>
                @if (!empty($product->image))
                    <img src="{{ asset('storage/' . $product->image) }}" class="mb-2 h-20 w-20 rounded-lg object-cover" />
                @endif
                <input type="file" name="image" accept="image/*"
                    class="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm" />
            </div>

            <div x-show="productType !== 'formation'" x-cloak>
                <label class="mb-1.5 block text-sm font-medium text-gray-700">Fichier numérique (PDF, ZIP…)</label>
                @if (!empty($product->file_path))
                    <p class="mb-2 text-xs text-gray-500">Fichier actuel : {{ basename($product->file_path) }}</p>
                @endif
                <input type="file" name="file"
                    class="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm" />
            </div>

            <div class="flex gap-6 sm:col-span-2">
                <label class="flex cursor-pointer items-center gap-2 text-sm">
                    <input type="hidden" name="is_active" value="0" />
                    <input type="checkbox" name="is_active" value="1" class="h-4 w-4 accent-indigo-600"
                        {{ old('is_active', $product->is_active ?? true) ? 'checked' : '' }} />
                    Produit actif
                </label>
                <label class="flex cursor-pointer items-center gap-2 text-sm">
                    <input type="hidden" name="is_featured" value="0" />
                    <input type="checkbox" name="is_featured" value="1" class="h-4 w-4 accent-amber-500"
                        {{ old('is_featured', $product->is_featured ?? false) ? 'checked' : '' }} />
                    Produit vedette (★)
                </label>
            </div>
        </div>

        {{-- SECTION FORMATION: MODULES --}}
        <div x-show="productType === 'formation'" x-cloak
             class="space-y-4 rounded-xl border-2 border-indigo-200 bg-indigo-50/30 p-6">

            <div class="flex items-center justify-between">
                <h3 class="text-sm font-bold text-indigo-900 flex items-center gap-2">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    Modules de la formation
                </h3>
                <button type="button" @click="addModule()"
                    class="rounded-lg bg-indigo-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-indigo-700 transition">
                    + Ajouter un module
                </button>
            </div>

            <template x-for="(mod, index) in modules" :key="index">
                <div class="rounded-lg border border-indigo-100 bg-white p-4 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-indigo-600" x-text="'Module ' + (index + 1)"></span>
                        <button type="button" @click="removeModule(index)"
                            class="text-red-400 hover:text-red-600 text-xs">
                            Supprimer
                        </button>
                    </div>

                    <input type="hidden" :name="`modules[${index}][id]`" x-model="mod.id" />
                    <input type="hidden" :name="`modules[${index}][existing_path]`" x-model="mod.file_path" />

                    <div>
                        <label class="mb-1 block text-xs font-medium text-gray-600">Titre du module *</label>
                        <input type="text" :name="`modules[${index}][title]`" x-model="mod.title" required
                            class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm outline-none focus:border-indigo-400"
                            placeholder="Ex: Introduction au marketing digital" />
                    </div>

                    <div>
                        <label class="mb-1 block text-xs font-medium text-gray-600">Description</label>
                        <textarea :name="`modules[${index}][description]`" x-model="mod.description" rows="2"
                            class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm outline-none focus:border-indigo-400"
                            placeholder="Résumé du module..."></textarea>
                    </div>

                    <div>
                        <label class="mb-1 block text-xs font-medium text-gray-600">Lien vidéo externe (YouTube/Vimeo — optionnel)</label>
                        <input type="url" :name="`modules[${index}][external_url]`" x-model="mod.external_url"
                            class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm outline-none focus:border-indigo-400"
                            placeholder="https://youtube.com/watch?v=..." />
                    </div>

                    <div>
                        <label class="mb-1 block text-xs font-medium text-gray-600">Fichiers du module (PDF, ZIP, MP4… max 150MB chacun — sélection multiple)</label>
                        <template x-if="mod.file_path && !mod.new_file">
                            <div class="mb-2 flex items-center gap-2 text-xs text-gray-500">
                                <svg class="h-4 w-4 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                Fichier actuel : <span x-text="mod.file_path.split('/').pop()"></span>
                                <button type="button" @click="mod.file_path = null; mod.new_file = true"
                                    class="text-red-400 hover:text-red-600">Remplacer</button>
                            </div>
                        </template>
                        <input type="file" :name="`modules[${index}][file]`"
                            class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm" />
                    </div>

                    {{-- Fichiers supplémentaires (upload multiple) --}}
                    <div>
                        <label class="mb-1 block text-xs font-medium text-gray-600">Ajouter d'autres fichiers à ce module (sélection multiple)</label>
                        <input type="file" :name="`modules[${index}][files][]`" multiple
                            class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm" />
                    </div>

                    {{-- Fichiers déjà uploadés pour ce module --}}
                    @if ($isEdit)
                    <template x-if="mod.files && mod.files.length > 0">
                        <div class="space-y-1.5">
                            <p class="text-xs font-medium text-gray-500">Fichiers de ce module :</p>
                            <template x-for="(f, fIdx) in mod.files" :key="fIdx">
                                <div class="flex items-center gap-2 rounded-md bg-gray-50 px-3 py-1.5 text-xs text-gray-600">
                                    <svg class="h-3.5 w-3.5 shrink-0 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                    <span class="flex-1 truncate" x-text="f.original_name || f.file_path.split('/').pop()"></span>
                                </div>
                            </template>
                        </div>
                    </template>
                    @endif
                </div>
            </template>

            <div x-show="modules.length === 0" class="text-center py-6 text-sm text-gray-400">
                Aucun module. Cliquez sur "Ajouter un module" pour commencer.
            </div>
        </div>

        {{-- SECTION FORMATION: GALERIE D'IMAGES --}}
        <div x-show="productType === 'formation'" x-cloak
             class="space-y-4 rounded-xl border-2 border-amber-200 bg-amber-50/30 p-6">

            <h3 class="text-sm font-bold text-amber-900 flex items-center gap-2">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                Galerie d'images (aperçus publics)
            </h3>

            @if ($isEdit && $product->galleryImages->isNotEmpty())
            <div class="grid grid-cols-4 gap-3">
                @foreach ($product->galleryImages as $img)
                <div class="relative group">
                    <img src="{{ asset('storage/' . $img->image_path) }}" class="h-20 w-full rounded-lg object-cover" />
                    <div class="absolute -top-1.5 -right-1.5 opacity-0 group-hover:opacity-100 transition">
                        <form method="POST" action="{{ route('admin.products.gallery.destroy', [$product, $img]) }}">
                            @csrf @method('DELETE')
                            <button type="submit" class="flex h-5 w-5 items-center justify-center rounded-full bg-red-500 text-white text-xs" onclick="return confirm('Supprimer cette image ?')">×</button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            <div>
                <label class="mb-1 block text-xs font-medium text-gray-600">Ajouter des images (max 7MB chacune)</label>
                <input type="file" name="gallery[]" multiple accept="image/*"
                    class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm" />
                <p class="mt-1 text-xs text-gray-400">Sélectionnez une ou plusieurs images. Elles apparaîtront sur la page produit.</p>
            </div>
        </div>

        <div class="flex gap-3 border-t border-gray-100 pt-6">
            <button type="submit" id="submitBtn" class="rounded-lg bg-indigo-600 px-6 py-2.5 text-sm font-semibold text-white shadow transition hover:bg-indigo-700">
                {{ $isEdit ? 'Enregistrer les modifications' : 'Créer le produit' }}
            </button>
            <a href="{{ route('admin.products.index') }}" class="rounded-lg border border-gray-300 px-6 py-2.5 text-sm font-medium text-gray-600 transition hover:bg-gray-50">
                Annuler
            </a>
        </div>
    </form>
</div>

{{-- Overlay de progression upload --}}
<div id="uploadOverlay" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm" style="display:none;">
    <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl">
        <div class="flex items-center gap-3 mb-4">
            <svg class="h-6 w-6 animate-spin text-indigo-600" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
            <div>
                <p class="text-sm font-bold text-gray-900" id="uploadTitle">Téléchargement en cours...</p>
                <p class="text-xs text-gray-500" id="uploadStatus">Envoi des fichiers vers le serveur...</p>
            </div>
        </div>
        <div class="h-3 w-full overflow-hidden rounded-full bg-gray-200">
            <div id="uploadProgressBar" class="h-full rounded-full bg-indigo-600 transition-all duration-300" style="width: 0%;"></div>
        </div>
        <p class="mt-2 text-center text-sm font-bold text-indigo-600" id="uploadPercent">0%</p>
    </div>
</div>

<script>
function productForm(config) {
    return {
        productType: config.productType,
        modules: config.existingModules.map(m => ({
            id: m.id,
            title: m.title,
            description: m.description || '',
            external_url: m.external_url || '',
            file_path: m.file_path || null,
            files: m.files || [],
            new_file: false
        })),

        addModule() {
            this.modules.push({
                id: null,
                title: '',
                description: '',
                external_url: '',
                file_path: null,
                files: [],
                new_file: true
            });
        },

        removeModule(index) {
            if (confirm('Supprimer ce module ?')) {
                this.modules.splice(index, 1);
            }
        }
    };
}

// Barre de progression upload
(function() {
    const form = document.querySelector('form[action]');
    const overlay = document.getElementById('uploadOverlay');
    const progressBar = document.getElementById('uploadProgressBar');
    const percentText = document.getElementById('uploadPercent');
    const statusText = document.getElementById('uploadStatus');
    const submitBtn = document.getElementById('submitBtn');

    if (!form || !overlay) return;

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(form);
        const action = form.getAttribute('action');
        const method = formData.get('_method') || 'POST';

        const xhr = new XMLHttpRequest();
        xhr.open('POST', action, true);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

        // Suivi de la progression de l'upload
        xhr.upload.addEventListener('progress', function(e) {
            if (e.lengthComputable) {
                const percent = Math.round((e.loaded / e.total) * 100);
                progressBar.style.width = percent + '%';
                percentText.textContent = percent + '%';

                if (percent < 100) {
                    statusText.textContent = 'Envoi des fichiers vers le serveur...';
                } else {
                    statusText.textContent = 'Traitement par le serveur...';
                    percentText.textContent = '100%';
                }
            }
        });

        xhr.onload = function() {
            if (xhr.status >= 200 && xhr.status < 400) {
                // Redirection vers la page d'index produits
                const response = xhr.responseText;
                // Vérifier si c'est une redirection HTML
                if (response.includes('redirect') || xhr.responseURL) {
                    window.location.href = xhr.responseURL || '{{ route("admin.products.index") }}';
                } else {
                    window.location.href = '{{ route("admin.products.index") }}';
                }
            } else {
                // Erreur - afficher le message
                overlay.style.display = 'none';
                submitBtn.disabled = false;
                submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');

                // Essayer d'extraire les erreurs de validation
                let errorMsg = 'Une erreur est survenue lors de l\'envi.';
                try {
                    const response = JSON.parse(xhr.responseText);
                    if (response.errors) {
                        const errorList = Object.values(response.errors).flat();
                        errorMsg = errorList.join('\n');
                    } else if (response.message) {
                        errorMsg = response.message;
                    }
                } catch(e) {
                    // Pas du JSON, afficher le statut
                    errorMsg = 'Erreur ' + xhr.status + '. Vérifiez la taille des fichiers.';
                }
                alert(errorMsg);
            }
        };

        xhr.onerror = function() {
            overlay.style.display = 'none';
            submitBtn.disabled = false;
            submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            alert('Erreur réseau. Vérifiez votre connexion.');
        };

        // Afficher l'overlay et desactiver le bouton
        overlay.style.display = 'flex';
        submitBtn.disabled = true;
        submitBtn.classList.add('opacity-50', 'cursor-not-allowed');

        xhr.send(formData);
    });
})();
</script>

<style>[x-cloak]{display:none!important}</style>
@endsection

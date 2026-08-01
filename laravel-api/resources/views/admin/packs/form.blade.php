@extends('admin.layouts.app')
@section('title', isset($pack) ? 'Modifier le pack' : 'Nouveau pack')

@section('content')
@php $isEdit = isset($pack); @endphp

<div class="mx-auto max-w-3xl">
    <form method="POST"
          action="{{ $isEdit ? route('admin.packs.update', $pack) : route('admin.packs.store') }}"
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
                <label class="mb-1.5 block text-sm font-medium text-gray-700">Nom du pack *</label>
                <input type="text" name="name" value="{{ old('name', $pack->name ?? '') }}" required
                    class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200" />
            </div>

            <div class="sm:col-span-2">
                <label class="mb-1.5 block text-sm font-medium text-gray-700">Description courte</label>
                <input type="text" name="short_description" value="{{ old('short_description', $pack->short_description ?? '') }}" maxlength="500"
                    class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200" />
            </div>

            <div class="sm:col-span-2">
                <label class="mb-1.5 block text-sm font-medium text-gray-700">Description longue</label>
                <textarea name="description" rows="5"
                    class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200">{{ old('description', $pack->description ?? '') }}</textarea>
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700">Prix *</label>
                <input type="number" name="price" value="{{ old('price', $pack->price ?? '') }}" min="0" step="1" required
                    class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200" />
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700">Prix promo (optionnel)</label>
                <input type="number" name="discount_price" value="{{ old('discount_price', $pack->discount_price ?? '') }}" min="0" step="1"
                    class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200" />
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700">Devise</label>
                <select name="currency" class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm outline-none focus:border-indigo-500">
                    @foreach (['XOF','EUR','USD'] as $cur)
                    <option value="{{ $cur }}" {{ old('currency', $pack->currency ?? 'XOF') === $cur ? 'selected' : '' }}>{{ $cur }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700">Catégorie</label>
                <select name="category_id" class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm outline-none focus:border-indigo-500">
                    <option value="">— Aucune —</option>
                    @foreach ($categories as $cat)
                    <option value="{{ $cat->id }}" {{ old('category_id', $pack->category_id ?? '') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="sm:col-span-2">
                <label class="mb-1.5 block text-sm font-medium text-gray-700">Produits inclus dans le pack</label>
                <div class="max-h-64 overflow-y-auto rounded-lg border border-gray-300 p-4">
                    @foreach ($products as $product)
                    <label class="flex cursor-pointer items-center gap-3 py-2">
                        <input type="checkbox" name="product_ids[]" value="{{ $product->id }}" 
                            class="h-4 w-4 accent-indigo-600"
                            {{ in_array($product->id, $selectedProductIds ?? []) ? 'checked' : '' }} />
                        <div>
                            <div class="text-sm font-medium text-gray-900">{{ $product->name }}</div>
                            <div class="text-xs text-gray-500">{{ number_format($product->price, 0, ',', ' ') }} {{ $product->currency }}</div>
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>

            <div class="sm:col-span-2">
                <label class="mb-1.5 block text-sm font-medium text-gray-700">Image (couverture)</label>
                <p class="mb-2 text-xs text-gray-500">Format: JPG, PNG, GIF, WebP — Max: 7 Mo</p>
                @if (!empty($pack->image))
                    <img src="{{ asset('storage/' . $pack->image) }}" class="mb-2 h-20 w-20 rounded-lg object-cover" />
                @endif
                <input type="file" name="image" accept="image/*"
                    class="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm" />
            </div>

            <div class="sm:col-span-2">
                <label class="mb-1.5 block text-sm font-medium text-gray-700">Fichiers du pack (ZIP, PDF, etc.)</label>
                <p class="mb-2 text-xs text-gray-500">Ajoutez des fichiers téléchargeables directement au pack — Max: 50 Mo par fichier</p>
                @if ($isEdit && $pack->files->isNotEmpty())
                    <div class="mb-3 space-y-2">
                        <p class="text-xs font-medium text-gray-700">Fichiers existants :</p>
                        @foreach ($pack->files as $file)
                        <div class="flex items-center justify-between rounded border border-gray-200 bg-gray-50 px-3 py-2 text-sm">
                            <div class="flex items-center gap-2">
                                <svg class="h-5 w-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <div>
                                    <span class="font-medium text-gray-900">{{ $file->name }}</span>
                                    <span class="ml-2 text-xs text-gray-500">({{ strtoupper($file->file_type) }} • {{ number_format($file->file_size / 1024 / 1024, 2) }} Mo)</span>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <a href="{{ asset('storage/' . $file->file_path) }}" target="_blank" class="text-indigo-600 hover:text-indigo-800 text-xs">Voir</a>
                                <label class="flex items-center gap-1 text-xs text-red-600 cursor-pointer">
                                    <input type="checkbox" name="delete_files[]" value="{{ $file->id }}" class="h-3 w-3">
                                    Supprimer
                                </label>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @endif
                <input type="file" name="files[]" multiple accept=".zip,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.rar,.7z"
                    class="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm" />
                <p class="mt-1 text-xs text-gray-400">Formats acceptés: ZIP, RAR, 7Z, PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX</p>
            </div>

            <div class="flex gap-6 sm:col-span-2">
                <label class="flex cursor-pointer items-center gap-2 text-sm">
                    <input type="hidden" name="is_active" value="0" />
                    <input type="checkbox" name="is_active" value="1" class="h-4 w-4 accent-indigo-600"
                        {{ old('is_active', $pack->is_active ?? true) ? 'checked' : '' }} />
                    Pack actif
                </label>
                <label class="flex cursor-pointer items-center gap-2 text-sm">
                    <input type="hidden" name="is_featured" value="0" />
                    <input type="checkbox" name="is_featured" value="1" class="h-4 w-4 accent-amber-500"
                        {{ old('is_featured', $pack->is_featured ?? false) ? 'checked' : '' }} />
                    Pack vedette (★)
                </label>
            </div>
        </div>

        <div class="flex gap-3 border-t border-gray-100 pt-6">
            <button type="submit" class="rounded-lg bg-indigo-600 px-6 py-2.5 text-sm font-semibold text-white shadow transition hover:bg-indigo-700">
                {{ $isEdit ? 'Enregistrer les modifications' : 'Créer le pack' }}
            </button>
            <a href="{{ route('admin.packs.index') }}" class="rounded-lg border border-gray-300 px-6 py-2.5 text-sm font-medium text-gray-600 transition hover:bg-gray-50">
                Annuler
            </a>
        </div>
    </form>
</div>
@endsection

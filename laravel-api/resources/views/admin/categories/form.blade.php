@extends('admin.layouts.app')
@section('title', isset($category) ? 'Modifier la catégorie' : 'Nouvelle catégorie')

@section('content')
@php $isEdit = isset($category); @endphp

<div class="mx-auto max-w-lg">
    <form method="POST"
          action="{{ $isEdit ? route('admin.categories.update', $category) : route('admin.categories.store') }}"
          class="space-y-5 rounded-xl border border-gray-200 bg-white p-8 shadow-sm">
        @csrf
        @if ($isEdit) @method('PUT') @endif

        @if ($errors->any())
        <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            @foreach ($errors->all() as $error)<p>{{ $error }}</p>@endforeach
        </div>
        @endif

        <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700">Nom *</label>
            <input type="text" name="name" value="{{ old('name', $category->name ?? '') }}" required
                class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200" />
        </div>

        <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700">Description</label>
            <textarea name="description" rows="3"
                class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200">{{ old('description', $category->description ?? '') }}</textarea>
        </div>

        <label class="flex cursor-pointer items-center gap-2 text-sm">
            <input type="hidden" name="is_active" value="0" />
            <input type="checkbox" name="is_active" value="1" class="h-4 w-4 accent-indigo-600"
                {{ old('is_active', $category->is_active ?? true) ? 'checked' : '' }} />
            Catégorie active
        </label>

        <div class="flex gap-3 border-t border-gray-100 pt-5">
            <button type="submit" class="rounded-lg bg-indigo-600 px-6 py-2.5 text-sm font-semibold text-white shadow transition hover:bg-indigo-700">
                {{ $isEdit ? 'Enregistrer' : 'Créer' }}
            </button>
            <a href="{{ route('admin.categories.index') }}" class="rounded-lg border border-gray-300 px-6 py-2.5 text-sm font-medium text-gray-600 transition hover:bg-gray-50">
                Annuler
            </a>
        </div>
    </form>
</div>
@endsection

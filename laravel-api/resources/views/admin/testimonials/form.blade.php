@extends('admin.layouts.app')
@section('title', isset($testimonial) ? 'Modifier le témoignage' : 'Nouveau témoignage')

@section('content')
@php $isEdit = isset($testimonial); @endphp
<div class="mx-auto max-w-2xl">
    <form method="POST"
          action="{{ $isEdit ? route('admin.testimonials.update', $testimonial) : route('admin.testimonials.store') }}"
          enctype="multipart/form-data"
          class="space-y-6 rounded-xl border border-gray-200 bg-white p-8 shadow-sm">
        @csrf
        @if ($isEdit) @method('PUT') @endif

        @if ($errors->any())
        <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            <ul class="list-inside list-disc">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
        @endif

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700">Nom du client *</label>
                <input type="text" name="author_name" value="{{ old('author_name', $testimonial->author_name ?? '') }}" required
                    class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200" />
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700">Titre / Poste</label>
                <input type="text" name="author_title" value="{{ old('author_title', $testimonial->author_title ?? '') }}"
                    class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm outline-none focus:border-indigo-500" placeholder="Ex: Entrepreneur, Dakar" />
            </div>
        </div>

        <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700">Témoignage *</label>
            <textarea name="content" rows="4" required
                class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200">{{ old('content', $testimonial->content ?? '') }}</textarea>
        </div>

        <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700">Note (1 à 5 étoiles)</label>
            <select name="rating" class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm outline-none focus:border-indigo-500">
                @for ($i = 5; $i >= 1; $i--)
                <option value="{{ $i }}" {{ old('rating', $testimonial->rating ?? 5) == $i ? 'selected' : '' }}>
                    {{ str_repeat('★', $i) }} ({{ $i }}/5)
                </option>
                @endfor
            </select>
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700">Photo du client (avatar)</label>
                @if (!empty($testimonial->author_avatar))
                    <img src="{{ asset('storage/' . $testimonial->author_avatar) }}" class="mb-2 h-12 w-12 rounded-full object-cover" />
                @endif
                <input type="file" name="author_avatar" accept="image/*"
                    class="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm" />
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700">Capture d'écran (preuve)</label>
                @if (!empty($testimonial->screenshot))
                    <img src="{{ asset('storage/' . $testimonial->screenshot) }}" class="mb-2 h-16 rounded-lg object-cover border" />
                @endif
                <input type="file" name="screenshot" accept="image/*"
                    class="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm" />
                <p class="mt-1 text-xs text-gray-400">Capture WhatsApp, email, réseau social…</p>
            </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700">Ordre d'affichage</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', $testimonial->sort_order ?? 0) }}" min="0"
                    class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm outline-none focus:border-indigo-500" />
            </div>
            <div class="flex items-end pb-2">
                <label class="flex cursor-pointer items-center gap-2 text-sm">
                    <input type="checkbox" name="is_active" value="1" class="h-4 w-4 accent-indigo-600"
                        {{ old('is_active', $testimonial->is_active ?? true) ? 'checked' : '' }} />
                    Visible sur le site
                </label>
            </div>
        </div>

        <div class="flex gap-3 border-t border-gray-100 pt-6">
            <button type="submit" class="rounded-lg bg-indigo-600 px-6 py-2.5 text-sm font-semibold text-white shadow transition hover:bg-indigo-700">
                {{ $isEdit ? 'Enregistrer' : 'Ajouter le témoignage' }}
            </button>
            <a href="{{ route('admin.testimonials.index') }}" class="rounded-lg border border-gray-300 px-6 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-50">Annuler</a>
        </div>
    </form>
</div>
@endsection

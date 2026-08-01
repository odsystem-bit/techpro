@extends('admin.layouts.app')
@section('title', 'Slides Hero')

@section('content')
<div class="mb-5 flex items-center justify-between">
    <p class="text-sm text-gray-500">{{ $slides->count() }} slide(s) — affiché(s) sur la page d'accueil</p>
    <a href="{{ route('admin.hero.create') }}" class="rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow transition hover:bg-indigo-700">
        + Nouveau slide
    </a>
</div>

<div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
    <table class="w-full text-sm">
        <thead class="border-b border-gray-100 bg-gray-50 text-xs uppercase tracking-wide text-gray-500">
            <tr>
                <th class="px-5 py-3 text-left">Image</th>
                <th class="px-5 py-3 text-left">Titre</th>
                <th class="px-5 py-3 text-left">Bouton</th>
                <th class="px-5 py-3 text-center">Ordre</th>
                <th class="px-5 py-3 text-center">Actif</th>
                <th class="px-5 py-3 text-center">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse ($slides as $slide)
            <tr class="hover:bg-gray-50">
                <td class="px-5 py-3">
                    @if ($slide->image)
                        <img src="{{ asset('storage/' . $slide->image) }}" class="h-14 w-24 rounded-lg object-cover" />
                    @else
                        <div class="flex h-14 w-24 items-center justify-center rounded-lg bg-gray-100 text-gray-300 text-xs">Aucune</div>
                    @endif
                </td>
                <td class="px-5 py-3 font-medium text-gray-900">
                    {{ $slide->title }}
                    @if ($slide->subtitle)<p class="text-xs text-gray-400">{{ Str::limit($slide->subtitle, 50) }}</p>@endif
                </td>
                <td class="px-5 py-3 text-gray-500">{{ $slide->btn_label ?? '—' }}</td>
                <td class="px-5 py-3 text-center text-gray-500">{{ $slide->sort_order }}</td>
                <td class="px-5 py-3 text-center">
                    <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $slide->is_active ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                        {{ $slide->is_active ? 'Oui' : 'Non' }}
                    </span>
                </td>
                <td class="px-5 py-3 text-center">
                    <div class="flex justify-center gap-2">
                        <a href="{{ route('admin.hero.edit', $slide) }}" class="rounded-lg border border-gray-200 px-3 py-1.5 text-xs text-gray-600 hover:bg-gray-50">Modifier</a>
                        <form method="POST" action="{{ route('admin.hero.destroy', $slide) }}" onsubmit="return confirm('Supprimer ce slide ?')">
                            @csrf @method('DELETE')
                            <button class="rounded-lg border border-red-200 px-3 py-1.5 text-xs text-red-600 hover:bg-red-50">Supprimer</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="py-12 text-center text-gray-400">Aucun slide. Ajoutez-en un !</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

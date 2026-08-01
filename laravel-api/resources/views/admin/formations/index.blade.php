@extends('admin.layouts.app')
@section('title', 'Formations')
@section('breadcrumb', 'Gestion des formations')

@section('content')
<div class="space-y-4">
    <div class="flex items-center justify-between">
        <p class="text-sm text-gray-500">{{ $formations->total() }} formation(s)</p>
        <a href="{{ route('admin.formations.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow transition hover:bg-indigo-700">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            Nouvelle formation
        </a>
    </div>

    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-xs font-medium uppercase text-gray-500">
                <tr>
                    <th class="px-6 py-3 text-left">Formation</th>
                    <th class="px-6 py-3 text-center">Modules</th>
                    <th class="px-6 py-3 text-right">Prix</th>
                    <th class="px-6 py-3 text-center">Statut</th>
                    <th class="px-6 py-3 text-center">Ventes</th>
                    <th class="px-6 py-3 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($formations as $formation)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            @if ($formation->image)
                                <img src="{{ asset('storage/' . $formation->image) }}" class="h-10 w-10 rounded-lg object-cover" alt="">
                            @else
                                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-100 text-indigo-600">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                </div>
                            @endif
                            <div>
                                <div class="font-medium text-gray-900">{{ $formation->name }}</div>
                                <div class="text-xs text-gray-400">{{ $formation->slug }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="inline-flex items-center gap-1 rounded-full bg-indigo-50 px-2.5 py-1 text-xs font-semibold text-indigo-700">
                            {{ $formation->modules_count }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        @if ($formation->discount_price)
                            <span class="text-gray-400 line-through">{{ number_format($formation->price, 0, ',', ' ') }}</span>
                            <span class="ml-1 font-semibold text-green-600">{{ number_format($formation->discount_price, 0, ',', ' ') }}</span>
                        @else
                            <span class="font-medium">{{ number_format($formation->price, 0, ',', ' ') }}</span>
                        @endif
                        <span class="text-xs text-gray-400"> {{ $formation->currency }}</span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if ($formation->is_active)
                            <span class="rounded-full bg-green-100 px-2.5 py-1 text-xs font-medium text-green-700">Actif</span>
                        @else
                            <span class="rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-600">Inactif</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="text-sm font-medium text-gray-700">{{ $formation->sales_count }}</span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('admin.formations.edit', $formation) }}" class="rounded-lg border border-gray-200 px-3 py-1.5 text-xs font-medium text-gray-600 transition hover:bg-gray-50">Modifier</a>
                            <form method="POST" action="{{ route('admin.formations.destroy', $formation) }}" onsubmit="return confirm('Supprimer cette formation et tous ses modules ?')">
                                @csrf @method('DELETE')
                                <button class="rounded-lg border border-red-200 px-3 py-1.5 text-xs font-medium text-red-600 transition hover:bg-red-50">Supprimer</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-6 py-12 text-center text-gray-400">
                    Aucune formation. <a href="{{ route('admin.formations.create') }}" class="text-indigo-600 hover:underline">Créer la première formation</a>
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>{{ $formations->links() }}</div>
</div>
@endsection

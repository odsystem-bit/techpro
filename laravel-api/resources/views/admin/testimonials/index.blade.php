@extends('admin.layouts.app')
@section('title', 'Témoignages')

@section('content')
<div class="mb-5 flex items-center justify-between">
    <p class="text-sm text-gray-500">{{ $testimonials->count() }} témoignage(s)</p>
    <a href="{{ route('admin.testimonials.create') }}" class="rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow transition hover:bg-indigo-700">
        + Nouveau témoignage
    </a>
</div>

<div class="space-y-4">
    @forelse ($testimonials as $t)
    <div class="flex gap-4 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
        <div class="shrink-0">
            @if ($t->author_avatar)
                <img src="{{ asset('storage/' . $t->author_avatar) }}" class="h-12 w-12 rounded-full object-cover" />
            @else
                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-indigo-100 text-indigo-600 font-bold text-lg">
                    {{ substr($t->author_name, 0, 1) }}
                </div>
            @endif
        </div>
        <div class="flex-1 min-w-0">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="font-semibold text-gray-900">{{ $t->author_name }}</p>
                    @if ($t->author_title)<p class="text-xs text-gray-400">{{ $t->author_title }}</p>@endif
                    <div class="mt-0.5 flex gap-0.5">
                        @for ($i = 1; $i <= 5; $i++)
                            <span class="text-sm {{ $i <= $t->rating ? 'text-amber-400' : 'text-gray-200' }}">★</span>
                        @endfor
                    </div>
                </div>
                <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $t->is_active ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                    {{ $t->is_active ? 'Actif' : 'Masqué' }}
                </span>
            </div>
            <p class="mt-2 text-sm text-gray-600">{{ Str::limit($t->content, 120) }}</p>
            @if ($t->screenshot)
                <img src="{{ asset('storage/' . $t->screenshot) }}" class="mt-3 h-20 rounded-lg object-cover border border-gray-200" />
            @endif
        </div>
        <div class="flex shrink-0 flex-col gap-2">
            <a href="{{ route('admin.testimonials.edit', $t) }}" class="rounded-lg border border-gray-200 px-3 py-1.5 text-xs text-gray-600 hover:bg-gray-50 text-center">Modifier</a>
            <form method="POST" action="{{ route('admin.testimonials.destroy', $t) }}" onsubmit="return confirm('Supprimer ?')">
                @csrf @method('DELETE')
                <button class="w-full rounded-lg border border-red-200 px-3 py-1.5 text-xs text-red-600 hover:bg-red-50">Supprimer</button>
            </form>
        </div>
    </div>
    @empty
    <div class="rounded-2xl border border-gray-200 bg-white py-16 text-center text-gray-400">
        Aucun témoignage. Ajoutez vos premiers clients satisfaits !
    </div>
    @endforelse
</div>
@endsection

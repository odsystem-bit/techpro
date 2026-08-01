@extends('layouts.app')
@section('title', $product->name . ' — Tech Pro Futur')

@section('content')
<div class="mx-auto max-w-3xl px-6 py-10 lg:px-8">

    <div class="mb-6 rounded-xl border-2 border-indigo-100 bg-indigo-50 p-6">
        <div class="flex items-center gap-3 mb-2">
            <span class="rounded-full bg-indigo-600 px-2.5 py-1 text-[10px] font-bold text-white">FORMATION</span>
            <h1 class="text-xl font-bold text-gray-900">{{ $product->name }}</h1>
        </div>
        <p class="text-sm text-gray-500">Commande #{{ $order->order_number }}</p>
        <div class="mt-3 flex items-center gap-4 text-xs text-gray-500">
            <span class="flex items-center gap-1">
                <svg class="h-4 w-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Lien valable jusqu'au {{ $order->download_expires_at?->format('d/m/Y') }}
            </span>
            <span class="flex items-center gap-1">
                <svg class="h-4 w-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                {{ $order->download_limit - $order->download_count }} téléchargement(s) restant(s)
            </span>
        </div>
    </div>

    <div class="space-y-4">
        @foreach ($product->modules as $module)
        <div class="rounded-xl border border-gray-200 bg-white p-5">
            <div class="flex items-center gap-3 mb-3">
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-indigo-100 text-indigo-600 font-bold text-sm">
                    {{ $loop->iteration }}
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="text-sm font-semibold text-gray-900">{{ $module->title }}</h3>
                    @if ($module->description)
                        <p class="text-xs text-gray-500 mt-0.5">{{ $module->description }}</p>
                    @endif
                </div>
                @if ($module->has_external_url)
                    <a href="{{ $module->external_url }}" target="_blank"
                       class="shrink-0 inline-flex items-center gap-1.5 rounded-lg bg-red-600 px-3 py-2 text-xs font-bold text-white hover:bg-red-700 transition">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Vidéo
                    </a>
                @endif
            </div>

            {{-- Fichier principal du module --}}
            @if ($module->file_path)
                <div class="flex items-center justify-between rounded-lg bg-gray-50 px-4 py-2.5 mb-2">
                    <div class="flex items-center gap-2 text-xs text-gray-600 min-w-0">
                        <svg class="h-4 w-4 shrink-0 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        <span class="truncate">{{ $module->file_name }}</span>
                    </div>
                    <a href="{{ route('download.module', ['token' => $token, 'module_id' => $module->id]) }}"
                       class="download-btn shrink-0 inline-flex items-center gap-1.5 rounded-lg bg-indigo-600 px-3 py-1.5 text-xs font-bold text-white hover:bg-indigo-700 transition">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        Télécharger
                    </a>
                </div>
            @endif

            {{-- Fichiers supplémentaires du module --}}
            @foreach ($module->files as $file)
                <div class="flex items-center justify-between rounded-lg bg-gray-50 px-4 py-2.5 mb-2">
                    <div class="flex items-center gap-2 text-xs text-gray-600 min-w-0">
                        <svg class="h-4 w-4 shrink-0 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        <span class="truncate">{{ $file->original_name }}</span>
                    </div>
                    <a href="{{ route('download.module.file', ['token' => $token, 'file_id' => $file->id]) }}"
                       class="download-btn shrink-0 inline-flex items-center gap-1.5 rounded-lg bg-indigo-600 px-3 py-1.5 text-xs font-bold text-white hover:bg-indigo-700 transition">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        Télécharger
                    </a>
                </div>
            @endforeach

            @if (!$module->file_path && $module->files->isEmpty() && !$module->has_external_url)
                <p class="text-xs text-gray-400 py-2">Aucun fichier pour ce module</p>
            @endif
        </div>
        @endforeach
    </div>

    @if ($product->modules->isEmpty())
        <div class="rounded-xl border border-gray-200 bg-white py-12 text-center">
            <p class="text-gray-400">Aucun module disponible pour cette formation.</p>
        </div>
    @endif

    <div class="mt-6 rounded-xl border border-gray-200 bg-white p-5 text-left space-y-2.5 text-sm text-gray-600">
        <p class="flex items-center gap-2.5">
            <svg class="h-4 w-4 shrink-0 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            Email de confirmation envoyé avec lien sécurisé
        </p>
        <p class="flex items-center gap-2.5">
            <svg class="h-4 w-4 shrink-0 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            Lien valable 7 jours
        </p>
    </div>

    <div class="mt-6 text-center">
        <a href="{{ route('shop.index') }}" class="rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-medium text-white hover:bg-indigo-500">
            Retour à la boutique
        </a>
    </div>
</div>
@endsection

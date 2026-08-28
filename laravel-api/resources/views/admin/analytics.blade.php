@extends('admin.layouts.app')
@section('title', 'Statistiques de visite')
@section('breadcrumb', 'Analytics')

@section('content')
<div class="space-y-6">

    {{-- Sélecteur de période --}}
    <div class="flex items-center gap-2">
        <span class="text-sm text-gray-500">Période :</span>
        @php $periods = ['1' => 'Aujourd\'hui', '7' => '7 jours', '30' => '30 jours', '90' => '90 jours']; @endphp
        @foreach ($periods as $value => $label)
        <a href="{{ route('admin.analytics', ['period' => $value]) }}"
           class="rounded-lg px-3 py-1.5 text-xs font-semibold transition {{ (int)$period === (int)$value ? 'bg-indigo-600 text-white' : 'border border-gray-200 bg-white text-gray-600 hover:bg-gray-50' }}">
            {{ $label }}
        </a>
        @endforeach
    </div>

    {{-- Cartes statistiques --}}
    <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
            <div class="flex items-center gap-2">
                <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-indigo-50">
                    <svg class="h-5 w-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                </div>
                <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Total vues</p>
            </div>
            <p class="mt-3 text-3xl font-bold text-gray-900">{{ number_format($totalViews, 0, ',', ' ') }}</p>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
            <div class="flex items-center gap-2">
                <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-green-50">
                    <svg class="h-5 w-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Visiteurs uniques</p>
            </div>
            <p class="mt-3 text-3xl font-bold text-gray-900">{{ number_format($uniqueVisitors, 0, ',', ' ') }}</p>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
            <div class="flex items-center gap-2">
                <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-amber-50">
                    <svg class="h-5 w-5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Vues aujourd'hui</p>
            </div>
            <p class="mt-3 text-3xl font-bold text-gray-900">{{ number_format($todayViews, 0, ',', ' ') }}</p>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
            <div class="flex items-center gap-2">
                <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-blue-50">
                    <svg class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </div>
                <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Vues hier</p>
            </div>
            <p class="mt-3 text-3xl font-bold text-gray-900">{{ number_format($yesterdayViews, 0, ',', ' ') }}</p>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">

        {{-- Graphique des visites par jour --}}
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <h2 class="mb-4 text-sm font-semibold text-gray-800">Visites par jour</h2>
            @php
                $maxViews = $dailyViews->max('views') ?? 1;
                $maxViews = max(1, $maxViews);
            @endphp
            <div class="space-y-2">
                @forelse ($dailyViews as $day)
                <div class="flex items-center gap-3">
                    <span class="w-20 shrink-0 text-xs text-gray-500">{{ \Carbon\Carbon::parse($day->date)->format('d/m') }}</span>
                    <div class="flex-1 overflow-hidden rounded-full bg-gray-100">
                        <div class="h-7 rounded-full bg-gradient-to-r from-indigo-500 to-indigo-600 flex items-center justify-end px-2"
                             style="width: {{ max(3, ($day->views / $maxViews) * 100) }}%">
                            <span class="text-xs font-bold text-white">{{ $day->views }}</span>
                        </div>
                    </div>
                    <span class="w-16 shrink-0 text-right text-xs text-gray-400">{{ $day->visitors }} vis.</span>
                </div>
                @empty
                <p class="py-8 text-center text-sm text-gray-400">Aucune donnée pour cette période.</p>
                @endforelse
            </div>
        </div>

        {{-- Pages les plus visitées --}}
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-100 px-6 py-4">
                <h2 class="text-sm font-semibold text-gray-800">Pages les plus visitées</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-xs font-medium uppercase text-gray-500">
                        <tr>
                            <th class="px-6 py-3 text-left">Page</th>
                            <th class="px-6 py-3 text-right">Vues</th>
                            <th class="px-6 py-3 text-right">Uniques</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($topPages as $page)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-3 text-gray-700">
                                <a href="{{ $page->path }}" target="_blank" class="text-indigo-600 hover:underline">{{ $page->path }}</a>
                            </td>
                            <td class="px-6 py-3 text-right font-semibold text-gray-900">{{ number_format($page->views) }}</td>
                            <td class="px-6 py-3 text-right text-gray-500">{{ number_format($page->unique_views) }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="px-6 py-8 text-center text-gray-400">Aucune donnée.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">

        {{-- Appareils --}}
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <h2 class="mb-4 text-sm font-semibold text-gray-800">Appareils</h2>
            <div class="space-y-3">
                @forelse ($topDevices as $device)
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        @if ($device->device_type === 'Mobile')
                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 18h.01M8 21h8a1 1 0 001-1V4a1 1 0 00-1-1H8a1 1 0 00-1 1v16a1 1 0 001 1z"/></svg>
                        @elseif ($device->device_type === 'Tablet')
                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 18h.01M7 21h10a1 1 0 001-1V4a1 1 0 00-1-1H7a1 1 0 00-1 1v16a1 1 0 001 1z"/></svg>
                        @else
                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        @endif
                        <span class="text-sm font-medium text-gray-700">{{ $device->device_type }}</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-32 rounded-full bg-gray-100">
                            <div class="h-2 rounded-full bg-indigo-500" style="width: {{ ($device->views / max(1, $totalViews)) * 100 }}%"></div>
                        </div>
                        <span class="w-12 text-right text-sm font-semibold text-gray-900">{{ $device->views }}</span>
                    </div>
                </div>
                @empty
                <p class="py-4 text-center text-sm text-gray-400">Aucune donnée.</p>
                @endforelse
            </div>
        </div>

        {{-- Sources de trafic --}}
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-100 px-6 py-4">
                <h2 class="text-sm font-semibold text-gray-800">Sources de trafic</h2>
            </div>
            <ul class="divide-y divide-gray-100">
                @forelse ($topReferers as $referer)
                <li class="flex items-center justify-between px-6 py-3">
                    <div class="min-w-0">
                        @php
                            $host = parse_url($referer->referer, PHP_URL_HOST) ?: $referer->referer;
                        @endphp
                        <p class="truncate text-sm text-gray-700">{{ $host }}</p>
                        <p class="truncate text-xs text-gray-400">{{ $referer->referer }}</p>
                    </div>
                    <span class="ml-4 shrink-0 rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-600">
                        {{ $referer->views }}
                    </span>
                </li>
                @empty
                <li class="px-6 py-8 text-center text-sm text-gray-400">Aucune donnée.</li>
                @endforelse
            </ul>
        </div>
    </div>

</div>
@endsection

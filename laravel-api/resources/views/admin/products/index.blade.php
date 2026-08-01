@extends('admin.layouts.app')
@section('title', 'Produits')

@section('content')
<div class="space-y-4">
    <div class="flex items-center justify-between">
        <p class="text-sm text-gray-500">{{ $products->total() }} produit(s)</p>
        <a href="{{ route('admin.products.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow transition hover:bg-indigo-700">
            + Nouveau produit
        </a>
    </div>

    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-xs font-medium uppercase text-gray-500">
                <tr>
                    <th class="px-6 py-3 text-left">Produit</th>
                    <th class="px-6 py-3 text-left">Catégorie</th>
                    <th class="px-6 py-3 text-right">Prix</th>
                    <th class="px-6 py-3 text-center">Statut</th>
                    <th class="px-6 py-3 text-center">Vedette</th>
                    <th class="px-6 py-3 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($products as $product)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <div class="font-medium text-gray-900">{{ $product->name }}</div>
                        <div class="text-xs text-gray-400">{{ $product->slug }}</div>
                    </td>
                    <td class="px-6 py-4 text-gray-600">{{ $product->category?->name ?? '—' }}</td>
                    <td class="px-6 py-4 text-right">
                        @if ($product->has_discount)
                            <span class="text-gray-400 line-through">{{ number_format($product->price, 0, ',', ' ') }}</span>
                            <span class="ml-1 font-semibold text-green-600">{{ number_format($product->discount_price, 0, ',', ' ') }}</span>
                        @else
                            <span class="font-medium">{{ number_format($product->price, 0, ',', ' ') }}</span>
                        @endif
                        <span class="text-xs text-gray-400"> {{ $product->currency }}</span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if ($product->is_active)
                            <span class="rounded-full bg-green-100 px-2.5 py-1 text-xs font-medium text-green-700">Actif</span>
                        @else
                            <span class="rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-600">Inactif</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        <form method="POST" action="{{ route('admin.products.toggle-featured', $product) }}">
                            @csrf @method('PATCH')
                            <button type="submit" class="text-2xl transition hover:scale-125 {{ $product->is_featured ? 'text-amber-400' : 'text-gray-300 hover:text-amber-400' }}" title="{{ $product->is_featured ? 'Retirer des vedettes' : 'Mettre en vedette' }}">
                                ★
                            </button>
                        </form>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('admin.products.edit', $product) }}" class="rounded-lg border border-gray-200 px-3 py-1.5 text-xs font-medium text-gray-600 transition hover:bg-gray-50">Modifier</a>
                            <form method="POST" action="{{ route('admin.products.destroy', $product) }}" onsubmit="return confirm('Supprimer ce produit ?')">
                                @csrf @method('DELETE')
                                <button class="rounded-lg border border-red-200 px-3 py-1.5 text-xs font-medium text-red-600 transition hover:bg-red-50">Supprimer</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-6 py-12 text-center text-gray-400">Aucun produit. <a href="{{ route('admin.products.create') }}" class="text-indigo-600 hover:underline">Créer le premier</a></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>{{ $products->links() }}</div>
</div>
@endsection

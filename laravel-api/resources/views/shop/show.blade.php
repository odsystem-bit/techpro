@extends('layouts.app')
@section('title', $product->name . ' — Tech Pro Futur')
@section('description', $product->short_description ?? Str::limit($product->description, 160))

@section('content')
<div class="mx-auto max-w-6xl px-6 py-10 lg:px-8">

    {{-- Banner ODIBOT pour les ebooks --}}
    @if ($product->product_type === 'ebook')
    <div class="mb-6 rounded-xl border-2 border-indigo-200 bg-gradient-to-r from-indigo-50 to-purple-50 p-5">
        <div class="flex items-start gap-4">
            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-indigo-600 text-white">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
            </div>
            <div class="flex-1">
                <h3 class="text-sm font-bold text-indigo-900">ODIBOT inclus gratuitement avec cet ebook !</h3>
                <p class="mt-1 text-sm text-indigo-700">Achetez cet ebook et recevez <strong>ODIBOT</strong>, votre assistant IA personnel, gratuitement. Posez vos questions, apprenez plus vite, et progressez dans vos projets.</p>
            </div>
        </div>
    </div>
    @endif

    <nav class="mb-6 flex items-center gap-1.5 text-sm text-gray-400">
        <a href="{{ route('home') }}" class="hover:text-gray-700">Accueil</a>
        <span>/</span>
        <a href="{{ route('shop.index') }}" class="hover:text-gray-700">Boutique</a>
        @if ($product->category)
        <span>/</span>
        <a href="{{ route('shop.index', ['category' => $product->category->slug]) }}" class="hover:text-gray-700">{{ $product->category->name }}</a>
        @endif
        <span>/</span>
        <span class="text-gray-600">{{ $product->name }}</span>
    </nav>

    <div class="grid gap-8 lg:grid-cols-2">

        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white">
            @if ($product->image)
                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" loading="lazy" class="h-full w-full object-cover">
            @else
                <div class="flex h-72 items-center justify-center bg-gray-100 text-gray-300">
                    <svg class="h-16 w-16" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
            @endif
        </div>

        <div class="space-y-5">
            <div>
                <div class="flex items-center gap-2">
                    @if ($product->category)
                        <span class="rounded bg-gray-100 px-2 py-0.5 text-xs text-gray-500">{{ $product->category->name }}</span>
                    @endif
                    @if ($product->has_discount)
                        <span class="rounded bg-green-50 px-2 py-0.5 text-xs font-semibold text-green-700">-{{ $product->discount_percent }}%</span>
                    @endif
                </div>
                <h1 class="mt-3 text-2xl font-bold text-gray-900">{{ $product->name }}</h1>
                @if ($product->short_description)
                    <p class="mt-2 text-gray-500">{{ $product->short_description }}</p>
                @endif
            </div>

            <div class="flex items-baseline gap-3">
                @if ($product->has_discount)
                    <span class="text-lg text-gray-400 line-through">{{ number_format($product->price, 0, ',', ' ') }} {{ $product->currency }}</span>
                    <span class="text-2xl font-bold text-gray-900">{{ number_format($product->discount_price, 0, ',', ' ') }} {{ $product->currency }}</span>
                @else
                    <span class="text-2xl font-bold text-gray-900">{{ number_format($product->price, 0, ',', ' ') }} {{ $product->currency }}</span>
                @endif
            </div>

            @if (!empty($product->features))
            <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
                <p class="mb-2 text-xs font-medium text-gray-500">Ce que vous obtenez</p>
                <ul class="space-y-1.5">
                    @foreach ($product->features as $feature)
                    <li class="flex items-center gap-2 text-sm text-gray-700">
                        <svg class="h-3.5 w-3.5 shrink-0 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        {{ $feature }}
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form id="addToCartForm" method="POST" action="{{ route('shop.cart.add', $product) }}" class="flex items-center gap-3">
                @csrf
                <input type="number" name="quantity" value="1" min="1"
                    @if ($product->stock !== -1) max="{{ $product->stock }}" @endif
                    class="w-16 rounded-lg border border-gray-200 px-3 py-2.5 text-center text-sm outline-none focus:border-indigo-400" />
                <button type="submit"
                    class="flex-1 rounded-lg bg-indigo-600 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-500">
                    Ajouter au panier
                </button>
            </form>

            <div class="flex gap-5 text-sm text-gray-400">
                <span class="flex items-center gap-1.5">
                    <svg class="h-3.5 w-3.5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    Livraison par email
                </span>
                <span class="flex items-center gap-1.5">
                    <svg class="h-3.5 w-3.5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    Paiement sécurisé
                </span>
            </div>
        </div>
    </div>

    @if ($product->preview_url)
    <div class="mt-8 rounded-xl border border-gray-200 bg-white p-6">
        <h2 class="mb-4 text-base font-bold text-gray-900">Aperçu du produit</h2>
        <div class="aspect-video w-full overflow-hidden rounded-lg bg-gray-100">
            <iframe src="{{ $product->preview_url }}" class="h-full w-full" frameborder="0" allowfullscreen></iframe>
        </div>
    </div>
    @endif

    @if ($product->isFormation() && $product->modules->isNotEmpty())
    <div class="mt-10 rounded-xl border border-gray-200 bg-white p-6">
        <h2 class="mb-4 text-base font-bold text-gray-900">Contenu de la formation</h2>
        <div class="space-y-2">
            @foreach ($product->modules as $module)
            <div class="flex items-center gap-3 rounded-lg bg-gray-50 px-4 py-3">
                <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-indigo-100 text-xs font-bold text-indigo-600">
                    {{ $loop->iteration }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900">{{ $module->title }}</p>
                    @if ($module->description)
                        <p class="text-xs text-gray-500 mt-0.5 truncate">{{ $module->description }}</p>
                    @endif
                </div>
                <div class="shrink-0 flex items-center gap-1.5 text-xs text-gray-400">
                    @if ($module->file_path || $module->files->isNotEmpty())
                        <span class="flex items-center gap-1">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                            {{ ($module->file_path ? 1 : 0) + $module->files->count() }} fichier(s)
                        </span>
                    @endif
                    @if ($module->has_external_url)
                        <span class="flex items-center gap-1">
                            <svg class="h-3.5 w-3.5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/></svg>
                            Vidéo
                        </span>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    @if ($product->isFormation() && $product->galleryImages->isNotEmpty())
    <div class="mt-10 rounded-xl border border-gray-200 bg-white p-6">
        <h2 class="mb-4 text-base font-bold text-gray-900">Galerie d'aperçus</h2>
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3">
            @foreach ($product->galleryImages as $img)
            <div class="overflow-hidden rounded-lg border border-gray-200">
                <img src="{{ asset('storage/' . $img->image_path) }}" alt="Aperçu {{ $loop->iteration }}" class="h-40 w-full object-cover">
            </div>
            @endforeach
        </div>
    </div>
    @endif

    @if ($product->description)
    <div class="mt-10 rounded-xl border border-gray-200 bg-white p-6">
        <h2 class="mb-3 text-base font-bold text-gray-900">Description</h2>
        <div class="prose prose-sm max-w-none text-gray-600">
            {!! nl2br(e($product->description)) !!}
        </div>
    </div>
    @endif

    @if ($related->isNotEmpty())
    <div class="mt-12">
        <h2 class="mb-5 text-base font-bold text-gray-900">Produits similaires</h2>
        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($related as $rel)
            <a href="{{ route('shop.show', $rel) }}" class="group overflow-hidden rounded-xl border border-gray-200 bg-white transition hover:shadow-md">
                @if ($rel->image)
                    <img src="{{ asset('storage/' . $rel->image) }}" alt="{{ $rel->name }}" class="h-32 w-full object-cover">
                @else
                    <div class="flex h-32 items-center justify-center bg-gray-100 text-gray-300">
                        <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                @endif
                <div class="p-3">
                    <p class="text-sm font-medium text-gray-900 group-hover:text-indigo-600">{{ $rel->name }}</p>
                    <p class="mt-1 text-sm font-bold text-gray-700">{{ number_format($rel->discount_price ?? $rel->price, 0, ',', ' ') }} {{ $rel->currency }}</p>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif
</div>

{{-- ViewContent Event --}}
<script>
    (function() {
        if (typeof window.MetaPixel !== 'undefined') {
            window.MetaPixel.trackViewContent(
                '{{ $product->id }}',
                {{ $product->effective_price }},
                '{{ $product->currency }}'
            );
        }

        // AddToCart tracking
        const addToCartForm = document.getElementById('addToCartForm');
        if (addToCartForm && typeof window.MetaPixel !== 'undefined') {
            addToCartForm.addEventListener('submit', function(e) {
                const quantity = parseInt(this.querySelector('input[name="quantity"]').value) || 1;
                const totalPrice = {{ $product->effective_price }} * quantity;
                window.MetaPixel.trackAddToCart(
                    '{{ $product->id }}',
                    totalPrice,
                    '{{ $product->currency }}'
                );
            });
        }
    })();
</script>
@endsection

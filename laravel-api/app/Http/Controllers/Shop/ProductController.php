<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Pack;
use App\Models\Product;
use App\Models\SiteSetting;
use App\Models\Testimonial;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function home()
    {
        $featured = Product::with('category')
            ->where('is_active', true)
            ->where('is_featured', true)
            ->latest()
            ->take(8)
            ->get();

        $ebooks = Product::with('category')
            ->where('is_active', true)
            ->where('product_type', 'ebook')
            ->latest()
            ->take(8)
            ->get();

        $formations = Product::with('category')
            ->where('is_active', true)
            ->where('product_type', 'formation')
            ->latest()
            ->take(6)
            ->get();

        $templates = Product::with('category')
            ->where('is_active', true)
            ->where('product_type', 'template')
            ->latest()
            ->take(6)
            ->get();

        $packs = collect();
        try {
            $packs = Pack::with('category', 'products')
                ->where('is_active', true)
                ->where('is_featured', true)
                ->latest()
                ->take(6)
                ->get();
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Pack table not available: ' . $e->getMessage());
        }

        $categories = Category::where('is_active', true)
            ->withCount(['products' => fn ($q) => $q->where('is_active', true)])
            ->orderBy('name')
            ->get()
            ->filter(fn ($c) => $c->products_count > 0);

        $testimonials = Testimonial::active();
        $settings     = SiteSetting::pluck('value', 'key');

        return view('home', compact('featured', 'ebooks', 'formations', 'templates', 'packs', 'categories', 'testimonials', 'settings'));
    }

    public function index(Request $request)
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        // Si le type est "pack", on affiche les packs
        if ($request->type === 'pack') {
            try {
                $query = Pack::with('category', 'products')->where('is_active', true);

                if ($request->filled('category')) {
                    $query->whereHas('category', fn ($q) => $q->where('slug', $request->category));
                }

                if ($request->filled('search')) {
                    $search = $request->search;
                    $query->where(fn ($q) => $q->where('name', 'like', "%{$search}%")
                        ->orWhere('short_description', 'like', "%{$search}%"));
                }

                $packs = $query->latest()->paginate(12)->withQueryString();

                return view('shop.packs.index', compact('packs', 'categories'));
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Pack table not available: ' . $e->getMessage());
                return redirect()->route('shop.index')->with('info', 'Les packs seront bientôt disponibles.');
            }
        }

        // Sinon on affiche les produits
        $query = Product::with('category')->where('is_active', true);

        if ($request->filled('category')) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $request->category));
        }

        if ($request->filled('type') && in_array($request->type, ['ebook', 'formation', 'template'])) {
            $query->where('product_type', $request->type);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(fn ($q) => $q->where('name', 'like', "%{$search}%")
                ->orWhere('short_description', 'like', "%{$search}%"));
        }

        // Mettre les ebooks en premier si aucun filtre de type n'est appliqué
        if (!$request->filled('type')) {
            $query->orderByRaw("CASE WHEN product_type = 'ebook' THEN 0 ELSE 1 END")
                  ->latest();
        } else {
            $query->latest();
        }

        $products = $query->paginate(12)->withQueryString();

        return view('shop.index', compact('products', 'categories'));
    }

    public function show(Product $product)
    {
        abort_if(! $product->is_active, 404);

        $product->load(['modules.files', 'galleryImages']);

        $related = Product::with('category')
            ->where('is_active', true)
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->take(4)
            ->get();

        return view('shop.show', compact('product', 'related'));
    }
}

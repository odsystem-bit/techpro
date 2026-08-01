<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Pack;
use App\Services\MonerooService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ApiController extends Controller
{
    public function catalog()
    {
        $products = Product::where('is_active', true)
            ->with('category')
            ->orderByDesc('is_featured')
            ->orderByDesc('sales_count')
            ->get()
            ->map(fn ($p) => [
                'id'                => $p->id,
                'name'              => $p->name,
                'slug'              => $p->slug,
                'price'             => (float) $p->price,
                'discount_price'    => $p->discount_price ? (float) $p->discount_price : null,
                'currency'          => $p->currency ?? 'XOF',
                'product_type'      => $p->product_type,
                'short_description' => $p->short_description,
                'description'       => $p->description,
                'image'             => $p->image,
                'image_url'         => $p->image ? asset('storage/' . $p->image) : null,
                'category'          => $p->category?->name,
                'is_featured'       => $p->is_featured,
                'sales_count'       => $p->sales_count,
                'features'          => $p->features,
            ]);

        $packs = Pack::where('is_active', true)
            ->with('category')
            ->orderByDesc('is_featured')
            ->get()
            ->map(fn ($pk) => [
                'id'                => $pk->id,
                'name'              => $pk->name,
                'slug'              => $pk->slug,
                'price'             => (float) $pk->price,
                'discount_price'    => $pk->discount_price ? (float) $pk->discount_price : null,
                'currency'          => $pk->currency ?? 'XOF',
                'short_description' => $pk->short_description,
                'description'       => $pk->description,
                'image'             => $pk->image,
                'image_url'         => $pk->image ? asset('storage/' . $pk->image) : null,
                'category'          => $pk->category?->name,
                'is_featured'       => $pk->is_featured,
                'sales_count'       => $pk->sales_count,
            ]);

        return response()->json([
            'products' => $products,
            'packs'    => $packs,
        ]);
    }

    public function product(string $slug)
    {
        $product = Product::where('slug', $slug)->where('is_active', true)->first();

        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        return response()->json([
            'id'                => $product->id,
            'name'              => $product->name,
            'slug'              => $product->slug,
            'price'             => (float) $product->price,
            'discount_price'    => $product->discount_price ? (float) $product->discount_price : null,
            'currency'          => $product->currency ?? 'XOF',
            'product_type'      => $product->product_type,
            'short_description' => $product->short_description,
            'description'       => $product->description,
            'image'             => $product->image,
            'image_url'         => $product->image ? asset('storage/' . $product->image) : null,
            'features'          => $product->features,
            'sales_count'       => $product->sales_count,
            'checkout_url'      => route('shop.checkout') . '?product=' . $product->slug,
        ]);
    }

    public function checkout(Request $request)
    {
        $validated = $request->validate([
            'product_id'    => 'nullable|exists:products,id',
            'pack_id'       => 'nullable|exists:packs,id',
            'customer_name' => 'nullable|string|max:255',
            'customer_email' => 'nullable|email',
            'customer_phone' => 'nullable|string|max:50',
        ]);

        $orderable = null;
        $orderableType = null;

        if (!empty($validated['product_id'])) {
            $orderable = Product::find($validated['product_id']);
            $orderableType = Product::class;
        } elseif (!empty($validated['pack_id'])) {
            $orderable = Pack::find($validated['pack_id']);
            $orderableType = Pack::class;
        }

        if (!$orderable) {
            return response()->json(['error' => 'Product or pack not specified'], 422);
        }

        $price = $orderable->discount_price ?? $orderable->price;
        $currency = $orderable->currency ?? 'XOF';

        $order = Order::create([
            'orderable_id'    => $orderable->id,
            'orderable_type'  => $orderableType,
            'customer_name'   => $validated['customer_name'] ?? 'Client WhatsApp',
            'customer_email'  => $validated['customer_email'] ?? '',
            'quantity'        => 1,
            'unit_price'      => $price,
            'total_amount'    => $price,
            'currency'        => $currency,
            'payment_gateway' => 'moneroo',
            'payment_status'  => 'pending',
            'download_token'  => Str::random(32),
            'download_count'  => 0,
            'download_limit'  => 5,
            'download_expires_at' => now()->addDays(30),
            'metadata'        => [
                'source' => 'whatsapp_bot',
                'phone'  => $validated['customer_phone'] ?? null,
            ],
        ]);

        try {
            $moneroo = new MonerooService();
            $session = $moneroo->createPaymentSession([
                'customer_email' => $validated['customer_email'] ?? '',
                'customer_name'  => $validated['customer_name'] ?? 'Client WhatsApp',
                'order_id'       => $order->id,
                'order_number'   => $order->order_number,
            ], (float) $price, $currency);

            $paymentUrl = $session['data']['payment_url'] ?? ($session['payment_url'] ?? null);

            if (!$paymentUrl) {
                $paymentUrl = route('shop.checkout') . '?order=' . $order->order_number;
            }

            return response()->json([
                'order_id'    => $order->id,
                'order_number' => $order->order_number,
                'payment_url' => $paymentUrl,
                'amount'      => (float) $price,
                'currency'    => $currency,
            ]);
        } catch (\Exception $e) {
            $fallbackUrl = route('shop.checkout') . '?order=' . $order->order_number;

            return response()->json([
                'order_id'    => $order->id,
                'order_number' => $order->order_number,
                'payment_url' => $fallbackUrl,
                'amount'      => (float) $price,
                'currency'    => $currency,
                'note'        => 'Paiement direct via le site',
            ]);
        }
    }

    public function stats()
    {
        $totalOrders = Order::count();
        $paidOrders = Order::where('payment_status', 'paid')->count();
        $totalRevenue = Order::where('payment_status', 'paid')->sum('total_amount');
        $pendingOrders = Order::where('payment_status', 'pending')->count();

        $topProducts = Product::orderByDesc('sales_count')
            ->limit(5)
            ->get(['id', 'name', 'price', 'sales_count'])
            ->map(fn ($p) => [
                'name'        => $p->name,
                'price'       => (float) $p->price,
                'sales_count' => $p->sales_count,
            ]);

        $recentOrders = Order::latest()
            ->limit(10)
            ->get(['id', 'order_number', 'customer_name', 'payment_status', 'total_amount', 'created_at']);

        $todayRevenue = Order::where('payment_status', 'paid')
            ->whereDate('created_at', today())
            ->sum('total_amount');

        $monthRevenue = Order::where('payment_status', 'paid')
            ->whereMonth('created_at', now()->month)
            ->sum('total_amount');

        return response()->json([
            'total_orders'    => $totalOrders,
            'paid_orders'     => $paidOrders,
            'pending_orders'  => $pendingOrders,
            'total_revenue'   => (float) $totalRevenue,
            'today_revenue'   => (float) $todayRevenue,
            'month_revenue'   => (float) $monthRevenue,
            'top_products'    => $topProducts,
            'recent_orders'   => $recentOrders,
        ]);
    }

    public function orders(Request $request)
    {
        $limit = min((int) $request->query('limit', 20), 100);

        $orders = Order::latest()
            ->limit($limit)
            ->get(['id', 'order_number', 'customer_name', 'customer_email', 'payment_status', 'total_amount', 'currency', 'created_at']);

        return response()->json($orders);
    }
}

<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Mail\OrderConfirmationMail;
use App\Models\Order;
use App\Models\Product;
use App\Models\Pack;
use App\Services\MonerooService;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class CartController extends Controller
{
    protected function getCart(): array
    {
        return session()->get('cart', []);
    }

    protected function saveCart(array $cart): void
    {
        session(['cart' => $cart]);
    }

    protected function buildItems(array $cart): \Illuminate\Support\Collection
    {
        if (empty($cart)) {
            return collect();
        }

        $items = collect();

        foreach ($cart as $id => $data) {
            $type = $data['type'] ?? 'product';
            $quantity = max(1, (int) ($data['quantity'] ?? 1));

            if ($type === 'pack') {
                $pack = Pack::with('products')->where('is_active', true)->find($id);
                if ($pack) {
                    $unitPrice = (float) ($pack->discount_price ?? $pack->price);
                    $items->push([
                        'type'       => 'pack',
                        'pack'       => $pack,
                        'quantity'   => $quantity,
                        'unit_price' => $unitPrice,
                        'total'      => $unitPrice * $quantity,
                    ]);
                }
            } else {
                $product = Product::where('is_active', true)->find($id);
                if ($product) {
                    $unitPrice = (float) ($product->discount_price ?? $product->price);
                    $items->push([
                        'type'       => 'product',
                        'product'    => $product,
                        'quantity'   => $quantity,
                        'unit_price' => $unitPrice,
                        'total'      => $unitPrice * $quantity,
                    ]);
                }
            }
        }

        return $items;
    }

    public function index()
    {
        $items = $this->buildItems($this->getCart());
        $total = $items->sum('total');
        return view('shop.cart', compact('items', 'total'));
    }

    public function add(Request $request, Product $product)
    {
        abort_if(! $product->is_active, 404);

        $quantity = max(1, (int) $request->input('quantity', 1));
        $cart     = $this->getCart();

        $maxQty = $product->stock === -1 ? 99 : $product->stock;
        $cart[$product->id] = [
            'type' => 'product',
            'quantity' => min($maxQty, ($cart[$product->id]['quantity'] ?? 0) + $quantity),
        ];

        $this->saveCart($cart);

        return redirect()->route('shop.cart')->with('success', 'Produit ajouté au panier.');
    }

    public function addPack(Request $request, Pack $pack)
    {
        abort_if(! $pack->is_active, 404);

        $quantity = max(1, (int) $request->input('quantity', 1));
        $cart     = $this->getCart();

        $cart[$pack->id] = [
            'type' => 'pack',
            'quantity' => min(99, ($cart[$pack->id]['quantity'] ?? 0) + $quantity),
        ];

        $this->saveCart($cart);

        return redirect()->route('shop.cart')->with('success', 'Pack ajouté au panier.');
    }

    public function update(Request $request, Product $product)
    {
        $quantity = max(0, (int) $request->input('quantity', 0));
        $cart     = $this->getCart();

        if ($quantity <= 0) {
            unset($cart[$product->id]);
        } else {
            $maxQty = $product->stock === -1 ? 99 : $product->stock;
            $cart[$product->id]['quantity'] = min($maxQty, $quantity);
        }

        $this->saveCart($cart);

        return redirect()->route('shop.cart')->with('success', 'Panier mis à jour.');
    }

    public function updatePack(Request $request, Pack $pack)
    {
        $quantity = max(0, (int) $request->input('quantity', 0));
        $cart     = $this->getCart();

        if ($quantity <= 0) {
            unset($cart[$pack->id]);
        } else {
            $cart[$pack->id]['quantity'] = min(99, $quantity);
        }

        $this->saveCart($cart);

        return redirect()->route('shop.cart')->with('success', 'Panier mis à jour.');
    }

    public function remove(Product $product)
    {
        $cart = $this->getCart();
        unset($cart[$product->id]);
        $this->saveCart($cart);

        return redirect()->route('shop.cart')->with('success', 'Produit retiré du panier.');
    }

    public function removePack(Pack $pack)
    {
        $cart = $this->getCart();
        unset($cart[$pack->id]);
        $this->saveCart($cart);

        return redirect()->route('shop.cart')->with('success', 'Pack retiré du panier.');
    }

    public function checkout()
    {
        $cart = $this->getCart();

        if (empty($cart)) {
            return redirect()->route('shop.index')->with('info', 'Votre panier est vide.');
        }

        $items = $this->buildItems($cart);
        $total = $items->sum('total');

        return view('shop.checkout', compact('items', 'total'));
    }

    public function processCheckout(Request $request, MonerooService $moneroo)
    {
        $request->validate([
            'customer_name'  => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
        ]);

        $cart  = $this->getCart();
        $items = $this->buildItems($cart);

        if ($items->isEmpty()) {
            return redirect()->route('shop.index')->with('info', 'Votre panier est vide.');
        }

        $total    = $items->sum('total');
        $firstItem = $items->first();
        $currency = $firstItem['type'] === 'pack' 
            ? $firstItem['pack']->currency 
            : ($firstItem['product']->currency ?? 'XOF');

        $downloadToken = Str::random(48);

        $metadata = [
            'customer_email' => $request->customer_email,
            'customer_name'  => $request->customer_name,
            'download_token' => $downloadToken,
            'cart'           => $cart,
        ];

        try {
            $session = $moneroo->createPaymentSession($metadata, $total, $currency);
            session([
                'pending_download_token' => $downloadToken,
                'pending_cart' => $cart,
                'pending_customer_email' => $request->customer_email,
                'pending_customer_name'  => $request->customer_name,
            ]);
            $checkoutUrl = $session['data']['checkout_url']
                ?? $session['data']['payment_url']
                ?? $session['checkout_url']
                ?? $session['payment_url']
                ?? null;
            if (! $checkoutUrl) {
                throw new \RuntimeException('URL de paiement non reçue. Réponse: ' . json_encode($session));
            }
            return redirect()->away($checkoutUrl);
        } catch (ConnectionException $e) {
            return back()->with('error', 'Impossible de joindre le service de paiement Moneroo. Vérifiez votre clé API et votre connexion.');
        } catch (\RuntimeException $e) {
            return back()->with('error', 'Erreur de paiement : ' . $e->getMessage());
        }
    }

    public function success(Request $request)
    {
        $cart          = session()->get('pending_cart', []);
        $downloadToken = session()->get('pending_download_token');

        if (empty($cart) || empty($downloadToken)) {
            session()->forget('cart');
            return view('shop.checkout-success');
        }

        // Si le webhook a déjà créé les commandes, on les réutilise
        $existingOrders = Order::where('download_token', $downloadToken)
            ->orWhere('moneroo_transaction_id', $request->query('transaction_id'))
            ->orWhere('moneroo_transaction_id', $request->query('reference'))
            ->get();

        if ($existingOrders->isNotEmpty()) {
            session()->forget(['cart', 'pending_cart', 'pending_download_token', 'pending_customer_email', 'pending_customer_name']);
            return view('shop.checkout-success', ['orders' => $existingOrders]);
        }

        // Fallback : créer les commandes depuis la session (webhook non reçu)
        $items         = $this->buildItems($cart);
        $customerEmail = session()->get('pending_customer_email');
        $customerName  = session()->get('pending_customer_name');
        $firstItem     = $items->first();
        $currency      = $firstItem['type'] === 'pack' 
            ? $firstItem['pack']->currency 
            : ($firstItem['product']->currency ?? 'XOF');
        $txId          = $request->query('transaction_id') ?? $request->query('reference') ?? 'redirect-' . Str::random(8);

        $orders = collect();
        foreach ($items as $item) {
            if ($item['type'] === 'pack') {
                $pack = $item['pack'];
                $order = Order::create([
                    'orderable_id'           => $pack->id,
                    'orderable_type'         => Pack::class,
                    'customer_email'         => $customerEmail,
                    'customer_name'          => $customerName,
                    'quantity'               => $item['quantity'],
                    'unit_price'             => $item['unit_price'],
                    'total_amount'           => $item['total'],
                    'currency'               => $currency,
                    'payment_gateway'        => 'moneroo',
                    'payment_status'         => 'paid',
                    'moneroo_transaction_id' => $txId,
                    'download_token'         => Str::random(48),
                    'download_limit'         => 3,
                    'download_expires_at'    => now()->addDays(7),
                    'metadata'               => ['source' => 'redirect_fallback'],
                ]);
                $pack->increment('sales_count', $item['quantity']);
                $orders->push($order);
            } else {
                $product = $item['product'];
                $isFormation = $product->product_type === 'formation';
                $order   = Order::create([
                    'orderable_id'           => $product->id,
                    'orderable_type'         => Product::class,
                    'customer_email'         => $customerEmail,
                    'customer_name'          => $customerName,
                    'quantity'               => $item['quantity'],
                    'unit_price'             => $item['unit_price'],
                    'total_amount'           => $item['total'],
                    'currency'               => $currency,
                    'payment_gateway'        => 'moneroo',
                    'payment_status'         => 'paid',
                    'moneroo_transaction_id' => $txId,
                    'download_token'         => Str::random(48),
                    'download_limit'         => $isFormation ? 100 : 3,
                    'download_expires_at'    => now()->addDays($isFormation ? 365 : 7),
                    'metadata'               => ['source' => 'redirect_fallback'],
                ]);
                $product->increment('sales_count', $item['quantity']);
                $orders->push($order);
            }
        }

        if ($customerEmail && $orders->isNotEmpty()) {
            try {
                Mail::to($customerEmail)->send(new OrderConfirmationMail($orders->first()));
            } catch (\Throwable $e) {
                // échec silencieux
            }
        }

        session()->forget(['cart', 'pending_cart', 'pending_download_token', 'pending_customer_email', 'pending_customer_name']);

        return view('shop.checkout-success', ['orders' => $orders]);
    }

    public function cancel()
    {
        return redirect()->route('shop.checkout')->with('info', 'Paiement annulé. Votre panier est intact.');
    }
}

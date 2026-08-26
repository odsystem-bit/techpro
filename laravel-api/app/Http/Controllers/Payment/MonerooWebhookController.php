<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Mail\OrderConfirmationMail;
use App\Models\Order;
use App\Models\Product;
use App\Models\SiteSetting;
use App\Services\MonerooService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class MonerooWebhookController extends Controller
{
    public function handle(Request $request, MonerooService $monerooService)
    {
        $payload   = $request->getContent();
        $signature = $request->header('X-Moneroo-Signature');

        Log::info('Moneroo webhook reçu.', ['body' => json_decode($payload, true), 'headers' => $request->headers->all()]);

        // Si un secret est configuré, on valide la signature (non-bloquant)
        if ($monerooService->webhookSecretConfigured()) {
            if (! $monerooService->validateWebhookSignature($payload, $signature)) {
                Log::warning('Moneroo webhook: signature invalide (traitement continué).', ['ip' => $request->ip()]);
            }
        }

        $event = $request->input('event') ?? $request->input('type') ?? '';
        $successEvents = ['payment.success', 'payment.completed', 'payment.successful'];
        if (! in_array($event, $successEvents)) {
            Log::info('Moneroo webhook: event ignoré.', ['event' => $event]);
            return response()->json(['message' => 'Event ignored.'], 200);
        }

        $payment = $request->input('data', []);
        if (empty($payment) || ($payment['status'] ?? null) !== 'success') {
            return response()->json(['message' => 'Payment not successful.'], 422);
        }

        $metadataRaw   = $payment['metadata'] ?? [];
        $metadata      = collect($metadataRaw)->map(function ($v) {
            $decoded = json_decode($v, true);
            return json_last_error() === JSON_ERROR_NONE ? $decoded : $v;
        })->all();

        $customerEmail = $metadata['customer_email'] ?? $payment['customer_email'] ?? null;
        $customerName  = $metadata['customer_name'] ?? null;
        $cartData      = $metadata['cart'] ?? [];
        $downloadToken = $metadata['download_token'] ?? Str::random(48);
        $transactionId = $payment['id'] ?? $payment['transaction_id'] ?? null;

        if (! $customerEmail || empty($cartData)) {
            Log::warning('Moneroo webhook: métadonnées manquantes.', compact('metadata', 'payment'));
            return response()->json(['message' => 'Missing metadata.'], 422);
        }

        if ($transactionId && Order::where('moneroo_transaction_id', $transactionId)->exists()) {
            return response()->json(['message' => 'Already processed.'], 200);
        }

        $currency = strtoupper($payment['currency'] ?? 'XOF');
        $orders   = collect();

        foreach ($cartData as $itemId => $itemData) {
            if (! is_numeric($itemId)) {
                continue;
            }

            $qty = is_array($itemData) ? max(1, (int) ($itemData['quantity'] ?? 1)) : max(1, (int) $itemData);
            $type = is_array($itemData) ? ($itemData['type'] ?? 'product') : 'product';

            if ($type === 'pack') {
                $pack = \App\Models\Pack::find((int) $itemId);
                if (! $pack) {
                    continue;
                }
                $unitPrice = (float) ($pack->discount_price ?? $pack->price);
                $order = Order::create([
                    'orderable_id'           => $pack->id,
                    'orderable_type'         => \App\Models\Pack::class,
                    'customer_email'        => $customerEmail,
                    'customer_name'         => $customerName,
                    'quantity'              => $qty,
                    'unit_price'            => $unitPrice,
                    'total_amount'          => $unitPrice * $qty,
                    'currency'              => $currency,
                    'payment_gateway'       => 'moneroo',
                    'payment_status'        => 'paid',
                    'moneroo_transaction_id'=> $transactionId,
                    'download_token'        => Str::random(48),
                    'download_limit'        => 3,
                    'download_expires_at'   => now()->addDays(7),
                    'metadata'              => $metadata,
                ]);
                $pack->increment('sales_count', $qty);
                $orders->push($order);
            } else {
                $product = Product::find((int) $itemId);
                if (! $product) {
                    continue;
                }
                $unitPrice = (float) ($product->discount_price ?? $product->price);
                $isFormation = $product->product_type === 'formation';
                $order = Order::create([
                    'orderable_id'           => $product->id,
                    'orderable_type'         => Product::class,
                    'customer_email'        => $customerEmail,
                    'customer_name'         => $customerName,
                    'quantity'              => $qty,
                    'unit_price'            => $unitPrice,
                    'total_amount'          => $unitPrice * $qty,
                    'currency'              => $currency,
                    'payment_gateway'       => 'moneroo',
                    'payment_status'        => 'paid',
                    'moneroo_transaction_id'=> $transactionId,
                    'download_token'        => Str::random(48),
                    'download_limit'        => $isFormation ? 100 : 3,
                    'download_expires_at'   => now()->addDays($isFormation ? 365 : 7),
                    'metadata'              => $metadata,
                ]);
                $product->increment('sales_count', $qty);
                $orders->push($order);
            }
        }

        if ($orders->isEmpty()) {
            Log::error('Moneroo webhook: aucun produit valide dans le panier.', compact('cartData'));
            return response()->json(['message' => 'No valid products.'], 422);
        }

        try {
            Mail::to($customerEmail)->send(new OrderConfirmationMail($orders->first()));
        } catch (\Throwable $e) {
            Log::error('Moneroo webhook: échec envoi email.', ['error' => $e->getMessage()]);
        }

        // Meta Pixel Purchase event (server-side)
        foreach ($orders as $order) {
            $this->sendMetaPixelPurchase($order);
        }

        return response()->json(['message' => 'Orders recorded.', 'count' => $orders->count()], 201);
    }

    private function sendMetaPixelPurchase(Order $order): void
    {
        $pixelId = SiteSetting::get('meta_pixel_id', '');
        if (! $pixelId || $order->pixel_purchase_sent) {
            return;
        }

        $eventId = 'order_' . $order->id . '_' . $order->order_number;
        $contentId = $order->orderable_id;

        try {
            $metaToken = SiteSetting::get('meta_access_token', '');
            if (! $metaToken) {
                return;
            }

            $response = Http::post("https://graph.facebook.com/v19.0/{$pixelId}/events", [
                'data' => [
                    [
                        'event_name' => 'Purchase',
                        'event_time' => time(),
                        'event_id' => $eventId,
                        'action_source' => 'website',
                        'user_data' => [
                            'em' => hash('sha256', strtolower(trim($order->customer_email))),
                            'client_ip_address' => request()->ip(),
                            'client_user_agent' => request()->userAgent(),
                        ],
                        'custom_data' => [
                            'content_ids' => [$contentId],
                            'content_type' => 'product',
                            'value' => (float) $order->total_amount,
                            'currency' => $order->currency,
                        ],
                    ],
                ],
                'access_token' => $metaToken,
            ]);

            if ($response->successful()) {
                $order->update(['pixel_purchase_sent' => true]);
                Log::info('Meta Pixel Purchase event envoyé.', [
                    'order_id' => $order->id,
                    'event_id' => $eventId,
                    'response' => $response->json(),
                ]);
            } else {
                Log::error('Meta Pixel Purchase event échoué.', [
                    'order_id' => $order->id,
                    'event_id' => $eventId,
                    'response' => $response->body(),
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('Meta Pixel Purchase exception.', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}

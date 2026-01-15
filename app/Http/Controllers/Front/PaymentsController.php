<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Delivery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\StripeClient;
use Throwable;

class PaymentsController extends Controller
{
    /**
     * Show payment page
     */
    public function create(Order $order)
    {
        abort_if($order->user_id !== auth()->id(), 403);

        // If already paid, redirect to success
        if ($order->status === 'completed') {
            return redirect()->route('orders.success', $order)
                ->with('info', 'This order has already been paid.');
        }

        return view('front.payments.create', compact('order'));
    }

    /**
     * Create Stripe PaymentIntent (AJAX)
     */
    public function stripeIntent(Order $order)
    {
        try {
            abort_if($order->user_id !== auth()->id(), 403);

            if ($order->status === 'completed') {
                return response()->json(['error' => 'Order already paid'], 400);
            }

            $total = (float) $order->total;
            if ($total <= 0) {
                return response()->json(['error' => 'Invalid order total'], 400);
            }

            \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

            $intent = \Stripe\PaymentIntent::create([
                'amount' => (int) ($total * 100),
                'currency' => 'usd',
                'automatic_payment_methods' => ['enabled' => true],
                'metadata' => [
                    'order_id' => $order->id,
                    'user_id' => auth()->id(),
                ],
            ]);

            $payment = Payment::create([
                'order_id' => $order->id,
                'amount' => $total,
                'currency' => 'USD',
                'method' => 'stripe',
                'status' => 'pending',
                'transaction_id' => $intent->id,
                'transaction_data' => json_encode([
                    'client_secret' => $intent->client_secret,
                    'status' => $intent->status,
                ]),
            ]);

            Log::info('Payment intent created', [
                'order_id' => $order->id,
                'payment_id' => $payment->id,
                'intent_id' => $intent->id,
            ]);

            return response()->json(['clientSecret' => $intent->client_secret]);

        } catch (\Exception $e) {
            Log::error('Payment Error: ' . $e->getMessage(), ['order_id' => $order->id]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Stripe redirect confirmation
     */
    public function confirm(Request $request, Order $order)
    {
        // Make sure the authenticated user owns this order
        abort_if($order->user_id !== auth()->id(), 403);

        $paymentIntentId = $request->query('payment_intent');

        if (!$paymentIntentId) {
            return redirect()
                ->route('orders.payments.create', $order)
                ->with('error', 'Missing payment information.');
        }

        try {
            $stripe = new StripeClient(config('services.stripe.secret'));

            $paymentIntent = $stripe->paymentIntents->retrieve($paymentIntentId);

            // Find the local payment record
            $payment = Payment::where('transaction_id', $paymentIntent->id)
                ->where('order_id', $order->id)
                ->first();

            if (!$payment) {
                return redirect()
                    ->route('orders.payments.create', $order)
                    ->with('error', 'Payment record not found. Please contact support.');
            }

            if ($paymentIntent->status === 'succeeded') {

                $payment->update([
                    'status' => 'completed',
                    'transaction_data' => json_encode([
                        'payment_intent' => $paymentIntent,
                        'completed_at' => now()->toIso8601String(),
                    ]),
                ]);

                $order->update([
                    'status' => 'completed', 
                    'paid_at' => now(),
                ]);

                $this->createDeliveryForOrder($order);

                Log::info('Payment completed successfully', [
                    'order_id' => $order->id,
                    'payment_id' => $payment->id,
                ]);

                return redirect()
                    ->route('orders.success', $order)
                    ->with('success', 'Payment completed successfully!');
            }

            if (in_array($paymentIntent->status, ['requires_action', 'processing'])) {
                return redirect()
                    ->route('orders.payments.create', $order)
                    ->with('info', 'Payment is still processing. You will be notified when it is completed.');
            }

            // Handle failed payments
            if (in_array($paymentIntent->status, ['requires_payment_method', 'canceled'])) {

                $payment->update([
                    'status' => 'failed',
                    'transaction_data' => json_encode([
                        'payment_intent' => $paymentIntent,
                        'failed_at' => now()->toIso8601String(),
                    ]),
                ]);

                return redirect()
                    ->route('orders.payments.create', $order)
                    ->with('error', 'Payment failed. Please try again.');
            }

            // Fallback for other statuses
            return redirect()
                ->route('orders.payments.create', $order)
                ->with('info', 'Payment status: ' . $paymentIntent->status);

        } catch (Throwable $e) {

            Log::error('Stripe confirmation error', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);

            return redirect()
                ->route('orders.payments.create', $order)
                ->with('error', 'Payment verification failed.');
        }
    }

    /**
     * Payment success page
     */
    public function success(Order $order)
    {
        abort_if($order->user_id !== auth()->id(), 403);

        if ($order->status !== 'completed') {
            return redirect()
                ->route('orders.payments.create', $order)
                ->with('info', 'This order has not been paid yet.');
        }

        // Load the delivery relationship
        $order->load('delivery');

        return view('front.payments.success', compact('order'));
    }

    /**
     * Create delivery record for order
     * Business Logic: Automatically create delivery when payment succeeds
     * 
     * @param Order $order
     * @return Delivery|null
     */
    protected function createDeliveryForOrder(Order $order)
    {
        // Don't create duplicate delivery
        if ($order->delivery) {
            Log::info('Delivery already exists for order', [
                'order_id' => $order->id,
                'delivery_id' => $order->delivery->id,
            ]);
            return $order->delivery;
        }

        try {
            // Get store/warehouse location from config
            $warehouseLat = config('store.warehouse_latitude', 30.0444);
            $warehouseLng = config('store.warehouse_longitude', 31.2357);

            // Create delivery record
            $delivery = Delivery::create([
                'order_id' => $order->id,
                'status' => 'pending', // pending → assigned → picked_up → in_transit → delivered
                'latitude' => $warehouseLat,
                'longitude' => $warehouseLng,
            ]);

            Log::info('Delivery created for order', [
                'order_id' => $order->id,
                'delivery_id' => $delivery->id,
                'latitude' => $warehouseLat,
                'longitude' => $warehouseLng,
            ]);

            // Optional: Trigger event for admin notification
            // event(new OrderReadyForShipment($order, $delivery));

            return $delivery;

        } catch (\Exception $e) {
            Log::error('Failed to create delivery for order', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Optional: Geocode shipping address to get coordinates
     * Uses OpenStreetMap Nominatim API (free, no API key required)
     * 
     * @param object|null $address
     * @return array [latitude, longitude]
     */
    protected function geocodeAddress($address)
    {
        // Fallback to warehouse location
        $defaultLat = config('store.warehouse_latitude', 30.0444);
        $defaultLng = config('store.warehouse_longitude', 31.2357);

        if (!$address) {
            return [$defaultLat, $defaultLng];
        }

        try {
            // Build address query
            $addressParts = array_filter([
                $address->street_address ?? null,
                $address->city ?? null,
                $address->state ?? null,
                $address->country ?? null,
            ]);

            if (empty($addressParts)) {
                return [$defaultLat, $defaultLng];
            }

            $query = urlencode(implode(', ', $addressParts));
            
            // Call Nominatim API
            $url = "https://nominatim.openstreetmap.org/search?q={$query}&format=json&limit=1";
            
            $context = stream_context_create([
                'http' => [
                    'header' => 'User-Agent: YourStoreApp/1.0'
                ]
            ]);
            
            $response = @file_get_contents($url, false, $context);
            
            if ($response) {
                $data = json_decode($response, true);
                
                if (!empty($data[0]['lat']) && !empty($data[0]['lon'])) {
                    return [
                        (float) $data[0]['lat'],
                        (float) $data[0]['lon']
                    ];
                }
            }
        } catch (\Exception $e) {
            Log::warning('Geocoding failed, using default location', [
                'error' => $e->getMessage(),
                'address' => $address,
            ]);
        }

        // Return default warehouse location if geocoding fails
        return [$defaultLat, $defaultLng];
    }
}
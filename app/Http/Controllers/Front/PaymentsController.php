<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
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

        return view('front.payments.create', [
            'order' => $order,
        ]);
    }

    /**
     * Create Stripe PaymentIntent (AJAX)
     */
    public function stripeIntent(Order $order)
    {
        try {
            abort_if($order->user_id !== auth()->id(), 403);

            if ($order->status === 'paid') {
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
                'automatic_payment_methods' => [
                    'enabled' => true,
                ],
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

            return response()->json([
                'clientSecret' => $intent->client_secret,
            ]);

        } catch (\Exception $e) {
            Log::error('Payment Error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    } // ← This closes stripeIntent method

    /**
     * Stripe redirect confirmation
     */
    public function confirm(Request $request, Order $order)
    {
        abort_if($order->user_id !== auth()->id(), 403);

        $paymentIntentId = $request->query('payment_intent');

        if (!$paymentIntentId) {
            Log::warning('Missing payment intent in confirmation', [
                'order_id' => $order->id,
                'query_params' => $request->query(),
            ]);

            return redirect()
                ->route('orders.payments.create', $order)
                ->with('error', 'Missing payment information');
        }

        try {
            $stripe = new StripeClient(config('services.stripe.secret'));

            $paymentIntent = $stripe->paymentIntents->retrieve($paymentIntentId);

            $payment = Payment::where('transaction_id', $paymentIntent->id)
                ->where('order_id', $order->id)
                ->first();

            if (!$payment) {
                Log::error('Payment record not found', [
                    'order_id' => $order->id,
                    'intent_id' => $paymentIntent->id,
                ]);

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
                    'status' => 'paid',
                    'paid_at' => now(),
                ]);

                Log::info('Payment completed successfully', [
                    'order_id' => $order->id,
                    'payment_id' => $payment->id,
                ]);

                return redirect()
                    ->route('home')
                    ->with('success', 'Payment completed successfully!');
            }

            $statusMessage = match($paymentIntent->status) {
                'requires_payment_method' => 'Payment failed. Please try another payment method.',
                'requires_action' => 'Additional verification required.',
                'processing' => 'Payment is being processed.',
                'canceled' => 'Payment was canceled.',
                default => 'Payment could not be completed.',
            };

            if (in_array($paymentIntent->status, ['canceled', 'requires_payment_method'])) {
                $payment->update([
                    'status' => 'failed',
                    'transaction_data' => json_encode([
                        'payment_intent' => $paymentIntent,
                        'failed_at' => now()->toIso8601String(),
                    ]),
                ]);
            }

            return redirect()
                ->route('orders.payments.create', $order)
                ->with('error', $statusMessage);

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
} 
<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Webhook;
use Symfony\Component\HttpFoundation\Response;

class StripeWebhooksController extends Controller
{
    public function handle(Request $request)
    {
        $payload    = $request->getContent();
        $sigHeader  = $request->header('Stripe-Signature');
        $secret     = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent(
                $payload,
                $sigHeader,
                $secret
            );
        } catch (\UnexpectedValueException $e) {
            Log::error('Stripe webhook invalid payload');
            return response('Invalid payload', Response::HTTP_BAD_REQUEST);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            Log::error('Stripe webhook invalid signature');
            return response('Invalid signature', Response::HTTP_BAD_REQUEST);
        }

        Log::info('Stripe webhook received', [
            'type' => $event->type
        ]);

        switch ($event->type) {

            /**
             * ✅ PAYMENT SUCCESS
             */
            case 'payment_intent.succeeded':
                $paymentIntent = $event->data->object;
                $orderId = $paymentIntent->metadata->order_id ?? null;

                if ($orderId) {
                    Order::where('id', $orderId)->update([
                        'payment_status' => 'paid',
                        'status' => 'processing', // order workflow starts
                    ]);

                    Log::info('Order marked as PAID', [
                        'order_id' => $orderId,
                        'payment_intent' => $paymentIntent->id,
                    ]);
                }
                break;

            /**
             * ❌ PAYMENT FAILED
             */
            case 'payment_intent.payment_failed':
                $paymentIntent = $event->data->object;
                $orderId = $paymentIntent->metadata->order_id ?? null;

                if ($orderId) {
                    Order::where('id', $orderId)->update([
                        'payment_status' => 'failed',
                    ]);

                    Log::warning('Order payment FAILED', [
                        'order_id' => $orderId,
                    ]);
                }
                break;

            /**
             * (Optional) Checkout Session completed
             * Useful if you ever use Stripe Checkout
             */
            case 'checkout.session.completed':
                $session = $event->data->object;
                $orderId = $session->metadata->order_id ?? null;

                if ($orderId) {
                    Order::where('id', $orderId)->update([
                        'payment_status' => 'paid',
                        'status' => 'processing',
                    ]);

                    Log::info('Checkout session completed', [
                        'order_id' => $orderId,
                        'session_id' => $session->id,
                    ]);
                }
                break;

            default:
                Log::info('Stripe event ignored', [
                    'type' => $event->type
                ]);
        }

        return response('Webhook handled', Response::HTTP_OK);
    }
}

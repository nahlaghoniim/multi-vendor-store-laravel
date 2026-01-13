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
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $secret = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent(
                $payload,
                $sigHeader,
                $secret
            );
        } catch (\UnexpectedValueException $e) {
            Log::error('Stripe webhook invalid payload');
            return response('Invalid payload', 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            Log::error('Stripe webhook invalid signature');
            return response('Invalid signature', 400);
        }

        Log::info('Stripe webhook received', ['type' => $event->type]);

        switch ($event->type) {
            case 'payment_intent.succeeded':
                $paymentIntent = $event->data->object;

                $orderId = $paymentIntent->metadata->order_id ?? null;

                if ($orderId) {
                    Order::where('id', $orderId)->update([
                        'status' => 'paid',
                        'payment_intent_id' => $paymentIntent->id,
                    ]);
                }

                break;

            case 'payment_intent.payment_failed':
                $paymentIntent = $event->data->object;
                $orderId = $paymentIntent->metadata->order_id ?? null;

                if ($orderId) {
                    Order::where('id', $orderId)->update([
                        'status' => 'failed',
                    ]);
                }

                break;
        }

        return response('Webhook handled', Response::HTTP_OK);
    }
}

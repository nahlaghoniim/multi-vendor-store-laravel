<?php

namespace App\Http\Controllers\Api;

use App\Events\DeliveryLocationUpdated;
use App\Http\Controllers\Controller;
use App\Models\Delivery;
use Illuminate\Http\Request;

class DeliveriesController extends Controller
{
    /**
     * Get delivery details
     */
    public function show($id)
    {
        $delivery = Delivery::where('order_id', $id)->firstOrFail();

        return response()->json([
            'delivery' => [
                'id' => $delivery->id,
                'order_id' => $delivery->order_id,
                'lat' => $delivery->latitude,
                'lng' => $delivery->longitude,
                'status' => $delivery->status,
            ]
        ]);
    }

    /**
     * Create new delivery
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_id' => ['required', 'exists:orders,id'],
            'lng' => ['required', 'numeric', 'between:-180,180'],
            'lat' => ['required', 'numeric', 'between:-90,90'],
            'status' => ['nullable', 'in:in-progress,delivered'],
        ]);

        $delivery = Delivery::create([
            'order_id' => $validated['order_id'],
            'latitude' => $validated['lat'],
            'longitude' => $validated['lng'],
            'status' => $validated['status'] ?? 'in-progress',
        ]);

        return response()->json([
            'message' => 'Delivery created successfully',
            'delivery' => $delivery,
        ], 201);
    }

    /**
     * Update delivery location
     */
    public function update(Request $request, Delivery $delivery)
    {
        $validated = $request->validate([
            'lng' => ['required', 'numeric', 'between:-180,180'],
            'lat' => ['required', 'numeric', 'between:-90,90'],
            'status' => ['nullable', 'in:in-progress,delivered'],
        ]);

        // Update with new column names
        $delivery->update([
            'latitude' => $validated['lat'],
            'longitude' => $validated['lng'],
            'status' => $validated['status'] ?? $delivery->status,
        ]);

        // Fire event if you have it set up
   event(new DeliveryLocationUpdated($delivery, $validated['lat'], $validated['lng']));

        return response()->json([
            'message' => 'Location updated successfully',
            'delivery' => [
                'id' => $delivery->id,
                'order_id' => $delivery->order_id,
                'lat' => $delivery->latitude,
                'lng' => $delivery->longitude,
                'status' => $delivery->status,
            ]
        ]);
    }
}
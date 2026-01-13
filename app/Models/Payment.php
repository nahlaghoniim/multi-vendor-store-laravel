<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'order_id',
        'transaction_id',
        'amount',
        'currency',
            'method',      

        'status',
        'payment_method',
        'transaction_data',
    ];



    /**
     * Casts
     */
    protected $casts = [
        'amount' => 'integer',
        'transaction_data' => 'array',
    ];

    /**
     * Payment belongs to an order
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Helpers (optional but useful)
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }
}

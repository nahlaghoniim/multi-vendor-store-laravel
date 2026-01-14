<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_id', 
        'user_id', 
        'payment_method', 
        'status', 
        'payment_status',
        'total',
        'paid_at',
        'number', // Add this too if not already in migration
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'total' => 'decimal:2',
    ];

    // Relationships
    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withDefault([
            'name' => 'Guest Customer'
        ]);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'order_items', 'order_id', 'product_id', 'id', 'id')
            ->using(OrderItem::class)
            ->as('order_item')
            ->withPivot([
                'product_name', 'price', 'quantity', 'options',
            ]);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    public function addresses()
    {
        return $this->hasMany(OrderAddress::class);
    }

    public function billingAddress()
    {
        return $this->hasOne(OrderAddress::class, 'order_id', 'id')
            ->where('type', '=', 'billing');
    }

    public function shippingAddress()
    {
        return $this->hasOne(OrderAddress::class, 'order_id', 'id')
            ->where('type', '=', 'shipping');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    // Calculate total from order items
    public function calculateTotal()
    {
        return $this->items()->get()->sum(function ($item) {
            return $item->price * $item->quantity;
        });
    }

    // Get total (from database or calculate if not set)
    public function getTotal()
    {
        if ($this->total) {
            return $this->total;
        }
        
        return $this->calculateTotal();
    }

    // Accessor for easy access
    public function getTotalAmountAttribute()
    {
        return $this->getTotal();
    }

    // Update total in database
    public function updateTotal()
    {
        $this->update([
            'total' => $this->calculateTotal()
        ]);
        
        return $this->total;
    }

    // Check if order is paid
    public function isPaid()
    {
        return $this->status === 'paid' || $this->payment_status === 'paid';
    }
 public function delivery()
    {
        return $this->hasOne(Delivery::class);
    }
    protected static function booted()
    {
        static::creating(function(Order $order) {
            $order->number = Order::getNextOrderNumber();
            
            // Calculate total if not set
            if (!$order->total) {
                $order->total = 0; // Will be updated after items are added
            }
        });

        static::created(function(Order $order) {
            // Recalculate total after order items are created
            if ($order->items()->count() > 0) {
                $order->updateTotal();
            }
        });
    }

    public static function getNextOrderNumber()
    {
        $year = Carbon::now()->year;
        $number = Order::whereYear('created_at', $year)->max('number');
        if ($number) {
            return $number + 1;
        }
        return $year . '0001';
    }
}
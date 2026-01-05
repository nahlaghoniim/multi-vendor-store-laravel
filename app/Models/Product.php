<?php

namespace App\Models;

use App\Models\Scopes\StoreScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'image',
        'category_id',
        'store_id',
        'price',
        'compare_price',
        'status',
    ];

    protected $appends = ['image_url'];

    protected static function booted()
    {
        static::addGlobalScope(new StoreScope());

        static::creating(function ($product) {
            if (!$product->slug) {
                $product->slug = Str::slug($product->name);
            }
        });
    }

    /* ------------------------------
        Route Model Binding (SLUG)
    --------------------------------*/
    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function resolveRouteBinding($value, $field = null)
    {
        return static::withoutGlobalScope(StoreScope::class)
            ->where($field ?? $this->getRouteKeyName(), $value)
            ->firstOrFail();
    }

    /* ------------------------------
        Relationships
    --------------------------------*/
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    /* ------------------------------
        Scopes
    --------------------------------*/
    public function scopeActive(Builder $builder)
    {
        return $builder->where('status', 'active');
    }

    /* ------------------------------
        Accessors
    --------------------------------*/
    public function getImageUrlAttribute()
    {
        if (!$this->image) {
            return 'https://www.incathlab.com/images/products/default_product.png';
        }

        if (Str::startsWith($this->image, ['http://', 'https://'])) {
            return $this->image;
        }

        return asset('storage/' . $this->image);
    }
}

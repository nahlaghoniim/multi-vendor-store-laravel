@php
    $items = $items ?? collect();
    $total = $total ?? 0;
@endphp

<div class="cart-items">
    <a href="{{ route('cart.index') }}" class="main-btn">
        <i class="lni lni-cart"></i>
        <span class="total-items">{{ $items->count() }}</span>
    </a>

    <div class="shopping-item">
        <div class="dropdown-cart-header">
            <span>{{ $items->count() }} Items</span>
            <a href="{{ route('cart.index') }}">View Cart</a>
        </div>

        <ul class="shopping-list">
            @forelse($items as $item)
                @php $product = $item->product; @endphp

                @if($product)
                    <li style="display: flex; align-items: center; gap: 12px; padding: 12px 0; border-bottom: 1px solid #f0f0f0;">
                        <div style="flex-shrink: 0; width: 60px; height: 60px; overflow: hidden; border-radius: 6px; background: #f8f8f8;">
                            <a href="{{ route('products.show', $product->slug) }}" style="display: block; width: 100%; height: 100%;">
                                <img src="{{ $product->image_url }}" 
                                     alt="{{ $product->name }}"
                                     style="width: 100%; height: 100%; object-fit: cover;">
                            </a>
                        </div>

                        <div style="flex: 1; min-width: 0;">
                            <h4 style="font-size: 14px; margin: 0 0 5px 0; line-height: 1.4;">
                                <a href="{{ route('products.show', $product->slug) }}" 
                                   title="{{ $product->name }}"
                                   style="color: #333; text-decoration: none;">
                                    {{ Str::limit($product->name, 40) }}
                                </a>
                            </h4>

                            <p style="font-size: 13px; color: #666; margin: 0;">
    {{ $item->quantity }}x - 
    <span style="color: #0167F3; font-weight: 600;">
        {{ Currency::format($item->price ?? $product->price ?? 0) }}
    </span>
</p>

                        </div>
                    </li>
                @endif
            @empty
                <li style="text-align: center; padding: 20px 0;">
                    <p style="color: #999; margin: 0;">Your cart is empty</p>
                </li>
            @endforelse
        </ul>

        <div class="bottom">
            <div class="total">
                <span>Total</span>
                <span class="amount">{{ Currency::format($total) }}</span>
            </div>

            @if($items->count() > 0)
                <div class="button">
                    <a href="{{ route('checkout') }}" class="btn animate">
                        Checkout
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
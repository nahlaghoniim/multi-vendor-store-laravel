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
         @foreach($items as $item)
    @php $product = $item->product; @endphp

    @if($product)
        <li>
            <div class="cart-img-head">
                <a href="{{ route('products.show', $product->slug) }}">
                    <img src="{{ $product->image_url }}" alt="">
                </a>
            </div>

            <div class="content">
                <h4>
                    <a href="{{ route('products.show', $product->slug) }}">
                        {{ $product->name }}
                    </a>
                </h4>

                <p class="quantity">
                    {{ $item->quantity }}x -
                    <span class="amount">{{ $item->price }}</span>
                </p>
            </div>
        </li>
    @endif
@endforeach

        </ul>

        <div class="bottom">
    <div class="total">
        <span>Total</span>
        <span class="amount">{{ Currency::format($total) }}</span>
    </div>

    <div class="button">
        <a href="{{ route('checkout') }}"
           class="btn animate">
            Checkout
        </a>
    </div>
</div>
    </div>
</div>

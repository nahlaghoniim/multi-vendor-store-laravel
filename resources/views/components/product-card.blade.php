@props(['product'])

<article {{ $attributes->merge(['class' => 'single-product d-flex flex-column h-100 shadow rounded overflow-hidden']) }}>
    <!-- Product Image -->
    <div class="product-image position-relative">
        <img src="{{ $product->image_url }}" 
             alt="{{ $product->name }}" 
             class="w-100" 
             style="height: 250px; object-fit: cover;">

        @if ($product->new)
            <span class="position-absolute top-0 start-0 bg-danger text-white px-2 py-1 small rounded">New</span>
        @endif

        <div class="position-absolute bottom-2 end-2">
            <a href="{{ route('products.show', $product->slug) }}" 
               class="btn btn-sm btn-primary">
                <i class="lni lni-cart"></i> Add to Cart
            </a>
        </div>
    </div>

    <!-- Product Info -->
    <div class="product-info p-3 d-flex flex-column flex-grow-1">
        <header>
            <span class="category text-muted small">{{ $product->category->name }}</span>
            <h4 class="title text-truncate mt-1">
                <a href="{{ route('products.show', $product->slug) }}" class="text-dark">
                    {{ $product->name }}
                </a>
            </h4>
        </header>

        <!-- Reviews -->
        <ul class="review list-inline mt-2 mb-0 text-warning">
            @for ($i = 1; $i <= 5; $i++)
                <li class="list-inline-item">
                    <i class="lni lni-star{{ ($i <= $product->rating) ? '-filled' : '' }}"></i>
                </li>
            @endfor
            <li class="list-inline-item text-muted small">{{ $product->rating }} Review(s)</li>
        </ul>

        <!-- Price -->
        <div class="price mt-auto">
            <span class="fw-bold text-success">{{ Currency::format($product->price) }}</span>
            @if ($product->compare_price)
                <span class="text-muted text-decoration-line-through ms-2">
                    {{ Currency::format($product->compare_price) }}
                </span>
            @endif
        </div>
    </div>
</article>

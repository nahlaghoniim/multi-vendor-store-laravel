@extends('layouts.dashboard')

@section('breadcrumb')
    @parent
    <li class="breadcrumb-item active">Store Details</li>
@endsection

@section('title', 'Store Details')

@section('content')
<div class="container-fluid">
    <!-- Store Information Card -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ $store->name }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('dashboard.stores.edit', $store) }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-edit"></i> Edit Store
                        </a>
                        <a href="{{ route('dashboard.stores.index') }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            @if($store->logo_image)
                                <img src="{{ asset('storage/' . $store->logo_image) }}" alt="{{ $store->name }}" class="img-fluid img-thumbnail">
                            @else
                                <div class="bg-secondary text-white p-5 text-center">
                                    <i class="fas fa-store fa-4x"></i>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-9">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="150">Store Name:</th>
                                    <td>{{ $store->name }}</td>
                                </tr>
                                <tr>
                                    <th>Slug:</th>
                                    <td>{{ $store->slug }}</td>
                                </tr>
                                <tr>
                                    <th>Description:</th>
                                    <td>{{ $store->description ?? 'No description provided' }}</td>
                                </tr>
                                <tr>
                                    <th>Status:</th>
                                    <td>
                                        @if($store->status == 'active')
                                            <span class="badge badge-success">Active</span>
                                        @else
                                            <span class="badge badge-secondary">Inactive</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Created:</th>
                                    <td>{{ $store->created_at->format('M d, Y h:i A') }}</td>
                                </tr>
                                <tr>
                                    <th>Total Products:</th>
                                    <td>{{ $store->products->count() }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Products Section -->
    <div class="row mt-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Store Products</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#addProductModal">
                            <i class="fas fa-plus"></i> Add Product
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    @if($products->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th width="80">ID</th>
                                        <th>Image</th>
                                        <th>Name</th>
                                        <th>Price</th>
                                        <th>Stock</th>
                                        <th>Status</th>
                                        <th width="100">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($products as $product)
                                        <tr>
                                            <td>{{ $product->id }}</td>
                                            <td>
                                                @if($product->image)
                                                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" style="width: 50px; height: 50px; object-fit: cover;">
                                                @else
                                                    <div class="bg-secondary" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                                                        <i class="fas fa-image text-white"></i>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>{{ $product->name }}</td>
                                            <td>${{ number_format($product->price, 2) }}</td>
                                            <td>{{ $product->stock ?? rand(10, 100) }}</td>
                                            <td>
                                                @if($product->status == 'active')
                                                    <span class="badge badge-success">Active</span>
                                                @else
                                                    <span class="badge badge-secondary">Inactive</span>
                                                @endif
                                            </td>
                                            <td>
                                                <form action="{{ route('dashboard.stores.removeProduct', [$store, $product]) }}" method="POST" class="d-inline" onsubmit="return confirm('Remove this product from the store?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" title="Remove from Store">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3">
                            {{ $products->links() }}
                        </div>
                    @else
                        <div class="alert alert-info text-center">
                            <i class="fas fa-info-circle"></i> No products in this store yet. Click "Add Product" to get started.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Product Modal -->
<div class="modal fade" id="addProductModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('dashboard.stores.addProduct', $store) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add Product to Store</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="product_id">Select Product</label>
                        <select name="product_id" id="product_id" class="form-control" required>
                            <option value="">-- Choose Product --</option>
                            @foreach(\App\Models\Product::whereNull('store_id')->orWhere('store_id', '!=', $store->id)->get() as $product)
                                <option value="{{ $product->id }}">{{ $product->name }} (ID: {{ $product->id }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Product</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
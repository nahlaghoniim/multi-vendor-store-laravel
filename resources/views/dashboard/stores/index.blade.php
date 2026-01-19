@extends('layouts.dashboard')

@section('title', 'Stores')

@section('breadcrumb')
    @parent
    <li class="breadcrumb-item active">Stores</li>
@endsection

@section('content')

<div class="mb-5">
    <a href="{{ route('dashboard.stores.create') }}" class="btn btn-sm btn-outline-primary mr-2">Create</a>
</div>

<x-alert type="success" />
<x-alert type="info" />

<form action="{{ URL::current() }}" method="get" class="d-flex justify-content-between mb-4">
    <x-form.input name="name" placeholder="Name" class="mx-2" :value="request('name')" />
    <select name="status" class="form-control mx-2">
        <option value="">All</option>
        <option value="active" @selected(request('status') == 'active')>Active</option>
        <option value="inactive" @selected(request('status') == 'inactive')>Inactive</option>
    </select>
    <button class="btn btn-dark mx-2">Filter</button>
</form>

<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Slug</th>
            <th>Products</th>
            <th>Status</th>
            <th>Created At</th>
            <th colspan="2"></th>
        </tr>
    </thead>
    <tbody>
        @forelse($stores as $store)
        <tr>
            <td>{{ $store->id }}</td>
            <td>
                <a href="{{ route('dashboard.products.index', ['store_id' => $store->id]) }}" class="font-weight-bold">
                    {{ $store->name }}
                </a>
            </td>
            <td>{{ $store->slug }}</td>
            <td>{{ $store->products->count() }}</td>
            <td>{{ $store->status }}</td>
            <td>{{ $store->created_at->format('Y-m-d') }}</td>
            <td>
                <a href="{{ route('dashboard.stores.edit', $store) }}" class="btn btn-sm btn-outline-success">Edit</a>
            </td>
            <td>
                <form action="{{ route('dashboard.stores.destroy', $store) }}" method="post" onsubmit="return confirm('Are you sure?');">
                    @csrf
                    @method('delete')
                    <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                </form>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="8">No stores defined.</td>
        </tr>
        @endforelse
    </tbody>
</table>

{{ $stores->withQueryString()->links() }}

@endsection
@extends('layouts.dashboard')

@section('title', 'Categories')

@section('breadcrumb')
    @parent
    <li class="breadcrumb-item active">Categories</li>
@endsection

@section('content')

<div class="d-flex justify-content-end align-items-center mb-4">
    <div>
        <a href="{{ route('dashboard.categories.create') }}" class="btn btn-sm btn-primary me-2">
            + Create
        </a>
        <a href="{{ route('dashboard.categories.trash') }}" class="btn btn-sm btn-outline-secondary">
            Trash
        </a>
    </div>
</div>

{{-- Alerts --}}
<x-alert type="success" />
<x-alert type="info" />

{{-- Filter --}}
<form action="{{ url()->current() }}" method="get" class="card card-body mb-4">
    <div class="row g-3 align-items-end">
        <div class="col-md-4">
            <label class="form-label">Name</label>
            <x-form.input name="name" :value="request('name')" />
        </div>

        <div class="col-md-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
                <option value="">All</option>
                <option value="active" @selected(request('status') === 'active')>
                    Active
                </option>
                <option value="archived" @selected(request('status') === 'archived')>
                    Archived
                </option>
            </select>
        </div>

        <div class="col-md-2">
            <button class="btn btn-dark w-100">
                Filter
            </button>
        </div>
    </div>
</form>

{{-- Table --}}
<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th width="60"></th>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Parent</th>
                    <th>Products</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>

            <tbody>
                @forelse($categories as $category)
                    <tr>
                        <td>
                            @if($category->image)
                                <img
                                    src="{{ asset('storage/' . $category->image) }}"
                                    class="rounded border"
                                    width="45"
                                    height="45"
                                    style="object-fit: cover"
                                    alt="{{ $category->name }}"
                                >
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>

                        <td>{{ $category->id }}</td>

                        <td>
                            <a href="{{ route('dashboard.categories.show', $category->id) }}"
                               class="fw-semibold text-decoration-none">
                                {{ $category->name }}
                            </a>
                        </td>

                        <td class="text-muted">
                            {{ $category->parent?->name ?? '—' }}
                        </td>

                        <td>
                            <span class="badge bg-info">
                                {{ $category->products_number ?? 0 }}
                            </span>
                        </td>

                        <td>
                            @if($category->status === 'active')
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-secondary">Archived</span>
                            @endif
                        </td>

                        <td class="text-muted">
                            {{ $category->created_at->format('Y-m-d') }}
                        </td>

                        <td class="text-end">
                            <div class="d-inline-flex gap-1">
                                <a href="{{ route('dashboard.categories.edit', $category->id) }}"
                                   class="btn btn-sm btn-outline-success">
                                    Edit
                                </a>

                                <form action="{{ route('dashboard.categories.destroy', $category->id) }}"
                                      method="POST"
                                      onsubmit="return confirm('Are you sure?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            No categories found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Pagination --}}
@if($categories instanceof \Illuminate\Pagination\AbstractPaginator)
    <div class="mt-4 d-flex justify-content-center">
        {{ $categories->withQueryString()->links() }}
    </div>
@endif

@endsection

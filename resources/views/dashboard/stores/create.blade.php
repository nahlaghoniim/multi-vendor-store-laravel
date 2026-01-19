@extends('layouts.dashboard')

@section('title', 'Create Store')

@section('breadcrumb')
    @parent
    <li class="breadcrumb-item"><a href="{{ route('dashboard.stores.index') }}">Stores</a></li>
    <li class="breadcrumb-item active">Create</li>
@endsection

@section('content')

<form action="{{ route('dashboard.stores.store') }}" method="post" enctype="multipart/form-data">
    @csrf

    <div class="form-group">
        <label for="name">Store Name</label>
        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" 
               value="{{ old('name') }}" required>
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="slug">Store Slug</label>
        <input type="text" name="slug" id="slug" class="form-control @error('slug') is-invalid @enderror" 
               value="{{ old('slug') }}">
        @error('slug')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="description">Description</label>
        <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" 
                  rows="5">{{ old('description') }}</textarea>
        @error('description')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="logo">Store Logo</label>
        <input type="file" name="logo" id="logo" class="form-control-file @error('logo') is-invalid @enderror" 
               accept="image/*">
        @error('logo')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="status">Status</label>
        <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" required>
            <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
        </select>
        @error('status')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <button type="submit" class="btn btn-primary">Create Store</button>
        <a href="{{ route('dashboard.stores.index') }}" class="btn btn-secondary">Cancel</a>
    </div>

</form>

@endsection
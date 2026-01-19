@php
// Ensure $product exists for create form
$product = $product ?? new \App\Models\Product();
$tags = $tags ?? '';
@endphp

<div class="form-group">
    <x-form.input label="Product Name" class="form-control-lg" name="name" :value="old('name', $product->name)" />
</div>

<div class="form-group">
    <label for="">Category</label>
    <select name="category_id" class="form-control form-select">
        <option value="">Primary Category</option>
        @foreach($categories ?? \App\Models\Category::all() as $category)
            <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id) == $category->id)>
                {{ $category->name }}
            </option>
        @endforeach
    </select>
</div>

<div class="form-group">
    <label for="">Store</label>
    <select name="store_id" class="form-control form-select">
        <option value="">Select Store</option>
        @foreach($stores ?? \App\Models\Store::all() as $store)
            <option value="{{ $store->id }}" @selected(old('store_id', $product->store_id) == $store->id)>
                {{ $store->name }}
            </option>
        @endforeach
    </select>
</div>

<div class="form-group">
    <label for="">Description</label>
    <x-form.textarea name="description">{{ old('description', $product->description) }}</x-form.textarea>
</div>

<div class="form-group">
    <x-form.label id="image">Image</x-form.label>
    <x-form.input type="file" name="image" accept="image/*" />
    @if($product->image)
        <img src="{{ asset('storage/' . $product->image) }}" alt="" height="60" class="mt-2">
    @endif
</div>

<div class="form-group">
    <x-form.input label="Price" name="price" :value="old('price', $product->price)" />
</div>

<div class="form-group">
    <x-form.input label="Compare Price" name="compare_price" :value="old('compare_price', $product->compare_price)" />
</div>

<div class="form-group">
    <x-form.input label="Tags" name="tags" :value="old('tags', $tags)" />
</div>

<div class="form-group">
    <label for="">Status</label>
    <div>
        <x-form.radio name="status" :checked="old('status', $product->status)" :options="['active' => 'Active', 'draft' => 'Draft', 'archived' => 'Archived']" />
    </div>
</div>

<div class="form-group mt-3">
    <button type="submit" class="btn btn-primary">{{ $button_label ?? 'Save' }}</button>
</div>

@push('styles')
<link href="{{ asset('css/tagify.css') }}" rel="stylesheet" type="text/css" />
@endpush

@push('scripts')
<script src="{{ asset('js/tagify.min.js') }}"></script>
<script src="{{ asset('js/tagify.polyfills.min.js') }}"></script>
<script>
    var inputElm = document.querySelector('[name=tags]');
    new Tagify(inputElm);
</script>
@endpush

<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductsController extends Controller
{
    public function index()
    {
        $this->authorize('view-any', Product::class);

        $products = Product::with(['category', 'store'])->paginate();

        return view('dashboard.products.index', compact('products'));
    }

    public function create()
    {
        $this->authorize('create', Product::class);

        return view('dashboard.products.create');
    }

public function store(Request $request)
{
    $this->authorize('create', Product::class);

    $data = $request->except('tags');

    if ($request->hasFile('image')) {
        $data['image'] = $request->file('image')->store('uploads', 'public');
    }

    $product = Product::create($data);

    if ($request->filled('tags')) {
        $this->syncTags($product, $request->tags);
    }

    return redirect()->route('dashboard.products.index')
        ->with('success', 'Product created successfully');
}

public function update(Request $request, Product $product)
{
    $this->authorize('update', $product);

    $data = $request->except('tags');

    if ($request->hasFile('image')) {
        $data['image'] = $request->file('image')->store('uploads', 'public');
    }

    $product->update($data);

    if ($request->filled('tags')) {
        $this->syncTags($product, $request->tags);
    }

    return redirect()->route('dashboard.products.index')
        ->with('success', 'Product updated successfully');
}




    public function edit(Product $product)
    {
        $this->authorize('update', $product);

        $tags = $product->tags()->pluck('name')->implode(',');

        return view('dashboard.products.edit', compact('product', 'tags'));
    }

   

    public function destroy(Product $product)
    {
        $this->authorize('delete', $product);

        $product->delete();

        return redirect()
            ->route('dashboard.products.index')
            ->with('success', 'Product deleted successfully');
    }

    /* ------------------------------
        Helper: Sync Tags
    --------------------------------*/
    private function syncTags(Product $product, $tagsJson)
    {
        $tags = json_decode($tagsJson);
        $tagIds = [];

        foreach ($tags as $item) {
            $slug = Str::slug($item->value);

            $tag = Tag::firstOrCreate(
                ['slug' => $slug],
                ['name' => $item->value]
            );

            $tagIds[] = $tag->id;
        }

        $product->tags()->sync($tagIds);
    }
     protected function uploadImage(Request $request): string
    {
        return $request->file('image')->store('products', 'public');
    }
}

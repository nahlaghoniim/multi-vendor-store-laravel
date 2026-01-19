<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StoreController extends Controller
{
    public function index(Request $request)
    {
        $stores = Store::query();

        if ($request->filled('name')) {
            $stores->where('name', 'like', "%{$request->name}%");
        }
        if ($request->filled('status')) {
            $stores->where('status', $request->status);
        }

        $stores = $stores->orderBy('id', 'desc')->paginate(10);

        return view('dashboard.stores.index', compact('stores'));
    }

    public function create()
    {
        return view('dashboard.stores.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|unique:stores,slug',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->only('name', 'slug', 'description', 'status');
        
        // Handle logo upload
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('stores/logos', 'public');
            $data['logo_image'] = $logoPath;
        }

        Store::create($data);

        return redirect()->route('dashboard.stores.index')->with('success', 'Store created successfully');
    }

    public function show(Store $store)
    {
        // Get products for this store with pagination
        $products = $store->products()->paginate(15);
        
        return view('dashboard.stores.show', compact('store', 'products'));
    }

    public function edit(Store $store)
    {
        return view('dashboard.stores.edit', compact('store'));
    }

    public function update(Request $request, Store $store)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|unique:stores,slug,' . $store->id,
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->only('name', 'slug', 'description', 'status');
        
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($store->logo_image && Storage::disk('public')->exists($store->logo_image)) {
                Storage::disk('public')->delete($store->logo_image);
            }
            $logoPath = $request->file('logo')->store('stores/logos', 'public');
            $data['logo_image'] = $logoPath;
        }

        $store->update($data);

        return redirect()->route('dashboard.stores.index')->with('success', 'Store updated successfully');
    }

    public function destroy(Store $store)
    {
        // Delete logo if exists
        if ($store->logo_image && Storage::disk('public')->exists($store->logo_image)) {
            Storage::disk('public')->delete($store->logo_image);
        }

        $store->delete();
        
        return redirect()->route('dashboard.stores.index')->with('success', 'Store deleted successfully');
    }

    // Method to add products to store
    public function addProduct(Request $request, Store $store)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $product = Product::findOrFail($request->product_id);
        $product->store_id = $store->id;
        $product->save();

        return redirect()->route('dashboard.stores.show', $store)
            ->with('success', 'Product added to store successfully');
    }

    // Method to remove product from store
    public function removeProduct(Store $store, Product $product)
    {
        if ($product->store_id == $store->id) {
            $product->store_id = null;
            $product->save();
            
            return redirect()->route('dashboard.stores.show', $store)
                ->with('success', 'Product removed from store successfully');
        }

        return redirect()->route('dashboard.stores.show', $store)
            ->with('error', 'Product does not belong to this store');
    }
}
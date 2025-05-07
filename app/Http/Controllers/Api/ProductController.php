<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Models\Store; // Import the Store model
use Illuminate\Foundation\Auth\Access\AuthorizesRequests; // Import the trait

class ProductController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index($storeId)
    {
        $store = Store::findOrFail($storeId);

        $this->authorize('manageProducts', $store);

        $products = $store->products;
        return response()->json($products);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductRequest $request, $storeId)
    {
        $store = Store::findOrFail($storeId); // Find the store

        // Authorize the user to manage products for this store
        $this->authorize('manageProducts', $store);

        $product = Product::create([
            'store_id' => $storeId,
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
        ]);

        return response()->json($product, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        $this->authorize('viewProduct', $product);

        return response()->json($product);
    }

    public function update(ProductRequest $request, Product $product)
    {
        // Authorize the user to manage this specific product
        $this->authorize('manageProducts', $product); // Assuming 'manageProducts' policy method handles updating products as well

        $product->update($request->validated());

        return response()->json($product);
    }

    public function destroy(Product $product)
    {
        $this->authorize('manageProducts', $product); // Assuming 'manageProducts' policy method handles deleting products as well

        $product->delete();
        return response()->json(null, 204);
    }
}

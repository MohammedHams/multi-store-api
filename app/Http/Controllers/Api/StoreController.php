<?php

namespace App\Http\Controllers\Api;

use App\Models\Store;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class StoreController extends Controller
{
    public function index()
    {
        if (!auth()->user()->can('viewAny', Store::class)) {
            return response()->json("لا يمكنك الوصول لهذه الصفحة");
        }

        $stores = Store::all();
        return response()->json($stores);
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'subdomain' => 'required|unique:stores',
            'email' => 'required|email|unique:stores',
            'phone' => 'required',
        ]);

        $store = Store::create($validated);
        return response()->json($store, 201);
    }

    public function show(Store $store)
    {
        return response()->json($store);
    }

    public function update(Request $request, Store $store)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string',
            'subdomain' => 'sometimes|unique:stores,subdomain,' . $store->id,
            'email' => 'sometimes|email|unique:stores,email,' . $store->id,
            'phone' => 'sometimes',
        ]);

        $store->update($validated);
        return response()->json($store);
    }

    public function destroy(Store $store)
    {
        $store->delete();
        return response()->json(null, 204);
    }
}

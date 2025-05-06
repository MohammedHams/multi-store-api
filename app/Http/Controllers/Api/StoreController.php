<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;


class StoreController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
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

        $user = auth()->user();
        if ($user->type == User::SUPER_ADMIN || $user->store_id == $store->id) {
            return response()->json($store);
        }

        return response()->json(['message' => 'Unauthorized'], 403);
    }    public function update(Request $request, Store $store)
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

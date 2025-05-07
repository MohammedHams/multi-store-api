<?php

namespace App\Http\Controllers\Api;

use App\Models\Store;
use App\Models\User;
use App\Models\StaffPermission;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StaffPermissionsController extends Controller
{
    public function index(Store $store, User $user)
    {
        if ($user->store_id !== $store->id) {
            return response()->json(['message' => 'User not found in this store'], 404);
        }

        return response()->json([
            'permissions' => $user->staffPermissions
        ]);
    }

    public function update(Request $request, Store $store, User $user, StaffPermission $permission)
    {
        if ($permission->user_id !== $user->id || $user->store_id !== $store->id) {
            return response()->json(['message' => 'Permission not found'], 404);
        }

        $request->validate([
            'manage_orders' => 'sometimes|boolean',
            'manage_products' => 'sometimes|boolean',
            'manage_settings' => 'sometimes|boolean'
        ]);

        $permission->update($request->all());

        return response()->json([
            'message' => 'Permissions updated successfully',
            'permissions' => $permission
        ]);
    }

    public function destroy(Store $store, User $user, StaffPermission $permission)
    {
        if ($permission->user_id !== $user->id || $user->store_id !== $store->id) {
            return response()->json(['message' => 'Permission not found'], 404);
        }

        $permission->delete();

        return response()->json(null, 204);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Models\StaffPermission;
use App\Http\Controllers\Controller;
use App\Http\Requests\StaffPermissionRequest;

class StaffPermissionsController extends Controller
{
    public function index($storeId, $userId)
    {
        $permissions = StaffPermission::where('store_id', $storeId)
            ->where('user_id', $userId)
            ->firstOrFail();

        return response()->json($permissions);
    }

    public function update(StaffPermissionRequest $request, $storeId, $userId)
    {
        $permissions = StaffPermission::where('store_id', $storeId)
            ->where('user_id', $userId)
            
            ->firstOrFail();

        $permissions->update($request->validated());

        return response()->json($permissions);
    }

    public function destroy($storeId, $userId)
    {
        StaffPermission::where('store_id', $storeId)
            ->where('user_id', $userId)
            ->delete();

        return response()->json(null, 204);
    }
}

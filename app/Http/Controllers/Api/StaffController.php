<?php

namespace App\Http\Controllers\Api;
use App\Models\User;
use App\Models\Store;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\StaffAccountCreated;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class StaffController extends Controller
{
    use AuthorizesRequests;
    public function index(Store $store)
    {
        $this->authorize('manageStaff', $store);

        $staff = $store->staff()
            ->with('staffPermissions')
            ->get();

        return response()->json($staff);
    }
    public function show(Store $store, User $user)
    {
        // Optionally, check if $user actually belongs to $store
        if ($user->store_id !== $store->id) {
            return response()->json(['message' => 'User not found in this store'], 404);
        }

        return response()->json($user);
    }


    public function store(Request $request, Store $store)
    {
        $this->authorize('manageStaff', $store);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users')->where(function ($query) use ($store) {
                    return $query->where('store_id', $store->id);
                })
            ],
            'phone' => 'required|string|unique:users,phone,NULL,id,store_id,'.$store->id,
            'manage_orders' => 'sometimes|boolean',
            'manage_products' => 'sometimes|boolean',
            'manage_settings' => 'sometimes|boolean'
        ]);

        $password = Str::random(12);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($password),
            'type' => 'staff', // Make sure 'type' is a fillable field in your User model
            'store_id' => $store->id,
            'email_verified_at' => null
        ]);
        $user->staffPermissions()->create([
            'store_id' => $store->id,
            'manage_orders' => $request->manage_orders ?? false,
            'manage_products' => $request->manage_products ?? false,
            'manage_settings' => $request->manage_settings ?? false
        ]);

        $this->sendEmailVerificationNotification($user, $password, $store);

        return response()->json([
            'message' => 'تم إنشاء حساب الموظف بنجاح',
            'staff' => $user->load('staffPermissions')
        ], 201);
    }
    protected function sendEmailVerificationNotification(User $user, string $password, Store $store)
    {
        $verificationUrl = url()->temporarySignedRoute(
            'verification.verify',
            now()->addDays(3),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        Mail::to($user->email)->send(new StaffAccountCreated(
            $user,
            $password,
            $store,
            $verificationUrl
        ));
    }

    public function update(Request $request, Store $store, User $staff)
    {
        $this->authorize('manageStaff', $store);

        $request->validate([
            'manage_orders' => 'sometimes|boolean',
            'manage_products' => 'sometimes|boolean',
            'manage_settings' => 'sometimes|boolean'
        ]);

        $staff->staffPermissions()->update($request->only([
            'manage_orders',
            'manage_products',
            'manage_settings'
        ]));

        return response()->json([
            'message' => 'تم تحديث صلاحيات الموظف',
            'staff' => $staff->load('staffPermissions')
        ]);
    }

    /**
     * حذف موظف
     */
    public function destroy(Store $store, User $staff)
    {
        $this->authorize('manageStaff', $store);

        $staff->staffPermissions()->delete();
        $staff->delete();

        return response()->json(null, 204);
    }
}

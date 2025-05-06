<?php

namespace App\Policies;

use App\Models\Store;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class StorePolicy
{
    use HandlesAuthorization;


    protected function isAdmin(User $user): bool
    {
        return in_array($user->type, ['super_admin', 'admin']);
    }

    public function viewAny(User $user): bool
    {
        return $this->isAdmin($user) || $user->type === 'store_owner';
    }

    public function manageOrders(User $user): bool
    {
        if ($this->isAdmin($user)) {
            return true;
        }

        return $user->type === 'store_owner' ||
            ($user->type === 'staff' && $user->staffPermissions->manage_orders);
    }

    public function manageProducts(User $user): bool
    {
        if ($this->isAdmin($user)) {
            return true;
        }

        return $user->type === 'store_owner' ||
            ($user->type === 'staff' && $user->staffPermissions->manage_products);
    }

    public function manageSettings(User $user): bool
    {
        if ($this->isAdmin($user)) {
            return true;
        }

        return $user->type === 'store_owner' ||
            ($user->type === 'staff' && $user->staffPermissions->manage_settings);
    }

    public function manageStaff(User $user, Store $store): bool
    {
        if ($this->isAdmin($user)) {
            return true;
        }

        return $user->type === 'store_owner' && $user->store_id === $store->id;
    }
}

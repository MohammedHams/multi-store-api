<?php

namespace App\Policies;

use App\Models\Store;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\Models\Product; // Import Product model
use App\Models\Order; // Import Order model

class StorePolicy
{
    use HandlesAuthorization;

    protected function isAdmin(User $user): bool
    {
        return in_array($user->type, [User::SUPER_ADMIN, 'admin']);
    }

    public function viewAny(User $user): bool
    {
        return $this->isAdmin($user) || $user->type === User::STORE_OWNER;
    }

    public function view(User $user, Store $store): bool
    {
        if ($this->isAdmin($user)) {
            return true;
        }

        return $user->type === User::STORE_OWNER && $user->store_id === $store->id;
    }

    public function manageOrders(User $user, Store $store): bool // Add Store parameter
    {
        if ($this->isAdmin($user)) {
            return true;
        }

        return ($user->type === User::STORE_OWNER && $user->store_id === $store->id) || // Add store ID check for store owners
            ($user->type === User::STAFF && $user->store_id === $store->id && $user->staffPermissions->manage_orders); // Add store ID check for staff
    }

    public function manageProducts(User $user, Store $store): bool // Add Store parameter
    {
        if ($this->isAdmin($user)) {
            return true;
        }

        return ($user->type === User::STORE_OWNER && $user->store_id === $store->id) || // Add store ID check for store owners
            ($user->type === User::STAFF && $user->store_id === $store->id && $user->staffPermissions->manage_products); // Add store ID check for staff
    }

    public function viewProduct(User $user, Product $product): bool
    {
        if ($this->isAdmin($user)) {
            return true;
        }

        return ($user->type === User::STORE_OWNER && $user->store_id === $product->store_id) ||
            ($user->type === User::STAFF && $user->staffPermissions->manage_products && $user->store_id === $product->store_id);
    }

    public function viewOrder(User $user, Order $order): bool
    {
        if ($this->isAdmin($user)) {
            return true;
        }

        return ($user->type === User::STORE_OWNER && $user->store_id === $order->store_id) ||
            ($user->type === User::STAFF && $user->staffPermissions->manage_orders && $user->store_id === $order->store_id);
    }


    public function manageSettings(User $user, Store $store): bool // Add Store parameter
    {
        if ($this->isAdmin($user)) {
            return true;
        }

        return ($user->type === User::STORE_OWNER && $user->store_id === $store->id) || // Add store ID check for store owners
            ($user->type === User::STAFF && $user->store_id === $store->id && $user->staffPermissions->manage_settings); // Add store ID check for staff
    }

    public function manageStaff(User $user, Store $store): bool
    {
        if ($this->isAdmin($user)) {
            return true;
        }

        return $user->type === User::STORE_OWNER && $user->store_id === $store->id;
    }
}

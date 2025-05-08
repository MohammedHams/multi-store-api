<?php
// StorePolicy.php - Ensure consistent method naming

namespace App\Policies;

use App\Models\Store;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\Models\Product;
use App\Models\Order;

class StorePolicy
{
    use HandlesAuthorization;

    protected function isAdmin(User $user): bool
    {
        return in_array($user->type, [User::SUPER_ADMIN, 'admin']);
    }



    public function manageOrders(User $user, Store $store): bool
    {
        if ($this->isAdmin($user)) {
            return true;
        }

        return ($user->type === User::STORE_OWNER && $user->store_id === $store->id) ||
            ($user->type === User::STAFF && $user->store_id === $store->id && $user->staffPermissions->manage_orders);
    }

    public function manageProducts(User $user, Store $store): bool
    {
        if ($this->isAdmin($user)) {
            return true;
        }

        return ($user->type === User::STORE_OWNER && $user->store_id === $store->id) ||
            ($user->type === User::STAFF && $user->store_id === $store->id && $user->staffPermissions->manage_products);
    }

    public function manageStaff(User $user, Store $store): bool
    {
        if ($this->isAdmin($user)) {
            return true;
        }

        return $user->type === User::STORE_OWNER && $user->store_id === $store->id;
    }}

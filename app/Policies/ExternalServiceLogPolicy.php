<?php

namespace App\Policies;

use App\Models\ExternalServiceLog;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ExternalServiceLogPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ExternalServiceLog $externalServiceLog): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ExternalServiceLog $externalServiceLog): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ExternalServiceLog $externalServiceLog): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ExternalServiceLog $externalServiceLog): bool
    {
        return false;
    }
    public function view(User $user, ExternalServiceLog $log)
    {
        return $user->type === 'super_admin'
            || $user->store_id === $log->order->store_id;
    }

    public function retry(User $user, ExternalServiceLog $log)
    {
        return $user->type === 'super_admin'
            || ($user->store_id === $log->order->store_id && $user->can('manage_orders'));
    }

}

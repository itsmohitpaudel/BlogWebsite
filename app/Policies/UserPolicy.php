<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->role === 'admin'; //Only Admin can do this
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $User): bool
    {
        //Admin can view any user while user can view their profile
        return $user->role === 'admin' || $user->id === $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->role === 'admin'; //Only Admin can do this
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $User): bool
    {
        //Admin can update any user while user can update their profile
        return $user->role === 'admin' || $user->id === $user->id;
    }

    // Determining whether user can update another user's role

    public function updateRole(User $user, User $User): bool
    {
        //Admin can update any user's role
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $User): bool
    {
        return $user->role === 'admin' && $user->id !== $user;
        // Admins can delete any user except themselves
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $User): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $User): bool
    {
        return false;
    }
}

<?php

namespace App\Policies;

use App\Apikeys;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ApikeysControl
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\User  $user
     * @return mixed
     */

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\User  $user
     * @param  \App\Apikeys  $apikeys
     * @return mixed
     */
    public function view(User $user, Apikeys $apikeys)
    {
		return $user->id == $apikeys->ownedBy || $user->access == "administrator";
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        if(auth()->user()->access == "administrator") {
			return true;
		} else {
			return false;
		}
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\User  $user
     * @param  \App\Apikeys  $apikeys
     * @return mixed
     */
    public function update(User $user, Apikeys $apikeys)
    {
        return $user->id == $apikeys->ownedBy || $user->access == "administrator";
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\Apikeys  $apikeys
     * @return mixed
     */
    public function delete(User $user, Apikeys $apikeys)
    {
        if(auth()->user()->access == "administrator") {
			return true;
		} else {
			return false;
		}
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\User  $user
     * @param  \App\Apikeys  $apikeys
     * @return mixed
     */
    public function restore(User $user, Apikeys $apikeys)
    {
        return true;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\Apikeys  $apikeys
     * @return mixed
     */
    public function forceDelete(User $user, Apikeys $apikeys)
    {
        return true;
    }
}

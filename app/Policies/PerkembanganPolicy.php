<?php

namespace App\Policies;

use App\Models\Perkembangan;
use App\Models\Staff;
use Illuminate\Auth\Access\Response;

class PerkembanganPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(Staff $user): bool
    {
        return $user->canCrudPerkembangan() || $user->canReadPerkembangan();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(Staff $user, Perkembangan $perkembangan): bool
    {
        return $this->viewAny($user);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(Staff $user): bool
    {
        return $user->canCrudPerkembangan();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Staff $user, Perkembangan $perkembangan): bool
    {
        return $user->canCrudPerkembangan();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Staff $user, Perkembangan $perkembangan): bool
    {
        return $user->canCrudPerkembangan();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(Staff $user, Perkembangan $perkembangan): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(Staff $user, Perkembangan $perkembangan): bool
    {
        return false;
    }
}

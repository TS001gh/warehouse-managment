<?php

namespace App\Policies;

use App\Enums\Permissions;
use App\Models\User;


class ItemPolicy
{
    /**
     * Determine if the user can view items.
     */
    public function view(User $user)
    {
        return $user->hasPermissionTo(Permissions::VIEW_ITEMS);
    }

    /**
     * Determine if the user can create items.
     */
    public function create(User $user)
    {
        return $user->hasPermissionTo(Permissions::ADD_ITEM);
    }

    /**
     * Determine if the user can edit items.
     */
    public function update(User $user)
    {
        return $user->hasPermissionTo(Permissions::EDIT_ITEM);
    }

    /**
     * Determine if the user can delete items.
     */
    public function delete(User $user)
    {
        return $user->hasPermissionTo(Permissions::DELETE_ITEM);
    }

    /**
     * Determine if the user can activate items.
     */
    public function activate(User $user)
    {
        return $user->hasPermissionTo(Permissions::ACTIVATE_ITEM);
    }

    /**
     * Determine if the user can deactivate items.
     */
    public function deactivate(User $user)
    {
        return $user->hasPermissionTo(Permissions::DEACTIVATE_ITEM);
    }
}

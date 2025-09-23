<?php

namespace App\Policies;

use App\Models\Layer1\User;

class OfficialAssignmentPolicy
{
    /**
     * Anyone logged in can view officials (coach, admin, tournament_manager).
     */
    public function view(User $user): bool
    {
        return in_array($user->role, ['coach', 'admin', 'tournament_manager']);
    }

    /**
     * Only admin & tournament_manager can assign officials.
     */
    public function assign(User $user): bool
    {
        return in_array($user->role, ['admin', 'tournament_manager']);
    }

    /**
     * Only admin & tournament_manager can remove officials.
     */
    public function remove(User $user): bool
    {
        return in_array($user->role, ['admin', 'tournament_manager']);
    }
}

<?php

namespace App\Policies;

use App\Enums\AdminRole;
use App\Models\Contact;
use App\Models\User;

class ContactPolicy
{
    /**
     * Inactive admins are blocked from every action regardless of role.
     */
    public function before(User $user): ?bool
    {
        return $user->is_active ? null : false;
    }

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Contact $contact): bool
    {
        return true;
    }

    public function update(User $user, Contact $contact): bool
    {
        return true;
    }

    public function delete(User $user, Contact $contact): bool
    {
        return $user->role === AdminRole::Admin;
    }

    public function deleteAny(User $user): bool
    {
        return $user->role === AdminRole::Admin;
    }
}

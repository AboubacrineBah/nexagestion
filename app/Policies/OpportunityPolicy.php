<?php

namespace App\Policies;

use App\Models\Opportunity;
use App\Models\User;

class OpportunityPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->canAccessModule('crm');
    }

    public function view(User $user, Opportunity $opportunity): bool
    {
        return $user->company_id !== null
            && $user->company_id === $opportunity->company_id
            && $user->canAccessModule('crm');
    }

    public function create(User $user): bool
    {
        return $user->canAccessModule('crm');
    }

    public function update(User $user, Opportunity $opportunity): bool
    {
        return $user->company_id === $opportunity->company_id && $user->canManageClients();
    }

    public function delete(User $user, Opportunity $opportunity): bool
    {
        return $user->company_id === $opportunity->company_id
            && $user->canManageClients()
            && in_array($user->role, ['owner', 'manager'], true);
    }

    public function restore(User $user, Opportunity $opportunity): bool
    {
        return $user->company_id === $opportunity->company_id
            && $user->canManageClients()
            && in_array($user->role, ['owner', 'manager'], true);
    }

    public function forceDelete(User $user, Opportunity $opportunity): bool
    {
        return $user->company_id === $opportunity->company_id && $user->role === 'owner';
    }
}

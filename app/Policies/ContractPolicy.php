<?php

namespace App\Policies;

use App\Models\Contract;
use App\Models\User;

class ContractPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->canAccessModule('hr');
    }

    public function view(User $user, Contract $contract): bool
    {
        return $user->company_id === $contract->employee->company_id && $user->canAccessModule('hr');
    }

    public function create(User $user): bool
    {
        return $user->canManageModule('hr');
    }

    public function update(User $user, Contract $contract): bool
    {
        return $user->company_id === $contract->employee->company_id && $user->canManageModule('hr');
    }

    public function delete(User $user, Contract $contract): bool
    {
        return $user->company_id === $contract->employee->company_id && $user->canManageModule('hr');
    }

    public function restore(User $user, Contract $contract): bool
    {
        return $user->company_id === $contract->employee->company_id && $user->canManageModule('hr');
    }

    public function forceDelete(User $user, Contract $contract): bool
    {
        return $user->company_id === $contract->employee->company_id && $user->role === 'owner';
    }
}

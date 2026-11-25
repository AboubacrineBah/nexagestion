<?php

namespace App\Policies;

use App\Models\Employee;
use App\Models\User;

class EmployeePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->canAccessModule('hr');
    }

    public function view(User $user, Employee $employee): bool
    {
        return $user->company_id === $employee->company_id && $user->canAccessModule('hr');
    }

    public function create(User $user): bool
    {
        return $user->canManageModule('hr');
    }

    public function update(User $user, Employee $employee): bool
    {
        return $user->company_id === $employee->company_id && $user->canManageModule('hr');
    }

    public function delete(User $user, Employee $employee): bool
    {
        return $user->company_id === $employee->company_id && $user->canManageModule('hr');
    }

    public function restore(User $user, Employee $employee): bool
    {
        return $user->company_id === $employee->company_id && $user->canManageModule('hr');
    }

    public function forceDelete(User $user, Employee $employee): bool
    {
        return $user->company_id === $employee->company_id && $user->role === 'owner';
    }
}

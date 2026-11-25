<?php

namespace App\Policies;

use App\Models\LeaveRequest;
use App\Models\User;

class LeaveRequestPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->canAccessModule('hr');
    }

    public function view(User $user, LeaveRequest $leaveRequest): bool
    {
        return $user->company_id === $leaveRequest->employee->company_id && $user->canAccessModule('hr');
    }

    public function create(User $user): bool
    {
        return $user->canManageModule('hr');
    }

    public function update(User $user, LeaveRequest $leaveRequest): bool
    {
        return $user->company_id === $leaveRequest->employee->company_id && $user->canManageModule('hr');
    }

    public function delete(User $user, LeaveRequest $leaveRequest): bool
    {
        return $user->company_id === $leaveRequest->employee->company_id && $user->canManageModule('hr');
    }

    public function restore(User $user, LeaveRequest $leaveRequest): bool
    {
        return $user->company_id === $leaveRequest->employee->company_id && $user->canManageModule('hr');
    }

    public function forceDelete(User $user, LeaveRequest $leaveRequest): bool
    {
        return $user->company_id === $leaveRequest->employee->company_id && $user->role === 'owner';
    }
}

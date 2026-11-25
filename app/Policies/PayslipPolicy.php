<?php

namespace App\Policies;

use App\Models\Payslip;
use App\Models\User;

class PayslipPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->canAccessModule('hr');
    }

    public function view(User $user, Payslip $payslip): bool
    {
        return $user->company_id === $payslip->employee->company_id
            && $user->canAccessModule('hr');
    }

    public function delete(User $user, Payslip $payslip): bool
    {
        return $user->company_id === $payslip->employee->company_id && $user->canManageModule('hr');
    }

    public function restore(User $user, Payslip $payslip): bool
    {
        return $user->company_id === $payslip->employee->company_id && $user->canManageModule('hr');
    }

    public function forceDelete(User $user, Payslip $payslip): bool
    {
        return $user->company_id === $payslip->employee->company_id && $user->role === 'owner';
    }
}

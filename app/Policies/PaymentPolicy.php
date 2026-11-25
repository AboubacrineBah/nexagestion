<?php

namespace App\Policies;

use App\Models\Payment;
use App\Models\User;

class PaymentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->canAccessModule('sales');
    }

    public function view(User $user, Payment $payment): bool
    {
        return $user->company_id !== null
            && $user->company_id === $payment->company_id
            && $user->canAccessModule('sales');
    }

    public function create(User $user): bool
    {
        return $user->canAccessModule('sales');
    }

    public function update(User $user, Payment $payment): bool
    {
        return $user->company_id === $payment->company_id && $user->canManageSales();
    }

    public function delete(User $user, Payment $payment): bool
    {
        return $user->company_id === $payment->company_id
            && $user->canManageSales()
            && in_array($user->role, ['owner', 'manager'], true);
    }

    public function restore(User $user, Payment $payment): bool
    {
        return $user->company_id === $payment->company_id
            && $user->canManageSales()
            && in_array($user->role, ['owner', 'manager'], true);
    }

    public function forceDelete(User $user, Payment $payment): bool
    {
        return $user->company_id === $payment->company_id && $user->role === 'owner';
    }
}

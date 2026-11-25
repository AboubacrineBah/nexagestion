<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;

class ProductPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->canAccessModule('sales') || $user->canAccessModule('crm');
    }

    public function view(User $user, Product $product): bool
    {
        return $user->company_id === $product->company_id && $user->canAccessModule('sales');
    }

    public function create(User $user): bool
    {
        return $user->canManageModule('sales');
    }

    public function update(User $user, Product $product): bool
    {
        return $user->company_id === $product->company_id && $user->canManageModule('sales');
    }

    public function delete(User $user, Product $product): bool
    {
        return $user->company_id === $product->company_id && $user->canManageModule('sales');
    }

    public function restore(User $user, Product $product): bool
    {
        return $user->company_id === $product->company_id && $user->canManageModule('sales');
    }

    public function forceDelete(User $user, Product $product): bool
    {
        return $user->company_id === $product->company_id && $user->role === 'owner';
    }
}

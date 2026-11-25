<?php

namespace App\Policies;

use App\Models\SalesDocument;
use App\Models\User;

class SalesDocumentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->canAccessModule('sales');
    }

    public function view(User $user, SalesDocument $document): bool
    {
        return $user->company_id !== null
            && $user->company_id === $document->company_id
            && $user->canAccessModule('sales');
    }

    public function create(User $user): bool
    {
        return $user->canAccessModule('sales');
    }

    public function update(User $user, SalesDocument $document): bool
    {
        $editable = $document->type === 'invoice'
            ? in_array($document->status, ['unpaid', 'partial'], true)
            : in_array($document->status, ['draft', 'sent'], true);

        return $user->company_id === $document->company_id && $user->canManageSales() && $editable;
    }

    public function delete(User $user, SalesDocument $document): bool
    {
        return $user->company_id === $document->company_id
            && $user->canManageSales()
            && in_array($user->role, ['owner', 'manager'], true)
            && $document->status === 'draft';
    }

    public function restore(User $user, SalesDocument $document): bool
    {
        return $user->company_id === $document->company_id
            && $user->canManageSales()
            && in_array($user->role, ['owner', 'manager'], true);
    }

    public function forceDelete(User $user, SalesDocument $document): bool
    {
        return $user->company_id === $document->company_id
            && $user->role === 'owner';
    }
}

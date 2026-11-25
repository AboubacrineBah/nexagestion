<?php

namespace App\Policies;

use App\Models\AuditEvent;
use App\Models\User;

class AuditEventPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->canAccessModule('sales') || $user->canAccessModule('crm') || $user->canAccessModule('hr');
    }

    public function view(User $user, AuditEvent $event): bool
    {
        return $user->company_id !== null
            && $user->company_id === $event->company_id;
    }
}

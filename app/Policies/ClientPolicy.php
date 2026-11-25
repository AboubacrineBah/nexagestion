<?php

namespace App\Policies;

use App\Models\Client;
use App\Models\User;

class ClientPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->canAccessModule('crm');
    }

    public function view(User $user, Client $client): bool
    {
        return $user->company_id !== null
            && $user->company_id === $client->company_id
            && $user->canAccessModule('crm');
    }

    public function create(User $user): bool
    {
        return $user->canAccessModule('crm');
    }

    public function update(User $user, Client $client): bool
    {
        return $user->company_id === $client->company_id && $user->canManageClients();
    }

    public function delete(User $user, Client $client): bool
    {
        return $user->company_id === $client->company_id
            && $user->canManageClients()
            && in_array($user->role, ['owner', 'manager'], true);
    }

    public function restore(User $user, Client $client): bool
    {
        return $user->company_id === $client->company_id
            && $user->canManageClients()
            && in_array($user->role, ['owner', 'manager'], true);
    }

    public function forceDelete(User $user, Client $client): bool
    {
        return $user->company_id === $client->company_id
            && $user->role === 'owner';
    }
}

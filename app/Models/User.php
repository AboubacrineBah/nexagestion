<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['company_id', 'name', 'email', 'password', 'role'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const MODULES = [
        'crm' => ['owner', 'manager', 'commercial'],
        'sales' => ['owner', 'manager', 'commercial', 'finance'],
        'hr' => ['owner', 'manager'],
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function canAccessModule(string $module): bool
    {
        return $this->company_id !== null && isset(self::MODULES[$module]) && in_array($this->role, self::MODULES[$module], true);
    }

    public function canManageModule(string $module): bool
    {
        return $this->canAccessModule($module) && in_array($this->role, ['owner', 'manager'], true);
    }

    public function canManageSales(): bool
    {
        return $this->canAccessModule('sales');
    }

    public function canManageClients(): bool
    {
        return $this->canAccessModule('crm');
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditEvent extends Model
{
    protected $fillable = ['company_id', 'user_id', 'subject_type', 'subject_id', 'action', 'details'];

    protected function casts(): array
    {
        return ['details' => 'array'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function record(string $type, int $id, string $action, array $details = []): void
    {
        $user = auth()->user();
        if (! $user?->company_id) {
            return;
        }

        self::create([
            'company_id' => $user->company_id,
            'user_id' => $user->id,
            'subject_type' => $type,
            'subject_id' => $id,
            'action' => $action,
            'details' => $details,
        ]);
    }
}

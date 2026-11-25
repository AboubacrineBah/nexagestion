<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesDocument extends Model
{
    use SoftDeletes;

    protected $fillable = ['company_id', 'client_id', 'created_by', 'source_document_id', 'reference', 'type', 'status', 'issued_at', 'due_at', 'currency', 'subtotal', 'total', 'notes'];

    protected function casts(): array
    {
        return ['issued_at' => 'date', 'due_at' => 'date', 'subtotal' => 'decimal:2', 'total' => 'decimal:2'];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function sourceDocument(): BelongsTo
    {
        return $this->belongsTo(self::class, 'source_document_id');
    }

    public function lines(): HasMany
    {
        return $this->hasMany(SalesDocumentLine::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}

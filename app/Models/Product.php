<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'company_id',
        'name',
        'sku',
        'description',
        'purchase_price_cents',
        'sale_price_cents',
        'quantity_in_stock',
        'alert_threshold',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'purchase_price_cents' => 'integer',
            'sale_price_cents' => 'integer',
            'quantity_in_stock' => 'integer',
            'alert_threshold' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }
}

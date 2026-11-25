<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payslip extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'employee_id',
        'pay_period',
        'gross_salary_cents',
        'deductions_cents',
        'net_salary_cents',
        'status',
        'paid_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'pay_period' => 'date',
            'gross_salary_cents' => 'integer',
            'deductions_cents' => 'integer',
            'net_salary_cents' => 'integer',
            'paid_at' => 'date',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStockMovementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->canManageModule('sales') ?? false;
    }

    public function rules(): array
    {
        return [
            'direction' => ['required', 'string', 'in:increase,decrease'],
            'quantity' => ['required', 'integer', 'gt:0'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }
}

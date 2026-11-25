<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->canManageModule('sales') ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:180'],
            'sku' => [
                'required',
                'string',
                'max:80',
                Rule::unique('products', 'sku')->where(fn ($query) => $query->where('company_id', $this->user()?->company_id ?? 0))->ignore($this->route('product')?->id),
            ],
            'description' => ['nullable', 'string', 'max:2000'],
            'purchase_price_cents' => ['required', 'integer', 'min:0'],
            'sale_price_cents' => ['required', 'integer', 'min:0'],
            'quantity_in_stock' => ['required', 'integer', 'min:0'],
            'alert_threshold' => ['required', 'integer', 'min:0'],
            'is_active' => ['required', 'boolean'],
        ];
    }
}

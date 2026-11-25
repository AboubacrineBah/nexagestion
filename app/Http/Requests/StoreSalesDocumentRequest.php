<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreSalesDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->canAccessModule('sales') ?? false;
    }

    public function rules(): array
    {
        return [
            'client_id' => [
                'required',
                'integer',
                Rule::exists('clients', 'id')->where(fn ($query) => $query->where('company_id', $this->user()?->company_id ?? 0)),
            ],
            'type' => ['required', 'in:quote,invoice'],
            'issued_at' => ['required', 'date'],
            'due_at' => ['nullable', 'date', 'after_or_equal:issued_at'],
            'notes' => ['nullable', 'string', 'max:5000'],
            'lines' => ['required', 'array', 'min:1', 'max:50'],
            'lines.*.product_id' => [
                'nullable',
                'integer',
                Rule::exists('products', 'id')->where(fn ($query) => $query->where('company_id', $this->user()?->company_id ?? 0)),
            ],
            'lines.*.description' => ['required', 'string', 'max:255'],
            'lines.*.quantity' => ['required', 'numeric', 'gt:0', 'max:1000000'],
            'lines.*.unit_price' => ['required', 'numeric', 'min:0', 'max:1000000000'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $total = collect($this->input('lines'))->sum(
                fn (array $line): float => round((float) $line['quantity'] * (float) $line['unit_price'], 2),
            );

            if ($total > 999_999_999_999.99) {
                $validator->errors()->add('lines', 'Le total dépasse la capacité autorisée pour ce document.');
            }
        });
    }
}

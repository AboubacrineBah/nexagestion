<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->canAccessModule('crm') ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:180'],
            'type' => ['required', 'in:prospect,client'],
            'email' => [
                'nullable',
                'email',
                'max:180',
                Rule::unique('clients', 'email')->where(fn ($query) => $query->where('company_id', $this->user()?->company_id ?? 0))->ignore($this->route('client')?->id),
            ],
            'phone' => ['nullable', 'string', 'max:40'],
            'address' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ];
    }
}

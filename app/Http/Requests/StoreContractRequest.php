<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreContractRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->canManageModule('hr') ?? false;
    }

    public function rules(): array
    {
        return [
            'type' => ['required', 'string', 'in:permanent,fixed,temporary,internship'],
            'status' => ['required', 'string', 'in:draft,active,ended'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'salary_cents' => ['required', 'integer', 'min:0'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }
}

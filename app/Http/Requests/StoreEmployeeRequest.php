<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->canManageModule('hr') ?? false;
    }

    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:80'],
            'last_name' => ['required', 'string', 'max:80'],
            'position' => ['nullable', 'string', 'max:120'],
            'email' => [
                'required',
                'email',
                'max:180',
                Rule::unique('employees', 'email')->where(fn ($query) => $query->where('company_id', $this->user()?->company_id ?? 0))->ignore($this->route('employee')?->id),
            ],
            'phone' => ['nullable', 'string', 'max:40'],
            'status' => ['required', 'in:active,paused,terminated'],
            'hire_date' => ['required', 'date'],
            'monthly_salary_cents' => ['required', 'integer', 'min:0'],
        ];
    }
}

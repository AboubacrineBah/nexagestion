<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLeaveRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->canManageModule('hr') ?? false;
    }

    public function rules(): array
    {
        return [
            'type' => ['required', 'string', 'in:annual,sick,personal,maternity'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'status' => ['required', 'string', 'in:pending,approved,rejected,cancelled'],
            'reason' => ['nullable', 'string', 'max:2000'],
        ];
    }
}

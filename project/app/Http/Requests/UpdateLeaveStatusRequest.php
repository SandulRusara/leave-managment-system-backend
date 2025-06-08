<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLeaveStatusRequest extends FormRequest
{

    public function authorize(): bool
    {
        return auth()->user() && auth()->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in(['approved', 'rejected'])],
            'admin_comments' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'Please select an action (approve or reject).',
            'status.in' => 'Invalid status. Please select approve or reject.',
            'admin_comments.max' => 'Comments cannot exceed 500 characters.',
        ];
    }
}
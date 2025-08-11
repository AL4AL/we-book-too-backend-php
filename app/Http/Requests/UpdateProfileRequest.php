<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'data' => 'required|array',
            'data.first_name' => 'nullable|string|max:100',
            'data.last_name' => 'nullable|string|max:100',
            'data.date_of_birth' => 'nullable|date|before:today',
            'data.address' => 'nullable|string|max:500',
            'data.emergency_contact' => 'nullable|string|max:255',
            'data.medical_conditions' => 'nullable|string|max:1000',
            'data.preferences' => 'nullable|array',
        ];
    }

    public function messages(): array
    {
        return [
            'data.required' => 'Profile data is required.',
            'data.date_of_birth.before' => 'Date of birth must be in the past.',
        ];
    }
}


<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RequestOtpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'identifier' => 'required|string|max:255',
            'channel' => 'required|string|in:email,phone',
        ];
    }

    public function messages(): array
    {
        return [
            'identifier.required' => 'Email or phone number is required.',
            'channel.in' => 'Channel must be either email or phone.',
        ];
    }

    protected function prepareForValidation(): void
    {
        // Auto-detect channel based on identifier format
        if (!$this->has('channel') && $this->has('identifier')) {
            $identifier = $this->input('identifier');
            $channel = filter_var($identifier, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';
            $this->merge(['channel' => $channel]);
        }
    }
}


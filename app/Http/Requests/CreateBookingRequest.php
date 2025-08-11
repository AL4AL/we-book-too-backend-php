<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'scheduled_at' => 'nullable|date|after:now',
            'notes' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.service_id' => 'required|exists:services,id',
            'items.*.specialist_id' => 'nullable|exists:specialists,id',
            'items.*.qty' => 'required|integer|min:1|max:10',
        ];
    }

    public function messages(): array
    {
        return [
            'scheduled_at.after' => 'Booking must be scheduled for a future date and time.',
            'items.required' => 'At least one service must be selected.',
            'items.*.service_id.exists' => 'Selected service does not exist.',
            'items.*.specialist_id.exists' => 'Selected specialist does not exist.',
        ];
    }
}


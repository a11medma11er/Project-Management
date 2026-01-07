<?php

namespace App\Http\Requests\Management;

use Illuminate\Foundation\Http\FormRequest;

class ActivityLogFilterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('view-activity-logs');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'log_name' => 'nullable|string|max:255',
            'event' => 'nullable|string|max:255',
            'user_id' => 'nullable|integer|exists:users,id',
            'date_from' => 'nullable|date|before_or_equal:today',
            'date_to' => 'nullable|date|after_or_equal:date_from|before_or_equal:today',
            'search' => 'nullable|string|max:500',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'user_id.exists' => 'The selected user does not exist.',
            'date_from.before_or_equal' => 'Start date cannot be in the future.',
            'date_to.after_or_equal' => 'End date must be after start date.',
            'date_to.before_or_equal' => 'End date cannot be in the future.',
        ];
    }
}

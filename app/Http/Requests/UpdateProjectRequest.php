<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProjectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->can('edit-projects');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'thumbnail' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:5120'], // 5MB
            'description' => ['required', 'string'],
            'priority' => ['required', 'in:High,Medium,Low'],
            'status' => ['required', 'in:Inprogress,Completed,On Hold'],
            'privacy' => ['nullable', 'in:Private,Team,Public'],
            'category' => ['nullable', 'string', 'max:100'],
            'skills' => ['nullable', 'string'], // Will be converted to array
            'deadline' => ['required', 'date'],
            'start_date' => ['nullable', 'date', 'before_or_equal:deadline'],
            'progress' => ['nullable', 'integer', 'min:0', 'max:100'],
            'is_favorite' => ['nullable', 'boolean'],
            'team_lead_id' => ['nullable', 'exists:users,id'],
            'members' => ['nullable', 'array'],
            'members.*' => ['exists:users,id'],
            'attachments' => ['nullable', 'array'],
            'attachments.*' => ['file', 'max:10240'], // 10MB per file
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Project title is required.',
            'description.required' => 'Project description is required.',
            'deadline.required' => 'Deadline is required.',
            'start_date.before_or_equal' => 'Start date must be before or equal to deadline.',
            'thumbnail.image' => 'Thumbnail must be an image file.',
            'thumbnail.max' => 'Thumbnail size must not exceed 5MB.',
            'progress.min' => 'Progress must be at least 0%.',
            'progress.max' => 'Progress cannot exceed 100%.',
        ];
    }
}

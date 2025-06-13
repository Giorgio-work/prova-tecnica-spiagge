<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'user_id' => ['required', 'exists:users,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'status' => ['required', Rule::in(['pending', 'in_progress', 'completed', 'cancelled'])],
            'priority' => ['required', Rule::in(['low', 'medium', 'high', 'urgent'])],
            'due_date' => ['nullable', 'date', 'after_or_equal:today'],
        ];

        // For updates, allow due_date to be in the past if task is completed
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            if ($this->input('status') === 'completed') {
                $rules['due_date'] = ['nullable', 'date'];
            }
        }

        return $rules;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Set user_id to current authenticated user if not provided
        if (!$this->has('user_id')) {
            $this->merge([
                'user_id' => auth()->id(),
            ]);
        }

        // Set completed_at if status is completed and not already set
        if ($this->input('status') === 'completed' && !$this->has('completed_at')) {
            $this->merge([
                'completed_at' => now(),
            ]);
        }

        // Clear completed_at if status is not completed
        if ($this->input('status') !== 'completed') {
            $this->merge([
                'completed_at' => null,
            ]);
        }
    }
}

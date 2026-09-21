<?php

namespace App\Http\Requests\Admin;

use App\Models\Project;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'brief' => ['nullable', 'string', 'max:5000'],
            'type' => ['required', Rule::in(array_keys(Project::TYPES))],
            'status' => ['required', Rule::in(array_keys(Project::STATUSES))],
            'due_date' => ['nullable', 'date'],
        ];
    }
}

<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\Concerns\ValidatesMediaUploads;
use App\Models\Project;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Admin creating a project on a client's behalf — same shape as the client
 * portal's request plus the client selector.
 */
class StoreProjectRequest extends FormRequest
{
    use ValidatesMediaUploads;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return array_merge([
            'client_id' => ['required', 'integer', 'exists:users,id'],
            'title' => ['required', 'string', 'max:255'],
            'brief' => ['nullable', 'string', 'max:5000'],
            'type' => ['required', Rule::in(array_keys(Project::TYPES))],
            'due_date' => ['nullable', 'date', 'after_or_equal:today'],
        ], $this->mediaRules(required: false));
    }

    public function messages(): array
    {
        return $this->mediaMessages();
    }
}

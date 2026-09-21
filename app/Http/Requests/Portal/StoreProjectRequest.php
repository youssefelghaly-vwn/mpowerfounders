<?php

namespace App\Http\Requests\Portal;

use App\Http\Requests\Concerns\ValidatesMediaUploads;
use App\Models\Project;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/** A client submitting their own podcast/video and describing the work. */
class StoreProjectRequest extends FormRequest
{
    use ValidatesMediaUploads;

    public function authorize(): bool
    {
        return $this->user()?->can('create', Project::class) ?? false;
    }

    public function rules(): array
    {
        return array_merge([
            'title' => ['required', 'string', 'max:255'],
            'brief' => ['required', 'string', 'max:5000'],
            'type' => ['required', Rule::in(array_keys(Project::TYPES))],
            'due_date' => ['nullable', 'date', 'after_or_equal:today'],
        ], $this->mediaRules(required: true));
    }

    public function attributes(): array
    {
        return [
            'brief' => 'description of your request',
            'files' => 'files',
        ];
    }

    public function messages(): array
    {
        return array_merge($this->mediaMessages(), [
            'files.required' => 'Add at least one video or audio file.',
            'brief.required' => 'Tell us what you would like us to do with this material.',
        ]);
    }
}

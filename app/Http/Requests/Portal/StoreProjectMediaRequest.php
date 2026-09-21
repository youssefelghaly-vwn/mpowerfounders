<?php

namespace App\Http\Requests\Portal;

use App\Http\Requests\Concerns\ValidatesMediaUploads;
use Illuminate\Foundation\Http\FormRequest;

/** Extra material added by the client to a project already in flight. */
class StoreProjectMediaRequest extends FormRequest
{
    use ValidatesMediaUploads;

    public function authorize(): bool
    {
        return $this->user()?->can('upload', $this->route('project')) ?? false;
    }

    public function rules(): array
    {
        return array_merge([
            'description' => ['nullable', 'string', 'max:2000'],
        ], $this->mediaRules(required: true));
    }

    public function messages(): array
    {
        return array_merge($this->mediaMessages(), [
            'files.required' => 'Choose at least one file to upload.',
        ]);
    }
}

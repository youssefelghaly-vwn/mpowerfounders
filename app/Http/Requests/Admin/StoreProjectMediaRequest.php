<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\Concerns\ValidatesMediaUploads;
use App\Models\ProjectMedia;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/** Our own uploads against a stage: cuts, revisions, reference material. */
class StoreProjectMediaRequest extends FormRequest
{
    use ValidatesMediaUploads;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return array_merge([
            'pipeline_stage_id' => ['nullable', 'integer', 'exists:pipeline_stages,id'],
            'kind' => ['required', Rule::in(array_keys(ProjectMedia::KINDS))],
            'description' => ['nullable', 'string', 'max:2000'],
            'visible_to_client' => ['nullable', 'boolean'],
        ], $this->mediaRules(required: true));
    }

    public function messages(): array
    {
        return $this->mediaMessages();
    }
}

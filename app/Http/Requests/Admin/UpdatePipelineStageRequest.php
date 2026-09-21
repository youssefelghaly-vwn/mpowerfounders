<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePipelineStageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('pipeline_stages', 'name')->ignore($this->route('stage')),
            ],
            'description' => ['nullable', 'string', 'max:255'],
            'client_message' => ['nullable', 'string', 'max:2000'],
            'position' => ['nullable', 'integer', 'min:0', 'max:999'],
            'color' => ['nullable', 'string', 'in:slate,amber,blue,violet,emerald,rose'],
            'is_default' => ['nullable', 'boolean'],
            'is_final' => ['nullable', 'boolean'],
            'notifies_client' => ['nullable', 'boolean'],
        ];
    }
}

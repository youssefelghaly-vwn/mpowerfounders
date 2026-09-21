<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class MoveProjectStageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'pipeline_stage_id' => ['required', 'integer', 'exists:pipeline_stages,id'],
            // Quoted back to the client in the stage-change email.
            'note' => ['nullable', 'string', 'max:2000'],
            // Overrides the stage's own notifies_client default for this
            // one move, so a quiet correction stays quiet.
            'notify_client' => ['nullable', 'boolean'],
        ];
    }
}

<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules;

/**
 * Sign-up is an application to work with us, so it collects a little
 * context (company, what they want made) that the team reads before
 * activating the account.
 */
class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'company' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'about' => ['nullable', 'string', 'max:2000'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ];
    }

    public function attributes(): array
    {
        return ['about' => 'what you want to make'];
    }
}

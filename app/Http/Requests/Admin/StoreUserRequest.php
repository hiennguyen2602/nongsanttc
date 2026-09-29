<?php

namespace App\Http\Requests\Admin;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')],
            'type' => ['required', Rule::in([User::TYPE_ADMIN, User::TYPE_STAFF])],
            'status' => ['required', Rule::in([0, 1])],
            'password' => ['required', 'confirmed', admin_password_rule()],
        ];
    }
}

<?php

namespace App\Http\Requests\Admin;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        /** @var User $user */
        $user = $this->route('user');

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'type' => ['required', Rule::in([User::TYPE_ADMIN, User::TYPE_STAFF])],
            'status' => ['required', Rule::in([0, 1])],
            'password' => ['nullable', 'confirmed', admin_password_rule()],
        ];
    }

    /** @return array<string, mixed> */
    public function toModelData(): array
    {
        $data = $this->validated();

        if (empty($data['password'])) {
            unset($data['password']);
        }

        return $data;
    }
}

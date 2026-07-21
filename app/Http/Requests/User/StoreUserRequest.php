<?php

namespace App\Http\Requests\User;

use App\Http\Requests\Concerns\AuthorizesPermissions;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
{
    use AuthorizesPermissions;

    public function authorize(): bool
    {
        return $this->authorizePermission('users.create');
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'role_id' => ['required', 'integer', 'exists:roles,id'],
            'site' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'user_code' => ['nullable', 'string', 'max:20', 'unique:users,user_code'],
        ];
    }
}

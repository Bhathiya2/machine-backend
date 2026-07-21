<?php

namespace App\Http\Requests\User;

use App\Http\Requests\Concerns\AuthorizesPermissions;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    use AuthorizesPermissions;

    public function authorize(): bool
    {
        return $this->authorizePermission('users.update');
    }

    public function rules(): array
    {
        /** @var User|null $user */
        $user = $this->route('user');

        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => ['sometimes', 'required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user?->id)],
            'password' => ['nullable', 'string', 'min:6'],
            'role_id' => ['sometimes', 'required', 'integer', 'exists:roles,id'],
            'site' => ['sometimes', 'required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'user_code' => ['sometimes', 'required', 'string', 'max:20', Rule::unique('users', 'user_code')->ignore($user?->id)],
        ];
    }
}

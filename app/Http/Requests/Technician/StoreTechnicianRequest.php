<?php

namespace App\Http\Requests\Technician;

use App\Http\Requests\Concerns\AuthorizesPermissions;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTechnicianRequest extends FormRequest
{
    use AuthorizesPermissions;

    public function authorize(): bool
    {
        return $this->authorizePermission('technicians.manage');
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'site' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'user_code' => ['nullable', 'string', 'max:20', 'unique:users,user_code'],
        ];
    }
}

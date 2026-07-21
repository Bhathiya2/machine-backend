<?php

namespace App\Http\Requests\Technician;

use App\Http\Requests\Concerns\AuthorizesPermissions;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTechnicianRequest extends FormRequest
{
    use AuthorizesPermissions;

    public function authorize(): bool
    {
        return $this->authorizePermission('technicians.manage');
    }

    public function rules(): array
    {
        $technicianId = $this->route('technician')?->id;

        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => ['sometimes', 'required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($technicianId)],
            'password' => ['nullable', 'string', 'min:6'],
            'site' => ['sometimes', 'required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'user_code' => ['sometimes', 'required', 'string', 'max:20', Rule::unique('users', 'user_code')->ignore($technicianId)],
        ];
    }
}

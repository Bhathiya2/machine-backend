<?php

namespace App\Http\Requests\Machine;

use App\Http\Requests\Concerns\AuthorizesPermissions;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMachineRequest extends FormRequest
{
    use AuthorizesPermissions;

    public function authorize(): bool
    {
        return $this->authorizePermission('machines.create');
    }

    public function rules(): array
    {
        return [
            'machine_number' => ['required', 'string', 'max:50', 'unique:machines,machine_number'],
            'name' => ['required', 'string', 'max:255'],
            'model' => ['nullable', 'string', 'max:255'],
            'site' => ['required', 'string', 'max:255'],
            'install_date' => ['required', 'date'],
            'setup_by' => ['required', 'string', 'max:255'],
            'factory_group' => ['nullable', 'string', 'max:255'],
            'factory' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'string', Rule::in([
                'Operational',
                'Under Maintenance',
                'Broken',
                'Offline',
            ])],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('machine_number')) {
            $this->merge([
                'machine_number' => strtoupper(trim((string) $this->input('machine_number'))),
            ]);
        }
    }
}

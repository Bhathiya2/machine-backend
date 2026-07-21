<?php

namespace App\Http\Requests\Machine;

use App\Http\Requests\Concerns\AuthorizesPermissions;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMachineRequest extends FormRequest
{
    use AuthorizesPermissions;

    public function authorize(): bool
    {
        return $this->authorizePermission('machines.update');
    }

    public function rules(): array
    {
        $machineId = $this->route('machine')?->id;

        return [
            'machine_number' => [
                'sometimes',
                'required',
                'string',
                'max:50',
                Rule::unique('machines', 'machine_number')->ignore($machineId),
            ],
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'model' => ['nullable', 'string', 'max:255'],
            'site' => ['sometimes', 'required', 'string', 'max:255'],
            'install_date' => ['sometimes', 'required', 'date'],
            'setup_by' => ['sometimes', 'required', 'string', 'max:255'],
            'factory_group' => ['nullable', 'string', 'max:255'],
            'factory' => ['nullable', 'string', 'max:255'],
            'status' => ['sometimes', 'required', 'string', Rule::in([
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

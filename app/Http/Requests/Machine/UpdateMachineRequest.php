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
            'cert_reference' => ['nullable', 'string', 'max:255'],
            'cert_calibration' => ['nullable', 'string', 'max:255'],
            'cert_warranty' => ['nullable', 'string', 'max:255'],
            'cert_contract' => ['nullable', 'string', 'max:255'],
            'client_name' => ['nullable', 'string', 'max:255'],
            'client_contact_person' => ['nullable', 'string', 'max:255'],
            'client_phone_number' => ['nullable', 'string', 'max:255'],
            'client_system' => ['nullable', 'string', 'max:255'],
            'client_customer_code' => ['nullable', 'string', 'max:255'],
            'client_job_title' => ['nullable', 'string', 'max:255'],
            'client_email' => ['nullable', 'string', 'max:255'],
            'client_expired_date' => ['nullable', 'string', 'max:255'],
            'client_date_of_manufacture' => ['nullable', 'string', 'max:255'],
            'tech_freq' => ['nullable', 'string', 'max:255'],
            'tech_voltage' => ['nullable', 'string', 'max:255'],
            'tech_amp' => ['nullable', 'string', 'max:255'],
            'tech_total_mc_power' => ['nullable', 'string', 'max:255'],
            'tech_ups' => ['nullable', 'string', 'max:255'],
            'tech_chiller_cooling_system' => ['nullable', 'string', 'max:255'],
            'tech_chiller_absorbed_power' => ['nullable', 'string', 'max:255'],
            'tech_smoke_extractor' => ['nullable', 'string', 'max:255'],
            'tech_room_temp' => ['nullable', 'string', 'max:255'],
            'sign_completed' => ['nullable', 'boolean'],
            'sign_incompleted' => ['nullable', 'boolean'],
            'sign_signed_by' => ['nullable', 'string', 'max:255'],
            'sign_technician_signature' => ['nullable', 'string', 'max:255'],
            'sign_date' => ['nullable', 'string', 'max:255'],
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

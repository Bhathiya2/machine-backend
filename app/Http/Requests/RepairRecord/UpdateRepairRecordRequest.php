<?php

namespace App\Http\Requests\RepairRecord;

use App\Http\Requests\Concerns\AuthorizesPermissions;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRepairRecordRequest extends FormRequest
{
    use AuthorizesPermissions;

    public function authorize(): bool
    {
        return $this->authorizePermission('repairs.update')
            || $this->authorizePermission('repairs.create');
    }

    public function rules(): array
    {
        return [
            'date' => ['sometimes', 'required', 'date'],
            'issue_category' => ['sometimes', 'required', 'string', Rule::in([
                'Mechanical', 'Electrical', 'Software / Firmware', 'Hydraulic', 'Preventive Maintenance',
            ])],
            'issue_description' => ['sometimes', 'required', 'string'],
            'parts_replaced' => ['nullable', 'array'],
            'labor_cost' => ['nullable', 'numeric', 'min:0'],
            'total_cost' => ['nullable', 'numeric', 'min:0'],
            'technician_id' => ['sometimes', 'required', 'string', 'max:50'],
            'photos' => ['nullable', 'array'],
        ];
    }
}

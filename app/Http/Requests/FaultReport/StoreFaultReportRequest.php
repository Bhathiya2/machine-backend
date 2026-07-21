<?php

namespace App\Http\Requests\FaultReport;

use App\Http\Requests\Concerns\AuthorizesPermissions;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreFaultReportRequest extends FormRequest
{
    use AuthorizesPermissions;

    public function authorize(): bool
    {
        return $this->authorizePermission('faults.report');
    }

    public function rules(): array
    {
        return [
            'machine_number' => ['required', 'string', 'exists:machines,machine_number'],
            'reported_by' => ['required', 'string', 'max:50'],
            'description' => ['required', 'string'],
            'severity' => ['required', 'string', Rule::in(['Low', 'Medium', 'High', 'Critical'])],
            'category' => ['required', 'string', Rule::in([
                'Mechanical', 'Electrical', 'Software / Firmware', 'Hydraulic', 'Preventive Maintenance',
            ])],
        ];
    }
}

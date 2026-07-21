<?php

namespace App\Http\Requests\FaultReport;

use App\Http\Requests\Concerns\AuthorizesPermissions;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateFaultReportRequest extends FormRequest
{
    use AuthorizesPermissions;

    public function authorize(): bool
    {
        $status = $this->input('status');

        if ($status === 'Dismissed') {
            return $this->authorizePermission('faults.dismiss');
        }

        if ($status === 'Converted' || $this->filled('converted_to_wo')) {
            return $this->authorizePermission('faults.convert');
        }

        return $this->authorizePermission('faults.report');
    }

    public function rules(): array
    {
        return [
            'description' => ['sometimes', 'required', 'string'],
            'severity' => ['sometimes', 'required', 'string', Rule::in(['Low', 'Medium', 'High', 'Critical'])],
            'category' => ['sometimes', 'required', 'string', Rule::in([
                'Mechanical', 'Electrical', 'Software / Firmware', 'Hydraulic', 'Preventive Maintenance',
            ])],
            'status' => ['sometimes', 'required', 'string', Rule::in(['Open', 'Converted', 'Dismissed'])],
            'converted_to_wo' => ['nullable', 'string', 'max:30'],
        ];
    }
}

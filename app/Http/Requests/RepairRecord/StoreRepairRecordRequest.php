<?php

namespace App\Http\Requests\RepairRecord;

use App\Http\Requests\Concerns\AuthorizesPermissions;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRepairRecordRequest extends FormRequest
{
    use AuthorizesPermissions;

    public function authorize(): bool
    {
        return $this->authorizePermission('repairs.create');
    }

    protected function prepareForValidation(): void
    {
        if (is_string($this->input('parts_replaced'))) {
            $this->merge(['parts_replaced' => json_decode($this->input('parts_replaced'), true) ?? []]);
        }
    }

    public function rules(): array
    {
        return [
            'work_order_number' => ['required', 'string', 'max:30'],
            'machine_number' => ['required', 'string', 'exists:machines,machine_number'],
            'date' => ['required', 'date'],
            'issue_category' => ['required', 'string', Rule::in([
                'Mechanical', 'Electrical', 'Software / Firmware', 'Hydraulic', 'Preventive Maintenance',
            ])],
            'issue_description' => ['required', 'string'],
            'parts_replaced' => ['nullable', 'array'],
            'parts_replaced.*.name' => ['required_with:parts_replaced', 'string'],
            'parts_replaced.*.partNumber' => ['nullable', 'string'],
            'parts_replaced.*.cost' => ['nullable', 'numeric', 'min:0'],
            'labor_cost' => ['nullable', 'numeric', 'min:0'],
            'total_cost' => ['nullable', 'numeric', 'min:0'],
            'technician_id' => ['required', 'string', 'max:50'],
            'photos' => ['nullable', 'array'],
            'photos.*' => ['file', 'image', 'mimes:jpeg,jpg,png,webp', 'max:10240'],
            'photo_type' => ['nullable', Rule::in(['before', 'after'])],
        ];
    }
}

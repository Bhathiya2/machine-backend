<?php

namespace App\Http\Requests\WorkOrder;

use App\Http\Requests\Concerns\AuthorizesPermissions;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreWorkOrderRequest extends FormRequest
{
    use AuthorizesPermissions;

    public function authorize(): bool
    {
        return $this->authorizePermission('workorders.create');
    }

    public function rules(): array
    {
        return [
            'machine_number' => ['required', 'string', 'exists:machines,machine_number'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'assigned_to' => ['required', 'string', 'max:50'],
            'created_by' => ['required', 'string', 'max:50'],
            'status' => ['nullable', 'string', Rule::in([
                'New',
                'Inprogress',
                'Close',
                'Verified',
                'Finished',
            ])],
            'priority' => ['required', 'string', Rule::in(['Low', 'Medium', 'High'])],
            'notes' => ['nullable', 'string'],
            'fault_report_id' => ['nullable', 'string', 'max:50'],
            'cost_entries' => ['nullable', 'array'],
            'cost_entries.*.id' => ['nullable', 'string', 'max:100'],
            'cost_entries.*.category' => ['required_with:cost_entries', 'string', 'in:Transportation,Accommodation,Labor,Spare Part,Others'],
            'cost_entries.*.quantity' => ['nullable', 'numeric', 'min:0'],
            'cost_entries.*.unitPrice' => ['nullable', 'numeric', 'min:0'],
            'cost_entries.*.amount' => ['required_with:cost_entries', 'numeric', 'min:0'],
            'cost_entries.*.details' => ['nullable', 'string'],
            'cost_entries.*.date' => ['required_with:cost_entries', 'date_format:Y-m-d'],
        ];
    }
}

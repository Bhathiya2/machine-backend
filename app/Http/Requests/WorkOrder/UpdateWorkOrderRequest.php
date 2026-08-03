<?php

namespace App\Http\Requests\WorkOrder;

use App\Http\Requests\Concerns\AuthorizesPermissions;
use App\Models\WorkOrder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateWorkOrderRequest extends FormRequest
{
    use AuthorizesPermissions;

    public function authorize(): bool
    {
        $user = $this->user();
        if ($user === null) {
            return false;
        }

        /** @var WorkOrder|null $workOrder */
        $workOrder = $this->route('work_order');

        // Block all edits to finished work orders
        if ($workOrder && $workOrder->status === 'Finished') {
            return false;
        }

        $payloadKeys = array_keys($this->all());
        $statusOnly = $payloadKeys === ['status'] || ($payloadKeys === ['status', 'notes'] && $this->has('status'));
        $notesOnly = $payloadKeys === ['notes'];

        if ($this->has('status')) {
            $nextStatus = (string) $this->input('status');

            if ($workOrder && $workOrder->status === 'Close' && $nextStatus === 'New') {
                return $user->resolvedRoleName() === 'Super Admin';
            }

            if ($workOrder && $workOrder->status === 'Close' && $nextStatus === 'Inprogress') {
                return $user->resolvedRoleName() === 'Super Admin';
            }

            if ($nextStatus === 'Verified' || $nextStatus === 'Finished') {
                return $user->resolvedRoleName() === 'Super Admin';
            }

            if (in_array($nextStatus, ['Close'], true)) {
                return $this->authorizePermission('workorders.verify_close')
                    || ($nextStatus === 'Close' && $this->authorizePermission('workorders.cancel'));
            }

            if ($statusOnly || $notesOnly || count($payloadKeys) <= 2) {
                if ($this->authorizePermission('workorders.update')) {
                    return true;
                }

                return $this->authorizePermission('workorders.update_status')
                    && $workOrder !== null
                    && $workOrder->assigned_to === $user->user_code;
            }
        }

        if ($notesOnly) {
            if ($this->authorizePermission('workorders.update')) {
                return true;
            }

            return $this->authorizePermission('workorders.update_notes')
                && $workOrder !== null
                && $workOrder->assigned_to === $user->user_code;
        }

        return $this->authorizePermission('workorders.update');
    }

    public function rules(): array
    {
        return [
            'machine_number' => ['sometimes', 'required', 'string', 'exists:machines,machine_number'],
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'assigned_to' => ['sometimes', 'required', 'string', 'max:50'],
            'status' => ['sometimes', 'required', 'string', Rule::in([
                'New',
                'Inprogress',
                'Close',
                'Verified',
                'Finished',
            ])],
            'priority' => ['sometimes', 'required', 'string', Rule::in(['Low', 'Medium', 'High'])],
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

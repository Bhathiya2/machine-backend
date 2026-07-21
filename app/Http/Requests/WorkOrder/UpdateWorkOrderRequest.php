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

        $payloadKeys = array_keys($this->all());
        $statusOnly = $payloadKeys === ['status'] || ($payloadKeys === ['status', 'notes'] && $this->has('status'));
        $notesOnly = $payloadKeys === ['notes'];

        if ($this->has('status')) {
            $nextStatus = (string) $this->input('status');
            if (in_array($nextStatus, ['Verified & Closed', 'Cancelled'], true)) {
                return $this->authorizePermission('workorders.verify_close')
                    || ($nextStatus === 'Cancelled' && $this->authorizePermission('workorders.cancel'));
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
                'Assigned',
                'Technician En Route',
                'Technician Arrived',
                'Work In Progress',
                'Work Completed',
                'Verified & Closed',
                'Cancelled',
            ])],
            'priority' => ['sometimes', 'required', 'string', Rule::in(['Low', 'Medium', 'High'])],
            'notes' => ['nullable', 'string'],
            'fault_report_id' => ['nullable', 'string', 'max:50'],
            'cost_entries' => ['nullable', 'array'],
        ];
    }
}

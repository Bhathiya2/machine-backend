<?php

namespace App\Http\Controllers\Api;

use App\Http\Concerns\AuthorizesApiPermissions;
use App\Http\Controllers\Controller;
use App\Http\Requests\Machine\StoreMachineRequest;
use App\Http\Requests\Machine\UpdateMachineRequest;
use App\Models\Machine;
use App\Repositories\All\Machine\MachineRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class MachineController extends Controller
{
    use AuthorizesApiPermissions;

    public function __construct(
        private readonly MachineRepositoryInterface $machines,
    ) {}

    public function index(): JsonResponse
    {
        $this->authorizePermission('machines.view');

        return response()->json($this->machines->allOrdered());
    }

    public function store(StoreMachineRequest $request): JsonResponse
    {
        $machine = $this->machines->create($request->validated());

        return response()->json($machine, Response::HTTP_CREATED);
    }

    public function show(Machine $machine): JsonResponse
    {
        $this->authorizePermission('machines.view');

        return response()->json($machine);
    }

    public function update(UpdateMachineRequest $request, Machine $machine): JsonResponse
    {
        $machine->update($request->validated());

        return response()->json($machine->fresh());
    }

    public function destroy(Machine $machine): Response
    {
        $this->authorizePermission('machines.delete');
        $machine->delete();

        return response()->noContent();
    }
}

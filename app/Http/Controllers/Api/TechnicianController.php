<?php

namespace App\Http\Controllers\Api;

use App\Http\Concerns\AuthorizesApiPermissions;
use App\Http\Controllers\Controller;
use App\Http\Requests\Technician\StoreTechnicianRequest;
use App\Http\Requests\Technician\UpdateTechnicianRequest;
use App\Models\User;
use App\Repositories\All\Technician\TechnicianRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class TechnicianController extends Controller
{
    use AuthorizesApiPermissions;

    public function __construct(
        private readonly TechnicianRepositoryInterface $technicians,
    ) {}

    public function index(): JsonResponse
    {
        abort_unless(
            auth()->user()?->hasAnyPermission(['technicians.manage', 'workorders.create', 'users.view']),
            Response::HTTP_FORBIDDEN
        );

        return response()->json($this->technicians->allTechnicians());
    }

    public function store(StoreTechnicianRequest $request): JsonResponse
    {
        $technician = $this->technicians->create([
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
            'password' => $request->validated('password'),
            'role' => 'Technician',
            'site' => $request->validated('site'),
            'phone' => $request->validated('phone'),
            'user_code' => $request->validated('user_code') ?? $this->technicians->nextUserCode(),
        ]);

        return response()->json($technician, Response::HTTP_CREATED);
    }

    public function show(User $technician): JsonResponse
    {
        $this->ensureTechnician($technician);
        abort_unless(
            auth()->user()?->hasAnyPermission(['technicians.manage', 'users.view']),
            Response::HTTP_FORBIDDEN
        );

        return response()->json($technician);
    }

    public function update(UpdateTechnicianRequest $request, User $technician): JsonResponse
    {
        $this->ensureTechnician($technician);

        $data = array_filter($request->validated(), fn ($value) => $value !== null);
        $technician->update($data);

        return response()->json($technician->fresh());
    }

    public function destroy(User $technician): Response
    {
        $this->ensureTechnician($technician);
        $this->authorizePermission('technicians.manage');
        $technician->delete();

        return response()->noContent();
    }

    private function ensureTechnician(User $user): void
    {
        abort_if($user->role !== 'Technician', Response::HTTP_NOT_FOUND);
    }
}

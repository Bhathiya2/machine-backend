<?php

namespace App\Http\Controllers\Api;

use App\Http\Concerns\AuthorizesApiPermissions;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Models\Role;
use App\Models\User;
use App\Repositories\All\User\UserRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    use AuthorizesApiPermissions;

    public function __construct(
        private readonly UserRepositoryInterface $users,
    ) {}

    public function index(): JsonResponse
    {
        $this->authorizePermission('users.view');

        return response()->json(
            $this->users->allUsers()->map(fn (User $user) => $this->formatUser($user))
        );
    }

    public function store(StoreUserRequest $request): JsonResponse
    {
        $role = Role::query()->findOrFail($request->validated('role_id'));

        $user = User::query()->create([
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
            'password' => $request->validated('password'),
            'role_id' => $role->id,
            'role' => $role->name,
            'site' => $request->validated('site'),
            'phone' => $request->validated('phone'),
            'user_code' => $request->validated('user_code') ?? $this->users->nextUserCode(),
            'email_verified_at' => now(),
        ]);

        return response()->json($this->formatUser($user->load('assignedRole')), Response::HTTP_CREATED);
    }

    public function show(User $user): JsonResponse
    {
        $this->authorizePermission('users.view');

        return response()->json($this->formatUser($user->load('assignedRole')));
    }

    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        $data = $request->validated();

        if (isset($data['role_id'])) {
            $role = Role::query()->findOrFail($data['role_id']);
            $data['role'] = $role->name;
        }

        if (array_key_exists('password', $data) && empty($data['password'])) {
            unset($data['password']);
        }

        $user->update($data);

        return response()->json($this->formatUser($user->fresh(['assignedRole'])));
    }

    public function destroy(User $user): Response
    {
        $this->authorizePermission('users.delete');
        abort_if($user->id === auth()->id(), Response::HTTP_FORBIDDEN, 'You cannot delete your own account.');

        $user->delete();

        return response()->noContent();
    }

    private function formatUser(User $user): array
    {
        return [
            'id' => $user->id,
            'user_code' => $user->user_code,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->resolvedRoleName(),
            'role_id' => $user->role_id,
            'site' => $user->site,
            'phone' => $user->phone,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
        ];
    }
}

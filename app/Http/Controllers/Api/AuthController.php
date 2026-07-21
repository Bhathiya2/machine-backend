<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->validated('email'))->first();

        if (! $user || ! Hash::check($request->validated('password'), $user->password)) {
            return response()->json(['message' => 'Invalid email or password'], Response::HTTP_UNAUTHORIZED);
        }

        $user->load(['assignedRole.permissions']);
        $token = $user->createToken('api')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $this->formatUser($user),
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->user();
        $user->load(['assignedRole.permissions']);

        return response()->json($this->formatUser($user));
    }

    public function logout(Request $request): Response
    {
        $request->user()->currentAccessToken()?->delete();

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
            'permissions' => $user->resolvedPermissions(),
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
        ];
    }
}

<?php

namespace App\Http\Requests\Concerns;

trait AuthorizesPermissions
{
    protected function authorizePermission(string $permission): bool
    {
        $user = $this->user();

        return $user !== null && $user->hasPermission($permission);
    }
}

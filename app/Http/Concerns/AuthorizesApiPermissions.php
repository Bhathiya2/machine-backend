<?php

namespace App\Http\Concerns;

use Symfony\Component\HttpFoundation\Response;

trait AuthorizesApiPermissions
{
    protected function authorizePermission(string $permission): void
    {
        abort_unless(
            auth()->user()?->hasPermission($permission),
            Response::HTTP_FORBIDDEN,
            'You do not have permission to perform this action.'
        );
    }
}

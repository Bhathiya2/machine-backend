<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

#[Fillable(['name', 'email', 'password', 'user_code', 'role', 'role_id', 'site', 'phone'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function assignedRole(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function hasPermission(string $permission): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        if ($this->assignedRole) {
            return $this->assignedRole->hasPermission($permission);
        }

        $role = $this->role ?? 'Technician';
        $permissions = config("permissions.roles.{$role}", []);

        if ($permissions === ['*']) {
            return true;
        }

        return in_array($permission, $permissions, true);
    }

    /** @param  list<string>  $permissions */
    public function hasAnyPermission(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }

        return false;
    }

    public function resolvedRoleName(): string
    {
        return $this->assignedRole?->name ?? ($this->role ?? 'Technician');
    }

    /** @return list<string> */
    public function resolvedPermissions(): array
    {
        if ($this->isSuperAdmin()) {
            return Permission::query()->pluck('name')->all();
        }

        if ($this->assignedRole) {
            return $this->assignedRole->permissionNames();
        }

        $role = $this->role ?? 'Technician';
        $permissions = config("permissions.roles.{$role}", []);

        return $permissions === ['*'] ? Permission::query()->pluck('name')->all() : $permissions;
    }

    public function isSuperAdmin(): bool
    {
        if ($this->assignedRole?->is_super_admin) {
            return true;
        }

        $role = $this->role ?? '';
        if (in_array($role, ['Super Admin', 'Admin'], true)) {
            return true;
        }

        return in_array(strtolower((string) $this->email), [
            'superadmin@example.com',
            'admin@example.com',
        ], true);
    }

    public function technicianNotes(): HasMany
    {
        return $this->hasMany(TechnicianNote::class);
    }
}

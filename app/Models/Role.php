<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Role extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'is_system', 'is_super_admin'];

    protected function casts(): array
    {
        return [
            'is_system' => 'boolean',
            'is_super_admin' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Role $role) {
            if (empty($role->slug)) {
                $role->slug = Str::slug($role->name);
            }
        });
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function hasPermission(string $permission): bool
    {
        if ($this->is_super_admin) {
            return true;
        }

        return $this->permissions->contains('name', $permission);
    }

    public function permissionNames(): array
    {
        if ($this->is_super_admin) {
            return Permission::query()->pluck('name')->all();
        }

        return $this->permissions->pluck('name')->all();
    }
}

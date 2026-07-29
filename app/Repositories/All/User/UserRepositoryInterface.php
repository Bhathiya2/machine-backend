<?php

namespace App\Repositories\All\User;

use Illuminate\Database\Eloquent\Collection;

interface UserRepositoryInterface
{
    public function allUsers(): Collection;

    public function nextUserCode(): string;
}

<?php

namespace App\Repositories\All\User;

use App\Models\User;
use App\Repositories\Base\BaseRepository;
use Illuminate\Database\Eloquent\Collection;

interface UserRepositoryInterface
{
    public function allUsers(): Collection;

    public function nextUserCode(): string;
}

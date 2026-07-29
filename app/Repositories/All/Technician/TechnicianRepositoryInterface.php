<?php

namespace App\Repositories\All\Technician;

use App\Repositories\Base\EloquentRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface TechnicianRepositoryInterface extends EloquentRepositoryInterface
{
    public function allTechnicians(): Collection;

    public function findByUserCode(string $userCode): ?Model;

    public function nextUserCode(): string;
}

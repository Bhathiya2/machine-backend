<?php

namespace App\Repositories\All\Technician;

use App\Repositories\Base\EloquentRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

interface TechnicianRepositoryInterface extends EloquentRepositoryInterface
{
    public function allTechnicians(): Collection;

    public function findByUserCode(string $userCode): ?\Illuminate\Database\Eloquent\Model;

    public function nextUserCode(): string;
}

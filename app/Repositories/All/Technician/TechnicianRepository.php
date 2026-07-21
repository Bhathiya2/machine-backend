<?php

namespace App\Repositories\All\Technician;

use App\Models\User;
use App\Repositories\Base\BaseRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class TechnicianRepository extends BaseRepository implements TechnicianRepositoryInterface
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    public function allTechnicians(): Collection
    {
        return $this->model->newQuery()
            ->where('role', 'Technician')
            ->orderBy('name')
            ->get();
    }

    public function findByUserCode(string $userCode): ?Model
    {
        return $this->model->newQuery()
            ->where('user_code', $userCode)
            ->where('role', 'Technician')
            ->first();
    }

    public function nextUserCode(): string
    {
        $latest = $this->model->newQuery()
            ->whereNotNull('user_code')
            ->orderByDesc('id')
            ->value('user_code');

        if (! $latest || ! preg_match('/u(\d+)/', $latest, $matches)) {
            return 'u1';
        }

        return 'u'.((int) $matches[1] + 1);
    }
}

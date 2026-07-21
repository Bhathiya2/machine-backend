<?php

namespace App\Repositories\All\User;

use App\Models\User;
use App\Repositories\Base\BaseRepository;
use Illuminate\Database\Eloquent\Collection;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    public function allUsers(): Collection
    {
        return $this->model->newQuery()
            ->with('assignedRole')
            ->orderBy('name')
            ->get();
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

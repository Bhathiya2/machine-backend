<?php

namespace App\Providers;

use App\Repositories\All\FaultReport\FaultReportRepository;
use App\Repositories\All\FaultReport\FaultReportRepositoryInterface;
use App\Repositories\All\Machine\MachineRepository;
use App\Repositories\All\Machine\MachineRepositoryInterface;
use App\Repositories\All\Notification\NotificationRepository;
use App\Repositories\All\Notification\NotificationRepositoryInterface;
use App\Repositories\All\Permission\PermissionRepository;
use App\Repositories\All\Permission\PermissionRepositoryInterface;
use App\Repositories\All\RepairRecord\RepairRecordRepository;
use App\Repositories\All\RepairRecord\RepairRecordRepositoryInterface;
use App\Repositories\All\Role\RoleRepository;
use App\Repositories\All\Role\RoleRepositoryInterface;
use App\Repositories\All\Technician\TechnicianRepository;
use App\Repositories\All\Technician\TechnicianRepositoryInterface;
use App\Repositories\All\User\UserRepository;
use App\Repositories\All\User\UserRepositoryInterface;
use App\Repositories\All\WorkOrder\WorkOrderRepository;
use App\Repositories\All\WorkOrder\WorkOrderRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(MachineRepositoryInterface::class, MachineRepository::class);
        $this->app->bind(WorkOrderRepositoryInterface::class, WorkOrderRepository::class);
        $this->app->bind(TechnicianRepositoryInterface::class, TechnicianRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(RoleRepositoryInterface::class, RoleRepository::class);
        $this->app->bind(PermissionRepositoryInterface::class, PermissionRepository::class);
        $this->app->bind(FaultReportRepositoryInterface::class, FaultReportRepository::class);
        $this->app->bind(NotificationRepositoryInterface::class, NotificationRepository::class);
        $this->app->bind(RepairRecordRepositoryInterface::class, RepairRecordRepository::class);
    }

    public function boot(): void
    {
        //
    }
}

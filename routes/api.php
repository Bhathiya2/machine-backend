<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FaultReportController;
use App\Http\Controllers\Api\MachineController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\RepairRecordController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\TechnicianController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\WorkOrderController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::apiResource('machines', MachineController::class);
    Route::apiResource('work-orders', WorkOrderController::class);
    Route::apiResource('technicians', TechnicianController::class);
    Route::apiResource('users', UserController::class);
    Route::apiResource('roles', RoleController::class);
    Route::put('roles/{role}/permissions', [RoleController::class, 'syncPermissions']);
    Route::get('permissions', [PermissionController::class, 'index']);

    Route::apiResource('fault-reports', FaultReportController::class);
    Route::apiResource('repair-records', RepairRecordController::class);
    Route::get('notifications', [NotificationController::class, 'index']);
    Route::post('notifications/mark-all-read', [NotificationController::class, 'markAllRead']);
    Route::post('notifications/{notification}/read', [NotificationController::class, 'markRead']);
});

<?php

use Illuminate\Support\Facades\Route;
use Modules\Auth\Http\Controllers\AuthController;
use Modules\Auth\Http\Controllers\RolesController;
use Modules\Auth\Http\Controllers\PermissionsController;
use Modules\Auth\Http\Controllers\UsersController;
use Modules\Auth\Http\Controllers\UserRoleController;

Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth:sanctum')->group(function () {

    // Profile
    Route::prefix('profile')->group(function () {
        Route::get('/', [AuthController::class, 'showProfile']);
        Route::put('/', [AuthController::class, 'updateProfile']);
        Route::put('/change-password', [AuthController::class, 'changePassword']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });

    // Assign & Remove roles/permissions
    Route::prefix('users/{user}')->group(function () {
        Route::post('/assign-role/{role}', [UserRoleController::class, 'assignRoleToUser']);
        Route::delete('/remove-role/{role}', [UserRoleController::class, 'removeRoleFromUser']);
        Route::post('/assign-permission/{permission}', [UserRoleController::class, 'assignPermissionToUser']);
        Route::delete('/remove-permission/{permission}', [UserRoleController::class, 'removePermissionFromUser']);
    });

    Route::apiResource('roles', RolesController::class);
    Route::apiResource('permissions', PermissionsController::class);
    Route::apiResource('users', UsersController::class);
});

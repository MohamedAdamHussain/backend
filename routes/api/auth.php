<?php


use App\Http\Controllers\Auth\PermissionsController;
use App\Http\Controllers\Auth\RolesController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {
    // بروفايل المستخدم الحالي
    Route::prefix('profile')->group(function () {
        Route::get('/', [AuthController::class, 'showProfile']);
        Route::put('/', [AuthController::class, 'updateProfile']);
        Route::put('/change-password', [AuthController::class, 'changePassword']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });

    // إدارة المستخدمين
    Route::prefix('users/{user}')->group(function () {
        Route::post('/assign-role', [AuthController::class, 'assignRole']);
        Route::post('/assign-permission', [AuthController::class, 'assignPermission']);
        Route::delete('/remove-role', [AuthController::class, 'removeRole']);
        Route::delete('/remove-permission', [AuthController::class, 'removePermission']);
    });

    Route::apiResource('roles', RolesController::class);
    Route::apiResource('permissions', PermissionsController::class);
    Route::apiResource('users', UserController::class);

});

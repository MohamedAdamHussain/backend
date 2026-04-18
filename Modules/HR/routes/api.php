<?php

use Illuminate\Support\Facades\Route;
use Modules\HR\Http\Controllers\AbsenceController;
use Modules\HR\Http\Controllers\AdvanceController;
use Modules\HR\Http\Controllers\AdvanceRepaymentController;
use Modules\HR\Http\Controllers\AttendanceController;
use Modules\HR\Http\Controllers\DepartmentController;
use Modules\HR\Http\Controllers\EmployeesController;
use Modules\HR\Http\Controllers\SalaryController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::prefix('hr')->group(function () {
        Route::apiResource('employees', EmployeesController::class);
        Route::apiResource('attendances', AttendanceController::class);
        Route::apiResource('departments', DepartmentController::class);
        Route::apiResource('absences', AbsenceController::class);
        Route::apiResource('advances', AdvanceController::class);
        Route::apiResource('advance-repayments', AdvanceRepaymentController::class);
        Route::apiResource('salaries', SalaryController::class);
        Route::prefix('salaries/{salary}')->group(function () {
            Route::put('/mark-paid',   [SalaryController::class, 'markAsPaid']);
            Route::put('/mark-unpaid', [SalaryController::class, 'markAsUnpaid']);
        });
    });
});

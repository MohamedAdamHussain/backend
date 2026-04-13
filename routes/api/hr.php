<?php

use App\Http\Controllers\HR\AbsenceController;
use App\Http\Controllers\HR\AdvanceController;
use App\Http\Controllers\HR\AttendanceController;
use App\Http\Controllers\HR\DepartmentController;
use App\Http\Controllers\HR\EmployeesController;
use App\Http\Controllers\HR\SalaryController;
use Illuminate\Support\Facades\Route;





Route::prefix('hr')->group(function () {
Route::apiResource('employees',EmployeesController::class);
Route::apiResource('attendances',AttendanceController::class);
Route::apiResource('departments',DepartmentController::class);
Route::apiResource('absences',AbsenceController::class);
Route::apiResource('advances',AdvanceController::class);
Route::apiResource('salaries',SalaryController::class);
Route::prefix('salary')->group(function () {
    Route::put('paid', [SalaryController::class, 'markAsPaid']);
    Route::put('unpaid', [SalaryController::class, 'markAsUnpaid']);
});
});


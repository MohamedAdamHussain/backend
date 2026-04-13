<?php

use App\Http\Controllers\Accounts\AccountController;
use App\Http\Controllers\Accounts\JournalEntriesController;
use Illuminate\Support\Facades\Route;


Route::prefix('accounting')->middleware('auth:sanctum')->group(function () {
    Route::apiResource('accounts', AccountController::class);
    Route::get('journal-entries', [JournalEntriesController::class, 'index']);
    Route::get('journal-entries/{journalEntry}', [JournalEntriesController::class, 'show']);
});

<?php

use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ProductController;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


// Route::prefix('v1')->group(function () {
//     require base_path('routes/api/auth.php');
//     require base_path('routes/api/inventory.php');
//     require base_path('routes/api/sales.php');
//     require base_path('routes/api/accounting.php');
//     require base_path('routes/api/hr.php');
// });

Route::get('products',function(){
    return \App\Models\Product::all();
});

Route::middleware('auth:sanctum')->group(function () {
Route::post('/product', [ProductController::class, 'store']);
Route::put('/product/{id}', [ProductController::class, 'update']);
Route::delete('/product/{id}', [ProductController::class, 'destroy']);
});

Route::get('/product/{id}', [ProductController::class, 'show'])->where('id','[0-9]+');

Route::apiResource('invoices',InvoiceController::class);

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Sales\CustomerController;
use App\Http\Controllers\Sales\PriceListController;
use App\Http\Controllers\Sales\SaleOrderController;
use App\Http\Controllers\Sales\InvoiceController;
use App\Http\Controllers\Sales\DeliveryOrderController;

   Route::prefix('sales')->middleware('auth:sanctum')->group(function () {
    Route::apiResource('customers', CustomerController::class);
    Route::apiResource('price-lists', PriceListController::class);

    Route::apiResource('sale-orders', SaleOrderController::class);
    Route::prefix('sale-orders/{saleOrder}')->group(function () {
        Route::post('/complete', [SaleOrderController::class, 'complete']);
        Route::post('/cancel',   [SaleOrderController::class, 'cancel']);
    });

    Route::apiResource('invoices', InvoiceController::class);
    Route::prefix('invoices/{invoice}')->group(function () {
        Route::post('/pay',    [InvoiceController::class, 'pay']);
        Route::post('/cancel', [InvoiceController::class, 'cancel']);
    });

    Route::apiResource('delivery-orders', DeliveryOrderController::class);
    Route::prefix('delivery-orders/{deliveryOrder}')->group(function () {
        Route::post('/complete', [DeliveryOrderController::class, 'complete']);
        Route::post('/cancel',   [DeliveryOrderController::class, 'cancel']);
    });
});

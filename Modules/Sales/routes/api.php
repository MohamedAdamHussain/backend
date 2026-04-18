<?php

use Illuminate\Support\Facades\Route;
use Modules\Sales\Http\Controllers\CustomerController;
use Modules\Sales\Http\Controllers\DeliveryOrderController;
use Modules\Sales\Http\Controllers\InvoiceController;
use Modules\Sales\Http\Controllers\PriceListController;
use Modules\Sales\Http\Controllers\SaleOrderController;
use Modules\Sales\Http\Controllers\SalesController;

Route::prefix('sales')->middleware('auth:sanctum')->group(function () {
    Route::apiResource('customers', CustomerController::class);
    Route::apiResource('price-lists', PriceListController::class);

    Route::apiResource('sale-orders', SaleOrderController::class);
    Route::prefix('sale-orders/{saleOrder}')->group(function () {
        Route::post('/accept',   [SaleOrderController::class, 'accept']);
        Route::post('/process',  [SaleOrderController::class, 'process']);
        Route::post('/ship',     [SaleOrderController::class, 'ship']);
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
        Route::post('/ship',     [DeliveryOrderController::class, 'ship']);
        Route::post('/complete', [DeliveryOrderController::class, 'complete']);
        Route::post('/cancel',   [DeliveryOrderController::class, 'cancel']);
    });
});

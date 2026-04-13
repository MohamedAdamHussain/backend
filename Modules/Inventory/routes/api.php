<?php

use Illuminate\Support\Facades\Route;
use Modules\Inventory\Http\Controllers\DashboardController;
use Modules\Inventory\Http\Controllers\InventoryCountsController;
use Modules\Inventory\Http\Controllers\ProductsController;
use Modules\Inventory\Http\Controllers\StockTransfersController;
use Modules\Inventory\Http\Controllers\SuppliersController;
use Modules\Inventory\Http\Controllers\SupplyOrderController;
use Modules\Inventory\Http\Controllers\WarehousesController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('dashboard/stats', [DashboardController::class, 'stats']);


    Route::prefix('inventory')->group(function () {

            Route::apiResource('products', ProductsController::class);
            Route::apiResource('warehouses', WarehousesController::class);
            Route::apiResource('suppliers', SuppliersController::class);

            Route::apiResource('supply-orders', SupplyOrderController::class);
            Route::prefix('supply-orders/{supplyOrder}')->group(function () {
                Route::post('/complete', [SupplyOrderController::class, 'complete']);
                Route::post('/cancel', [SupplyOrderController::class, 'cancel']);
            });
            // عمليات الحالة
            Route::apiResource('stock-transfers', StockTransfersController::class);
            Route::prefix('stock-transfers/{stockTransfer}')->group(function () {
                Route::post('/complete', [StockTransfersController::class, 'complete']);
                Route::post('/cancel', [StockTransfersController::class, 'cancel']);
            });
            Route::apiResource('inventory-counts', InventoryCountsController::class);
            Route::prefix('inventory-counts/{inventoryCount}')->group(function () {
                Route::post('/complete', [InventoryCountsController::class, 'complete']);
                Route::post('/cancel', [InventoryCountsController::class, 'cancel']);
            });


        });

});

<?php

use App\Http\Controllers\Inventory\InventoryCountController;
use App\Http\Controllers\Inventory\ProductsController;
use App\Http\Controllers\Inventory\StockTransferController;
use App\Http\Controllers\Inventory\SuppliersController;
use App\Http\Controllers\Inventory\SupplyOrderController;
use App\Http\Controllers\Inventory\WarehousesController;
use illuminate\Support\Facades\Route;


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
        Route::apiResource('stock-transfers', StockTransferController::class);
        Route::prefix('stock-transfers/{stockTransfer}')->group(function () {
            Route::post('/complete', [StockTransferController::class, 'complete']);
            Route::post('/cancel', [StockTransferController::class, 'cancel']);
        });
        Route::apiResource('inventory-counts', InventoryCountController::class);
        Route::prefix('inventory-counts/{inventoryCount}')->group(function () {
            Route::post('/complete', [InventoryCountController::class, 'complete']);
            Route::post('/cancel', [InventoryCountController::class, 'cancel']);
        });


    });

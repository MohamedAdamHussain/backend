<?php

namespace Modules\Inventory\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class InsufficientStockException extends Exception {
    public function __construct(int $productId, int $warehouseId)
    {
        parent::__construct(
            "Insufficient stock for product ID $productId in warehouse ID $warehouseId"
        );
    }

    public function render() : JsonResponse
    {
        return response()->json([
            'success' => false,
            'error' => $this->getMessage()
        ], 422);
    }
}

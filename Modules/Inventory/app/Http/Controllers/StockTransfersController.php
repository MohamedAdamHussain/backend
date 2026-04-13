<?php

namespace Modules\Inventory\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Inventory\Models\StockTransfer;
use Modules\shared\Http\Traits\ApiResponse;
use Modules\shared\Http\Traits\LogsInventoryMovement;

class StockTransfersController extends Controller
{
    use LogsInventoryMovement, ApiResponse;

    public function index(): JsonResponse
    {
        Gate::authorize('view-stock-transfers');
        return $this->successResponse(StockTransfer::with(['fromWarehouse', 'toWarehouse', 'items.product'])->paginate(20));
    }

    public function show(StockTransfer $stockTransfer): JsonResponse
    {
        Gate::authorize('view-stock-transfers');
        return $this->successResponse($stockTransfer->load(['fromWarehouse', 'toWarehouse', 'items.product']));
    }

    public function store(Request $request): JsonResponse
    {
        Gate::authorize('create-stock-transfers');
        $validated = $request->validate([
            'from_warehouse_id' => 'required|exists:warehouses,id',
            'to_warehouse_id' => 'required|exists:warehouses,id|different:from_warehouse_id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        // foreach ($validated['items'] as $item) {
        //     $stockTransfer->items()->create([
        //         'product_id' => $item['product_id'],
        //         'quantity'   => $item['quantity'],
        //         'unit_price' => 0, // أو تضيفه في الـ validation
        //         'total_price' => 0,
        //     ]);
        // }
        $stockTransfer = StockTransfer::create([
            'from_warehouse_id' => $validated['from_warehouse_id'],
            'to_warehouse_id' => $validated['to_warehouse_id'],
            'status' => 'pending',
        ]);
        return $this->successResponse($stockTransfer);
    }

    public function update(StockTransfer $stockTransfer, Request $request): JsonResponse
    {
        Gate::authorize('edit-stock-transfers');

        // pending فقط يمكن تعديل الـ items
        if ($stockTransfer->status === 'in_transit' && $request->has('items')) {
            return $this->errorResponse('Cannot edit items of in_transit transfer', 400);
        }

        if ($stockTransfer->status === 'completed') {
            return $this->errorResponse('Cannot edit completed transfer', 400);
        }

        $validated = $request->validate([
            'from_warehouse_id' => 'sometimes|required|exists:warehouses,id',
            'to_warehouse_id' => 'sometimes|required|exists:warehouses,id|different:from_warehouse_id',
            'items' => 'sometimes|required|array|min:1',
            'items.*.product_id' => 'required_with:items|exists:products,id',
            'items.*.quantity' => 'required_with:items|integer|min:1',
        ]);

        $stockTransfer->update($validated);
        return $this->successResponse($stockTransfer);
    }
    // عند in_transit → خصم من المستودع المصدر
    public function transit(StockTransfer $stockTransfer): JsonResponse
    {
        Gate::authorize('complete-stock-transfers');

        if ($stockTransfer->status !== 'pending') {
            return $this->errorResponse('Only pending transfers can be transited', 400);
        }
        DB::transaction(function () use ($stockTransfer) {
            foreach ($stockTransfer->items as $item) {
                // خصم من المستودع المصدر
                $this->updateWarehouseStock(
                    $stockTransfer->from_warehouse_id,
                    $item->product_id,
                    -$item->quantity
                );

                $this->logInventoryMovement(
                    $item->product_id,
                    $stockTransfer->from_warehouse_id,
                    'transfer',
                    -$item->quantity,
                    $stockTransfer
                );
            }
            $stockTransfer->update(['status' => 'in_transit']);
        });
        return $this->successResponse($stockTransfer);
    }

    // عند complete → إضافة للمستودع الوجهة
    public function complete(StockTransfer $stockTransfer): JsonResponse
    {
        Gate::authorize('complete-stock-transfers');

        if ($stockTransfer->status !== 'in_transit') {
            return $this->errorResponse('Only in_transit transfers can be completed', 400);
        }
        DB::transaction(function () use ($stockTransfer) {
            foreach ($stockTransfer->items as $item) {
                $this->updateWarehouseStock(
                    $stockTransfer->to_warehouse_id,
                    $item->product_id,
                    +$item->quantity
                );

                $this->logInventoryMovement(
                    $item->product_id,
                    $stockTransfer->to_warehouse_id,
                    'transfer',
                    +$item->quantity,
                    $stockTransfer
                );
            }

            $stockTransfer->update(['status' => 'completed']);
        });
        return $this->successResponse($stockTransfer);
    }

    // عند cancel من in_transit → عكس الحركة
    public function cancel(StockTransfer $stockTransfer): JsonResponse
    {
        Gate::authorize('cancel-stock-transfers');

        if (!in_array($stockTransfer->status, ['pending', 'in_transit'])) {
            return $this->errorResponse('Cannot cancel completed transfer', 400);
        }

        // إذا كان in_transit → يجب إرجاع الكمية للمستودع المصدر
        DB::transaction(function () use ($stockTransfer) {
            if ($stockTransfer->status === 'in_transit') {

                foreach ($stockTransfer->items as $item) {
                    $this->updateWarehouseStock(
                        $stockTransfer->from_warehouse_id,
                        $item->product_id,
                        +$item->quantity  // عكس الحركة
                    );

                    $this->logInventoryMovement(
                        $item->product_id,
                        $stockTransfer->from_warehouse_id,
                        'transfer',
                        +$item->quantity,
                        $stockTransfer
                    );
                }
            }

            $stockTransfer->update(['status' => 'cancelled']);
        });
        return $this->successResponse($stockTransfer->fresh());
    }
}

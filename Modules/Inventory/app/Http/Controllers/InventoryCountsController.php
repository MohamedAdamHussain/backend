<?php

namespace Modules\Inventory\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Modules\Inventory\Models\InventoryCount;
use Modules\Shared\Http\Traits\ApiResponse;
use Modules\Shared\Http\Traits\LogsInventoryMovement;

class InventoryCountsController extends Controller
{
    use LogsInventoryMovement, ApiResponse;

    public function index(): JsonResponse
    {
        Gate::authorize('view-inventory-counts');
        return $this->successResponse(InventoryCount::with('items')->paginate(20));
    }

    public function show(InventoryCount $inventoryCount): JsonResponse
    {
        Gate::authorize('view-inventory-counts');
        return $this->successResponse($inventoryCount->load('items.product'));
    }

    public function store(Request $request): JsonResponse
    {
        Gate::authorize('create-inventory-counts');

        $validated = $request->validate([
            'warehouse_id'            => 'required|exists:warehouses,id',
            'notes'                   => 'nullable|string',
            'items'                   => 'required|array|min:1',
            'items.*.product_id'      => 'required|exists:products,id',
            'items.*.system_quantity' => 'required|integer|min:0',
            'items.*.actual_quantity' => 'required|integer|min:0',
            'items.*.notes'           => 'nullable|string',
        ]);

        $inventoryCount = InventoryCount::create([
            'warehouse_id' => $validated['warehouse_id'],
            'status'       => 'pending',
            'notes'        => $validated['notes'] ?? null,
        ]);

        foreach ($validated['items'] as $item) {
            $inventoryCount->items()->create([
                'product_id'      => $item['product_id'],
                'system_quantity' => $item['system_quantity'],
                'actual_quantity' => $item['actual_quantity'],
                'difference'      => $item['actual_quantity'] - $item['system_quantity'],
                'notes'           => $item['notes'] ?? null,
            ]);
        }

        return $this->successResponse($inventoryCount->load('items'), 201);
    }

    public function update(InventoryCount $inventoryCount, Request $request): JsonResponse
    {
        Gate::authorize('edit-inventory-counts');

        if ($inventoryCount->status !== 'pending') {
            return $this->errorResponse('Only pending counts can be updated', 400);
        }

        $validated = $request->validate([
            'notes' => 'nullable|string',
        ]);

        $inventoryCount->update($validated);
        return $this->successResponse($inventoryCount);
    }

    public function complete(InventoryCount $inventoryCount): JsonResponse
    {
        Gate::authorize('complete-inventory-counts');

        if ($inventoryCount->status !== 'pending') {
            return $this->errorResponse('Only pending counts can be completed', 400);
        }

        DB::transaction(function () use ($inventoryCount) {

            foreach ($inventoryCount->items as $item) {
                if ($item->difference !== 0) {
                    $this->updateWarehouseStock(
                        $inventoryCount->warehouse_id,
                        $item->product_id,
                        $item->difference
                    );

                    $this->logInventoryMovement(
                        $item->product_id,
                        $inventoryCount->warehouse_id,
                        'count_adjustment',
                        $item->difference,
                        $inventoryCount
                    );
                }
            }

            $inventoryCount->update(['status' => 'completed']);
        });
        return $this->successResponse($inventoryCount);
    }

    public function cancel(InventoryCount $inventoryCount): JsonResponse
    {
        Gate::authorize('cancel-inventory-counts');

        if ($inventoryCount->status !== 'pending') {
            return $this->errorResponse('Only pending counts can be cancelled', 400);
        }

        $inventoryCount->update(['status' => 'cancelled']);
        return $this->successResponse($inventoryCount);
    }
}

<?php

namespace Modules\Inventory\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Modules\Inventory\Models\SupplyOrder;
use Modules\Shared\Http\Traits\ApiResponse;
use Modules\Shared\Http\Traits\LogsInventoryMovement;

class SupplyOrderController extends Controller
{

    use ApiResponse, LogsInventoryMovement;

    public function index(): JsonResponse
    {
        Gate::authorize('view-supply-orders');
        return $this->successResponse(SupplyOrder::with('supplier', 'warehouse')->paginate(20));
    }

    public function show(SupplyOrder $supplyOrder): JsonResponse
    {
        Gate::authorize('view-supply-orders');
        return $this->successResponse($supplyOrder->load('supplier', 'warehouse', 'items.product'));
    }

    public function store(Request $request): JsonResponse
    {
        Gate::authorize('create-supply-orders');

        $validated = $request->validate([
            'supplier_id'              => 'required|exists:suppliers,id',
            'warehouse_id'             => 'required|exists:warehouses,id',
            'notes'                    => 'nullable|string',
            'items'                    => 'required|array|min:1',
            'items.*.product_id'       => 'required|exists:products,id',
            'items.*.quantity'         => 'required|integer|min:1',
            'items.*.unit_price'       => 'required|numeric|min:0',
        ]);

        $order = null;
        DB::transaction(function () use ($validated, &$order) {
            $order = SupplyOrder::create([
                'supplier_id'  => $validated['supplier_id'],
                'warehouse_id' => $validated['warehouse_id'],
                'status'       => 'pending',
                'notes'        => $validated['notes'] ?? null,
                'total_amount' => collect($validated['items'])->sum(fn($item) => $item['quantity'] * $item['unit_price']),
            ]);

            foreach ($validated['items'] as $item) {
                $order->items()->create([
                    'product_id'  => $item['product_id'],
                    'quantity'    => $item['quantity'],
                    'unit_price'  => $item['unit_price'],
                    'total_price' => $item['quantity'] * $item['unit_price'],
                ]);
            }
        });

        return $this->successResponse($order->load(['supplier', 'warehouse', 'items.product']), 201);
    }

    public function update(SupplyOrder $supplyOrder, Request $request): JsonResponse
    {
        Gate::authorize('edit-supply-orders');
        $validated = $request->validate([
            'supplier_id' => 'sometimes|required|exists:suppliers,id',
            'warehouse_id' => 'sometimes|required|exists:warehouses,id',
            'total_amount' => 'sometimes|required|numeric|min:0',
            'status' => 'nullable|in:pending,received,cancelled',
            'notes' => 'nullable|string',
        ]);
        $supplyOrder->update($validated);
        return $this->successResponse($supplyOrder);
    }

    public function destroy(SupplyOrder $supplyOrder): JsonResponse
    {
        Gate::authorize('delete-supply-orders');
        $supplyOrder->delete();
        return $this->successResponse(null, 204);
    }

    public function complete(SupplyOrder $supplyOrder): JsonResponse
    {
        Gate::authorize('complete-supply-orders');

        if ($supplyOrder->status !== 'pending') {
            return $this->errorResponse('Only pending orders can be completed', 400);
        }
        DB::transaction(function () use ($supplyOrder) {
            foreach ($supplyOrder->items as $item) {
                $this->updateWarehouseStock($supplyOrder->warehouse_id, $item->product_id, +$item->quantity);
                $this->logInventoryMovement($item->product_id, $supplyOrder->warehouse_id, 'supply', +$item->quantity, $supplyOrder);
            }
            // Here you would typically update inventory levels based on the order items
            $supplyOrder->update(['status' => 'received']);
        });
        return $this->successResponse($supplyOrder);
    }

    public function cancel(SupplyOrder $supplyOrder): JsonResponse
    {
        Gate::authorize('edit-supply-orders');
        if ($supplyOrder->status !== 'pending') {
            return $this->errorResponse('Only pending orders can be cancelled', 400);
        }
        $supplyOrder->update(['status' => 'cancelled']);
        return $this->successResponse($supplyOrder);
    }
}

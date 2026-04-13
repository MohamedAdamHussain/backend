<?php

namespace Modules\Sales\Http\Controllers;

use Modules\Sales\Models\SaleOrder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Modules\Sales\Models\DeliveryOrder;
use Modules\Sales\Models\Invoice;
use Modules\shared\Http\Traits\ApiResponse;

class SaleOrderController extends Controller
{
    use ApiResponse;

    public function index(): JsonResponse
    {
        Gate::authorize('view-sale-orders');
        return $this->successResponse(SaleOrder::with(['customer', 'items'])->paginate(20));
    }
    public function show(SaleOrder $saleOrder): JsonResponse
    {
        Gate::authorize('view-sale-orders');
        return $this->successResponse($saleOrder->load('items', 'invoice'));
    }
    public function store(Request $request): JsonResponse
    {
        Gate::authorize('create-sale-orders');

        $validated = $request->validate([
            'warehouse_id'         => 'required|exists:warehouses,id',
            'customer_id'          => 'required|exists:customers,id',
            'notes'                => 'nullable|string',
            'items'                => 'required|array|min:1',
            'items.*.product_id'   => 'required|exists:products,id',
            'items.*.quantity'     => 'required|integer|min:1',
            'items.*.unit_price'   => 'required|numeric|min:0',
            'items.*.discount'     => 'nullable|numeric|min:0|max:100',
        ]);

        /** @var SaleOrder $saleOrder */
        $saleOrder = null;

        DB::transaction(function () use ($validated, &$saleOrder) {
            $saleOrder = SaleOrder::create([
                'warehouse_id'   => $validated['warehouse_id'],
                'customer_id'    => $validated['customer_id'],
                'notes'          => $validated['notes'] ?? null,
                'status'         => 'pending',
                'payment_status' => 'unpaid',
                'amount_paid'    => 0,
                'total_amount'   => collect($validated['items'])->sum(
                    fn($item) =>
                    $item['quantity'] * $item['unit_price'] * (1 - ($item['discount'] ?? 0) / 100)
                ),
            ]);

            foreach ($validated['items'] as $item) {
                $saleOrder->items()->create([
                    'product_id'  => $item['product_id'],
                    'quantity'    => $item['quantity'],
                    'unit_price'  => $item['unit_price'],
                    'discount'    => $item['discount'] ?? 0,
                    'total_price' => $item['quantity'] * $item['unit_price'] * (1 - ($item['discount'] ?? 0) / 100),
                ]);
            }
        });

        return $this->successResponse($saleOrder->load(['customer', 'items.product']), 201);
    }

    public function update(Request $request, SaleOrder $saleOrder): JsonResponse
    {
        Gate::authorize('update-sale-orders');
        $validated = $request->validate([
            'warehouse_id' => 'required|integer',
            'customer_id' => 'required|integer',
            'total_amount' => 'required|numeric',
            'amount_paid' => 'required|numeric',
            'notes' => 'nullable|string',
        ]);
        if ($saleOrder->status === 'received' || $saleOrder->status === 'cancelled') {
            return $this->errorResponse('Cannot update a closed order', 400);
        }

        $saleOrder->update($validated);
        return $this->successResponse($saleOrder->load('items'));
    }


    public function destroy(SaleOrder $saleOrder): JsonResponse
    {
        Gate::authorize('delete-sale-orders');
        if ($saleOrder->status === 'received' || $saleOrder->payment_status === 'paid') {
            return $this->errorResponse('Cannot delete a completed or paid sale order', 400);
        }
        $saleOrder->delete();
        return $this->successResponse(null, 204);
    }


    public function complete(SaleOrder $saleOrder): JsonResponse
    {
        Gate::authorize('complete-sale-orders');

        if ($saleOrder->status !== 'shipped') {
            return $this->errorResponse('Only shipped orders can be completed', 400);
        }

        DB::transaction(function () use ($saleOrder) {
            $saleOrder->update(['status' => 'received']);

            // إنشاء الفاتورة تلقائياً
            Invoice::create([
                'sale_order_id' => $saleOrder->id,
                'paid_amount'   => 0,
                'notes'         => null,
            ]);
        });

        return $this->successResponse($saleOrder->fresh()->load('invoice'));
    }

    public function cancel(SaleOrder $saleOrder): JsonResponse
    {
        Gate::authorize('cancel-sale-orders');

        if ($saleOrder->status === 'received') {
            return $this->errorResponse('Cannot cancel a received order', 400);
        }

        $saleOrder->update(['status' => 'cancelled']);
        return $this->successResponse($saleOrder->fresh());
    }

    public function accept(SaleOrder $saleOrder): JsonResponse
    {
        Gate::authorize('accept-sale-orders');

        if ($saleOrder->status !== 'pending') {
            return $this->errorResponse('Only pending orders can be accepted', 400);
        }

        $saleOrder->update(['status' => 'accepted']);
        return $this->successResponse($saleOrder->fresh());
    }

    public function process(SaleOrder $saleOrder): JsonResponse
    {
        Gate::authorize('process-sale-orders');

        if ($saleOrder->status !== 'accepted') {
            return $this->errorResponse('Only accepted orders can be processed', 400);
        }

        $saleOrder->update(['status' => 'processing']);
        return $this->successResponse($saleOrder->fresh());
    }

    public function ship(SaleOrder $saleOrder): JsonResponse
    {
        Gate::authorize('ship-sale-orders');

        if ($saleOrder->status !== 'processing') {
            return $this->errorResponse('Only processing orders can be shipped', 400);
        }

        DB::transaction(function () use ($saleOrder) {
            $saleOrder->update(['status' => 'shipped']);

            // إنشاء DeliveryOrder تلقائياً
            DeliveryOrder::create([
                'sale_order_id' => $saleOrder->id,
                'customer_id'   => $saleOrder->customer_id,
                'warehouse_id'  => $saleOrder->warehouse_id,
                'total_amount'  => $saleOrder->total_amount,
                'status'        => 'pending',
                'notes'         => '',
            ]);
        });

        return $this->successResponse($saleOrder->fresh()->load('deliveryOrder'));
    }
}

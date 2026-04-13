<?php

namespace Modules\Sales\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Modules\Sales\Models\DeliveryOrder;
use Modules\shared\Http\Traits\ApiResponse;

class DeliveryOrderController extends Controller
{
    use ApiResponse;
    public function index()
    {
        Gate::authorize('view-delivery-orders');
        return $this->successResponse(DeliveryOrder::with('saleOrder')->paginate(20));
    }
    public function show(DeliveryOrder $deliveryOrder)
    {
        Gate::authorize('view-delivery-orders');
        return $this->successResponse(
            $deliveryOrder->load(['saleOrder.customer', 'items.product', 'warehouse'])
        );
    }
   public function ship(DeliveryOrder $deliveryOrder): JsonResponse
{
    Gate::authorize('ship-delivery-orders');

    if ($deliveryOrder->status !== 'pending') {
        return $this->errorResponse('Only pending delivery orders can be shipped', 400);
    }

    $deliveryOrder->update(['status' => 'shipped']);
    return $this->successResponse($deliveryOrder->fresh());
}

public function complete(DeliveryOrder $deliveryOrder): JsonResponse
{
    Gate::authorize('complete-delivery-orders');

    if ($deliveryOrder->status !== 'shipped') {
        return $this->errorResponse('Only shipped delivery orders can be completed', 400);
    }

    $deliveryOrder->update(['status' => 'delivered']);
    return $this->successResponse($deliveryOrder->fresh());
}

public function cancel(DeliveryOrder $deliveryOrder): JsonResponse
{
    Gate::authorize('cancel-delivery-orders');

    if ($deliveryOrder->status === 'delivered') {
        return $this->errorResponse('Cannot cancel a delivered order', 400);
    }

    $deliveryOrder->update(['status' => 'cancelled']);
    return $this->successResponse($deliveryOrder->fresh());
}
}

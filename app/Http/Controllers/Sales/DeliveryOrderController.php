<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\DeliveryOrder;
use Illuminate\Http\Request;

class DeliveryOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $deliveryOrders = DeliveryOrder::all();
        return response()->json($deliveryOrders);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $deliveryOrder = DeliveryOrder::create($request->validated());
        return response()->json($deliveryOrder, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(DeliveryOrder $deliveryOrder)
    {
        return response()->json($deliveryOrder);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DeliveryOrder $deliveryOrder)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DeliveryOrder $deliveryOrder)
    {
        $deliveryOrder->update($request->validated());
        return response()->json($deliveryOrder);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DeliveryOrder $deliveryOrder)
    {
        $deliveryOrder->delete();
        return response()->json(null, 204);
    }

    /**
     * Complete the delivery order
     */
    public function complete(DeliveryOrder $deliveryOrder)
    {
        $deliveryOrder->update(['status' => 'completed']);
        return response()->json($deliveryOrder);
    }

    /**
     * Cancel the delivery order
     */
    public function cancel(DeliveryOrder $deliveryOrder)
    {
        $deliveryOrder->update(['status' => 'cancelled']);
        return response()->json($deliveryOrder);
    }
}

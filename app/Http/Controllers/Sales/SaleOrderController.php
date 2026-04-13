<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\SaleOrder;
use Illuminate\Http\Request;

class SaleOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $saleOrders = SaleOrder::all();
        return response()->json($saleOrders);
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
        $saleOrder = SaleOrder::create($request->validated());
        return response()->json($saleOrder, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(SaleOrder $saleOrder)
    {
        return response()->json($saleOrder);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SaleOrder $saleOrder)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SaleOrder $saleOrder)
    {
        $saleOrder->update($request->validated());
        return response()->json($saleOrder);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SaleOrder $saleOrder)
    {
        $saleOrder->delete();
        return response()->json(null, 204);
    }

    /**
     * Complete the sale order
     */
    public function complete(SaleOrder $saleOrder)
    {
        $saleOrder->update(['status' => 'completed']);
        return response()->json($saleOrder);
    }

    /**
     * Cancel the sale order
     */
    public function cancel(SaleOrder $saleOrder)
    {
        $saleOrder->update(['status' => 'cancelled']);
        return response()->json($saleOrder);
    }
}

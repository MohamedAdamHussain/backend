<?php

namespace Modules\Inventory\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Modules\Inventory\Models\Product;
use Modules\shared\Http\Traits\ApiResponse;

class ProductsController extends Controller
{
    use ApiResponse;
    /**
     * Display a listing of the resource.
     */
    public function index() : JsonResponse
    {
        Gate::authorize('view-products');
        return $this->successResponse(Product::with('warehouses')->paginate(20));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) : JsonResponse {
        Gate::authorize('create-products');
        $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'production_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after_or_equal:production_date',
            'warehouse_id' => 'nullable|exists:warehouses,id',
            'quantity' => 'nullable|integer|min:0',
            'low_stock_alert' => 'nullable|integer|min:0',
        ]);

        $product = Product::create($request->only('name', 'description', 'price', 'production_date', 'expiry_date'));

            if ($request->has('warehouse_id')) {
                $product->warehouses()->attach($request->warehouse_id, [
                    'quantity' => $request->quantity ?? 0,
                    'low_stock_alert' => $request->low_stock_alert ?? 10,
                ]);
            }

        return $this->successResponse($product->load('warehouses'), 201);
    }

    /**
     * Show the specified resource.
     */
    public function show(Product $product): JsonResponse
    {
        Gate::authorize('view-products');
        return $this->successResponse($product->load('warehouses'));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Product $product, Request $request): JsonResponse {
        Gate::authorize('edit-products');
        $validated = $request->validate([
            'name' => 'sometimes|required|string',
            'description' => 'sometimes|nullable|string',
            'price' => 'sometimes|required|numeric',
            'production_date' => 'sometimes|nullable|date',
            'expiry_date' => 'sometimes|nullable|date|after_or_equal:production_date',
        ]);
        $product->update($validated);
        return $this->successResponse($product->load('warehouses'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product): JsonResponse {
        Gate::authorize('delete-products');
        if ($product->warehouses()->exists()) {
            return $this->errorResponse('Cannot delete product that is associated with warehouses', 400);
        }
        $product->delete();
        return $this->successResponse(null , 204);
    }
}

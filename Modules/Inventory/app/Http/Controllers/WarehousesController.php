<?php

namespace Modules\Inventory\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Modules\Inventory\Models\Warehouse;
use Modules\shared\Http\Traits\ApiResponse;

class WarehousesController extends Controller
{
    use ApiResponse;
    public function index(): JsonResponse
    {
        Gate::authorize('view-warehouses');
        return $this->successResponse(Warehouse::with('products')->paginate(20));
    }
    public function store(Request $request): JsonResponse
    {
        Gate::authorize('create-warehouses');
        $request->validate([
            'name' => 'required|string',
            'location' => 'required|string',
            'capacity' => 'nullable|integer|min:0',
            'area' => 'nullable|numeric|min:0',
        ]);

        $warehouse = Warehouse::create($request->only('name', 'location', 'capacity', 'area'));

        return $this->successResponse($warehouse, 201);
    }
    public function show(Warehouse $warehouse): JsonResponse
    {
        Gate::authorize('view-warehouses');
        return $this->successResponse($warehouse->load('products'));
    }
    public function update(Warehouse $warehouse, Request $request): JsonResponse
    {
        Gate::authorize('edit-warehouses');
        $validated = $request->validate([
            'name' => 'sometimes|required|string',
            'location' => 'required|string',
            'capacity' => 'nullable|integer|min:0',
            'area' => 'nullable|numeric|min:0',
        ]);
        $warehouse->update($validated);
        return $this->successResponse($warehouse->load('products'));
    }
    public function destroy(Warehouse $warehouse): JsonResponse
    {
        Gate::authorize('delete-warehouses');
        if ($warehouse->products()->exists()) {
            return $this->errorResponse('Cannot delete warehouse that has associated products', 400);
        }
        $warehouse->delete();
        return $this->successResponse(null, 204);
    }
}

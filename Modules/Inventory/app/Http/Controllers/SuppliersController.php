<?php

namespace Modules\Inventory\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Modules\Inventory\Models\Supplier;
use Modules\shared\Http\Traits\ApiResponse;

class SuppliersController extends Controller {
    use ApiResponse;

    public function index() {
        Gate::authorize('view-suppliers');
        return $this->successResponse(Supplier::paginate(20));
    }

    public function store(Request $request) {

        Gate::authorize('create-suppliers');
        $validated = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email:unique:suppliers,email',
            'phone' => 'nullable|string',
            'location' => 'nullable|string',
            'is_active' => 'boolean',
        ]);
        $supplier = Supplier::create($validated);
        return $this->successResponse($supplier, 201);
    }

    public function show(Supplier $supplier) {
        Gate::authorize('view-suppliers');
        return $this->successResponse($supplier);
    }

    public function update(Supplier $supplier, Request $request) {
        Gate::authorize('edit-suppliers');
        $validated = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email:unique:suppliers,email,' . $supplier->id,
            'phone' => 'nullable|string',
            'location' => 'nullable|string',
            'is_active' => 'boolean',
        ]);
        $supplier->update($validated);
        return $this->successResponse($supplier);
    }

    public function destroy(Supplier $supplier) {
        Gate::authorize('delete-suppliers');
         if ($supplier->orders()->exists()) {
             return $this->errorResponse('Cannot delete supplier that has associated orders', 400);
         }
         $supplier->delete();
         return $this->successResponse(null, 204);
    }

}

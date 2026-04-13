<?php

namespace Modules\Sales\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Modules\Sales\Models\Customer;
use Modules\shared\Http\Traits\ApiResponse;

class CustomerController extends Controller
{
    use ApiResponse;
    public function index() : JsonResponse
    {
        Gate::authorize('view-customers');
        return $this->successResponse(Customer::with('priceList')->paginate(20));
    }
    public function show(Customer $customer) : JsonResponse
    {
        Gate::authorize('view-customers');
        return $this->successResponse($customer->load('priceList', 'saleOrders'));
    }
    public function store(Request $request) : JsonResponse
    {
        Gate::authorize('create-customers');
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:customers,email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'price_list_id' => 'nullable|exists:price_lists,id',
        ]);
        $customer = Customer::create($validated);
        return $this->successResponse($customer, 'Customer created successfully', 201);
    }
    public function update(Customer $customer, Request $request) : JsonResponse
    {
        Gate::authorize('edit-customers');
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:customers,email,' . $customer->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'price_list_id' => 'nullable|exists:price_lists,id',
        ]);
        $customer->update($validated);
        return $this->successResponse($customer, 'Customer updated successfully');
    }
    // ماذا لو عنده saleOrders？
    public function destroy(Customer $customer): JsonResponse
    {
        Gate::authorize('delete-customers');

        if ($customer->saleOrders()->exists()) {
            return $this->errorResponse('Cannot delete customer with existing orders', 400);
        }

        $customer->delete();
        return $this->successResponse(null, 204);
    }
}

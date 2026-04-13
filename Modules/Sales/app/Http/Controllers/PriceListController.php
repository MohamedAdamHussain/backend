<?php

namespace Modules\Sales\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Modules\Sales\Models\PriceList;
use Modules\shared\Http\Traits\ApiResponse;

class PriceListController extends Controller
{
    use ApiResponse;
    public function index(): JsonResponse
    {
        Gate::authorize('view-price-lists');
        return $this->successResponse(PriceList::with('items.product')->paginate(20));
    }
    public function show(PriceList $priceList): JsonResponse
    {
        Gate::authorize('view-price-lists');
        return $this->successResponse($priceList->load('items.product'));
    }
    public function store(Request $request): JsonResponse
    {
        Gate::authorize('create-price-lists');
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'discount' => 'nullable|numeric|min:0|max:100',
            'items' => 'nullable|array',
            'items.*.product_id' => 'required_with:items|exists:products,id',
            'items.*.special_price' => 'required_with:items|numeric|min:0',
        ]);
        $priceList = PriceList::create([
            'name'        => $validated['name'],
            'description' => $validated['description'] ?? null,
            'discount'    => $validated['discount'] ?? null,
        ]);

        if (!empty($validated['items'])) {
            foreach ($validated['items'] as $item) {
                $priceList->items()->create([
                    'product_id'    => $item['product_id'],
                    'special_price' => $item['special_price'],
                ]);
            }
        }

        return $this->successResponse($priceList->load('items.product'), 201);
    }
    public function update(PriceList $priceList, Request $request): JsonResponse
    {
        Gate::authorize('edit-price-lists');
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'discount' => 'nullable|numeric|min:0|max:100',
            'items' => 'nullable|array',
            'items.*.id' => 'nullable|exists:price_list_items,id',
            'items.*.product_id' => 'required_with:items|exists:products,id',
            'items.*.special_price' => 'required_with:items|numeric|min:0'
        ]);
        $priceList->update([
            'name'        => $validated['name'],
            'description' => $validated['description'] ?? null,
            'discount'    => $validated['discount'] ?? null,
        ]);

        if (!empty($validated['items'])) {
            // حذف القديم وإضافة الجديد
            $priceList->items()->delete();
            foreach ($validated['items'] as $item) {
                $priceList->items()->create([
                    'product_id'    => $item['product_id'],
                    'special_price' => $item['special_price'],
                ]);
            }
        }
        return $this->successResponse($priceList);
    }
    public function destroy(PriceList $priceList): JsonResponse
    {
        Gate::authorize('delete-price-lists');
        if ($priceList->customers()->exists()) {
            return $this->errorResponse('Cannot delete price list assigned to customers', 400);
        }
        $priceList->delete();
        return $this->successResponse(null, 204);
    }
}

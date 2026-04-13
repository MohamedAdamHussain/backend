<?php

namespace Modules\HR\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Modules\HR\Models\Advance;
use Modules\HR\Models\Employee;
use Modules\Shared\Http\Traits\ApiResponse;

class AdvanceController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        Gate::authorize('view-advances');

        $query = Advance::with('employee');

        if ($request->has('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        return $this->successResponse($query->paginate(20));
    }

    public function store(Request $request): JsonResponse
    {
        Gate::authorize('create-advances');

        $validated = $request->validate([
            'employee_id'   => 'required|exists:employees,id',
            'amount'        => 'required|numeric|min:0',
            'request_date'  => 'required|date',
            'approval_date' => 'nullable|date|after_or_equal:request_date',
            'notes'         => 'nullable|string',
        ]);

        $advance = Advance::create([
            'employee_id'   => $validated['employee_id'],
            'amount'        => $validated['amount'],
            'approved_by'   => Auth::user()->employee?->id,
            'request_date'  => $validated['request_date'],
            'approval_date' => $validated['approval_date'] ?? null,
            'status'        => 'active',
            'notes'         => $validated['notes'] ?? null,
        ]);

        return $this->successResponse($advance->load('employee'), 201);
    }

    public function update(Request $request, Advance $advance): JsonResponse
    {
        Gate::authorize('edit-advances');

        if ($advance->status === 'completed') {
            return $this->errorResponse('Cannot update a completed advance', 400);
        }

        $validated = $request->validate([
            'amount'        => 'sometimes|required|numeric|min:0',
            'request_date'  => 'sometimes|required|date',
            'approval_date' => 'sometimes|nullable|date',
            'notes'         => 'sometimes|nullable|string',
        ]);

        $advance->update($validated);
        return $this->successResponse($advance->fresh());
    }

    public function destroy(Advance $advance): JsonResponse
    {
        Gate::authorize('delete-advances');

        if ($advance->status === 'active' && $advance->repayments()->exists()) {
            return $this->errorResponse('Cannot delete advance with repayments', 400);
        }

        $advance->delete();
        return $this->successResponse(null, 204);
    }
}

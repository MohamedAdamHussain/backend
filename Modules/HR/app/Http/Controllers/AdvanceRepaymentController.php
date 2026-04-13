<?php

namespace Modules\HR\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Modules\HR\Models\Advance;
use Modules\HR\Models\AdvanceRepayment;
use Modules\Shared\Http\Traits\ApiResponse;


class AdvanceRepaymentController extends Controller
{
    use ApiResponse;

    private function updateAdvanceStatus(Advance $advance): void
    {
        $totalPaid = $advance->repayments()->sum('amount');

        $advance->status = $totalPaid >= $advance->amount ? 'completed' : 'active';
        $advance->save();
    }

    public function index(Request $request): JsonResponse
    {
        Gate::authorize('view-advances');

        $query = AdvanceRepayment::with('advance.employee');

        if ($request->has('advance_id')) {
            $query->where('advance_id', $request->advance_id);
        }

        return $this->successResponse($query->paginate(20));
    }

    public function store(Request $request): JsonResponse
    {
        Gate::authorize('create-advances');

        $validated = $request->validate([
            'advance_id'      => 'required|exists:advances,id',
            'amount'         => 'required|numeric|min:0',
            'payment_date'   => 'required|date',
            'payment_method' => 'nullable|string|max:50',
            'notes'          => 'nullable|string',
        ]);

        $advance = Advance::findOrFail($validated['advance_id']);

        if ($advance->status === 'completed') {
            return $this->errorResponse('Cannot add repayment to completed advance', 400);
        }

        $repayment = AdvanceRepayment::create([
            'advance_id'      => $validated['advance_id'],
            'amount'         => $validated['amount'],
            'payment_date'   => $validated['payment_date'],
            'payment_method' => $validated['payment_method'] ?? null,
            'notes'          => $validated['notes'] ?? null,
        ]);

        $this->updateAdvanceStatus($advance);

        return $this->successResponse($repayment->load('advance'), 201);
    }

    public function update(Request $request, AdvanceRepayment $repayment): JsonResponse
    {
        Gate::authorize('edit-advances');

        if ($repayment->advance->status === 'completed') {
            return $this->errorResponse('Cannot update repayment of completed advance', 400);
        }

        $validated = $request->validate([
            'amount'         => 'sometimes|required|numeric|min:0',
            'payment_date'   => 'sometimes|required|date',
            'payment_method' => 'sometimes|nullable|string|max:50',
            'notes'          => 'sometimes|nullable|string',
        ]);

        $repayment->update($validated);

        $this->updateAdvanceStatus($repayment->advance);

        return $this->successResponse($repayment->fresh()->load('advance'));
    }

    public function destroy(AdvanceRepayment $repayment): JsonResponse
    {
        Gate::authorize('delete-advances');

        $advance = $repayment->advance;

        if ($advance->status === 'completed') {
            return $this->errorResponse('Cannot delete repayment of completed advance', 400);
        }

        $repayment->delete();

        $this->updateAdvanceStatus($advance);

        return $this->successResponse(null, 204);
    }
}

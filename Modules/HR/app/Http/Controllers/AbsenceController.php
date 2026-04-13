<?php

namespace Modules\HR\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Modules\HR\Models\Absence;
use Modules\HR\Models\Employee;
use Modules\Shared\Http\Traits\ApiResponse;

class AbsenceController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        Gate::authorize('view-absences');

        $query = Absence::with('employee');

        if ($request->has('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->has('date')) {
            $query->where('date', $request->date);
        }

        return $this->successResponse($query->paginate(20));
    }

    public function store(Request $request): JsonResponse
    {
        Gate::authorize('create-absences');

        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date'        => 'required|date',
            'reason'      => 'nullable|in:sick_leave,vacation,personal,unpaid_leave',
            'notes'       => 'nullable|string',
        ]);

        $employee = Employee::find($validated['employee_id']);

        if ($employee->absences()->where('date', $validated['date'])->exists()) {
            return $this->errorResponse('Absence already recorded for this date', 400);
        }

        $absence = Absence::create([
            'employee_id' => $validated['employee_id'],
            'date'        => $validated['date'],
            'reason'      => $validated['reason'] ?? null,
            'approved_by' => Auth::user()->employee?->id,
            'notes'       => $validated['notes'] ?? null,
        ]);

        return $this->successResponse($absence->load('employee'), 201);
    }

    public function update(Request $request, Absence $absence): JsonResponse
    {
        Gate::authorize('edit-absences');

        $validated = $request->validate([
            'reason' => 'sometimes|required|in:sick_leave,vacation,personal,unpaid_leave',
            'notes'  => 'sometimes|nullable|string',
        ]);

        $absence->update($validated);
        return $this->successResponse($absence->fresh());
    }

    public function destroy(Absence $absence): JsonResponse
    {
        Gate::authorize('delete-absences');
        $absence->delete();
        return $this->successResponse(null, 204);
    }
}

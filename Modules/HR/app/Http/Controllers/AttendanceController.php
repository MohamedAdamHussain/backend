<?php

namespace Modules\HR\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Modules\HR\Models\Attendance;
use Modules\HR\Models\Employee;
use Modules\Shared\Http\Traits\ApiResponse;

class AttendanceController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        Gate::authorize('view-attendances');

        $query = Attendance::with('employee');

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
        Gate::authorize('create-attendances');

        $validated = $request->validate([
            'employee_id'    => 'required|exists:employees,id',
            'check_in_time'  => 'nullable|date_format:H:i',
            'check_out_time' => 'nullable|date_format:H:i|after:check_in_time',
            'notes'          => 'nullable|string',
        ]);

        $employee = Employee::find($validated['employee_id']);

        // تحقق من تكرار الحضور
        if ($employee->attendances()->where('date', now()->toDateString())->exists()) {
            return $this->errorResponse('Attendance already recorded for today', 400);
        }

        $attendance = Attendance::create([
            'employee_id'    => $validated['employee_id'],
            'date'           => now()->toDateString(),
            'check_in_time'  => $validated['check_in_time'] ?? null,
            'check_out_time' => $validated['check_out_time'] ?? null,
            'checked_by'     => Auth::user()->employee?->id,
            'notes'          => $validated['notes'] ?? null,
        ]);

        return $this->successResponse($attendance->load('employee'), 201);
    }

    public function update(Attendance $attendance, Request $request): JsonResponse
    {
        Gate::authorize('edit-attendances');

        $validated = $request->validate([
            'check_in_time'  => 'sometimes|nullable|date_format:H:i',
            'check_out_time' => 'sometimes|nullable|date_format:H:i|after:check_in_time',
            'notes'          => 'sometimes|nullable|string',
        ]);

        $attendance->update($validated);

        return $this->successResponse($attendance->fresh()->load('employee'));
    }

    public function destroy(Attendance $attendance): JsonResponse
    {
        Gate::authorize('delete-attendances');
        $attendance->delete();
        return $this->successResponse(null, 204);
    }
}

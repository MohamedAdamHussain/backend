<?php

namespace Modules\HR\Http\Controllers;

use Modules\HR\Models\Salary;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Modules\shared\Http\Traits\ApiResponse;

class SalaryController extends Controller
{
    use ApiResponse;
    private function calculateNetSalary(Salary $salary): void
    {
        $salary->net_salary = $salary->basic_salary
            + ($salary->transport_allowance ?? 0)
            + ($salary->housing_allowance ?? 0)
            - ($salary->absences_deduction ?? 0)
            - ($salary->advances_deduction ?? 0);
    }

    public function index(Request $request): JsonResponse
    {
        Gate::authorize('view-salaries');

        $query = Salary::with('employee');

        if ($request->has('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        return $this->successResponse($query->paginate(20));
    }

    public function store(Request $request): JsonResponse
    {
        Gate::authorize('create-salaries');

        $validatedData = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'basic_salary' => 'required|numeric|min:0',
            'transport_allowance' => 'nullable|numeric|min:0',
            'housing_allowance' => 'nullable|numeric|min:0',
            'absences_deduction' => 'nullable|numeric|min:0',
            'advances_deduction' => 'nullable|numeric|min:0',
            'month' => 'required|date_format:Y-m',
            'notes' => 'nullable|string',
        ]);

        $exists = Salary::where('employee_id', $validatedData['employee_id'])
            ->where('month', $validatedData['month'] . '-01')
            ->exists();

        if ($exists) {
            return $this->errorResponse('Salary already exists for this month', 400);
        }

        $salary = Salary::create($validatedData);
        $this->calculateNetSalary($salary);
        $salary->month = $validatedData['month'] . '-01';
        $salary->payment_status = 'unpaid';
        $salary->save();

        return $this->successResponse($salary, 201);
    }
    public function update(Request $request, Salary $salary): JsonResponse
    {
        Gate::authorize('edit-salaries');

        $validatedData = $request->validate([
            'basic_salary' => 'sometimes|numeric|min:0',
            'transport_allowance' => 'sometimes|numeric|min:0',
            'housing_allowance' => 'sometimes|numeric|min:0',
            'absences_deduction' => 'sometimes|numeric|min:0',
            'advances_deduction' => 'sometimes|numeric|min:0',
            'month' => 'sometimes|date_format:Y-m',
            'notes' => 'sometimes|nullable|string',
        ]);

        $salary->update($validatedData);
        $this->calculateNetSalary($salary);
        if (isset($validatedData['month'])) {
            $salary->month = $validatedData['month'] . '-01';
        }
        $salary->save();

        return $this->successResponse($salary);
    }

    public function markAsPaid(Salary $salary): JsonResponse
    {
        Gate::authorize('mark-salaries-paid');

        if ($salary->payment_status === 'paid') {
            return $this->errorResponse('Salary already paid', 400);
        }

        $salary->update(['payment_status' => 'paid']);
        return $this->successResponse($salary->fresh());
    }
    public function markAsUnpaid(Salary $salary): JsonResponse
    {
        Gate::authorize('update-salaries');
        $salary->payment_status = 'unpaid';
        $salary->save();
        return $this->successResponse($salary);
    }
}

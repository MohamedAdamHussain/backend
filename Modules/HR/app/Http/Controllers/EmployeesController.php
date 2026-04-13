<?php

namespace Modules\HR\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Modules\HR\Models\Employee;
use Modules\shared\Http\Traits\ApiResponse;

class EmployeesController extends Controller
{
    use ApiResponse;

    public function index()
    {
        Gate::authorize('view-employees');
        return $this->successResponse(Employee::with('departments')->paginate(20));
    }



    public function store(Request $request)
    {
        Gate::authorize('create-employees');
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'gender' => 'required|in:male,female',
            'occupation' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email',
            'phone_number' => 'nullable|string|max:20',
            'hire_date' => 'nullable|date',
            'is_active' => 'boolean',
            'user_id' => 'nullable|exists:users,id',
        ]);

        $employee = Employee::create($validated);
        return $this->successResponse($employee, 201);
    }


    public function show(Employee $employee)
    {
        Gate::authorize('view-employees');
        return $this->successResponse($employee);
    }



    public function update(Request $request, Employee $employee)
    {
        Gate::authorize('edit-employees');
        $validated = $request->validate([
            'first_name'   => 'sometimes|required|string|max:255',
            'last_name'    => 'sometimes|required|string|max:255',
            'gender'       => 'sometimes|required|in:male,female',
            'occupation'   => 'sometimes|required|string|max:255',
            'email'        => 'sometimes|required|email|unique:employees,email,' . $employee->id,
            'phone_number' => 'sometimes|nullable|string|max:20',
            'hire_date'    => 'sometimes|nullable|date',
            'is_active'    => 'sometimes|boolean',
            'user_id'      => 'sometimes|nullable|exists:users,id',
        ]);
        $employee->update($validated);
        return $this->successResponse($employee);
    }


    public function destroy(Employee $employee): JsonResponse
    {
        Gate::authorize('delete-employees');

        // تحقق من وجود سجلات مرتبطة
        if ($employee->salaries()->exists() || $employee->advances()->exists()) {
            return $this->errorResponse('Cannot delete employee with salary or advance records', 400);
        }

        $employee->delete();
        return $this->successResponse(null, 204);
    }
}

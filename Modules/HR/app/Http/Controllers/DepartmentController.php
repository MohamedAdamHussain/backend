<?php

namespace Modules\HR\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Modules\HR\Models\Department;
use Modules\shared\Http\Traits\ApiResponse;

class DepartmentController extends Controller {
    use ApiResponse;
    public function index()
    {
        Gate::authorize('view-departments');
        return $this->successResponse(Department::all());
    }
    public function show(Department $department)
    {
        Gate::authorize('view-departments');
        return $this->successResponse($department);
    }
    public function store(Request $request)
    {
        Gate::authorize('create-departments');
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:departments,name',
            'description' => 'nullable|string',
        ]);

        $department = Department::create($validated);
        return $this->successResponse($department, 201);
    }

    public function update(Request $request, Department $department)
    {
        Gate::authorize('update-departments');
        $validated = $request->validate([
            'name' => 'string|max:255|unique:departments,name,' . $department->id,
            'description' => 'nullable|string',
        ]);

        $department->update($validated);
        return $this->successResponse($department);
    }
    public function destroy(Department $department)
    {
        Gate::authorize('delete-departments');
        if ($department->employees()->count() > 0) {
            return $this->errorResponse('Cannot delete department with assigned employees', 400);
        }
        $department->delete();
        return $this->successResponse(null, 'Department deleted successfully');
    }
}

<?php

namespace Modules\Auth\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Modules\Auth\Models\Role;
use Modules\shared\Http\Traits\ApiResponse;

class RolesController extends Controller
{
    use ApiResponse;
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        Gate::authorize('view-roles');
        return $this->successResponse(Role::all());
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        Gate::authorize('create-roles');
        $request->validate([
            'name' => 'required|string|unique:roles,name',
            // 'permissions' => 'array',
            // 'permissions.*' => 'string|exists:permissions,name',
        ]);

        $role = Role::create(['name' => $request->name]);

        return  $this->successResponse($role, 201);
    }

    /**
     * Show the specified resource.
     */
    public function show(Role $role): JsonResponse
    {
        Gate::authorize('view-roles');
        $role->load('permissions');
        return $this->successResponse($role);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Role $role, Request $request): JsonResponse
    {
        Gate::authorize('edit-roles');
        $request->validate([
            'name' => 'required|string|unique:roles,name,' . $role->id,
        ]);
        $role->update(['name' => $request->name]);

        return $this->successResponse($role);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role): JsonResponse
    {
        Gate::authorize('delete-roles');
        $role->delete();

        return $this->successResponse(null, 204);
    }
}

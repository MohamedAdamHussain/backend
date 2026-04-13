<?php

namespace Modules\Auth\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Modules\Auth\Models\Permission;
use Modules\shared\Http\Traits\ApiResponse;

class PermissionsController extends Controller
{
    use ApiResponse;
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        Gate::authorize('view-permissions');
        return $this->successResponse(Permission::all());
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        Gate::authorize('create-permissions');
        $request->validate([
            'name' => 'required|string|unique:permissions,name',
            'module' => 'required|string|in:auth,inventory,sales,hr,accounting',
        ]);

        $permission = Permission::create(['name' => $request->name, 'module' => $request->module]);

        return  $this->successResponse($permission, 201);
    }

    /**
     * Show the specified resource.
     */
    public function show(Permission $permission): JsonResponse
    {
        Gate::authorize('view-permissions');
        return $this->successResponse($permission);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Permission $permission, Request $request): JsonResponse
    {
        Gate::authorize('edit-permissions');
        $request->validate([
            'name' => 'required|string|unique:permissions,name,' . $permission->id,
            'module' => 'required|string|in:auth,inventory,sales,hr,accounting',
        ]);
        $permission->update(['name' => $request->name, 'module' => $request->module]);

        return $this->successResponse($permission);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Permission $permission): JsonResponse
    {
        Gate::authorize('delete-permissions');
        $permission->delete();

        return $this->successResponse(null, 204);
    }
}

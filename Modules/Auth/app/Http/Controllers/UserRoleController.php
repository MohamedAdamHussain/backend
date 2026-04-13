<?php

namespace Modules\Auth\Http\Controllers;


use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Modules\Auth\Models\Permission;
use Modules\Auth\Models\Role;
use Modules\Auth\Models\User;
use Modules\Shared\Http\Traits\ApiResponse;

class UserRoleController extends Controller
{
    use ApiResponse;

    public function assignRoleToUser(User $user, Role $role): JsonResponse
    {
        Gate::authorize('assign-roles');
        $user->roles()->syncWithoutDetaching([$role->id]);
        return $this->successResponse("Role assigned to user successfully.");
    }

    public function removeRoleFromUser(User $user, Role $role): JsonResponse
    {
        Gate::authorize('remove-roles');
        $user->roles()->detach($role->id);
        return $this->successResponse("Role removed from user successfully.");
    }


    public function addPermissionToUser(User $user, Permission $permission): JsonResponse
    {
        Gate::authorize('assign-permissions');
        $user->permissions()->syncWithoutDetaching([$permission->id]);
        return $this->successResponse("Permission assigned to user successfully.");
    }
    public function removePermissionFromUser(User $user, Permission $permission): JsonResponse
    {
        Gate::authorize('remove-permissions');
        $user->permissions()->detach($permission->id);
        return $this->successResponse("Permission removed from user successfully.");
    }
}

<?php

namespace Modules\Auth\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Modules\Auth\Models\User;
use Modules\Shared\Http\Traits\ApiResponse;

class UsersController extends Controller
{
    use ApiResponse;

    public function index(): JsonResponse
    {
        Gate::authorize('view-users');
        $users = User::with(['roles', 'permissions'])->paginate(20);
        return $this->successResponse($users);
    }

    public function show(User $user): JsonResponse
    {
        Gate::authorize('view-users');
        $user->load(['roles', 'permissions']);
        return $this->successResponse($user);
    }

    public function store(Request $request): JsonResponse
    {
        Gate::authorize('create-users');

        $validated = $request->validate([
            'name'                  => 'required|string',
            'email'                 => 'required|email|unique:users,email',
            'password'              => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => bcrypt($validated['password']),
        ]);

        return $this->successResponse($user, 201);
    }

    public function update(Request $request, User $user): JsonResponse
    {
        Gate::authorize('edit-users');

        $validated = $request->validate([
            'name'      => 'sometimes|required|string',
            'email'     => 'sometimes|required|email|unique:users,email,' . $user->id,
            'is_active' => 'sometimes|required|boolean',
        ]);

        $user->update($validated);
        return $this->successResponse($user);
    }

    public function destroy(User $user): JsonResponse
    {
        Gate::authorize('delete-users');
        $user->delete();
        return $this->successResponse(null, 204);
    }
}

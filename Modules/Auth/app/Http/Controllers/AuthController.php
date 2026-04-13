<?php

namespace Modules\Auth\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Auth\Models\User;
use Modules\shared\Http\Traits\ApiResponse;

class AuthController extends Controller
{
    use ApiResponse;

    public function login(Request $request)
    {

        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['password']])) {
            return $this->errorResponse('Invalid credentials', 401);
        }
        /** @var User $user */
        $user = Auth::user();
        $token = $user->createToken('auth_token')->plainTextToken;
        return $this->successResponse([
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->roles->first()?->name,
            ],
            'permissions' => $user->permissions()->pluck('name'),
        ], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->successResponse(null, 200);
    }
}

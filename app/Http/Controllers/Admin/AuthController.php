<?php

namespace App\Http\Controllers\Admin;

use App\Http\Concerns\ApiResponses;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LoginRequest;
use App\Http\Resources\Admin\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    use ApiResponses;

    public function login(LoginRequest $request)
    {
        $data = $request->validated();

        $user = User::where('email', $data['email'])->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            Log::warning('Admin login failed', ['email' => $data['email'], 'ip' => $request->ip()]);

            return $this->error('These credentials do not match our records.', 401);
        }

        if (! $user->is_active) {
            Log::warning('Admin login blocked: inactive account', ['user_id' => $user->id]);

            return $this->error('This account has been deactivated.', 403);
        }

        $token = $user->createToken($data['device_name'] ?? 'admin-api')->plainTextToken;

        Log::info('Admin login succeeded', ['user_id' => $user->id]);

        return $this->success([
            'token' => $token,
            'user' => new UserResource($user),
        ], 'Logged in successfully.');
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->success(null, 'Logged out successfully.');
    }

    public function me(Request $request)
    {
        return $this->success(new UserResource($request->user()));
    }
}

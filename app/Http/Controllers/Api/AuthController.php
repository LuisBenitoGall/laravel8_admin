<?php
namespace App\Http\Controllers\Api;

use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * @param \App\Http\Requests\LoginRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();

        if (!auth()->attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => [trans('auth.failed')],
            ]);
        }

        $user = User::whereEmail($request->email)->firstOrFail();

        $token = $user->createToken('auth-token')->accessToken;

        return response()->json([
            'token' => $token,
        ]);
    }
}

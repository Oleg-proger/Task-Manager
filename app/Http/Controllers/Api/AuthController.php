<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (
            $user === null ||
            ! Hash::check($credentials['password'], $user->password)
        ) {
            throw ValidationException::withMessages([
                'email' => ['Неверный email или пароль.'],
            ]);
        }

        $token = $user->createToken('task-manager-api')->plainTextToken;

        return response()->json([
            'token' => $token,
            'token_type' => 'Bearer',
        ]);
    }
}

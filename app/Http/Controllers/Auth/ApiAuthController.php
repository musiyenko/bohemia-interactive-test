<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApiAuthController extends Controller
{
    /**
     * Handle an authentication attempt.
     */
    public function authenticate(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->user()->tokens()->delete();

            $token = $request->user()->createToken('Personal access token')->plainTextToken;

            return response()->json([
                'status' => 'OK',
                'token' => $token,
            ]);
        }

        return response()->json([
            'status' => 'KO',
            'message' => 'Invalid login credentials',
        ], 401);
    }
}

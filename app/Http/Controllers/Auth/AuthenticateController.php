<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AuthenticateController extends Controller
{


    public function login(Request $request){
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();

            $token = $user->createToken('authToken')->plainTextToken;

            return response()->json([
                'message' => 'Login successful',
                'request_data' => $request->all(),
                'token' => $token,
                'user' => $user,
            ], 200);
        } else {
            return response()->json([
                'message' => 'Invalid email or password',
            ], 401);
        }
    }


    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'middle_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::create([
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('authToken')->plainTextToken;

        return response()->json([
            'message' => 'Registration successful',
            'token' => $token,
            'user' => new UserResource($user),
        ], 201);
    }


    public function logout(Request $request)
    {
        $user = $request->user();

        if ($user) {
            Log::info('Tokens before logout', [
                'tokens' => $user->tokens,
            ]);
            $user->tokens()->delete();


            $request->user()->currentAccessToken()->delete();

            Log::info('Tokens after logout', [
                'tokens' => $user->tokens,
            ]);
        }

        return response()->json(['message' => 'Logged out successfully.'], 200);
    }
}

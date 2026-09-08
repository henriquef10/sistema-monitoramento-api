<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AuthController extends Controller
{

    public function register(Request $request){

        $name = $request->name;
        $email = $request->email;
        $password = $request->password;

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => User::encodePassword($password),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'User registered successfully',
            'data' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 201);

    }

    public function login(Request $request){

        $email = $request->email;
        $password = $request->password;

        $user = User::where('email', $email)->first();

        if (!$user || !User::checkPassword($password, $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials',
            ], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'User logged in successfully',
            'data' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 200);
        
    }

    public function logout(Request $request){

        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'User logged out successfully',
        ], 200);

    }

    public function me(Request $request){

        return response()->json([
            'message' => 'User details retrieved successfully',
            'data' => $request->user(),
        ], 200);

    }

}

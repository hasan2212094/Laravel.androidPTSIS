<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(Request $request) {
        $validationRules = [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role_id' => 'required|exists:roles,id',
        ];

        $validationMessages = [
            'name.required' => 'Nama harus diisi',
            'email.required' => 'Email harus diisi',
            'email.email' => 'Email harus valid',
            'email.unique' => 'Email sudah digunakan',
            'password.required' => 'Password harus diisi',
            'password.min' => 'Password harus minimal 6 karakter',
            'role_id.required' => 'Role ID harus diisi',
            'role_id.exists' => 'Role ID tidak valid',
        ];

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), $validationRules, $validationMessages);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 422);
        }

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role_id' => $request->role_id,
            ]);

            return response()->json(['message' => 'User registered successfully'], 201);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(['error' => 'Error occurred while creating user'], 500);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred'], 500);
        }
    }


    public function login(Request $request) {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'error' => 'Invalid credentials',
                'message' => 'Email atau password salah',
            ], 401);
        }

        $authToken = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'token' => $authToken,
            'role' => $user->role->name,
            'name' => $user->name,
            'email' => $user->email,
            'created_at' => $user->created_at,
        ]);
    }



    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }
}

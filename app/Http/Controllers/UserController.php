<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Http\Resources\RoleResource;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return UserResource::collection(User::with('role')->get());
    }

    public function role_list()
    {
        return RoleResource::collection(Role::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
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
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::with('role')->find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role_id' => $user->role_id,
            'role_name' => $user->role->name ?? null, // Pastikan tidak error jika role null
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $user = User::find($id);
            if (!$user) {
                return response()->json(['message' => 'User not found'], 404);
            }

            $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'email' => 'sometimes|required|email|unique:users,email,' . $id,
                'password' => 'sometimes|required|min:6',
                'role_id' => 'required|exists:roles,id',
            ]);

            if ($request->has('name')) $user->name = $request->name;
            if ($request->has('email')) $user->email = $request->email;
            if ($request->has('password')) $user->password = Hash::make($request->password);
            if ($request->has('role_id')) $user->role_id = $request->role_id;

            $user->save();

            return response()->json([
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role_id' => $user->role_id,
                'role_name' => $user->role->name ?? null, // Pastikan tidak error jika role null
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['message' => 'Validation error', 'error' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Internal server error', 'error' => $e->getMessage()], 500);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }

    public function restore($id)
    {
        $user = User::onlyTrashed()->find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found or not deleted'], 404);
        }

        $user->restore(); // Mengembalikan user

        return response()->json(['message' => 'User restored successfully']);
    }
    public function forceDelete($id)
    {
        $user = User::onlyTrashed()->find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found or not deleted'], 404);
        }

        $user->forceDelete(); // Hapus permanen

        return response()->json(['message' => 'User permanently deleted']);
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Resources\AssignmentResource;
use App\Models\Assignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AssignmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return AssignmentResource::collection(Assignment::with('user','role')->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'role_id' => 'required|exists:roles,id',
            'user_id' => 'required|exists:users,id',
            'name' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'description' => 'required',
            'date' => 'required|date',
            'image' => 'nullable|image|mimes:jpg,png,jpeg|max:2048', // Maksimal 2MB
            'level_urgent' => 'boolean', // Validasi level_urgent harus true/false
            'status' => 'boolean', // Validasi level_urgent harus true/false
            'description_end' => 'required',
            'date_end' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal!',
                'errors' => $validator->errors()
            ], 422);
        }

        $path = null;
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('assignments', 'public');
        }
        $assignment = Assignment::create([
            'role_id' => $request->role_id,
            'user_id' => $request->user_id,
            'name' => $request->name,
            'title' => $request->title,
            'description' => $request->description,
            'date' => $request->date,
            'image' => $path ? asset('storage/' . $path) : null,
            'level_urgent' => $request->level_urgent ?? true, // Jika tidak diisi, default true
            'status' => $request->status ?? false, // Default false (belum selesai)
            'description_end' => $request->description_end,
            'date_end' => $request->date_end,
        ]);

        return response()->json([
            'message' => 'Tugas berhasil ditambahkan!',
            'data' => $assignment
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $assignment = Assignment::find($id);
        if (!$assignment) {
            return response()->json(['message' => 'Assignment not found'], 404);
        }
        return response()->json($assignment, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $assignment = Assignment::find($id);

        if (!$assignment) {
            return response()->json([
                'message' => 'Tugas tidak ditemukan!'
            ], 404);
        }

        $assignment->update($request->all());

        return response()->json([
            'message' => 'Tugas berhasil diperbarui!',
            'data' => $assignment
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $assignment = Assignment::find($id);

        if (!$assignment) {
            return response()->json([
                'message' => 'Tugas tidak ditemukan!'
            ], 404);
        }

        $assignment->delete();

        return response()->json([
            'message' => 'Tugas berhasil dihapus!'
        ], 200);
    }
}
